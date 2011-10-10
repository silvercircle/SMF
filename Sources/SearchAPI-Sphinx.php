<?php
/**
 * %%@productname@%%
 * @copyright 2011 Alex Vie silvercircle(AT)gmail(DOT)com
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright:	2011 Simple Machines (http://www.simplemachines.org)
 * license:  	BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version %%@productversion@%%
 */
if (!defined('SMF'))
	die('Hacking attempt...');

/*
	int searchSort(string $wordA, string $wordB)
		- callback function for usort used to sort the fulltext results.
		- the order of sorting is: large words, small words, large words that
		  are excluded from the search, small words that are excluded.
*/

class sphinx_search
{
	// This is the last version of SMF that this was tested on, to protect against API changes.
	public $version_compatible = 'SMF 2.0';
	// This won't work with versions of SMF less than this.
	public $min_smf_version = 'SMF 2.0';
	// Is it supported?
	public $is_supported = true;

	protected $indexSettings = array();
	// What words are banned?
	protected $bannedWords = array();
	// What is the minimum word length?
	protected $min_word_length = null;
	// What databases support the custom index?
	protected $supported_databases = array('mysql');

	public function __construct()
	{
		global $modSettings;
		
		//$this->indexSettings = unserialize($modSettings['search_custom_index_config']);
		//var_dump($this->indexSettings);
		$this->bannedWords = empty($modSettings['search_stopwords']) ? array() : explode(',', $modSettings['search_stopwords']);
		$this->min_word_length = null;//$this->indexSettings['bytes_per_word'];
	}

	// Check whether the search can be performed by this API.
	public function supportsMethod($methodName, $query_params = null)
	{
		switch ($methodName)
		{
			case 'isValid':
			case 'searchSort':
			case 'prepareIndexes':
			//case 'indexedWordQuery':
			case 'searchQuery':
				return true;
			break;

			default:

				// All other methods, too bad dunno you.
				return false;
			return;
		}
	}

	// If the settings don't exist we can't continue.
	public function isValid()
	{
		global $modSettings;
		$result = !(empty($modSettings['sphinx_searchd_server']) || empty($modSettings['sphinx_searchd_port']));
		return($result);
	}

	// This function compares the length of two strings plus a little.
	public function searchSort($a, $b)
	{
		global $modSettings, $excludedWords;

		$x = strlen($a) - (in_array($a, $excludedWords) ? 1000 : 0);
		$y = strlen($b) - (in_array($b, $excludedWords) ? 1000 : 0);

		return $y < $x ? 1 : ($y > $x ? -1 : 0);
	}

	public function prepareIndexes($word, &$wordsSearch, &$wordsExclude, $isExcluded)
	{
		global $modSettings, $smcFunc;

		$subwords = text2words($word, $this->min_word_length, false);

		// Excluded phrases don't benefit from being split into subwords.
		if (count($subwords) > 1 && $isExcluded)
			return;
		else
		{
			foreach ($subwords as $subword)
			{
				if (commonAPI::strlen($subword) >= $this->min_word_length && !in_array($subword, $this->bannedWords))
				{
					$wordsSearch['indexed_words'][] = $subword;
					if ($isExcluded)
						$wordsExclude[] = $subword;
				}
			}
		}
	}

	public function searchQuery($search_params, $searchWords, $excludedIndexWords, &$participants, &$searchArray)
	{
		global $modSettings, $context, $sourcedir, $user_info, $scripturl;
		if (($cached_results = CacheAPI::getCache('search_results_' . md5($user_info['query_see_board'] . '_' . $context['params']))) === null)	{
			require_once($sourcedir . '/contrib/sphinxapi.php');

			$mySphinx = new SphinxClient();
			$mySphinx->SetServer($modSettings['sphinx_searchd_server'], (int)$modSettings['sphinx_searchd_port']);
			$mySphinx->SetLimits(0, (int) $modSettings['sphinx_max_results']);
			$mySphinx->SetMatchMode(SPH_MATCH_BOOLEAN);
			if(!$search_params['show_complete'])
				$mySphinx->SetGroupBy('ID_TOPIC', SPH_GROUPBY_ATTR);
			$mySphinx->SetSortMode($search_params['sort_dir'] === 'asc' ? SPH_SORT_ATTR_ASC : SPH_SORT_ATTR_DESC, $search_params['sort'] === 'ID_MSG' ? 'ID_TOPIC' : $search_params['sort']);

			if (!empty($search_params['topic']))
				$mySphinx->SetFilter('ID_TOPIC', array((int) $search_params['topic']));
			if (!empty($search_params['min_msg_id']) || !empty($search_params['max_msg_id']))
				$mySphinx->SetIDRange(empty($search_params['min_msg_id']) ? 0 : (int) $search_params['min_msg_id'], empty($search_params['max_msg_id']) ? (int) $modSettings['maxMsgID'] : (int) $search_params['max_msg_id']);
			if (!empty($search_params['brd']))
				$mySphinx->SetFilter('ID_BOARD', $search_params['brd']);
			if (!empty($search_params['prefix']))
				$mySphinx->SetFilter('ID_PREFIX', $search_params['prefix']);
			if (!empty($search_params['memberlist']))
				$mySphinx->SetFilter('ID_MEMBER', $search_params['memberlist']);
			
			$orResults = array();
			foreach ($searchWords as $orIndex => $words) {
				$andResult = '';
				foreach ($words['indexed_words'] as $sphinxWord) {
					$andResult .= (in_array($sphinxWord, $excludedIndexWords) ? '-' : '') . $sphinxWord . ' & ';
				}
				$orResults[] = substr($andResult, 0, -3);
			}
			$query = count($orResults) === 1 ? $orResults[0]  : '(' . implode(') | (', $orResults) . ')';

			// Execute the search query.
			$request = $mySphinx->Query($query, 'smf_index');

			// Can a connection to the deamon be made?
			if ($request === false)
				fatal_error('Unable to access the search deamon.');

			// Get the relevant information from the search results.
			$cached_results = array(
				'matches' => array(),
				'num_results' => $request['total'],
			);
			if (isset($request['matches'])) {
				foreach ($request['matches'] as $msgID => $match) {
					$cached_results['matches'][$msgID] = array(
						'id' => $match['attrs']['id_topic'],
						'relevance' => round($match['attrs']['relevance'] / 10000, 1) . '%',
						//'num_matches' => $match['attrs']['@count'],
						'matches' => array(),
					);
					if(!$search_params['show_complete'])
						$cached_results['matches'][$msgID]['num_matches'] = $match['attrs']['@count'];
				}
			}
			CacheAPI::putCache('search_results_' . md5($user_info['query_see_board']) . '_' . $context['params'], $cached_results, 600);
		}
		foreach (array_slice(array_keys($cached_results['matches']), $_REQUEST['start'], $modSettings['search_results_per_page']) as $msgID) {
			$context['topics'][$msgID] = $cached_results['matches'][$msgID];
			$participants[$cached_results['matches'][$msgID]['id']] = false;
		}

		// Sentences need to be broken up in words for proper highlighting.
		foreach ($searchWords as $orIndex => $words)
			$searchArray = array_merge($searchArray, $searchWords[$orIndex]['subject_words']);

		// Now that we know how many results to expect we can start calculating the page numbers.
		$context['page_index'] = constructPageIndex($scripturl . '?action=search2;params=' . $context['params'], $_REQUEST['start'], $cached_results['num_results'], $modSettings['search_results_per_page'], false);
		
		return($cached_results['num_results']);
	}
}
?>