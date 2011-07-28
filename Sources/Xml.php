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

function GetMcard()
{
	global $memberContext, $context;
	global $modSettings, $settings, $user_info, $board, $topic, $board_info, $maintenance, $sourcedir;

	//var_dump($user_info);
	$uid = (int)$_REQUEST['u'];
	
	if(allowedTo('profile_view_any')) {
		loadMemberData($uid);
		loadMemberContext($uid);
		$member = $memberContext[$uid];
		echo '<div style="float:left;margin:5px;">',$member['avatar']['image'];
		echo '</div><div style="float:left;margin-left:10px;"><div style="float:right;margin-left:10px;">',
		$member['group_stars'],'<br />',$member['blurb'],'</div><span style="font-size:22px;font-weight:bold;">',$member['name'],'</span><hr />';
		echo $member['group'],' ',$member['post_group'],'<br />';
		echo $member['gender']['name'];
		if(!empty($member['location']))
			echo ' from ',$member['location'];
		
		echo '<br />Member since: ', $member['registered'];
		echo '</div>';
		die;
	}
	echo "ERROR";
	die;
}
?>