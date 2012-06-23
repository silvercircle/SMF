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

/*	This is perhaps the most important and probably most accessed files in all
	of SMF.  This file controls topic, message, and attachment display.  It
	does so with the following functions:

	void Display()
		- loads the posts in a topic up so they can be displayed.
		- uses the main sub template of the Display template.
		- requires a topic, and can go to the previous or next topic from it.
		- jumps to the correct post depending on a number/time/IS_MSG passed.
		- depends on the messages_per_page, defaultMaxMessages and enableAllMessages settings.
		- is accessed by ?topic=id_topic.START.

	array prepareDisplayContext(bool reset = false)
		- actually gets and prepares the message context.
		- starts over from the beginning if reset is set to true, which is
		  useful for showing an index before or after the posts.

	void Download()
		- downloads an attachment or avatar, and increments the downloads.
		- requires the view_attachments permission. (not for avatars!)
		- disables the session parser, and clears any previous output.
		- depends on the attachmentUploadDir setting being correct.
		- is accessed via the query string ?action=dlattach.
		- views to attachments and avatars do not increase hits and are not
		  logged in the "Who's Online" log.

	array loadAttachmentContext(int id_msg)
		- loads an attachment's contextual data including, most importantly,
		  its size if it is an image.
		- expects the $attachments array to have been filled with the proper
		  attachment data, as Display() does.
		- requires the view_attachments permission to calculate image size.
		- attempts to keep the "aspect ratio" of the posted image in line,
		  even if it has to be resized by the max_image_width and
		  max_image_height settings.

	int approved_attach_sort(array a, array b)
		- a sort function for putting unapproved attachments first.

	void QuickInTopicModeration()
		- in-topic quick moderation.

*/

define('PCACHE_UPDATE_PER_VIEW', 5); // maximum number of posts to be parse-cached during a single topic page display. TODO: make that an admin panel setting?

// The central part of the board - topic display.
function Display()
{
	global $scripturl, $txt, $modSettings, $context, $settings, $memberContext;
	global $options, $sourcedir, $user_info, $user_profile, $board_info, $topic, $board;
	global $attachments, $messages_request, $topicinfo, $language;

	$context['response_prefixlen'] = strlen($txt['response_prefix']);

	$context['need_synhlt'] = true;
	$context['is_display_std'] = true;

	$context['pcache_update_counter'] = !empty($modSettings['use_post_cache']) ? 0 : PCACHE_UPDATE_PER_VIEW + 1;
	$context['time_cutoff_ref'] = time();

	$context['template_hooks']['display'] = array(
		'header' => '',
	    'extend_topicheader' => '',
		'above_posts' => '',
		'below_posts' => '',
		'footer' => ''
	);
	require_once($sourcedir . '/lib/Subs-LikeSystem.php');
	fetchNewsItems($board, $topic);
	// What are you gonna display if these are empty?!
	if (empty($topic))
		fatal_lang_error('no_board', false);

	// Not only does a prefetch make things slower for the server, but it makes it impossible to know if they read it.
	if (isset($_SERVER['HTTP_X_MOZ']) && $_SERVER['HTTP_X_MOZ'] == 'prefetch')
	{
		ob_end_clean();
		header('HTTP/1.1 403 Prefetch Forbidden');
		die;
	}
	// How much are we sticking on each page?
	$context['messages_per_page'] = commonAPI::getMessagesPerPage();
	$context['page_number'] = isset($_REQUEST['start']) ? $_REQUEST['start'] / $context['messages_per_page'] : 0;

	// Let's do some work on what to search index.

	//$context['multiquote_cookiename'] = 'mq_' . $context['current_topic'];

	$context['multiquote_posts'] = array();
	if(isset($_COOKIE[$context['multiquote_cookiename']]) && strlen($_COOKIE[$context['multiquote_cookiename']]) > 1)
		$context['multiquote_posts'] = explode(',', $_COOKIE[$context['multiquote_cookiename']]);

	$context['multiquote_posts_count'] = count($context['multiquote_posts']);
	if (count($_GET) > 2) {
		foreach ($_GET as $k => $v)
		{
			if (!in_array($k, array('topic', 'board', 'start', session_name())))
				$context['robot_no_index'] = true;
		}
	}
	if (!empty($_REQUEST['start']) && (!is_numeric($_REQUEST['start']) || $_REQUEST['start'] % $context['messages_per_page'] != 0))
		$context['robot_no_index'] = true;

	// Find the previous or next topic.  Make a fuss if there are no more.
	if (isset($_REQUEST['prev_next']) && ($_REQUEST['prev_next'] == 'prev' || $_REQUEST['prev_next'] == 'next'))
	{
		// No use in calculating the next topic if there's only one.
		if ($board_info['num_topics'] > 1)
		{
			// Just prepare some variables that are used in the query.
			$gt_lt = $_REQUEST['prev_next'] == 'prev' ? '>' : '<';
			$order = $_REQUEST['prev_next'] == 'prev' ? '' : ' DESC';

			$request = smf_db_query( '
				SELECT t2.id_topic
				FROM {db_prefix}topics AS t
					INNER JOIN {db_prefix}topics AS t2 ON (' . (empty($modSettings['enableStickyTopics']) ? '
					t2.id_last_msg ' . $gt_lt . ' t.id_last_msg' : '
					(t2.id_last_msg ' . $gt_lt . ' t.id_last_msg AND t2.is_sticky ' . $gt_lt . '= t.is_sticky) OR t2.is_sticky ' . $gt_lt . ' t.is_sticky') . ')
				WHERE t.id_topic = {int:current_topic}
					AND t2.id_board = {int:current_board}' . (!$modSettings['postmod_active'] || allowedTo('approve_posts') ? '' : '
					AND (t2.approved = {int:is_approved} OR (t2.id_member_started != {int:id_member_started} AND t2.id_member_started = {int:current_member}))') . '
				ORDER BY' . (empty($modSettings['enableStickyTopics']) ? '' : ' t2.is_sticky' . $order . ',') . ' t2.id_last_msg' . $order . '
				LIMIT 1',
				array(
					'current_board' => $board,
					'current_member' => $user_info['id'],
					'current_topic' => $topic,
					'is_approved' => 1,
					'id_member_started' => 0,
				)
			);

			// No more left.
			if (mysql_num_rows($request) == 0)
			{
				mysql_free_result($request);

				// Roll over - if we're going prev, get the last - otherwise the first.
				$request = smf_db_query( '
					SELECT id_topic
					FROM {db_prefix}topics
					WHERE id_board = {int:current_board}' . (!$modSettings['postmod_active'] || allowedTo('approve_posts') ? '' : '
						AND (approved = {int:is_approved} OR (id_member_started != {int:id_member_started} AND id_member_started = {int:current_member}))') . '
					ORDER BY' . (empty($modSettings['enableStickyTopics']) ? '' : ' is_sticky' . $order . ',') . ' id_last_msg' . $order . '
					LIMIT 1',
					array(
						'current_board' => $board,
						'current_member' => $user_info['id'],
						'is_approved' => 1,
						'id_member_started' => 0,
					)
				);
			}

			// Now you can be sure $topic is the id_topic to view.
			list ($topic) = mysql_fetch_row($request);
			mysql_free_result($request);

			$context['current_topic'] = $topic;
		}

		// Go to the newest message on this topic.
		$_REQUEST['start'] = 'new';
	}

	// Add 1 to the number of views of this topic.
	if (empty($_SESSION['last_read_topic']) || $_SESSION['last_read_topic'] != $topic)
	{
		smf_db_query( '
			UPDATE {db_prefix}topics
			SET num_views = num_views + 1
			WHERE id_topic = {int:current_topic}',
			array(
				'current_topic' => $topic,
			)
		);

		$_SESSION['last_read_topic'] = $topic;
	}

	if($modSettings['tags_active']) {
		$dbresult= smf_db_query( '
		   SELECT t.tag,l.ID,t.ID_TAG FROM {db_prefix}tags_log as l, {db_prefix}tags as t
			WHERE t.ID_TAG = l.ID_TAG && l.ID_TOPIC = {int:topic}',
			array('topic' => $topic));

		$context['topic_tags'] = array();
		while($row = mysql_fetch_assoc($dbresult)) {
			$context['topic_tags'][] = array(
				'ID' => $row['ID'],
				'ID_TAG' => $row['ID_TAG'],
				'tag' => $row['tag'],
			);
		}
		mysql_free_result($dbresult);
		$context['tags_active'] = true;
	}
	else
		$context['topic_tags'] = $context['tags_active'] = 0;
	
	// Get all the important topic info.
	$request = smf_db_query( '
		SELECT
			t.num_replies, t.num_views, t.locked, ms.poster_name, ms.subject, ms.poster_email, ms.poster_time AS first_post_time, t.is_sticky, t.id_poll,
			t.id_member_started, t.id_first_msg, t.id_last_msg, t.approved, t.unapproved_posts, t.id_layout,
			' . ($user_info['is_guest'] ? 't.id_last_msg + 1' : 'IFNULL(lt.id_msg, IFNULL(lmr.id_msg, -1)) + 1') . ' AS new_from
			' . (!empty($modSettings['recycle_board']) && $modSettings['recycle_board'] == $board ? ', id_previous_board, id_previous_topic' : '') . ',
			p.name AS prefix_name, ms1.poster_time AS last_post_time, ms1.modified_time AS last_modified_time, IFNULL(b.automerge, 0) AS automerge
		FROM {db_prefix}topics AS t
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
			INNER JOIN {db_prefix}messages AS ms1 ON (ms1.id_msg = t.id_last_msg)
			INNER JOIN {db_prefix}messages AS ms ON (ms.id_msg = t.id_first_msg)' . ($user_info['is_guest'] ? '' : '
			LEFT JOIN {db_prefix}log_topics AS lt ON (lt.id_topic = {int:current_topic} AND lt.id_member = {int:current_member})
			LEFT JOIN {db_prefix}log_mark_read AS lmr ON (lmr.id_board = {int:current_board} AND lmr.id_member = {int:current_member})') . '
			LEFT JOIN {db_prefix}prefixes as p ON p.id_prefix = t.id_prefix
		WHERE t.id_topic = {int:current_topic}
		LIMIT 1',
		array(
			'current_member' => $user_info['id'],
			'current_topic' => $topic,
			'current_board' => $board,
		)
	);
	if (mysql_num_rows($request) == 0)
		fatal_lang_error('not_a_topic', false);
		
	// Added by Related Topics
	if (isset($modSettings['have_related_topics']) && $modSettings['have_related_topics'] && !empty($modSettings['relatedTopicsEnabled'])) {
		require_once($sourcedir . '/lib/Subs-Related.php');
		loadRelated($topic);
	}
	
	$topicinfo = mysql_fetch_assoc($request);
	mysql_free_result($request);


	$context['topic_last_modified'] = max($topicinfo['last_post_time'], $topicinfo['last_modified_time']);		// todo: considering - make post cutoff time for the cache depend on the modification time of the topic's last post
	$context['real_num_replies'] = $context['num_replies'] = $topicinfo['num_replies'];
	$context['topic_first_message'] = $topicinfo['id_first_msg'];
	$context['topic_last_message'] = $topicinfo['id_last_msg'];
	$context['first_subject'] = $topicinfo['subject'];
	$context['prefix'] = !empty($topicinfo['prefix_name']) ? html_entity_decode($topicinfo['prefix_name']) .'&nbsp;' : '';
	$context['automerge'] = $topicinfo['automerge'] > 0;

	// Add up unapproved replies to get real number of replies...
	if ($modSettings['postmod_active'] && allowedTo('approve_posts'))
		$context['real_num_replies'] += $topicinfo['unapproved_posts'] - ($topicinfo['approved'] ? 0 : 1);

	// If this topic has unapproved posts, we need to work out how many posts the user can see, for page indexing.
	if ($modSettings['postmod_active'] && $topicinfo['unapproved_posts'] && !$user_info['is_guest'] && !allowedTo('approve_posts'))
	{
		$request = smf_db_query( '
			SELECT COUNT(id_member) AS my_unapproved_posts
			FROM {db_prefix}messages
			WHERE id_topic = {int:current_topic}
				AND id_member = {int:current_member}
				AND approved = 0',
			array(
				'current_topic' => $topic,
				'current_member' => $user_info['id'],
			)
		);
		list ($myUnapprovedPosts) = mysql_fetch_row($request);
		mysql_free_result($request);

		$context['total_visible_posts'] = $context['num_replies'] + $myUnapprovedPosts + ($topicinfo['approved'] ? 1 : 0);
	}
	else
		$context['total_visible_posts'] = $context['num_replies'] + $topicinfo['unapproved_posts'] + ($topicinfo['approved'] ? 1 : 0);

	// When was the last time this topic was replied to?  Should we warn them about it?
	/* redundant query? last_post_time is already in $topicinfo[]
	$request = smf_db_query( '
		SELECT poster_time
		FROM {db_prefix}messages
		WHERE id_msg = {int:id_last_msg}
		LIMIT 1',
		array(
			'id_last_msg' => $topicinfo['id_last_msg'],
		)
	);

	list ($lastPostTime) = mysql_fetch_row($request);
	mysql_free_result($request);
	*/
	$lastPostTime = $topicinfo['last_post_time'];

	$context['oldTopicError'] = !empty($modSettings['oldTopicDays']) && $lastPostTime + $modSettings['oldTopicDays'] * 86400 < time() && empty($sticky);

	// The start isn't a number; it's information about what to do, where to go.
	if (!is_numeric($_REQUEST['start']))
	{
		// Redirect to the page and post with new messages, originally by Omar Bazavilvazo.
		if ($_REQUEST['start'] == 'new')
		{
			// Guests automatically go to the last post.
			if ($user_info['is_guest'])
			{
				$context['start_from'] = $context['total_visible_posts'] - 1;
				$_REQUEST['start'] = empty($options['view_newest_first']) ? $context['start_from'] : 0;
			}
			else
			{
				// Find the earliest unread message in the topic. (the use of topics here is just for both tables.)
				$request = smf_db_query( '
					SELECT IFNULL(lt.id_msg, IFNULL(lmr.id_msg, -1)) + 1 AS new_from
					FROM {db_prefix}topics AS t
						LEFT JOIN {db_prefix}log_topics AS lt ON (lt.id_topic = {int:current_topic} AND lt.id_member = {int:current_member})
						LEFT JOIN {db_prefix}log_mark_read AS lmr ON (lmr.id_board = {int:current_board} AND lmr.id_member = {int:current_member})
					WHERE t.id_topic = {int:current_topic}
					LIMIT 1',
					array(
						'current_board' => $board,
						'current_member' => $user_info['id'],
						'current_topic' => $topic,
					)
				);
				list ($new_from) = mysql_fetch_row($request);
				mysql_free_result($request);

				// Fall through to the next if statement.
				$_REQUEST['start'] = 'msg' . $new_from;
			}
		}

		// Start from a certain time index, not a message.
		if (substr($_REQUEST['start'], 0, 4) == 'from')
		{
			$timestamp = (int) substr($_REQUEST['start'], 4);
			if ($timestamp === 0)
				$_REQUEST['start'] = 0;
			else
			{
				// Find the number of messages posted before said time...
				$request = smf_db_query( '
					SELECT COUNT(*)
					FROM {db_prefix}messages
					WHERE poster_time < {int:timestamp}
						AND id_topic = {int:current_topic}' . ($modSettings['postmod_active'] && $topicinfo['unapproved_posts'] && !allowedTo('approve_posts') ? '
						AND (approved = {int:is_approved}' . ($user_info['is_guest'] ? '' : ' OR id_member = {int:current_member}') . ')' : ''),
					array(
						'current_topic' => $topic,
						'current_member' => $user_info['id'],
						'is_approved' => 1,
						'timestamp' => $timestamp,
					)
				);
				list ($context['start_from']) = mysql_fetch_row($request);
				mysql_free_result($request);

				// Handle view_newest_first options, and get the correct start value.
				$_REQUEST['start'] = empty($options['view_newest_first']) ? $context['start_from'] : $context['total_visible_posts'] - $context['start_from'] - 1;
			}
		}

		// Link to a message...
		elseif (substr($_REQUEST['start'], 0, 3) == 'msg')
		{
			$virtual_msg = (int) substr($_REQUEST['start'], 3);
			if (!$topicinfo['unapproved_posts'] && $virtual_msg >= $topicinfo['id_last_msg'])
				$context['start_from'] = $context['total_visible_posts'] - 1;
			elseif (!$topicinfo['unapproved_posts'] && $virtual_msg <= $topicinfo['id_first_msg'])
				$context['start_from'] = 0;
			else
			{
				// Find the start value for that message......
				$request = smf_db_query( '
					SELECT COUNT(*)
					FROM {db_prefix}messages
					WHERE id_msg < {int:virtual_msg}
						AND id_topic = {int:current_topic}' . ($modSettings['postmod_active'] && $topicinfo['unapproved_posts'] && !allowedTo('approve_posts') ? '
						AND (approved = {int:is_approved}' . ($user_info['is_guest'] ? '' : ' OR id_member = {int:current_member}') . ')' : ''),
					array(
						'current_member' => $user_info['id'],
						'current_topic' => $topic,
						'virtual_msg' => $virtual_msg,
						'is_approved' => 1,
						'no_member' => 0,
					)
				);
				list ($context['start_from']) = mysql_fetch_row($request);
				mysql_free_result($request);
			}

			// We need to reverse the start as well in this case.
			if(isset($_REQUEST['perma']))
				$_REQUEST['start'] = $virtual_msg;
			else
				$_REQUEST['start'] = empty($options['view_newest_first']) ? $context['start_from'] : $context['total_visible_posts'] - $context['start_from'] - 1;
		}
	}

	// Create a previous next string if the selected theme has it as a selected option.
	$context['previous_next'] = $modSettings['enablePreviousNext'] ? '<a href="' . $scripturl . '?topic=' . $topic . '.0;prev_next=prev#new">' . $txt['previous_next_back'] . '</a> <a href="' . $scripturl . '?topic=' . $topic . '.0;prev_next=next#new">' . $txt['previous_next_forward'] . '</a>' : '';

	// Do we need to show the visual verification image?
	$context['require_verification'] = !$user_info['is_mod'] && !$user_info['is_admin'] && !empty($modSettings['posts_require_captcha']) && ($user_info['posts'] < $modSettings['posts_require_captcha'] || ($user_info['is_guest'] && $modSettings['posts_require_captcha'] == -1));
	if ($context['require_verification'])
	{
		require_once($sourcedir . '/lib/Subs-Editor.php');
		$verificationOptions = array(
			'id' => 'post',
		);
		$context['require_verification'] = create_control_verification($verificationOptions);
		$context['visual_verification_id'] = $verificationOptions['id'];
	}

	// Are we showing signatures - or disabled fields?
	$context['signature_enabled'] = substr($modSettings['signature_settings'], 0, 1) == 1;
	$context['disabled_fields'] = isset($modSettings['disabled_profile_fields']) ? array_flip(explode(',', $modSettings['disabled_profile_fields'])) : array();

	// Censor the title...
	censorText($topicinfo['subject']);
	$context['page_title'] = $topicinfo['subject'] . ((int) $context['page_number'] > 0 ? ' - ' . $txt['page'] . ' ' . ($context['page_number'] + 1) : '');

	// Is this topic sticky, or can it even be?
	$topicinfo['is_sticky'] = empty($modSettings['enableStickyTopics']) ? '0' : $topicinfo['is_sticky'];

	// Default this topic to not marked for notifications... of course...
	$context['is_marked_notify'] = false;

	// Did we report a post to a moderator just now?
	$context['report_sent'] = isset($_GET['reportsent']);

	// Let's get nosey, who is viewing this topic?
	if (!empty($settings['display_who_viewing']))
	{
		// Start out with no one at all viewing it.
		$context['view_members'] = array();
		$context['view_members_list'] = array();
		$context['view_num_hidden'] = 0;

		// Search for members who have this topic set in their GET data.
		$request = smf_db_query( '
			SELECT
				lo.id_member, lo.log_time, mem.real_name, mem.member_name, mem.show_online,
				mg.online_color, mg.id_group, mg.group_name
			FROM {db_prefix}log_online AS lo
				LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = lo.id_member)
				LEFT JOIN {db_prefix}membergroups AS mg ON (mg.id_group = CASE WHEN mem.id_group = {int:reg_id_group} THEN mem.id_post_group ELSE mem.id_group END)
			WHERE INSTR(lo.url, {string:in_url_string}) > 0 OR lo.session = {string:session}',
			array(
				'reg_id_group' => 0,
				'in_url_string' => 's:5:"topic";i:' . $topic . ';',
				'session' => $user_info['is_guest'] ? 'ip' . $user_info['ip'] : session_id(),
			)
		);
		while ($row = mysql_fetch_assoc($request))
		{
			if (empty($row['id_member']))
				continue;

			if (!empty($row['online_color']))
				$link = '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '" style="color: ' . $row['online_color'] . ';">' . $row['real_name'] . '</a>';
			else
				$link = '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">' . $row['real_name'] . '</a>';

			$is_buddy = in_array($row['id_member'], $user_info['buddies']);
			if ($is_buddy)
				$link = '<strong>' . $link . '</strong>';

			// Add them both to the list and to the more detailed list.
			if (!empty($row['show_online']) || allowedTo('moderate_forum'))
				$context['view_members_list'][$row['log_time'] . $row['member_name']] = empty($row['show_online']) ? '<em>' . $link . '</em>' : $link;
			$context['view_members'][$row['log_time'] . $row['member_name']] = array(
				'id' => $row['id_member'],
				'username' => $row['member_name'],
				'name' => $row['real_name'],
				'group' => $row['id_group'],
				'href' => $scripturl . '?action=profile;u=' . $row['id_member'],
				'link' => $link,
				'is_buddy' => $is_buddy,
				'hidden' => empty($row['show_online']),
			);

			if (empty($row['show_online']))
				$context['view_num_hidden']++;
		}

		// The number of guests is equal to the rows minus the ones we actually used ;).
		$context['view_num_guests'] = mysql_num_rows($request) - count($context['view_members']);
		mysql_free_result($request);

		// Sort the list.
		krsort($context['view_members']);
		krsort($context['view_members_list']);
	}

	// If all is set, but not allowed... just unset it.
	$can_show_all = !empty($modSettings['enableAllMessages']) && $context['total_visible_posts'] > $context['messages_per_page'] && $context['total_visible_posts'] < $modSettings['enableAllMessages'];
	if (isset($_REQUEST['all']) && !$can_show_all)
		unset($_REQUEST['all']);
	// Otherwise, it must be allowed... so pretend start was -1.
	elseif (isset($_REQUEST['all']))
		$_REQUEST['start'] = -1;

	// Construct the page index, allowing for the .START method...
	if(!isset($_REQUEST['perma']))
		$context['page_index'] = constructPageIndex(URL::topic($topic, $topicinfo['subject'], '%1$d'), $_REQUEST['start'], $context['total_visible_posts'], $context['messages_per_page'], true);
	$context['start'] = $_REQUEST['start'];

	// This is information about which page is current, and which page we're on - in case you don't like the constructed page index. (again, wireles..)
	$context['page_info'] = array(
		'current_page' => $_REQUEST['start'] / $context['messages_per_page'] + 1,
		'num_pages' => floor(($context['total_visible_posts'] - 1) / $context['messages_per_page']) + 1,
	);

	$context['links'] = array(
		'first' => $_REQUEST['start'] >= $context['messages_per_page'] ? $scripturl . '?topic=' . $topic . '.0' : '',
		'prev' => $_REQUEST['start'] >= $context['messages_per_page'] ? $scripturl . '?topic=' . $topic . '.' . ($_REQUEST['start'] - $context['messages_per_page']) : '',
		'next' => $_REQUEST['start'] + $context['messages_per_page'] < $context['total_visible_posts'] ? $scripturl . '?topic=' . $topic. '.' . ($_REQUEST['start'] + $context['messages_per_page']) : '',
		'last' => $_REQUEST['start'] + $context['messages_per_page'] < $context['total_visible_posts'] ? $scripturl . '?topic=' . $topic. '.' . (floor($context['total_visible_posts'] / $context['messages_per_page']) * $context['messages_per_page']) : '',
		'up' => $scripturl . '?board=' . $board . '.0'
	);
	// If they are viewing all the posts, show all the posts, otherwise limit the number.
	if ($can_show_all)
	{
		if (isset($_REQUEST['all']))
		{
			// No limit! (actually, there is a limit, but...)
			$context['messages_per_page'] = -1;
			$context['page_index'] .= ('[<strong>' . $txt['all'] . '</strong>] ');

			// Set start back to 0...
			$_REQUEST['start'] = 0;
		}
		// They aren't using it, but the *option* is there, at least.
		else
			$context['page_index'] .= '&nbsp;<a href="' . $scripturl . '?topic=' . $topic . '.0;all">' . $txt['all'] . '</a> ';
	}

	// Build the link tree.
	$context['linktree'][] = array(
		'url' => URL::topic($topic, $topicinfo['subject'], 0),
		'name' => $topicinfo['subject'],
		'extra_before' => $settings['linktree_inline'] ? $txt['topic'] . ': ' : ''
	);

	// Build a list of this board's moderators.
	$context['moderators'] = &$board_info['moderators'];
	$context['link_moderators'] = array();
	if (!empty($board_info['moderators']))
	{
		// Add a link for each moderator...
		foreach ($board_info['moderators'] as $mod)
			$context['link_moderators'][] = '<a href="' . $scripturl . '?action=profile;u=' . $mod['id'] . '" title="' . $txt['board_moderator'] . '">' . $mod['name'] . '</a>';

		// And show it after the board's name.
		//$context['linktree'][count($context['linktree']) - 2]['extra_after'] = ' (' . (count($context['link_moderators']) == 1 ? $txt['moderator'] : $txt['moderators']) . ': ' . implode(', ', $context['link_moderators']) . ')';
	}

	// Information about the current topic...
	$context['is_locked'] = $topicinfo['locked'];
	$context['is_sticky'] = $topicinfo['is_sticky'];
	$context['is_very_hot'] = $topicinfo['num_replies'] >= $modSettings['hotTopicVeryPosts'];
	$context['is_hot'] = $topicinfo['num_replies'] >= $modSettings['hotTopicPosts'];
	$context['is_approved'] = $topicinfo['approved'];

	// We don't want to show the poll icon in the topic class here, so pretend it's not one.
	$context['is_poll'] = false;
	determineTopicClass($context);

	$context['is_poll'] = $topicinfo['id_poll'] > 0 && $modSettings['pollMode'] == '1' && allowedTo('poll_view');

	// Did this user start the topic or not?
	$context['user']['started'] = $user_info['id'] == $topicinfo['id_member_started'] && !$user_info['is_guest'];
	$context['topic_starter_id'] = $topicinfo['id_member_started'];

	// Set the topic's information for the template.
	$context['subject'] = $topicinfo['subject'];
	$context['num_views'] = $topicinfo['num_views'];
	$context['mark_unread_time'] = $topicinfo['new_from'];

	// Set a canonical URL for this page.
	$context['canonical_url'] = URL::topic($topic, $topicinfo['subject'], $context['start']);
	$context['share_url'] = $scripturl . '?topic=' . $topic;
	// For quick reply we need a response prefix in the default forum language.
	if (!isset($context['response_prefix']) && !($context['response_prefix'] = CacheAPI::getCache('response_prefix', 600)))
	{
		if ($language === $user_info['language'])
			$context['response_prefix'] = $txt['response_prefix'];
		else
		{
			loadLanguage('index', $language, false);
			$context['response_prefix'] = $txt['response_prefix'];
			loadLanguage('index');
		}
		CacheAPI::putCache('response_prefix', $context['response_prefix'], 600);
	}

	// If we want to show event information in the topic, prepare the data.
	if (allowedTo('calendar_view') && !empty($modSettings['cal_showInTopic']) && !empty($modSettings['cal_enabled']))
	{
		// First, try create a better time format, ignoring the "time" elements.
		if (preg_match('~%[AaBbCcDdeGghjmuYy](?:[^%]*%[AaBbCcDdeGghjmuYy])*~', $user_info['time_format'], $matches) == 0 || empty($matches[0]))
			$date_string = $user_info['time_format'];
		else
			$date_string = $matches[0];

		// Any calendar information for this topic?
		$request = smf_db_query( '
			SELECT cal.id_event, cal.start_date, cal.end_date, cal.title, cal.id_member, mem.real_name
			FROM {db_prefix}calendar AS cal
				LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = cal.id_member)
			WHERE cal.id_topic = {int:current_topic}
			ORDER BY start_date',
			array(
				'current_topic' => $topic,
			)
		);
		$context['linked_calendar_events'] = array();
		while ($row = mysql_fetch_assoc($request))
		{
			// Prepare the dates for being formatted.
			$start_date = sscanf($row['start_date'], '%04d-%02d-%02d');
			$start_date = mktime(12, 0, 0, $start_date[1], $start_date[2], $start_date[0]);
			$end_date = sscanf($row['end_date'], '%04d-%02d-%02d');
			$end_date = mktime(12, 0, 0, $end_date[1], $end_date[2], $end_date[0]);

			$context['linked_calendar_events'][] = array(
				'id' => $row['id_event'],
				'title' => $row['title'],
				'can_edit' => allowedTo('calendar_edit_any') || ($row['id_member'] == $user_info['id'] && allowedTo('calendar_edit_own')),
				'modify_href' => $scripturl . '?action=post;msg=' . $topicinfo['id_first_msg'] . ';topic=' . $topic . '.0;calendar;eventid=' . $row['id_event'] . ';' . $context['session_var'] . '=' . $context['session_id'],
				'start_date' => timeformat_static($start_date, $date_string, 'none'),
				'start_timestamp' => $start_date,
				'end_date' => timeformat_static($end_date, $date_string, 'none'),
				'end_timestamp' => $end_date,
				'is_last' => false
			);
		}
		mysql_free_result($request);

		if (!empty($context['linked_calendar_events']))
			$context['linked_calendar_events'][count($context['linked_calendar_events']) - 1]['is_last'] = true;
	}

	// Create the poll info if it exists.
	if ($context['is_poll'])
	{
		// Get the question and if it's locked.
		$request = smf_db_query( '
			SELECT
				p.question, p.voting_locked, p.hide_results, p.expire_time, p.max_votes, p.change_vote,
				p.guest_vote, p.id_member, IFNULL(mem.real_name, p.poster_name) AS poster_name, p.num_guest_voters, p.reset_poll
			FROM {db_prefix}polls AS p
				LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = p.id_member)
			WHERE p.id_poll = {int:id_poll}
			LIMIT 1',
			array(
				'id_poll' => $topicinfo['id_poll'],
			)
		);
		$pollinfo = mysql_fetch_assoc($request);
		mysql_free_result($request);

		$request = smf_db_query( '
			SELECT COUNT(DISTINCT id_member) AS total
			FROM {db_prefix}log_polls
			WHERE id_poll = {int:id_poll}
				AND id_member != {int:not_guest}',
			array(
				'id_poll' => $topicinfo['id_poll'],
				'not_guest' => 0,
			)
		);
		list ($pollinfo['total']) = mysql_fetch_row($request);
		mysql_free_result($request);

		// Total voters needs to include guest voters
		$pollinfo['total'] += $pollinfo['num_guest_voters'];

		// Get all the options, and calculate the total votes.
		$request = smf_db_query( '
			SELECT pc.id_choice, pc.label, pc.votes, IFNULL(lp.id_choice, -1) AS voted_this
			FROM {db_prefix}poll_choices AS pc
				LEFT JOIN {db_prefix}log_polls AS lp ON (lp.id_choice = pc.id_choice AND lp.id_poll = {int:id_poll} AND lp.id_member = {int:current_member} AND lp.id_member != {int:not_guest})
			WHERE pc.id_poll = {int:id_poll}',
			array(
				'current_member' => $user_info['id'],
				'id_poll' => $topicinfo['id_poll'],
				'not_guest' => 0,
			)
		);
		$pollOptions = array();
		$realtotal = 0;
		$pollinfo['has_voted'] = false;
		while ($row = mysql_fetch_assoc($request))
		{
			censorText($row['label']);
			$pollOptions[$row['id_choice']] = $row;
			$realtotal += $row['votes'];
			$pollinfo['has_voted'] |= $row['voted_this'] != -1;
		}
		mysql_free_result($request);

		// If this is a guest we need to do our best to work out if they have voted, and what they voted for.
		if ($user_info['is_guest'] && $pollinfo['guest_vote'] && allowedTo('poll_vote'))
		{
			if (!empty($_COOKIE['guest_poll_vote']) && preg_match('~^[0-9,;]+$~', $_COOKIE['guest_poll_vote']) && strpos($_COOKIE['guest_poll_vote'], ';' . $topicinfo['id_poll'] . ',') !== false)
			{
				// ;id,timestamp,[vote,vote...]; etc
				$guestinfo = explode(';', $_COOKIE['guest_poll_vote']);
				// Find the poll we're after.
				foreach ($guestinfo as $i => $guestvoted)
				{
					$guestvoted = explode(',', $guestvoted);
					if ($guestvoted[0] == $topicinfo['id_poll'])
						break;
				}
				// Has the poll been reset since guest voted?
				if ($pollinfo['reset_poll'] > $guestvoted[1])
				{
					// Remove the poll info from the cookie to allow guest to vote again
					unset($guestinfo[$i]);
					if (!empty($guestinfo))
						$_COOKIE['guest_poll_vote'] = ';' . implode(';', $guestinfo);
					else
						unset($_COOKIE['guest_poll_vote']);
				}
				else
				{
					// What did they vote for?
					unset($guestvoted[0], $guestvoted[1]);
					foreach ($pollOptions as $choice => $details)
					{
						$pollOptions[$choice]['voted_this'] = in_array($choice, $guestvoted) ? 1 : -1;
						$pollinfo['has_voted'] |= $pollOptions[$choice]['voted_this'] != -1;
					}
					unset($choice, $details, $guestvoted);
				}
				unset($guestinfo, $guestvoted, $i);
			}
		}

		// Set up the basic poll information.
		$context['poll'] = array(
			'id' => $topicinfo['id_poll'],
			'image' => 'normal_' . (empty($pollinfo['voting_locked']) ? 'poll' : 'locked_poll'),
			'question' => parse_bbc($pollinfo['question']),
			'total_votes' => $pollinfo['total'],
			'change_vote' => !empty($pollinfo['change_vote']),
			'is_locked' => !empty($pollinfo['voting_locked']),
			'options' => array(),
			'lock' => allowedTo('poll_lock_any') || ($context['user']['started'] && allowedTo('poll_lock_own')),
			'edit' => allowedTo('poll_edit_any') || ($context['user']['started'] && allowedTo('poll_edit_own')),
			'allowed_warning' => $pollinfo['max_votes'] > 1 ? sprintf($txt['poll_options6'], min(count($pollOptions), $pollinfo['max_votes'])) : '',
			'is_expired' => !empty($pollinfo['expire_time']) && $pollinfo['expire_time'] < time(),
			'expire_time' => !empty($pollinfo['expire_time']) ? timeformat($pollinfo['expire_time']) : 0,
			'has_voted' => !empty($pollinfo['has_voted']),
			'starter' => array(
				'id' => $pollinfo['id_member'],
				'name' => $row['poster_name'],
				'href' => $pollinfo['id_member'] == 0 ? '' : $scripturl . '?action=profile;u=' . $pollinfo['id_member'],
				'link' => $pollinfo['id_member'] == 0 ? $row['poster_name'] : '<a href="' . $scripturl . '?action=profile;u=' . $pollinfo['id_member'] . '">' . $row['poster_name'] . '</a>'
			)
		);

		// Make the lock and edit permissions defined above more directly accessible.
		$context['allow_lock_poll'] = $context['poll']['lock'];
		$context['allow_edit_poll'] = $context['poll']['edit'];

		// You're allowed to vote if:
		// 1. the poll did not expire, and
		// 2. you're either not a guest OR guest voting is enabled... and
		// 3. you're not trying to view the results, and
		// 4. the poll is not locked, and
		// 5. you have the proper permissions, and
		// 6. you haven't already voted before.
		$context['allow_vote'] = !$context['poll']['is_expired'] && (!$user_info['is_guest'] || ($pollinfo['guest_vote'] && allowedTo('poll_vote'))) && empty($pollinfo['voting_locked']) && allowedTo('poll_vote') && !$context['poll']['has_voted'];

		// You're allowed to view the results if:
		// 1. you're just a super-nice-guy, or
		// 2. anyone can see them (hide_results == 0), or
		// 3. you can see them after you voted (hide_results == 1), or
		// 4. you've waited long enough for the poll to expire. (whether hide_results is 1 or 2.)
		$context['allow_poll_view'] = allowedTo('moderate_board') || $pollinfo['hide_results'] == 0 || ($pollinfo['hide_results'] == 1 && $context['poll']['has_voted']) || $context['poll']['is_expired'];
		$context['poll']['show_results'] = $context['allow_poll_view'] && (isset($_REQUEST['viewresults']) || isset($_REQUEST['viewResults']));
		$context['show_view_results_button'] = $context['allow_vote'] && (!$context['allow_poll_view'] || !$context['poll']['show_results'] || !$context['poll']['has_voted']);

		// You're allowed to change your vote if:
		// 1. the poll did not expire, and
		// 2. you're not a guest... and
		// 3. the poll is not locked, and
		// 4. you have the proper permissions, and
		// 5. you have already voted, and
		// 6. the poll creator has said you can!
		$context['allow_change_vote'] = !$context['poll']['is_expired'] && !$user_info['is_guest'] && empty($pollinfo['voting_locked']) && allowedTo('poll_vote') && $context['poll']['has_voted'] && $context['poll']['change_vote'];

		// You're allowed to return to voting options if:
		// 1. you are (still) allowed to vote.
		// 2. you are currently seeing the results.
		$context['allow_return_vote'] = $context['allow_vote'] && $context['poll']['show_results'];

		// Calculate the percentages and bar lengths...
		$divisor = $realtotal == 0 ? 1 : $realtotal;

		// Determine if a decimal point is needed in order for the options to add to 100%.
		$precision = $realtotal == 100 ? 0 : 1;

		// Now look through each option, and...
		foreach ($pollOptions as $i => $option)
		{
			// First calculate the percentage, and then the width of the bar...
			$bar = round(($option['votes'] * 100) / $divisor, $precision);
			$barWide = $bar == 0 ? 1 : floor(($bar * 8) / 3);

			// Now add it to the poll's contextual theme data.
			$context['poll']['options'][$i] = array(
				'id' => 'options-' . $i,
				'percent' => $bar,
				'votes' => $option['votes'],
				'voted_this' => $option['voted_this'] != -1,
				'bar' => '<span style="white-space: nowrap;"><img src="' . $settings['images_url'] . '/poll_' . ($context['right_to_left'] ? 'right' : 'left') . '.gif" alt="" /><img src="' . $settings['images_url'] . '/poll_middle.gif" width="' . $barWide . '" height="12" alt="-" /><img src="' . $settings['images_url'] . '/poll_' . ($context['right_to_left'] ? 'left' : 'right') . '.gif" alt="" /></span>',
				// Note: IE < 8 requires us to set a width on the container, too.
				'bar_ndt' => $bar > 0 ? '<div class="bar" style="width: ' . ($bar * 3.5 + 4) . 'px;"><div style="width: ' . $bar * 3.5 . 'px;"></div></div>' : '',
				'bar_width' => $barWide,
				'option' => parse_bbc($option['label']),
				'vote_button' => '<input type="' . ($pollinfo['max_votes'] > 1 ? 'checkbox' : 'radio') . '" name="options[]" id="options-' . $i . '" value="' . $i . '" class="input_' . ($pollinfo['max_votes'] > 1 ? 'check' : 'radio') . '" />'
			);
		}
	}

	// Calculate the fastest way to get the messages!
	$ascending = empty($options['view_newest_first']);
	$start = $_REQUEST['start'];
	$limit = $context['messages_per_page'];
	$firstIndex = 0;
	if ($start >= $context['total_visible_posts'] / 2 && $context['messages_per_page'] != -1)
	{
		$ascending = !$ascending;
		$limit = $context['total_visible_posts'] <= $start + $limit ? $context['total_visible_posts'] - $start : $limit;
		$start = $context['total_visible_posts'] <= $start + $limit ? 0 : $context['total_visible_posts'] - $start - $limit;
		$firstIndex = $limit - 1;
	}

	if(!isset($_REQUEST['perma'])) {
		// Get each post and poster in this topic.
		$request = smf_db_query('
			SELECT id_msg, id_member, approved
			FROM {db_prefix}messages
			WHERE id_topic = {int:current_topic}' . (!$modSettings['postmod_active'] || allowedTo('approve_posts') ? '' : (!empty($modSettings['db_mysql_group_by_fix']) ? '' : '
			GROUP BY id_msg') . '
			HAVING (approved = {int:is_approved}' . ($user_info['is_guest'] ? '' : ' OR id_member = {int:current_member}') . ')') . '
			ORDER BY id_msg ' . ($ascending ? '' : 'DESC') . ($context['messages_per_page'] == -1 ? '' : '
			LIMIT ' . $start . ', ' . $limit),
			array(
				'current_member' => $user_info['id'],
				'current_topic' => $topic,
				'is_approved' => 1,
				'blank_id_member' => 0,
			)
		);

		$messages = array();
		$all_posters = array();
		while ($row = mysql_fetch_assoc($request))
		{
			if (!empty($row['id_member']))
				$all_posters[$row['id_msg']] = $row['id_member'];
			$messages[] = $row['id_msg'];
		}
		mysql_free_result($request);
		$posters[$context['topic_first_message']] = $context['topic_starter_id'];
		$posters = array_unique($all_posters);
	}
	else {
		$request = smf_db_query('
			SELECT id_member, approved
			FROM {db_prefix}messages
			WHERE id_msg = {int:id_msg}',
			array(
				'id_msg' => $virtual_msg
			)
		);
		list($id_member, $approved) = mysql_fetch_row($request);
		mysql_free_result($request);
		EoS_Smarty::loadTemplate('topic_singlepost');
		//loadTemplate('DisplaySingle');
		$context['sub_template'] = isset($_REQUEST['xml']) ? 'single_post_xml' : 'single_post';
		if(isset($_REQUEST['xml'])) {
			$context['template_layers'] = array();
			header('Content-Type: text/xml; charset=UTF-8');
		}
		$messages = array($virtual_msg);
		$posters[$virtual_msg] = $id_member;
	}
	// Guests can't mark topics read or for notifications, just can't sorry.
	if (!$user_info['is_guest'])
	{
		$mark_at_msg = max($messages);
		if ($mark_at_msg >= $topicinfo['id_last_msg'])
			$mark_at_msg = $modSettings['maxMsgID'];
		if ($mark_at_msg >= $topicinfo['new_from'])
		{
			smf_db_insert($topicinfo['new_from'] == 0 ? 'ignore' : 'replace',
				'{db_prefix}log_topics',
				array(
					'id_member' => 'int', 'id_topic' => 'int', 'id_msg' => 'int',
				),
				array(
					$user_info['id'], $topic, $mark_at_msg,
				),
				array('id_member', 'id_topic')
			);
		}

		// Check for notifications on this topic OR board.
		$request = smf_db_query( '
			SELECT sent, id_topic
			FROM {db_prefix}log_notify
			WHERE (id_topic = {int:current_topic} OR id_board = {int:current_board})
				AND id_member = {int:current_member}
			LIMIT 2',
			array(
				'current_board' => $board,
				'current_member' => $user_info['id'],
				'current_topic' => $topic,
			)
		);
		$do_once = true;
		while ($row = mysql_fetch_assoc($request))
		{
			// Find if this topic is marked for notification...
			if (!empty($row['id_topic']))
				$context['is_marked_notify'] = true;

			// Only do this once, but mark the notifications as "not sent yet" for next time.
			if (!empty($row['sent']) && $do_once)
			{
				smf_db_query( '
					UPDATE {db_prefix}log_notify
					SET sent = {int:is_not_sent}
					WHERE (id_topic = {int:current_topic} OR id_board = {int:current_board})
						AND id_member = {int:current_member}',
					array(
						'current_board' => $board,
						'current_member' => $user_info['id'],
						'current_topic' => $topic,
						'is_not_sent' => 0,
					)
				);
				$do_once = false;
			}
		}

		// Have we recently cached the number of new topics in this board, and it's still a lot?
		if (isset($_REQUEST['topicseen']) && isset($_SESSION['topicseen_cache'][$board]) && $_SESSION['topicseen_cache'][$board] > 5)
			$_SESSION['topicseen_cache'][$board]--;
		// Mark board as seen if this is the only new topic.
		elseif (isset($_REQUEST['topicseen']))
		{
			// Use the mark read tables... and the last visit to figure out if this should be read or not.
			$request = smf_db_query( '
				SELECT COUNT(*)
				FROM {db_prefix}topics AS t
					LEFT JOIN {db_prefix}log_boards AS lb ON (lb.id_board = {int:current_board} AND lb.id_member = {int:current_member})
					LEFT JOIN {db_prefix}log_topics AS lt ON (lt.id_topic = t.id_topic AND lt.id_member = {int:current_member})
				WHERE t.id_board = {int:current_board}
					AND t.id_last_msg > IFNULL(lb.id_msg, 0)
					AND t.id_last_msg > IFNULL(lt.id_msg, 0)' . (empty($_SESSION['id_msg_last_visit']) ? '' : '
					AND t.id_last_msg > {int:id_msg_last_visit}'),
				array(
					'current_board' => $board,
					'current_member' => $user_info['id'],
					'id_msg_last_visit' => (int) $_SESSION['id_msg_last_visit'],
				)
			);
			list ($numNewTopics) = mysql_fetch_row($request);
			mysql_free_result($request);

			// If there're no real new topics in this board, mark the board as seen.
			if (empty($numNewTopics))
				$_REQUEST['boardseen'] = true;
			else
				$_SESSION['topicseen_cache'][$board] = $numNewTopics;
		}
		// Probably one less topic - maybe not, but even if we decrease this too fast it will only make us look more often.
		elseif (isset($_SESSION['topicseen_cache'][$board]))
			$_SESSION['topicseen_cache'][$board]--;

		// Mark board as seen if we came using last post link from BoardIndex. (or other places...)
		if (isset($_REQUEST['boardseen']))
		{
			smf_db_insert('replace',
				'{db_prefix}log_boards',
				array('id_msg' => 'int', 'id_member' => 'int', 'id_board' => 'int'),
				array($modSettings['maxMsgID'], $user_info['id'], $board),
				array('id_member', 'id_board')
			);
		}
	}

	$attachments = array();

	// deal with possible sticky posts and different postbit layouts for
	// the first post
	// topic.id_layout meanings: bit 0-6 > layout id, bit 7 > first post sticky on every page.
	// don't blame me for using bit magic here. I'm a C guy and a 8bits can store more than just one bool :P
	
	$layout = (int)($topicinfo['id_layout'] & 0x7f);
	$postbit_classes = &EoS_Smarty::getConfigInstance()->getPostbitClasses();
		// set defaults...
	$context['postbit_callbacks'] = array(
		'firstpost' => 'template_postbit_normal',
		'post' => 'template_postbit_normal'
	);
	$context['postbit_template_class'] = array(
		'firstpost' => $postbit_classes['normal'],
		'post' => $postbit_classes['normal']
	);
	if($topicinfo['id_layout']) {
		$this_start = isset($_REQUEST['perma']) ? 0 : (int)$_REQUEST['start'];
		if(((int)$topicinfo['id_layout'] & 0x80)) {
			if($this_start > 0)
				array_unshift($messages, intval($topicinfo['id_first_msg']));
			$context['postbit_callbacks']['firstpost'] = ($layout == 0 ? 'template_postbit_normal' : ($layout == 2 ? 'template_postbit_clean' : 'template_postbit_lean'));
			$context['postbit_callbacks']['post'] = ($layout == 2 ? 'template_postbit_comment' : 'template_postbit_normal');

			$context['postbit_template_class']['firstpost'] = ($layout == 0 ? $postbit_classes['normal'] : ($layout == 2 ? $postbit_classes['article'] : $postbit_classes['lean']));
			$context['postbit_template_class']['post'] = ($layout == 2 ? $postbit_classes['comment'] : $postbit_classes['normal']);
		}
		elseif($layout) {
			$context['postbit_callbacks']['firstpost'] = ($layout == 0 || $this_start != 0 ? 'template_postbit_normal' : ($layout == 2 ? 'template_postbit_clean' : 'template_postbit_lean'));
			$context['postbit_callbacks']['post'] = ($layout == 2 ? 'template_postbit_comment' : 'template_postbit_normal');

			$context['postbit_template_class']['firstpost'] = ($layout == 0 || $this_start != 0 ? $postbit_classes['normal'] : ($layout == 2 ? $postbit_classes['article'] : $postbit_classes['lean']));
			$context['postbit_template_class']['post'] = ($layout == 2 ? $postbit_classes['comment'] : $postbit_classes['normal']);
		}
	}
	// now we know which display template we need
	if(!isset($_REQUEST['perma']))
		EoS_Smarty::loadTemplate($layout > 1 ? 'topic_page' : 'topic');
	/*
	if($user_info['is_admin']) {
		EoS_Smarty::init();
		if(!isset($_REQUEST['perma']))
			EoS_Smarty::loadTemplate($layout > 1 ? 'topic_page' : 'topic');
	}
	else {
		if(!isset($_REQUEST['perma']))
			loadTemplate($layout > 1 ? 'DisplayPage' : 'Display');
		loadTemplate('Postbit');
	}
	*/
	// If there _are_ messages here... (probably an error otherwise :!)
	if (!empty($messages))
	{
		// Fetch attachments.
		if (!empty($modSettings['attachmentEnable']) && allowedTo('view_attachments'))
		{
			$request = smf_db_query( '
				SELECT
					a.id_attach, a.id_folder, a.id_msg, a.filename, a.file_hash, IFNULL(a.size, 0) AS filesize, a.downloads, a.approved,
					a.width, a.height' . (empty($modSettings['attachmentShowImages']) || empty($modSettings['attachmentThumbnails']) ? '' : ',
					IFNULL(thumb.id_attach, 0) AS id_thumb, thumb.width AS thumb_width, thumb.height AS thumb_height') . '
				FROM {db_prefix}attachments AS a' . (empty($modSettings['attachmentShowImages']) || empty($modSettings['attachmentThumbnails']) ? '' : '
					LEFT JOIN {db_prefix}attachments AS thumb ON (thumb.id_attach = a.id_thumb)') . '
				WHERE a.id_msg IN ({array_int:message_list})
					AND a.attachment_type = {int:attachment_type}',
				array(
					'message_list' => $messages,
					'attachment_type' => 0,
					'is_approved' => 1,
				)
			);
			$temp = array();
			while ($row = mysql_fetch_assoc($request))
			{
				if (!$row['approved'] && $modSettings['postmod_active'] && !allowedTo('approve_posts') && (!isset($all_posters[$row['id_msg']]) || $all_posters[$row['id_msg']] != $user_info['id']))
					continue;

				$temp[$row['id_attach']] = $row;

				if (!isset($attachments[$row['id_msg']]))
					$attachments[$row['id_msg']] = array();
			}
			mysql_free_result($request);

			// This is better than sorting it with the query...
			ksort($temp);

			foreach ($temp as $row)
				$attachments[$row['id_msg']][] = $row;
		}

		// What?  It's not like it *couldn't* be only guests in this topic...
		if(!isset($posters[$context['topic_starter_id']]))
			$posters[] = $context['topic_starter_id'];

		if (!empty($posters))
			loadMemberData($posters);

		if (!isset($user_profile[$context['topic_starter_id']])) // !loadMemberContext($context['topic_starter_id'], true))
		{
			$context['topicstarter']['name'] = $topicinfo['poster_name'];
			$context['topicstarter']['id'] = 0;
			$context['topicstarter']['group'] = $txt['guest_title'];
			$context['topicstarter']['link'] = $topicinfo['poster_name'];
			$context['topicstarter']['email'] = $topicinfo['poster_email'];
			$context['topicstarter']['show_email'] = showEmailAddress(true, 0);
			$context['topicstarter']['is_guest'] = true;
			$context['topicstarter']['avatar'] = array();
		}
		else {
			loadMemberContext($context['topic_starter_id']);
			$context['topicstarter'] = &$memberContext[$context['topic_starter_id']];
		}

		$context['topicstarter']['start_time'] = timeformat($topicinfo['first_post_time']);

		$sql_what = '
			m.id_msg, m.icon, m.subject, m.poster_time, m.poster_ip, m.id_member, m.modified_time, m.modified_name, m.body, mc.body AS cached_body,
			m.smileys_enabled, m.poster_name, m.poster_email, m.approved, m.locked, c.likes_count, c.like_status, c.updated AS like_updated, l.id_user AS liked,
			m.id_msg_modified < {int:new_from} AS is_read';

		$sql_from_tables = '
			FROM {db_prefix}messages AS m';

		$sql_from_joins = '
			LEFT JOIN {db_prefix}likes AS l ON (l.id_msg = m.id_msg AND l.ctype = 1 AND l.id_user = {int:id_user})
			LEFT JOIN {db_prefix}like_cache AS c ON (c.id_msg = m.id_msg AND c.ctype = 1)
			LEFT JOIN {db_prefix}messages_cache AS mc on mc.id_msg = m.id_msg AND mc.style = {int:style} AND mc.lang = {int:lang}';

		$sql_array = array(
			'message_list' => $messages,
			'new_from' => $topicinfo['new_from'],
			'style' => $user_info['smiley_set_id'],
			'lang' => $user_info['language_id'],
			'id_user' => $user_info['id']
		);

		HookAPI::callHook('display_messagerequest', array(&$sql_what, &$sql_from_tables, &$sql_from_joins, &$sql_array));

		$messages_request = smf_db_query('
			SELECT ' . $sql_what . ' ' . $sql_from_tables . $sql_from_joins . '
			WHERE m.id_msg IN ({array_int:message_list})
			ORDER BY m.id_msg' . (empty($options['view_newest_first']) ? '' : ' DESC'),
			$sql_array
		);

		// Go to the last message if the given time is beyond the time of the last message.
		if (isset($context['start_from']) && $context['start_from'] >= $topicinfo['num_replies'])
			$context['start_from'] = $topicinfo['num_replies'];

		// Since the anchor information is needed on the top of the page we load these variables beforehand.
		$context['first_message'] = isset($messages[$firstIndex]) ? $messages[$firstIndex] : $messages[0];
		if (empty($options['view_newest_first']))
			$context['first_new_message'] = isset($context['start_from']) && $_REQUEST['start'] == $context['start_from'];
		else
			$context['first_new_message'] = isset($context['start_from']) && $_REQUEST['start'] == $topicinfo['num_replies'] - $context['start_from'];
	}
	else
	{
		$messages_request = false;
		$context['first_message'] = 0;
		$context['first_new_message'] = false;
	}

	$context['jump_to'] = array(
		'label' => addslashes(un_htmlspecialchars($txt['jump_to'])),
		'board_name' => htmlspecialchars(strtr(strip_tags($board_info['name']), array('&amp;' => '&'))),
		'child_level' => $board_info['child_level'],
	);

	// Set the callback.  (do you REALIZE how much memory all the messages would take?!?)
	$context['get_message'] = 'prepareDisplayContext';

	// Now set all the wonderful, wonderful permissions... like moderation ones...
	$common_permissions = array(
		'can_approve' => 'approve_posts',
		'can_ban' => 'manage_bans',
		'can_sticky' => 'make_sticky',
		'can_merge' => 'merge_any',
		'can_split' => 'split_any',
		'calendar_post' => 'calendar_post',
		'can_mark_notify' => 'mark_any_notify',
		'can_send_topic' => 'send_topic',
		'can_send_pm' => 'pm_send',
		'can_report_moderator' => 'report_any',
		'can_moderate_forum' => 'moderate_forum',
		'can_issue_warning' => 'issue_warning',
		'can_restore_topic' => 'move_any',
		'can_restore_msg' => 'move_any',
	);
	foreach ($common_permissions as $contextual => $perm)
		$context[$contextual] = allowedTo($perm);

	// Permissions with _any/_own versions.  $context[YYY] => ZZZ_any/_own.
	$anyown_permissions = array(
		'can_move' => 'move',
		'can_lock' => 'lock',
		'can_delete' => 'remove',
		'can_add_poll' => 'poll_add',
		'can_remove_poll' => 'poll_remove',
		'can_reply' => 'post_reply',
		'can_reply_unapproved' => 'post_unapproved_replies',
	);
	foreach ($anyown_permissions as $contextual => $perm)
		$context[$contextual] = allowedTo($perm . '_any') || ($context['user']['started'] && allowedTo($perm . '_own'));

	$context['can_add_tags'] = (($context['user']['started'] && allowedTo('smftags_add')) || allowedTo('smftags_manage'));
	$context['can_delete_tags'] = (($context['user']['started'] && allowedTo('smftags_del')) || allowedTo('smftags_manage'));
	$context['can_manage_own'] = ($context['user']['started'] && allowedTo('manage_own_topics'));
	$context['can_moderate_board'] = allowedTo('moderate_board');
	$context['can_modify_any'] = allowedTo('modify_any');
	$context['can_modify_replies'] = allowedTo('modify_replies');
	$context['can_modify_own'] = allowedTo('modify_own');
	$context['can_delete_any'] = allowedTo('delete_any');
	$context['can_delete_replies'] = allowedTo('delete_replies');
	$context['can_delete_own'] = allowedTo('delete_own');
	
	$context['can_see_like'] = allowedTo('like_see');
	$context['can_give_like'] = allowedTo('like_give');
	$context['use_share'] = allowedTo('use_share') && ($context['user']['is_guest'] || (empty($options['use_share_bar']) ? 1 : !$options['use_share_bar']));
	
	$context['can_lock'] |= $context['can_manage_own'];
	$context['can_sticky'] |= $context['can_manage_own'];
	
	$context['can_profile_view_any'] = allowedTo('profile_view_any');
	$context['can_profile_view_own'] = allowedTo('profile_view_own');
	
	// Cleanup all the permissions with extra stuff...
	
	$context['can_mark_notify'] &= !$context['user']['is_guest'];
	$context['can_sticky'] &= !empty($modSettings['enableStickyTopics']);
	$context['calendar_post'] &= !empty($modSettings['cal_enabled']);
	$context['can_add_poll'] &= $modSettings['pollMode'] == '1' && $topicinfo['id_poll'] <= 0;
	$context['can_remove_poll'] &= $modSettings['pollMode'] == '1' && $topicinfo['id_poll'] > 0;
	$context['can_reply'] &= empty($topicinfo['locked']) || allowedTo('moderate_board');
	$context['can_reply_unapproved'] &= $modSettings['postmod_active'] && (empty($topicinfo['locked']) || allowedTo('moderate_board'));
	$context['can_issue_warning'] &= in_array('w', $context['admin_features']) && $modSettings['warning_settings'][0] == 1;
	// Handle approval flags...
	$context['can_reply_approved'] = $context['can_reply'];
	$context['can_reply'] |= $context['can_reply_unapproved'];
	$context['can_quote'] = $context['can_reply'] && (empty($modSettings['disabledBBC']) || !in_array('quote', explode(',', $modSettings['disabledBBC'])));
	$context['can_mark_unread'] = !$user_info['is_guest'] && $settings['show_mark_read'];

	$context['can_send_topic'] = (!$modSettings['postmod_active'] || $topicinfo['approved']) && allowedTo('send_topic');

	// Start this off for quick moderation - it will be or'd for each post.
	$context['can_remove_post'] = allowedTo('delete_any') || (allowedTo('delete_replies') && $context['user']['started']);

	// Can restore topic?  That's if the topic is in the recycle board and has a previous restore state.
	$context['can_restore_topic'] &= !empty($modSettings['recycle_enable']) && $modSettings['recycle_board'] == $board && !empty($topicinfo['id_previous_board']);
	$context['can_restore_msg'] &= !empty($modSettings['recycle_enable']) && $modSettings['recycle_board'] == $board && !empty($topicinfo['id_previous_topic']);

	// Load up the "double post" sequencing magic.
	if (!empty($options['display_quick_reply']))
	{
		checkSubmitOnce('register');
		$context['name'] = isset($_SESSION['guest_name']) ? $_SESSION['guest_name'] : '';
		$context['email'] = isset($_SESSION['guest_email']) ? $_SESSION['guest_email'] : '';
	}
	
	$context['can_save_draft'] = $context['can_reply'] && !$context['user']['is_guest'] && in_array('dr', $context['admin_features']) && !empty($options['use_drafts']) && allowedTo('drafts_allow');
	$context['can_autosave_draft'] = $context['can_save_draft'] && !empty($modSettings['enableAutoSaveDrafts']) && allowedTo('drafts_autosave_allow');

	enqueueThemeScript('topic', 'scripts/topic.js', true);
	if($context['can_autosave_draft'])
		enqueueThemeScript('drafts', 'scripts/drafts.js', true);

	if(EoS_Smarty::isActive()) {
		if(isset($context['poll'])) {
		    $context['poll_buttons'] = array(
		      'vote' => array('test' => 'allow_return_vote', 'text' => 'poll_return_vote', 'image' => 'poll_options.gif', 'lang' => true, 'url' => $scripturl . '?topic=' . $context['current_topic'] . '.' . $context['start']),
		      'results' => array('test' => 'show_view_results_button', 'text' => 'poll_results', 'image' => 'poll_results.gif', 'lang' => true, 'url' => $scripturl . '?topic=' . $context['current_topic'] . '.' . $context['start'] . ';viewresults'),
		      'change_vote' => array('test' => 'allow_change_vote', 'text' => 'poll_change_vote', 'image' => 'poll_change_vote.gif', 'lang' => true, 'url' => $scripturl . '?action=vote;topic=' . $context['current_topic'] . '.' . $context['start'] . ';poll=' . $context['poll']['id'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		      'lock' => array('test' => 'allow_lock_poll', 'text' => (!$context['poll']['is_locked'] ? 'poll_lock' : 'poll_unlock'), 'image' => 'poll_lock.gif', 'lang' => true, 'url' => $scripturl . '?action=lockvoting;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		      'edit' => array('test' => 'allow_edit_poll', 'text' => 'poll_edit', 'image' => 'poll_edit.gif', 'lang' => true, 'url' => $scripturl . '?action=editpoll;topic=' . $context['current_topic'] . '.' . $context['start']),
		      'remove_poll' => array('test' => 'can_remove_poll', 'text' => 'poll_remove', 'image' => 'admin_remove_poll.gif', 'lang' => true, 'custom' => 'onclick="return Eos_Confirm(\'\', \'' . $txt['poll_remove_warn'] . '\', $(this).attr(\'href\'));"', 'url' => $scripturl . '?action=removepoll;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		    );
		}
  		$context['normal_buttons'] = array(
    		'reply' => array('test' => 'can_reply', 'text' => 'reply', 'custom' => 'onclick="return oQuickReply.quote(0);" ', 'image' => 'reply.gif', 'lang' => true, 'url' => $scripturl . '?action=post;topic=' . $context['current_topic'] . '.' . $context['start'] . ';last_msg=' . $context['topic_last_message'], 'active' => true),
    		'add_poll' => array('test' => 'can_add_poll', 'text' => 'add_poll', 'image' => 'add_poll.gif', 'lang' => true, 'url' => $scripturl . '?action=editpoll;add;topic=' . $context['current_topic'] . '.' . $context['start']),
    		'mark_unread' => array('test' => 'can_mark_unread', 'text' => 'mark_unread', 'image' => 'markunread.gif', 'lang' => true, 'url' => $scripturl . '?action=markasread;sa=topic;t=' . $context['mark_unread_time'] . ';topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
  		);
  		HookAPI::callHook('integrate_display_buttons', array(&$context['normal_buttons']));
    
  		$remove_url = $scripturl . '?action=removetopic2;topic=' . $context['current_topic'] . '.0;' . $context['session_var'] . '=' . $context['session_id'];
  		$context['mod_buttons'] = array(
    		'move' => array('test' => 'can_move', 'text' => 'move_topic', 'image' => 'admin_move.gif', 'lang' => true, 'url' => $scripturl . '?action=movetopic;topic=' . $context['current_topic'] . '.0'),
    		'delete' => array('test' => 'can_delete', 'text' => 'remove_topic', 'image' => 'admin_rem.gif', 'lang' => true, 'custom' => 'onclick="return Eos_Confirm(\'\',\'' . $txt['are_sure_remove_topic'] . '\',\''.$remove_url.'\');"', 'url' => $remove_url),
    		'lock' => array('test' => 'can_lock', 'text' => empty($context['is_locked']) ? 'set_lock' : 'set_unlock', 'image' => 'admin_lock.gif', 'lang' => true, 'url' => $scripturl . '?action=lock;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
    		'sticky' => array('test' => 'can_sticky', 'text' => empty($context['is_sticky']) ? 'set_sticky' : 'set_nonsticky', 'image' => 'admin_sticky.gif', 'lang' => true, 'url' => $scripturl . '?action=sticky;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
    		'merge' => array('test' => 'can_merge', 'text' => 'merge', 'image' => 'merge.gif', 'lang' => true, 'url' => $scripturl . '?action=mergetopics;board=' . $context['current_board'] . '.0;from=' . $context['current_topic']),
    		'calendar' => array('test' => 'calendar_post', 'text' => 'calendar_link', 'image' => 'linktocal.gif', 'lang' => true, 'url' => $scripturl . '?action=post;calendar;msg=' . $context['topic_first_message'] . ';topic=' . $context['current_topic'] . '.0'),
  		);
  		// Restore topic. eh?  No monkey business.
  		if ($context['can_restore_topic'])
    		$context['mod_buttons'][] = array('text' => 'restore_topic', 'image' => '', 'lang' => true, 'url' => $scripturl . '?action=restoretopic;topics=' . $context['current_topic'] . ';' . $context['session_var'] . '=' . $context['session_id']);
  		// Allow adding new mod buttons easily.
  		HookAPI::callHook('integrate_mod_buttons', array(&$context['mod_buttons']));

  		$context['message_ids'] = $messages;
  		$context['perma_request'] = isset($_REQUEST['perma']) ? true : false;

  		$context['mod_buttons_style'] = array('id' => 'moderationbuttons_strip', 'class' => 'plainbuttonlist');
		$context['full_members_viewing_list'] = empty($context['view_members_list']) ? '0 ' . $txt['members'] : implode(', ', $context['view_members_list']) . ((empty($context['view_num_hidden']) || $context['can_moderate_forum']) ? '' : ' (+ ' . $context['view_num_hidden'] . ' ' . $txt['hidden'] . ')');
	}
	HookAPI::callHook('display_general', array());
}

// Callback for the message display.
function prepareDisplayContext($reset = false)
{
	global $txt, $modSettings, $options, $user_info;
	global $memberContext, $context, $messages_request;
	static $counter = null;
	static $seqnr = 0;
	static $output = array();

	// If the query returned false, bail.
	if ($messages_request == false)
		return false;

	// Remember which message this is.  (ie. reply #83)
	if ($counter === null || $reset)
		$counter = empty($options['view_newest_first']) ? $context['start'] : $context['total_visible_posts'] - $context['start'];

	// Start from the beginning...
	if ($reset)
		return @mysql_data_seek($messages_request, 0);

	// Attempt to get the next message.
	$message = mysql_fetch_assoc($messages_request);
	if (!$message)
	{
		mysql_free_result($messages_request);
		return false;
	}

	// If you're a lazy bum, you probably didn't give a subject...
	$message['subject'] = $message['subject'] != '' ? $message['subject'] : $txt['no_subject'];

	// Are you allowed to remove at least a single reply?
	$context['can_remove_post'] |= $context['can_delete_own'] && (empty($modSettings['edit_disable_time']) || $message['poster_time'] + $modSettings['edit_disable_time'] * 60 >= time()) && ($message['id_member'] == $user_info['id'] || $context['can_manage_own']);

	// If it couldn't load, or the user was a guest.... someday may be done with a guest table.
	if (!loadMemberContext($message['id_member'], true))
	{
		// Notice this information isn't used anywhere else....
		$memberContext[$message['id_member']]['name'] = $message['poster_name'];
		$memberContext[$message['id_member']]['id'] = 0;
		$memberContext[$message['id_member']]['group'] = $txt['guest_title'];
		$memberContext[$message['id_member']]['link'] = $message['poster_name'];
		$memberContext[$message['id_member']]['email'] = $message['poster_email'];
		$memberContext[$message['id_member']]['show_email'] = showEmailAddress(true, 0);
		$memberContext[$message['id_member']]['is_guest'] = true;
	}
	else
	{
		$memberContext[$message['id_member']]['can_view_profile'] = $context['can_profile_view_any'] || ($message['id_member'] == $user_info['id'] && $context['can_profile_view_own']);
		$memberContext[$message['id_member']]['is_topic_starter'] = $message['id_member'] == $context['topic_starter_id'];
		$memberContext[$message['id_member']]['can_see_warning'] = !isset($context['disabled_fields']['warning_status']) && $memberContext[$message['id_member']]['warning_status'] && ($context['user']['can_mod'] || (!$user_info['is_guest'] && !empty($modSettings['warning_show']) && ($modSettings['warning_show'] > 1 || $message['id_member'] == $user_info['id'])));
	}

	$memberContext[$message['id_member']]['ip'] = $message['poster_ip'];

	// Do the censor thang.
	censorText($message['subject']);

	// create a cached (= parsed) version of the post on the fly
	// but only if it's not older than the cutoff time.
	// and do not cache more than PCACHE_UPDATE_PER_VIEW posts per thread view to reduce load spikes
	$dateline = max($message['modified_time'], $message['poster_time']);
	if($context['pcache_update_counter'] < PCACHE_UPDATE_PER_VIEW && (($context['time_cutoff_ref'] - $dateline) < ($modSettings['post_cache_cutoff'] * 86400))) {
		if(empty($message['cached_body'])) {
			$context['pcache_update_counter']++;
			$message['body'] = parse_bbc($message['body'], $message['smileys_enabled'], '');  // don't cache bbc when we pre-parse the post anyway...
			smf_db_insert('replace', '{db_prefix}messages_cache',
				array('id_msg' => 'int', 'body' => 'string', 'style' => 'string', 'lang' => 'string', 'updated' => 'int'),
				array($message['id_msg'], $message['body'], $user_info['smiley_set_id'], $user_info['language_id'], $dateline),
				array('id_msg', 'body', 'style', 'lang', 'updated'));
			parse_bbc_stage2($message['body'], $message['id_msg']);
		}
		else {
			$message['body'] = &$message['cached_body'];
			parse_bbc_stage2($message['body'], $message['id_msg']);
        }
	}
	else {
		$message['body'] = parse_bbc($message['body'], $message['smileys_enabled'], $message['id_msg']);
		parse_bbc_stage2($message['body'], $message['id_msg']);
    }

	censorText($message['body']);

	// Compose the memory eat- I mean message array.
	//$t_href = URL::topic($topic, $message['subject'], 0, false, '.msg' . $message['id_msg'] . '#msg'.$message['id_msg']);
	$output = array(
		'attachment' => loadAttachmentContext($message['id_msg']),
		'alternate' => $counter % 2,
		'id' => $message['id_msg'],
		'permahref' => URL::parse('?msg=' . $message['id_msg'] . (isset($_REQUEST['perma']) ? '' : ';perma')),
		'member' => &$memberContext[$message['id_member']],
		'icon' => $message['icon'],
		'icon_url' => getPostIcon($message['icon']),
		'subject' => $message['subject'],
		'time' => timeformat($message['poster_time']),
		'counter' => $counter,
//		'permalink' => isset($_REQUEST['perma']) ? $txt['view_in_thread'] : ($counter ? ($txt['reply_noun'].' #'.$counter) : $txt['permalink']),
		'permalink' => isset($_REQUEST['perma']) ? $txt['view_in_thread'] : ' #' . ($counter + 1),
		'modified' => array(
			'time' => timeformat($message['modified_time']),
			'name' => $message['modified_name']
		),
		'body' => &$message['body'],
		'new' => empty($message['is_read']),
		'approved' => $message['approved'],
		'first_new' => isset($context['start_from']) && $context['start_from'] == $counter,
		'is_ignored' => !empty($modSettings['enable_buddylist']) && !empty($options['posts_apply_ignore_list']) && in_array($message['id_member'], $context['user']['ignoreusers']),
		'can_approve' => !$message['approved'] && $context['can_approve'],
		'can_unapprove' => $message['approved'] && $context['can_approve'],
		'can_modify' => (!$message['locked'] || $context['can_moderate_board']) && ((!$context['is_locked'] || $context['can_moderate_board']) && ($context['can_modify_any'] || ($context['can_modify_replies'] && $context['user']['started']) || ($context['can_modify_own'] && $message['id_member'] == $user_info['id'] && (empty($modSettings['edit_disable_time']) || !$message['approved'] || $message['poster_time'] + $modSettings['edit_disable_time'] * 60 > time())))),
		'can_remove' => (!$message['locked'] || $context['can_moderate_board']) && ($context['can_delete_any'] || ($context['can_delete_replies'] && $context['user']['started']) || ($context['can_delete_own'] && $message['id_member'] == $user_info['id'] && (empty($modSettings['edit_disable_time']) || $message['poster_time'] + $modSettings['edit_disable_time'] * 60 > time()))),
		'can_see_ip' => $context['can_moderate_forum'] || ($message['id_member'] == $user_info['id'] && !empty($user_info['id'])),
		'likes_count' => $message['likes_count'],
		'like_status' => $message['like_status'],
		'liked' => $message['liked'],
		'like_updated' => $message['like_updated'],
		'id_member' => $message['id_member'],
		'postbit_callback' => $message['approved'] ? ($message['id_msg'] == $context['first_message'] ? $context['postbit_callbacks']['firstpost'] : $context['postbit_callbacks']['post']) : 'template_postbit_comment',
		'postbit_template_class' => $message['approved'] ? ($message['id_msg'] == $context['first_message'] ? $context['postbit_template_class']['firstpost'] : $context['postbit_template_class']['post']) : 'c',
		'mq_marked' => in_array($message['id_msg'], $context['multiquote_posts'])
	);

	if($context['can_see_like'])
		AddLikeBar($output, $context['can_give_like'], $context['time_cutoff_ref']);
	else
		$output['likes_count'] = 0;
	// Is this user the message author?
	$output['is_message_author'] = $message['id_member'] == $user_info['id'];
	$counter += (empty($options['view_newest_first']) ? 1 : -1);
	// hooks can populate these fields with additional content
	$output['template_hook'] = array(
		'before_sig' => '',
		'after_sig' => '',
		'postbit_below' => '',
		'poster_details' => ''
	);
	HookAPI::callHook('display_postbit', array(&$output));
	if(isset($output['member']['can_see_warning']) && !empty($output['member']['can_see_warning'])) {
		$output['member']['warning_status_desc'] = isset($output['member']['warning_status']) ? $txt['user_warn_' . $output['member']['warning_status']] : '';
		$output['member']['warning_status_desc1'] = isset($output['member']['warning_status']) ? $txt['warn_' . $output['member']['warning_status']] : '';
	}
	$output['member']['allow_show_email'] = $output['member']['is_guest'] ? (!empty($output['member']['email']) && in_array($output['member']['show_email'], array('yes', 'yes_permission_override', 'no_through_forum'))) : false;
	//$context['current_message'] = &$output;
	if($output['can_remove'])
		$context['removableMessageIDs'][] = $output['id'];
	return $output;
}

// Download an attachment.
function Download()
{
	global $txt, $modSettings, $user_info, $context, $topic;

	// Some defaults that we need.
	$context['character_set'] = 'UTF-8';
	$context['no_last_modified'] = true;

	// Make sure some attachment was requested!
	if (!isset($_REQUEST['attach']) && !isset($_REQUEST['id']))
		fatal_lang_error('no_access', false);

	$_REQUEST['attach'] = isset($_REQUEST['attach']) ? (int) $_REQUEST['attach'] : (int) $_REQUEST['id'];

	if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'avatar')
	{
		$request = smf_db_query( '
			SELECT id_folder, filename, file_hash, fileext, id_attach, attachment_type, mime_type, approved, id_member
			FROM {db_prefix}attachments
			WHERE id_attach = {int:id_attach}
				AND id_member > {int:blank_id_member}
			LIMIT 1',
			array(
				'id_attach' => $_REQUEST['attach'],
				'blank_id_member' => 0,
			)
		);
		$_REQUEST['image'] = true;
	}
	// This is just a regular attachment...
	else
	{
		// This checks only the current board for $board/$topic's permissions.
		isAllowedTo('view_attachments');

		// Make sure this attachment is on this board.
		// NOTE: We must verify that $topic is the attachment's topic, or else the permission check above is broken.
		$request = smf_db_query( '
			SELECT a.id_folder, a.filename, a.file_hash, a.fileext, a.id_attach, a.attachment_type, a.mime_type, a.approved, m.id_member
			FROM {db_prefix}attachments AS a
				INNER JOIN {db_prefix}messages AS m ON (m.id_msg = a.id_msg AND m.id_topic = {int:current_topic})
				INNER JOIN {db_prefix}boards AS b ON (b.id_board = m.id_board AND {query_see_board})
			WHERE a.id_attach = {int:attach}
			LIMIT 1',
			array(
				'attach' => $_REQUEST['attach'],
				'current_topic' => $topic,
			)
		);
	}
	if (mysql_num_rows($request) == 0)
		fatal_lang_error('no_access', false);
	list ($id_folder, $real_filename, $file_hash, $file_ext, $id_attach, $attachment_type, $mime_type, $is_approved, $id_member) = mysql_fetch_row($request);
	mysql_free_result($request);

	// If it isn't yet approved, do they have permission to view it?
	if (!$is_approved && ($id_member == 0 || $user_info['id'] != $id_member) && ($attachment_type == 0 || $attachment_type == 3))
		isAllowedTo('approve_posts');

	// Update the download counter (unless it's a thumbnail).
	if ($attachment_type != 3)
		smf_db_query('
			UPDATE LOW_PRIORITY {db_prefix}attachments
			SET downloads = downloads + 1
			WHERE id_attach = {int:id_attach}',
			array(
				'id_attach' => $id_attach,
			)
		);

	$filename = getAttachmentFilename($real_filename, $_REQUEST['attach'], $id_folder, false, $file_hash);

	// This is done to clear any output that was made before now. (would use ob_clean(), but that's PHP 4.2.0+...)
	ob_end_clean();
	if (!empty($modSettings['enableCompressedOutput']) && @filesize($filename) <= 4194304 && in_array($file_ext, array('txt', 'html', 'htm', 'js', 'doc', 'pdf', 'docx', 'rtf', 'css', 'php', 'log', 'xml', 'sql', 'c', 'java')))
		@ob_start('ob_gzhandler');
	else
	{
		ob_start();
		header('Content-Encoding: none');
	}

	// No point in a nicer message, because this is supposed to be an attachment anyway...
	if (!file_exists($filename))
	{
		loadLanguage('Errors');

		header('HTTP/1.0 404 ' . $txt['attachment_not_found']);
		header('Content-Type: text/plain; charset=UTF-8');

		// We need to die like this *before* we send any anti-caching headers as below.
		die('404 - ' . $txt['attachment_not_found']);
	}

	// If it hasn't been modified since the last time this attachement was retrieved, there's no need to display it again.
	if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']))
	{
		list($modified_since) = explode(';', $_SERVER['HTTP_IF_MODIFIED_SINCE']);
		if (strtotime($modified_since) >= filemtime($filename))
		{
			ob_end_clean();

			// Answer the question - no, it hasn't been modified ;).
			header('HTTP/1.1 304 Not Modified');
			exit;
		}
	}

	// Check whether the ETag was sent back, and cache based on that...
	$eTag = '"' . substr($_REQUEST['attach'] . $real_filename . filemtime($filename), 0, 64) . '"';
	if (!empty($_SERVER['HTTP_IF_NONE_MATCH']) && strpos($_SERVER['HTTP_IF_NONE_MATCH'], $eTag) !== false)
	{
		ob_end_clean();

		header('HTTP/1.1 304 Not Modified');
		exit;
	}

	// Send the attachment headers.
	header('Pragma: ');
	if (!$context['browser']['is_gecko'])
		header('Content-Transfer-Encoding: binary');
	header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 525600 * 60) . ' GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filename)) . ' GMT');
	header('Accept-Ranges: bytes');
	header('Connection: close');
	header('ETag: ' . $eTag);

	// Make sure the mime type warrants an inline display.
	if (isset($_REQUEST['image']) && !empty($mime_type) && strpos($mime_type, 'image/') !== 0)
		unset($_REQUEST['image']);

	// Does this have a mime type?
	elseif (!empty($mime_type) && (isset($_REQUEST['image']) || !in_array($file_ext, array('jpg', 'gif', 'jpeg', 'x-ms-bmp', 'png', 'psd', 'tiff', 'iff'))))
		header('Content-Type: ' . strtr($mime_type, array('image/bmp' => 'image/x-ms-bmp')));

	else
	{
		header('Content-Type: ' . ($context['browser']['is_ie'] || $context['browser']['is_opera'] ? 'application/octetstream' : 'application/octet-stream'));
		if (isset($_REQUEST['image']))
			unset($_REQUEST['image']);
	}

	// Convert the file to UTF-8, cuz most browsers dig that.
	$utf8name = $real_filename;
	$fixchar = create_function('$n', '
		if ($n < 32)
			return \'\';
		elseif ($n < 128)
			return chr($n);
		elseif ($n < 2048)
			return chr(192 | $n >> 6) . chr(128 | $n & 63);
		elseif ($n < 65536)
			return chr(224 | $n >> 12) . chr(128 | $n >> 6 & 63) . chr(128 | $n & 63);
		else
			return chr(240 | $n >> 18) . chr(128 | $n >> 12 & 63) . chr(128 | $n >> 6 & 63) . chr(128 | $n & 63);');

	$disposition = !isset($_REQUEST['image']) ? 'attachment' : 'inline';

	// Different browsers like different standards...
	if ($context['browser']['is_firefox'])
		//header('Content-Disposition: ' . $disposition . '; filename*="UTF-8\'\'' . preg_replace('~&#(\d{3,8});~e', '$fixchar(\'$1\')', $utf8name) . '"');
		header('Content-Disposition: ' . $disposition . '; filename*=UTF-8\'\'' . preg_replace('~&#(\d{3,8});~e', '$fixchar(\'$1\')', $utf8name));

	elseif ($context['browser']['is_opera'])
		header('Content-Disposition: ' . $disposition . '; filename="' . preg_replace('~&#(\d{3,8});~e', '$fixchar(\'$1\')', $utf8name) . '"');

	elseif ($context['browser']['is_ie'])
		header('Content-Disposition: ' . $disposition . '; filename="' . urlencode(preg_replace('~&#(\d{3,8});~e', '$fixchar(\'$1\')', $utf8name)) . '"');

	else
		header('Content-Disposition: ' . $disposition . '; filename="' . $utf8name . '"');

	// If this has an "image extension" - but isn't actually an image - then ensure it isn't cached cause of silly IE.
	if (!isset($_REQUEST['image']) && in_array($file_ext, array('gif', 'jpg', 'bmp', 'png', 'jpeg', 'tiff')))
		header('Cache-Control: no-cache');
	else
		header('Cache-Control: max-age=' . (525600 * 60) . ', private');

	if (empty($modSettings['enableCompressedOutput']) || filesize($filename) > 4194304)
		header('Content-Length: ' . filesize($filename));

	// Try to buy some time...
	@set_time_limit(600);

	// Recode line endings for text files, if enabled.
	if (!empty($modSettings['attachmentRecodeLineEndings']) && !isset($_REQUEST['image']) && in_array($file_ext, array('txt', 'css', 'htm', 'html', 'php', 'xml')))
	{
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'Windows') !== false)
			$callback = create_function('$buffer', 'return preg_replace(\'~[\r]?\n~\', "\r\n", $buffer);');
		elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Mac') !== false)
			$callback = create_function('$buffer', 'return preg_replace(\'~[\r]?\n~\', "\r", $buffer);');
		else
			$callback = create_function('$buffer', 'return preg_replace(\'~[\r]?\n~\', "\n", $buffer);');
	}

	// Since we don't do output compression for files this large...
	if (filesize($filename) > 4194304)
	{
		// Forcibly end any output buffering going on.
		if (function_exists('ob_get_level'))
		{
			while (@ob_get_level() > 0)
				@ob_end_clean();
		}
		else
		{
			@ob_end_clean();
			@ob_end_clean();
			@ob_end_clean();
		}

		$fp = fopen($filename, 'rb');
		while (!feof($fp))
		{
			if (isset($callback))
				echo $callback(fread($fp, 8192));
			else
				echo fread($fp, 8192);
			flush();
		}
		fclose($fp);
	}
	// On some of the less-bright hosts, readfile() is disabled.  It's just a faster, more byte safe, version of what's in the if.
	elseif (isset($callback) || @readfile($filename) == null)
		echo isset($callback) ? $callback(file_get_contents($filename)) : file_get_contents($filename);

	obExit(false);
}

function loadAttachmentContext($id_msg)
{
	global $attachments, $modSettings, $txt, $scripturl, $topic, $sourcedir, $backend_subdir;

	// Set up the attachment info - based on code by Meriadoc.
	$attachmentData = array();
	$have_unapproved = false;
	if (isset($attachments[$id_msg]) && !empty($modSettings['attachmentEnable']))
	{
		foreach ($attachments[$id_msg] as $i => $attachment)
		{
			$attachmentData[$i] = array(
				'id' => $attachment['id_attach'],
				'name' => preg_replace('~&amp;#(\\d{1,7}|x[0-9a-fA-F]{1,6});~', '&#\\1;', htmlspecialchars($attachment['filename'])),
				'downloads' => $attachment['downloads'],
				'size' => round($attachment['filesize'] / 1024, 2) . ' ' . $txt['kilobyte'],
				'byte_size' => $attachment['filesize'],
				'href' => $scripturl . '?action=dlattach;topic=' . $topic . '.0;attach=' . $attachment['id_attach'],
				'link' => '<a href="' . $scripturl . '?action=dlattach;topic=' . $topic . '.0;attach=' . $attachment['id_attach'] . '">' . htmlspecialchars($attachment['filename']) . '</a>',
				'is_image' => !empty($attachment['width']) && !empty($attachment['height']) && !empty($modSettings['attachmentShowImages']),
				'is_approved' => $attachment['approved'],
			);

			// If something is unapproved we'll note it so we can sort them.
			if (!$attachment['approved'])
				$have_unapproved = true;

			if (!$attachmentData[$i]['is_image'])
				continue;

			$attachmentData[$i]['real_width'] = $attachment['width'];
			$attachmentData[$i]['width'] = $attachment['width'];
			$attachmentData[$i]['real_height'] = $attachment['height'];
			$attachmentData[$i]['height'] = $attachment['height'];

			// Let's see, do we want thumbs?
			if (!empty($modSettings['attachmentThumbnails']) && !empty($modSettings['attachmentThumbWidth']) && !empty($modSettings['attachmentThumbHeight']) && ($attachment['width'] > $modSettings['attachmentThumbWidth'] || $attachment['height'] > $modSettings['attachmentThumbHeight']) && strlen($attachment['filename']) < 249)
			{
				// A proper thumb doesn't exist yet? Create one!
				if (empty($attachment['id_thumb']) || $attachment['thumb_width'] > $modSettings['attachmentThumbWidth'] || $attachment['thumb_height'] > $modSettings['attachmentThumbHeight'] || ($attachment['thumb_width'] < $modSettings['attachmentThumbWidth'] && $attachment['thumb_height'] < $modSettings['attachmentThumbHeight']))
				{
					$filename = getAttachmentFilename($attachment['filename'], $attachment['id_attach'], $attachment['id_folder']);

					require_once($sourcedir . '/lib/Subs-Graphics.php');
					if (createThumbnail($filename, $modSettings['attachmentThumbWidth'], $modSettings['attachmentThumbHeight']))
					{
						// So what folder are we putting this image in?
						if (!empty($modSettings['currentAttachmentUploadDir']))
						{
							if (!is_array($modSettings['attachmentUploadDir']))
								$modSettings['attachmentUploadDir'] = @unserialize($modSettings['attachmentUploadDir']);
							$path = $modSettings['attachmentUploadDir'][$modSettings['currentAttachmentUploadDir']];
							$id_folder_thumb = $modSettings['currentAttachmentUploadDir'];
						}
						else
						{
							$path = $modSettings['attachmentUploadDir'];
							$id_folder_thumb = 1;
						}

						// Calculate the size of the created thumbnail.
						$size = @getimagesize($filename . '_thumb');
						list ($attachment['thumb_width'], $attachment['thumb_height']) = $size;
						$thumb_size = filesize($filename . '_thumb');

						// These are the only valid image types for SMF.
						$validImageTypes = array(1 => 'gif', 2 => 'jpeg', 3 => 'png', 5 => 'psd', 6 => 'bmp', 7 => 'tiff', 8 => 'tiff', 9 => 'jpeg', 14 => 'iff');

						// What about the extension?
						$thumb_ext = isset($validImageTypes[$size[2]]) ? $validImageTypes[$size[2]] : '';

						// Figure out the mime type.
						if (!empty($size['mime']))
							$thumb_mime = $size['mime'];
						else
							$thumb_mime = 'image/' . $thumb_ext;

						$thumb_filename = $attachment['filename'] . '_thumb';
						$thumb_hash = getAttachmentFilename($thumb_filename, false, null, true);

						// Add this beauty to the database.
						smf_db_insert('',
							'{db_prefix}attachments',
							array('id_folder' => 'int', 'id_msg' => 'int', 'attachment_type' => 'int', 'filename' => 'string', 'file_hash' => 'string', 'size' => 'int', 'width' => 'int', 'height' => 'int', 'fileext' => 'string', 'mime_type' => 'string'),
							array($id_folder_thumb, $id_msg, 3, $thumb_filename, $thumb_hash, (int) $thumb_size, (int) $attachment['thumb_width'], (int) $attachment['thumb_height'], $thumb_ext, $thumb_mime),
							array('id_attach')
						);
						$old_id_thumb = $attachment['id_thumb'];
						$attachment['id_thumb'] = smf_db_insert_id('{db_prefix}attachments', 'id_attach');
						if (!empty($attachment['id_thumb']))
						{
							smf_db_query( '
								UPDATE {db_prefix}attachments
								SET id_thumb = {int:id_thumb}
								WHERE id_attach = {int:id_attach}',
								array(
									'id_thumb' => $attachment['id_thumb'],
									'id_attach' => $attachment['id_attach'],
								)
							);

							$thumb_realname = getAttachmentFilename($thumb_filename, $attachment['id_thumb'], $id_folder_thumb, false, $thumb_hash);
							rename($filename . '_thumb', $thumb_realname);

							// Do we need to remove an old thumbnail?
							if (!empty($old_id_thumb))
							{
								require_once($sourcedir . '/lib/Subs-ManageAttachments.php');
								removeAttachments(array('id_attach' => $old_id_thumb), '', false, false);
							}
						}
					}
				}

				// Only adjust dimensions on successful thumbnail creation.
				if (!empty($attachment['thumb_width']) && !empty($attachment['thumb_height']))
				{
					$attachmentData[$i]['width'] = $attachment['thumb_width'];
					$attachmentData[$i]['height'] = $attachment['thumb_height'];
				}
			}

			if (!empty($attachment['id_thumb']))
				$attachmentData[$i]['thumbnail'] = array(
					'id' => $attachment['id_thumb'],
					'href' => $scripturl . '?action=dlattach;topic=' . $topic . '.0;attach=' . $attachment['id_thumb'] . ';image',
				);
			$attachmentData[$i]['thumbnail']['has_thumb'] = !empty($attachment['id_thumb']);

			// If thumbnails are disabled, check the maximum size of the image.
			if (!$attachmentData[$i]['thumbnail']['has_thumb'] && ((!empty($modSettings['max_image_width']) && $attachment['width'] > $modSettings['max_image_width']) || (!empty($modSettings['max_image_height']) && $attachment['height'] > $modSettings['max_image_height'])))
			{
				if (!empty($modSettings['max_image_width']) && (empty($modSettings['max_image_height']) || $attachment['height'] * $modSettings['max_image_width'] / $attachment['width'] <= $modSettings['max_image_height']))
				{
					$attachmentData[$i]['width'] = $modSettings['max_image_width'];
					$attachmentData[$i]['height'] = floor($attachment['height'] * $modSettings['max_image_width'] / $attachment['width']);
				}
				elseif (!empty($modSettings['max_image_width']))
				{
					$attachmentData[$i]['width'] = floor($attachment['width'] * $modSettings['max_image_height'] / $attachment['height']);
					$attachmentData[$i]['height'] = $modSettings['max_image_height'];
				}
			}
			elseif ($attachmentData[$i]['thumbnail']['has_thumb'])
			{
				// If the image is too large to show inline, make it a popup.
				if (((!empty($modSettings['max_image_width']) && $attachmentData[$i]['real_width'] > $modSettings['max_image_width']) || (!empty($modSettings['max_image_height']) && $attachmentData[$i]['real_height'] > $modSettings['max_image_height'])))
					$attachmentData[$i]['thumbnail']['javascript'] = 'return reqWin(\'' . $attachmentData[$i]['href'] . ';image\', ' . ($attachment['width'] + 20) . ', ' . ($attachment['height'] + 20) . ', true);';
				else
					$attachmentData[$i]['thumbnail']['javascript'] = 'return expandThumb(' . $attachment['id_attach'] . ');';
			}

			if (!$attachmentData[$i]['thumbnail']['has_thumb'])
				$attachmentData[$i]['downloads']++;
		}
		// sort images to the top
		usort($attachmentData, 'sort_by_type');
	}

	// Do we need to instigate a sort?
	if ($have_unapproved)
		usort($attachmentData, 'approved_attach_sort');

	return $attachmentData;
}

// sort images to the top of the list
function sort_by_type($a, $b)
{
	if($a['is_image'] == $b['is_image'])
		return(0);
	
	return($a['is_image'] > $b['is_image']) ? -1 : 1;
}

// A sort function for putting unapproved attachments first.
function approved_attach_sort($a, $b)
{
	if ($a['is_approved'] == $b['is_approved'])
		return 0;

	return $a['is_approved'] > $b['is_approved'] ? -1 : 1;
}

// In-topic quick moderation.
function QuickInTopicModeration()
{
	global $sourcedir, $topic, $board, $user_info, $modSettings, $context;

	// Check the session = get or post.
	checkSession('request');

	require_once($sourcedir . '/RemoveTopic.php');

	if (empty($_REQUEST['msgs']))
		redirectexit('topic=' . $topic . '.' . $_REQUEST['start']);

	$messages = array();
	foreach ($_REQUEST['msgs'] as $dummy)
		$messages[] = (int) $dummy;

	// We are restoring messages. We handle this in another place.
	if (isset($_REQUEST['restore_selected']))
		redirectexit('action=restoretopic;msgs=' . implode(',', $messages) . ';' . $context['session_var'] . '=' . $context['session_id']);

	// Allowed to delete any message?
	if (allowedTo('delete_any'))
		$allowed_all = true;
	// Allowed to delete replies to their messages?
	elseif (allowedTo('delete_replies'))
	{
		$request = smf_db_query( '
			SELECT id_member_started
			FROM {db_prefix}topics
			WHERE id_topic = {int:current_topic}
			LIMIT 1',
			array(
				'current_topic' => $topic,
			)
		);
		list ($starter) = mysql_fetch_row($request);
		mysql_free_result($request);

		$allowed_all = $starter == $user_info['id'];
	}
	else
		$allowed_all = false;

	// Make sure they're allowed to delete their own messages, if not any.
	if (!$allowed_all)
		isAllowedTo('delete_own');

	// Allowed to remove which messages?
	$request = smf_db_query( '
		SELECT id_msg, subject, id_member, poster_time
		FROM {db_prefix}messages
		WHERE id_msg IN ({array_int:message_list})
			AND id_topic = {int:current_topic}' . (!$allowed_all ? '
			AND id_member = {int:current_member}' : '') . '
		LIMIT ' . count($messages),
		array(
			'current_member' => $user_info['id'],
			'current_topic' => $topic,
			'message_list' => $messages,
		)
	);
	$messages = array();
	while ($row = mysql_fetch_assoc($request))
	{
		if (!$allowed_all && !empty($modSettings['edit_disable_time']) && $row['poster_time'] + $modSettings['edit_disable_time'] * 60 < time())
			continue;

		$messages[$row['id_msg']] = array($row['subject'], $row['id_member']);
	}
	mysql_free_result($request);

	// Get the first message in the topic - because you can't delete that!
	$request = smf_db_query( '
		SELECT id_first_msg, id_last_msg
		FROM {db_prefix}topics
		WHERE id_topic = {int:current_topic}
		LIMIT 1',
		array(
			'current_topic' => $topic,
		)
	);
	list ($first_message, $last_message) = mysql_fetch_row($request);
	mysql_free_result($request);

	// Delete all the messages we know they can delete. ($messages)
	foreach ($messages as $message => $info)
	{
		// Just skip the first message - if it's not the last.
		if ($message == $first_message && $message != $last_message)
			continue;
		// If the first message is going then don't bother going back to the topic as we're effectively deleting it.
		elseif ($message == $first_message)
			$topicGone = true;

		removeMessage($message);

		// Log this moderation action ;).
		if (allowedTo('delete_any') && (!allowedTo('delete_own') || $info[1] != $user_info['id']))
			logAction('delete', array('topic' => $topic, 'subject' => $info[0], 'member' => $info[1], 'board' => $board));
	}

	redirectexit(!empty($topicGone) ? 'board=' . $board : 'topic=' . $topic . '.' . $_REQUEST['start']);
}
/**
 * process a link (URL BBC tag) when link security is enabled on the forum.
 * check the posting member's permission and redirect to the target if he's allowed to post direct links
 * otherwise just show a (non-clickable) link to the reader, so the reader can decide whether
 * he wants to visit it.
 */
function ProcessLink()
{
	global $context, $boardurl, $modSettings;

	$referrer_checked = (isset($_SERVER['HTTP_REFERER']) && substr($_SERVER['HTTP_REFERER'], 0, strlen($boardurl)) == $boardurl) ? true : false;
	$mid = isset($_REQUEST['m']) ? $_REQUEST['m'] : 0;
	$target = isset($_REQUEST['target']) ? $_REQUEST['target'] : '';
	$poster = $bid = 0;

	if(empty($modSettings['linkSecurity']) && !empty($target))
		redirectexit($target);
	else if(empty($target))
		fatal_lang_error('invalid_or_missing_link');

	if(substr($mid, 0, 3) == 'sig') {			// a signature, but verify if the member is still in our db
		$_p = intval(substr($mid, 3));
		$request = smf_db_query('SELECT id_member FROM {db_prefix}members WHERE id_member = {int:id_member}', array('id_member' => $_p));
		if(mysql_num_rows($request) > 0)
			list($poster) = mysql_fetch_row($request);
		mysql_free_result($request);
		if($referrer_checked && $poster && !empty($target)) {		// check if the user still exists
			if(isUserAllowedTo('post_links', null, $poster))
				redirectexit($target);
		}
	}
	else if(substr($mid, 0, 2) == 'pm') {
		$_p = intval(substr($mid, 2));
		$request = smf_db_query('SELECT id_member_from FROM {db_prefix}personal_messages WHERE id_pm = {int:id_pm}', array('id_pm' => $_p));
		if(mysql_num_rows($request) > 0)
			list($poster) = mysql_fetch_row($request);
		mysql_free_result($request);
		if($referrer_checked && $poster && !empty($target)) {
			if(isUserAllowedTo('post_links', null, $poster))
				redirectExit($target);
		}
	}
	else if ((int)$mid === 0 && !empty($target))
		redirectexit($target);
	else {
		$request = smf_db_query('SELECT mem.id_member, b.id_board FROM {db_prefix}messages AS m
	    	LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = m.id_member)
			LEFT JOIN {db_prefix}topics AS t ON (t.id_topic = m.id_topic)
		    LEFT JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
			WHERE id_msg = {int:id_msg}', array('id_msg' => (int)$mid));
		if(mysql_num_rows($request) > 0)
			list($poster, $bid) = mysql_fetch_row($request);
		mysql_free_result($request);
	}
	if($referrer_checked && $poster && $bid && !empty($target)) {		// check if the user still exists
		// it does exist, now check the permissions
		if(isUserAllowedTo('post_links', $bid, $poster))
			redirectexit($target);
	}
	// all attempts failed, now output the page, presenting a plain text link
	loadTemplate('DisplaySingle');
	loadLanguage('Profile');
	$context['sub_template'] = 'processlink';
	$context['target_link'] = $target;
}
?>