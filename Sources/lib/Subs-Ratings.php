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
if (!defined('EOSA'))
	die('No access');

Ratings::init();

class Ratings {
	protected static $perm_can_see, $perm_can_give;
	protected static $rate_bar = '';
	protected static $is_valid;
	protected static $show_repair_link;

	public static function init()
	{
		global $context, $modSettings, $txt;

		$modSettings['ratings'] = !empty($modSettings['raw_ratings']) ? @unserialize($modSettings['raw_ratings']) : array();

		loadLanguage('Ratings');
		foreach($modSettings['ratings'] as &$rating)
			$rating['text'] = sprintf(html_entity_decode($rating['format']), !empty($rating['localized']) && isset($txt[$rating['localized']]) ? $txt[$rating['localized']] : $rating['label']);

		self::$is_valid = (isset($modSettings['ratings']) && count($modSettings['ratings']) > 0 ? true : false);

		$context['can_see_like'] = self::$perm_can_see = (self::$is_valid ? allowedTo('like_see') : false);
		$context['can_give_like'] = self::$perm_can_give = (self::$is_valid ? allowedTo('like_give') : false);
		
		self::$show_repair_link = !empty($modSettings['rating_show_repair']) ? true : false;
		self::$rate_bar = '<a onclick="ratingWidgetInvoke($(this));return(false);" rel="nofollow" href="!#" class="widgetanchor">' . $txt['rate_this'] . '</a>';
	}

	/**
	 * @static
	 * tell caller if ratings system is enabled
	 * @return mixed true if ratings are enabled and available.
	 */
	public static function isValid()
	{
		return self::$is_valid;
	}

	/**
	 * @static
	 * @param: $class_id int, the rating id we want to query
	 * @param: $board_id int, the board id
	 *
	 * determine whether the rating class id is allowed in the given board
	 * and for the current member's member groups.
	 */
	public static function isAllowed($class_id, $board_id)
	{
		global $user_info, $modSettings;

		$board_allowed = $board_denied = $group_allowed = false;

		$id = (int)$class_id;

		if($user_info['is_admin'])
			return true;

		if(0 == $id || 0 == (int)$board_id)
			return false;

		if(isset($modSettings['ratings'][$id])) {
			$rating = &$modSettings['ratings'][$id];
			$board_allowed = (isset($rating['boards']) && !empty($rating['boards'])) ? in_array((int)$board_id, $rating['boards']) : true;
			$board_denied = (isset($rating['boards_denied']) && !empty($rating['boards_denied'])) ? in_array((int)$board_id, $rating['boards_denied']) : false;
			if(isset($rating['groups']) && !empty($rating['groups'])) {
				$group_interset = array_intersect($rating['groups'], $user_info['groups']);
			 	$group_allowed = !empty($group_interset) ? true : false;
			}
			else
				$group_allowed = true;

			return $board_allowed && !$board_denied && $group_allowed ? true : false;
		}
		return false;
	}

	/**
	 * @param $row - array, fully prepared message
	 * @param $can_give_like - int, permission to use the ratings
	 *
	 * populates $row['likelink'] (all the links required to rate a post)
	 * and $row['likers'] (the result of the like cache)
	 * be used in a template. $can_give_like should be the result of a allowedTo('like_give') check.
	 */
	public static function addContent(&$row, $can_give_like)
	{
		global $user_info, $txt;
		
		$row['likers'] = '';

		$have_liked_it = (int)$row['liked'] > 0 ? $row['liked'] : false;

		if($can_give_like) {
			if($have_liked_it)
				$row['likelink'] = '<a rel="nofollow" class="givelike" data-fn="remove" href="#" data-id="'.$row['id'].'">'.$txt['unlike_label'].'</a>';
			else if(!$user_info['is_guest']) {
				if($row['id_member'] != $user_info['id'])
					$row['likelink'] = self::$rate_bar;
				else
					$row['likelink'] = '';
			}
		}
		else
			$row['likelink'] = '';

		// todo: admin gets a "repair likes" link (just a debugging tool, will probably go away...)
		if($user_info['is_admin'] && self::$show_repair_link)
			$row['likelink'] .= ' <a rel="nofollow" class="givelike" data-fn="repair" href="#" data-id="'.$row['id'].'">Repair ratings</a>';

		// todo: make ctype dynamic (for different content types)
		if(!empty($row['likelink']))
			$row['likelink'] = '<div style="position:relative;"><span data-ctype="1" data-likebarid="'.$row['id'].'">'. $row['likelink'] . '</span></div>';
		if($row['likes_count'] > 0)
			self::generateOutput(unserialize($row['like_status']), $row['likers'], $row['id'], $have_liked_it);
	}

	/**
	 * @static
	 * @param $mid	- int. a valid message id
	 * @return array - 'status' => readable form of rating stats, 'count' => total number of ratings for this post
	 *
	 * this updates the ratings cache entry for a given content (=message) id.
	 * it is only executed when a new rating is added or removed.
	 */
	public static function updateForContent($mid)
	{
		global $modSettings;

		$count = 0;
		$likers = array();
		$content_type = 1;

		$request = smf_db_query('SELECT l.id_msg AS like_message, l.id_user AS like_user, l.rtype, m.real_name AS member_name FROM {db_prefix}likes AS l
				LEFT JOIN {db_prefix}members AS m ON (m.id_member = l.id_user) 
				WHERE l.id_msg = {int:id_message} AND l.ctype = {int:ctype} AND m.id_member <> 0 
				ORDER BY l.updated DESC',
			array('id_message' => $mid, 'ctype' => $content_type));

		while ($row = mysql_fetch_assoc($request)) {
			$rtypes = explode(',', $row['rtype']);
			foreach($rtypes as $rtype) {
				if(empty($row['member_name']) || !isset($modSettings['ratings'][$rtype]))
					continue;
				if(!isset($likers[$rtype]['count']))
					$likers[$rtype]['count'] = 0;
				$likers[$rtype]['count']++;
				if(!isset($likers[$rtype]['members']))
					$likers[$rtype]['members'][0] = array('name' => $row['member_name'], 'id' => $row['like_user']);
				$count++;
			}
		}
		mysql_free_result($request);

		smf_db_query('INSERT INTO {db_prefix}like_cache(id_msg, likes_count, like_status, updated, ctype) 
				VALUES({int:id_msg}, {int:total}, {string:like_status}, {int:updated}, {int:ctype})
				ON DUPLICATE KEY UPDATE updated = {int:updated}, likes_count = {int:total}, like_status = {string:like_status}',
			array('id_msg' => $mid, 'total' => $count, 'updated' => time(), 'like_status' => serialize($likers), 'ctype' => $content_type));

		$result['count'] = $count;
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
	public static function generateOutput($like_status, &$output, $mid, $have_liked)
	{
		global $txt, $modSettings;
		$parts = array();
		$types = explode(',', $have_liked);

		if(is_array($like_status)) {
			foreach($like_status as $key => $the_like) {
				if(isset($modSettings['ratings'][$key]) && isset($the_like['members'])) {
					$parts[$key] = '<span data-rtype="'.$key.'" class="number">' . $the_like['count'] . '</span>&nbsp;' . $modSettings['ratings'][$key]['text'] . '&nbsp;';
					if($the_like['count'] > 1)
						$parts[$key] .= (in_array($key, $types) ? sprintf($the_like['count'] > 2 ? $txt['you_and_others'] : $txt['you_and_other'], $the_like['count'] - 1) : '(<a rel="nofollow" data-mid="'.$the_like['members'][0]['id'].'" class="mcard" href="'.URL::user($the_like['members'][0]['id'], $the_like['members'][0]['name']) .'">'.$the_like['members'][0]['name'].'</a>&nbsp;' . sprintf($the_like['count'] > 2 ? $txt['and_others'] : $txt['and_other'], $the_like['count'] - 1));
					else
						$parts[$key] .= (in_array($key, $types) ? $txt['rated_you'] : '(<a rel="nofollow" data-mid="'.$the_like['members'][0]['id'].'" class="mcard" href="'.URL::user($the_like['members'][0]['id'], $the_like['members'][0]['name']) .'">'.$the_like['members'][0]['name'].'</a>)');
				}
			}
		}

		if(!empty($parts))
			$output = '<span class="ratings" data-mid="'.$mid.'"><span class="title"></span> ' . implode(' | ', $parts) . '</span>';
	}

	/**
	 * @param $mid = int message (or content) id
	 *
	 * handle the ajax request for rating a post. Also handles deletion of 
	 * 
	 * TODO: remove likes from the database when a user is deleted
	 * TODO: make it work without AJAX and JavaScript
	 */
	 
	public static function rateIt($mid)
	{
		global $context, $user_info, $sourcedir, $txt, $modSettings;
		$total = array();
		$content_type = 1;			// > post content type, we should define them elsewhere later when we have more than just this one
		
		if((int)$mid > 0) {
			$uid = $user_info['id'];
			$remove_it = isset($_REQUEST['remove']) ? true : false;
			$repair = isset($_REQUEST['repair']) && $user_info['is_admin'] ? true : false;
			$is_xmlreq = $_REQUEST['action'] == 'xmlhttp' ? true : false;
			$update_mode = false;
			$like_type = ((isset($_REQUEST['r']) && (int)$_REQUEST['r'] > 0) ? $_REQUEST['r'] : '1');
			$comment = isset($_REQUEST['comment']) ? strip_tags($_REQUEST['comment']) : '';

			$rtypes = explode(',', $like_type);
			foreach($rtypes as $rtype) {
				if(!isset($modSettings['ratings'][$rtype]))
					AjaxErrorMsg($txt['unknown_rating_type']);
			}
			if($user_info['is_guest'])
				AjaxErrorMsg($txt['no_like_for_guests']);

			$request = smf_db_query('SELECT m.id_msg, m.id_member, m.id_board, m.id_topic, m.subject, l.id_msg AS like_message, l.rtype, l.id_user
					FROM {db_prefix}messages AS m 
					LEFT JOIN {db_prefix}likes AS l ON (l.id_msg = m.id_msg AND l.ctype = {int:content_type} AND l.id_user = {int:id_user})
					WHERE m.id_msg = {int:id_msg} LIMIT 1',
				array('content_type' => $content_type, 'id_msg' => $mid, 'id_user' => $uid));

			$row = mysql_fetch_assoc($request);
			mysql_free_result($request);
			$like_owner = $row['id_user'];

			if($row['id_user'] > 0 && !$remove_it && !$repair)		// duplicate like (but not when removing it)
				AjaxErrorMsg($txt['like_verify_error']);
				
			$like_receiver = $row['id_member'];
			EoS_Smarty::loadTemplate('xml_blocks');
			$context['template_functions'] = 'rating_response';
			$context['ratings_output']['mid'] = $mid;

			/*
			 * this is a debugging feature and allows the admin to repair
			 * the likes for a post.
			 * it may go away at a later time.
			 */
			if($repair) {
				if(!$user_info['is_admin'])
					obExit(false);
				$total = self::updateForContent($mid);
				$output = '';
				self::generateOutput($total['status'], $output, $mid, $row['id_user'] > 0 ? $row['rtype'] : 0);
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
				AjaxErrorMsg($txt['cannot_like_own']);
			
			if(!allowedTo('like_give', $row['id_board']))			// no permission to give likes in this board
				AjaxErrorMsg($txt['like_no_permission']);

			if($remove_it && $row['id_user'] > 0) {
				// remove a rating
				if($like_owner == $uid) {
					smf_db_query('DELETE FROM {db_prefix}likes WHERE id_msg = {int:id_msg} AND id_user = {int:id_user} AND ctype = {int:ctype}',
						array('id_msg' => $mid, 'id_user' => $uid, 'ctype' => $content_type));
					
					if($like_receiver)
						smf_db_query( 'UPDATE {db_prefix}members SET likes_received = likes_received - 1 WHERE id_member = {int:id_member}',
							array('id_member' => $like_receiver));
					
					smf_db_query('UPDATE {db_prefix}members SET likes_given = likes_given - 1 WHERE id_member = {int:id_member}',
						array('id_member' => $uid));

					// if we remove a like (unlike) a post, also delete the corresponding activity
					smf_db_query('DELETE a.*, n.* FROM {db_prefix}log_activities AS a LEFT JOIN {db_prefix}log_notifications AS n ON(n.id_act = a.id_act)
						WHERE a.id_member = {int:id_member} AND a.id_type = 1 AND a.id_content = {int:id_content}',
						array('id_member' => $uid, 'id_content' => $mid));

					$context['ratings_output']['likebar'] = self::$rate_bar;
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
					smf_db_query('INSERT INTO {db_prefix}likes(id_msg, id_user, id_receiver, updated, ctype, rtype, comment) 
							VALUES({int:id_message}, {int:id_user}, {int:id_receiver}, {int:updated}, {int:ctype}, {string:rtype}, {string:comment})',
						array('id_message' => $mid, 'id_user' => $uid, 'id_receiver' => $like_receiver, 'updated' => time(), 'ctype' => $content_type, 'rtype' => $like_type, 'comment' => $comment));
						
					if($like_receiver)
						smf_db_query('UPDATE {db_prefix}members SET likes_received = likes_received + 1 WHERE id_member = {int:id_member}',
							array('id_member' => $like_receiver));
						
					smf_db_query('UPDATE {db_prefix}members SET likes_given = likes_given + 1 WHERE id_member = {int:uid}',
						array('uid' => $uid));
						
					$update_mode = $like_type;

					if($modSettings['astream_active']) {
						@require_once($sourcedir . '/lib/Subs-Activities.php');
						aStreamAdd($uid, ACT_LIKE,
								array('member_name' => $context['user']['name'],
								  'topic_title' => $row['subject'],
								  'rtype' => $like_type),
								$row['id_board'], $row['id_topic'], $mid, $like_receiver);
					}
				}
				else
					AjaxErrorMsg($txt['like_cannot_like']);
			
				$context['ratings_output']['likebar'] = '<a rel="nofollow" class="givelike" data-fn="remove" href="#" data-id="'.$mid.'">'.$txt['unlike_label'].'</a>';
			}
			if($user_info['is_admin'] && self::$show_repair_link)
				$context['ratings_output']['likebar'] .= ' <a rel="nofollow" class="givelike" data-fn="repair" href="#" data-id="'.$mid.'">Repair ratings</a>';
			$total = self::updateForContent($mid);
			$output = '';
			self::generateOutput($total['status'], $output, $mid, $update_mode);
			$context['ratings_output']['output'] = $output;
			$context['postratings'] = json_encode($context['ratings_output']);
		}
	}

	/**
	 * @param $mid 		array of message ids or a single message id.
	 *
	 * remove all rating data for one ore more message ids.
	 */
	public static function removeByPosts($mid, $ctype = 1)
	{
		$_mid = !is_array($mid) ? array($mid) : array_unique($mid);

		smf_db_query('
			DELETE FROM {db_prefix}likes WHERE id_msg IN ({array_int:id_msg}) AND ctype = {int:ctype}',
			array('id_msg' => $_mid, 'ctype' => $ctype));

		smf_db_query('
			DELETE FROM {db_prefix}like_cache WHERE id_msg IN ({array_int:id_msg}) AND ctype = {int:ctype}',
			array('id_msg' => $_mid, 'ctype' => $ctype));
	}
}
