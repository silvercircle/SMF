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
		'givelike' => array('function' => 'GiveLike'),
		'mpeek' => array('function' => 'TopicPeek'),
		'tags' => array('function' => 'TagsActionDispatcher')
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

/*
 * output the member card
 */
 
function GetMcard()
{
	global $memberContext, $context, $txt;
	global $settings, $user_info, $sourcedir, $scripturl;

	$is_xmlreq = $_REQUEST['action'] == 'xmlhttp' ? true : false;
	
	if(!$is_xmlreq)
		die;
		
	if(!isset($_REQUEST['u']))
		die;
		
	$uid = intval($_REQUEST['u']);
	
	if(allowedTo('profile_view_any') && $uid) {
		loadMemberData($uid, false, 'profile');
		loadMemberContext($uid);
		loadTemplate('MemberCard');
		loadLanguage('Profile');
		loadLanguage('Like');
		$context['member'] = $memberContext[$uid];
	}
	else {
		loadTemplate('MemberCard');
		loadLanguage('Errors');
		echo "Forbidden";
		die;
	}
}

/*
 * handle a like. _REQUEST['m'] is the message id that is to receive the
 * like
 * 
 * TODO: remove likes from the database when a user is deleted
 * TODO: make it work without AJAX and JavaScript
 * TODO: error responses
 * TODO: disallow like for posts by banned users
 * TODO: use language packs to make it fully translatable
 */
 
function GiveLike()
{
	global $context;
	global $settings, $user_info, $sourcedir, $smcFunc;
	$total = array();
	
	if(isset($_REQUEST['m']))
		$mid = intval($_REQUEST['m']);
	else
		$mid = 0;
	
	if($mid > 0) {
		$uid = $user_info['id'];
		$remove_it = isset($_REQUEST['remove']) ? true : false;
		$is_xmlreq = $_REQUEST['action'] == 'xmlhttp' ? true : false;
		
		require_once($sourcedir . '/LikeSystem.php');

		if($user_info['is_guest'])
			LikesError("Permission denied", $is_xmlreq);

		/* check for dupes */
		$request = $smcFunc['db_query']('', '
			SELECT COUNT(id_msg) as count, id_user 
				FROM {db_prefix}likes AS l WHERE l.id_msg = {int:id_message} AND l.id_user = {int:id_user}',
				array('id_message' => $mid, 'id_user' => $uid));
				
		$count = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);
		
		$c = intval($count[0]);
		$like_owner = intval($count[1]);
		/*
		 * this is a debugging feature and allows the admin to repair
		 * the likes for a post.
		 * it may go away at a later time.
		 */
		if(isset($_REQUEST['repair'])) {
			if(!$user_info['is_admin'])
				die;
			$total = LikesUpdate($mid);
			$output = '';
			LikesGenerateOutput($total['status'], $output, $total['count'], $mid, $c > 0 ? true : false);
			if($is_xmlreq)
				echo $output;
			else
				LikesError("The like status cache for post ".$mid."was rebuilt successfully");
			die;
		}
		
		if($c > 0 && !$remove_it)		// duplicate like (but not when removing it)
			LikesError('Verification failed (duplicate)', $is_xmlreq);
			
		/*
		 * you cannot like your own post - the front end handles this with a seperate check and
		 * doesn't show the like button for own messages, but this check is still necessary
		 */		
		
		$request = $smcFunc['db_query']('', '
			SELECT id_member, id_board FROM {db_prefix}messages AS m WHERE m.id_msg = '.$mid);

		$m = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);
		$like_receiver = intval($m[0]);
		
		if($like_receiver == $uid)
			LikesError('Cannot like own posts.', $is_xmlreq);
		
		if(!allowedTo('like_give', $m[1]))			// no permission to give likes in this board
			LikesError('You cannot use this feature.', $is_xmlreq);

		if($remove_it && $c > 0) {   	// TODO: remove a like, $c must indicate a duplicate (existing) like
										// and you must be the owner of the like or admin
			LikesError('Removing is ok', $is_xmlreq);
		}
		else {
			/* store the like */
			global $memberContext;
			
			if($like_receiver) {
				loadMemberData($like_receiver);
				loadMemberContext($like_receiver);
				if(!$memberContext[$like_receiver]['is_banned']) {
					$smcFunc['db_query']('', '
						INSERT INTO {db_prefix}likes values({int:id_message}, {int:id_user}, {int:id_receiver}, {int:updated})',
						array('id_message' => $mid, 'id_user' => $uid, 'id_receiver' => $like_receiver, 'updated' => time()));
					
					$smcFunc['db_query']('', 'UPDATE {db_prefix}members SET likes_received = likes_received + 1 WHERE id_member = {int:id_member}',
						array('id_member' => $like_receiver));
					
					$smcFunc['db_query']('', 'UPDATE {db_prefix}members SET likes_given = likes_given + 1 WHERE id_member = '.$uid);
				}
			}
			else
				LikesError('Cannot like this post', $is_xmlreq);
				
		}
		$total = LikesUpdate($mid);
		$output = '';
		LikesGenerateOutput($total['status'], $output, $total['count'], $mid, true);
		echo $output;
	}
	die;
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
			m.poster_time AS first_time, m1.poster_time AS last_time FROM {db_prefix}topics AS t
			LEFT JOIN {db_prefix}boards AS b ON b.id_board = t.id_board
			LEFT JOIN {db_prefix}messages AS m ON m.id_msg = t.id_first_msg 
			LEFT JOIN {db_prefix}messages AS m1 ON m1.id_msg = t.id_last_msg WHERE t.id_topic = {int:topic_id} AND {query_see_board}',
			array('topic_id' => $tid));
			
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
				$context['preview']['last_body'] = $smcFunc['substr']($context['preview']['last_body'], 0, 300) . '...';
				$context['preview']['last_time'] = timeformat($row['last_time']);
			}
		}
		$smcFunc['db_free_result']($result);
	}
}

function TagsActionDispatcher()
{
	global $sourcedir;
	
	require_once($sourcedir . '/Tagging.php');
	if(isset($_REQUEST['addtag']))
		TaggingSystem_Add();
	if(isset($_REQUEST['submittag']))
		TaggingSystem_Submit();
	if(isset($_REQUEST['deletetag']))
		TaggingSystem_Delete();
}
?>
