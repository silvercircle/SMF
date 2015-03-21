<?php
/**
 * @name      EosAlpha BBS
 * @copyright 2015 Alex Vie silvercircle(AT)gmail(DOT)com
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright:	2011 Simple Machines (http://www.simplemachines.org)
 * license:  	BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version 1.0pre
 */
if (!defined('SMF'))
	die('Hacking attempt...');

/* The admin screen to change the search settings.

	void ManageSearch()
		- main entry point for the admin search settings screen.
		- called by ?action=admin;area=managesearch.
		- requires the admin_forum permission.
		- loads the ManageSearch template.
		- loads the Search language file.
		- calls a function based on the given sub-action.
		- defaults to sub-action 'settings'.

	void EditSearchSettings()
		- edit some general settings related to the search function.
		- called by ?action=admin;area=managesearch;sa=settings.
		- requires the admin_forum permission.
		- uses the 'modify_settings' sub template of the ManageSearch template.

	void EditWeights()
		- edit the relative weight of the search factors.
		- called by ?action=admin;area=managesearch;sa=weights.
		- requires the admin_forum permission.
		- uses the 'modify_weights' sub template of the ManageSearch template.

	void EditSearchMethod()
		- edit the search method and search index used.
		- called by ?action=admin;area=managesearch;sa=method.
		- requires the admin_forum permission.
		- uses the 'select_search_method' sub template of the ManageSearch
		  template.
		- allows to create and delete a fulltext index on the messages table.
		- allows to delete a custom index (that CreateMessageIndex() created).
		- calculates the size of the current search indexes in use.

	void CreateMessageIndex()
		- create a custom search index for the messages table.
		- called by ?action=admin;area=managesearch;sa=createmsgindex.
		- linked from the EditSearchMethod screen.
		- requires the admin_forum permission.
		- uses the 'create_index', 'create_index_progress', and
		  'create_index_done' sub templates of the ManageSearch template.
		- depending on the size of the message table, the process is divided
		  in steps.

	array loadSearchAPIs()
		- get the installed APIs.

*/

function ManageSearch()
{
	global $context, $txt;

	isAllowedTo('admin_forum');

	loadLanguage('Search');
	loadAdminTemplate('ManageSearch');

	db_extend('search');

	$subActions = array(
		'settings' => 'EditSearchSettings',
		'weights' => 'EditWeights',
		'method' => 'EditSearchMethod',
		'createfulltext' => 'EditSearchMethod',
		'removecustom' => 'EditSearchMethod',
		'removefulltext' => 'EditSearchMethod',
		'createmsgindex' => 'CreateMessageIndex',
		'managesphinx' => 'ManageSphinx',
		'sphinxconfig' => 'CreateSphinxConfig',
	);

	// Default the sub-action to 'edit search settings'.
	$_REQUEST['sa'] = isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) ? $_REQUEST['sa'] : 'weights';

	$context['sub_action'] = $_REQUEST['sa'];

	// Create the tabs for the template.
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $txt['manage_search'],
		'help' => 'search',
		'description' => $txt['search_settings_desc'],
		'tabs' => array(
			'weights' => array(
				'description' => $txt['search_weights_desc'],
			),
			'method' => array(
				'description' => $txt['search_method_desc'],
			),
			'settings' => array(
				'description' => $txt['search_settings_desc'],
			),
			'managesphinx' => array(
				'description' => $txt['search_config_sphinx_desc'],
				'label' => $txt['search_managesphinx'],
			),
		),
	);

	// Call the right function for this sub-acton.
	$subActions[$_REQUEST['sa']]();
}

function EditSearchSettings($return_config = false)
{
	global $txt, $context, $scripturl, $sourcedir, $modSettings, $backend_subdir;

	// What are we editing anyway?
	$config_vars = array(
			// Permission...
			array('permissions', 'search_posts'),
			// Some simple settings.
			array('check', 'simpleSearch'),
			array('int', 'search_results_per_page'),
			array('int', 'search_max_results', 'subtext' => $txt['search_max_results_disable']),
		'',
			// Some limitations.
			array('int', 'search_floodcontrol_time', 'subtext' => $txt['search_floodcontrol_time_desc']),
	);

	// Perhaps the search method wants to add some settings?
	$modSettings['search_index'] = empty($modSettings['search_index']) ? 'standard' : $modSettings['search_index'];
	if (file_exists($sourcedir . '/SearchAPI-' . ucwords($modSettings['search_index']) . '.php'))
	{
		loadClassFile('SearchAPI-' . ucwords($modSettings['search_index']) . '.php');

		$method_call = array($modSettings['search_index'] . '_search', 'searchSettings');
		if (is_callable($method_call))
			call_user_func_array($method_call, array(&$config_vars));
	}

	if ($return_config)
		return $config_vars;

	$context['page_title'] = $txt['search_settings_title'];
	$context['sub_template'] = 'show_settings';

	// We'll need this for the settings.
	require_once($sourcedir . '/' . $backend_subdir . '/ManageServer.php');

	// A form was submitted.
	if (isset($_REQUEST['save']))
	{
		checkSession();

		saveDBSettings($config_vars);
		redirectexit('action=admin;area=managesearch;sa=settings;' . $context['session_var'] . '=' . $context['session_id']);
	}

	// Prep the template!
	$context['post_url'] = $scripturl . '?action=admin;area=managesearch;save;sa=settings';
	$context['settings_title'] = $txt['search_settings_title'];

	prepareDBSettingContext($config_vars);
}

function EditWeights()
{
	global $txt, $context, $modSettings;

	$context['page_title'] = $txt['search_weights_title'];
	$context['sub_template'] = 'modify_weights';

	$factors = array(
		'search_weight_frequency',
		'search_weight_age',
		'search_weight_length',
		'search_weight_subject',
		'search_weight_first_message',
		'search_weight_sticky',
	);

	// A form was submitted.
	if (isset($_POST['save']))
	{
		checkSession();

		$changes = array();
		foreach ($factors as $factor)
			$changes[$factor] = (int) $_POST[$factor];
		updateSettings($changes);
	}

	$context['relative_weights'] = array('total' => 0);
	foreach ($factors as $factor)
		$context['relative_weights']['total'] += isset($modSettings[$factor]) ? $modSettings[$factor] : 0;

	foreach ($factors as $factor)
		$context['relative_weights'][$factor] = round(100 * (isset($modSettings[$factor]) ? $modSettings[$factor] : 0) / $context['relative_weights']['total'], 1);
}

function EditSearchMethod()
{
	global $txt, $context, $modSettings, $db_type, $db_prefix;

	$context[$context['admin_menu_name']]['current_subsection'] = 'method';
	$context['page_title'] = $txt['search_method_title'];
	$context['sub_template'] = 'select_search_method';

	// Load any apis.
	$context['search_apis'] = loadSearchAPIs();

	if (!empty($_REQUEST['sa']) && $_REQUEST['sa'] == 'removecustom')
	{
		checkSession('get');

		db_extend();
		$tables = smf_db_list_tables(false, $db_prefix . 'log_search_words');
		if (!empty($tables))
		{
			smf_db_query('
				DROP TABLE {db_prefix}log_search_words',
				array(
				)
			);
		}

		updateSettings(array(
			'search_custom_index_config' => '',
			'search_custom_index_resume' => '',
		));

		// Go back to the default search method.
		if (!empty($modSettings['search_index']) && $modSettings['search_index'] == 'custom')
			updateSettings(array(
				'search_index' => '',
			));
	}
	elseif (isset($_POST['save']))
	{
		checkSession();
		updateSettings(array(
			'search_index' => empty($_POST['search_index']) || (!in_array($_POST['search_index'], array('custom', 'sphinx', 'sphinxql')) && !isset($context['search_apis'][$_POST['search_index']])) ? '' : $_POST['search_index'],
			'search_force_index' => isset($_POST['search_force_index']) ? '1' : '0',
			'search_match_words' => isset($_POST['search_match_words']) ? '1' : '0',
		));
	}

	$context['table_info'] = array(
		'data_length' => 0,
		'index_length' => 0,
		'fulltext_length' => 0,
		'custom_index_length' => 0,
	);

	// Get some info about the messages table, to show its size and index size.
	if ($db_type == 'mysql')
	{
		if (preg_match('~^`(.+?)`\.(.+?)$~', $db_prefix, $match) !== 0)
			$request = smf_db_query( '
				SHOW TABLE STATUS
				FROM {string:database_name}
				LIKE {string:table_name}',
				array(
					'database_name' => '`' . strtr($match[1], array('`' => '')) . '`',
					'table_name' => str_replace('_', '\_', $match[2]) . 'messages',
				)
			);
		else
			$request = smf_db_query( '
				SHOW TABLE STATUS
				LIKE {string:table_name}',
				array(
					'table_name' => str_replace('_', '\_', $db_prefix) . 'messages',
				)
			);
		if ($request !== false && mysql_num_rows($request) == 1)
		{
			// Only do this if the user has permission to execute this query.
			$row = mysql_fetch_assoc($request);
			$context['table_info']['data_length'] = $row['Data_length'];
			$context['table_info']['index_length'] = $row['Index_length'];
			$context['table_info']['fulltext_length'] = $row['Index_length'];
			mysql_free_result($request);
		}

		// Now check the custom index table, if it exists at all.
		if (preg_match('~^`(.+?)`\.(.+?)$~', $db_prefix, $match) !== 0)
			$request = smf_db_query( '
				SHOW TABLE STATUS
				FROM {string:database_name}
				LIKE {string:table_name}',
				array(
					'database_name' => '`' . strtr($match[1], array('`' => '')) . '`',
					'table_name' => str_replace('_', '\_', $match[2]) . 'log_search_words',
				)
			);
		else
			$request = smf_db_query( '
				SHOW TABLE STATUS
				LIKE {string:table_name}',
				array(
					'table_name' => str_replace('_', '\_', $db_prefix) . 'log_search_words',
				)
			);
		if ($request !== false && mysql_num_rows($request) == 1)
		{
			// Only do this if the user has permission to execute this query.
			$row = mysql_fetch_assoc($request);
			$context['table_info']['index_length'] += $row['Data_length'] + $row['Index_length'];
			$context['table_info']['custom_index_length'] = $row['Data_length'] + $row['Index_length'];
			mysql_free_result($request);
		}
	}
	else
		$context['table_info'] = array(
			'data_length' => $txt['not_applicable'],
			'index_length' => $txt['not_applicable'],
			'fulltext_length' => $txt['not_applicable'],
			'custom_index_length' => $txt['not_applicable'],
		);

	// Format the data and index length in kilobytes.
	foreach ($context['table_info'] as $type => $size)
	{
		// If it's not numeric then just break.  This database engine doesn't support size.
		if (!is_numeric($size))
			break;

		$context['table_info'][$type] = comma_format($context['table_info'][$type] / 1024) . ' ' . $txt['search_method_kilobytes'];
	}

	$context['custom_index'] = !empty($modSettings['search_custom_index_config']);
	$context['partial_custom_index'] = !empty($modSettings['search_custom_index_resume']) && empty($modSettings['search_custom_index_config']);
	$context['double_index'] = !empty($context['fulltext_index']) && $context['custom_index'];
}

function CreateMessageIndex()
{
	global $modSettings, $context, $db_prefix, $txt;

	// Scotty, we need more time...
	@set_time_limit(600);
	if (function_exists('apache_reset_timeout'))
		@apache_reset_timeout();

	$context[$context['admin_menu_name']]['current_subsection'] = 'method';
	$context['page_title'] = $txt['search_index_custom'];

	$messages_per_batch = 50;

	$index_properties = array(
		2 => array(
			'column_definition' => 'small',
			'step_size' => 1000000,
		),
		4 => array(
			'column_definition' => 'medium',
			'step_size' => 1000000,
			'max_size' => 16777215,
		),
		5 => array(
			'column_definition' => 'large',
			'step_size' => 100000000,
			'max_size' => 2000000000,
		),
	);

	if (isset($_REQUEST['resume']) && !empty($modSettings['search_custom_index_resume']))
	{
		$context['index_settings'] = unserialize($modSettings['search_custom_index_resume']);
		$context['start'] = (int) $context['index_settings']['resume_at'];
		unset($context['index_settings']['resume_at']);
		$context['step'] = 1;
	}
	else
	{
		$context['index_settings'] = array(
			'bytes_per_word' => isset($_REQUEST['bytes_per_word']) && isset($index_properties[$_REQUEST['bytes_per_word']]) ? (int) $_REQUEST['bytes_per_word'] : 2,
		);
		$context['start'] = isset($_REQUEST['start']) ? (int) $_REQUEST['start'] : 0;
		$context['step'] = isset($_REQUEST['step']) ? (int) $_REQUEST['step'] : 0;
	}

	if ($context['step'] !== 0)
		checkSession('request');

	// Step 0: let the user determine how they like their index.
	if ($context['step'] === 0)
	{
		$context['sub_template'] = 'create_index';
	}

	// Step 1: insert all the words.
	if ($context['step'] === 1)
	{
		$context['sub_template'] = 'create_index_progress';

		if ($context['start'] === 0)
		{
			db_extend();
			$tables = smf_db_list_tables(false, $db_prefix . 'log_search_words');
			if (!empty($tables))
			{
				smf_db_query('
					DROP TABLE {db_prefix}log_search_words',
					array(
					)
				);
			}

			smf_db_create_word_search($index_properties[$context['index_settings']['bytes_per_word']]['column_definition']);

			// Temporarily switch back to not using a search index.
			if (!empty($modSettings['search_index']) && $modSettings['search_index'] == 'custom')
				updateSettings(array('search_index' => ''));

			// Don't let simultanious processes be updating the search index.
			if (!empty($modSettings['search_custom_index_config']))
				updateSettings(array('search_custom_index_config' => ''));
		}

		$num_messages = array(
			'done' => 0,
			'todo' => 0,
		);

		$request = smf_db_query( '
			SELECT id_msg >= {int:starting_id} AS todo, COUNT(*) AS num_messages
			FROM {db_prefix}messages
			GROUP BY todo',
			array(
				'starting_id' => $context['start'],
			)
		);
		while ($row = mysql_fetch_assoc($request))
			$num_messages[empty($row['todo']) ? 'done' : 'todo'] = $row['num_messages'];

		if (empty($num_messages['todo']))
		{
			$context['step'] = 2;
			$context['percentage'] = 80;
			$context['start'] = 0;
		}
		else
		{
			// Number of seconds before the next step.
			$stop = time() + 3;
			while (time() < $stop)
			{
				$inserts = array();
				$request = smf_db_query( '
					SELECT id_msg, body
					FROM {db_prefix}messages
					WHERE id_msg BETWEEN {int:starting_id} AND {int:ending_id}
					LIMIT {int:limit}',
					array(
						'starting_id' => $context['start'],
						'ending_id' => $context['start'] + $messages_per_batch - 1,
						'limit' => $messages_per_batch,
					)
				);
				$forced_break = false;
				$number_processed = 0;
				while ($row = mysql_fetch_assoc($request))
				{
					// In theory it's possible for one of these to take friggin ages so add more timeout protection.
					if ($stop < time())
					{
						$forced_break = true;
						break;
					}

					$number_processed++;
					foreach (text2words($row['body'], $context['index_settings']['bytes_per_word'], true) as $id_word)
					{
						$inserts[] = array($id_word, $row['id_msg']);
					}
				}
				$num_messages['done'] += $number_processed;
				$num_messages['todo'] -= $number_processed;
				mysql_free_result($request);

				$context['start'] += $forced_break ? $number_processed : $messages_per_batch;

				if (!empty($inserts))
					smf_db_insert('ignore',
						'{db_prefix}log_search_words',
						array('id_word' => 'int', 'id_msg' => 'int'),
						$inserts,
						array('id_word', 'id_msg')
					);
				if ($num_messages['todo'] === 0)
				{
					$context['step'] = 2;
					$context['start'] = 0;
					break;
				}
				else
					updateSettings(array('search_custom_index_resume' => serialize(array_merge($context['index_settings'], array('resume_at' => $context['start'])))));
			}

			// Since there are still two steps to go, 90% is the maximum here.
			$context['percentage'] = round($num_messages['done'] / ($num_messages['done'] + $num_messages['todo']), 3) * 80;
		}
	}

	// Step 2: removing the words that occur too often and are of no use.
	elseif ($context['step'] === 2)
	{
		if ($context['index_settings']['bytes_per_word'] < 4)
			$context['step'] = 3;
		else
		{
			$stop_words = $context['start'] === 0 || empty($modSettings['search_stopwords']) ? array() : explode(',', $modSettings['search_stopwords']);
			$stop = time() + 3;
			$context['sub_template'] = 'create_index_progress';
			$max_messages = ceil(60 * $modSettings['totalMessages'] / 100);

			while (time() < $stop)
			{
				$request = smf_db_query( '
					SELECT id_word, COUNT(id_word) AS num_words
					FROM {db_prefix}log_search_words
					WHERE id_word BETWEEN {int:starting_id} AND {int:ending_id}
					GROUP BY id_word
					HAVING COUNT(id_word) > {int:minimum_messages}',
					array(
						'starting_id' => $context['start'],
						'ending_id' => $context['start'] + $index_properties[$context['index_settings']['bytes_per_word']]['step_size'] - 1,
						'minimum_messages' => $max_messages,
					)
				);
				while ($row = mysql_fetch_assoc($request))
					$stop_words[] = $row['id_word'];
				mysql_free_result($request);

				updateSettings(array('search_stopwords' => implode(',', $stop_words)));

				if (!empty($stop_words))
					smf_db_query( '
						DELETE FROM {db_prefix}log_search_words
						WHERE id_word in ({array_int:stop_words})',
						array(
							'stop_words' => $stop_words,
						)
					);

				$context['start'] += $index_properties[$context['index_settings']['bytes_per_word']]['step_size'];
				if ($context['start'] > $index_properties[$context['index_settings']['bytes_per_word']]['max_size'])
				{
					$context['step'] = 3;
					break;
				}
			}
			$context['percentage'] = 80 + round($context['start'] / $index_properties[$context['index_settings']['bytes_per_word']]['max_size'], 3) * 20;
		}
	}

	// Step 3: remove words not distinctive enough.
	if ($context['step'] === 3)
	{
		$context['sub_template'] = 'create_index_done';

		updateSettings(array('search_index' => 'custom', 'search_custom_index_config' => serialize($context['index_settings'])));
		smf_db_query( '
			DELETE FROM {db_prefix}settings
			WHERE variable = {string:search_custom_index_resume}',
			array(
				'search_custom_index_resume' => 'search_custom_index_resume',
			)
		);
	}
}

// Get the installed APIs.
function loadSearchAPIs()
{
	global $sourcedir, $txt;

	$apis = array();
	if ($dh = opendir($sourcedir))
	{
		while (($file = readdir($dh)) !== false)
		{
			if (is_file($sourcedir . '/' . $file) && preg_match('~SearchAPI-([A-Za-z\d_]+)\.php~', $file, $matches))
			{
				// Check this is definitely a valid API!
				$fp = fopen($sourcedir . '/' . $file, 'rb');
				$header = fread($fp, 4096);
				fclose($fp);

				//if (strpos($header, '* SearchAPI-' . $matches[1] . '.php') !== false)
				//{
					loadClassFile($file);

					$index_name = strtolower($matches[1]);
					$search_class_name = $index_name . '_search';
					$searchAPI = new $search_class_name();

					// No Support?  NEXT!
					if (!$searchAPI->is_supported)
						continue;

					$apis[$index_name] = array(
						'filename' => $file,
						'setting_index' => $index_name,
						'has_template' => in_array($index_name, array('custom', 'fulltext', 'standard')),
						'label' => $index_name && isset($txt['search_index_' . $index_name]) ? $txt['search_index_' . $index_name] : '',
						'desc' => $index_name && isset($txt['search_index_' . $index_name . '_desc']) ? $txt['search_index_' . $index_name . '_desc'] : '',
					);
				//}
			}
		}
	}
	closedir($dh);

	return $apis;
}

function ManageSphinx()
{
	global $txt, $context, $modSettings, $sourcedir;

	if(isset($_REQUEST['save'])) {
		checkSession();
		updateSettings(array(
			'sphinx_data_path' => rtrim($_REQUEST['sphinx_data_path'], '/'),
			'sphinx_log_path' => rtrim($_REQUEST['sphinx_log_path'], '/'),
			'sphinx_stopword_path' => $_REQUEST['sphinx_stopword_path'],
			'sphinx_indexer_mem' => (int) $_REQUEST['sphinx_indexer_mem'],
			'sphinx_searchd_server' => $_REQUEST['sphinx_searchd_server'],
			'sphinx_searchd_port' => (int) $_REQUEST['sphinx_searchd_port'],
			'sphinxql_searchd_port' => (int) $_REQUEST['sphinxql_searchd_port'],
			'sphinx_max_results' => (int) $_REQUEST['sphinx_max_results'],
		));
		redirectexit('action=admin;area=managesearch;sa=managesphinx;' . $context['session_var'] . '=' . $context['session_id']);
	}
	else if(isset($_REQUEST['checkconnect'])) {
		//checkSession();
		$context['checkresult']['message'] = $txt['sphinx_test_passed']; 
		$context['checkresult']['result'] = true;
		if(@file_exists($sourcedir . '/contrib/sphinxapi.php')) {
			include_once($sourcedir . '/contrib/sphinxapi.php');
			$mySphinx = new SphinxClient();
			$mySphinx->SetServer($modSettings['sphinx_searchd_server'], (int) $modSettings['sphinx_searchd_port']);
			$mySphinx->SetLimits(0, (int) $modSettings['sphinx_max_results']);
			$mySphinx->SetMatchMode(SPH_MATCH_BOOLEAN);
			$mySphinx->SetSortMode(SPH_SORT_ATTR_ASC, 'ID_TOPIC');
			
			$request = $mySphinx->Query('test', 'smf_index');
			if ($request === false) {
				$context['checkresult']['result'] = false;
				$context['checkresult']['message'] = $txt['sphinx_test_connect_failed'];
			}
		}
		else {
			$context['checkresult']['result'] = false;
			$context['checkresult']['message'] = $txt['sphinx_test_api_missing'];
		}
		// try to connect via SphinxQL
		$result = mysql_connect(($modSettings['sphinx_searchd_server'] == 'localhost' ? '127.0.0.1' : $modSettings['sphinx_searchd_server']) . ':' . (int) $modSettings['sphinxql_searchd_port']);
		if(false === $result) {
			$context['checkresult']['result'] = false;
			$context['checkresult']['message'] = $txt['sphinx_test_ql_connect_failed'];
		}
	}
	$context['page_title'] = $txt['search_managesphinx'];
	$context['sub_template'] = 'manage_sphinx';
}

function CreateSphinxConfig()
{
	global $context, $db_server, $db_name, $db_user, $db_passwd, $db_prefix;
	global $modSettings;

	$humungousTopicPosts = 200;

	ob_end_clean();
	header('Pragma: ');
	if (!$context['browser']['is_gecko'])
		header('Content-Transfer-Encoding: binary');
	header('Connection: close');
	header('Content-Disposition: attachment; filename="sphinx.conf"');
	header('Content-Type: application/octet-stream');

	$weight_factors = array(
		'age',
		'length',
		'first_message',
		'sticky',
	);
	$weight = array();
	$weight_total = 0;
	foreach ($weight_factors as $weight_factor)
	{
		$weight[$weight_factor] = empty($modSettings['search_weight_' . $weight_factor]) ? 0 : (int) $modSettings['search_weight_' . $weight_factor];
		$weight_total += $weight[$weight_factor];
	}

	if ($weight_total === 0)
	{
		$weight = array(
			'age' => 25,
			'length' => 25,
			'first_message' => 25,
			'sticky' => 25,
		);
		$weight_total = 100;
	}


	echo '#
# Sphinx configuration file (sphinx.conf), configured for SMF 2
#
# By default the location of this file would probably be:
# /usr/local/etc/sphinx.conf

source smf_source
{
	type = mysql
	sql_host = ', $db_server, '
	sql_user = ', $db_user, '
	sql_pass = ', $db_passwd, '
	sql_db = ', $db_name, '
	sql_port = 3306
	sql_query_pre = SET NAMES utf8
	sql_query_pre =	\
		REPLACE INTO ', $db_prefix, 'settings (variable, value) \
		SELECT \'sphinx_indexed_msg_until\', MAX(ID_MSG) \
		FROM ', $db_prefix, 'messages
	sql_query_range = \
		SELECT 1, value \
		FROM ', $db_prefix, 'settings \
		WHERE variable = \'sphinx_indexed_msg_until\'
	sql_range_step = 1000
	sql_query =	\
		SELECT \
			m.ID_MSG, m.ID_TOPIC, m.ID_BOARD, IF(m.ID_MEMBER = 0, 4294967295, m.ID_MEMBER) AS ID_MEMBER, m.poster_time, m.body, m.subject, \
			t.num_replies + 1 AS num_replies, CEILING(1000000 * ( \
				IF(m.ID_MSG < 0.7 * s.value, 0, (m.ID_MSG - 0.7 * s.value) / (0.3 * s.value)) * ' . $weight['age'] . ' + \
				IF(t.num_replies < 200, t.num_replies / 200, 1) * ' . $weight['length'] . ' + \
				IF(m.ID_MSG = t.ID_FIRST_MSG, 1, 0) * ' . $weight['first_message'] . ' + \
				IF(t.is_sticky = 0, 0, 1) * ' . $weight['sticky'] . ' \
			) / ' . $weight_total . ') AS relevance \
		FROM ', $db_prefix, 'messages AS m, ', $db_prefix, 'topics AS t, ', $db_prefix, 'settings AS s \
		WHERE t.ID_TOPIC = m.ID_TOPIC \
			AND s.variable = \'maxMsgID\' \
			AND m.ID_MSG BETWEEN $start AND $end
	sql_attr_uint = ID_TOPIC
	sql_attr_uint = ID_BOARD
	sql_attr_uint = ID_MEMBER
	sql_attr_timestamp = poster_time
	sql_attr_timestamp = relevance
	sql_attr_timestamp = num_replies
	sql_query_info = \
		SELECT * \
		FROM ', $db_prefix, 'messages \
		WHERE ID_MSG = $id
}

source smf_delta_source : smf_source
{
	sql_query_pre = SET NAMES utf8
	sql_query_range = \
		SELECT s1.value, s2.value \
		FROM ', $db_prefix, 'settings AS s1, ', $db_prefix, 'settings AS s2 \
		WHERE s1.variable = \'sphinx_indexed_msg_until\' \
			AND s2.variable = \'maxMsgID\'
}

index smf_base_index
{
	source = smf_source
	path = ', $modSettings['sphinx_data_path'], '/smf_sphinx_base.index', empty($modSettings['sphinx_stopword_path']) ? '' : '
	stopwords = ' . $modSettings['sphinx_stopword_path'], '
	html_strip = 1
	min_word_len = 2
	charset_type = utf-8
	charset_table = 0..9, A..Z->a..z, _, a..z
}

index smf_delta_index : smf_base_index
{
	source = smf_delta_source
	path = ', $modSettings['sphinx_data_path'], '/smf_sphinx_delta.index
}

index smf_index
{
	type = distributed
	local = smf_base_index
	local = smf_delta_index
}

indexer
{
	mem_limit = ', (int) $modSettings['sphinx_indexer_mem'], 'M
}

searchd
{
	listen = ', (int) $modSettings['sphinx_searchd_port'], '
	listen = ', (int) $modSettings['sphinxql_searchd_port'], ':mysql41
	log = ', $modSettings['sphinx_log_path'], '/searchd.log
	query_log = ', $modSettings['sphinx_log_path'], '/query.log
	read_timeout = 5
	max_children = 30
	pid_file = ', $modSettings['sphinx_data_path'], '/searchd.pid
	max_matches = 1000
}
';

	obExit(false, false, false);
}
