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
function FixLikes()
{
	global $sourcedir;

	require_once($sourcedir . '/lib/Subs-LikeSystem.php');

	$result = smf_db_query('SELECT id_msg FROM {db_prefix}messages');
	while($row = mysql_fetch_assoc($result))
		LikesUpdate($row['id_msg']);

	mysql_free_result($result);
}

function LikeDispatch()
{
	global $context, $board, $memberContext, $txt, $user_info;

	$xml = isset($_REQUEST['xml']) ? true : false;
	$action = isset($_REQUEST['sa']) ? $_REQUEST['sa'] : '';
	if($action === '')
		$action = 'getlikes';
	$ctype = isset($_REQUEST['ctype']) ? $_REQUEST['ctype'] : 1;		// default to content type = 1 (post)
	$mid = isset($_REQUEST['m']) ? (int)$_REQUEST['m'] : 0;

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
		$allowed = isset($board) && $board && allowedTo('like_see', $board);
		if(!$allowed)
			AjaxErrorMsg($txt['no_access'], 'Permission error');

		$start = isset($_REQUEST['start']) ? (int)$_REQUEST['start'] : 0;
		$users = array();
		if($action === 'getlikes') {
			$request = smf_db_query('
				SELECT l.id_msg, l.id_user, l.updated, l.id_receiver FROM {db_prefix}likes AS l WHERE l.id_msg = {int:idmsg} AND l.ctype = {int:ctype}
					ORDER BY l.updated DESC LIMIT {int:start}, 20',
				array('idmsg' => $mid, 'ctype' => $ctype, 'start' => $start)); // todo: paging and limit per page should be configurable

			while($row = mysql_fetch_assoc($request)) {
				$row['dateline'] = timeformat($row['updated']);
				$users[] = $row['id_user'];
				$context['likes'][$row['id_user']] = $row;
			}
			mysql_free_result($request);
			loadMemberData($users);
			foreach($users as $user) {
				loadMemberContext($user);
				$context['likes'][$user]['member'] = &$memberContext[$user];
			}
		}
		loadLanguage('Like');
		loadTemplate('LikeSystem');
		loadTemplate('GenericBits');
		$context['sub_template'] = 'getlikes';
		if($xml)
			$context['xml'] = true;
	}
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
	
	loadLanguage('Like');
	$boards_like_see  = boardsAllowedTo('like_see');	// respect permissions
	$start = isset($_REQUEST['start']) ? (int)$_REQUEST['start'] : 0;

	if(!($user_info['is_admin'] || allowedTo('moderate_forum')))	// admins and global mods can see everything
		$bq = ' AND b.id_board IN({array_int:boards})';
	else
		$bq = '';

	$q = ($out ? 'l.id_user = {int:id_user}' : 'l.id_receiver = {int:id_user}');
	$request = smf_db_query('
		SELECT count(l.id_msg) FROM {db_prefix}likes AS l
			INNER JOIN {db_prefix}messages AS m ON (m.id_msg = l.id_msg)
			INNER JOIN {db_prefix}topics AS t ON (t.id_topic = m.id_topic)
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
			WHERE ' . $q . ' AND {query_see_board}' . $bq,
		array('id_user' => $memID, 'boards' => $boards_like_see));

	list($context['total_likes']) = mysql_fetch_row($request);
	mysql_free_result($request);

	$request = smf_db_query('
		SELECT m.subject, m.id_topic, l.id_user, l.id_receiver, l.updated, l.id_msg, l.rtype, mfirst.subject AS first_subject, SUBSTRING(m.body, 1, 150) AS body FROM {db_prefix}likes AS l
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
	$context['sub_template'] = 'showlikes';
}
?>
