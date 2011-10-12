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
 * TODO: allow likes for more than just post content types (i.e. profile messages in a later stage)
 * ctype is already available as content type in the database, but right now,
 * there is only one content type: a post (ctype == 1)
 */
 
function GiveLike($mid)
{
	global $context, $user_info, $sourcedir, $txt;
	$total = array();
	$content_type = 1;			// > post content type, we should define them elsewhere later when we have more than just this one
	
	if($mid > 0) {
		$uid = $user_info['id'];
		$remove_it = isset($_REQUEST['remove']) ? true : false;
		$is_xmlreq = $_REQUEST['action'] == 'xmlhttp' ? true : false;
		$update_mode = false;
		
		if($user_info['is_guest'])
			AjaxErrorMsg($txt['no_like_for_guests'], $is_xmlreq);

		/* check for dupes */
		$request = smf_db_query( '
			SELECT COUNT(id_msg) as count, id_user 
				FROM {db_prefix}likes AS l WHERE l.id_msg = {int:id_message} AND l.id_user = {int:id_user} AND l.ctype = {int:ctype}',
				array('id_message' => $mid, 'id_user' => $uid, 'ctype' => $content_type));
				
		$count = mysql_fetch_row($request);
		mysql_free_result($request);
		
		$c = intval($count[0]);
		$like_owner = intval($count[1]);
		/*
		 * this is a debugging feature and allows the admin to repair
		 * the likes for a post.
		 * it may go away at a later time.
		 */
		if(isset($_REQUEST['repair'])) {
			if(!$user_info['is_admin'])
				die;
			$total = LikesUpdate($mid);
			$output = '';
			LikesGenerateOutput($total['status'], $output, $total['count'], $mid, $c > 0 ? true : false);
			if($is_xmlreq) {
				echo $output;
				die;
			}
			else
				redirectexit();
		}
		if($c > 0 && !$remove_it)		// duplicate like (but not when removing it)
			AjaxErrorMsg($txt['like_verify_error'], $is_xmlreq);
			
		/*
		 * you cannot like your own post - the front end handles this with a seperate check and
		 * doesn't show the like button for own messages, but this check is still necessary
		 */		
		
		$request = smf_db_query('
			SELECT id_member, id_board, id_topic, subject FROM {db_prefix}messages AS m WHERE m.id_msg = '.$mid);

		$m = mysql_fetch_row($request);
		mysql_free_result($request);
		$id_board = $m[1];
		$like_receiver = intval($m[0]);
		$id_topic = (int)$m[2];
		$topic_title = $m[3];
		
		if($like_receiver == $uid)
			AjaxErrorMsg($txt['cannot_like_own'], $is_xmlreq);
		
		if(!allowedTo('like_give', $id_board))			// no permission to give likes in this board
			AjaxErrorMsg($txt['like_no_permission'], $is_xmlreq);

		if($remove_it && $c > 0) {
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
					INSERT INTO {db_prefix}likes(id_msg, id_user, id_receiver, updated, ctype) values({int:id_message}, {int:id_user}, {int:id_receiver}, {int:updated}, {int:ctype})',
					array('id_message' => $mid, 'id_user' => $uid, 'id_receiver' => $like_receiver, 'updated' => time(), 'ctype' => $content_type));
					
				if($like_receiver)
					smf_db_query( 'UPDATE {db_prefix}members SET likes_received = likes_received + 1 WHERE id_member = {int:id_member}',
						array('id_member' => $like_receiver));
					
				smf_db_query( 'UPDATE {db_prefix}members SET likes_given = likes_given + 1 WHERE id_member = {int:uid}',
					array('uid' => $uid));
					
				$update_mode = true;

				if($context['astream_active']) {
					require_once($sourcedir . '/Subs-Activities.php');
					aStreamAdd($uid, ACT_LIKE,
							array('member_name' => $context['user']['name'],
							  'topic_title' => $topic_title),
							$id_board, $id_topic, $mid, $like_receiver);
				}
			}
			else
				AjaxErrorMsg($txt['like_cannot_like'], $is_xmlreq);
				
		}
		$total = LikesUpdate($mid);
		$output = '';
		LikesGenerateOutput($total['status'], $output, $total['count'], $mid, $update_mode);
		echo $output;
	}
	obExit(false, false, false);
}

function LikesUpdate($mid)
{
	$first = true;
	$like_string = '';
	$count = 0;
	$likers = array();
	$content_type = 1; 
	
	$request = smf_db_query( '
		SELECT l.id_msg AS like_message, l.id_user AS like_user, m.real_name AS member_name FROM {db_prefix}likes AS l
			LEFT JOIN {db_prefix}members AS m on m.id_member = l.id_user WHERE l.id_msg = {int:id_message} 
				AND l.ctype = {int:ctype} AND m.id_member <> 0 ORDER BY l.updated DESC LIMIT 4', 
				array('id_message' => $mid, 'ctype' => $content_type));

	while ($row = mysql_fetch_assoc($request)) {
		if(empty($row['member_name']))
			continue;
		$likers[$count] = $row['like_user'].'@' . base64_encode($row['member_name']);
		$count++;
		if($count > 3)
			break;
	}
	$like_string = implode("|", $likers);
	mysql_free_result($request);
	
	$request = smf_db_query( '
		SELECT COUNT(id_msg) as count
			FROM {db_prefix}likes AS l WHERE l.id_msg = {int:id_msg} AND l.ctype = {int:ctype}', 
			array('id_msg' => $mid, 'ctype' => $content_type));
	
	$count = mysql_fetch_row($request);
	mysql_free_result($request);
	$totalcount = $count[0];

	smf_db_query( '
		INSERT INTO {db_prefix}like_cache(id_msg, likes_count, like_status, updated, ctype) VALUES({int:id_msg}, {int:total}, {string:like_status}, {int:updated}, {int:ctype}) 
			ON DUPLICATE KEY UPDATE updated = {int:updated}, likes_count = {int:total}, like_status = {string:like_status}',
			array('id_msg' => $mid, 'total' => $totalcount, 'updated' => time(), 'like_status' => $like_string, 'ctype' => $content_type));

	$result['count'] = $totalcount;
	$result['status'] = $like_string;
	return($result);
}

/*
 * generate readable output from the cached like status
 * store it in $output
 * $have_liked indicates that the current user has liked the post.
 */
function LikesGenerateOutput($like_status, &$output, $total_likes, $mid, $have_liked)
{
	global $user_info, $scripturl, $txt;
	
	$like_template = array();
	$like_template[1] = $txt['1like'];
	$like_template[2] = $txt['2likes'];
	$like_template[3] = $txt['3likes'];
	$like_template[4] = $txt['4likes'];
	$like_template[5] = $txt['5likes'];

	$likers = explode("|", $like_status);
	$n = 1;
	$results = array();
	foreach($likers as $liker) {
		if(!empty($liker)) {
			$liker_components = explode("@", $liker);
			if(isset($liker_components[0]) && isset($liker_components[1])) {
				$liker_components[1] = base64_decode($liker_components[1]);
				if((int)$liker_components[0] === (int)$user_info['id']) {
					$results[0] = $txt['you_liker'];
					continue;
				}
				$results[$n++] = '<a rel="nofollow" onclick="getMcard('.$liker_components[0].', $(this));return(false);" class="mcard" href="'.$scripturl.'?action=profile;u='.$liker_components[0].'">'.$liker_components[1].'</a>';
			}
		}
	}
	$count = count($results);
	if($count == 0)
		return($output);
		
	/*
	 * we have liked it but our entry is too old to be in the top 4, so move 
	 * it to the front and remove the oldest - we always want our own like in the first position
	 */
	if($have_liked && !isset($results[0])) {
		array_pop($results);
		$results[0] = $txt['you_liker'];
	}
	
	ksort($results);
	if(isset($results[0]) && $count == 1)
		$output = $txt['you_like_it'];
	else if($total_likes > 4) {
		$output = vsprintf($like_template[5], $results);
		$output .= ('<a class="likedpost" onclick="getLikes('.$mid.', $(this));return(false);" rel="nofollow" href="'.$scripturl.'?action=like;sa=getlikes;;m='.$mid.'">'. ($total_likes - $count).$txt['like_others']);
	}
	else
		$output = vsprintf($like_template[$count], $results);
	return($output);
}

/*
 * $row[] is supposed to hold all the relevant data for a post
 * populates all like-related fields and generates the like links
 * be used in a template. $can_give_like should be the result of a allowedTo('like_give') check.
 */
function AddLikeBar(&$row, $can_give_like, $now)
{
	global $user_info, $txt;
	
	$row['likers'] = '';

	$have_liked_it = (int)$row['liked'] > 0 ? true : false;

	if($can_give_like) {
		if($have_liked_it)
			$row['likelink'] = '<a rel="nofollow" class="givelike" data-fn="remove" href="#" data-id="'.$row['id'].'">'.$txt['unlike_label'].'</a>';
		else if(!$user_info['is_guest']) {
			if($row['id_member'] != $user_info['id'])
				$row['likelink'] = '<a rel="nofollow" class="givelike" data-fn="give" href="#" data-id="'.$row['id'].'">'.$txt['like_label'].'</a>';
			else
				$row['likelink'] = '&nbsp;';
		}
	}
	else
		$row['likelink'] = '';

	// todo: admin gets a "repair likes" link (just a debugging tool, will probably go away...)
	if($user_info['is_admin'])
		$row['likelink'] .= ' <a rel="nofollow" class="givelike" data-fn="repair" href="#" data-id="'.$row['id'].'">Repair Likes</a>';
		
	if($row['likes_count'] > 0) {
		if($now - $row['like_updated'] > 86400) {
			$result = LikesUpdate($row['id']);
			LikesGenerateOutput($result['status'], $row['likers'], $result['count'], $row['id'], $have_liked_it);
		}
		else
			LikesGenerateOutput($row['like_status'], $row['likers'], $row['likes_count'], $row['id'], $have_liked_it);
	}
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
