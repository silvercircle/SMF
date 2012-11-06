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

/*	This file currently only holds the function for showing a list of online
	users used by the board index and SSI. In the future it'll also contain
	functions used by the Who's online page.

	array getMembersOnlineStats(array membersOnlineOptions)
		- retrieve a list and several other statistics of the users currently
		  online on the forum.
		- used by the board index and SSI.
		- also returns the membergroups of the users that are currently online.
		- (optionally) hides members that chose to hide their online presense.
		- populate the buddies array for the sidebar user panel
		- populate team_members with members who are in the global moderators
		  group (= "the forum team") for the sidebar panel.
*/

// Retrieve a list and several other statistics of the users currently online.
function getMembersOnlineStats($membersOnlineOptions)
{
	global $smcFunc, $context, $scripturl, $user_info, $modSettings, $txt, $memberContext;

	// The list can be sorted in several ways.
	$allowed_sort_options = array(
		'log_time',
		'real_name',
		'show_online',
		'online_color',
		'group_name',
	);
	// Default the sorting method to 'most recent online members first'.
	if (!isset($membersOnlineOptions['sort']))
	{
		$membersOnlineOptions['sort'] = 'log_time';
		$membersOnlineOptions['reverse_sort'] = true;
	}

	// Not allowed sort method? Bang! Error!
	elseif (!in_array($membersOnlineOptions['sort'], $allowed_sort_options))
		trigger_error('Sort method for getMembersOnlineStats() function is not allowed', E_USER_NOTICE);

	// Initialize the array that'll be returned later on.
	$membersOnlineStats = array(
		'users_online' => array(),
		'list_users_online' => array(),
		'online_groups' => array(),
		'num_guests' => 0,
		'num_spiders' => 0,
		'num_buddies' => 0,
		'num_users_hidden' => 0,
		'num_users_online' => 0,
	);

	// Get any spiders if enabled.
	$spiders = array();
	$spider_finds = array();
	if (!empty($modSettings['show_spider_online']) && ($modSettings['show_spider_online'] < 3 || allowedTo('admin_forum')) && !empty($modSettings['spider_name_cache']))
		$spiders = unserialize($modSettings['spider_name_cache']);

	// Load the users online right now.
	$request = smf_db_query('
		SELECT
			lo.id_member, lo.log_time, lo.id_spider, mem.real_name, mem.member_name, mem.show_online, mem.id_group AS primary_group, mem.id_post_group AS post_group,
			mem.additional_groups AS secondary_groups, mg.group_name
		FROM {db_prefix}log_online AS lo
			LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = lo.id_member)
			LEFT JOIN {db_prefix}membergroups AS mg ON (mg.id_group = CASE WHEN mem.id_group = {int:reg_mem_group} THEN mem.id_post_group ELSE mem.id_group END)',
		array(
			'reg_mem_group' => 0
		)
	);
	$real_team_members = array();
	$visible_team_members = array();
	while ($row = mysql_fetch_assoc($request))
	{
		if(!empty($modSettings['who_track_team']) && ($row['primary_group'] == 2 || in_array(2, explode(',', $row['secondary_groups'])))) {
			$real_team_members[] = $row['id_member'];
			if(($row['show_online'] || $membersOnlineOptions['show_hidden']) && $user_info['id'] != $row['id_member'])
				$visible_team_members[] = $row['id_member'];
		}
		if (empty($row['real_name']))
		{
			// Do we think it's a spider?
			if ($row['id_spider'] && isset($spiders[$row['id_spider']]))
			{
				$spider_finds[$row['id_spider']] = isset($spider_finds[$row['id_spider']]) ? $spider_finds[$row['id_spider']] + 1 : 1;
				$membersOnlineStats['num_spiders']++;
			}
			// Guests are only nice for statistics.
			$membersOnlineStats['num_guests']++;

			continue;
		}

		elseif (empty($row['show_online']) && empty($membersOnlineOptions['show_hidden']))
		{
			// Just increase the stats and don't add this hidden user to any list.
			$membersOnlineStats['num_users_hidden']++;
			continue;
		}

		$href = URL::user($row['id_member'], $row['real_name']);
		// Some basic color coding...
		// Buddies get counted and highlighted.
		$is_buddy = in_array($row['id_member'], $user_info['buddies']);
		if ($is_buddy)
			$membersOnlineStats['num_buddies']++;

		$class = 'member group_' . (empty($row['primary_group']) ? $row['post_group'] : $row['primary_group']) . ($is_buddy ? ' buddy' : '');

		if($row['id_member'] == $user_info['id'])
			$link = '<strong>'.$txt['you'].'</strong>';
		else
			$link = '<a onclick="getMcard('.$row['id_member'].');return(false);" class="'.$class.'" href="' . $href . '">' . $row['real_name'] . '</a>';

		// A lot of useful information for each member.
		$membersOnlineStats['users_online'][$row[$membersOnlineOptions['sort']] . $row['member_name']] = array(
			'id' => $row['id_member'],
			'username' => $row['member_name'],
			'name' => $row['real_name'],
			'group' => $row['primary_group'],
			'href' => $href,
			'link' => $link,
			'is_buddy' => $is_buddy,
			'hidden' => empty($row['show_online']),
			'is_last' => false,
		);
		if($is_buddy)
			$membersOnlineStats['buddies_online'][] = $membersOnlineStats['users_online'][$row[$membersOnlineOptions['sort']] . $row['member_name']]['link'];
		// This is the compact version, simply implode it to show.
		$membersOnlineStats['list_users_online'][$row[$membersOnlineOptions['sort']] . $row['member_name']] = empty($row['show_online']) ? '<em>' . $link . '</em>' : $link;

		if($row['primary_group'] == 2 || in_array(2, explode(',', $row['secondary_groups'])))
			$team_members[] = $row['id_member'];

		// Store all distinct (primary) membergroups that are shown.
		if (!isset($membersOnlineStats['online_groups'][$row['primary_group']]))
			$membersOnlineStats['online_groups'][$row['primary_group']] = array(
				'id' => $row['primary_group'],
				'name' => $row['group_name'],
			);
	}
	mysql_free_result($request);

	/*
	 * team members can be cached. reload them when the number of cached team members != the number of
	 * actually online (= a team member logged in or out)
	 */
	if(($_team_members = CacheAPI::getCache('_team_members_online', 1200)) == null) {
		$_team_members = array();
		if(!empty($real_team_members)) {
			$ids = loadMemberData($real_team_members);
			foreach($real_team_members as $member) {
				loadMemberContext($member);
				$_team_members[$member] = &$memberContext[$member];
			}
			CacheAPI::putCache('_team_members_online', $_team_members, 1200);
		}
		else
			CacheAPI::putCache('_team_members_online', null, 0);
	}
	$membersOnlineStats['team_members'] = &$_team_members;		
	$membersOnlineStats['visible_team_members'] = $visible_team_members;

	// If there are spiders only and we're showing the detail, add them to the online list - at the bottom.
	if (!empty($spider_finds) && $modSettings['show_spider_online'] > 1)
		foreach ($spider_finds as $id => $count)
		{
			$link = $spiders[$id] . ($count > 1 ? ' (' . $count . ')' : '');
			$sort = $membersOnlineOptions['sort'] = 'log_time' && $membersOnlineOptions['reverse_sort'] ? 0 : 'zzz_';
			$membersOnlineStats['users_online'][$sort . $spiders[$id]] = array(
				'id' => 0,
				'username' => $spiders[$id],
				'name' => $link,
				'group' => $txt['spiders'],
				'href' => '',
				'link' => $link,
				'is_buddy' => false,
				'hidden' => false,
				'is_last' => false,
			);
			$membersOnlineStats['list_users_online'][$sort . $spiders[$id]] = $link;
		}

	// Time to sort the list a bit.
	if (!empty($membersOnlineStats['users_online']))
	{
		// Determine the sort direction.
		$sortFunction = empty($membersOnlineOptions['reverse_sort']) ? 'ksort' : 'krsort';

		// Sort the two lists.
		$sortFunction($membersOnlineStats['users_online']);
		$sortFunction($membersOnlineStats['list_users_online']);

		// Mark the last list item as 'is_last'.
		$userKeys = array_keys($membersOnlineStats['users_online']);
		$membersOnlineStats['users_online'][end($userKeys)]['is_last'] = true;
	}

	// Also sort the membergroups.
	ksort($membersOnlineStats['online_groups']);

	// Hidden and non-hidden members make up all online members.
	$membersOnlineStats['num_users_online'] = count($membersOnlineStats['users_online']) + $membersOnlineStats['num_users_hidden'] - (isset($modSettings['show_spider_online']) && $modSettings['show_spider_online'] > 1 ? count($spider_finds) : 0);

	// output users who were online today
	if(!empty($modSettings['who_track_daily_visitors']) && !empty($modSettings['online_today'])) {
		foreach($modSettings['online_today'] as $member) {
			if($member['show_online'] || $membersOnlineOptions['show_hidden'])
				$membersOnlineStats['online_today'][] = $member['link'];
		}
	}
	return $membersOnlineStats;
}

// Check if the number of users online is a record and store it.
function trackStatsUsersOnline($total_users_online)
{
	global $modSettings, $smcFunc;

	$settingsToUpdate = array();

	// More members on now than ever were?  Update it!
	if (!isset($modSettings['mostOnline']) || $total_users_online >= $modSettings['mostOnline'])
		$settingsToUpdate = array(
			'mostOnline' => $total_users_online,
			'mostDate' => time()
		);

	$date = strftime('%Y-%m-%d', forum_time(false));

	// No entry exists for today yet?
	if (!isset($modSettings['mostOnlineUpdated']) || $modSettings['mostOnlineUpdated'] != $date)
	{
		$request = smf_db_query( '
			SELECT most_on
			FROM {db_prefix}log_activity
			WHERE date = {date:date}
			LIMIT 1',
			array(
				'date' => $date,
			)
		);

		// The log_activity hasn't got an entry for today?
		if (mysql_num_rows($request) === 0)
		{
			smf_db_insert('ignore',
				'{db_prefix}log_activity',
				array('date' => 'date', 'most_on' => 'int'),
				array($date, $total_users_online),
				array('date')
			);
		}
		// There's an entry in log_activity on today...
		else
		{
			list ($modSettings['mostOnlineToday']) = mysql_fetch_row($request);

			if ($total_users_online > $modSettings['mostOnlineToday'])
				trackStats(array('most_on' => $total_users_online));

			$total_users_online = max($total_users_online, $modSettings['mostOnlineToday']);
		}
		mysql_free_result($request);

		$settingsToUpdate['mostOnlineUpdated'] = $date;
		$settingsToUpdate['mostOnlineToday'] = $total_users_online;
		$settingsToUpdate['log_online_today'] = '';
	}

	// Highest number of users online today?
	elseif ($total_users_online > $modSettings['mostOnlineToday'])
	{
		trackStats(array('most_on' => $total_users_online));
		$settingsToUpdate['mostOnlineToday'] = $total_users_online;
	}

	if (!empty($settingsToUpdate))
		updateSettings($settingsToUpdate);
}

function determineActions($urls, $preferred_prefix = false)
{
	global $txt, $user_info, $modSettings, $smcFunc, $context;

	if (!allowedTo('who_view'))
		return array();
	loadLanguage('Who');

	// Actions that require a specific permission level.
	$allowedActions = array(
		'admin' => array('moderate_forum', 'manage_membergroups', 'manage_bans', 'admin_forum', 'manage_permissions', 'send_mail', 'manage_attachments', 'manage_smileys', 'manage_boards', 'edit_news'),
		'ban' => array('manage_bans'),
		'boardrecount' => array('admin_forum'),
		'calendar' => array('calendar_view'),
		'editnews' => array('edit_news'),
		'mailing' => array('send_mail'),
		'maintain' => array('admin_forum'),
		'manageattachments' => array('manage_attachments'),
		'manageboards' => array('manage_boards'),
		'mlist' => array('view_mlist'),
		'moderate' => array('access_mod_center', 'moderate_forum', 'manage_membergroups'),
		'optimizetables' => array('admin_forum'),
		'repairboards' => array('admin_forum'),
		'search' => array('search_posts'),
		'search2' => array('search_posts'),
		'setcensor' => array('moderate_forum'),
		'setreserve' => array('moderate_forum'),
		'stats' => array('view_stats'),
		'viewErrorLog' => array('admin_forum'),
		'viewmembers' => array('moderate_forum'),
	);

	if (!is_array($urls))
		$url_list = array(array($urls, $user_info['id']));
	else
		$url_list = $urls;

	// These are done to later query these in large chunks. (instead of one by one.)
	$topic_ids = array();
	$profile_ids = array();
	$board_ids = array();

	$data = array();
	foreach ($url_list as $k => $url)
	{
		// Get the request parameters..
		$actions = @unserialize($url[0]);
		if ($actions === false)
			continue;

		// If it's the admin or moderation center, and there is an area set, use that instead.
		if (isset($actions['action']) && ($actions['action'] == 'admin' || $actions['action'] == 'moderate') && isset($actions['area']))
			$actions['action'] = $actions['area'];

		// Check if there was no action or the action is display.
		if (!isset($actions['action']) || $actions['action'] == 'display')
		{
			// It's a topic!  Must be!
			if (isset($actions['topic']))
			{
				// Assume they can't view it, and queue it up for later.
				$data[$k] = $txt['who_hidden'];
				$topic_ids[(int) $actions['topic']][$k] = $txt['who_topic'];
			}
			// It's a board!
			elseif (isset($actions['board']))
			{
				// Hide first, show later.
				$data[$k] = $txt['who_hidden'];
				$board_ids[$actions['board']][$k] = $txt['who_board'];
			}
			// It's the board index!!  It must be!
			else
				$data[$k] = $txt['who_index'];
		}
		// Probably an error or some goon?
		elseif ($actions['action'] == '')
			$data[$k] = $txt['who_index'];
		// Some other normal action...?
		else
		{
			// Viewing/editing a profile.
			if ($actions['action'] == 'profile')
			{
				// Whose?  Their own?
				if (empty($actions['u']))
					$actions['u'] = $url[1];

				$data[$k] = $txt['who_hidden'];
				$profile_ids[(int) $actions['u']][$k] = $actions['action'] == 'profile' ? $txt['who_viewprofile'] : $txt['who_profile'];
			}
			elseif (($actions['action'] == 'post' || $actions['action'] == 'post2') && empty($actions['topic']) && isset($actions['board']))
			{
				$data[$k] = $txt['who_hidden'];
				$board_ids[(int) $actions['board']][$k] = isset($actions['poll']) ? $txt['who_poll'] : $txt['who_post'];
			}
			// A subaction anyone can view... if the language string is there, show it.
			elseif (isset($actions['sa']) && isset($txt['whoall_' . $actions['action'] . '_' . $actions['sa']]))
				$data[$k] = $preferred_prefix && isset($txt[$preferred_prefix . $actions['action'] . '_' . $actions['sa']]) ? $txt[$preferred_prefix . $actions['action'] . '_' . $actions['sa']] : $txt['whoall_' . $actions['action'] . '_' . $actions['sa']];
			// An action any old fellow can look at. (if ['whoall_' . $action] exists, we know everyone can see it.)
			elseif (isset($txt['whoall_' . $actions['action']]))
				$data[$k] = $preferred_prefix && isset($txt[$preferred_prefix . $actions['action']]) ? $txt[$preferred_prefix . $actions['action']] : $txt['whoall_' . $actions['action']];
			// Viewable if and only if they can see the board...
			elseif (isset($txt['whotopic_' . $actions['action']]))
			{
				// Find out what topic they are accessing.
				$topic = (int) (isset($actions['topic']) ? $actions['topic'] : (isset($actions['from']) ? $actions['from'] : 0));

				$data[$k] = $txt['who_hidden'];
				$topic_ids[$topic][$k] = $txt['whotopic_' . $actions['action']];
			}
			elseif (isset($txt['whopost_' . $actions['action']]))
			{
				// Find out what message they are accessing.
				$msgid = (int) (isset($actions['msg']) ? $actions['msg'] : (isset($actions['quote']) ? $actions['quote'] : 0));

				$result = smf_db_query( '
					SELECT m.id_topic, m.subject
					FROM {db_prefix}messages AS m
						INNER JOIN {db_prefix}boards AS b ON (b.id_board = m.id_board)
						INNER JOIN {db_prefix}topics AS t ON (t.id_topic = m.id_topic' . ($modSettings['postmod_active'] ? ' AND t.approved = {int:is_approved}' : '') . ')
					WHERE m.id_msg = {int:id_msg}
						AND {query_see_board}' . ($modSettings['postmod_active'] ? '
						AND m.approved = {int:is_approved}' : '') . '
					LIMIT 1',
					array(
						'is_approved' => 1,
						'id_msg' => $msgid,
					)
				);
				list ($id_topic, $subject) = mysql_fetch_row($result);
				$data[$k] = sprintf($txt['whopost_' . $actions['action']], $id_topic, $subject);
				mysql_free_result($result);

				if (empty($id_topic))
					$data[$k] = $txt['who_hidden'];
			}
			// Viewable only by administrators.. (if it starts with whoadmin, it's admin only!)
			elseif (allowedTo('moderate_forum') && isset($txt['whoadmin_' . $actions['action']]))
				$data[$k] = $txt['whoadmin_' . $actions['action']];
			// Viewable by permission level.
			elseif (isset($allowedActions[$actions['action']]))
			{
				if (allowedTo($allowedActions[$actions['action']]))
					$data[$k] = $txt['whoallow_' . $actions['action']];
				else
					$data[$k] = $txt['who_hidden'];
			}
			// Unlisted or unknown action.
			else
				$data[$k] = $txt['who_unknown'];
		}
		// Maybe the action is integrated into another system?
		if (count($integrate_actions = HookAPI::callHook('integrate_whos_online', array($actions))) > 0)
		{
			foreach ($integrate_actions as $integrate_action)
			{
				if (!empty($integrate_action))
				{
					$data[$k] = $integrate_action;
					break;
				}
			}
		}
		if(!empty($modSettings['simplesef_enable']))
			SimpleSEF::actionArray($actions);
	}

	// Load topic names.
	if (!empty($topic_ids))
	{
		$result = smf_db_query( '
			SELECT t.id_topic, m.subject
			FROM {db_prefix}topics AS t
				INNER JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
				INNER JOIN {db_prefix}messages AS m ON (m.id_msg = t.id_first_msg)
			WHERE {query_see_board}
				AND t.id_topic IN ({array_int:topic_list})' . ($modSettings['postmod_active'] ? '
				AND t.approved = {int:is_approved}' : '') . '
			LIMIT {int:limit}',
			array(
				'topic_list' => array_keys($topic_ids),
				'is_approved' => 1,
				'limit' => count($topic_ids),
			)
		);
		while ($row = mysql_fetch_assoc($result))
		{
			// Show the topic's subject for each of the actions.
			foreach ($topic_ids[$row['id_topic']] as $k => $session_text)
				$data[$k] = sprintf($session_text, $row['id_topic'], censorText($row['subject']));
		}
		mysql_free_result($result);
	}

	// Load board names.
	if (!empty($board_ids))
	{
		$result = smf_db_query( '
			SELECT b.id_board, b.name
			FROM {db_prefix}boards AS b
			WHERE {query_see_board}
				AND b.id_board IN ({array_int:board_list})
			LIMIT ' . count($board_ids),
			array(
				'board_list' => array_keys($board_ids),
			)
		);
		while ($row = mysql_fetch_assoc($result))
		{
			// Put the board name into the string for each member...
			foreach ($board_ids[$row['id_board']] as $k => $session_text)
				$data[$k] = sprintf($session_text, $row['id_board'], $row['name']);
		}
		mysql_free_result($result);
	}

	// Load member names for the profile.
	if (!empty($profile_ids) && (allowedTo('profile_view_any') || allowedTo('profile_view_own')))
	{
		$result = smf_db_query( '
			SELECT id_member, real_name
			FROM {db_prefix}members
			WHERE id_member IN ({array_int:member_list})
			LIMIT ' . count($profile_ids),
			array(
				'member_list' => array_keys($profile_ids),
			)
		);
		while ($row = mysql_fetch_assoc($result))
		{
			// If they aren't allowed to view this person's profile, skip it.
			if (!allowedTo('profile_view_any') && $user_info['id'] != $row['id_member'])
				continue;

			// Set their action on each - session/text to sprintf.
			foreach ($profile_ids[$row['id_member']] as $k => $session_text)
				$data[$k] = sprintf($session_text, $row['id_member'], $row['real_name']);
		}
		mysql_free_result($result);
	}

	if (!is_array($urls))
		return isset($data[0]) ? $data[0] : false;
	else
		return $data;
}
