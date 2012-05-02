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

/*	This file maintains all XML-based interaction (mainly XMLhttp).

	void GetJumpTo()

*/

function XMLhttpMain()
{
	loadTemplate('Xml');

	$sub_actions = array(
		'jumpto' => array(
			'function' => 'GetJumpTo',
		),
		'messageicons' => array(
			'function' => 'ListMessageIcons',
		),
		'mcard' => array('function' => 'GetMcard'),
		'givelike' => array('function' => 'HandleLikeRequest'),
		'mpeek' => array('function' => 'TopicPeek'),
		'tags' => array('function' => 'TagsActionDispatcher'),
		'whoposted' => array('function' => 'WhoPosted'),
		'prefix' => array('function' => 'InlinePrefixActions'),
		'collapse' => array('function' => 'AjaxCollapseCategory'),
		'sidebar' => array('function' => 'GetSidebarContent'),
	);
	if (!isset($_REQUEST['sa'], $sub_actions[$_REQUEST['sa']]))
		fatal_lang_error('no_access', false);

	$sub_actions[$_REQUEST['sa']]['function']();
}

// Get a list of boards and categories used for the jumpto dropdown.
function GetJumpTo()
{
	global $context, $sourcedir;

	// Find the boards/cateogories they can see.
	require_once($sourcedir . '/lib/Subs-MessageIndex.php');
	$boardListOptions = array(
		'use_permissions' => true,
		'selected_board' => isset($context['current_board']) ? $context['current_board'] : 0,
	);
	$context['jump_to'] = getBoardList($boardListOptions);

	// Make the board safe for display.
	foreach ($context['jump_to'] as $id_cat => $cat)
	{
		$context['jump_to'][$id_cat]['name'] = un_htmlspecialchars(strip_tags($cat['name']));
		foreach ($cat['boards'] as $id_board => $board)
			$context['jump_to'][$id_cat]['boards'][$id_board]['name'] = un_htmlspecialchars(strip_tags($board['name']));
	}

	$context['sub_template'] = 'jump_to';
}

function ListMessageIcons()
{
	global $context, $sourcedir, $board;

	require_once($sourcedir . '/lib/Subs-Editor.php');
	$context['icons'] = getMessageIcons($board);
	$context['id_msg'] = isset($_REQUEST['m']) ? (int)$_REQUEST['m'] : 0;
	$context['id_topic'] = isset($_REQUEST['t']) ? (int)$_REQUEST['t'] : 0;
	if($context['id_msg'] <= 0 || $context['id_topic'] <= 0)
		obExit(false);
	$context['sub_template'] = 'message_icons';
}
/*
 * output the member card
 * todo: better error response
 */
 
function GetMcard()
{
	global $memberContext, $context, $txt;

	$is_xmlreq = $_REQUEST['action'] == 'xmlhttp' ? true : false;
	
	if(!$is_xmlreq)
		redirectexit();		// this isn't supposed to be called normally
		
	if(!isset($_REQUEST['u']))
		AjaxErrorMsg($txt['no_access'], $txt['error_occured']);
		
	$uid = intval($_REQUEST['u']);

	if(allowedTo('profile_view_any') && $uid) {
		loadTemplate('MemberCard');
		loadMemberData($uid, false, 'profile');
		loadMemberContext($uid);
		loadLanguage('Profile');
		loadLanguage('Like');
		$context['member'] = $memberContext[$uid];
	}
	else
		AjaxErrorMsg($txt['no_access'], $txt['error_occured']);
}

function HandleLikeRequest()
{
	global $sourcedir;
	
	$mid = isset($_REQUEST['m']) ? (int)$_REQUEST['m'] : 0;
	
	require_once($sourcedir . '/lib/Subs-LikeSystem.php');
	GiveLike($mid);
}
	
// todo: check permissions!!
function TopicPeek()
{
	global $context;
	global $user_info, $board, $memberContext, $txt;
	
	$is_xmlreq = $_REQUEST['action'] == 'xmlhttp' ? true : false;
	
	if(isset($_REQUEST['t']))
		$tid = intval($_REQUEST['t']);
	else
		$tid = 0;
	
	if(!$is_xmlreq)
		redirectexit();			// this action is XMLHttp - only
		
	if($tid) {
		global $memberContext;
		loadTemplate('TopicPreview');
		loadLanguage('index');
		loadLanguage('Errors');
		$result = smf_db_query( '
			SELECT b.*, t.id_topic, t.id_board, t.id_first_msg, t.id_last_msg, m.id_member AS member_started, m1.id_member AS member_lastpost, m.subject AS first_subject, m.poster_name AS starter_name, m1.subject AS last_subject,
			m1.poster_name AS last_name, m.body as first_body, m1.body AS last_body, 
			' . ($user_info['is_guest'] ? '0' : 'IFNULL(lt.id_msg, IFNULL(lmr.id_msg, -1)) + 1') . ' AS new_from,
			m.poster_time AS first_time, m1.poster_time AS last_time FROM {db_prefix}topics AS t
			LEFT JOIN {db_prefix}log_topics AS lt ON (lt.id_topic = t.id_topic AND lt.id_member = {int:current_member})
			LEFT JOIN {db_prefix}log_mark_read AS lmr ON (lmr.id_board = t.id_board AND lmr.id_member = {int:current_member})
			LEFT JOIN {db_prefix}boards AS b ON b.id_board = t.id_board
			LEFT JOIN {db_prefix}messages AS m ON m.id_msg = t.id_first_msg 
			LEFT JOIN {db_prefix}messages AS m1 ON m1.id_msg = t.id_last_msg WHERE t.id_topic = {int:topic_id} AND {query_see_board} LIMIT 1',
			array('topic_id' => $tid, 'current_member' => $user_info['id'], 'current_board' => $board));
			
		$row = mysql_fetch_assoc($result);
		mysql_free_result($result);

		if(!$row)
			AjaxErrorMsg($txt['topic_gone'], $txt['error_occured']);
		else {
			$m = array();
			$m[0] = $row['member_started'];

			if(($row['id_first_msg'] != $row['id_last_msg']) && $row['member_lastpost'])
				$m[1] = $row['member_lastpost'];

			loadMemberData($m);
			loadMemberContext($m[0]);
			$context['member_started'] = &$memberContext[$row['member_started']];
		
			if(isset($m[1])) {
				loadMemberContext($m[1]);
				$context['member_lastpost'] = &$memberContext[$row['member_lastpost']];
			}
			else
				$context['member_lastpost'] = null;
		
			$context['preview'] = &$row;
		
			// truncate, censor and parse bbc
			$_b = commonAPI::substr($context['preview']['first_body'], 0, 300) . '...';
			censorText($_b);
			$context['preview']['first_body'] = parse_bbc($_b, false);
			$context['preview']['first_time'] = timeformat($row['first_time']);
		
			if($context['member_lastpost']) {
				$_b = commonAPI::substr($context['preview']['last_body'], 0, 600) . '...';
				censorText($_b);
				$context['preview']['last_body'] = parse_bbc($_b, false);
				$context['preview']['last_time'] = timeformat($row['last_time']);
			}
		}
	}
}

function TagsActionDispatcher()
{
	global $sourcedir;
	
	loadLanguage('Tagging');
	require_once($sourcedir . '/Tagging.php');
	if(isset($_REQUEST['addtag']))
		TaggingSystem_Add();
	if(isset($_REQUEST['submittag']))
		TaggingSystem_Submit();
	if(isset($_REQUEST['deletetag']))
		TaggingSystem_Delete();
}

/*
 * return a list of member ids and the number of posts they made in the
 * topic specified in ;t=
 * 
 * todo: error checking, improve this for non-ajax requests
 */
function WhoPosted()
{
	global $context;
	
	$tid = isset($_REQUEST['t']) ? (int)$_REQUEST['t'] : 0;
		
	if($tid) {
		$result = smf_db_query( 'SELECT t.id_board FROM {db_prefix}topics AS t
			INNER JOIN {db_prefix}boards AS b ON b.id_board = t.id_board WHERE t.id_topic = {int:topic} 
			AND {query_see_board}', array('topic' => $tid));
		
		$b = mysql_fetch_row($result);
		mysql_free_result($result);
		
		if($b) {
			loadTemplate('MessageIndex');
			$context['sub_template'] = 'ajaxresponse_whoposted';
			$context['template_layers'] = array();		// ouput "plain", no header etc.
			
			$result = smf_db_query( '
				SELECT mem.real_name, m.id_member, count(m.id_member) AS count FROM {db_prefix}messages AS m
					LEFT JOIN {db_prefix}members AS mem ON mem.id_member = m.id_member WHERE m.id_topic = {int:topic} 
					GROUP BY m.id_member ORDER BY count DESC limit 20', array('topic' => $tid));
			
			while($row = mysql_fetch_assoc($result))
				$context['posters'][] = $row;

			mysql_free_result($result);
		}
	}
}

/*
 * handle AJAX requests for changing topic prefixes in the message and topic view(s)
 */
 // todo: complete this (ui not done yet)
function InlinePrefixActions()
{
	// board and topic ids must be submitted with the request
	$b = isset($_REQUEST['b']) ? (int)$_REQUEST['b'] : 0;
	$t = isset($_REQUEST['t']) ? (int)$_REQUEST['t'] : 0;
	
	if($b && $t) {
		if(isset($_REQUEST['request'])) {			// request list of prefixes in a small popup element
		
		}
	}
}

function AjaxCollapseCategory()
{
	global $sourcedir;
	
	require_once($sourcedir . '/BoardIndex.php');
	
	$_REQUEST['action'] = 'collapse';
	
	if(isset($_REQUEST['expand']))
		$_GET['sa'] = $_REQUEST['sa'] = 'expand';
	else if(isset($_REQUEST['collapse']))
		$_GET['sa'] = $_REQUEST['sa'] = 'collapse';
	
	CollapseCategory(true);
	obExit(false, false, false);
}

function GetSidebarContent()		// unused at the moment
{
	global $context, $user_info, $txt, $modSettings, $sourcedir, $scripturl, $settings;
	loadTemplate('index');
	$context['sub_template'] = 'sidebar_content';
	$context['template_layers'] = array();

	$context['canonical_url'] = $scripturl;

	// Get the user online list.
	require_once($sourcedir . '/lib/Subs-MembersOnline.php');
	$membersOnlineOptions = array(
		'show_hidden' => allowedTo('moderate_forum'),
		'sort' => 'log_time',
		'reverse_sort' => true,
	);
	$context += getMembersOnlineStats($membersOnlineOptions);

	$context['show_buddies'] = !empty($user_info['buddies']);

	// Are we showing all membergroups on the board index?
	if (!empty($settings['show_group_key']))
		$context['membergroups'] = cache_quick_get('membergroup_list', 'lib/Subs-Membergroups.php', 'cache_getMembergroupList', array());

	// Track most online statistics? (Subs-MembersOnline.php)
	if (!empty($modSettings['trackStats']))
		trackStatsUsersOnline($context['num_guests'] + $context['num_spiders'] + $context['num_users_online']);

	// Retrieve the latest posts if the theme settings require it.
	if (isset($settings['number_recent_posts']) && $settings['number_recent_posts'] > 1)
	{
		$latestPostOptions = array(
			'number_posts' => $settings['number_recent_posts'],
		);
		$context['latest_posts'] = cache_quick_get('boardindex-latest_posts:' . md5($user_info['query_wanna_see_board'] . $user_info['language']), 'lib/Subs-Recent.php', 'cache_getLastPosts', array($latestPostOptions));
	}

	$settings['display_recent_bar'] = !empty($settings['number_recent_posts']) ? $settings['number_recent_posts'] : 0;
	$settings['show_member_bar'] &= allowedTo('view_mlist');
	$context['show_stats'] = allowedTo('view_stats') && !empty($modSettings['trackStats']);
	$context['show_member_list'] = allowedTo('view_mlist');
	$context['show_who'] = allowedTo('who_view') && !empty($modSettings['who_enabled']);

	// Load the calendar?
	if (!empty($modSettings['cal_enabled']) && allowedTo('calendar_view'))
	{
		// Retrieve the calendar data (events, birthdays, holidays).
		$eventOptions = array(
			'include_holidays' => $modSettings['cal_showholidays'] > 1,
			'include_birthdays' => $modSettings['cal_showbdays'] > 1,
			'include_events' => $modSettings['cal_showevents'] > 1,
			'num_days_shown' => empty($modSettings['cal_days_for_index']) || $modSettings['cal_days_for_index'] < 1 ? 1 : $modSettings['cal_days_for_index'],
		);
		$context += cache_quick_get('calendar_index_offset_' . ($user_info['time_offset'] + $modSettings['time_offset']), 'lib/Subs-Calendar.php', 'cache_getRecentEvents', array($eventOptions));

		// Whether one or multiple days are shown on the board index.
		$context['calendar_only_today'] = $modSettings['cal_days_for_index'] == 1;

		// This is used to show the "how-do-I-edit" help.
		$context['calendar_can_edit'] = allowedTo('calendar_edit_any');
	}
	else
		$context['show_calendar'] = false;

	$context['page_title'] = sprintf($txt['forum_index'], $context['forum_name']);
}
?>
