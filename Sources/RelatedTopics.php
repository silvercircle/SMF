<?php
/**
 * Related Topics
 *
 * @package RelatedTopics
 * @version 1.4
 */

function RelatedTopicsAdmin()
{
	global $context, $smcFunc, $sourcedir, $user_info, $txt, $related_version;

	require_once($sourcedir . '/Subs-Related.php');
	require_once($sourcedir . '/ManageServer.php');

	$related_version = '1.4';

	loadTemplate('RelatedTopicsAdmin');

	$context[$context['admin_menu_name']]['tab_data']['title'] = $txt['related_topics_admin_title'];
	$context[$context['admin_menu_name']]['tab_data']['description'] = $txt['related_topics_admin_desc'];

	$context['page_title'] = $txt['related_topics_admin_title'];

	$subActions = array(
		'main' => array('RelatedTopicsAdminMain'),
		'settings' => array('RelatedTopicsAdminSettings'),
		'methods' => array('RelatedTopicsAdminMethods'),
		'buildIndex' => array('RelatedTopicsAdminBuildIndex'),
	);

	$_REQUEST['sa'] = isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) ? $_REQUEST['sa'] : 'main';

	if (isset($subActions[$_REQUEST['sa']][1]))
		isAllowedTo($subActions[$_REQUEST['sa']][1]);

	$subActions[$_REQUEST['sa']][0]();
}

function RelatedTopicsAdminMain()
{
	global $context, $smcFunc, $sourcedir, $scripturl, $user_info, $txt;

	$context['sub_template'] = 'related_topics_admin_main';
}

function RelatedTopicsAdminSettings($return_config = false)
{
	global $context, $smcFunc, $sourcedir, $scripturl, $user_info, $txt;

	$config_vars = array(
		array('check', 'relatedTopicsEnabled'),
		array('int', 'relatedTopicsCount'),
	);

	if ($return_config)
		return $config_vars;

	if (isset($_GET['save']))
	{
		checkSession('post');
		saveDBSettings($config_vars);

		writeLog();

		redirectexit('action=admin;area=relatedtopics;sa=settings');
	}

	$context['post_url'] = $scripturl . '?action=admin;area=relatedtopics;sa=settings;save';
	$context['settings_title'] = $txt['related_topics_settings_title'];
	$context['sub_template'] = 'show_settings';

	prepareDBSettingContext($config_vars);
}

function RelatedTopicsAdminMethods()
{
	global $context, $smcFunc, $modSettings, $scripturl, $user_info, $txt, $db_type;
	
	initRelated();

	$relatedIndexes = !empty($modSettings['relatedIndex']) ? explode(',', $modSettings['relatedIndex']) : array();

	$context['related_methods'] = array(
		'fulltext' => array(
			'name' => $txt['relatedFulltext'],
			'selected' => false,
			'supported' => $db_type == 'mysql',
		),
	);

	foreach ($context['related_methods'] as $id => $dummy)
		$context['related_methods'][$id]['selected'] = in_array($id, $relatedIndexes);

	if (isset($_GET['save']))
	{
		checkSession('post');

		$methods = array();

		if (isset($_POST['related_methods']))
		{
			foreach ($_POST['related_methods'] as $method)
			{
				if (isset($context['related_methods'][$method]) && $context['related_methods'][$method]['supported'])
					$methods[] = $method;
			}
		}

		updateSettings(array(
			'relatedIndex' => implode(',', $methods),
			'relatedIgnoredboards' => !empty($_POST['ignored_boards']) ? implode(',', $_POST['ignored_boards']) : '',
		));
		
		redirectexit('action=admin;area=relatedtopics;sa=methods');
	}

	$request = $smcFunc['db_query']('', '
		SELECT b.id_board, b.name, c.id_cat, c.name AS cat_name
		FROM {db_prefix}boards AS b
			LEFT JOIN {db_prefix}categories AS c ON (c.id_cat = b.id_cat)
		WHERE b.redirect = {string:blank_redirect}'. (!empty($modSettings['recycle_enable']) && !empty($modSettings['recycle_board']) ? '
			AND NOT b.id_board = {int:recyle_board}' : ''),
		array(
			'blank_redirect' => '',
			'recyle_board' => $modSettings['recycle_board'],
		)
	);
	
	$context['categories'] = array();
	
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		if (!isset($context['categories'][$row['id_cat']]))
			$context['categories'][$row['id_cat']] = array(
				'id' => $row['id_cat'],
				'name' => $row['cat_name'],
				'boards' => array(),
			);
		
		$context['categories'][$row['id_cat']]['boards'][$row['id_board']] = array(
			'id' => $row['id_board'],
			'name' => $row['name'],
			'selected' => in_array($row['id_board'], $context['rt_ignore']),
		);
	}
	$smcFunc['db_free_result']($request);

	$context['sub_template'] = 'related_topics_admin_methods';
}

function RelatedTopicsAdminBuildIndex()
{
	global $smcFunc, $scripturl, $modSettings, $context, $txt;

	loadTemplate('Admin');
	loadLanguage('Admin');

	if (!isset($context['relatedClass']) && !initRelated())
		fatal_lang_error('no_methods_selected');
		
	$context['step'] = empty($_REQUEST['step']) ? 0 : (int) $_REQUEST['step'];

	if ($context['step'] == 0)
	{
		// Clear caches
		foreach ($context['relatedClass'] as $class)
			$class->recreateIndexTables();

		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}related_topics'
		);
	}

	$request = $smcFunc['db_query']('', '
		SELECT MAX(id_topic)
		FROM {db_prefix}topics');
	list ($max_topics) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);

	// How many topics to do per page load?
	$perStep = 150;
	$last = $context['step'] + $perStep;

	// Search for topic ids between first and last which are not in ignored boards
	$request = $smcFunc['db_query']('', '
		SELECT t.id_topic
		FROM {db_prefix}topics AS t
		WHERE t.id_topic > {int:start}
			AND t.id_topic <= {int:last}' . (!empty($context['rt_ignore']) ? '
			AND t.id_board NOT IN({array_int:ignored})' : ''),
		array(
			'start' => $context['step'],
			'last' => $last,
			'ignored' => $context['rt_ignore'],
		)
	);

	$topics = array();

	while ($row =  $smcFunc['db_fetch_assoc']($request))
		$topics[] = $row['id_topic'];
	$smcFunc['db_free_result']($request);

	// Update topics
	relatedUpdateTopics($topics, true);

	if ($last >= $max_topics)
		redirectexit('action=admin;area=relatedtopics;sa=methods');

	$context['sub_template'] = 'not_done';
	$context['continue_get_data'] = '?action=admin;area=relatedtopics;sa=buildIndex;step=' . $last;

	$context['continue_percent'] = round(100 * ($last / $max_topics));
	$context['continue_post_data'] = '';
	$context['continue_countdown'] = '2';
	
	obExit();
}

?>