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

/*	This file is what shows the listing of topics in a board.  It's just one
	function, but don't under estimate it ;).

	void MessageIndex()
		// !!!

	void QuickModeration()
		// !!!

*/

// Show the list of topics in this board, along with any child boards.
function MessageIndex()
{
	global $txt, $scripturl, $board, $modSettings, $context;
	global $options, $settings, $board_info, $user_info, $smcFunc, $sourcedir;
	global $memberContext;
	
	// If this is a redirection board head off.
	if ($board_info['redirect'])
	{
		smf_db_query( '
			UPDATE {db_prefix}boards
			SET num_posts = num_posts + 1
			WHERE id_board = {int:current_board}',
			array(
				'current_board' => $board,
			)
		);

		redirectexit($board_info['redirect']);
	}
	EoS_Smarty::loadTemplate('messageindex');
	fetchNewsItems($board, 0);
	$context['act_as_cat'] = $board_info['allow_topics'] ? false : true;
	$context['name'] = $board_info['name'];
	$context['description'] = $board_info['description'];
	// How many topics do we have in total?
	$board_info['total_topics'] = allowedTo('approve_posts') ? $board_info['num_topics'] + $board_info['unapproved_topics'] : $board_info['num_topics'] + $board_info['unapproved_user_topics'];

	// View all the topics, or just a few?
	$context['topics_per_page'] = empty($modSettings['disableCustomPerPage']) && !empty($options['topics_per_page']) ? $options['topics_per_page'] : $modSettings['defaultMaxTopics'];
	$context['messages_per_page'] = commonAPI::getMessagesPerPage();
	$maxindex = isset($_REQUEST['all']) && !empty($modSettings['enableAllMessages']) ? $board_info['total_topics'] : $context['topics_per_page'];

	// Right, let's only index normal stuff!
	if (count($_GET) > 1)
	{
		$session_name = session_name();
		foreach ($_GET as $k => $v)
		{
			if (!in_array($k, array('board', 'start', $session_name)))
				$context['robot_no_index'] = true;
		}
	}
	if (!empty($_REQUEST['start']) && (!is_numeric($_REQUEST['start']) || $_REQUEST['start'] % $context['messages_per_page'] != 0))
		$context['robot_no_index'] = true;

	// If we can view unapproved messages and there are some build up a list.
	if (allowedTo('approve_posts') && ($board_info['unapproved_topics'] || $board_info['unapproved_posts']))
	{
		$untopics = $board_info['unapproved_topics'] ? '<a href="' . $scripturl . '?action=moderate;area=postmod;sa=topics;brd=' . $board . '">' . $board_info['unapproved_topics'] . '</a>' : 0;
		$unposts = $board_info['unapproved_posts'] ? '<a href="' . $scripturl . '?action=moderate;area=postmod;sa=posts;brd=' . $board . '">' . ($board_info['unapproved_posts'] - $board_info['unapproved_topics']) . '</a>' : 0;
		$context['unapproved_posts_message'] = sprintf($txt['there_are_unapproved_topics'], $untopics, $unposts, $scripturl . '?action=moderate;area=postmod;sa=' . ($board_info['unapproved_topics'] ? 'topics' : 'posts') . ';brd=' . $board);
	}

	// Make sure the starting place makes sense and construct the page index.
	if (isset($_REQUEST['sort']))
		$context['page_index'] = constructPageIndex(URL::board($board_info['id'], $board_info['name'], '%1$d;sort=' . $_REQUEST['sort'] . (isset($_REQUEST['desc']) ? ';desc' : ''), true), $_REQUEST['start'], $board_info['total_topics'], $maxindex, true);
	else
		//$context['page_index'] = constructPageIndex($scripturl . '?board=' . $board . '.%1$d', $_REQUEST['start'], $board_info['total_topics'], $maxindex, true);
		$context['page_index'] = constructPageIndex(URL::board($board_info['id'], $board_info['name'], '%1$d', true), $_REQUEST['start'], $board_info['total_topics'], $maxindex, true);
	$context['start'] = &$_REQUEST['start'];
	setcookie('smf_topicstart', intval($board) . '_'. $context['start'], time() + 86400, '/');

	// Set a canonical URL for this page.
	$context['canonical_url'] = URL::board($board, $board_info['name'], $context['start'], true);

	$context['links'] = array(
		'first' => $_REQUEST['start'] >= $context['topics_per_page'] ? $scripturl . '?board=' . $board . '.0' : '',
		'prev' => $_REQUEST['start'] >= $context['topics_per_page'] ? $scripturl . '?board=' . $board . '.' . ($_REQUEST['start'] - $context['topics_per_page']) : '',
		'next' => $_REQUEST['start'] + $context['topics_per_page'] < $board_info['total_topics'] ? $scripturl . '?board=' . $board . '.' . ($_REQUEST['start'] + $context['topics_per_page']) : '',
		'last' => $_REQUEST['start'] + $context['topics_per_page'] < $board_info['total_topics'] ? $scripturl . '?board=' . $board . '.' . (floor(($board_info['total_topics'] - 1) / $context['topics_per_page']) * $context['topics_per_page']) : '',
		'up' => $board_info['parent'] == 0 ? $scripturl . '?' : $scripturl . '?board=' . $board_info['parent'] . '.0'
	);

	$context['page_info'] = array(
		'current_page' => $_REQUEST['start'] / $context['topics_per_page'] + 1,
		'num_pages' => floor(($board_info['total_topics'] - 1) / $context['topics_per_page']) + 1
	);

	if (isset($_REQUEST['all']) && !empty($modSettings['enableAllMessages']) && $maxindex > $modSettings['enableAllMessages'])
	{
		$maxindex = $modSettings['enableAllMessages'];
		$_REQUEST['start'] = 0;
	}

	// Build a list of the board's moderators.
	$context['moderators'] = &$board_info['moderators'];
	$context['link_moderators'] = array();
	if (!empty($board_info['moderators']))
	{
		foreach ($board_info['moderators'] as $mod)
			$context['link_moderators'][] ='<a href="' . $scripturl . '?action=profile;u=' . $mod['id'] . '" title="' . $txt['board_moderator'] . '">' . $mod['name'] . '</a>';

		//$context['linktree'][count($context['linktree']) - 1]['extra_after'] = ' (' . (count($context['link_moderators']) == 1 ? $txt['moderator'] : $txt['moderators']) . ': ' . implode(', ', $context['link_moderators']) . ')';
	}

	// Mark current and parent boards as seen.
	if (!$user_info['is_guest'])
	{
		// We can't know they read it if we allow prefetches.
		if (isset($_SERVER['HTTP_X_MOZ']) && $_SERVER['HTTP_X_MOZ'] == 'prefetch')
		{
			ob_end_clean();
			header('HTTP/1.1 403 Prefetch Forbidden');
			die;
		}

		smf_db_insert('replace',
			'{db_prefix}log_boards',
			array('id_msg' => 'int', 'id_member' => 'int', 'id_board' => 'int'),
			array($modSettings['maxMsgID'], $user_info['id'], $board),
			array('id_member', 'id_board')
		);

		if (!empty($board_info['parent_boards']))
		{
			smf_db_query( '
				UPDATE {db_prefix}log_boards
				SET id_msg = {int:id_msg}
				WHERE id_member = {int:current_member}
					AND id_board IN ({array_int:board_list})',
				array(
					'current_member' => $user_info['id'],
					'board_list' => array_keys($board_info['parent_boards']),
					'id_msg' => $modSettings['maxMsgID'],
				)
			);

			// We've seen all these boards now!
			foreach ($board_info['parent_boards'] as $k => $dummy)
				if (isset($_SESSION['topicseen_cache'][$k]))
					unset($_SESSION['topicseen_cache'][$k]);
		}

		if (isset($_SESSION['topicseen_cache'][$board]))
			unset($_SESSION['topicseen_cache'][$board]);

		$request = smf_db_query( '
			SELECT sent
			FROM {db_prefix}log_notify
			WHERE id_board = {int:current_board}
				AND id_member = {int:current_member}
			LIMIT 1',
			array(
				'current_board' => $board,
				'current_member' => $user_info['id'],
			)
		);
		$context['is_marked_notify'] = mysql_num_rows($request) != 0;
		if ($context['is_marked_notify'])
		{
			list ($sent) = mysql_fetch_row($request);
			if (!empty($sent))
			{
				smf_db_query( '
					UPDATE {db_prefix}log_notify
					SET sent = {int:is_sent}
					WHERE id_board = {int:current_board}
						AND id_member = {int:current_member}',
					array(
						'current_board' => $board,
						'current_member' => $user_info['id'],
						'is_sent' => 0,
					)
				);
			}
		}
		mysql_free_result($request);
	}
	else
		$context['is_marked_notify'] = false;

	// 'Print' the header and board info.
	$context['page_number'] = isset($_REQUEST['start']) ? $_REQUEST['start'] / $context['topics_per_page'] : 0;
	$context['page_title'] = strip_tags($board_info['name'] . ((int)$context['page_number'] > 0 ? ' - ' . $txt['page'] . ' ' . ($context['page_number'] + 1) : ''));
	$context['meta_page_description'] = (!empty($board_info['description']) ? $board_info['description'] : $context['page_title']);
	// Set the variables up for the template.
	$context['can_mark_notify'] = allowedTo('mark_notify') && !$user_info['is_guest'];
	$context['can_post_new'] = allowedTo('post_new') || ($modSettings['postmod_active'] && allowedTo('post_unapproved_topics'));
	$context['can_post_poll'] = $modSettings['pollMode'] == '1' && allowedTo('poll_post') && $context['can_post_new'];
	$context['can_moderate_forum'] = allowedTo('moderate_forum');
	$context['can_approve_posts'] = allowedTo('approve_posts');

	require_once($sourcedir . '/lib/Subs-BoardIndex.php');
	$boardIndexOptions = array(
		'include_categories' => false,
		'base_level' => $board_info['child_level'] + 1,
		'parent_id' => $board_info['id'],
		'set_latest_post' => false,
		'countChildPosts' => !empty($modSettings['countChildPosts']),
	);
	$context['boards'] = getBoardIndex($boardIndexOptions);

	// Nosey, nosey - who's viewing this topic?
	if (!empty($settings['display_who_viewing']))
	{
		$context['view_members'] = array();
		$context['view_members_list'] = array();
		$context['view_num_hidden'] = 0;

		$request = smf_db_query( '
			SELECT
				lo.id_member, lo.log_time, mem.real_name, mem.member_name, mem.show_online, mem.id_group, mem.id_post_group
			FROM {db_prefix}log_online AS lo
				LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = lo.id_member)
			WHERE INSTR(lo.url, {string:in_url_string}) > 0 OR lo.session = {string:session}',
			array(
				'reg_member_group' => 0,
				'in_url_string' => 's:5:"board";i:' . $board . ';',
				'session' => $user_info['is_guest'] ? 'ip' . $user_info['ip'] : session_id(),
			)
		);
		while ($row = mysql_fetch_assoc($request))
		{
			if (empty($row['id_member']))
				continue;

			$class = 'member group_' . (empty($row['id_group']) ? $row['id_post_group'] : $row['id_group']) . (in_array($row['id_member'], $user_info['buddies']) ? ' buddy' : '');
			$href = URL::user($row['id_member'], $row['real_name']);

			if($row['id_member'] == $user_info['id'])
				$link = '<strong>'.$txt['you'].'</strong>';
			else
				$link = '<a attr-mid="'.$row['id_member'].'" class="member '.$class.'" href="' . $href . '">' . $row['real_name'] . '</a>';

			if (!empty($row['show_online']) || allowedTo('moderate_forum'))
				$context['view_members_list'][$row['log_time'] . $row['member_name']] = empty($row['show_online']) ? '<em>' . $link . '</em>' : $link;
			$context['view_members'][$row['log_time'] . $row['member_name']] = array(
				'id' => $row['id_member'],
				'username' => $row['member_name'],
				'name' => $row['real_name'],
				'group' => $row['id_group'],
				'href' => $href,
				'link' => $link,
				'hidden' => empty($row['show_online']),
			);

			if (empty($row['show_online']))
				$context['view_num_hidden']++;
		}
		$context['view_num_guests'] = mysql_num_rows($request) - count($context['view_members']);
		mysql_free_result($request);

		// Put them in "last clicked" order.
		krsort($context['view_members_list']);
		krsort($context['view_members']);

		$context['full_members_viewing_list'] = empty($context['view_members_list']) ? '0 ' . $txt['members'] : implode(', ', $context['view_members_list']) . ((empty($context['view_num_hidden']) or $context['can_moderate_forum']) ? '' : ' (+ ' . $context['view_num_hidden'] . ' ' . $txt['hidden'] . ')');

	}

	// Default sort methods.
	$sort_methods = array(
		'subject' => 'mf.subject',
		'starter' => 'IFNULL(memf.real_name, mf.poster_name)',
		'last_poster' => 'IFNULL(meml.real_name, ml.poster_name)',
		'replies' => 't.num_replies',
		'views' => 't.num_views',
		'first_post' => 't.id_topic',
		'last_post' => 't.id_last_msg'
	);

	// They didn't pick one, default to by last post descending.
	if (!isset($_REQUEST['sort']) || !isset($sort_methods[$_REQUEST['sort']]))
	{
		$context['sort_by'] = 'last_post';
		$_REQUEST['sort'] = 'id_last_msg';
		$ascending = isset($_REQUEST['asc']);
	}
	// Otherwise default to ascending.
	else
	{
		$context['sort_by'] = $_REQUEST['sort'];
		$_REQUEST['sort'] = $sort_methods[$_REQUEST['sort']];
		$ascending = !isset($_REQUEST['desc']);
	}

	$context['sort_direction'] = $ascending ? 'up' : 'down';

	// Calculate the fastest way to get the topics.
	$start = (int) $_REQUEST['start'];
	if ($start > ($board_info['total_topics'] - 1) / 2)
	{
		$ascending = !$ascending;
		$fake_ascending = true;
		$maxindex = $board_info['total_topics'] < $start + $maxindex + 1 ? $board_info['total_topics'] - $start : $maxindex;
		$start = $board_info['total_topics'] < $start + $maxindex + 1 ? 0 : $board_info['total_topics'] - $start - $maxindex;
	}
	else
		$fake_ascending = false;

	$topic_ids = array();
	$context['topics'] = array();

	$prefixid = isset($_REQUEST['prefix']) ? (int)$_REQUEST['prefix'] : 0;
	$prefixfilter = !empty($prefixid) ? 't.id_prefix = {int:id_prefix} AND ' : '';

	// Sequential pages are often not optimized, so we add an additional query.
	$pre_query = $start > 0;
	if ($pre_query && $maxindex > 0)
	{
		$request = smf_db_query( '
			SELECT t.id_topic
			FROM {db_prefix}topics AS t' . ($context['sort_by'] === 'last_poster' ? '
				INNER JOIN {db_prefix}messages AS ml ON (ml.id_msg = t.id_last_msg)' : (in_array($context['sort_by'], array('starter', 'subject')) ? '
				INNER JOIN {db_prefix}messages AS mf ON (mf.id_msg = t.id_first_msg)' : '')) . ($context['sort_by'] === 'starter' ? '
				LEFT JOIN {db_prefix}members AS memf ON (memf.id_member = mf.id_member)' : '') . ($context['sort_by'] === 'last_poster' ? '
				LEFT JOIN {db_prefix}members AS meml ON (meml.id_member = ml.id_member)' : '') . '
			WHERE ' . $prefixfilter . ' t.id_board = {int:current_board}' . (!$modSettings['postmod_active'] || $context['can_approve_posts'] ? '' : '
				AND (t.approved = {int:is_approved}' . ($user_info['is_guest'] ? '' : ' OR t.id_member_started = {int:current_member}') . ')') . '
			ORDER BY ' . (!empty($modSettings['enableStickyTopics']) ? 'is_sticky' . ($fake_ascending ? '' : ' DESC') . ', ' : '') . $_REQUEST['sort'] . ($ascending ? '' : ' DESC') . '
			LIMIT {int:start}, {int:maxindex}',
			array(
				'current_board' => $board,
				'current_member' => $user_info['id'],
				'is_approved' => 1,
				'id_member_guest' => 0,
				'start' => $start,
				'maxindex' => $maxindex,
				'id_prefix' => $prefixid
			)
		);
		$topic_ids = array();
		while ($row = mysql_fetch_assoc($request))
			$topic_ids[] = $row['id_topic'];
	}
	// Grab the appropriate topic information...
	if (!$pre_query || !empty($topic_ids))
	{
		// For search engine effectiveness we'll link guests differently.
		$context['pageindex_multiplier'] = commonAPI::getMessagesPerPage();

		$result = smf_db_query('
			SELECT 
				t.id_topic, t.num_replies, t.locked, t.num_views, t.is_sticky, t.id_poll, t.id_previous_board,
				' . ($user_info['is_guest'] ? '0' : 'IFNULL(lt.id_msg, IFNULL(lmr.id_msg, -1)) + 1') . ' AS new_from,
				t.id_last_msg, t.approved, t.unapproved_posts, t.id_prefix, ml.poster_time AS last_poster_time,
				ml.id_msg_modified, ml.subject AS last_subject, ml.icon AS last_icon,
				ml.poster_name AS last_member_name, ml.id_member AS last_id_member,
				IFNULL(meml.real_name, ml.poster_name) AS last_display_name, t.id_first_msg,
				mf.poster_time AS first_poster_time, mf.subject AS first_subject, mf.icon AS first_icon,
				mf.poster_name AS first_member_name, mf.id_member AS first_id_member,
				IFNULL(memf.real_name, mf.poster_name) AS first_display_name,
				ml.smileys_enabled AS last_smileys, mf.smileys_enabled AS first_smileys,
				p.name AS prefix_name
			FROM {db_prefix}topics AS t	
				INNER JOIN {db_prefix}messages AS ml ON (ml.id_msg = t.id_last_msg)
				INNER JOIN {db_prefix}messages AS mf ON (mf.id_msg = t.id_first_msg)
				LEFT JOIN {db_prefix}members AS meml ON (meml.id_member = ml.id_member)
				LEFT JOIN {db_prefix}members AS memf ON (memf.id_member = mf.id_member)' . ($user_info['is_guest'] ? '' : '
				LEFT JOIN {db_prefix}log_topics AS lt ON (lt.id_topic = t.id_topic AND lt.id_member = {int:current_member})
				LEFT JOIN {db_prefix}log_mark_read AS lmr ON (lmr.id_board = {int:current_board} AND lmr.id_member = {int:current_member})'). '
				LEFT JOIN {db_prefix}prefixes AS p ON p.id_prefix = t.id_prefix 
			WHERE ' . $prefixfilter . ($pre_query ? 't.id_topic IN ({array_int:topic_list})' : 't.id_board = {int:current_board}') . (!$modSettings['postmod_active'] || $context['can_approve_posts'] ? '' : '
				AND (t.approved = {int:is_approved}' . ($user_info['is_guest'] ? '' : ' OR t.id_member_started = {int:current_member}') . ')') . '
			ORDER BY ' . ($pre_query ? 'FIND_IN_SET(t.id_topic, {string:find_set_topics})' : (!empty($modSettings['enableStickyTopics']) ? 'is_sticky' . ($fake_ascending ? '' : ' DESC') . ', ' : '') . $_REQUEST['sort'] . ($ascending ? '' : ' DESC')) . '
			LIMIT ' . ($pre_query ? '' : '{int:start}, ') . '{int:maxindex}',
			array(
				'current_board' => $board,
				'current_member' => $user_info['id'],
				'topic_list' => $topic_ids,
				'is_approved' => 1,
				'find_set_topics' => implode(',', $topic_ids),
				'start' => $start,
				'maxindex' => $maxindex,
				'id_prefix' => $prefixid
			)
		);

		// Begin 'printing' the message index for current board.
		$first_posters = array();
		while ($row = mysql_fetch_assoc($result))
		{
			if ($row['id_poll'] > 0 && $modSettings['pollMode'] == '0')
				continue;

			if (!$pre_query)
				$topic_ids[] = $row['id_topic'];

			$row['first_body'] = '';
			$row['last_body'] = '';
			censorText($row['first_subject']);

			if ($row['id_first_msg'] == $row['id_last_msg'])
				$row['last_subject'] = $row['first_subject'];
			else
				censorText($row['last_subject']);


			// Decide how many pages the topic should have.
			if ($row['num_replies'] + 1 > $context['messages_per_page'])
			{
				$pages = '&nbsp;&nbsp;';

				// We can't pass start by reference.
				$start = -1;
				$pages .= constructPageIndex(URL::topic($row['id_topic'], $row['first_subject'], '%1$d'), $start, $row['num_replies'] + 1, $context['messages_per_page'], true, false, true);

				// If we can use all, show all.
				if (!empty($modSettings['enableAllMessages']) && $row['num_replies'] + 1 < $modSettings['enableAllMessages'])
					$pages .= '<a class="navPages compact" href="' . URL::topic($row['id_topic'], $row['first_subject'], 0) .';all">' . $txt['show_all'] . '</a>';
				$pages .= ' ';
			}
			else
				$pages = '';

            $first_posters[$row['id_topic']] = $row['first_id_member'];
			// 'Print' the topic info.
			$f_post_mem_href = !empty($row['first_id_member']) ? URL::user($row['first_id_member'], $row['first_display_name']) : '';
			$t_href = URL::topic($row['id_topic'], $row['first_subject'], 0);
			$l_post_mem_href = !empty($row['last_id_member']) ? URL::user($row['last_id_member'], $row['last_display_name'] ) : '';
			$l_post_msg_href = URL::topic($row['id_topic'], $row['last_subject'], $user_info['is_guest'] ? (!empty($options['view_newest_first']) ? 0 : ((int) (($row['num_replies']) / $context['pageindex_multiplier'])) * $context['pageindex_multiplier']) : 0, $user_info['is_guest'] ? true : false, $user_info['is_guest'] ? '' : ('.msg' . $row['id_last_msg']), $user_info['is_guest'] ? ('#msg' . $row['id_last_msg']) : '#new');
			$context['topics'][$row['id_topic']] = array(
				'id' => $row['id_topic'],
				'first_post' => array(
					'id' => $row['id_first_msg'],
					'member' => array(
						'username' => $row['first_member_name'],
						'name' => $row['first_display_name'],
						'id' => $row['first_id_member'],
						'href' => $f_post_mem_href,
						'link' => !empty($row['first_id_member']) ? '<a class="member" attr-mid="'.$row['first_id_member'].'" href="' . $f_post_mem_href . '" title="' . $txt['profile_of'] . ' ' . $row['first_display_name'] . '">' . $row['first_display_name'] . '</a>' : $row['first_display_name'],
					),
					'time' => timeformat($row['first_poster_time']),
					'timestamp' => forum_time(true, $row['first_poster_time']),
					'subject' => $row['first_subject'],
					'icon' => $row['first_icon'],
					'icon_url' => getPostIcon($row['first_icon']),
					'href' => $t_href,
					'link' => '<a href="' . $t_href .'">' . $row['first_subject'] . '</a>'
				),
				'last_post' => array(
					'id' => $row['id_last_msg'],
					'member' => array(
						'username' => $row['last_member_name'],
						'name' => $row['last_display_name'],
						'id' => $row['last_id_member'],
						'href' => $l_post_mem_href,
						'link' => !empty($row['last_id_member']) ? '<a class="member" attr-mid="'.$row['last_id_member'].'" href="' . $l_post_mem_href . '">' . $row['last_display_name'] . '</a>' : $row['last_display_name']
					),
					'time' => timeformat($row['last_poster_time']),
					'timestamp' => forum_time(true, $row['last_poster_time']),
					'subject' => $row['last_subject'],
					'icon' => $row['last_icon'],
					'icon_url' => getPostIcon($row['last_icon']),
					'href' => $l_post_msg_href,
					'link' => '<a href="' . $l_post_msg_href . ($row['num_replies'] == 0 ? '' : ' rel="nofollow"') . '>' . $row['last_subject'] . '</a>'
				),
				'prefix' => $row['prefix_name'] ? '<a href="' . $scripturl . '?board=' . $board . ';prefix=' . $row['id_prefix'] . '" class="prefix">'.(html_entity_decode($row['prefix_name']) . '</a>') : '',
				'is_sticky' => !empty($modSettings['enableStickyTopics']) && !empty($row['is_sticky']),
				'is_locked' => !empty($row['locked']),
				'is_poll' => $modSettings['pollMode'] == '1' && $row['id_poll'] > 0,
				'is_hot' => $row['num_replies'] >= $modSettings['hotTopicPosts'],
				'is_very_hot' => $row['num_replies'] >= $modSettings['hotTopicVeryPosts'],
				'is_posted_in' => false,
				'is_old' => !empty($modSettings['oldTopicDays']) ? (($context['time_now'] - $row['last_poster_time']) > ($modSettings['oldTopicDays'] * 86400)) : false,
				'subject' => $row['first_subject'],
				'new' => $row['new_from'] <= $row['id_msg_modified'],
				'new_from' => $row['new_from'],
				'newtime' => $row['new_from'],
				'new_href' => URL::topic($row['id_topic'], $row['first_subject'], 0, false, '.msg' . $row['new_from'], '#new'),
				'pages' => $pages,
				'replies' => comma_format($row['num_replies']),
				'views' => comma_format($row['num_views']),
				'approved' => $row['approved'],
				'unapproved_posts' => $row['unapproved_posts'],
			);
			determineTopicClass($context['topics'][$row['id_topic']]);
			
			if(!empty($context['topics'][$row['id_topic']]['prefix']))
				$context['topics'][$row['id_topic']]['prefix'] .= '&nbsp;';
		}
		if (!empty($settings['show_user_images']) && empty($options['show_no_avatars'])) {
			$all_posters = array_unique($first_posters);
			loadMemberData($all_posters);
			foreach($context['topics'] as &$_topic) {
				if(!isset($memberContext[$first_posters[$_topic['id']]]))
					loadMemberContext($first_posters[$_topic['id']], true);
				if(isset($memberContext[$first_posters[$_topic['id']]]['avatar']['image']))
					$_topic['first_post']['member']['avatar'] = &$memberContext[$first_posters[$_topic['id']]]['avatar']['image'];
			}
		}
		mysql_free_result($result);

		// Fix the sequence of topics if they were retrieved in the wrong order. (for speed reasons...)
		if ($fake_ascending)
			$context['topics'] = array_reverse($context['topics'], true);

		if (!empty($modSettings['enableParticipation']) && !$user_info['is_guest'] && !empty($topic_ids))
		{
			$result = smf_db_query( '
				SELECT id_topic
				FROM {db_prefix}messages
				WHERE id_topic IN ({array_int:topic_list})
					AND id_member = {int:current_member}
				GROUP BY id_topic
				LIMIT ' . count($topic_ids),
				array(
					'current_member' => $user_info['id'],
					'topic_list' => $topic_ids,
				)
			);
			while ($row = mysql_fetch_assoc($result)) {
				if($context['topics'][$row['id_topic']]['first_post']['member']['id'] != $user_info['id'])
				$context['topics'][$row['id_topic']]['is_posted_in'] = true;
			}
			mysql_free_result($result);
		}
	}

	$context['jump_to'] = array(
		'label' => addslashes(un_htmlspecialchars($txt['jump_to'])),
		'board_name' => htmlspecialchars(strtr(strip_tags($board_info['name']), array('&amp;' => '&'))),
		'child_level' => $board_info['child_level'],
	);

	// Is Quick Moderation active/needed?
	if (!empty($options['display_quick_mod']) && !empty($context['topics']))
	{
		$context['can_lock'] = allowedTo('lock_any');
		$context['can_sticky'] = allowedTo('make_sticky') && !empty($modSettings['enableStickyTopics']);
		$context['can_move'] = allowedTo('move_any');
		$context['can_remove'] = allowedTo('remove_any');
		$context['can_merge'] = allowedTo('merge_any');
		// Ignore approving own topics as it's unlikely to come up...
		$context['can_approve'] = $modSettings['postmod_active'] && allowedTo('approve_posts') && !empty($board_info['unapproved_topics']);
		// Can we restore topics?
		$context['can_restore'] = allowedTo('move_any') && !empty($modSettings['recycle_enable']) && $modSettings['recycle_board'] == $board;

		// Set permissions for all the topics.
		foreach ($context['topics'] as $t => $topic)
		{
			$started = $topic['first_post']['member']['id'] == $user_info['id'];
			$context['topics'][$t]['quick_mod'] = array(
				'lock' => allowedTo('lock_any') || ($started && allowedTo('lock_own')),
				'sticky' => allowedTo('make_sticky') && !empty($modSettings['enableStickyTopics']),
				'move' => allowedTo('move_any') || ($started && allowedTo('move_own')),
				'modify' => allowedTo('modify_any') || ($started && allowedTo('modify_own')),
				'remove' => allowedTo('remove_any') || ($started && allowedTo('remove_own')),
				'approve' => $context['can_approve'] && $topic['unapproved_posts']
			);
			$context['can_lock'] |= ($started && allowedTo('lock_own'));
			$context['can_move'] |= ($started && allowedTo('move_own'));
			$context['can_remove'] |= ($started && allowedTo('remove_own'));
		}

		// Find the boards/cateogories they can move their topic to.
		if ($options['display_quick_mod'] && $context['can_move'] && !empty($context['topics']))
		{
			require_once($sourcedir . '/lib/Subs-MessageIndex.php');
			$boardListOptions = array(
				'excluded_boards' => array($board),
				'not_redirection' => true,
				'use_permissions' => true,
				'selected_board' => empty($_SESSION['move_to_topic']) ? null : $_SESSION['move_to_topic'],
			);
			$context['move_to_boards'] = getBoardList($boardListOptions);

			// Make the boards safe for display.
			foreach ($context['move_to_boards'] as $id_cat => $cat)
			{
				$context['move_to_boards'][$id_cat]['name'] = strip_tags($cat['name']);
				foreach ($cat['boards'] as $id_board => $board)
					$context['move_to_boards'][$id_cat]['boards'][$id_board]['name'] = strip_tags($board['name']);
			}

			// With no other boards to see, it's useless to move.
			if (empty($context['move_to_boards']))
				$context['can_move'] = false;
		}
		// Can we use quick moderation checkboxes?
		if ($options['display_quick_mod'])
			$context['can_quick_mod'] = $context['user']['is_logged'] || $context['can_approve'] || $context['can_remove'] || $context['can_lock'] || $context['can_sticky'] || $context['can_move'] || $context['can_merge'] || $context['can_restore'];
	}

	// If there are children, but no topics and no ability to post topics...
	$context['no_topic_listing'] = !empty($context['boards']) && empty($context['topics']) && !$context['can_post_new'];


	$context['normal_buttons'] = array(
		'new_topic' => array('test' => 'can_post_new', 'text' => 'new_topic', 'image' => 'new_topic.gif', 'lang' => true, 'url' => $scripturl . '?action=post;board=' . $context['current_board'] . '.0', 'active' => true),
		'post_poll' => array('test' => 'can_post_poll', 'text' => 'new_poll', 'image' => 'new_poll.gif', 'lang' => true, 'url' => $scripturl . '?action=post;board=' . $context['current_board'] . '.0;poll'),
		'notify' => array('test' => 'can_mark_notify', 'text' => $context['is_marked_notify'] ? 'unnotify' : 'notify', 'image' => ($context['is_marked_notify'] ? 'un' : ''). 'notify.gif', 'lang' => true, 'custom' => 'onclick="return confirm(\'' . ($context['is_marked_notify'] ? $txt['notification_disable_board'] : $txt['notification_enable_board']) . '\');"', 'url' => $scripturl . '?action=notifyboard;sa=' . ($context['is_marked_notify'] ? 'off' : 'on') . ';board=' . $context['current_board'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'markread' => array('text' => 'mark_read_short', 'image' => 'markread.gif', 'lang' => true, 'url' => $scripturl . '?action=markasread;sa=board;board=' . $context['current_board'] . '.0;' . $context['session_var'] . '=' . $context['session_id']),
	);

	if(!empty($context['topics'])) {
		$context['subject_sort_header'] = '<a rel="nofollow" href="' . $scripturl . '?board=' . $context['current_board'] . '.' . $context['start'] . ';sort=subject' . ($context['sort_by'] == 'subject' && $context['sort_direction'] == 'up' ? ';desc' : '') . '">' . $txt['subject'] . ($context['sort_by'] == 'subject' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '') . '</a> / <a rel="nofollow" href="' . $scripturl . '?board=' . $context['current_board'] . '.' . $context['start'] . ';sort=starter' . ($context['sort_by'] == 'starter' && $context['sort_direction'] == 'up' ? ';desc' : '') . '">' . $txt['started_by'] . ($context['sort_by'] == 'starter' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '') . '</a>';
		$context['views_sort_header'] = '<a rel="nofollow" href="' . $scripturl . '?board=' . $context['current_board'] . '.' . $context['start'] . ';sort=replies' . ($context['sort_by'] == 'replies' && $context['sort_direction'] == 'up' ? ';desc' : '') . '">' . $txt['replies'] . ($context['sort_by'] == 'replies' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '') . '</a> / <a href="' . $scripturl . '?board=' . $context['current_board'] . '.' . $context['start'] . ';sort=views' . ($context['sort_by'] == 'views' && $context['sort_direction'] == 'up' ? ';desc' : '') . '">' . $txt['views'] . ($context['sort_by'] == 'views' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '') . '</a>';
		$context['lastpost_sort_header'] = '<a rel="nofollow" href="'. $scripturl . '?board=' . $context['current_board'] . '.' . $context['start'] . ';sort=last_post' . ($context['sort_by'] == 'last_post' && $context['sort_direction'] == 'up' ? ';desc' : '') . '">' . $txt['last_post'] . ($context['sort_by'] == 'last_post' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '') . '</a>';

	}
	// They can only mark read if they are logged in and it's enabled!
	if (!$context['user']['is_logged'] || !$settings['show_mark_read'])
		unset($context['normal_buttons']['markread']);
	HookAPI::callHook('messageindex_buttons', array(&$normal_buttons));

	enqueueThemeScript('topic', 'scripts/topic.js', true);
	HookAPI::callHook('messageindex', array(&$board_info));
}

// Allows for moderation from the message index.
function QuickModeration()
{
	global $sourcedir, $board, $user_info, $modSettings, $sourcedir, $smcFunc, $context;

	// Check the session = get or post.
	checkSession('request');

	// Lets go straight to the restore area.
	if (isset($_REQUEST['qaction']) && $_REQUEST['qaction'] == 'restore' && !empty($_REQUEST['topics']))
		redirectexit('action=restoretopic;topics=' . implode(',', $_REQUEST['topics']) . ';' . $context['session_var'] . '=' . $context['session_id']);

	if (isset($_SESSION['topicseen_cache']))
		$_SESSION['topicseen_cache'] = array();

	// This is going to be needed to send off the notifications and for updateLastMessages().
	require_once($sourcedir . '/lib/Subs-Post.php');

	// Remember the last board they moved things to.
	if (isset($_REQUEST['move_to']))
		$_SESSION['move_to_topic'] = $_REQUEST['move_to'];

	// Only a few possible actions.
	$possibleActions = array();

	if (!empty($board))
	{
		$boards_can = array(
			'make_sticky' => allowedTo('make_sticky') ? array($board) : array(),
			'move_any' => allowedTo('move_any') ? array($board) : array(),
			'move_own' => allowedTo('move_own') ? array($board) : array(),
			'remove_any' => allowedTo('remove_any') ? array($board) : array(),
			'remove_own' => allowedTo('remove_own') ? array($board) : array(),
			'lock_any' => allowedTo('lock_any') ? array($board) : array(),
			'lock_own' => allowedTo('lock_own') ? array($board) : array(),
			'merge_any' => allowedTo('merge_any') ? array($board) : array(),
			'approve_posts' => allowedTo('approve_posts') ? array($board) : array(),
		);

		$redirect_url = 'board=' . $board . '.' . $_REQUEST['start'];
	}
	else
	{
		// !!! Ugly.  There's no getting around this, is there?
		// !!! Maybe just do this on the actions people want to use?
		$boards_can = array(
			'make_sticky' => boardsAllowedTo('make_sticky'),
			'move_any' => boardsAllowedTo('move_any'),
			'move_own' => boardsAllowedTo('move_own'),
			'remove_any' => boardsAllowedTo('remove_any'),
			'remove_own' => boardsAllowedTo('remove_own'),
			'lock_any' => boardsAllowedTo('lock_any'),
			'lock_own' => boardsAllowedTo('lock_own'),
			'merge_any' => boardsAllowedTo('merge_any'),
			'approve_posts' => boardsAllowedTo('approve_posts'),
		);

		$redirect_url = isset($_POST['redirect_url']) ? $_POST['redirect_url'] : (isset($_SESSION['old_url']) ? $_SESSION['old_url'] : '');
	}

	if (!$user_info['is_guest'])
		$possibleActions[] = 'markread';
	if (!empty($boards_can['make_sticky']) && !empty($modSettings['enableStickyTopics']))
		$possibleActions[] = 'sticky';
	if (!empty($boards_can['move_any']) || !empty($boards_can['move_own']))
		$possibleActions[] = 'move';
	if (!empty($boards_can['remove_any']) || !empty($boards_can['remove_own']))
		$possibleActions[] = 'remove';
	if (!empty($boards_can['lock_any']) || !empty($boards_can['lock_own']))
		$possibleActions[] = 'lock';
	if (!empty($boards_can['merge_any']))
		$possibleActions[] = 'merge';
	if (!empty($boards_can['approve_posts']))
		$possibleActions[] = 'approve';

	// Two methods: $_REQUEST['actions'] (id_topic => action), and $_REQUEST['topics'] and $_REQUEST['qaction'].
	// (if action is 'move', $_REQUEST['move_to'] or $_REQUEST['move_tos'][$topic] is used.)
	if (!empty($_REQUEST['topics']))
	{
		// If the action isn't valid, just quit now.
		if (empty($_REQUEST['qaction']) || !in_array($_REQUEST['qaction'], $possibleActions))
			redirectexit($redirect_url);

		// Merge requires all topics as one parameter and can be done at once.
		if ($_REQUEST['qaction'] == 'merge')
		{
			// Merge requires at least two topics.
			if (empty($_REQUEST['topics']) || count($_REQUEST['topics']) < 2)
				redirectexit($redirect_url);

			require_once($sourcedir . '/SplitTopics.php');
			return MergeExecute($_REQUEST['topics']);
		}

		// Just convert to the other method, to make it easier.
		foreach ($_REQUEST['topics'] as $topic)
			$_REQUEST['actions'][(int) $topic] = $_REQUEST['qaction'];
	}

	// Weird... how'd you get here?
	if (empty($_REQUEST['actions']))
		redirectexit($redirect_url);

	// Validate each action.
	$temp = array();
	foreach ($_REQUEST['actions'] as $topic => $action)
	{
		if (in_array($action, $possibleActions))
			$temp[(int) $topic] = $action;
	}
	$_REQUEST['actions'] = $temp;

	if (!empty($_REQUEST['actions']))
	{
		// Find all topics...
		$request = smf_db_query( '
			SELECT id_topic, id_member_started, id_board, locked, approved, unapproved_posts
			FROM {db_prefix}topics
			WHERE id_topic IN ({array_int:action_topic_ids})
			LIMIT ' . count($_REQUEST['actions']),
			array(
				'action_topic_ids' => array_keys($_REQUEST['actions']),
			)
		);
		while ($row = mysql_fetch_assoc($request))
		{
			if (!empty($board))
			{
				if ($row['id_board'] != $board || ($modSettings['postmod_active'] && !$row['approved'] && !allowedTo('approve_posts')))
					unset($_REQUEST['actions'][$row['id_topic']]);
			}
			else
			{
				// Don't allow them to act on unapproved posts they can't see...
				if ($modSettings['postmod_active'] && !$row['approved'] && !in_array(0, $boards_can['approve_posts']) && !in_array($row['id_board'], $boards_can['approve_posts']))
					unset($_REQUEST['actions'][$row['id_topic']]);
				// Goodness, this is fun.  We need to validate the action.
				elseif ($_REQUEST['actions'][$row['id_topic']] == 'sticky' && !in_array(0, $boards_can['make_sticky']) && !in_array($row['id_board'], $boards_can['make_sticky']))
					unset($_REQUEST['actions'][$row['id_topic']]);
				elseif ($_REQUEST['actions'][$row['id_topic']] == 'move' && !in_array(0, $boards_can['move_any']) && !in_array($row['id_board'], $boards_can['move_any']) && ($row['id_member_started'] != $user_info['id'] || (!in_array(0, $boards_can['move_own']) && !in_array($row['id_board'], $boards_can['move_own']))))
					unset($_REQUEST['actions'][$row['id_topic']]);
				elseif ($_REQUEST['actions'][$row['id_topic']] == 'remove' && !in_array(0, $boards_can['remove_any']) && !in_array($row['id_board'], $boards_can['remove_any']) && ($row['id_member_started'] != $user_info['id'] || (!in_array(0, $boards_can['remove_own']) && !in_array($row['id_board'], $boards_can['remove_own']))))
					unset($_REQUEST['actions'][$row['id_topic']]);
				elseif ($_REQUEST['actions'][$row['id_topic']] == 'lock' && !in_array(0, $boards_can['lock_any']) && !in_array($row['id_board'], $boards_can['lock_any']) && ($row['id_member_started'] != $user_info['id'] || $locked == 1 || (!in_array(0, $boards_can['lock_own']) && !in_array($row['id_board'], $boards_can['lock_own']))))
					unset($_REQUEST['actions'][$row['id_topic']]);
				// If the topic is approved then you need permission to approve the posts within.
				elseif ($_REQUEST['actions'][$row['id_topic']] == 'approve' && (!$row['unapproved_posts'] || (!in_array(0, $boards_can['approve_posts']) && !in_array($row['id_board'], $boards_can['approve_posts']))))
					unset($_REQUEST['actions'][$row['id_topic']]);
			}
		}
		mysql_free_result($request);
	}

	$stickyCache = array();
	$moveCache = array(0 => array(), 1 => array());
	$removeCache = array();
	$lockCache = array();
	$markCache = array();
	$approveCache = array();

	// Separate the actions.
	foreach ($_REQUEST['actions'] as $topic => $action)
	{
		$topic = (int) $topic;

		if ($action == 'markread')
			$markCache[] = $topic;
		elseif ($action == 'sticky')
			$stickyCache[] = $topic;
		elseif ($action == 'move')
		{
			// $moveCache[0] is the topic, $moveCache[1] is the board to move to.
			$moveCache[1][$topic] = (int) (isset($_REQUEST['move_tos'][$topic]) ? $_REQUEST['move_tos'][$topic] : $_REQUEST['move_to']);

			if (empty($moveCache[1][$topic]))
				continue;

			$moveCache[0][] = $topic;
		}
		elseif ($action == 'remove')
			$removeCache[] = $topic;
		elseif ($action == 'lock')
			$lockCache[] = $topic;
		elseif ($action == 'approve')
			$approveCache[] = $topic;
	}

	if (empty($board))
		$affectedBoards = array();
	else
		$affectedBoards = array($board => array(0, 0));

	// Do all the stickies...
	if (!empty($stickyCache))
	{
		smf_db_query( '
			UPDATE {db_prefix}topics
			SET is_sticky = CASE WHEN is_sticky = {int:is_sticky} THEN 0 ELSE 1 END
			WHERE id_topic IN ({array_int:sticky_topic_ids})',
			array(
				'sticky_topic_ids' => $stickyCache,
				'is_sticky' => 1,
			)
		);

		// Get the board IDs and Sticky status
		$request = smf_db_query( '
			SELECT id_topic, id_board, is_sticky
			FROM {db_prefix}topics
			WHERE id_topic IN ({array_int:sticky_topic_ids})
			LIMIT ' . count($stickyCache),
			array(
				'sticky_topic_ids' => $stickyCache,
			)
		);
		$stickyCacheBoards = array();
		$stickyCacheStatus = array();
		while ($row = mysql_fetch_assoc($request))
		{
			$stickyCacheBoards[$row['id_topic']] = $row['id_board'];
			$stickyCacheStatus[$row['id_topic']] = empty($row['is_sticky']);
		}
		mysql_free_result($request);
	}

	// Move sucka! (this is, by the by, probably the most complicated part....)
	if (!empty($moveCache[0]))
	{
		// I know - I just KNOW you're trying to beat the system.  Too bad for you... we CHECK :P.
		$request = smf_db_query( '
			SELECT t.id_topic, t.id_board, b.count_posts
			FROM {db_prefix}topics AS t
				LEFT JOIN {db_prefix}boards AS b ON (t.id_board = b.id_board)
			WHERE t.id_topic IN ({array_int:move_topic_ids})' . (!empty($board) && !allowedTo('move_any') ? '
				AND t.id_member_started = {int:current_member}' : '') . '
			LIMIT ' . count($moveCache[0]),
			array(
				'current_member' => $user_info['id'],
				'move_topic_ids' => $moveCache[0],
			)
		);
		$moveTos = array();
		$moveCache2 = array();
		$countPosts = array();
		while ($row = mysql_fetch_assoc($request))
		{
			$to = $moveCache[1][$row['id_topic']];

			if (empty($to))
				continue;

			// Does this topic's board count the posts or not?
			$countPosts[$row['id_topic']] = empty($row['count_posts']);

			if (!isset($moveTos[$to]))
				$moveTos[$to] = array();

			$moveTos[$to][] = $row['id_topic'];

			// For reporting...
			$moveCache2[] = array($row['id_topic'], $row['id_board'], $to);
		}
		mysql_free_result($request);

		$moveCache = $moveCache2;

		require_once($sourcedir . '/MoveTopic.php');

		// Do the actual moves...
		foreach ($moveTos as $to => $topics)
			moveTopics($topics, $to);

		// Does the post counts need to be updated?
		if (!empty($moveTos))
		{
			$topicRecounts = array();
			$request = smf_db_query( '
				SELECT id_board, count_posts
				FROM {db_prefix}boards
				WHERE id_board IN ({array_int:move_boards})',
				array(
					'move_boards' => array_keys($moveTos),
				)
			);

			while ($row = mysql_fetch_assoc($request))
			{
				$cp = empty($row['count_posts']);

				// Go through all the topics that are being moved to this board.
				foreach ($moveTos[$row['id_board']] as $topic)
				{
					// If both boards have the same value for post counting then no adjustment needs to be made.
					if ($countPosts[$topic] != $cp)
					{
						// If the board being moved to does count the posts then the other one doesn't so add to their post count.
						$topicRecounts[$topic] = $cp ? '+' : '-';
					}
				}
			}

			mysql_free_result($request);

			if (!empty($topicRecounts))
			{
				$members = array();

				// Get all the members who have posted in the moved topics.
				$request = smf_db_query( '
					SELECT id_member, id_topic
					FROM {db_prefix}messages
					WHERE id_topic IN ({array_int:moved_topic_ids})',
					array(
						'moved_topic_ids' => array_keys($topicRecounts),
					)
				);

				while ($row = mysql_fetch_assoc($request))
				{
					if (!isset($members[$row['id_member']]))
						$members[$row['id_member']] = 0;

					if ($topicRecounts[$row['id_topic']] === '+')
						$members[$row['id_member']] += 1;
					else
						$members[$row['id_member']] -= 1;
				}

				mysql_free_result($request);

				// And now update them member's post counts
				foreach ($members as $id_member => $post_adj)
					updateMemberData($id_member, array('posts' => 'posts + ' . $post_adj));

			}
		}
	}

	// Now delete the topics...
	if (!empty($removeCache))
	{
		// They can only delete their own topics. (we wouldn't be here if they couldn't do that..)
		$result = smf_db_query( '
			SELECT id_topic, id_board
			FROM {db_prefix}topics
			WHERE id_topic IN ({array_int:removed_topic_ids})' . (!empty($board) && !allowedTo('remove_any') ? '
				AND id_member_started = {int:current_member}' : '') . '
			LIMIT ' . count($removeCache),
			array(
				'current_member' => $user_info['id'],
				'removed_topic_ids' => $removeCache,
			)
		);

		$removeCache = array();
		$removeCacheBoards = array();
		while ($row = mysql_fetch_assoc($result))
		{
			$removeCache[] = $row['id_topic'];
			$removeCacheBoards[$row['id_topic']] = $row['id_board'];
		}
		mysql_free_result($result);

		// Maybe *none* were their own topics.
		if (!empty($removeCache))
		{
			// Gotta send the notifications *first*!
			foreach ($removeCache as $topic)
			{
				// Only log the topic ID if it's not in the recycle board.
				logAction('remove', array((empty($modSettings['recycle_enable']) || $modSettings['recycle_board'] != $removeCacheBoards[$topic] ? 'topic' : 'old_topic_id') => $topic, 'board' => $removeCacheBoards[$topic]));
				sendNotifications($topic, 'remove');
			}

			require_once($sourcedir . '/RemoveTopic.php');
			removeTopics($removeCache);
		}
	}

	// Approve the topics...
	if (!empty($approveCache))
	{
		// We need unapproved topic ids and their authors!
		$request = smf_db_query( '
			SELECT id_topic, id_member_started
			FROM {db_prefix}topics
			WHERE id_topic IN ({array_int:approve_topic_ids})
				AND approved = {int:not_approved}
			LIMIT ' . count($approveCache),
			array(
				'approve_topic_ids' => $approveCache,
				'not_approved' => 0,
			)
		);
		$approveCache = array();
		$approveCacheMembers = array();
		while ($row = mysql_fetch_assoc($request))
		{
			$approveCache[] = $row['id_topic'];
			$approveCacheMembers[$row['id_topic']] = $row['id_member_started'];
		}
		mysql_free_result($request);

		// Any topics to approve?
		if (!empty($approveCache))
		{
			// Handle the approval part...
			approveTopics($approveCache);

			// Time for some logging!
			foreach ($approveCache as $topic)
				logAction('approve_topic', array('topic' => $topic, 'member' => $approveCacheMembers[$topic]));
		}
	}

	// And (almost) lastly, lock the topics...
	if (!empty($lockCache))
	{
		$lockStatus = array();

		// Gotta make sure they CAN lock/unlock these topics...
		if (!empty($board) && !allowedTo('lock_any'))
		{
			// Make sure they started the topic AND it isn't already locked by someone with higher priv's.
			$result = smf_db_query( '
				SELECT id_topic, locked, id_board
				FROM {db_prefix}topics
				WHERE id_topic IN ({array_int:locked_topic_ids})
					AND id_member_started = {int:current_member}
					AND locked IN (2, 0)
				LIMIT ' . count($lockCache),
				array(
					'current_member' => $user_info['id'],
					'locked_topic_ids' => $lockCache,
				)
			);
			$lockCache = array();
			$lockCacheBoards = array();
			while ($row = mysql_fetch_assoc($result))
			{
				$lockCache[] = $row['id_topic'];
				$lockCacheBoards[$row['id_topic']] = $row['id_board'];
				$lockStatus[$row['id_topic']] = empty($row['locked']);
			}
			mysql_free_result($result);
		}
		else
		{
			$result = smf_db_query( '
				SELECT id_topic, locked, id_board
				FROM {db_prefix}topics
				WHERE id_topic IN ({array_int:locked_topic_ids})
				LIMIT ' . count($lockCache),
				array(
					'locked_topic_ids' => $lockCache,
				)
			);
			$lockCacheBoards = array();
			while ($row = mysql_fetch_assoc($result))
			{
				$lockStatus[$row['id_topic']] = empty($row['locked']);
				$lockCacheBoards[$row['id_topic']] = $row['id_board'];
			}
			mysql_free_result($result);
		}

		// It could just be that *none* were their own topics...
		if (!empty($lockCache))
		{
			// Alternate the locked value.
			smf_db_query( '
				UPDATE {db_prefix}topics
				SET locked = CASE WHEN locked = {int:is_locked} THEN ' . (allowedTo('lock_any') ? '1' : '2') . ' ELSE 0 END
				WHERE id_topic IN ({array_int:locked_topic_ids})',
				array(
					'locked_topic_ids' => $lockCache,
					'is_locked' => 0,
				)
			);
		}
	}

	if (!empty($markCache))
	{
		$markArray = array();
		foreach ($markCache as $topic)
			$markArray[] = array($modSettings['maxMsgID'], $user_info['id'], $topic);

		smf_db_insert('replace',
			'{db_prefix}log_topics',
			array('id_msg' => 'int', 'id_member' => 'int', 'id_topic' => 'int'),
			$markArray,
			array('id_member', 'id_topic')
		);
	}

	foreach ($moveCache as $topic)
	{
		// Didn't actually move anything!
		if (!isset($topic[0]))
			break;

		logAction('move', array('topic' => $topic[0], 'board_from' => $topic[1], 'board_to' => $topic[2]));
		sendNotifications($topic[0], 'move');
	}
	foreach ($lockCache as $topic)
	{
		logAction($lockStatus[$topic] ? 'lock' : 'unlock', array('topic' => $topic, 'board' => $lockCacheBoards[$topic]));
		sendNotifications($topic, $lockStatus[$topic] ? 'lock' : 'unlock');
	}
	foreach ($stickyCache as $topic)
	{
		logAction($stickyCacheStatus[$topic] ? 'unsticky' : 'sticky', array('topic' => $topic, 'board' => $stickyCacheBoards[$topic]));
		sendNotifications($topic, 'sticky');
	}

	updateStats('topic');
	updateStats('message');
	updateSettings(array(
		'calendar_updated' => time(),
	));

	if (!empty($affectedBoards))
		updateLastMessages(array_keys($affectedBoards));

	redirectexit($redirect_url);
}
