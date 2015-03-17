<?php
/**
 * @name      EosAlpha BBS
 * @copyright 2015 Alex Vie silvercircle(AT)gmail(DOT)com
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright:    2011 Simple Machines (http://www.simplemachines.org)
 * license:      BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version 1.0pre
 */
if (!defined('SMF'))
	die('Hacking attempt...');

// Removes attachments - allowed query_types: '', 'messages', 'members'
function removeAttachments($condition, $query_type = '', $return_affected_messages = false, $autoThumbRemoval = true)
{
	global $modSettings, $smcFunc;

//!!! This might need more work!
	$new_condition = array();
	$query_parameter = array(
		'thumb_attachment_type' => 3,
	);

	if (is_array($condition)) {
		foreach ($condition as $real_type => $restriction) {
// Doing a NOT?
			$is_not = substr($real_type, 0, 4) == 'not_';
			$type = $is_not ? substr($real_type, 4) : $real_type;

			if (in_array($type, array('id_member', 'id_attach', 'id_msg')))
				$new_condition[] = 'a.' . $type . ($is_not ? ' NOT' : '') . ' IN (' . (is_array($restriction) ? '{array_int:' . $real_type . '}' : '{int:' . $real_type . '}') . ')';
			elseif ($type == 'attachment_type')
				$new_condition[] = 'a.attachment_type = {int:' . $real_type . '}';
			elseif ($type == 'poster_time')
				$new_condition[] = 'm.poster_time < {int:' . $real_type . '}';
			elseif ($type == 'last_login')
				$new_condition[] = 'mem.last_login < {int:' . $real_type . '}';
			elseif ($type == 'size')
				$new_condition[] = 'a.size > {int:' . $real_type . '}';
			elseif ($type == 'id_topic')
				$new_condition[] = 'm.id_topic IN (' . (is_array($restriction) ? '{array_int:' . $real_type . '}' : '{int:' . $real_type . '}') . ')';

// Add the parameter!
			$query_parameter[$real_type] = $restriction;
		}
		$condition = implode(' AND ', $new_condition);
	}

// Delete it only if it exists...
	$msgs = array();
	$attach = array();
	$parents = array();

// Get all the attachment names and id_msg's.
	$request = smf_db_query('
		SELECT
			a.id_folder, a.filename, a.file_hash, a.attachment_type, a.id_attach, a.id_member' . ($query_type == 'messages' ? ', m.id_msg' : ', a.id_msg') . ',
			thumb.id_folder AS thumb_folder, IFNULL(thumb.id_attach, 0) AS id_thumb, thumb.filename AS thumb_filename, thumb.file_hash AS thumb_file_hash, thumb_parent.id_attach AS id_parent
			FROM {db_prefix}attachments AS a' . ($query_type == 'members' ? '
			INNER JOIN {db_prefix}members AS mem ON (mem.id_member = a.id_member)' : ($query_type == 'messages' ? '
			INNER JOIN {db_prefix}messages AS m ON (m.id_msg = a.id_msg)' : '')) . '
			LEFT JOIN {db_prefix}attachments AS thumb ON (thumb.id_attach = a.id_thumb)
			LEFT JOIN {db_prefix}attachments AS thumb_parent ON (thumb.attachment_type = {int:thumb_attachment_type} AND thumb_parent.id_thumb = a.id_attach)
			WHERE ' . $condition,
		$query_parameter
	);
	while ($row = mysql_fetch_assoc($request)) {
// Figure out the "encrypted" filename and unlink it ;).
		if ($row['attachment_type'] == 1)
			@unlink($modSettings['custom_avatar_dir'] . '/' . $row['filename']);
		else {
			$filename = getAttachmentFilename($row['filename'], $row['id_attach'], $row['id_folder'], false, $row['file_hash']);
			@unlink($filename);

// If this was a thumb, the parent attachment should know about it.
			if (!empty($row['id_parent']))
				$parents[] = $row['id_parent'];

// If this attachments has a thumb, remove it as well.
			if (!empty($row['id_thumb']) && $autoThumbRemoval) {
				$thumb_filename = getAttachmentFilename($row['thumb_filename'], $row['id_thumb'], $row['thumb_folder'], false, $row['thumb_file_hash']);
				@unlink($thumb_filename);
				$attach[] = $row['id_thumb'];
			}
		}

// Make a list.
		if ($return_affected_messages && empty($row['attachment_type']))
			$msgs[] = $row['id_msg'];
		$attach[] = $row['id_attach'];
	}
	mysql_free_result($request);

// Removed attachments don't have to be updated anymore.
	$parents = array_diff($parents, $attach);
	if (!empty($parents))
		smf_db_query('
			UPDATE {db_prefix}attachments
			SET id_thumb = {int:no_thumb}
			WHERE id_attach IN ({array_int:parent_attachments})',
			array(
				'parent_attachments' => $parents,
				'no_thumb' => 0,
			)
		);

	if (!empty($attach))
		smf_db_query('
			DELETE FROM {db_prefix}attachments
			WHERE id_attach IN ({array_int:attachment_list})',
			array(
				'attachment_list' => $attach,
			)
		);

	if ($return_affected_messages)
		return array_unique($msgs);
}

// Approve an attachment, or maybe even more - no permission check!
function ApproveAttachments($attachments)
{
	global $smcFunc;

	if (empty($attachments))
		return 0;

	// For safety, check for thumbnails...
	$request = smf_db_query( '
		SELECT
			a.id_attach, a.id_member, IFNULL(thumb.id_attach, 0) AS id_thumb
		FROM {db_prefix}attachments AS a
			LEFT JOIN {db_prefix}attachments AS thumb ON (thumb.id_attach = a.id_thumb)
		WHERE a.id_attach IN ({array_int:attachments})
			AND a.attachment_type = {int:attachment_type}',
		array(
			'attachments' => $attachments,
			'attachment_type' => 0,
		)
	);
	$attachments = array();
	while ($row = mysql_fetch_assoc($request))
	{
		// Update the thumbnail too...
		if (!empty($row['id_thumb']))
			$attachments[] = $row['id_thumb'];

		$attachments[] = $row['id_attach'];
	}
	mysql_free_result($request);

	// Approving an attachment is not hard - it's easy.
	smf_db_query( '
		UPDATE {db_prefix}attachments
		SET approved = {int:is_approved}
		WHERE id_attach IN ({array_int:attachments})',
		array(
			'attachments' => $attachments,
			'is_approved' => 1,
		)
	);

	// Remove from the approval queue.
	smf_db_query( '
		DELETE FROM {db_prefix}approval_queue
		WHERE id_attach IN ({array_int:attachments})',
		array(
			'attachments' => $attachments,
		)
	);
}
