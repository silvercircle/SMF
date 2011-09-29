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
	global $context, $sourcedir, $modSettings;

	$xml = isset($_REQUEST['xml']) ? true : false;
	if(!$modSettings['astream_active']) {
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
	$context['rich_output'] = true;		// todo: this indicates whether we want simple or rich activity bits (rich = with avatar and possibly other member data)

	$where = 'WHERE n.id_member = {int:id_member} AND ({query_see_board} OR a.id_board = 0) ';
	if($view != 'all') {
		$where .= ' AND n.unread = 1 ';
		$context['view_all'] = false;
	}
	else
		$context['view_all'] = true;
	$result = smf_db_query('
		SELECT n.id_act, n.unread, a.id_member, a.updated, a.id_type, a.params, a.is_private, a.id_board, a.id_topic, a.id_content, a.id_owner, t.*, b.name AS board_name FROM {db_prefix}log_notifications AS n
		LEFT JOIN {db_prefix}log_activities AS a ON (a.id_act = n.id_act)
		LEFT JOIN {db_prefix}activity_types AS t ON (t.id_type = a.id_type)
		LEFT JOIN {db_prefix}boards AS b ON(b.id_board = a.id_board) '.
		$where . 'ORDER BY n.id_act DESC LIMIT {int:start}, 20',
		array('id_member' => $user_info['id'], 'start' => $start));

	aStreamOutput($result, true);

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
			updateMemberData($user_info['id'], array('last_login' => time()));
		}

		if($xml) {		// construct xml response for the JavaScript markread handler
			header('Content-Type: text/xml; charset=' . (empty($context['character_set']) ? 'UTF-8' : $context['character_set']));
			echo '<', '?xml version="1.0" encoding="', $context['character_set'], '"?', '>
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
		$pquery = ' AND (a.is_private <= ' . ACT_PLEVEL_ADMIN . ' OR a.id_member = {int:id_user} OR a.id_owner = {int:id_user}) ';
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
function aStreamOutput($result, $is_notification = false)
{
	global $context, $memberContext, $txt;

	$users = array();
	$context['act_results'] = 0;
	$context['unread_count'] = 0;
	while($row = mysql_fetch_assoc($result)) {
		if(!isset($context['board_name']))
			$context['board_name'] = $row['board_name'];
		$users[] = $row['id_member'];
		aStreamFormatActivity($row, $is_notification);
		$row['dateline'] = timeformat($row['updated']);
		$row['unread'] = isset($row['unread']) ? $row['unread'] : false;
		$context['unread_count'] += ($row['unread'] ? 1 : 0);			// needed when showing notifications
		$context['activities'][] = $row;
		$context['act_results']++;
	}
	mysql_free_result($result);
	if($context['rich_output']) {
		$n = 0;
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
