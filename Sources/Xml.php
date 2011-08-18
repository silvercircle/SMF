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
		'whoposted' => array('function' => 'WhoPosted')
	);
	if (!isset($_REQUEST['sa'], $sub_actions[$_REQUEST['sa']]))
		fatal_lang_error('no_access', false);

	$sub_actions[$_REQUEST['sa']]['function']();
}

// Get a list of boards and categories used for the jumpto dropdown.
function GetJumpTo()
{
	global $user_info, $context, $smcFunc, $sourcedir;

	// Find the boards/cateogories they can see.
	require_once($sourcedir . '/Subs-MessageIndex.php');
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

	require_once($sourcedir . '/Subs-Editor.php');
	$context['icons'] = getMessageIcons($board);

	$context['sub_template'] = 'message_icons';
}

function AjaxErrorMsg($msg)
{
	global $context;
	
	$xmlreq = (isset($_REQUEST['action']) && $_REQUEST['action'] == 'xmlhttp') ? true : false;

	if($xmlreq) {
		$context['ajax_error_message'] = $msg;
		$context['error_container_id'] = 'ajax_error_container';
		$context['sub_template'] = 'ajax_error';
		$context['template_layers'] = array();		// ouput "plain", no header etc.
		obExit(true, true, false);
	}
	else
		fatal_error($msg, '');
}

/*
 * output the member card
 * todo: better error response
 */
 
function GetMcard()
{
	global $memberContext, $context, $txt;
	global $settings, $user_info, $sourcedir, $scripturl;

	$is_xmlreq = $_REQUEST['action'] == 'xmlhttp' ? true : false;
	
	if(!$is_xmlreq)
		redirectexit();
		
	if(!isset($_REQUEST['u']))
		die;
		
	$uid = intval($_REQUEST['u']);
	
	loadTemplate('MemberCard');
	if(allowedTo('profile_view_any') && $uid) {
		loadMemberData($uid, false, 'profile');
		loadMemberContext($uid);
		loadLanguage('Profile');
		loadLanguage('Like');
		$context['member'] = $memberContext[$uid];
	}
	else
		$context['member'] = null;
}

function HandleLikeRequest()
{
	global $sourcedir;
	
	$mid = isset($_REQUEST['m']) ? (int)$_REQUEST['m'] : 0;
	
	require_once($sourcedir . '/Subs-LikeSystem.php');
	GiveLike($mid);
}
	
// todo: check permissions!!
function TopicPeek()
{
	global $context;
	global $settings, $user_info, $sourcedir, $smcFunc, $board, $memberContext, $scripturl;
	
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
		$result = $smcFunc['db_query']('', '
			SELECT b.*, t.id_topic, t.id_board, t.id_first_msg, t.id_last_msg, m.id_member AS member_started, m1.id_member AS member_lastpost, m.subject AS first_subject, m.poster_name AS starter_name, m1.subject AS last_subject,
			m1.poster_name AS last_name, m.body as first_body, m1.body AS last_body, 
			' . ($user_info['is_guest'] ? '0' : 'IFNULL(lt.id_msg, IFNULL(lmr.id_msg, -1)) + 1') . ' AS new_from,
			m.poster_time AS first_time, m1.poster_time AS last_time FROM {db_prefix}topics AS t
			LEFT JOIN {db_prefix}log_topics AS lt ON (lt.id_topic = t.id_topic AND lt.id_member = {int:current_member})
			LEFT JOIN {db_prefix}log_mark_read AS lmr ON (lmr.id_board = t.id_board AND lmr.id_member = {int:current_member})
			LEFT JOIN {db_prefix}boards AS b ON b.id_board = t.id_board
			LEFT JOIN {db_prefix}messages AS m ON m.id_msg = t.id_first_msg 
			LEFT JOIN {db_prefix}messages AS m1 ON m1.id_msg = t.id_last_msg WHERE t.id_topic = {int:topic_id} AND {query_see_board}',
			array('topic_id' => $tid, 'current_member' => $user_info['id'], 'current_board' => $board));
			
		$row = $smcFunc['db_fetch_assoc']($result);
		
		if(!$row)
			$context['preview'] = null;			// no access or other error
		else {
			$m = array();
			$m[0] = $row['member_started'];

			if(($row['id_first_msg'] != $row['id_last_msg']) && $row['member_lastpost'])
				$m[1] = $row['member_lastpost'];

			loadMemberData($m);
			loadMemberContext($m[0]);
			$context['member_started'] = $memberContext[$row['member_started']];
		
			if(isset($m[1])) {
				loadMemberContext($m[1]);
				$context['member_lastpost'] = $memberContext[$row['member_lastpost']];
			}
			else {
				$context['member_lastpost'] = null;
			}
		
			$context['preview'] = $row;
		
			censorText($context['preview']['first_subject']);

			$context['preview']['first_body'] = parse_bbc($context['preview']['first_body'], false);
			$context['preview']['first_body'] = $smcFunc['substr']($context['preview']['first_body'], 0, 300) . '...';
			$context['preview']['first_time'] = timeformat($row['first_time']);
		
			if($context['member_lastpost']) {
				censorText($context['preview']['last_subject']);
				$context['preview']['last_body'] = parse_bbc($context['preview']['last_body'], false);
				$context['preview']['last_body'] = substr($context['preview']['last_body'], 0, 600) . '...';
				$context['preview']['last_time'] = timeformat($row['last_time']);
			}
		}
		$smcFunc['db_free_result']($result);
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
	global $smcFunc, $context, $txt;
	
	$is_xmlreq = $_REQUEST['action'] == 'xmlhttp' ? true : false;
	
	$tid = isset($_REQUEST['t']) ? (int)$_REQUEST['t'] : 0;
		
	if($tid) {
		$result = $smcFunc['db_query']('', 'SELECT t.id_board FROM {db_prefix}topics AS t
			INNER JOIN {db_prefix}boards AS b ON b.id_board = t.id_board WHERE t.id_topic = {int:topic} 
			AND {query_see_board}', array('topic' => $tid));
		
		$b = $smcFunc['db_fetch_row']($result);
		$smcFunc['db_free_result']($result);
		
		if($b) {
			loadTemplate('MessageIndex');
			$context['sub_template'] = 'ajaxresponse_whoposted';
			$context['template_layers'] = array();		// ouput "plain", no header etc.
			
			$result = $smcFunc['db_query']('', '
				SELECT mem.real_name, m.id_member, count(m.id_member) AS count FROM {db_prefix}messages AS m
					LEFT JOIN {db_prefix}members AS mem ON mem.id_member = m.id_member WHERE m.id_topic = {int:topic} 
					GROUP BY m.id_member ORDER BY count DESC limit 20', array('topic' => $tid));
			
			while($row = $smcFunc['db_fetch_assoc']($result))
				$context['posters'][] = $row;

			$smcFunc['db_free_result']($result);
		}
	}
}
?>
