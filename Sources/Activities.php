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
	global $board, $context, $memberContext, $txt;

	if(!isset($board) || !$board)
		$board = $b;

	$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;

	$context['xml'] = $xml;

	if($global) {
		$result = smf_db_query('
			SELECT a.*, t.*, b.name AS board_name FROM {db_prefix}log_activities AS a
			LEFT JOIN {db_prefix}activity_types AS t ON (t.id_type = a.id_type)
			LEFT JOIN {db_prefix}boards AS b ON(b.id_board = a.id_board OR a.id_board = 0)
			WHERE {query_see_board} ORDER BY a.updated DESC LIMIT {int:start}, 20',
			array('start' => $start));

		$context['titletext'] = $txt['act_recent_global'];
	}
	else
		$result = smf_db_query('
			SELECT a.*, t.*, b.name AS board_name FROM {db_prefix}log_activities AS a
			LEFT JOIN {db_prefix}activity_types AS t ON (t.id_type = a.id_type)
			INNER JOIN {db_prefix}boards AS b ON(b.id_board = a.id_board)
			WHERE a.id_board = {int:id_board} AND {query_see_board} ORDER BY a.updated DESC LIMIT {int:start}, 20',
			array('id_board' => $board, 'start' => $start));

	$users = array();
	while($row = mysql_fetch_assoc($result)) {
		if(!isset($context['titletext']))
			$context['titletext'] = sprintf($txt['act_recent_board'], $row['board_name']);
		$users[] = $row['id_member'];
		aStreamFormatActivity($row);
		$row['dateline'] = timeformat($row['updated']);
		$context['activities'][] = $row;
	}
	mysql_free_result($result);
	$n = 0;
	if(1) {									// todo: this indicates whether we want simple or rich activity bits (rich = with avatar)
		loadMemberData($users);
		foreach($users as $user) {
			loadMemberContext($user);
			$context['activities'][$n++]['member'] = &$memberContext[$user];
		}
	}
}

function aStreamGetForTopic($t = 0, $xml = false)
{

}
?>
