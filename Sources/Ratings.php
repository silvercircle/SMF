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
 * implements UI features for the content - liking system.
 */

global $sourcedir;

require_once($sourcedir . '/lib/Subs-Ratings.php');

function FixLikes()
{
	global $sourcedir;

	require_once($sourcedir . '/lib/Subs-Ratings.php');

	$result = smf_db_query('SELECT id_msg FROM {db_prefix}messages');
	while($row = mysql_fetch_assoc($result))
		Ratings::updateForContent($row['id_msg']);

	mysql_free_result($result);
}

function LikeDispatch()
{
	global $context, $board, $txt, $user_info, $modSettings;

	$xml = isset($_REQUEST['xml']) ? true : false;
	$action = isset($_REQUEST['sa']) ? $_REQUEST['sa'] : '';
	
	if($action === '')
		$action = 'getlikes';
	if($action === 'widget') {
		GetRatingWidget();
		return;
	}
	else if($action === 'stats_detailed') {
		DetailedStatsForUser();
		return;
	}

	$ctype = isset($_REQUEST['ctype']) ? $_REQUEST['ctype'] : 1;		// default to content type = 1 (post)
	$mid = isset($_REQUEST['m']) ? (int)$_REQUEST['m'] : 0;
	$rtype = isset($_REQUEST['r']) ? (int)$_REQUEST['r'] : '0';

	if(!isset($modSettings['ratings'][$rtype]))
		AjaxErrorMsg($txt['unknown_rating_type']);

	if($user_info['is_admin'] && $action === 'fixlikes') {
		FixLikes();
		return;
	}
	if($mid) {
		if(!isset($board) || !$board) {
			$request = smf_db_query('SELECT m.id_topic, t.id_board FROM {db_prefix}messages AS m
				LEFT JOIN {db_prefix}topics AS t ON (t.id_topic = m.id_topic)
				WHERE m.id_msg = {int:id_msg}',
				array('id_msg' => $mid));
			$row = mysql_fetch_assoc($request);
			mysql_free_result($request);
			$board = $row ? $row['id_board'] : 0;
		}
		$allowed = isset($board) && $board && allowedTo('like_see', $board) && (allowedTo('like_details', $board) || $modSettings['ratings'][$rtype]['anon']);
		if(!$allowed)
			AjaxErrorMsg($txt['no_access']);

		$start = 0;
		$users = array();
		if($action === 'getlikes') {
			$request = smf_db_query('SELECT l.id_msg, l.id_user, l.updated, l.id_receiver, l.comment, m.real_name
					FROM {db_prefix}likes AS l LEFT JOIN {db_prefix}members AS m ON (m.id_member = l.id_user) 
					WHERE l.id_msg = {int:idmsg} AND l.ctype = {int:ctype} AND FIND_IN_SET({int:rtype}, l.rtype)
					ORDER BY l.updated DESC LIMIT {int:start}, 500',
				array('idmsg' => $mid, 'ctype' => $ctype, 'start' => $start, 'rtype' => $rtype)); // todo: paging and limit per page should be configurable

			while($row = mysql_fetch_assoc($request)) {
				$row['dateline'] = timeformat($row['updated']);
				$row['memberlink'] = '<a href="' . URL::user($row['id_user'], $row['real_name']) . '">' . $row['real_name'] . '</a>';
				$users[] = $row['id_user'];
				$context['likes'][$row['id_user']] = $row;
			}
			mysql_free_result($request);
		}
		EoS_Smarty::loadTemplate('xml_blocks');
		$context['template_functions'] = 'getlikes_by_type';
		$context['rating_title'] = sprintf($txt['members_who_rated_with'], $modSettings['ratings'][$rtype]['text']);
		if($xml)
			$context['xml'] = true;
	}
}

/**
 * generate the rating widget
 */
function GetRatingWidget()
{
	global $modSettings, $user_info, $context, $txt;

	$pool_avail = Ratings::getPool();

	if($user_info['is_guest'])
		AjaxErrorMsg($txt['no_like_for_guests']);

	$xml = isset($_REQUEST['xml']);
	$content_id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
	$ctype = isset($_REQUEST['c']) ? (int)$_REQUEST['c'] : 0;
	if(0 == $ctype || 0 == $content_id)
		AjaxErrorMsg($txt['rating_invalid_params']);

	if($xml) {
		EoS_Smarty::loadTemplate('xml_blocks');
		$context['template_functions'] = 'ratingwidget';
	}
	else
		EoS_Smarty::loadTemplate('ratings/widget');	// todo: allow rating without ajax / js

	$request = smf_db_query('SELECT m.id_board FROM {db_prefix}messages AS m WHERE m.id_msg = {int:id} LIMIT 1',
		array('id' => $content_id));

	list($id_board) = mysql_fetch_row($request);
	mysql_free_result($request);

	$context['result_count'] = 0;
	$uniques = array(true, false);
	foreach($uniques as $uniqueness) {
		foreach($modSettings['ratings'] as $key => $rating) {
			if($rating['unique'] != $uniqueness)
				continue;
			if(!empty($rating['enabled']) && Ratings::isAllowed($key, $id_board)) {
				$context['result_count']++;
				$cost = isset($rating['cost']) ? $rating['cost'] : 0;
				$context['ratings'][] = array(
					'rtype' => (int)$key,
					'label' => $rating['text'],
					'unique' => $rating['unique'],
					'cost' => $cost,
					'points' => isset($rating['points']) ? $rating['points'] : 0,
					//'avail' => true
					'avail' => $user_info['is_admin'] || $cost <= $pool_avail,
				);
			}
		}
	}
	$context['pool_avail'] = $pool_avail;
	$context['content_id'] = $content_id;
	$context['json_data'] = htmlspecialchars(json_encode(array('id' => $content_id, 'error_text' => $txt['ratingwidget_error'])));
	$context['widget_help_href'] = URL::parse('?action=helpadmin;help=ratingwidget_help');
}

/**
 * output a more detailed overview for a member's rating stats
 */
function DetailedStatsForUser()
{
	global $memberContext, $context, $modSettings;

	$uid = isset($_REQUEST['uid']) ? (int)$_REQUEST['uid'] : 0;
	$mid = isset($_REQUEST['mid']) ? (int)$_REQUEST['mid'] : 0;

	if($mid > 0 && $uid > 0 && loadMemberData($uid) !== false) {
		$ratings = &$modSettings['ratings'];

		loadMemberContext($uid);
		if(isset($memberContext[$uid])) {
			Ratings::refreshStats($uid);
			EoS_Smarty::loadTemplate('ratings/stats_output_detailed');
			$context['rating_stats'] = $memberContext[$uid]['ratings_received'];

			foreach($context['rating_stats']['rtypes'] as $type => $count) {
				if($count > 0 && isset($ratings[$type]) && $ratings[$type]['enabled']) {
					$context['rating_labels'][] = array(
						'count' => $count,
						'label' => $ratings[$type]['text']
					);
				}
			}
		}
		$context['json_data'] = htmlspecialchars(json_encode(array('mid' => $mid)));
	}
	else
		AjaxErrorMsg();
}

/**
 * @param $memID 		int id_member
 *
 * fetch all likes received by the given user and display them
 * part of the profile -> show content area.
 */
function LikesByUser($memID)
{
	global $context, $user_info, $scripturl, $memberContext, $txt, $modSettings, $options;
	
	if($memID != $user_info['id'])
		isAllowedTo('can_view_ratings');

	// let us use the same value as for topics per page here.
	$perpage = empty($modSettings['disableCustomPerPage']) && !empty($options['topics_per_page']) ? $options['topics_per_page'] : $modSettings['defaultMaxTopics'];
	$out = $_GET['sa'] === 'likesout';			// display likes *given* instead of received ones
	$is_owner = $user_info['id'] == $memID;		// we are the owner of this profile, this is important for proper formatting (you/yours etc.)
	
	$boards_like_see  = boardsAllowedTo('like_see');	// respect permissions
	$start = isset($_REQUEST['start']) ? (int)$_REQUEST['start'] : 0;

	if(!($user_info['is_admin'] || allowedTo('moderate_forum')))	// admins and global mods can see everything
		$bq = ' AND b.id_board IN({array_int:boards})';
	else
		$bq = '';

	$q = ($out ? 'l.id_user = {int:id_user}' : 'l.id_receiver = {int:id_user}');
	$request = smf_db_query('SELECT count(l.id_msg) FROM {db_prefix}likes AS l
			INNER JOIN {db_prefix}messages AS m ON (m.id_msg = l.id_msg)
			INNER JOIN {db_prefix}topics AS t ON (t.id_topic = m.id_topic)
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
			WHERE ' . $q . ' AND {query_see_board}' . $bq,
		array('id_user' => $memID, 'boards' => $boards_like_see));

	list($context['total_likes']) = mysql_fetch_row($request);
	mysql_free_result($request);

	$request = smf_db_query('SELECT m.subject, m.id_topic, l.id_user, l.id_receiver, l.updated, l.id_msg, l.rtype, mfirst.subject AS first_subject, SUBSTRING(m.body, 1, 150) AS body FROM {db_prefix}likes AS l
			INNER JOIN {db_prefix}messages AS m ON (m.id_msg = l.id_msg)
			INNER JOIN {db_prefix}topics AS t ON (t.id_topic = m.id_topic)
			INNER JOIN {db_prefix}messages AS mfirst ON (mfirst.id_msg = t.id_first_msg)
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
			WHERE ' . $q . ' AND {query_see_board} '.$bq.' ORDER BY l.id_like DESC LIMIT {int:startwith}, {int:perpage}',
		array('id_user' => $memID, 'startwith' => $start, 'perpage' => $perpage, 'boards' => $boards_like_see));

	$context['results_count'] = 0;
	$context['likes'] = array();
	$context['displaymode'] = $out ? true : false;
	$context['pages'] = '';
	if($context['total_likes'] > $perpage)
		$context['pages'] = constructPageIndex($scripturl . '?action=profile;area=showposts;sa='.$_GET['sa'].';u=' . trim($memID), $start, $context['total_likes'], $perpage);
	$users = array();
	while($row = mysql_fetch_assoc($request)) {
		$context['results_count']++;
		$thref = URL::topic($row['id_topic'], $row['first_subject'], 0);
		$phref = URL::topic($row['id_topic'], $row['subject'], 0, false, '.msg' . $row['id_msg'], '#msg' . $row['id_msg']);
		$users[] = $out ? $row['id_receiver'] : $row['id_user'];
		$context['likes'][] = array(
			'id_user' => $out ? $row['id_receiver'] : $row['id_user'],
			'time' => timeformat($row['updated']),
			'topic' => array(
				'href' => $thref,
				'link' => '<a href="'.$thref.'">'.$row['first_subject'].'</a>',
				'subject' => $row['first_subject']
			),
			'post' => array(
				'href' => $phref,
				'link' => '<a href="'.$phref.'">'.$row['subject'].'</a>',
				'subject' => $row['subject'],
				'id' => $row['id_msg']
			),
			'rtype' => $row['rtype'],
			'teaser' => strip_tags(preg_replace('~[[\/\!]*?[^\[\]]*?]~si', '', $row['body'])) . '...',
			'morelink' => URL::parse('?msg=' . $row['id_msg'] . ';perma')
		);
	}
	loadMemberData(array_unique($users));
	foreach($context['likes'] as &$like) {
		loadMemberContext($like['id_user']);
		$like['member'] = &$memberContext[$like['id_user']];
		$like['text'] = $out ? ($is_owner ? sprintf($txt['liked_a_post'], $is_owner ? $txt['you_liker'] : $memberContext[$memID]['name'], $memberContext[$like['id_user']]['link'], $like['post']['href'], $like['topic']['link'], $modSettings['ratings'][$like['rtype']]['text']) : sprintf($txt['liked_a_post'], $is_owner ? $txt['you_liker'] : $memberContext[$memID]['name'], $memberContext[$like['id_user']]['link'], $like['post']['href'], $like['topic']['link'], $modSettings['ratings'][$like['rtype']]['text'])) :
				($is_owner ? sprintf($txt['liked_your_post'], $like['id_user'] == $user_info['id'] ? $txt['you_liker'] : $like['member']['link'], $like['post']['href'], $like['topic']['link'], $modSettings['ratings'][$like['rtype']]['text']) :
				sprintf($txt['liked_a_post'], $like['id_user'] == $user_info['id'] ? $txt['you_liker'] : $like['member']['link'], $memberContext[$memID]['name'], $like['post']['href'], $like['topic']['link'], $modSettings['ratings'][$like['rtype']]['text']));
	}
	mysql_free_result($request);
	EoS_Smarty::getConfigInstance()->registerHookTemplate('profile_content_area', 'ratings/profile_display');
}
