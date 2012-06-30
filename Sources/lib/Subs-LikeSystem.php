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

 loadLanguage('Like');

/*
 * handle a like. $mid is the message id that is to receive the
 * like
 * $mid = id_msg to like
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

		/* check for dupes */
		$request = smf_db_query( '
			SELECT COUNT(id_msg) as count, rtype, id_user
				FROM {db_prefix}likes AS l WHERE l.id_msg = {int:id_message} AND l.id_user = {int:id_user} AND l.ctype = {int:ctype} LIMIT 1',
				array('id_message' => $mid, 'id_user' => $uid, 'ctype' => $content_type));
				
		$count = mysql_fetch_row($request);
		mysql_free_result($request);
		
		$c = intval($count[0]);
		$r = $like_type;
		$like_owner = intval($count[2]);

		if($c > 0 && !$remove_it && !$repair)		// duplicate like (but not when removing it)
			AjaxErrorMsg($txt['like_verify_error'], $is_xmlreq);
			
		/*
		 * you cannot like your own post - the front end handles this with a seperate check and
		 * doesn't show the like button for own messages, but this check is still necessary
		 */		
		
		$request = smf_db_query('
			SELECT id_member, id_board, id_topic, subject FROM {db_prefix}messages AS m WHERE m.id_msg = {int:idmsg} LIMIT 1',
			array('idmsg' => $mid));

		$m = mysql_fetch_row($request);
		mysql_free_result($request);
		$id_board = $m[1];
		$like_receiver = intval($m[0]);
		$id_topic = (int)$m[2];
		$topic_title = $m[3];

		/*
		 * this is a debugging feature and allows the admin to repair
		 * the likes for a post.
		 * it may go away at a later time.
		 */
		EoS_Smarty::loadTemplate('xml_blocks');
		$context['template_functions'] = 'rating_response';
		CreateLikeBar();
		$context['ratings_output']['mid'] = $mid;

		if($repair) {
			if(!$user_info['is_admin'])
				obExit(false);
			$total = LikesUpdate($mid);
			$output = '';
			LikesGenerateOutput($total['status'], $output, $total['count'], $mid, $c > 0 ? $r : 0);
			// fix like stats for the like_giver and like_receiver. This might be a very slow query, but
			// since this feature will most likely go away, right now I do not care.
			smf_db_query('UPDATE {db_prefix}members AS m
				SET m.likes_given = (SELECT COUNT(l.id_user) FROM {db_prefix}likes AS l WHERE l.id_user = m.id_member),
					m.likes_received = (SELECT COUNT(l1.id_receiver) FROM {db_prefix}likes AS l1 WHERE l1.id_receiver = m.id_member)
				WHERE m.id_member = {int:owner} OR m.id_member = {int:receiver}', array('owner' => $like_owner, 'receiver' => $like_receiver));
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
		
		if(!allowedTo('like_give', $id_board))			// no permission to give likes in this board
			AjaxErrorMsg($txt['like_no_permission'], $is_xmlreq);

		if($remove_it && $c > 0) {
			// remove a like (unlike feature)
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
			/* store the like */
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
							  'topic_title' => $topic_title,
							  'rating_type' => $modSettings['ratings'][$like_type]['text']),
							$id_board, $id_topic, $mid, $like_receiver);
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
		LikesGenerateOutput($total['status'], $output, $total['count'], $mid, $update_mode);
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

/*
 * generate readable output from the cached like status
 * store it in $output
 * $have_liked indicates that the current user has liked the post.
 */

function LikesGenerateOutput($like_status, &$output, $total_likes, $mid, $have_liked)
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
/*

function LikesGenerateOutput($like_status, &$output, $total_likes, $mid, $have_liked)
{
	global $user_info, $scripturl, $txt;

	$like_template = array();
	$like_template[1] = $txt['1like'];
	$like_template[2] = $txt['2likes'];
	$like_template[3] = $txt['3likes'];
	$like_template[4] = $txt['4likes'];
	$like_template[5] = $txt['5likes'];

	$n = 1;
	$results = array();
	if(is_array($like_status) && count($like_status) > 0) {
		foreach($like_status as $key => $liker) {
			if(!empty($liker)) {
				if((int)$key === (int)$user_info['id']) {
					$results[0] = $txt['you_liker'];
					continue;
				}
				$results[$n++] = '<a rel="nofollow" onclick="getMcard('.$key.', $(this));return(false);" class="mcard" href="'.URL::user($key, $liker) .'">'.$liker.'</a>';
			}
		}
	}
	$count = count($results);
	if($count == 0)
		return($output);

	if($have_liked && !isset($results[0])) {
		array_pop($results);
		$results[0] = $txt['you_liker'];
	}

	ksort($results);
	if(isset($results[0]) && $count == 1)
		$output = $txt['you_like_it'];
	else if($total_likes > 4) {
		$output = vsprintf($like_template[5], $results);
		$output .= ('<a class="likedpost" onclick="getLikes('.$mid.');return(false);" rel="nofollow" href="'.$scripturl.'?action=like;sa=getlikes;m='.$mid.'">'. ($total_likes - $count).$txt['like_others']);
	}
	else
		$output = vsprintf($like_template[$count], $results) . '&nbsp;<a class="likedpost" onclick="getLikes('.$mid.');return(false);" rel="nofollow" href="'.$scripturl.'?action=like;sa=getlikes;m='.$mid.'">[...]</a>';
	return($output);
}
*/

function CreateLikeBar()
{
	global $context, $modSettings;

	$context['like_bar'] = '';
	foreach($modSettings['ratings'] as $key => $rating)
		$context['like_bar'] .= '<a rel="nofollow" class="givelike" data-fn="give" href="#" data-rtype="'.$key.'">'.$rating['text'].'</a>&nbsp;&nbsp;&nbsp;';
}
/*
 * $row[] is supposed to hold all the relevant data for a post
 * populates all like-related fields and generates the like links
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
		LikesGenerateOutput(unserialize($row['like_status']), $row['likers'], $row['likes_count'], $row['id'], $have_liked_it);
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
