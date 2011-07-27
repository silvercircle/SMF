<?php
/******************************************************************************
* InstantMessage.php                                                          *
*******************************************************************************
* SMF: Simple Machines Forum                                                  *
* Open-Source Project Inspired by Zef Hemel (zef@zefhemel.com)                *
* =========================================================================== *
* Software Version:           SMF 1.0.2                                       *
* Software by:                Simple Machines (http://www.simplemachines.org) *
* Copyright 2001-2005 by:     Lewis Media (http://www.lewismedia.com)         *
* Support, News, Updates at:  http://www.simplemachines.org                   *
*******************************************************************************
* This program is free software; you may redistribute it and/or modify it     *
* under the terms of the provided license as published by Lewis Media.        *
*                                                                             *
* This program is distributed in the hope that it is and will be useful,      *
* but WITHOUT ANY WARRANTIES; without even any implied warranty of            *
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                        *
*                                                                             *
* See the "license.txt" file for details of the Simple Machines license.      *
* The latest version can always be found at http://www.simplemachines.org.    *
******************************************************************************/
if (!defined('SMF'))
	die('Hacking attempt...');

/*	This file is mainly meant for viewing personal messages.  It also sends,
	deletes, and marks personal messages.  For compatibility reasons, they are
	often called "instant messages".
*/

// This helps organize things...
function MessageMain()
{
	global $txt, $scripturl, $sourcedir, $context;

	// No guests!
	is_not_guest();
	// You're not supposed to be here at all, if you can't even read PMs.
	isAllowedTo('pm_read');

	require_once($sourcedir . '/Subs-Post.php');

	loadTemplate('InstantMessage');
	loadLanguage('InstantMessage');

	// Build the linktree for all the actions...
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=pm',
		'name' => $txt[144]
	);

	$subActions = array(
		'send' => 'MessagePost',
		'send2' => 'MessagePost2',
		'outbox' => 'MessageFolder',
		'prune' => 'MessagePrune',
		'removeall' => 'MessageKillAllQuery',
		'removeall2' => 'MessageKillAll',
		'removemore' => 'MessageRemoveMore',
	);

	if (!isset($_REQUEST['sa']) || !isset($subActions[$_REQUEST['sa']]))
		MessageFolder();
	else
		$subActions[$_REQUEST['sa']]();
}

// A folder, ie. outbox/inbox.
function MessageFolder()
{
	global $txt, $scripturl, $db_prefix, $ID_MEMBER, $modSettings, $context;
	global $messages_request, $user_info, $recipients, $options;

	// Make sure the starting location is valid.
	if (isset($_GET['start']) && $_GET['start'] != 'new')
		$_GET['start'] = (int) $_GET['start'];
	elseif (!isset($_GET['start']) && !empty($options['view_newest_pm_first']))
		$_GET['start'] = 0;
	else
		$_GET['start'] = 'new';

	// Set up some basic theme stuff.
	$context['allow_hide_email'] = !empty($modSettings['allow_hideEmail']);
	$context['folder'] = !isset($_REQUEST['f']) || $_REQUEST['f'] != 'outbox' ? 'inbox' : 'outbox';
	$context['from_or_to'] = $context['folder'] != 'outbox' ? 'from' : 'to';
	$context['get_pmessage'] = 'prepareMessageContext';

	// Sorting the folder.
	$sort_methods = array(
		'date' => 'pm.ID_PM',
		'name' => "IFNULL(mem.realName, '')",
		'subject' => 'pm.subject',
	);

	// They didn't pick one, use the forum default.
	if (!isset($_GET['sort']) || !isset($sort_methods[$_GET['sort']]))
	{
		$context['sort_by'] = 'date';
		$_GET['sort'] = 'pm.ID_PM';
		$descending = false;
	}
	// Otherwise use the defaults: ascending, by date.
	else
	{
		$context['sort_by'] = $_GET['sort'];
		$_GET['sort'] = $sort_methods[$_GET['sort']];
		$descending = isset($_GET['desc']);
	}

	if (!empty($options['view_newest_pm_first']))
		$descending = !$descending;

	$context['sort_direction'] = $descending ? 'down' : 'up';

	// Why would you want access to your outbox if you're not allowed to send anything?
	if ($context['folder'] == 'outbox')
		isAllowedTo('pm_send');

	// Set the text to resemble the current folder.
	$pmbox = $context['folder'] != 'outbox' ? $txt[316] : $txt[320];
	$txt[412] = str_replace('PMBOX', $pmbox, $txt[412]);

	// Now, build the link tree!
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=pm;f=' . $context['folder'],
		'name' => $pmbox
	);

	// Mark all messages as read if in the inbox.
	if ($context['folder'] != 'outbox' && !empty($user_info['unread_messages']))
	{
		markMessages();
		$user_info['unread_messages'] = 0;
	}

	// Figure out how many messages there are.
	if ($context['folder'] == 'outbox')
	{
		$request = db_query("
			SELECT COUNT(ID_PM)
			FROM {$db_prefix}instant_messages
			WHERE ID_MEMBER_FROM = $ID_MEMBER
				AND deletedBySender = 0", __FILE__, __LINE__);
		list ($max_messages) = mysql_fetch_row($request);
		mysql_free_result($request);
	}
	else
		$max_messages = $user_info['messages'];

	// Only show the button if there are messages to delete.
	$context['show_delete'] = $max_messages > 0;

	// Start on the last page.
	if (!is_numeric($_GET['start']) || $_GET['start'] >= $max_messages)
		$_GET['start'] = ($max_messages - 1) - (($max_messages - 1) % $modSettings['defaultMaxMessages']);
	elseif ($_GET['start'] < 0)
		$_GET['start'] = 0;

	// Set up the page index.
	$context['page_index'] = constructPageIndex($scripturl . '?action=pm;f=' . $context['folder'] . ';sort=' . $context['sort_by'] . (isset($_GET['desc']) ? ';desc' : ''), $_GET['start'], $max_messages, $modSettings['defaultMaxMessages']);
	$context['start'] = $_GET['start'];

	// Load the messages up...
	$request = db_query("
		SELECT pm.ID_PM, pm.ID_MEMBER_FROM
		FROM ({$db_prefix}instant_messages AS pm" . ($context['folder'] == 'outbox' ? ')' . ($context['sort_by'] == 'name' ? "
			LEFT JOIN {$db_prefix}im_recipients AS pmr ON (pmr.ID_PM = pm.ID_PM)" : '') : ", {$db_prefix}im_recipients AS pmr)") . ($context['sort_by'] == 'name' ? ("
			LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = " . ($context['folder'] == 'outbox' ? 'pmr.ID_MEMBER' : 'pm.ID_MEMBER_FROM') . ")") : '') . "
		WHERE " . ($context['folder'] == 'outbox' ? "pm.ID_MEMBER_FROM = $ID_MEMBER
			AND pm.deletedBySender = 0" : "pm.ID_PM = pmr.ID_PM
			AND pmr.ID_MEMBER = $ID_MEMBER
			AND pmr.deleted = 0") . "
		ORDER BY " . ($_GET['sort'] == 'pm.ID_PM' && $context['folder'] != 'outbox' ? 'pmr.ID_PM' : $_GET['sort']) . ($descending ? ' DESC' : ' ASC') . "
		LIMIT $_GET[start], $modSettings[defaultMaxMessages]", __FILE__, __LINE__);

	// Load the ID_PMs and ID_MEMBERs and initialize recipients.
	$pms = array();
	$posters = $context['folder'] == 'outbox' ? array($ID_MEMBER) : array();
	$recipients = array();
	while ($row = mysql_fetch_assoc($request))
	{
		if (!isset($recipients[$row['ID_PM']]))
		{
			$pms[] = $row['ID_PM'];
			if (!empty($row['ID_MEMBER_FROM']) && $context['folder'] != 'outbox')
				$posters[] = $row['ID_MEMBER_FROM'];
			$recipients[$row['ID_PM']] = array(
				'to' => array(),
				'bcc' => array()
			);
		}
	}
	mysql_free_result($request);

	if (!empty($pms))
	{
		// Get recipients (don't include bcc-recipients for your inbox, you're not supposed to know :P).
		$request = db_query("
			SELECT pmr.ID_PM, mem_to.ID_MEMBER AS ID_MEMBER_TO, mem_to.realName AS toName, pmr.bcc
			FROM {$db_prefix}im_recipients AS pmr
				LEFT JOIN {$db_prefix}members AS mem_to ON (mem_to.ID_MEMBER = pmr.ID_MEMBER)
			WHERE pmr.ID_PM IN (" . implode(', ', $pms) . ")" . ($context['folder'] == 'outbox' ? '' : "
				AND pmr.bcc = 0"), __FILE__, __LINE__);
		while ($row = mysql_fetch_assoc($request))
			$recipients[$row['ID_PM']][empty($row['bcc']) ? 'to' : 'bcc'][] = empty($row['ID_MEMBER_TO']) ? $txt[28] : '<a href="' . $scripturl . '?action=profile;u=' . $row['ID_MEMBER_TO'] . '">' . $row['toName'] . '</a>';

		// Load any users....
		$posters = array_unique($posters);
		if (!empty($posters))
			loadMemberData($posters);

		// Execute the query!
		$messages_request = db_query("
			SELECT pm.ID_PM, pm.subject, pm.ID_MEMBER_FROM, pm.body, pm.msgtime, pm.fromName
			FROM {$db_prefix}instant_messages AS pm" . ($context['folder'] == 'outbox' ? "
				LEFT JOIN {$db_prefix}im_recipients AS pmr ON (pmr.ID_PM = pm.ID_PM)" : '') . ($context['sort_by'] == 'name' ? "
				LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = " . ($context['folder'] == 'outbox' ? 'pmr.ID_MEMBER' : 'pm.ID_MEMBER_FROM') . ")" : '') . "
			WHERE pm.ID_PM IN (" . implode(',', $pms) . ")" . ($context['folder'] == 'outbox' ? "
			GROUP BY pm.ID_PM" : '') . "
			ORDER BY $_GET[sort] " . ($descending ? ' DESC' : ' ASC') . "
			LIMIT " . count($pms), __FILE__, __LINE__);
	}
	else
		$messages_request = false;

	$context['can_send_pm'] = allowedTo('pm_send');
	$context['sub_template'] = 'folder';
	$context['page_title'] = $txt[143];
}

// Get a personal message for the theme.  (used to save memory.)
function prepareMessageContext($reset = false)
{
	global $txt, $scripturl, $modSettings, $context, $messages_request, $themeUser, $recipients;

	// Count the current message number....
	static $counter = null;
	if ($counter == null || $reset)
		$counter = $context['start'];

	// Bail if it's false, ie. no messages.
	if ($messages_request == false)
		return false;

	// Reset the data?
	if ($reset == true)
		return @mysql_data_seek($messages_request, 0);

	// Get the next one... bail if anything goes wrong.
	$message = mysql_fetch_assoc($messages_request);
	if (!$message)
		return(false);

	// Use '(no subject)' if none was specified.
	$message['subject'] = $message['subject'] == '' ? $txt[24] : $message['subject'];

	// Load the message's information - if it's not there, load the guest information.
	if (!loadMemberContext($message['ID_MEMBER_FROM']))
	{
		$themeUser[$message['ID_MEMBER_FROM']]['name'] = $message['fromName'];
		$themeUser[$message['ID_MEMBER_FROM']]['id'] = 0;
		$themeUser[$message['ID_MEMBER_FROM']]['group'] = $txt[28];
		$themeUser[$message['ID_MEMBER_FROM']]['link'] = $message['fromName'];
		$themeUser[$message['ID_MEMBER_FROM']]['email'] = '';
		$themeUser[$message['ID_MEMBER_FROM']]['is_guest'] = true;
	}

	// Censor all the important text...
	censorText($message['body']);
	censorText($message['subject']);

	// Run UBBC interpreter on the message.
	$message['body'] = doUBBC($message['body']);

	// Send the array.
	$output = array(
		'alternate' => $counter % 2,
		'id' => $message['ID_PM'],
		'member' => &$themeUser[$message['ID_MEMBER_FROM']],
		'subject' => $message['subject'],
		'time' => timeformat($message['msgtime']),
		'timestamp' => $message['msgtime'],
		'counter' => $counter,
		'body' => $message['body'],
		'recipients' => &$recipients[$message['ID_PM']]
	);

	$counter++;

	return $output;
}

// Send a new message?
function MessagePost()
{
	global $txt, $sourcedir;
	global $db_prefix, $ID_MEMBER, $scripturl, $modSettings, $context, $options;

	isAllowedTo('pm_send');

	// Just in case it was loaded from somewhere else.
	loadTemplate('InstantMessage');
	loadLanguage('InstantMessage');

	$context['show_spellchecking'] = $modSettings['enableSpellChecking'] && function_exists('pspell_new');

	// Set the title...
	$context['sub_template'] = 'send';
	$context['page_title'] = $txt[148];

	$context['folder'] = !isset($_REQUEST['f']) || $_REQUEST['f'] != 'outbox' ? 'inbox' : 'outbox';
	$context['reply'] = isset($_REQUEST['reply']) || isset($_REQUEST['quote']);

	// Quoting/Replying to a message?
	if (!empty($_REQUEST['pmsg']))
	{
		$_REQUEST['pmsg'] = (int) $_REQUEST['pmsg'];

		// Get the quoted message (and make sure you're allowed to see this quote!).
		$request = db_query("
			SELECT
				pm.ID_PM, pm.body, pm.subject, pm.msgtime, mem.memberName,
				IFNULL(mem.ID_MEMBER, 0) AS ID_MEMBER, IFNULL(mem.realName, pm.fromName) AS realName
			FROM ({$db_prefix}instant_messages AS pm" . ($context['folder'] == 'outbox' ? '' : ", {$db_prefix}im_recipients AS pmr") . ")
				LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = pm.ID_MEMBER_FROM)
			WHERE pm.ID_PM = $_REQUEST[pmsg]" . ($context['folder'] == 'outbox' ? "
				AND pm.ID_MEMBER_FROM = $ID_MEMBER" : "
				AND pmr.ID_PM = $_REQUEST[pmsg]
				AND pmr.ID_MEMBER = $ID_MEMBER") . "
			LIMIT 1", __FILE__, __LINE__);
		if (mysql_num_rows($request) == 0)
			fatal_lang_error('pm_not_yours', false);
		$row_quoted = mysql_fetch_assoc($request);
		mysql_free_result($request);

		// Censor the message.
		censorText($row_quoted['subject']);
		censorText($row_quoted['body']);

		// Add 'Re: ' to it....
		$form_subject = $row_quoted['subject'];
		if ($context['reply'] && trim($txt['response_prefix']) != '' && strpos($form_subject, trim($txt['response_prefix'])) !== 0)
			$form_subject = $txt['response_prefix'] . $form_subject;

		if (isset($_REQUEST['quote']))
		{
			// Remove any nested quotes and <br />...
			$form_message = preg_replace('~<br( /)?>~i', "\n", $row_quoted['body']);
			if (!empty($modSettings['removeNestedQuotes']))
				$form_message = preg_replace(array('~\n?\[quote.*?\].+?\[/quote\]\n?~is', '~^\n~', '~\[/quote\]~'), '', $form_message);
			$form_message = '[quote author=&quot;' . $row_quoted['realName'] . "&quot;]\n" . $form_message . "\n[/quote]";
		}
		else
			$form_message = '';

		// Do the BBC thang on the message.
		$row_quoted['body'] = doUBBC($row_quoted['body']);

		// Set up the quoted message array.
		$context['quoted_message'] = array(
			'id' => &$row_quoted['ID_PM'],
			'member' => array(
				'name' => $row_quoted['realName'],
				'username' => $row_quoted['memberName'],
				'id' => $row_quoted['ID_MEMBER'],
				'href' => !empty($row_quoted['ID_MEMBER']) ? $scripturl . '?action=profile;u=' . $row_quoted['ID_MEMBER'] : '',
				'link' => !empty($row_quoted['ID_MEMBER']) ? '<a href="' . $scripturl . '?action=profile;u=' . $row_quoted['ID_MEMBER'] . '">' . $row_quoted['realName'] . '</a>' : $row_quoted['realName'],
			),
			'subject' => &$row_quoted['subject'],
			'time' => timeformat($row_quoted['msgtime']),
			'timestamp' => $row_quoted['msgtime'],
			'body' => &$row_quoted['body']
		);
	}
	else
	{
		$context['quoted_message'] = false;
		$form_subject = '';
		$form_message = '';
	}

	// Sending by ID?  Replying to all?  Fetch the memberName(s).
	if (isset($_REQUEST['u']))
	{
		// Store all the members who are getting this...
		$membersTo = array();

		// If the user is replying to all, get all the other members this was sent to..
		if ($_REQUEST['u'] == 'all' && isset($row_quoted))
		{
			// Firstly, to reply to all we clearly already have $row_quoted - so have the original member from.
			$membersTo[] = '&quot;' . $row_quoted['memberName'] . '&quot;';

			// Now to get the others.
			$request = db_query("
				SELECT mem.memberName
				FROM {$db_prefix}im_recipients AS pmr
					LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = pmr.ID_MEMBER)
				WHERE pmr.ID_PM = $_REQUEST[pmsg]
					AND pmr.ID_MEMBER != $ID_MEMBER
					AND bcc = 0", __FILE__, __LINE__);
			while ($row = mysql_fetch_row($request))
				$membersTo[] = '&quot;' . $row[0] . '&quot;';
			mysql_free_result($request);
		}
		else
		{
			$request = db_query("
				SELECT memberName
				FROM {$db_prefix}members
				WHERE ID_MEMBER = " . (int) $_REQUEST['u'] . "
				LIMIT 1", __FILE__, __LINE__);
			list ($membersTo[0]) = mysql_fetch_row($request);
			mysql_free_result($request);

			if ($membersTo[0] != '')
				$membersTo[0] = '&quot;' . $membersTo[0] . '&quot;';
		}

		// Create the 'to' string - Quoting it, just in case it's something like bob,i,like,commas,man.
		$_REQUEST['to'] = implode(', ', $membersTo);
	}

	// Set the defaults...
	$context['subject'] = $form_subject != '' ? $form_subject : $txt[24];
	$context['message'] = str_replace(array('"', '<', '>'), array('&quot;', '&lt;', '&gt;'), $form_message);
	$context['to'] = isset($_REQUEST['to']) ? $_REQUEST['to'] : '';
	$context['bcc'] = isset($_REQUEST['bcc']) ? $_REQUEST['bcc'] : '';
	$context['post_error'] = array();
	$context['copy_to_outbox'] = !empty($options['copy_to_outbox']);

	// And build the link tree.
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=pm;sa=send',
		'name' => $txt[321]
	);

	// Register this form and get a sequence number in $context.
	checkSubmitOnce('register');
}

// An error in the message...
function messagePostError($error_types, $to, $bcc)
{
	global $txt, $context, $scripturl, $modSettings;

	$context['show_spellchecking'] = $modSettings['enableSpellChecking'] && function_exists('pspell_new');

	$context['sub_template'] = 'send';

	$context['page_title'] = $txt[148];

	// Set everything up like before....
	$context['to'] = htmlspecialchars(stripslashes($to));
	$context['bcc'] = htmlspecialchars(stripslashes($bcc));
	$context['subject'] = isset($_REQUEST['subject']) ? htmlspecialchars(stripslashes($_REQUEST['subject'])) : '';
	$context['message'] = isset($_REQUEST['message']) ? str_replace(array('  '), array('&nbsp; '), htmlspecialchars(stripslashes($_REQUEST['message']))) : '';
	$context['reply'] = false;
	$context['copy_to_outbox'] = !empty($_REQUEST['outbox']);

	// Build the link tree....
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=pm;sa=send',
		'name' => $txt[321]
	);

	// Set each of the errors for the template.
	loadLanguage('Errors');
	$context['post_error'] = array(
		'messages' => array(),
	);
	foreach ($error_types as $error_type)
	{
		$context['post_error'][$error_type] = true;
		if (isset($txt['error_' . $error_type]))
			$context['post_error']['messages'][] = $txt['error_' . $error_type];
	}

	// No check for the previous submission is needed.
	checkSubmitOnce('free');

	// Acquire a new form sequence number.
	checkSubmitOnce('register');
}

// Send it!
function MessagePost2()
{
	global $txt, $ID_MEMBER, $context, $sourcedir;
	global $db_prefix, $user_info, $modSettings, $scripturl;

	isAllowedTo('pm_send');
	require_once($sourcedir . '/Subs-Auth.php');

	loadLanguage('InstantMessage');

	// Initialize the errors we're about to make.
	$post_errors = array();

	// If your session timed out, show an error, but do allow to re-submit.
	if (checkSession('post', '', false) != '')
		$post_errors[] = 'session_timeout';

	$_REQUEST['subject'] = isset($_REQUEST['subject']) ? trim($_REQUEST['subject']) : '';

	// Did they make any mistakes?
	if ($_REQUEST['subject'] == '')
		$post_errors[] = 'no_subject';
	if (!isset($_REQUEST['message']) || $_REQUEST['message'] == '')
		$post_errors[] = 'no_message';
	if (empty($_REQUEST['to']) && empty($_REQUEST['bcc']))
		$post_errors[] = 'no_to';
	if (!empty($modSettings['max_messageLength']) && isset($_REQUEST['message']) && strlen($_REQUEST['message']) > $modSettings['max_messageLength'])
		$post_errors[] = 'long_message';

	if (empty($_REQUEST['to']))
		$_REQUEST['to'] = '';
	if (empty($_REQUEST['bcc']))
		$_REQUEST['bcc'] = '';

	// If they did, give a chance to make ammends.
	if (!empty($post_errors))
		return messagePostError($post_errors, $_REQUEST['to'], $_REQUEST['bcc']);

	// Want to take a second glance before you send?
	if (isset($_REQUEST['preview']))
	{
		// Set everything up to be displayed.
		$context['preview_subject'] = htmlspecialchars(stripslashes($_REQUEST['subject']));
		$context['preview_message'] = htmlspecialchars(stripslashes($_REQUEST['message']), ENT_QUOTES);
		preparsecode($context['preview_message'], false);

		// Parse out the BBC if it is enabled.
		$context['preview_message'] = doUBBC($context['preview_message']);

		// Censor, as always.
		censorText($context['preview_subject']);
		censorText($context['preview_message']);

		// Set a descriptive title.
		$context['page_title'] = $txt[507] . ' - ' . $context['preview_subject'];

		// Pretend they messed up :P.
		return messagePostError(array(), $_REQUEST['to'], $_REQUEST['bcc']);
	}

	// Protect from message spamming.
	spamProtection('spam');

	// Prevent double submission of this form.
	checkSubmitOnce('check');

	// Format the to and bcc members.
	$input = array(
		'to' => array(),
		'bcc' => array()
	);

	// To who..?
	if (!empty($_REQUEST['to']))
	{
		// We're going to take out the "s anyway ;).
		$_REQUEST['to'] = strtr($_REQUEST['to'], array('\\"' => '"'));

		preg_match_all('~"([^"]+)"~', $_REQUEST['to'], $matches);
		$input['to'] = array_unique(array_merge($matches[1], explode(',', preg_replace('~"([^"]+)"~', '', $_REQUEST['to']))));
	}

	// Your secret's safe with me!
	if (!empty($_REQUEST['bcc']))
	{
		// We're going to take out the "s anyway ;).
		$_REQUEST['bcc'] = strtr($_REQUEST['bcc'], array('\\"' => '"'));

		preg_match_all('~"([^"]+)"~', $_REQUEST['bcc'], $matches);
		$input['bcc'] = array_unique(array_merge($matches[1], explode(',', preg_replace('~"([^"]+)"~', '', $_REQUEST['bcc']))));
	}

	foreach ($input as $rec_type => $rec)
	{
		foreach ($rec as $index => $member)
			if (strlen(trim($member)) > 0)
				$input[$rec_type][$index] = strtolower(htmlspecialchars(stripslashes(trim($member)), ENT_QUOTES));
			else
				unset($input[$rec_type][$index]);
	}

	// Find the requested members - bcc and to.
	$foundMembers = findMembers(array_merge($input['to'], $input['bcc']));

	// Initialize member ID array.
	$recipients = array(
		'to' => array(),
		'bcc' => array()
	);

	// Store IDs of the members that were found.
	foreach ($foundMembers as $member)
	{
		foreach ($input as $rec_type => $to_members)
			if (array_intersect(array(strtolower($member['username']), strtolower($member['name']), strtolower($member['email'])), $to_members))
			{
				$recipients[$rec_type][] = $member['id'];

				// Get rid of this username. The ones that remain were not found.
				$input[$rec_type] = array_diff($input[$rec_type], array(strtolower($member['username']), strtolower($member['name']), strtolower($member['email'])));
			}
	}

	// Do the actual sending of the PM.
	if (!empty($recipients['to']) || !empty($recipients['bcc']))
		$context['send_log'] = sendpm($recipients, $_REQUEST['subject'], $_REQUEST['message'], !empty($_REQUEST['outbox']));
	else
		$context['send_log'] = array(
			'sent' => array(),
			'failed' => array()
		);

	// Add a log message for all recipients that were not found.
	foreach ($input as $rec_type => $rec)
	{
		// Either bad_to or bad_bcc.
		if (!empty($rec))
			$post_errors[] = 'bad_' . $rec_type;
		foreach ($rec as $member)
			$context['send_log']['failed'][] = sprintf($txt['pm_error_user_not_found'], $member);
	}

	// If one or more of the recipient were invalid, go back to the post screen with the failed usernames.
	if (!empty($context['send_log']['failed']))
		return messagePostError($post_errors, empty($input['to']) ? '' : '"' . implode('","', $input['to']) . '"', empty($input['bcc']) ? '' : '"' . implode('","', $input['bcc']) . '"');

	// Back to the inbox.
	redirectexit('action=pm');
}

// Remove a mess of messages.
function MessageRemoveMore()
{
	global $txt, $db_prefix, $ID_MEMBER, $context, $user_info;

	checkSession(isset($_POST['sc']) ? 'post' : 'get');

	// Delete from the inbox or outbox?
	$context['folder'] = !isset($_REQUEST['f']) || $_REQUEST['f'] != 'outbox' ? 'inbox' : 'outbox';

	if (!empty($_REQUEST['delete']))
		deleteMessages($_REQUEST['delete'], $context['folder']);

	// Back to the folder.
	redirectexit('action=pm;f=' . $context['folder'] . (isset($_GET['start']) ? ';start=' . $_GET['start'] : ''));
}

// Are you sure you want to PERMANENTLY (mostly) delete ALL your messages?
function MessageKillAllQuery()
{
	global $txt, $context;

	// Only have to set up the template....
	$context['sub_template'] = 'ask_delete';
	$context['page_title'] = $txt[412];
	$context['folder'] = $_REQUEST['f'];
	$context['delete_all'] = $context['folder'] == 'all';

	// And set the folder name...
	$txt[412] = str_replace('PMBOX', $context['folder'] != 'outbox' ? $txt[316] : $txt[320], $txt[412]);
}

// Delete ALL the messages!
function MessageKillAll()
{
	checkSession('get');

	// If all then delete all messages the user has.
	if ($_REQUEST['f'] == 'all')
		deleteMessages(null, null);
	// Otherwise just the selected folder.
	else
		deleteMessages(null, $_REQUEST['f'] != 'outbox' ? 'inbox' : 'outbox');

	// Done... all gone.
	redirectexit('action=' . ($_REQUEST['f'] == 'outbox' ? 'pm;f=outbox' : 'pm'));
}

// This function allows the user to delete all messages older than so many days.
function MessagePrune()
{
	global $txt, $context, $db_prefix, $ID_MEMBER;

	// Actually delete the messages.
	if (isset($_REQUEST['age']))
	{
		checkSession();

		// Calculate the time to delete before.
		$deleteTime = time() - (86400 * (int) $_REQUEST['age']);

		// Array to store the IDs in.
		$toDelete = array();

		// Select all the messages they have sent older than $deleteTime.
		$request = db_query("
			SELECT ID_PM
			FROM {$db_prefix}instant_messages
			WHERE deletedBySender = 0
				AND ID_MEMBER_FROM = $ID_MEMBER
				AND msgtime < $deleteTime", __FILE__, __LINE__);
		while ($row = mysql_fetch_row($request))
			$toDelete[] = $row[0];
		mysql_free_result($request);

		// Select all messages in their inbox older than $deleteTime.
		$request = db_query("
			SELECT ir.ID_PM
			FROM {$db_prefix}im_recipients AS ir
				LEFT JOIN {$db_prefix}instant_messages AS pm ON (pm.ID_PM = ir.ID_PM)
			WHERE ir.deleted = 0
				AND ir.ID_MEMBER = $ID_MEMBER
				AND pm.msgtime < $deleteTime", __FILE__, __LINE__);
		while ($row = mysql_fetch_row($request))
			$toDelete[] = $row[0];
		mysql_free_result($request);

		// Delete the actual messages.
		deleteMessages($toDelete);

		// Go back to their inbox.
		redirectexit('action=pm');
	}
	// Show the template.
	else
	{
		$context['sub_template'] = 'prune';
		$context['page_title'] = $txt[411];
	}
}

// Delete the specified personal messages.
function deleteMessages($personal_messages, $folder = null, $owner = null)
{
	global $ID_MEMBER, $db_prefix;

	if ($owner === null)
		$owner = array($ID_MEMBER);
	elseif (empty($owner))
		return;
	elseif (!is_array($owner))
		$owner = array($owner);

	if ($personal_messages !== null)
	{
		if (empty($personal_messages) || !is_array($personal_messages))
			return;

		foreach ($personal_messages as $index => $delete_id)
			$personal_messages[$index] = (int) $delete_id;

		$where =  '
				AND ID_PM IN (' . implode(', ', array_unique($personal_messages)) . ')';
	}
	else
		$where = '';

	if ($folder == 'outbox' || $folder === null)
	{
		db_query("
			UPDATE {$db_prefix}instant_messages
			SET deletedBySender = 1
			WHERE ID_MEMBER_FROM IN (" . implode(', ', $owner) . ")
				AND deletedBySender = 0$where", __FILE__, __LINE__);
	}
	if ($folder != 'outbox' || $folder === null)
	{
		// Calculate the number of messages each member's gonna lose...
		$request = db_query("
			SELECT ID_MEMBER, COUNT(ID_PM) AS numDeletedMessages
			FROM {$db_prefix}im_recipients
			WHERE ID_MEMBER IN (" . implode(', ', $owner) . ")
				AND deleted = 0$where
			GROUP BY ID_MEMBER", __FILE__, __LINE__);
		// ...And update the statistics accordingly.
		while ($row = mysql_fetch_assoc($request))
			updateMemberData($row['ID_MEMBER'], array('instantMessages' => $where == '' ? 0 : "instantMessages - $row[numDeletedMessages]"));
		mysql_free_result($request);

		// Do the actual deletion.
		db_query("
			UPDATE {$db_prefix}im_recipients
			SET deleted = 1
			WHERE ID_MEMBER IN (" . implode(', ', $owner) . ")
				AND deleted = 0$where", __FILE__, __LINE__);
	}

	// If sender and recipients all have deleted their message, it can be removed.
	$request = db_query("
		SELECT pm.ID_PM, pmr.ID_PM AS recipient
		FROM {$db_prefix}instant_messages AS pm
			LEFT JOIN {$db_prefix}im_recipients AS pmr ON (pmr.ID_PM = pm.ID_PM AND deleted = 0)
		WHERE pm.deletedBySender = 1
			" . str_replace('ID_PM', 'pm.ID_PM', $where) . "
		HAVING recipient IS null", __FILE__, __LINE__);
	$remove_pms = array();
	while ($row = mysql_fetch_assoc($request))
		$remove_pms[] = $row['ID_PM'];

	if (!empty($remove_pms))
	{
		db_query("
			DELETE FROM {$db_prefix}instant_messages
			WHERE ID_PM IN (" . implode(', ', $remove_pms) . ")
			LIMIT " . count($remove_pms), __FILE__, __LINE__);

		db_query("
			DELETE FROM {$db_prefix}im_recipients
			WHERE ID_PM IN (" . implode(', ', $remove_pms) . ')', __FILE__, __LINE__);
	}
}

// Mark personal messages read.
function markMessages($personal_messages = null, $owner = null)
{
	global $ID_MEMBER, $db_prefix;

	if ($owner === null)
		$owner = $ID_MEMBER;

	db_query("
		UPDATE {$db_prefix}im_recipients
		SET is_read = 1
		WHERE ID_MEMBER = $owner
			AND is_read = 0" . ($personal_messages !== null ? "
			AND ID_PM IN (" . implode(', ', $personal_messages) . ")
		LIMIT " . count($personal_messages) : ''), __FILE__, __LINE__);
	// If something was marked as read, get the number of unread messages remaining.
	if (db_affected_rows() > 0);
	{
		$request = db_query("
			SELECT COUNT(ID_PM)
			FROM {$db_prefix}im_recipients
			WHERE ID_MEMBER = $owner
				AND is_read = 0", __FILE__, __LINE__);
		list ($num_unmarked) = mysql_fetch_row($request);
		updateMemberData($owner, array('unreadMessages' => $num_unmarked));
	}
}

?>