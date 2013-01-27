<?php
/**
 * @name      EosAlpha BBS
 * @copyright 2011 Alex Vie silvercircle(AT)gmail(DOT)com
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

function ManagePostSettings()
{
	global $context, $txt, $modSettings;

	// Make sure you can be here.
	isAllowedTo('admin_forum');

	$subActions = array(
		'posts' => 'ModifyPostSettings',
		'bbc' => 'ModifyBBCSettings',
		'censor' => 'SetCensor',
		'topics' => 'ModifyTopicSettings',
		'prefixes' => 'ModifyPrefixSettings',
		'ratings' => 'ModifyRatingSettings'
	);
	if($modSettings['tags_active'])
		$subActions['tags'] = 'ModifyTagSettings';
	
	//if(in_array('dr', $context['admin_features']))
	//	$subActions['drafts'] = 'ModifyDraftSettings'; todo: drafts -> plugin

	// Default the sub-action to 'posts'.
	$_REQUEST['sa'] = isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) ? $_REQUEST['sa'] : 'posts';

	$context['page_title'] = $txt['manageposts_title'];

	// Tabs for browsing the different ban functions.
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $txt['manageposts_title'],
		'help' => 'posts_and_topics',
		'description' => $txt['manageposts_description'],
		'tabs' => array(
			'posts' => array(
				'description' => $txt['manageposts_settings_description'],
			),
			'bbc' => array(
				'description' => $txt['manageposts_bbc_settings_description'],
			),
			'censor' => array(
				'description' => $txt['admin_censored_desc'],
			),
			'topics' => array(
				'description' => $txt['manageposts_topic_settings_description'],
			),
		),
	);
	/* todo: drafts -> plugin
	if(in_array('dr', $context['admin_features']))
		$context[$context['admin_menu_name']]['tab_data']['tabs']['drafts'] = array(
			'label' => $txt['manageposts_draft_label'],
			'description' => $txt['manageposts_draft_settings_desc'],
		);
	*/
	$subActions[$_REQUEST['sa']]();
}

// Set the censored words.
function SetCensor()
{
	global $txt, $modSettings, $context;

	if (!empty($_POST['save_censor']))
	{
		// Make sure censoring is something they can do.
		checkSession();

		$censored_vulgar = array();
		$censored_proper = array();

		// Rip it apart, then split it into two arrays.
		if (isset($_POST['censortext']))
		{
			$_POST['censortext'] = explode("\n", strtr($_POST['censortext'], array("\r" => '')));

			foreach ($_POST['censortext'] as $c)
				list ($censored_vulgar[], $censored_proper[]) = array_pad(explode('=', trim($c)), 2, '');
		}
		elseif (isset($_POST['censor_vulgar'], $_POST['censor_proper']))
		{
			if (is_array($_POST['censor_vulgar']))
			{
				foreach ($_POST['censor_vulgar'] as $i => $value)
				{
					if (trim(strtr($value, '*', ' ')) == '')
						unset($_POST['censor_vulgar'][$i], $_POST['censor_proper'][$i]);
				}

				$censored_vulgar = $_POST['censor_vulgar'];
				$censored_proper = $_POST['censor_proper'];
			}
			else
			{
				$censored_vulgar = explode("\n", strtr($_POST['censor_vulgar'], array("\r" => '')));
				$censored_proper = explode("\n", strtr($_POST['censor_proper'], array("\r" => '')));
			}
		}

		// Set the new arrays and settings in the database.
		$updates = array(
			'censor_vulgar' => implode("\n", $censored_vulgar),
			'censor_proper' => implode("\n", $censored_proper),
			'censorWholeWord' => empty($_POST['censorWholeWord']) ? '0' : '1',
			'censorIgnoreCase' => empty($_POST['censorIgnoreCase']) ? '0' : '1',
		);

		updateSettings($updates);
	}

	if (isset($_POST['censortest']))
	{
		$censorText = htmlspecialchars($_POST['censortest'], ENT_QUOTES);
		$context['censor_test'] = strtr(censorText($censorText), array('"' => '&quot;'));
	}

	// Set everything up for the template to do its thang.
	$censor_vulgar = explode("\n", $modSettings['censor_vulgar']);
	$censor_proper = explode("\n", $modSettings['censor_proper']);

	$context['censored_words'] = array();
	for ($i = 0, $n = count($censor_vulgar); $i < $n; $i++)
	{
		if (empty($censor_vulgar[$i]))
			continue;

		// Skip it, it's either spaces or stars only.
		if (trim(strtr($censor_vulgar[$i], '*', ' ')) == '')
			continue;

		$context['censored_words'][htmlspecialchars(trim($censor_vulgar[$i]))] = isset($censor_proper[$i]) ? htmlspecialchars($censor_proper[$i]) : '';
	}

	$context['sub_template'] = 'edit_censored';
	$context['page_title'] = $txt['admin_censored_words'];
}

// Modify all settings related to posts and posting.
function ModifyPostSettings($return_config = false)
{
	global $context, $txt, $modSettings, $scripturl, $sourcedir, $backend_subdir;

	// All the settings...
	$config_vars = array(
			// Simple post options...
			array('check', 'enableUserTagging'),
			array('int', 'maxTagsPerPost'),
			array('check', 'removeNestedQuotes'),
			array('check', 'disable_wysiwyg'),
			array('check', 'use_post_cache'),
			array('int', 'post_cache_cutoff'),
		'',
			// Posting limits...
			array('int', 'max_messageLength', 'subtext' => $txt['max_messageLength_zero'], 'postinput' => $txt['manageposts_characters']),
			array('int', 'fixLongWords', 'subtext' => $txt['fixLongWords_zero'] . ($context['utf8'] ? ' <span class="alert">' . $txt['fixLongWords_warning'] . '</span>' : ''), 'postinput' => $txt['manageposts_characters']),
			array('int', 'topicSummaryPosts', 'postinput' => $txt['manageposts_posts']),
		'',
			// Posting time limits...
			array('int', 'spamWaitTime', 'postinput' => $txt['manageposts_seconds']),
			array('int', 'edit_wait_time', 'postinput' => $txt['manageposts_seconds']),
			array('int', 'edit_disable_time', 'subtext' => $txt['edit_disable_time_zero'], 'postinput' => $txt['manageposts_minutes']),
	);

	if(!isset($modSettings['post_cache_cutoff']) || $modSettings['post_cache_cutoff'] < 10)
		$modSettings['post_cache_cutoff'] = 10;
	
	if($modSettings['post_cache_cutoff'] > 9999)
		$modSettings['post_cache_cutoff'] = 9999;

	if(empty($modSettings['maxTagsPerPost']) || $modSettings['maxTagsPerPost'] > 20)
		$modSettings['maxTagsPerPost'] = 10;
	
	if ($return_config)
		return $config_vars;

	// We'll want this for our easy save.
	require_once($sourcedir . '/' . $backend_subdir . '/ManageServer.php');

	// Setup the template.
	$context['page_title'] = $txt['manageposts_settings'];
	$context['sub_template'] = 'show_settings';

	// Are we saving them - are we??
	if (isset($_GET['save']))
	{
		checkSession();

		// If we're changing the message length let's check the column is big enough.
		if (!empty($_POST['max_messageLength']) && $_POST['max_messageLength'] != $modSettings['max_messageLength'])
		{
			db_extend('packages');

			$colData = smf_db_list_columns('{db_prefix}messages', true);
			foreach ($colData as $column)
				if ($column['name'] == 'body')
					$body_type = $column['type'];

			$indData = smf_db_list_indexes('{db_prefix}messages', true);
			foreach ($indData as $index)
				foreach ($index['columns'] as $column)
					if ($column == 'body' && $index['type'] == 'fulltext')
						$fulltext = true;

			if (isset($body_type) && $_POST['max_messageLength'] > 65535 && $body_type == 'text')
			{
				// !!! Show an error message?!
				// MySQL only likes fulltext indexes on text columns... for now?
				if (!empty($fulltext))
					$_POST['max_messageLength'] = 65535;
				else
				{
					// Make it longer so we can do their limit.
					smf_db_change_column('{db_prefix}messages', 'body', array('type' => 'mediumtext'));
				}
			}
			elseif (isset($body_type) && $_POST['max_messageLength'] <= 65535 && $body_type != 'text')
			{
				// Shorten the column so we can have the benefit of fulltext searching again!
				smf_db_change_column('{db_prefix}messages', 'body', array('type' => 'text'));
			}
		}

		saveDBSettings($config_vars);
		redirectexit('action=admin;area=postsettings;sa=posts');
	}

	// Final settings...
	$context['post_url'] = $scripturl . '?action=admin;area=postsettings;save;sa=posts';
	$context['settings_title'] = $txt['manageposts_settings'];

	// Prepare the settings...
	prepareDBSettingContext($config_vars);
}

// Bulletin Board Code...a lot of Bulletin Board Code.
function ModifyBBCSettings($return_config = false)
{
	global $context, $txt, $modSettings, $scripturl, $sourcedir, $backend_subdir;

	$config_vars = array(
			// Main tweaks
			array('check', 'enableBBC'),
			array('check', 'enablePostHTML'),
			array('check', 'autoLinkUrls'),
			array('check', 'legacyBBC'),
			array('check', 'linkSecurity'),
			'',
			array('bbc', 'disabledBBC'),
			array('callback_template', 'template_bbc_settings')
	);

	if ($return_config)
		return $config_vars;

	// Setup the template.
	require_once($sourcedir . '/' . $backend_subdir . '/ManageServer.php');
	$context['sub_template'] = 'show_settings';
	$context['page_title'] = $txt['manageposts_bbc_settings_title'];

	// Make sure we check the right tags!
	$modSettings['bbc_disabled_disabledBBC'] = empty($modSettings['disabledBBC']) ? array() : explode(',', $modSettings['disabledBBC']);

	// Saving?
	if (isset($_GET['save']))
	{
		checkSession();

		// Clean up the tags.
		$bbcTags = array();
		foreach (parse_bbc(false) as $tag)
			$bbcTags[] = $tag['tag'];

		if (!isset($_POST['disabledBBC_enabledTags']))
			$_POST['disabledBBC_enabledTags'] = array();
		elseif (!is_array($_POST['disabledBBC_enabledTags']))
			$_POST['disabledBBC_enabledTags'] = array($_POST['disabledBBC_enabledTags']);
		// Work out what is actually disabled!
		$_POST['disabledBBC'] = implode(',', array_diff($bbcTags, $_POST['disabledBBC_enabledTags']));

		$hidden_content_messages = array();
		for($i = 1; $i <= 3; $i++) {
			if(isset($_POST['hidden_content_level_' . $i]) && !empty($_POST['hidden_content_level_' . $i]))
				$hidden_content_messages[$i] = $_POST['hidden_content_level_' . $i];
		}
		if(isset($_POST['legacyBBC']) && !empty($_POST['legacyBBC']))
			HookAPI::addHook('integrate_bbc_codes', 'LegacyBBC', 'main.php', 'legacybbc_addtags');
		else
			HookAPI::removeAll('LegacyBBC');

		saveDBSettings($config_vars);
		if(count($hidden_content_messages))
			updateSettings(array('hidden_content_no_view' => serialize($hidden_content_messages)));
		redirectexit('action=admin;area=postsettings;sa=bbc');
	}

	$context['post_url'] = $scripturl . '?action=admin;area=postsettings;save;sa=bbc';
	$context['settings_title'] = $txt['manageposts_bbc_settings_title'];

	prepareDBSettingContext($config_vars);
}

// Function for modifying topic settings. Not very exciting.
function ModifyTopicSettings($return_config = false)
{
	global $context, $txt, $sourcedir, $scripturl, $backend_subdir;

	// Here are all the topic settings.
	$config_vars = array(
			// Some simple bools...
			array('check', 'enableStickyTopics'),
			array('check', 'enableParticipation'),
		'',
			// Pagination etc...
			array('int', 'oldTopicDays', 'postinput' => $txt['manageposts_days'], 'subtext' => $txt['oldTopicDays_zero']),
			array('int', 'defaultMaxTopics', 'postinput' => $txt['manageposts_topics']),
			array('int', 'defaultMaxMessages', 'postinput' => $txt['manageposts_posts']),
		'',
			// Hot topics (etc)...
			array('int', 'hotTopicPosts', 'postinput' => $txt['manageposts_posts']),
			array('int', 'hotTopicVeryPosts', 'postinput' => $txt['manageposts_posts']),
		'',
			// All, next/prev...
			array('int', 'enableAllMessages', 'postinput' => $txt['manageposts_posts'], 'subtext' => $txt['enableAllMessages_zero']),
			array('check', 'disableCustomPerPage'),
			array('check', 'enablePreviousNext'),
			array('int', 'whatsNewCutoffDays', 'postinput' => $txt['manageposts_days']),
	);

	if ($return_config)
		return $config_vars;

	// Get the settings template ready.
	require_once($sourcedir . '/' . $backend_subdir . '/ManageServer.php');

	// Setup the template.
	$context['page_title'] = $txt['manageposts_topic_settings'];
	$context['sub_template'] = 'show_settings';

	// Are we saving them - are we??
	if (isset($_GET['save']))
	{
		checkSession();

		saveDBSettings($config_vars);
		redirectexit('action=admin;area=postsettings;sa=topics');
	}

	// Final settings...
	$context['post_url'] = $scripturl . '?action=admin;area=postsettings;save;sa=topics';
	$context['settings_title'] = $txt['manageposts_topic_settings'];

	// Prepare the settings...
	prepareDBSettingContext($config_vars);
}

function getPrefixes()
{
	global $context;
	
	$request = smf_db_query( '
		SELECT * FROM {db_prefix}prefixes');
	
	while($row = mysql_fetch_assoc($request)) {
		$context['prefixes'][$row['id_prefix']] = $row;
		$context['prefixes'][$row['id_prefix']]['preview'] = html_entity_decode($row['name']);
	}
	mysql_free_result($request);
}

function ModifyDraftSettings($return_config = false)
{
	global $txt, $sourcedir, $context, $scripturl, $backend_subdir;
	$config_vars = array(
		array('int', 'enableAutoSaveDrafts', 'subtext' => $txt['draftsave_subnote'], 'postinput' => $txt['manageposts_seconds']),
	);

	$context['page_title'] = $txt['manageposts_draft_settings'];
	$context['sub_template'] = 'show_settings';

	if ($return_config)
		return $config_vars;

	require_once($sourcedir . '/' . $backend_subdir . '/ManageServer.php');

	if (isset($_GET['save']))
	{
		checkSession();

		saveDBSettings($config_vars);
		redirectexit('action=admin;area=postsettings;sa=drafts');
	}

	// Final settings...
	$context['post_url'] = $scripturl . '?action=admin;area=postsettings;save;sa=drafts';
	$context['settings_title'] = $txt['manageposts_draft_settings'];

	prepareDBSettingContext($config_vars);
}

function ModifyPrefixSettings()
{
	global $context, $txt;
	
	$context['page_title'] = $txt['manageposts_prefix_settings'];
	$context['settings_title'] = $txt['manageposts_prefix_settings'];
	$context['sub_template'] = 'prefix_settings';

	getPrefixes();
	
	if (isset($_GET['save']))
	{
		checkSession();
		// check existing ones for changes...
		foreach($context['prefixes'] as $prefix) {
			$id = $prefix['id_prefix'];
			if(isset($_POST['name_'.$id]) && strlen($_POST['name_'.$id]) >= 2) {
				/*if($_POST['name_'.$id] != $prefix['name'] || $_POST['html_before_'.$id] != $prefix['html_before'] ||
					$_POST['html_after_'.$id] != $prefix['html_after'] || $_POST['boards_'.$id] != $prefix['boards']) {
						smf_db_query( '
							UPDATE {db_prefix}prefixes SET name = {string:name}, html_before = {string:html_before},
							html_after = {string:html_after}, boards = {string:boards} WHERE id_prefix = {int:id_prefix}',
							array('id_prefix' => $id, 'name' => $_POST['name_'.$id], 'html_before' => htmlentities($_POST['html_before_'.$id]), 
								'html_after' => htmlentities($_POST['html_after_'.$id]), 'boards' => $_POST['boards_'.$id]));*/
				if($_POST['name_'.$id] != $prefix['name'] || $_POST['boards_'.$id] != $prefix['boards'] || $_POST['groups_'.$id] != $prefix['groups']) {
					$boards = normalizeCommaDelimitedList($_POST['boards_'.$id]);
					$groups = normalizeCommaDelimitedList($_POST['groups_'.$id]);
					smf_db_query( '
						UPDATE {db_prefix}prefixes SET name = {string:name}, boards = {string:boards}, groups = {string:groups} WHERE id_prefix = {int:id_prefix}',
						array('id_prefix' => $id, 'name' => htmlspecialchars($_POST['name_'.$id]),
						'boards' => $boards, 'groups' => $groups));
				}
			}
		}
		// check the new fields
		for($i = 0; $i < 5; $i++) {
			if(isset($_POST['name_new_'.$i]) && strlen($_POST['name_new_'.$i]) >= 2) {
				/*smf_db_query( '
					INSERT INTO {db_prefix}prefixes (name, html_before, html_after, boards) VALUES({string:name},
					{string:html_before}, {string:html_after}, {string:boards})',
					array('name' => $_POST['name_new_'.$i], 'html_before' => htmlentities($_POST['html_before_new_'.$i]),
						'html_after' => htmlentities($_POST['html_after_new_'.$i]), 'boards' => $_POST['boards_new_'.$i]));*/
				$boards = normalizeCommaDelimitedList($_POST['boards_new_'.$id]);
				$groups = normalizeCommaDelimitedList($_POST['groups_new_'.$id]);
				smf_db_query( '
					INSERT INTO {db_prefix}prefixes (name, boards, groups) VALUES({string:name},
					{string:boards}, {string:groups})',
					array('name' => htmlentities($_POST['name_new_'.$i]), 'boards' => $boards, 'groups' => $groups));
			}
		}
		redirectexit('action=admin;area=postsettings;sa=prefixes');
	}
}

/**
 * implements features and options -> Post ratings settings page
 */
function ModifyRatingSettings()
{
	global $scripturl, $context, $modSettings, $sourcedir;

	@require_once($sourcedir . '/lib/Subs-Ratings.php');

	loadAdminTemplate('ManageRatings');
	$context['sub_template'] = 'manage_ratings';
	$context['rating_show_repair'] = empty($modSettings['rating_show_repair']) ? false : true;
	$context['rating_allow_comments'] = empty($modSettings['rating_allow_comments']) ? false : true;
	
	for($i = 1; $i <= 10; $i++) {
		if(isset($modSettings['ratings'][$i])) {
			$rating = &$modSettings['ratings'][$i];
			$context['rating_classes'][$i] = array(
				'id' => $i,
				'desc' => isset($rating['desc']) ? $rating['desc'] : '',
				'format' => isset($rating['format']) ? $rating['format'] : '<span>%s</span>',
				'label' => $rating['label'],
				'localized' => isset($rating['localized']) ? $rating['localized'] : '',
				'points' => isset($rating['points']) ? $rating['points'] : 0,
				'groups' => isset($rating['groups']) && !empty($rating['groups']) ? implode(',', $rating['groups']) : '',
				'boards' => isset($rating['boards']) && !empty($rating['boards']) ? implode(',', $rating['boards']) : '',
				'boards_denied' => isset($rating['boards_denied']) && !empty($rating['boards_denied']) ? implode(',', $rating['boards_denied']) : '',
				'groups_denied' => isset($rating['groups_denied']) && !empty($rating['groups_denied']) ? implode(',', $rating['groups_denied']) : '',
				'unique' => isset($rating['unique']) && !empty($rating['unique']) ? true : false,
			);
		}
		else {
			$context['rating_classes'][$i] = array(
				'id' => $i,
				'desc' => '',
				'format' => '',
				'label' => '',
				'points' => '',
				'groups' => '',
				'boards' => '',
				'boards_denied' => '',
				'groups_denied' => '',
				'localized' => '',
				'unique' => true,
			);
		}
	}
	// Saving?
	if (isset($_GET['save']))
	{
		checkSession();
		$new_ratings = array();

		for($i = 1; $i <= 10; $i++) {
			if(isset($_REQUEST['rating_id_' . $i]) && (int)$_REQUEST['rating_id_' . $i] >= 1 && (int)$_REQUEST['rating_id_' . $i] <= 10 && isset($_REQUEST['rating_label_' . $i]) && !empty($_REQUEST['rating_label_' . $i])) {
				$new_ratings[$i] = array(
					'desc' => (isset($_REQUEST['rating_desc_' . $i]) ? strip_tags($_REQUEST['rating_desc_' . $i]) : ''),
					'format' => htmlentities(isset($_REQUEST['rating_format_' . $i]) ? $_REQUEST['rating_format_' . $i] : '<span>%s</span>'),
					'label' => htmlspecialchars(isset($_REQUEST['rating_label_' . $i]) ? $_REQUEST['rating_label_' . $i] : 'No label'),
					'localized' => htmlentities(isset($_REQUEST['rating_localized_' . $i]) ? $_REQUEST['rating_localized_' . $i] : ''),
					'groups' => isset($_REQUEST['rating_groups_' . $i]) && !empty($_REQUEST['rating_groups_' . $i]) ? explode(',', normalizeCommaDelimitedList($_REQUEST['rating_groups_' . $i])) : array(),
					'groups_denied' => isset($_REQUEST['rating_groups_denied_' . $i]) && !empty($_REQUEST['rating_groups_denied_' . $i]) ? explode(',', normalizeCommaDelimitedList($_REQUEST['rating_groups_denied_' . $i])) : array(),
					'boards' => isset($_REQUEST['rating_boards_' . $i]) && !empty($_REQUEST['rating_boards_' . $i]) ? explode(',', normalizeCommaDelimitedList($_REQUEST['rating_boards_' . $i])) : array(),
					'boards_denied' => isset($_REQUEST['rating_boards_denied_' . $i]) && !empty($_REQUEST['rating_boards_denied_' . $i]) ? explode(',', normalizeCommaDelimitedList($_REQUEST['rating_boards_denied_' . $i])) : array(),
					'points' => isset($_REQUEST['rating_points_' . $i]) && !empty($_REQUEST['rating_points_' . $i]) ? $_REQUEST['rating_points_' . $i] : 0,
					'unique' => isset($_REQUEST['rating_unique_' . $i]) && $_REQUEST['rating_unique_' . $i] ? true : false,
				);
			}
		}
		$settings_to_update = array(
			'rating_show_repair' => isset($_REQUEST['rating_show_repair']) ? $_REQUEST['rating_show_repair'] : 0,
			'rating_allow_comments' => isset($_REQUEST['rating_allow_comments']) ? $_REQUEST['rating_allow_comments'] : 0,
		);
		if(!empty($new_ratings))
			$settings_to_update['raw_ratings'] = @serialize($new_ratings);

		updateSettings($settings_to_update);
		redirectexit('action=admin;area=postsettings;sa=ratings');
	}

	$context['post_url'] = $scripturl . '?action=admin;area=postsettings;save;sa=ratings';
}

function ModifyTagSettings()
{
	global $context, $txt;

	isAllowedTo('smftags_manage');
	loadLanguage('Tagging');
	$context['page_title'] = $txt['manageposts_tag_settings'];
	$context['settings_title'] = $txt['manageposts_tag_settings'];
	$context['sub_template']  = 'tag_admin_settings';
	
	if (isset($_GET['save'])) {

		isAllowedTo('smftags_manage');

		$smftags_set_mintaglength = (int) $_REQUEST['smftags_set_mintaglength'];
		$smftags_set_maxtaglength =  (int) $_REQUEST['smftags_set_maxtaglength'];
		$smftags_set_maxtags =  (int) $_REQUEST['smftags_set_maxtags'];

		$smftags_set_cloud_tags_per_row = (int) $_REQUEST['smftags_set_cloud_tags_per_row'];
		$smftags_set_cloud_tags_to_show = (int) $_REQUEST['smftags_set_cloud_tags_to_show'];
		$smftags_set_cloud_max_font_size_precent = (int) $_REQUEST['smftags_set_cloud_max_font_size_precent'];
		$smftags_set_cloud_min_font_size_precent = (int) $_REQUEST['smftags_set_cloud_min_font_size_precent'];

		updateSettings(
			array('smftags_set_maxtags' => $smftags_set_maxtags,
				'smftags_set_mintaglength' => $smftags_set_mintaglength,
				'smftags_set_maxtaglength' => $smftags_set_maxtaglength,
				'smftags_set_cloud_tags_per_row' => $smftags_set_cloud_tags_per_row,
				'smftags_set_cloud_tags_to_show' => $smftags_set_cloud_tags_to_show,
				'smftags_set_cloud_max_font_size_precent' => $smftags_set_cloud_max_font_size_precent,
				'smftags_set_cloud_min_font_size_precent' => $smftags_set_cloud_min_font_size_precent,
				));
		redirectexit('action=admin;area=postsettings;sa=tags');
	}
}
