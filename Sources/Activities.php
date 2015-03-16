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
 *
 * activity stream main module
 */
if (!defined('SMF'))
	die('No access');

@require_once($sourcedir . '/lib/Subs-Ratings.php');

function aStreamDispatch()
{
	global $sourcedir, $modSettings;

	$xml = isset($_REQUEST['xml']) ? true : false;
	if(!$modSettings['astream_active']) {
		if(!$xml)
                    redirectexit();
		else
                    obExit(false);
	}
	require_once($sourcedir . '/lib/Subs-Activities.php');
	$sub_actions = array(
		'get' => array('function' => 'aStreamGetStream'),
		'add' => array('function' => 'aStreamAdd'),
		'notifications' => array('function' => 'aStreamGetNotifications'),
		'markread' => array('function' => 'aStreamMarkNotificationRead')
	);
	if (!isset($_REQUEST['sa'], $sub_actions[$_REQUEST['sa']]))
		fatal_lang_error('no_access', false);

	$sub_actions[$_REQUEST['sa']]['function']();
}

/**
 * get the notifications for the current user.
 *
 * $_REQUEST['view'] tells us what to get:
 * a) 'recent' (default) - the most recent and unread notifications
 * b) 'unread' - all unread notifications
 * c) 'all' - everything (this is for the profile page mainly).
 *
 * since notifications are basically references into the activity stream, they are pruned
 * together with actitvities. default activity expiration is 30 days.
 */
function aStreamGetNotifications()
{
	global $user_info, $context, $scripturl;

	if($user_info['is_guest'])				// guests don't get anything, they can't have notifications
		fatal_lang_error('no_access');

	$xml = isset($_REQUEST['xml']) ? true : false;
	$view = isset($_REQUEST['view']) ? $_REQUEST['view'] : 'recent';

	//loadTemplate('Activities');
	loadLanguage('Activities');
	$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;

	$context['get_notifications'] = true;
	$context['rich_output'] = true;		// todo: this indicates whether we want simple or rich activity bits (rich = with avatar and possibly other member data)

	$where = 'WHERE n.id_member = {int:id_member} AND ' . (!empty($user_info['ignoreusers']) ? 'a.id_member NOT IN({array_int:ignoredusers}) AND ' : '')  . ' ({query_wanna_see_board} OR a.id_board = 0) ';
	if($view != 'all') {
		$where .= ' AND n.unread = 1 ';
		$context['view_all'] = false;
	}
	else
		$context['view_all'] = true;
	$result = smf_db_query('SELECT n.id_act, n.unread, a.id_member, a.updated, a.id_type, a.params, a.is_private, a.id_board, a.id_topic, a.id_content, a.id_owner, t.*, b.name AS board_name 
		FROM {db_prefix}log_notifications AS n
		LEFT JOIN {db_prefix}log_activities AS a ON (a.id_act = n.id_act)
		LEFT JOIN {db_prefix}activity_types AS t ON (t.id_type = a.id_type)
		LEFT JOIN {db_prefix}boards AS b ON(b.id_board = a.id_board) '.
		$where . 'ORDER BY n.id_act DESC LIMIT {int:start}, 20',
		array('id_member' => $user_info['id'], 'start' => $start, 'ignoredusers' => $user_info['ignoreusers']));

	aStreamOutput($result, true);
	Eos_Smarty::loadTemplate($xml ? 'astream/notification_popup' : 'astream/notification_full');

	$context['unread_pm'] = $user_info['unread_messages'];
	$context['pmlink'] = URL::parse($scripturl . '?action=pm');
	$context['modlink'] = URL::parse($scripturl . '?action=moderate;area=reports');
	/*
	 * this hook allows plugins to extend the notification popup
	 */
	HookAPI::callHook('astream_notification_popup');
}

/**
 * @return void
 *
 * marks one ore more notifications as read
 */
function aStreamMarkNotificationRead()
{
	global $user_info;

	$xml = isset($_REQUEST['xml']) ? true : false;
	if($user_info['is_guest'])
		return;

	if(isset($_REQUEST['act'])) {
		$new_act_ids = array();
		if($_REQUEST['act'] === 'all') {
			$where = 'id_member = {int:id_member}';
			$markallread = true;
		}
		else {
			$act_ids = explode(',', $_REQUEST['act']);
			foreach($act_ids as $act) {
				if((int)$act > 0)
					$new_act_ids[] = (int)$act;
			}
			$new_act = join(',', $new_act_ids);
			$where = 'id_member = {int:id_member} AND id_act IN('.$new_act.')';
			$markallread = false;
		}

		if($markallread || count($new_act_ids) > 0) {
			$query = 'UPDATE {db_prefix}log_notifications SET unread = 0 WHERE ' . $where;
			smf_db_query($query,
				array('id_member' => $user_info['id']));
			invalidateMemberData($user_info['id']);
		}

		if($xml) {		// construct xml response for the JavaScript markread handler
			header('Content-Type: text/xml; charset=UTF-8');
			echo '<', '?xml version="1.0" encoding="UTF-8', '"?', '>
			<response>';
			if($markallread)
				echo '
				<markedread name="markedread"><![CDATA[all]]></markedread>
				';
			else {
		 		foreach($new_act_ids as $act)
					echo '
					<markedread name="markedread"><![CDATA[',$act,']]></markedread>
					';
			}
			echo '
			</response>
			';
			obExit(false);
		}
	}
	redirectexit();
}
/**
 * dispatch the get sub-action. Right now, it is possible to retrieve the
 * activity stream for a board, a topic, a user or recent list of global
 * activities. More types might be added later.
 */
function aStreamGetStream()
{
	global $context;

	$xml = isset($_REQUEST['xml']) ? true : false;
	loadLanguage('Activities');

	$board = isset($_REQUEST['b']) ? $_REQUEST['b'] : 0;
	$topic = isset($_REQUEST['t']) ? $_REQUEST['t'] : 0;

	$context['rich_output'] = true;		// todo: this indicates whether we want simple or rich activity bits (rich = with avatar)
	if((int)$board > 0 || isset($_REQUEST['all']))
		aStreamGet((int)$board, $xml, isset($_REQUEST['all']) ? true : false);
	else if($topic)
		aStreamGetForTopic($topic, $xml);

	Eos_Smarty::loadTemplate($xml ? 'astream/astream_xml' : 'astream/astream_full');
}

function aStreamGet($b = 0, $xml = false, $global = false)
{
	global $board, $context, $user_info, $modSettings, $options, $scripturl;

	if(!isset($board) || !$board)
		$board = $b;

	$start = isset($_REQUEST['start']) ? (int)$_REQUEST['start'] : 0;
	$context['xml'] = $xml;
	$context['act_global'] = false;
	$total = 0;
	$context['sef_full_rewrite'] = true;
	
	$perpage = $xml ? 15 : (empty($modSettings['disableCustomPerPage']) && !empty($options['topics_per_page']) ? $options['topics_per_page'] : $modSettings['defaultMaxTopics']);
	
	if($user_info['is_admin'])
		$pquery = ' AND (a.is_private <= ' . ACT_PLEVEL_ADMIN . ' OR a.id_member = {int:id_user} OR a.id_owner = {int:id_user}) ';
	else
		$pquery = ' AND (a.is_private = 0 OR a.id_member = {int:id_user} OR a.id_owner = {int:id_user}) ';

	$filterby = '';
	if(isset($_REQUEST['filter'])) {
		$filterby = normalizeCommaDelimitedList($_REQUEST['filter']);
		if(strlen($filterby))
			$pquery .= ' AND a.id_type IN({string:filter})';
	}
	$uquery = '';
	if(isset($_REQUEST['u']) && (int)$_REQUEST['u'] > 0)
		$uquery .= 'a.id_member = {int:id_user} AND ';
	else
		$uquery = (!empty($user_info['ignoreusers']) ? 'a.id_member NOT IN({array_int:ignoredusers}) AND ' : '');

	if($global) {
		if(!$xml) {
			$result = smf_db_query('SELECT COUNT(a.id_act) FROM {db_prefix}log_activities AS a
				LEFT JOIN {db_prefix}boards AS b ON(b.id_board = a.id_board)
				WHERE ' . $uquery . ' ({query_wanna_see_board} OR a.id_board = 0)'.$pquery,
				array('start' => 0, 'id_user' => $user_info['id'], 'filter' => $filterby, 'perpage' => $perpage, 'ignoredusers' => $user_info['ignoreusers']));
			
			list($total) = mysql_fetch_row($result);
			mysql_free_result($result);
		}
		$result = smf_db_query('SELECT a.*, t.*, b.name AS board_name FROM {db_prefix}log_activities AS a
			LEFT JOIN {db_prefix}activity_types AS t ON (t.id_type = a.id_type)
			LEFT JOIN {db_prefix}boards AS b ON(b.id_board = a.id_board)
			WHERE ' . $uquery . ' ({query_wanna_see_board} OR a.id_board = 0)'.$pquery.' ORDER BY a.id_act DESC LIMIT {int:start}, {int:perpage}',
			array('start' => $start, 'id_user' => $user_info['id'], 'filter' => $filterby, 'perpage' => $perpage, 'ignoredusers' => $user_info['ignoreusers']));

		$context['act_global'] = true;
		$context['viewall_url'] = URL::parse($scripturl . '?action=astream;sa=get;all');
	}
	else {
		if(!$xml) {
			$result = smf_db_query('SELECT COUNT(a.id_act) FROM {db_prefix}log_activities AS a
				LEFT JOIN {db_prefix}boards AS b ON(b.id_board = a.id_board)
				WHERE ' . $uquery . ' a.id_board = {int:id_board} AND {query_wanna_see_board} '.$pquery,
				array('id_board' => $board, 'start' => 0, 'id_user' => $user_info['id'], 'filter' => $filterby, 'perpage' => $perpage, 'ignoredusers' => $user_info['ignoreusers']));

			list($total) = mysql_fetch_row($result);
			mysql_free_result($result);
		}
		$result = smf_db_query('SELECT a.*, t.*, b.name AS board_name FROM {db_prefix}log_activities AS a
			LEFT JOIN {db_prefix}activity_types AS t ON (t.id_type = a.id_type)
			LEFT JOIN {db_prefix}boards AS b ON(b.id_board = a.id_board)
			WHERE ' . $uquery . ' a.id_board = {int:id_board} AND {query_wanna_see_board}'.$pquery.' ORDER BY a.id_act DESC LIMIT {int:start}, {int:perpage}',
			array('id_board' => $board, 'start' => $start, 'id_user' => $user_info['id'], 'filter' => $filterby, 'perpage' => $perpage, 'ignoredusers' => $user_info['ignoreusers']));
		
		$context['viewall_url'] = URL::parse($scripturl . '?action=astream;sa=get;b=' . $board);
	}
	$pages_base = URL::parse($scripturl . '?action=astream;sa=get;all;');
	$pages_base = URL::addParam($pages_base, 'start=%1$d', true);
	$context['pages'] = $total ? constructPageIndex($pages_base, $start, $total, $perpage, true) : '';
	if($xml)
		header('Content-Type: text/xml; charset=UTF-8');
	aStreamOutput($result);
}

function aStreamGetForTopic($t = 0, $xml = false)
{
	global $context;

	$context['xml'] = $xml;
	$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;

	$result = smf_db_query('
		SELECT a.*, t.*, b.name AS board_name FROM {db_prefix}log_activities AS a
		LEFT JOIN {db_prefix}activity_types AS t ON (t.id_type = a.id_type)
		LEFT JOIN {db_prefix}boards AS b ON(b.id_board = a.id_board)
		WHERE {query_see_board} AND a.id_topic = {int:id_topic} ORDER BY a.updated DESC LIMIT {int:start}, 20',
		array('id_topic' => $t, 'start' => $start));

	aStreamOutput($result);
}

/**
 * @param $memID		int member id
 *
 * show activities, notifications and settings on the profile page. The latter two
 * are only handled when viewing your own profile.
 *
 * This is here because Profile-View.php is already big enough :)
 */
function showActivitiesProfile($memID)
{
  	global $context, $user_info, $sourcedir, $user_profile, $txt, $modSettings, $options;

	$context['user']['is_owner'] = $memID == $user_info['id'];
	require_once($sourcedir . '/lib/Subs-Activities.php');
	Eos_Smarty::loadTemplate('profile/profile_base');

	loadLanguage('Activities');

	$sa = isset($_GET['sa']) ? $_GET['sa'] : 'activities';
	$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;

	if($sa == 'settings')
		return(showActivitiesProfileSettings($memID));
	
	Eos_Smarty::getConfigInstance()->registerHookTemplate('profile_content_area', 'profile/activities_display');
	$context['page_title'] = $txt['showActivities'] . ' - ' . $user_profile[$memID]['real_name'];
	$context['pageindex_multiplier'] = commonAPI::getMessagesPerPage();
	$context['act_results'] = 0;
	$context['rich_output'] = true;

	$context[$context['profile_menu_name']]['tab_data'] = array(
		'title' => $txt['showActivities'],
		'description' => $txt['showActivitiesMenu'],
		'tabs' => array(
		),
	);

	// these areas cannot be visited if it's not our own profile unless you are the mighty one
	if(($sa == 'notifications' || $sa == 'settings') && !$context['user']['is_owner'] && !$user_info['is_admin'])
		fatal_lang_error('no_access');

	$result = smf_db_query('
		SELECT a.*, t.*, b.name AS board_name FROM {db_prefix}log_activities AS a
		LEFT JOIN {db_prefix}activity_types AS t ON (t.id_type = a.id_type)
		LEFT JOIN {db_prefix}boards AS b ON(b.id_board = a.id_board)
		WHERE ({query_see_board} OR a.id_board = 0) AND (a.id_member = {int:id_user} OR a.id_owner = {int:id_user}) ORDER BY a.id_act DESC LIMIT {int:start}, 20',
		array('start' => $start, 'id_user' => $memID));

	$context['act_global'] = true;

	aStreamOutput($result);
	$context['titletext'] = $context['page_title'];
}

/**
 * @param $memID	int member ID
 * 
 * show the settings to customize opt-outs for activity entries and notifications
 * to receive.
 * 
 * todo: we need to find a way to filter out notifications that are for
 * admins/mods only. probably needs a db scheme change...
 */
function showActivitiesProfileSettings($memID)
{
	global $modSettings, $context, $user_info, $txt, $user_profile, $scripturl;
	
	loadLanguage('Activities-Profile');
	if(empty($modSettings['astream_active']) || ($user_info['id'] != $memID && !$user_info['is_admin']))
		fatal_lang_error ('no_access');

	Eos_Smarty::getConfigInstance()->registerHookTemplate('profile_content_area', 'profile/astream_settings');
	$context['submiturl'] = $scripturl . '?action=profile;area=activities;sa=settings;save;u=' . $memID;
	
	$context['page_title'] = $txt['showActivities'] . ' - ' . $user_profile[$memID]['real_name'];

	$context[$context['profile_menu_name']]['tab_data'] = array(
		'title' => $txt['showActivitiesSettings'],
		'description' => $txt['showActivitiesSettings_desc'],
		'tabs' => array(
		),
	);
	
	$result = smf_db_query('SELECT * FROM {db_prefix}activity_types ORDER BY id_type ASC');
	
	if($user_info['id'] == $memID) {
		$my_act_optout = empty($user_info['act_optout']) ? array(0) : explode(',', $user_info['act_optout']);
		$my_notify_optout = empty($user_info['notify_optout']) ? array(0) : explode(',', $user_info['notify_optout']);
	}
	else {
		loadMemberData($memID, false, 'minimal');
		$my_act_optout = empty($user_profile[$memID]['act_optout']) ? array(0) : explode(',', $user_profile[$memID]['act_optout']);
		$my_notify_optout = empty($user_profile[$memID]['notify_optout']) ? array(0) : explode(',', $user_profile[$memID]['notify_optout']);
	}
	$context['activity_types'] = array();
	
	while($row = mysql_fetch_assoc($result)) {
		$context['activity_types'][] = array(
			'id' => $row['id_type'],
			'shortdesc' => $row['id_desc'],
			'longdesc_act' => $txt['actdesc_' . trim($row['id_desc'])],
			'longdesc_not' => isset($txt['ndesc_' . trim($row['id_desc'])]) ? $txt['ndesc_' . trim($row['id_desc'])] : '',
			'act_optout' => in_array($row['id_type'], $my_act_optout),
			'notify_optout' => in_array($row['id_type'], $my_notify_optout),
		);
	}
	mysql_free_result($result);
	
	if (isset($_GET['save'])) {
		$new_not_optout = array();
		$new_act_optout = array();
		$update_array = array();
		
		foreach($context['activity_types'] as $t) {
			$_id = trim($t['id']);
			if(!empty($t['longdesc_act']) && (!isset($_REQUEST['act_check_' . $_id]) || empty($_REQUEST['act_check_' . $_id])))
				$new_act_optout[] = $_id;
			if(!empty($t['longdesc_not']) && (!isset($_REQUEST['not_check_' . $_id]) || empty($_REQUEST['not_check_' . $_id])))
				$new_not_optout[] = $_id;
		}
		//if(count(array_unique($new_act_optout)) > 0)
			$update_array['act_optout'] = implode(',', array_unique($new_act_optout));
		//if(count(array_unique($new_not_optout)) > 0)
			$update_array['notify_optout'] = implode(',', array_unique($new_not_optout));

		if(count($update_array))
			updateMemberData($memID, $update_array);
		redirectexit($scripturl . '?action=profile;area=activities;sa=settings;u=' . $memID);
	}
}
