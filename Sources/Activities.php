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
		'add' => array('function' => 'aStreamAdd')
	);
	if (!isset($_REQUEST['sa'], $sub_actions[$_REQUEST['sa']]))
		fatal_lang_error('no_access', false);

	$sub_actions[$_REQUEST['sa']]['function']();
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
	global $board, $context, $txt;

	if(!isset($board) || !$board)
		$board = $b;

	$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
	$context['xml'] = $xml;
	$context['act_global'] = false;

	if($global) {
		$result = smf_db_query('
			SELECT a.*, t.*, b.name AS board_name FROM {db_prefix}log_activities AS a
			LEFT JOIN {db_prefix}activity_types AS t ON (t.id_type = a.id_type)
			LEFT JOIN {db_prefix}boards AS b ON(b.id_board = a.id_board)
			WHERE {query_see_board} OR a.id_board = 0 ORDER BY a.updated DESC LIMIT {int:start}, 20',
			array('start' => $start));

		$context['act_global'] = true;
	}
	else
		$result = smf_db_query('
			SELECT a.*, t.*, b.name AS board_name FROM {db_prefix}log_activities AS a
			LEFT JOIN {db_prefix}activity_types AS t ON (t.id_type = a.id_type)
			INNER JOIN {db_prefix}boards AS b ON(b.id_board = a.id_board)
			WHERE a.id_board = {int:id_board} AND {query_see_board} ORDER BY a.updated DESC LIMIT {int:start}, 20',
			array('id_board' => $board, 'start' => $start));

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
