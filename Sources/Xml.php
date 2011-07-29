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
		'mpeek' => array('function' => 'TopicPeek')
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
 * todo: make it use the template system
 */
 
function GetMcard()
{
	global $memberContext, $context, $txt;
	global $settings, $user_info, $sourcedir, $scripturl;

	$is_xmlreq = $_REQUEST['action'] == 'xmlhttp' ? true : false;
	
	if(!$is_xmlreq)
		die;
		
	$uid = (int)$_REQUEST['u'];
	
	if(allowedTo('profile_view_any')) {
		loadMemberData($uid);
		loadMemberContext($uid);
		$member = $memberContext[$uid];
		if(!empty($member['avatar']['image']))
			echo '<div style="float:left;margin:5px 10px 0 0;">',$member['avatar']['image'],'</div>';
		else
			echo '<div style="float:left;margin:5px 10px 0 0;"><img src="',$settings['images_url'], '/unknown.png" alt="avatar" /></div>';
		echo '<div style="float:left;"><div style="float:right;margin-left:10px;">',
		$member['group_stars'],'<br /><strong>',$member['blurb'],'</strong></div><span style="font-size:22px;font-weight:bold;">',$member['name'],'</span><hr />';
		echo $member['group'],' ',$member['post_group'],'<br />';
		echo $member['gender']['name'];
		if(!empty($member['location']))
			echo ', from ',$member['location'];
		
		echo '<br />Member since: ', $member['registered'];
		echo '</div>';
		echo '<div style="position:absolute;bottom:-2px;right:5px;"><a href="',$scripturl,'?action=profile;u=',$uid,'">View full profile</a></div><div style="clear:both;"></div>';
		die;
	}
	loadLanguage('Login');
	echo '<div style="text-align:center;font-size:15px;margin:10px 0;">'.$txt['only_members_can_access'].'</div>';
	die;
}

/*
 * handle a like. _REQUEST['m'] is the message id that is to receive the
 * like, 'b' is the board number (needed to check permissions)
 * 
 * todo: remove likes from the database when a user is deleted
 * todo: make it work without AJAX and JavaScript
 * todo: error responses
 * todo: disallow like for posts by banned users
 * todo: use language packs to make it fully translatable
 */
 
function GiveLike()
{
	global $context;
	global $settings, $user_info, $sourcedir, $smcFunc;
	$total = array();
	
	$mid = intval($_REQUEST['m']);
	$bid = intval($_REQUEST['b']);
	
	if($mid > 0) {
		$uid = $user_info['id'];
		$remove_it = intval($_REQUEST['remove']) == 1 ? true : false;
		$is_xmlreq = $_REQUEST['action'] == 'xmlhttp' ? true : false;
		
		require_once($sourcedir . '/LikeSystem.php');

		$allowed = allowedTo('like_give', $bid);
		if(!$allowed || $user_info['is_guest'])
			LikesError("Permission denied", $is_xmlreq);

		/* check for dupes */
		$request = $smcFunc['db_query']('', '
			SELECT COUNT(id_msg) as count, id_user 
				FROM {db_prefix}likes AS l WHERE l.id_msg = '.$mid.' AND l.id_user = '.$uid);
		$count = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);
		
		$c = intval($count[0]);
		$like_owner = intval($count[1]);
		/*
		 * this is a debugging feature and allows the admin to repair
		 * the likes for a post.
		 * it may go away at a later time.
		 */
		if(intval($_REQUEST['repair']) == 1) {
			if(!$user_info['is_admin'])
				die;
			$total = LikesUpdate($mid);
			$output = '';
			LikesGenerateOutput($total['status'], $output, $total['count'], $mid, $c > 0 ? true : false);
			if($is_xmlreq)
				echo $output;
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
		if(intval($m[0]) == $uid || $m[1] != $bid)
			LikesError('Verification failed', $is_xmlreq);
		
		if($remove_it && $c > 0) {
			LikesError('Removing is ok', $is_xmlreq);
		}
		else {
			$smcFunc['db_query']('', '
				INSERT INTO {db_prefix}likes values('.$mid.', ' . $uid . ', ' . time() . ')');
		}
		
		$total = LikesUpdate($mid);
		$output = '';
		LikesGenerateOutput($total['status'], $output, $total['count'], $mid, true);
		echo $output;
	}
	die;
}

function TopicPeek()
{
	global $context;
	global $settings, $user_info, $sourcedir, $smcFunc, $board;
	
	$is_xmlreq = $_REQUEST['action'] == 'xmlhttp' ? true : false;
	$mid = intval($_REQUEST['m']);
	
	echo "hahaha";
	if($mid) {
	
		/*
		censorText($message['body']);
		censorText($message['subject']);

		$message['body'] = parse_bbc($message['body'], $message['smileys_enabled'], $message['id_msg']);
		*/
	}
	die;
}
?>