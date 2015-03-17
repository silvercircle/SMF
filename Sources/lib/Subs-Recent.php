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

/*	!!!

*/

// Get the latest posts of a forum.
function getLastPosts($latestPostOptions)
{
	global $modSettings;

	// Find all the posts.  Newer ones will have higher IDs.  (assuming the last 20 * number are accessable...)
	// !!!SLOW This query is now slow, NEEDS to be fixed.  Maybe break into two?
	$request = smf_db_query('
		SELECT
			m.poster_time, m.subject, m.id_topic, m.id_member, m.id_msg, b.name, m1.subject AS first_subject,
			IFNULL(mem.real_name, m.poster_name) AS poster_name, t.id_board, b.name AS board_name,
			SUBSTRING(m.body, 1, 385) AS body, m.smileys_enabled
		FROM {db_prefix}messages AS m
			INNER JOIN {db_prefix}topics AS t ON (t.id_topic = m.id_topic)
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
			INNER JOIN {db_prefix}messages AS m1 ON (m1.id_msg = t.id_first_msg)
			LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = m.id_member)
		WHERE m.id_msg >= {int:likely_max_msg}' .
			(!empty($modSettings['recycle_enable']) && $modSettings['recycle_board'] > 0 ? '
			AND b.id_board != {int:recycle_board}' : '') . '
			AND {query_wanna_see_board}' . ($modSettings['postmod_active'] ? '
			AND t.approved = {int:is_approved}
			AND m.approved = {int:is_approved}' : '') . '
		ORDER BY m.id_msg DESC
		LIMIT ' . $latestPostOptions['number_posts'],
		array(
			'likely_max_msg' => max(0, $modSettings['maxMsgID'] - 50 * $latestPostOptions['number_posts']),
			'recycle_board' => $modSettings['recycle_board'],
			'is_approved' => 1,
		)
	);
	$posts = array();
	while ($row = mysql_fetch_assoc($request))
	{
		// Censor the subject and post for the preview ;).
		censorText($row['subject']);
		censorText($row['body']);

		$row['body'] = strip_tags(strtr(parse_bbc($row['body'], $row['smileys_enabled'], $row['id_msg']), array('<br />' => '&#10;')));
		if (commonAPI::strlen($row['body']) > 128)
			$row['body'] = commonAPI::substr($row['body'], 0, 128) . '...';

		$bhref = URL::board($row['id_board'], $row['board_name'], 0, true);
		$mhref = URL::user($row['id_member'], $row['poster_name']);
		$thref = URL::topic($row['id_topic'], $row['first_subject'], 0, false, '.msg'.$row['id_msg'], ';topicseen#msg'.$row['id_msg']);
		// Build the array.
		$posts[] = array(
			'board' => array(
				'id' => $row['id_board'],
				'name' => $row['board_name'],
				'href' => $bhref,
				'link' => '<a href="' . $bhref . '">' . $row['board_name'] . '</a>'
			),
			'topic' => $row['id_topic'],
			'poster' => array(
				'id' => $row['id_member'],
				'name' => $row['poster_name'],
				'href' => empty($row['id_member']) ? '' : $mhref,
				'link' => empty($row['id_member']) ? $row['poster_name'] : '<a href="' . $mhref . '">' . $row['poster_name'] . '</a>'
			),
			'subject' => $row['subject'],
			'short_subject' => shorten_subject($row['subject'], 35),
			'preview' => $row['body'],
			'time' => timeformat($row['poster_time']),
			'timestamp' => forum_time(true, $row['poster_time']),
			'raw_timestamp' => $row['poster_time'],
			'href' => $thref,
			'link' => '<a href="' . $thref . '" rel="nofollow">' . $row['first_subject'] . '</a>'
		);
	}
	mysql_free_result($request);

	return $posts;
}

// Callback-function for the cache for getLastPosts().
function cache_getLastPosts($latestPostOptions)
{
	return array(
		'data' => getLastPosts($latestPostOptions),
		'expires' => time() + 300,
		'post_retri_eval' => '
			foreach ($cache_block[\'data\'] as $k => $post)
			{
				$cache_block[\'data\'][$k][\'time\'] = timeformat($post[\'raw_timestamp\']);
				$cache_block[\'data\'][$k][\'timestamp\'] = forum_time(true, $post[\'raw_timestamp\']);
			}',
	);
}
