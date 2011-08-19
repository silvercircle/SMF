<?php

/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines http://www.simplemachines.org
 * @copyright 2011 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0
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
		global $modSettings, $db_type;
		$this->bannedWords = empty($modSettings['search_stopwords']) ? array() : explode(',', $modSettings['search_stopwords']);
	}

	// Check whether the search can be performed by this API.
	public function supportsMethod($methodName, $query_params = null)
	{
		switch ($methodName)
		{
			case 'isValid':
			case 'searchSort':
			case 'prepareIndexes':
			case 'indexedWordQuery':
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
		return !(empty($modSettings['sphinx_searchd_server']) || empty($modSettings['sphinx_searchd_port']));
	}

	// This function compares the length of two strings plus a little.
	public function searchSort($a, $b)
	{
		global $modSettings, $excludedWords;

		$x = strlen($a) - (in_array($a, $excludedWords) ? 1000 : 0);
		$y = strlen($b) - (in_array($b, $excludedWords) ? 1000 : 0);

		return $y < $x ? 1 : ($y > $x ? -1 : 0);
	}

	// Do we have to do some work with the words we are searching for to prepare them?
	public function prepareIndexes($word, &$wordsSearch, &$wordsExclude, $isExcluded)
	{
		global $modSettings, $smcFunc;

	}

	// Search for indexed words.
	public function indexedWordQuery($words, $search_data)
	{
	}
}
?>