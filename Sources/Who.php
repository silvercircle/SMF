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

/*	This file is mainly concerned, or that is to say only concerned, with the
	Who's Online list.  It contains only the following functions:

	void Who()
		- prepares the who's online data for the Who template.
		- uses the Who template (main sub template.) and language file.
		- requires the who_view permission.
		- is enabled with the who_enabled setting.
		- is accessed via ?action=who.

	array determineActions(array urls, string preferred_prefix = false)
		- determine the actions of the members passed in urls.
		- urls should be a single url (string) or an array of arrays, each
		  inner array being (serialized request data, id_member).
		- returns an array of descriptions if you passed an array, otherwise
		  the string describing their current location.

	void Credits(bool in_admin)
		- prepares credit and copyright information for the credits page or the admin page
		- if parameter is true the it will not load the sub template nor the template file

	Adding actions to the Who's Online list:
	---------------------------------------------------------------------------
		Adding actions to this list is actually relatively easy....
		- for actions anyone should be able to see, just add a string named
		   whoall_ACTION.  (where ACTION is the action used in index.php.)
		- for actions that have a subaction which should be represented
		   differently, use whoall_ACTION_SUBACTION.
		- for actions that include a topic, and should be restricted, use
		   whotopic_ACTION.
		- for actions that use a message, by msg or quote, use whopost_ACTION.
		- for administrator-only actions, use whoadmin_ACTION.
		- for actions that should be viewable only with certain permissions,
		   use whoallow_ACTION and add a list of possible permissions to the
		   $allowedActions array, using ACTION as the key.
*/

// Who's online, and what are they doing?
function Who()
{
	global $context, $scripturl, $user_info, $txt, $modSettings, $memberContext, $sourcedir;

	// Permissions, permissions, permissions.
	isAllowedTo('who_view');
	require_once($sourcedir . '/lib/Subs-MembersOnline.php');
	$context['sef_full_rewrite'] = true;

	// You can't do anything if this is off.
	if (empty($modSettings['who_enabled']))
		fatal_lang_error('who_off', false);

	// Load the 'Who' template.
	//loadTemplate('Who');
	loadLanguage('Who');
	EoS_Smarty::loadTemplate('who');
	// Sort out... the column sorting.
	$sort_methods = array(
		'user' => 'mem.real_name',
		'time' => 'lo.log_time'
	);

	$show_methods = array(
		'members' => '(lo.id_member != 0)',
		'guests' => '(lo.id_member = 0)',
		'all' => '1=1',
	);

	// Store the sort methods and the show types for use in the template.
	$context['sort_methods'] = array(
		'user' => $txt['who_user'],
		'time' => $txt['who_time'],
	);
	$context['show_methods'] = array(
		'all' => $txt['who_show_all'],
		'members' => $txt['who_show_members_only'],
		'guests' => $txt['who_show_guests_only'],
	);

	// Can they see spiders too?
	if (!empty($modSettings['show_spider_online']) && ($modSettings['show_spider_online'] == 2 || allowedTo('admin_forum')) && !empty($modSettings['spider_name_cache']))
	{
		$show_methods['spiders'] = '(lo.id_member = 0 AND lo.id_spider > 0)';
		$show_methods['guests'] = '(lo.id_member = 0 AND lo.id_spider = 0)';
		$context['show_methods']['spiders'] = $txt['who_show_spiders_only'];
	}

	// Does the user prefer a different sort direction?
	if (isset($_REQUEST['sort']) && isset($sort_methods[$_REQUEST['sort']]))
	{
		$context['sort_by'] = $_SESSION['who_online_sort_by'] = $_REQUEST['sort'];
		$sort_method = $sort_methods[$_REQUEST['sort']];
	}
	// Did we set a preferred sort order earlier in the session?
	elseif (isset($_SESSION['who_online_sort_by']))
	{
		$context['sort_by'] = $_SESSION['who_online_sort_by'];
		$sort_method = $sort_methods[$_SESSION['who_online_sort_by']];
	}
	// Default to last time online.
	else
	{
		$context['sort_by'] = $_SESSION['who_online_sort_by'] = 'time';
		$sort_method = 'lo.log_time';
	}

	$context['sort_direction'] = isset($_REQUEST['asc']) || (isset($_REQUEST['sort_dir']) && $_REQUEST['sort_dir'] == 'asc') ? 'up' : 'down';

	$conditions = array();
	if (!allowedTo('moderate_forum'))
		$conditions[] = '(IFNULL(mem.show_online, 1) = 1)';

	// Fallback to top filter?
	if (isset($_REQUEST['submit_top']) && isset($_REQUEST['show_top']))
		$_REQUEST['show'] = $_REQUEST['show_top'];
	// Does the user wish to apply a filter?
	if (isset($_REQUEST['show']) && isset($show_methods[$_REQUEST['show']]))
	{
		$context['show_by'] = $_SESSION['who_online_filter'] = $_REQUEST['show'];
		$conditions[] = $show_methods[$_REQUEST['show']];
	}
	// Perhaps we saved a filter earlier in the session?
	elseif (isset($_SESSION['who_online_filter']))
	{
		$context['show_by'] = $_SESSION['who_online_filter'];
		$conditions[] = $show_methods[$_SESSION['who_online_filter']];
	}
	else
		$context['show_by'] = $_SESSION['who_online_filter'] = 'all';

	// Get the total amount of members online.
	$request = smf_db_query( '
		SELECT COUNT(*)
		FROM {db_prefix}log_online AS lo
			LEFT JOIN {db_prefix}members AS mem ON (lo.id_member = mem.id_member)' . (!empty($conditions) ? '
		WHERE ' . implode(' AND ', $conditions) : ''),
		array(
		)
	);
	list ($totalMembers) = mysql_fetch_row($request);
	mysql_free_result($request);

	// Prepare some page index variables.
	$context['page_index'] = constructPageIndex($scripturl . '?action=who;sort=' . $context['sort_by'] . ($context['sort_direction'] == 'up' ? ';asc' : '') . ';show=' . $context['show_by'], $_REQUEST['start'], $totalMembers, $modSettings['defaultMaxMembers']);
	$context['start'] = $_REQUEST['start'];

	// Look for people online, provided they don't mind if you see they are.
	$request = smf_db_query( '
		SELECT
			lo.log_time, lo.id_member, lo.url, INET_NTOA(lo.ip) AS ip, mem.real_name,
			lo.session, mg.online_color, IFNULL(mem.show_online, 1) AS show_online,
			lo.id_spider
		FROM {db_prefix}log_online AS lo
			LEFT JOIN {db_prefix}members AS mem ON (lo.id_member = mem.id_member)
			LEFT JOIN {db_prefix}membergroups AS mg ON (mg.id_group = CASE WHEN mem.id_group = {int:regular_member} THEN mem.id_post_group ELSE mem.id_group END)' . (!empty($conditions) ? '
		WHERE ' . implode(' AND ', $conditions) : '') . '
		ORDER BY {raw:sort_method} {raw:sort_direction}
		LIMIT {int:offset}, {int:limit}',
		array(
			'regular_member' => 0,
			'sort_method' => $sort_method,
			'sort_direction' => $context['sort_direction'] == 'up' ? 'ASC' : 'DESC',
			'offset' => $context['start'],
			'limit' => $modSettings['defaultMaxMembers'],
		)
	);
	$context['members'] = array();
	$member_ids = array();
	$url_data = array();
	while ($row = mysql_fetch_assoc($request))
	{
		$actions = @unserialize($row['url']);
		if ($actions === false)
			continue;

		// Send the information to the template.
		$context['members'][$row['session']] = array(
			'id' => $row['id_member'],
			'ip' => allowedTo('moderate_forum') ? $row['ip'] : '',
			// It is *going* to be today or yesterday, so why keep that information in there?
			'time' => strtr(timeformat($row['log_time']), array($txt['today'] => '', $txt['yesterday'] => '')),
			'timestamp' => forum_time(true, $row['log_time']),
			'query' => $actions,
			'is_hidden' => $row['show_online'] == 0,
			'id_spider' => $row['id_spider'],
			'id_unique' => base64_encode($row['session']),
			'color' => empty($row['online_color']) ? '' : $row['online_color']
		);
		$url = @unserialize($row['url']);
		$context['members'][$row['session']]['user_agent'] = isset($url['USER_AGENT']) ? $url['USER_AGENT'] : '';
		$context['members'][$row['session']]['user_agent_short'] = isset($url['USER_AGENT']) ? shorten_subject(substr($url['USER_AGENT'], 0, strpos($url['USER_AGENT'], ' ')), 25) : '';

		$url_data[$row['session']] = array($row['url'], $row['id_member']);
		$member_ids[] = $row['id_member'];
	}
	mysql_free_result($request);

	// Load the user data for these members.
	loadMemberData($member_ids);

	// Load up the guest user.
	$memberContext[0] = array(
		'id' => 0,
		'name' => $txt['guest_title'],
		'group' => $txt['guest_title'],
		'href' => '',
		'link' => $txt['guest_title'],
		'email' => $txt['guest_title'],
		'is_guest' => true
	);

	// Are we showing spiders?
	$spiderContext = array();
	if (!empty($modSettings['show_spider_online']) && ($modSettings['show_spider_online'] == 2 || allowedTo('admin_forum')) && !empty($modSettings['spider_name_cache']))
	{
		foreach (unserialize($modSettings['spider_name_cache']) as $id => $name)
			$spiderContext[$id] = array(
				'id' => 0,
				'name' => $name,
				'group' => $txt['spiders'],
				'href' => '',
				'link' => $name,
				'email' => $name,
				'is_guest' => true
			);
	}

	$url_data = determineActions($url_data);

	// Setup the linktree and page title (do it down here because the language files are now loaded..)
	$context['page_title'] = $txt['who_title'];
	$context['linktree'][] = array(
		'url' => URL::action($scripturl . '?action=who'),
		'name' => $txt['who_title']
	);

	// Put it in the context variables.
	foreach ($context['members'] as $i => $member)
	{
		if ($member['id'] != 0)
			$member['id'] = loadMemberContext($member['id']) ? $member['id'] : 0;

		// Keep the IP that came from the database.
		$memberContext[$member['id']]['ip'] = $member['ip'];
		$context['members'][$i]['action'] = isset($url_data[$i]) ? $url_data[$i] : $txt['who_hidden'];
		if ($member['id'] == 0 && isset($spiderContext[$member['id_spider']]))
			$context['members'][$i] += $spiderContext[$member['id_spider']];
		else
			$context['members'][$i] += $memberContext[$member['id']];
	}

	// Some people can't send personal messages...
	$context['can_send_pm'] = allowedTo('pm_send');

	// any profile fields disabled?
	$context['disabled_fields'] = isset($modSettings['disabled_profile_fields']) ? array_flip(explode(',', $modSettings['disabled_profile_fields'])) : array();

}

function Credits($in_admin = false)
{
	global $context, $modSettings, $forum_copyright, $forum_version, $boardurl, $txt, $user_info;

	// Don't blink. Don't even blink. Blink and you're dead.
	loadLanguage('Who');

	$context['credits_intro'] = '<strong><span style="color:blue;">EosAlpha BBS</span></strong> is a software product based on the popular and successful <a href="http://www.simplemachines.org">Simple Machines Forum</a>, also known as simply SMF.<br>
	It started around mid-2011 as a fork of the then current code base of SMF 2.0 which had been released under a OSF compliant <a href="http://www.simplemachines.org/about/smf/license.php">BSD-style license</a> in June 2011. The goal is to
	create a new forum software with a fresh, modern and social touch.
	<br>
	<br>
	<dl><dt><strong>Developers</strong></dt>
	<dd>Alex "Silvercircle" Vie, Annika "Velvet" Ostrovsky</dd></dl>';
	$context['credits'] = array(
		array(
			'pretext' => $txt['credits_intro'],
			'title' => $txt['credits_team'],
			'groups' => array(
				array(
					'title' => $txt['credits_groups_ps'],
					'members' => array(
						'Michael &quot;Oldiesmann&quot; Eshom',
						'Amacythe',
						'Jeremy &quot;SleePy&quot; Darwood',
						'Justin &quot;metallica48423&quot; O\'Leary',
					),
				),
				array(
					'title' => $txt['credits_groups_dev'],
					'members' => array(
						'Norv',
						'Aaron van Geffen',
						'Antechinus',
						'Bjoern &quot;Bloc&quot; Kristiansen',
						'Hendrik Jan &quot;Compuart&quot; Visser',
						'Juan &quot;JayBachatero&quot; Hernandez',
						'Karl &quot;RegularExpression&quot; Benson',
						$user_info['is_admin'] ? 'Matt &quot;Grudge&quot; Wolf': 'Grudge',
						'Michael &quot;Thantos&quot; Miller',
						'Selman &quot;[SiNaN]&quot; Eser',
						'Theodore &quot;Orstio&quot; Hildebrandt',
						'Thorsten &quot;TE&quot; Eurich',
						'winrules',
					),
				),
				array(
					'title' => $txt['credits_groups_support'],
					'members' => array(
						'JimM',
						'Adish &quot;(F.L.A.M.E.R)&quot; Patel',
						'Aleksi &quot;Lex&quot; Kilpinen',
						'Ben Scott',
						'Bigguy',
						'CapadY',
						'Chas Large',
						'Duncan85',
						'Eliana Tamerin',
						'Fiery',
						'gbsothere',
						'Harro',
						'Huw',
						'Jan-Olof &quot;Owdy&quot; Eriksson',
						'Jeremy &quot;jerm&quot; Strike',
						'Jessica &quot;Miss All Sunday&quot; Gonzales',
						'K@',
						'Kevin &quot;greyknight17&quot; Hou',
						'KGIII',
						'Kill Em All',
						'Mattitude',
						'Mashby',
						'Mick G.',
						'Michele &quot;Illori&quot; Davis',
						'MrPhil',
						'Nick &quot;Fizzy&quot; Dyer',
						'Nick &quot;Ha&sup2;&quot;',
						'Paul_Pauline',
						'Piro &quot;Sarge&quot; Dhima',
						'Rumbaar',
						'Pitti',
						'RedOne',
						'S-Ace',
						'Wade &quot;s&eta;&sigma;&omega;&quot; Poulsen',
						'xenovanis',
					),
				),
				array(
					'title' => $txt['credits_groups_customize'],
					'members' => array(
						'Brad &quot;IchBin&trade;&quot; Grow',
						'&#12487;&#12451;&#12531;1031',
						'Brannon &quot;B&quot; Hall',
						'Bryan &quot;Runic&quot; Deakin',
						'Bulakbol',
						'Colin &quot;Shadow82x&quot; Blaber',
						'Daniel15',
						'Eren Yasarkurt',
						'Gary M. Gadsdon',
						'Jason &quot;JBlaze&quot; Clemons',
						'Jerry',
						'Jonathan &quot;vbgamer45&quot; Valentin',
						'Kays',
						'Killer Possum',
						'Kirby',
						'Matt &quot;SlammedDime&quot; Zuba',
						'Matthew &quot;Labradoodle-360&quot; Kerle',
						'Nibogo',
						'Niko',
						'Peter &quot;Arantor&quot; Spicer',
						'snork13',
						'Spuds',
						'Steven &quot;Fustrate&quot; Hoffman',
						'Joey &quot;Tyrsson&quot; Smith',
					),
				),
				array(
					'title' => $txt['credits_groups_docs'],
					'members' => array(
						'Joshua &quot;groundup&quot; Dickerson',
						'AngellinaBelle',
						'Daniel Diehl',
						'Dannii Willis',
						'emanuele',
						'Graeme Spence',
						'Jack &quot;akabugeyes&quot; Thorsen',
						'Jade Elizabeth Trainor',
						'Peter Duggan',
					),
				),
				array(
					'title' => $txt['credits_groups_marketing'],
					'members' => array(
						'Kindred',
						'Marcus &quot;c&sigma;&sigma;&#1082;&iota;&#1108; &#1084;&sigma;&eta;&#1109;&#1090;&#1108;&#1103;&quot; Forsberg',
						'Ralph &quot;[n3rve]&quot; Otowo',
						'rickC',
						'Tony Reid',
					),
				),
				array(
					'title' => $txt['credits_groups_internationalizers'],
					'members' => array(
						'Relyana',
						'Akyhne',
						'GravuTrad',
					),
				),
				array(
					'title' => $txt['credits_groups_servers'],
					'members' => array(
						'Derek Schwab',
						'Liroy &quot;CoreISP&quot; van Hoewijk',
					),
				),
			),
		),
	);

	// Give the translators some credit for their hard work.
	if (!empty($txt['translation_credits']))
		$context['credits'][] = array(
			'title' => $txt['credits_groups_translation'],
			'groups' => array(
				array(
					'title' => $txt['credits_groups_translation'],
					'members' => $txt['translation_credits'],
				),
			),
		);

	$context['credits'][] = array(
		'title' => $txt['credits_special'],
		'posttext' => $txt['credits_anyone'],
		'groups' => array(
			array(
				'title' => $txt['credits_groups_consultants'],
				'members' => array(
					'Brett Flannigan',
					'Mark Rose',
					'Ren&eacute;-Gilles &quot;Nao &#23578;&quot; Deberdt',
				),
			),
			array(
				'title' => $txt['credits_groups_beta'],
				'members' => array(
					$txt['credits_beta_message'],
				),
			),
			array(
				'title' => $txt['credits_groups_translators'],
				'members' => array(
					$txt['credits_translators_message'],
				),
			),
			array(
				'title' => $txt['credits_groups_founder'],
				'members' => array(
					'Unknown W. &quot;[Unknown]&quot; Brackets',
				),
			),
			array(
				'title' => $txt['credits_groups_orignal_pm'],
				'members' => array(
					'Jeff Lewis',
					'Joseph Fung',
					'David Recordon',
				),
			),
		),
	);

	$context['copyrights'] = array(
		'smf' => sprintf($forum_copyright, $forum_version),
		'mods' => array(
		),
	);

	foreach($context['credits'] as &$credit) {
		foreach($credit['groups'] as &$group) {
			if(count($group['members']) > 2)
				$group['last_peep'] = array_pop($group['members']);
		}
	}
	if (!$in_admin)
	{
		EoS_Smarty::loadTemplate('credits');

		$context['robot_no_index'] = true;
		$context['page_title'] = $txt['credits'];
	}
}

?>