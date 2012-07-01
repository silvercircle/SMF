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
	die('No access.');

 loadLanguage('Like');

/**
 * @param $mid = int message (or content) id
 *
 * handle the ajax request for rating a post. Also handles deletion of 
 * 
 * TODO: remove likes from the database when a user is deleted
 * TODO: make it work without AJAX and JavaScript
 */
 
function GiveLike($mid)
{
	global $context, $user_info, $sourcedir, $txt, $modSettings;
	$total = array();
	$content_type = 1;			// > post content type, we should define them elsewhere later when we have more than just this one
	
	if($mid > 0) {
		$uid = $user_info['id'];
		$remove_it = isset($_REQUEST['remove']) ? true : false;
		$repair = isset($_REQUEST['repair']) && $user_info['is_admin'] ? true : false;
		$is_xmlreq = $_REQUEST['action'] == 'xmlhttp' ? true : false;
		$update_mode = false;
		$like_type = ((isset($_REQUEST['r']) && (int)$_REQUEST['r'] > 0) ? $_REQUEST['r'] : '1');

		if(!isset($modSettings['ratings'][$like_type]))
			AjaxErrorMsg($txt['unknown_rating_type'], $is_xmlreq);
		if($user_info['is_guest'])
			AjaxErrorMsg($txt['no_like_for_guests'], $is_xmlreq);

		$request = smf_db_query('SELECT m.id_msg, m.id_member, m.id_board, m.id_topic, m.subject, l.id_msg AS like_message, l.rtype, l.id_user
				FROM {db_prefix}messages AS m LEFT JOIN {db_prefix}likes AS l ON (l.id_msg = m.id_msg AND l.ctype = {int:content_type} AND l.id_user = {int:id_user})
				WHERE m.id_msg = {int:id_msg} LIMIT 1',
			array('content_type' => $content_type, 'id_msg' => $mid, 'id_user' => $uid));

		$row = mysql_fetch_assoc($request);
		mysql_free_result($request);
		$like_owner = $row['id_user'];

		if($row['id_user'] > 0 && !$remove_it && !$repair)		// duplicate like (but not when removing it)
			AjaxErrorMsg($txt['like_verify_error'], $is_xmlreq);
			
		$like_receiver = $row['id_member'];
		EoS_Smarty::loadTemplate('xml_blocks');
		$context['template_functions'] = 'rating_response';
		CreateLikeBar();
		$context['ratings_output']['mid'] = $mid;

		/*
		 * this is a debugging feature and allows the admin to repair
		 * the likes for a post.
		 * it may go away at a later time.
		 */
		if($repair) {
			if(!$user_info['is_admin'])
				obExit(false);
			$total = LikesUpdate($mid);
			$output = '';
			LikesGenerateOutput($total['status'], $output, $mid, $row['id_user'] > 0 ? $row['rtype'] : 0);
			// fix like stats for the like_giver and like_receiver. This might be a very slow query, but
			// since this feature will most likely go away, right now I do not care.
			/*
			smf_db_query('UPDATE {db_prefix}members AS m
				SET m.likes_given = (SELECT COUNT(l.id_user) FROM {db_prefix}likes AS l WHERE l.id_user = m.id_member),
					m.likes_received = (SELECT COUNT(l1.id_receiver) FROM {db_prefix}likes AS l1 WHERE l1.id_receiver = m.id_member)
				WHERE m.id_member = {int:owner} OR m.id_member = {int:receiver}', array('owner' => $like_owner, 'receiver' => $like_receiver));
				*/
			invalidateMemberData(array($like_owner, $like_receiver));
			if($is_xmlreq) {
				$context['ratings_output']['output'] = $output;
				$context['ratings_output']['likebar'] = '';
				$context['postratings'] = json_encode($context['ratings_output']);
				return;
			}
			else
				redirectexit();
		}

		if($like_receiver == $uid)
			AjaxErrorMsg($txt['cannot_like_own'], $is_xmlreq);
		
		if(!allowedTo('like_give', $row['id_board']))			// no permission to give likes in this board
			AjaxErrorMsg($txt['like_no_permission'], $is_xmlreq);

		if($remove_it && $row['id_user'] > 0) {
			// remove a rating
			if($like_owner == $uid) {
				smf_db_query( '
					DELETE FROM {db_prefix}likes WHERE id_msg = {int:id_msg} AND id_user = {int:id_user} AND ctype = {int:ctype}',
					array('id_msg' => $mid, 'id_user' => $uid, 'ctype' => $content_type));
				
				if($like_receiver)
					smf_db_query( '
						UPDATE {db_prefix}members SET likes_received = likes_received - 1 WHERE id_member = {int:id_member}',
						array('id_member' => $like_receiver));
				
				smf_db_query( '
					UPDATE {db_prefix}members SET likes_given = likes_given - 1 WHERE id_member = {int:id_member}',
					array('id_member' => $uid));

				// if we remove a like (unlike) a post, also delete the corresponding activity
				smf_db_query( 'DELETE a.*, n.* FROM {db_prefix}log_activities AS a LEFT JOIN {db_prefix}log_notifications AS n ON(n.id_act = a.id_act)
					WHERE a.id_member = {int:id_member} AND a.id_type = 1 AND a.id_content = {int:id_content}',
					array('id_member' => $uid, 'id_content' => $mid));

				$context['ratings_output']['likebar'] = $context['like_bar'];
			}
		}
		else {
			/* store the rating */
			global $memberContext;
			
			if($like_receiver) {					// we do have a member, but still allow to like posts made by guests
				loadMemberData($like_receiver);		// but banned users shall not receive likes
				loadMemberContext($like_receiver);
			}
			if(($like_receiver && !$memberContext[$like_receiver]['is_banned']) || $like_receiver == 0) {  // posts by guests can be liked
				smf_db_query( '
					INSERT INTO {db_prefix}likes(id_msg, id_user, id_receiver, updated, ctype, rtype) values({int:id_message}, {int:id_user}, {int:id_receiver}, {int:updated}, {int:ctype}, {int:rtype})',
					array('id_message' => $mid, 'id_user' => $uid, 'id_receiver' => $like_receiver, 'updated' => time(), 'ctype' => $content_type, 'rtype' => $like_type));
					
				if($like_receiver)
					smf_db_query( 'UPDATE {db_prefix}members SET likes_received = likes_received + 1 WHERE id_member = {int:id_member}',
						array('id_member' => $like_receiver));
					
				smf_db_query( 'UPDATE {db_prefix}members SET likes_given = likes_given + 1 WHERE id_member = {int:uid}',
					array('uid' => $uid));
					
				$update_mode = $like_type;

				if($modSettings['astream_active']) {
					require_once($sourcedir . '/lib/Subs-Activities.php');
					aStreamAdd($uid, ACT_LIKE,
							array('member_name' => $context['user']['name'],
							  'topic_title' => $row['subject'],
							  'rating_type' => $modSettings['ratings'][$like_type]['text']),
							$row['id_board'], $row['id_topic'], $mid, $like_receiver);
				}
			}

			else
				AjaxErrorMsg($txt['like_cannot_like'], $is_xmlreq);
		
			$context['ratings_output']['likebar'] = '<a rel="nofollow" class="givelike" data-fn="remove" href="#" data-id="'.$mid.'">'.$txt['unlike_label'].'</a>';
		}
		if($user_info['is_admin'])
			$context['ratings_output']['likebar'] .= ' <a rel="nofollow" class="givelike" data-fn="repair" href="#" data-id="'.$mid.'">Repair ratings</a>';
		$total = LikesUpdate($mid);
		$output = '';
		LikesGenerateOutput($total['status'], $output, $mid, $update_mode);
		$context['ratings_output']['output'] = $output;
		$context['postratings'] = json_encode($context['ratings_output']);
	}
}

function LikesUpdate($mid)
{
	global $modSettings;

	$count = 0;
	$likers = array();
	$content_type = 1;

	$request = smf_db_query( '
		SELECT l.id_msg AS like_message, l.id_user AS like_user, l.rtype, m.real_name AS member_name FROM {db_prefix}likes AS l
			LEFT JOIN {db_prefix}members AS m on m.id_member = l.id_user WHERE l.id_msg = {int:id_message}
				AND l.ctype = {int:ctype} AND m.id_member <> 0 ORDER BY l.updated DESC',
				array('id_message' => $mid, 'ctype' => $content_type));

	while ($row = mysql_fetch_assoc($request)) {
		$rtype = $row['rtype'];
		if(empty($row['member_name']) || !isset($modSettings['ratings'][$rtype]))
			continue;
		if(!isset($likers[$rtype]['count']))
			$likers[$rtype]['count'] = 0;
		$likers[$rtype]['count']++;
		if(!isset($likers[$rtype]['members']))
			$likers[$rtype]['members'][0] = array('name' => $row['member_name'], 'id' => $row['like_user']);
		$count++;
	}
	mysql_free_result($request);
	$totalcount = $count;
	smf_db_query( '
		INSERT INTO {db_prefix}like_cache(id_msg, likes_count, like_status, updated, ctype) VALUES({int:id_msg}, {int:total}, {string:like_status}, {int:updated}, {int:ctype})
			ON DUPLICATE KEY UPDATE updated = {int:updated}, likes_count = {int:total}, like_status = {string:like_status}',
			array('id_msg' => $mid, 'total' => $totalcount, 'updated' => time(), 'like_status' => serialize($likers), 'ctype' => $content_type));

	$result['count'] = $totalcount;
	$result['status'] = $likers;
	return($result);
}

/**
 * @param $like_status - array. the cached like status (from like_cache table)
 * @param $output - string (ref) - where to store the output
 * @param $mid - int. the message id
 * @param $have_liked - int. if the current user has rated the post, this contains
 *        his rating id.
 *
 * generate readable output from the cached like status
 */
function LikesGenerateOutput($like_status, &$output, $mid, $have_liked)
{
	global $txt, $modSettings;
	$parts = array();

	if(is_array($like_status)) {
		foreach($like_status as $key => $the_like) {
			if(isset($modSettings['ratings'][$key]) && isset($the_like['members'])) {
				$parts[$key] = '<span data-rtype="'.$key.'" class="number">' . $the_like['count'] . '</span>&nbsp;' . $modSettings['ratings'][$key]['text'] . '&nbsp;';
				if($the_like['count'] > 1)
					$parts[$key] .= ($key == $have_liked ? sprintf($the_like['count'] > 2 ? $txt['you_and_others'] : $txt['you_and_other'], $the_like['count'] - 1) : '(<a rel="nofollow" onclick="getMcard('.$the_like['members'][0]['id'].', $(this));return(false);" class="mcard" href="'.URL::user($the_like['members'][0]['id'], $the_like['members'][0]['name']) .'">'.$the_like['members'][0]['name'].'</a>&nbsp;' . sprintf($the_like['count'] > 2 ? $txt['and_others'] : $txt['and_other'], $the_like['count'] - 1));
				else
					$parts[$key] .= ($key == $have_liked ? $txt['rated_you'] : '(<a rel="nofollow" onclick="getMcard('.$the_like['members'][0]['id'].', $(this));return(false);" class="mcard" href="'.URL::user($the_like['members'][0]['id'], $the_like['members'][0]['name']) .'">'.$the_like['members'][0]['name'].'</a>)');
			}
		}
	}

	if(!empty($parts))
		$output = '<span class="ratings" data-mid="'.$mid.'"><span class="title">Ratings:</span> ' . implode(' | ', $parts) . '</span>';
}

function CreateLikeBar()
{
	global $context, $modSettings;

	$context['like_bar'] = '';
	foreach($modSettings['ratings'] as $key => $rating)
		$context['like_bar'] .= '<a rel="nofollow" class="givelike" data-fn="give" href="#" data-rtype="'.$key.'">'.$rating['text'].'</a>&nbsp;&nbsp;&nbsp;';
}
/**
 * @param $row - array, fully prepared message
 * @param $can_give_like - int, permission to use the ratings
 * @param $now - int, current unix time
 * 
 * populates $row['likelink'] (all the links required to rate a post)
 * and $row['likers'] (the result of the like cache)
 * be used in a template. $can_give_like should be the result of a allowedTo('like_give') check.
 */
function AddLikeBar(&$row, $can_give_like, $now)
{
	global $user_info, $txt, $context, $modSettings;
	
	$row['likers'] = '';

	if(!isset($context['like_bar']))
		CreateLikeBar();

	$have_liked_it = (int)$row['liked'] > 0 ? $row['liked'] : false;

	if($can_give_like) {
		if($have_liked_it)
			$row['likelink'] = '<a rel="nofollow" class="givelike" data-fn="remove" href="#" data-id="'.$row['id'].'">'.$txt['unlike_label'].'</a>';
		else if(!$user_info['is_guest']) {
			if($row['id_member'] != $user_info['id'])
				$row['likelink'] = $context['like_bar'];
			else
				$row['likelink'] = '&nbsp;';
		}
	}
	else
		$row['likelink'] = '';

	// todo: admin gets a "repair likes" link (just a debugging tool, will probably go away...)
	if($user_info['is_admin'])
		$row['likelink'] .= ' <a rel="nofollow" class="givelike" data-fn="repair" href="#" data-id="'.$row['id'].'">Repair ratings</a>';

	$row['likelink'] = '<span data-likebarid="'.$row['id'].'">'. $row['likelink'] . '</span>';
	if($row['likes_count'] > 0)
		LikesGenerateOutput(unserialize($row['like_status']), $row['likers'], $row['id'], $have_liked_it);
}

/**
 * @param $mid 		array -> id_msg
 *
 * remove the likes and like cache for a given set of message ids
 */
function LikesRemoveByPosts($mid, $ctype = 1)
{
	smf_db_query('
		DELETE FROM {db_prefix}likes WHERE id_msg IN ({array_int:id_msg}) AND ctype = {int:ctype}',
		array('id_msg' => $mid, 'ctype' => $ctype));

	smf_db_query('
		DELETE FROM {db_prefix}like_cache WHERE id_msg IN ({array_int:id_msg}) AND ctype = {int:ctype}',
		array('id_msg' => $mid, 'ctype' => $ctype));
}
?>
