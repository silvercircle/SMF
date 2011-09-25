<?php
/**
 * %%@productname@%%
 * @copyright 2011 Alex Vie silvercircle(AT)gmail(DOT)com
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright:	2011 Simple Machines (http://www.simplemachines.org)
 * license:  	BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version %%@productversion@%%
 */
function saveDraft()
{
	global $smcFunc, $topic, $board, $user_info, $options;

	if (!isset($_REQUEST['draft']) || $user_info['is_guest'] || empty($options['use_drafts']))
		return false;

	$msgid = isset($_REQUEST['msg']) ? $_REQUEST['msg'] : 0;

	// Clean up what we may or may not have
	$subject = isset($_POST['subject']) ? $_POST['subject'] : '';
	$message = isset($_POST['message']) ? $_POST['message'] : '';
	$icon = isset($_POST['icon']) ? preg_replace('~[\./\\\\*:"\'<>]~', '', $_POST['icon']) : 'xx';

	// Sanitise what we do have
	$subject = $smcFunc['htmltrim']($smcFunc['htmlspecialchars']($subject));
	$message = $smcFunc['htmlspecialchars']($message, ENT_QUOTES);
	preparsecode($message);

	if ($smcFunc['htmltrim']($smcFunc['htmlspecialchars']($subject)) === '' && $smcFunc['htmltrim']($smcFunc['htmlspecialchars']($_POST['message']), ENT_QUOTES) === '')
		fatal_lang_error('empty_draft', false);

	// Hrm, so is this a new draft or not?
	if (isset($_REQUEST['draft_id']) && (int) $_REQUEST['draft_id'] > 0 || $msgid) {
		$_REQUEST['draft_id'] = (int) $_REQUEST['draft_id'];

		$id_cond = $msgid ? ' 1=1 ' : ' id_draft = {int:draft} ';
		$id_sel = $msgid ? ' AND id_msg = {int:message} ' : ' AND id_board = {int:board} AND id_topic = {int:topic} ';

		// Does this draft exist?
		smf_db_query( '
			UPDATE {db_prefix}drafts
			SET subject = {string:subject},
				body = {string:body},
				updated = {int:post_time},
				icon = {string:post_icon},
				smileys = {int:smileys_enabled},
				is_locked = {int:locked},
				is_sticky = {int:sticky}
			WHERE '.$id_cond.'
				AND id_member = {int:member}
				'.$id_sel.'
			LIMIT 1',
			array(
				'draft' => $_REQUEST['draft_id'],
				'board' => $board,
				'topic' => $topic,
				'message' => $msgid,
				'member' => $user_info['id'],
				'subject' => $subject,
				'body' => $message,
				'post_time' => time(),
				'post_icon' => $icon,
				'smileys_enabled' => !isset($_POST['ns']) ? 1 : 0,
				'locked' => !empty($_POST['lock_draft']) ? 1 : 0,
				'sticky' => isset($_POST['sticky']) ? 1: 0,
			)
		);

		if (smf_db_affected_rows() != 0)
			return $_REQUEST['draft_id'];
	}

	smf_db_insert('insert',
		'{db_prefix}drafts',
		array(
			'id_board' => 'int',
			'id_topic' => 'int',
			'id_msg' => 'int',
			'id_member' => 'int',
			'subject' => 'string',
			'body' => 'string',
			'updated' => 'int',
			'icon' => 'string',
			'smileys' => 'int',
			'is_locked' => 'int',
			'is_sticky' => 'int',
		),
		array(
			$board,
			$topic,
			$msgid,
			$user_info['id'],
			$subject,
			$message,
			time(),
			$icon,
			!isset($_POST['ns']) ? 1 : 0,
			!empty($_POST['lock_draft']) ? 1 : 0,
			isset($_POST['sticky']) ? 1: 0,
		),
		array('id_draft')
	);

	return smf_db_insert_id('{db_prefix}drafts');
}

// Output a block of XML that contains the details of our draft
function draftXmlReturn($draft)
{
	if (empty($draft))
		return;

	global $txt, $context;
	header('Content-Type: text/xml; charset=' . (empty($context['character_set']) ? 'ISO-8859-1' : $context['character_set']));
	echo '<', '?xml version="1.0" encoding="', $context['character_set'], '"?', '>
	<response>
		<lastsave id="', $draft, '"><![CDATA[', $txt['last_saved_on'], ': ', timeformat(time()), ']', ']></lastsave>
	</response>';
	obExit(false);
}
?>