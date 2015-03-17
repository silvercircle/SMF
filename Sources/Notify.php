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

/*	This file contains just the functions that turn on and off notifications to
	topics or boards. The following two functions are included:

	void Notify()
		- is called to turn off/on notification for a particular topic.
		- must be called with a topic specified in the URL.
		- uses the Notify template (main sub template.) when called with no sa.
		- the sub action can be 'on', 'off', or nothing for what to do.
		- requires the mark_any_notify permission.
		- upon successful completion of action will direct user back to topic.
		- is accessed via ?action=notify.

	void BoardNotify()
		- is called to turn off/on notification for a particular board.
		- must be called with a board specified in the URL.
		- uses the Notify template. (notify_board sub template.)
		- only uses the template if no sub action is used. (on/off)
		- requires the mark_notify permission.
		- redirects the user back to the board after it is done.
		- is accessed via ?action=notifyboard.
*/

// Turn on/off notifications...
function Notify()
{
	global $scripturl, $txt, $topic, $user_info, $context, $smcFunc;

	// Make sure they aren't a guest or something - guests can't really receive notifications!
	is_not_guest();
	isAllowedTo('mark_any_notify');

	// Make sure the topic has been specified.
	if (empty($topic))
		fatal_lang_error('not_a_topic', false);

	// What do we do?  Better ask if they didn't say..
	if (empty($_GET['sa']))
	{
		Eos_Smarty::loadTemplate('generic_skeleton');
		EoS_Smarty::getConfigInstance()->registerHookTemplate('generic_content_area', 'notify/notify_topic');

		// Find out if they have notification set for this topic already.
		$request = smf_db_query( '
			SELECT id_member
			FROM {db_prefix}log_notify
			WHERE id_member = {int:current_member}
				AND id_topic = {int:current_topic}
			LIMIT 1',
			array(
				'current_member' => $user_info['id'],
				'current_topic' => $topic,
			)
		);
		$context['notification_set'] = mysql_num_rows($request) != 0;
		mysql_free_result($request);

		// Set the template variables...
		$context['topic_href'] = $scripturl . '?topic=' . $topic . '.' . $_REQUEST['start'];
		$context['start'] = $_REQUEST['start'];
		$context['page_title'] = $txt['notification'];

		return;
	}
	elseif ($_GET['sa'] == 'on')
	{
		checkSession('get');

		// Attempt to turn notifications on.
		smf_db_insert('ignore',
			'{db_prefix}log_notify',
			array('id_member' => 'int', 'id_topic' => 'int'),
			array($user_info['id'], $topic),
			array('id_member', 'id_topic')
		);
	}
	else
	{
		checkSession('get');

		// Just turn notifications off.
		smf_db_query( '
			DELETE FROM {db_prefix}log_notify
			WHERE id_member = {int:current_member}
				AND id_topic = {int:current_topic}',
			array(
				'current_member' => $user_info['id'],
				'current_topic' => $topic,
			)
		);
	}

	// Send them back to the topic.
	redirectexit('topic=' . $topic . '.' . $_REQUEST['start']);
}

function BoardNotify()
{
	global $scripturl, $txt, $board, $user_info, $context, $smcFunc;

	// Permissions are an important part of anything ;).
	is_not_guest();
	isAllowedTo('mark_notify');

	// You have to specify a board to turn notifications on!
	if (empty($board))
		fatal_lang_error('no_board', false);

	// No subaction: find out what to do.
	if (empty($_GET['sa']))
	{
		// We're gonna need the notify template...
		Eos_Smarty::loadTemplate('generic_skeleton');
		EoS_Smarty::getConfigInstance()->registerHookTemplate('generic_content_area', 'notify/notify_board');

		// Find out if they have notification set for this topic already.
		$request = smf_db_query( '
			SELECT id_member
			FROM {db_prefix}log_notify
			WHERE id_member = {int:current_member}
				AND id_board = {int:current_board}
			LIMIT 1',
			array(
				'current_board' => $board,
				'current_member' => $user_info['id'],
			)
		);
		$context['notification_set'] = mysql_num_rows($request) != 0;
		mysql_free_result($request);

		// Set the template variables...
		$context['board_href'] = $scripturl . '?board=' . $board . '.' . $_REQUEST['start'];
		$context['start'] = $_REQUEST['start'];
		$context['page_title'] = $txt['notification'];

		return;
	}
	// Turn the board level notification on....
	elseif ($_GET['sa'] == 'on')
	{
		checkSession('get');

		// Turn notification on.  (note this just blows smoke if it's already on.)
		smf_db_insert('ignore',
			'{db_prefix}log_notify',
			array('id_member' => 'int', 'id_board' => 'int'),
			array($user_info['id'], $board),
			array('id_member', 'id_board')
		);
	}
	// ...or off?
	else
	{
		checkSession('get');

		// Turn notification off for this board.
		smf_db_query( '
			DELETE FROM {db_prefix}log_notify
			WHERE id_member = {int:current_member}
				AND id_board = {int:current_board}',
			array(
				'current_board' => $board,
				'current_member' => $user_info['id'],
			)
		);
	}

	// Back to the board!
	redirectexit('board=' . $board . '.' . $_REQUEST['start']);
}
