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
 *
 * activity stream main module
 */

function aStreamDispatch()
{
	global $context, $sourcedir;

	$xml = isset($_REQUEST['xml']) ? true : false;
	if(!in_array('as', $context['admin_features'])) {
		if(!$xml)
	    	redirectexit();
		else
			obExit(false);
	}

	require_once($sourcedir . '/Subs-Activities.php');
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
	global $user_info, $context;

	if($user_info['is_guest'])				// guests don't get anything, they can't have notifications
		fatal_lang_error('no_access');

	$xml = isset($_REQUEST['xml']) ? true : false;
	$view = isset($_REQUEST['view']) ? $_REQUEST['view'] : 'recent';

	loadTemplate('Activities');
	loadLanguage('Activities');
	$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;

	$context['get_notifications'] = true;
	$context['rich_output'] = true;		// todo: this indicates whether we want simple or rich activity bits (rich = with avatar)

	$where = 'WHERE n.id_member = {int:id_member} AND ({query_see_board} OR a.id_board = 0) ';

	$result = smf_db_query('
		SELECT n.id_act, a.id_member, a.updated, a.id_type, a.params, a.is_private, a.id_board, a.id_topic, a.id_content, a.id_owner, t.*, b.name AS board_name FROM {db_prefix}log_notifications AS n
		LEFT JOIN {db_prefix}log_activities AS a ON (a.id_act = n.id_act)
		LEFT JOIN {db_prefix}activity_types AS t ON (t.id_type = a.id_type)
		LEFT JOIN {db_prefix}boards AS b ON(b.id_board = a.id_board) '.
		$where .'AND n.unread = 1 ORDER BY n.id_act DESC LIMIT {int:start}, 20',
		array('id_member' => $user_info['id'], 'start' => $start));

	aStreamOutput($result);

	if($xml) {
		$context['template_layers'] = array();
		$context['sub_template'] = 'notifications_xml';
	}
	else
		$context['sub_template'] = 'notifications';
}

/**
 * @return void
 *
 * marks one ore more notifications as read
 */
function aStreamMarkNotificationRead()
{
	global $context, $user_info;

	if($user_info['is_guest'])
		return;

	$new_act_ids = array();
	if(isset($_REQUEST['act'])) {
		if($_REQUEST['act'] === 'all')
			$where = 'WHERE id_member = {int:id_member}';
		else {
			$act_ids = explode(',', $_REQUEST['act']);
			foreach($act_ids as $act)
				$new_act_ids[] = (int)$act;
			$new_act = join(',', $new_act_ids);
			$where = 'WHERE id_member = {int:id_member} AND id_act IN('.$new_act.')';
		}
		$query = 'UPDATE {db_prefix}log_notifications SET unread = 0 WHERE ' . $where;
		echo $query;
	}
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
	loadTemplate('Activities');
	loadLanguage('Activities');

	$board = isset($_REQUEST['b']) ? $_REQUEST['b'] : 0;
	$topic = isset($_REQUEST['t']) ? $_REQUEST['t'] : 0;

	$context['rich_output'] = true;		// todo: this indicates whether we want simple or rich activity bits (rich = with avatar)
	if((int)$board > 0 || isset($_REQUEST['all']))
		aStreamGet((int)$board, $xml, isset($_REQUEST['all']) ? true : false);
	else if($topic)
		aStreamGetForTopic($topic, $xml);

	if($xml) {
		$context['template_layers'] = array();
		$context['sub_template'] = 'showactivity_xml';
	}
	else
		$context['sub_template'] = 'showactivity';
}

function aStreamGet($b = 0, $xml = false, $global = false)
{
	global $board, $context, $user_info;

	if(!isset($board) || !$board)
		$board = $b;

	$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
	$context['xml'] = $xml;
	$context['act_global'] = false;

	if($user_info['is_admin'])
		$pquery = '';
	else
		$pquery = ' AND (a.is_private = 0 OR a.id_member = {int:id_user} OR a.id_owner = {int:id_user}) ';

	if($global) {
		$result = smf_db_query('
			SELECT a.*, t.*, b.name AS board_name FROM {db_prefix}log_activities AS a
			LEFT JOIN {db_prefix}activity_types AS t ON (t.id_type = a.id_type)
			LEFT JOIN {db_prefix}boards AS b ON(b.id_board = a.id_board)
			WHERE ({query_see_board} OR a.id_board = 0)'.$pquery.' ORDER BY a.id_act DESC LIMIT {int:start}, 20',
			array('start' => $start, 'id_user' => $user_info['id']));

		$context['act_global'] = true;
	}
	else
		$result = smf_db_query('
			SELECT a.*, t.*, b.name AS board_name FROM {db_prefix}log_activities AS a
			LEFT JOIN {db_prefix}activity_types AS t ON (t.id_type = a.id_type)
			INNER JOIN {db_prefix}boards AS b ON(b.id_board = a.id_board)
			WHERE a.id_board = {int:id_board} AND {query_see_board}'.$pquery.' ORDER BY a.id_act DESC LIMIT {int:start}, 20',
			array('id_board' => $board, 'start' => $start, 'id_user' => $user_info['id']));

	aStreamOutput($result);
}

/**
 * @param $result = mysql database query result
 * @return void
 *
 * output the result of a activity stream query
 */
function aStreamOutput($result)
{
	global $context, $memberContext, $txt;

	$users = array();
	$context['act_results'] = 0;
	while($row = mysql_fetch_assoc($result)) {
		if(!isset($context['board_name']))
			$context['board_name'] = $row['board_name'];
		$users[] = $row['id_member'];
		aStreamFormatActivity($row);
		$row['dateline'] = timeformat($row['updated']);
		$context['activities'][] = $row;
		$context['act_results']++;
	}
	mysql_free_result($result);
	$n = 0;
	if($context['rich_output']) {
		loadMemberData($users);
		foreach($users as $user) {
			loadMemberContext($user);
			$context['activities'][$n++]['member'] = &$memberContext[$user];
		}
	}
	if(!isset($context['get_notifications']))
		$context['titletext'] = $context['act_results'] ? ($context['act_global'] ? $txt['act_recent_global'] : sprintf($txt['act_recent_board'], $context['board_name'])) : $txt['act_no_results_title'];
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
?>
