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
			$link = '<a attr-mid="'.$row['id_member'].'" class="member '.$class.'" href="' . $href . '">' . $row['real_name'] . '</a>';

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

?>