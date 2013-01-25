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

/*	This file currently only contains one function to collect the data needed to
	show a list of boards for the board index and the message index.

	array getBoardIndex(array boardIndexOptions)
		- Fetches a list of boards and (optional) categories including
		  statistical information, child boards and moderators.
		- Used by both the board index (main data) and the message index (child
		  boards).
		- Depending on the include_categories setting returns an associative
		  array with categories->boards->child_boards or an associative array
		  with boards->child_boards.
*/

function getBoardIndex($boardIndexOptions)
{
	global $scripturl, $user_info, $modSettings, $txt;
	global $settings, $context;

	// For performance, track the latest post while going through the boards.
	if (!empty($boardIndexOptions['set_latest_post']))
		$latest_post = array(
			'timestamp' => 0,
			'ref' => 0,
		);

	// Find all boards and categories, as well as related information.  This will be sorted by the natural order of boards and categories, which we control.
	$result_boards = smf_db_query('
		SELECT' . ($boardIndexOptions['include_categories'] ? '
			c.id_cat, c.name AS cat_name, c.description AS cat_desc,' : '') . '
			b.id_board, b.name AS board_name, b.description, b.redirect, b.icon AS boardicon,
			CASE WHEN b.redirect != {string:blank_string} THEN 1 ELSE 0 END AS is_redirect,
			b.num_posts, b.num_topics, b.unapproved_posts, b.unapproved_topics, b.id_parent, b.allow_topics,
			IFNULL(m.poster_time, 0) AS poster_time, IFNULL(mem.member_name, m.poster_name) AS poster_name,
			m.subject, m1.subject AS first_subject, m.id_topic, t.id_first_msg AS id_first_msg, t.id_prefix, m1.icon AS icon, IFNULL(mem.real_name, m.poster_name) AS real_name, p.name as topic_prefix,
			' . ($user_info['is_guest'] ? ' 1 AS is_read, 0 AS new_from,' : '
			(IFNULL(lb.id_msg, 0) >= b.id_msg_updated) AS is_read, IFNULL(lb.id_msg, -1) + 1 AS new_from,' . ($boardIndexOptions['include_categories'] ? '
			c.can_collapse, IFNULL(cc.id_member, 0) AS is_collapsed,' : '')) . '
			IFNULL(mem.id_member, 0) AS id_member, m.id_msg,
			IFNULL(mods_mem.id_member, 0) AS id_moderator, mods_mem.real_name AS mod_real_name
		FROM {db_prefix}boards AS b' . ($boardIndexOptions['include_categories'] ? '
			LEFT JOIN {db_prefix}categories AS c ON (c.id_cat = b.id_cat)' : '') . '
			LEFT JOIN {db_prefix}messages AS m ON (m.id_msg = b.id_last_msg)
			LEFT JOIN {db_prefix}topics AS t ON (t.id_topic = m.id_topic)
			LEFT JOIN {db_prefix}prefixes AS p ON (p.id_prefix = t.id_prefix)
			LEFT JOIN {db_prefix}messages AS m1 ON (m1.id_msg = t.id_first_msg)
			LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = m.id_member)' . ($user_info['is_guest'] ? '' : '
			LEFT JOIN {db_prefix}log_boards AS lb ON (lb.id_board = b.id_board AND lb.id_member = {int:current_member})' . ($boardIndexOptions['include_categories'] ? '
			LEFT JOIN {db_prefix}collapsed_categories AS cc ON (cc.id_cat = c.id_cat AND cc.id_member = {int:current_member})' : '')) . '
			LEFT JOIN {db_prefix}moderators AS mods ON (mods.id_board = b.id_board)
			LEFT JOIN {db_prefix}members AS mods_mem ON (mods_mem.id_member = mods.id_member)
		WHERE {query_see_board}' . (empty($boardIndexOptions['countChildPosts']) ? (empty($boardIndexOptions['base_level']) ? '' : '
			AND b.child_level >= {int:child_level}') : '
			AND b.child_level BETWEEN ' . $boardIndexOptions['base_level'] . ' AND ' . ($boardIndexOptions['base_level'] + 1)),
		array(
			'current_member' => $user_info['id'],
			'child_level' => $boardIndexOptions['base_level'],
			'blank_string' => '',
		)
	);

	// Start with an empty array.
	if ($boardIndexOptions['include_categories'])
		$categories = array();
	else
		$this_category = array();

	$total_ignored_boards = 0;

	// Run through the categories and boards (or only boards)....
	while ($row_board = mysql_fetch_assoc($result_boards))
	{
		// Perhaps we are ignoring this board?
		$ignoreThisBoard = in_array($row_board['id_board'], $user_info['ignoreboards']);
		$total_ignored_boards += ($ignoreThisBoard ? 1 : 0);
		$row_board['is_read'] = !empty($row_board['is_read']) || $ignoreThisBoard ? '1' : '0';

		if ($boardIndexOptions['include_categories'])
		{
			// Haven't set this category yet.
			if (empty($categories[$row_board['id_cat']]))
			{
				$categories[$row_board['id_cat']] = array(
					'id' => $row_board['id_cat'],
					'name' => $row_board['cat_name'],
					'desc' => $row_board['cat_desc'],
					'is_collapsed' => isset($row_board['can_collapse']) && $row_board['can_collapse'] == 1 && $row_board['is_collapsed'] > 0,
					'can_collapse' => isset($row_board['can_collapse']) && $row_board['can_collapse'] == 1,
					'collapse_href' => isset($row_board['can_collapse']) ? $scripturl . '?action=collapse;c=' . $row_board['id_cat'] . ';sa=' . ($row_board['is_collapsed'] > 0 ? 'expand;' : 'collapse;') . $context['session_var'] . '=' . $context['session_id'] . '#c' . $row_board['id_cat'] : '',
					'collapse_image' => isset($row_board['can_collapse']) ? '<img class="clipsrc '.($row_board['is_collapsed'] ? ' _expand' : '_collapse').'" src="' . $settings['images_url'] . '/clipsrc.png" alt="-" />' : '',
					'href' => $scripturl . '#c' . $row_board['id_cat'],
					'boards' => array(),
					'is_root' => $row_board['cat_name'][0] === '!' ? true : false,
					'new' => false
				);
				$categories[$row_board['id_cat']]['link'] = '<a id="c' . $row_board['id_cat'] . '"></a>' . ($categories[$row_board['id_cat']]['can_collapse'] ? '<a href="' . $categories[$row_board['id_cat']]['collapse_href'] . '">' . $row_board['cat_name'] . '</a>' : $row_board['cat_name']);
			}

			// If this board has new posts in it (and isn't the recycle bin!) then the category is new.
			if (empty($modSettings['recycle_enable']) || $modSettings['recycle_board'] != $row_board['id_board'])
				$categories[$row_board['id_cat']]['new'] |= empty($row_board['is_read']) && $row_board['poster_name'] != '';

			// Avoid showing category unread link where it only has redirection boards.
			$categories[$row_board['id_cat']]['show_unread'] = !empty($categories[$row_board['id_cat']]['show_unread']) ? 1 : !$row_board['is_redirect'];

			// Collapsed category - don't do any of this.
			//if ($categories[$row_board['id_cat']]['is_collapsed'])
			//	continue;

			// Let's save some typing.  Climbing the array might be slower, anyhow.
			$this_category = &$categories[$row_board['id_cat']]['boards'];
		}

		// This is a parent board.
		if ($row_board['id_parent'] == $boardIndexOptions['parent_id'])
		{
			// Is this a new board, or just another moderator?
			if (!isset($this_category[$row_board['id_board']]))
			{
				// Not a child.
				$isChild = false;

				$href = URL::board($row_board['id_board'], $row_board['board_name'], 0, false);
				$this_category[$row_board['id_board']] = array(
					'new' => empty($row_board['is_read']),
					'id' => $row_board['id_board'],
					'name' => $row_board['board_name'],
					'description' => $row_board['description'],
					'moderators' => array(),
					'link_moderators' => array(),
					'children' => array(),
					'link_children' => array(),
					'children_new' => false,
					'topics' => $row_board['num_topics'],
					'posts' => $row_board['num_posts'],
					'is_redirect' => $row_board['is_redirect'],
					'is_page' => !empty($row_board['redirect']) && $row_board['redirect'][0] === '%' && intval(substr($row_board['redirect'], 1)) > 0,
					'redirect' => $row_board['redirect'],
					'boardicon' => $row_board['boardicon'],
					'unapproved_topics' => $row_board['unapproved_topics'],
					'unapproved_posts' => $row_board['unapproved_posts'] - $row_board['unapproved_topics'],
					'can_approve_posts' => !empty($user_info['mod_cache']['ap']) && ($user_info['mod_cache']['ap'] == array(0) || in_array($row_board['id_board'], $user_info['mod_cache']['ap'])),
					'href' => $href,
					'link' => '<a href="' . $href . '">' . $row_board['board_name'] . '</a>',
					'act_as_cat' => $row_board['allow_topics'] ? false : true,
					'ignored' => $ignoreThisBoard
				);
				$this_category[$row_board['id_board']]['page_link'] = $this_category[$row_board['id_board']]['is_page'] ? URL::topic(intval(substr($this_category[$row_board['id_board']]['redirect'], 1)), $this_category[$row_board['id_board']]['name'], 0) : '';

			}
			if (!empty($row_board['id_moderator']))
			{
				$this_category[$row_board['id_board']]['moderators'][$row_board['id_moderator']] = array(
					'id' => $row_board['id_moderator'],
					'name' => $row_board['mod_real_name'],
					'href' => $scripturl . '?action=profile;u=' . $row_board['id_moderator'],
					'link' => '<a href="' . $scripturl . '?action=profile;u=' . $row_board['id_moderator'] . '" title="' . $txt['board_moderator'] . '">' . $row_board['mod_real_name'] . '</a>'
				);
				$this_category[$row_board['id_board']]['link_moderators'][] = '<a href="' . $scripturl . '?action=profile;u=' . $row_board['id_moderator'] . '" title="' . $txt['board_moderator'] . '">' . $row_board['mod_real_name'] . '</a>';
			}
		}
		// Found a child board.... make sure we've found its parent and the child hasn't been set already.
		elseif (isset($this_category[$row_board['id_parent']]['children']) && !isset($this_category[$row_board['id_parent']]['children'][$row_board['id_board']]))
		{
			// A valid child!
			$isChild = true;

			$href = URL::board($row_board['id_board'], $row_board['board_name'], 0, false);
			$this_category[$row_board['id_parent']]['children'][$row_board['id_board']] = array(
				'id' => $row_board['id_board'],
				'name' => $row_board['board_name'],
				'description' => $row_board['description'],
				'short_description' => !empty($row_board['description']) ? ($modSettings['child_board_desc_shortened'] ? '('.commonAPI::substr($row_board['description'], 0, $modSettings['child_board_desc_shortened']) . '...)' : '('.$row_board['description'].')') : '',
				'new' => empty($row_board['is_read']) && $row_board['poster_name'] != '',
				'topics' => $row_board['num_topics'],
				'posts' => $row_board['num_posts'],
				'is_redirect' => $row_board['is_redirect'],
				'is_page' => !empty($row_board['redirect']) && $row_board['redirect'][0] === '%' && intval(substr($row_board['redirect'], 1)) > 0,
				'redirect' => $row_board['redirect'],
				'boardicon' => $row_board['boardicon'],
				'unapproved_topics' => $row_board['unapproved_topics'],
				'unapproved_posts' => $row_board['unapproved_posts'] - $row_board['unapproved_topics'],
				'can_approve_posts' => !empty($user_info['mod_cache']['ap']) && ($user_info['mod_cache']['ap'] == array(0) || in_array($row_board['id_board'], $user_info['mod_cache']['ap'])),
				'href' => $href,
				'link' => '<a href="' . $href . '">' . $row_board['board_name'] . '</a>',
				'act_as_cat' => $row_board['allow_topics'] ? false : true,
				'ignored' => $ignoreThisBoard
			);

			$this_category[$row_board['id_parent']]['children'][$row_board['id_board']]['page_link'] = $this_category[$row_board['id_parent']]['children'][$row_board['id_board']]['is_page'] ? URL::topic(intval(substr($this_category[$row_board['id_parent']]['children'][$row_board['id_board']]['redirect'], 1)), $this_category[$row_board['id_parent']]['children'][$row_board['id_board']]['name'], 0) : '';

			// Counting child board posts is... slow :/.
			if (!empty($boardIndexOptions['countChildPosts']) && !$row_board['is_redirect'])
			{
				$this_category[$row_board['id_parent']]['posts'] += $row_board['num_posts'];
				$this_category[$row_board['id_parent']]['topics'] += $row_board['num_topics'];
			}

			// Does this board contain new boards?
			$this_category[$row_board['id_parent']]['children_new'] |= empty($row_board['is_read']);

			// This is easier to use in many cases for the theme....
			$this_category[$row_board['id_parent']]['link_children'][] = &$this_category[$row_board['id_parent']]['children'][$row_board['id_board']]['link'];
		}
		// Child of a child... just add it on...
		elseif (!empty($boardIndexOptions['countChildPosts']))
		{
			if (!isset($parent_map))
				$parent_map = array();

			if (!isset($parent_map[$row_board['id_parent']]))
				foreach ($this_category as $id => $board)
				{
					if (!isset($board['children'][$row_board['id_parent']]))
						continue;

					$parent_map[$row_board['id_parent']] = array(&$this_category[$id], &$this_category[$id]['children'][$row_board['id_parent']]);
					$parent_map[$row_board['id_board']] = array(&$this_category[$id], &$this_category[$id]['children'][$row_board['id_parent']]);

					break;
				}

			if (isset($parent_map[$row_board['id_parent']]) && !$row_board['is_redirect'])
			{
				$parent_map[$row_board['id_parent']][0]['posts'] += $row_board['num_posts'];
				$parent_map[$row_board['id_parent']][0]['topics'] += $row_board['num_topics'];
				$parent_map[$row_board['id_parent']][1]['posts'] += $row_board['num_posts'];
				$parent_map[$row_board['id_parent']][1]['topics'] += $row_board['num_topics'];

				continue;
			}

			continue;
		}
		// Found a child of a child - skip.
		else
			continue;

		// Prepare the subject, and make sure it's not too long.
		censorText($row_board['subject']);
		$mhref = $row_board['poster_name'] != '' && !empty($row_board['id_member']) ? URL::user($row_board['id_member'], $row_board['real_name']) : '';

		$this_last_post = array(
			'id' => $row_board['id_msg'],
			'time' => $row_board['poster_time'] > 0 ? timeformat($row_board['poster_time']) : $txt['not_applicable'],
			'timestamp' => forum_time(true, $row_board['poster_time']),
			'member' => array(
				'id' => $row_board['id_member'],
				'username' => $row_board['poster_name'] != '' ? $row_board['poster_name'] : $txt['not_applicable'],
				'name' => $row_board['real_name'],
				'href' => $mhref,
				'link' => $row_board['poster_name'] != '' ? (!empty($row_board['id_member']) ? '<a class="mcard" data-mid="'.$row_board['id_member'].'" href="'.$mhref.'">' . $row_board['real_name'] . '</a>' : $row_board['real_name']) : $txt['not_applicable'],
			),
			'start' => 'msg' . $row_board['new_from'],
			'topic' => $row_board['id_topic'],
			'prefix' => !empty($row_board['topic_prefix']) ? html_entity_decode($row_board['topic_prefix']) . '&nbsp;' : ''
		);
		$row_board['short_subject'] = shorten_subject($row_board['subject'], 50);
		$this_last_post['subject'] = $row_board['short_subject'];

		$this_first_post = array(
			'id' => $row_board['id_first_msg'],
			'icon' => $row_board['icon'],
			'icon_url' => getPostIcon($row_board['icon']),

		);
		// Provide the href and link.
		if ($row_board['subject'] != '')
		{
			$this_last_post['href'] = URL::topic($row_board['id_topic'], $row_board['first_subject'], 0, false, '.msg' . ($user_info['is_guest'] ? $row_board['id_msg'] : $row_board['new_from']), '#new');
			if(empty($row_board['is_read']))
				$this_last_post['href'] = URL::addParam($this_last_post['href'], 'boardseen');
			//$this_last_post['href'] = $scripturl . '?topic=' . $row_board['id_topic'] . '.msg' . ($user_info['is_guest'] ? $row_board['id_msg'] : $row_board['new_from']) . (empty($row_board['is_read']) ? ';boardseen' : '') . '#new';
			$this_last_post['link'] = '<a rel="nofollow" href="' . $this_last_post['href'] . '" title="' . $row_board['subject'] . '">' . $row_board['short_subject'] . '</a>';
			$this_last_post['topichref'] = URL::topic($row_board['id_topic'], $row_board['first_subject'], 0);// $scripturl . '?topic=' . $row_board['id_topic'];
			$this_last_post['topiclink'] = '<a href="' . $this_last_post['topichref'] . '" title="' . $row_board['first_subject'] . '">' . $row_board['short_subject'] . '</a>';
		}
		else
		{
			$this_last_post['href'] = '';
			$this_last_post['link'] = $txt['not_applicable'];
			$this_last_post['topiclink'] = $txt['not_applicable'];
		}

		// Set the last post in the parent board.
		if ($row_board['id_parent'] == $boardIndexOptions['parent_id'] || ($isChild && !empty($row_board['poster_time']) && $this_category[$row_board['id_parent']]['last_post']['timestamp'] < forum_time(true, $row_board['poster_time']))) {
			$this_category[$isChild ? $row_board['id_parent'] : $row_board['id_board']]['last_post'] = $this_last_post;
			$this_category[$isChild ? $row_board['id_parent'] : $row_board['id_board']]['first_post'] = $this_first_post;
		}
		// Just in the child...?
		if ($isChild)
		{
			$this_category[$row_board['id_parent']]['children'][$row_board['id_board']]['last_post'] = $this_last_post;
			$this_category[$row_board['id_parent']]['children'][$row_board['id_board']]['first_post'] = $this_first_post;

			// If there are no posts in this board, it really can't be new...
			$this_category[$row_board['id_parent']]['children'][$row_board['id_board']]['new'] &= $row_board['poster_name'] != '';
		}
		// No last post for this board?  It's not new then, is it..?
		elseif ($row_board['poster_name'] == '')
			$this_category[$row_board['id_board']]['new'] = false;

		// Determine a global most recent topic.
		if (!empty($boardIndexOptions['set_latest_post']) && !empty($row_board['poster_time']) && $row_board['poster_time'] > $latest_post['timestamp'] && !$ignoreThisBoard)
			$latest_post = array(
				'timestamp' => $row_board['poster_time'],
				'ref' => &$this_category[$isChild ? $row_board['id_parent'] : $row_board['id_board']]['last_post'],
			);
	}
	mysql_free_result($result_boards);

	// By now we should know the most recent post...if we wanna know it that is.
	if (!empty($boardIndexOptions['set_latest_post']) && !empty($latest_post['ref']))
		$context['latest_post'] = $latest_post['ref'];

	$hidden_boards = $visible_boards = 0;

	$context['hidden_boards']['id'] = $context['hidden_boards']['is_collapsed'] = 0;

	// only run this if we actually have some boards on the ignore list to save cycles.
	if($total_ignored_boards) {
		if($boardIndexOptions['include_categories']) {
			foreach($categories as &$cat)
				$hidden_boards += hideIgnoredBoards($cat['boards']);
		}
		else if(count($this_category))
			$hidden_boards += hideIgnoredBoards($this_category);
		$context['hidden_boards']['notice'] = $txt[$hidden_boards > 1 ? 'hidden_boards_notice_many' : 'hidden_boards_notice_one'];
		$context['hidden_boards']['setup_notice'] = sprintf($txt['hidden_boards_setup_notice'], $scripturl . '?action=profile;area=ignoreboards');
	}

	$context['hidden_boards']['hidden_count'] = $hidden_boards;
	$context['hidden_boards']['visible_count'] = $visible_boards;
	return $boardIndexOptions['include_categories'] ? $categories : $this_category;
}

/**
 * move hidden boards into a separate pseudo category to hide them from normal
 * board list display.
 *
 * @param $boardlist	array of boards (either a $category['boards'] or normal list
 * 						when categories are unwanted.
 * @return int			number of boards that are hidden in this list of boards
 */
function hideIgnoredBoards(&$boardlist)
{
	global $context;
	$_hidden = 0;

	foreach($boardlist as $key => &$board) {
		if($board['ignored']) {
			$context['hidden_boards']['boards'][$key] = &$board;
			$_hidden++;
			if(isset($board['children']))
				$_hidden += count($board['children']);
			unset($boardlist[$key]);
		}
		else if(isset($board['children'])) {
			foreach($board['children'] as $ckey => &$child) {
				if($child['ignored']) {
					$context['hidden_boards']['boards'][$ckey] = &$child;
					$_hidden++;
					unset($board['children'][$ckey]);
				}
			}
		}
	}
	return $_hidden;
}
