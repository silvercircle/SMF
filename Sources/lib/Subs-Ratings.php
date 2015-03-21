<?php
/**
 * @name      EosAlpha BBS
 * @copyright 2015 Alex Vie silvercircle(AT)gmail(DOT)com
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright:	2011 Simple Machines (http://www.simplemachines.org)
 * license:  	BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version 1.0pre
 *
 * class Ratings contains all the core functionality for the content
 * rating system.
 */
if (!defined('EOSA'))
	die('No access');

Ratings::init();

class Ratings {
	protected static 	$perm_can_see, $perm_can_give, $perm_can_see_details;
	protected static 	$rate_bar = '';
	protected static 	$is_valid;
	protected static 	$show_repair_link;
	protected static	$user;
	/**
	 * @var array		reference to the ratings array in $modSettings
	 */
	protected static	$_ratings;
	const           	REFRESH = 1;
	const				UPDATE = 2;
	const				RETURN_POINTS = 4;
	const				POOL_REFRESH_INTERVAL = 86400;
	const				STATS_REFRESH_INTERVAL = 86400;
	const				RATING_RATE	= 1;
	const				RATING_REMOVE = -1;

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
		$context['can_see_like_details'] = self::$perm_can_see_details = (self::$is_valid ? allowedTo('like_details') : false);

		self::$show_repair_link = !empty($modSettings['rating_show_repair']) ? true : false;
		self::$rate_bar = '<a onclick="ratingWidgetInvoke($(this));return(false);" rel="nofollow" href="!#" class="widgetanchor">' . $txt['rate_this'] . '</a>';

		self::$_ratings = &$modSettings['ratings'];
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

		$board_allowed = $board_denied = $group_allowed = $group_denied = false;

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

			if(isset($rating['groups_denied']) && !empty($rating['groups_denied'])) {
				$group_interset = array_intersect($rating['groups_denied'], $user_info['groups']);
				$group_denied = !empty($group_interset) ? true : false;
			}
			else
				$group_denied = false;


			return $board_allowed && !$board_denied && $group_allowed && !$group_denied ? true : false;
		}
		return false;
	}

	/**
	 * @param $row 				array, fully prepared message
	 * @param $can_give_like 	int, permission to use the ratings
	 * @param $can_see_details  boolean, true for detailed output (like_details permission controls who can see
	 * 							how users rated a post).
	 *
	 * populates $row['likelink'] (all the links required to rate a post)
	 * and $row['likers'] (the result of the like cache)
	 * be used in a template. $can_give_like should be the result of a allowedTo('like_give') check.
	 */
	public static function addContent(&$row, $can_give_like, $can_see_details = true)
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
			self::generateOutput(unserialize($row['like_status']), $row['likers'], $row['id'], $have_liked_it, $can_see_details);
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
	 * @param $like_status array 		the cached like status (from like_cache table)
	 * @param $output string (ref) 		where to store the output
	 * @param $mid int. 				the message id
	 * @param $have_liked int. 			if the current user has rated the post, this contains
	 *        							his rating id (multiple ids, separated with commas are possible).
	 * @param $can_see_details bool		show detailed output (like_details permission allowed)
	 *
	 * generate readable output from the cached like status
	 */
	public static function generateOutput($like_status, &$output, $mid, $have_liked, $can_see_detailed = true)
	{
		global $txt;
		$parts = array();
		$types = explode(',', $have_liked);

		if(is_array($like_status)) {
			foreach($like_status as $key => $the_like) {
				if(isset(self::$_ratings[$key]) && isset($the_like['members'])) {
					if($can_see_detailed || self::$_ratings[$key]['anon']) {
						$parts[$key] = '<span data-rtype="'.$key.'" class="number">' . $the_like['count'] . '</span>&nbsp;' . self::$_ratings[$key]['text'] . '&nbsp;';
						if($the_like['count'] > 1)
							$parts[$key] .= (in_array($key, $types) ? sprintf($the_like['count'] > 2 ? $txt['you_and_others'] : $txt['you_and_other'], $the_like['count'] - 1) : '(<a rel="nofollow" data-mid="'.$the_like['members'][0]['id'].'" class="mcard" href="'.URL::user($the_like['members'][0]['id'], $the_like['members'][0]['name']) .'">'.$the_like['members'][0]['name'].'</a>&nbsp;' . sprintf($the_like['count'] > 2 ? $txt['and_others'] : $txt['and_other'], $the_like['count'] - 1));
						else
							$parts[$key] .= (in_array($key, $types) ? $txt['rated_you'] : '(<a rel="nofollow" data-mid="'.$the_like['members'][0]['id'].'" class="mcard" href="'.URL::user($the_like['members'][0]['id'], $the_like['members'][0]['name']) .'">'.$the_like['members'][0]['name'].'</a>)');
					}
					/*
					 * this outputs a "anonymized" version - only list the number of ratings, do not show who rated
					 * and don't allow the member to click the number to retrieve a detailed listing.
					 */
					else {
						$parts[$key] = '<span data-rtype="'.$key.'" class="number_inactive">' . $the_like['count'] . '</span>&nbsp;' . self::$_ratings[$key]['text'] . '&nbsp;';
						if($the_like['count'] > 1)
							$parts[$key] .= (in_array($key, $types) ? sprintf($the_like['count'] > 2 ? $txt['you_and_others'] : $txt['you_and_other'], $the_like['count'] - 1) : '');
						else
							$parts[$key] .= (in_array($key, $types) ? $txt['rated_you'] : '');
					}
				}
			}
		}

		if(!empty($parts))
			$output = '<span class="ratings" data-mid="'.$mid.'"><span class="title"></span> ' . implode(' | ', $parts) . '</span>';
	}

	/**
	 * @param $mid = int message (or content) id
	 *
	 * handle the ajax request for rating a post. Also handles deletion of ratings
	 * and (optionally) the repair action.
	 * 
	 * TODO: remove likes from the database when a user is deleted
	 * TODO: make it work without AJAX and JavaScript
	 */
	 
	public static function rateIt($mid)
	{
		global $context, $user_info, $sourcedir, $txt, $modSettings;
		$total = array();
		$content_type = 1;			// > post content type, we should define them elsewhere later when we have more than just this one

		$pool_avail = self::getPool();

		if((int)$mid > 0) {
			$rtypes = array();

			$uid = $user_info['id'];
			$remove_it = isset($_REQUEST['remove']) ? true : false;
			$repair = isset($_REQUEST['repair']) && $user_info['is_admin'] ? true : false;
			$is_xmlreq = $_REQUEST['action'] == 'xmlhttp' ? true : false;
			$update_mode = false;
			$like_type = ((isset($_REQUEST['r']) && (int)$_REQUEST['r'] > 0) ? $_REQUEST['r'] : '1');
			$comment = isset($_REQUEST['comment']) ? strip_tags($_REQUEST['comment']) : '';

			$rtypes_submitted = explode(',', $like_type);
			foreach($rtypes_submitted as $rtype) {
				if(!isset($modSettings['ratings'][$rtype]))
					AjaxErrorMsg($txt['unknown_rating_type']);

				if($modSettings['ratings'][$rtype]['enabled'])		// only enabled types can be used
					$rtypes[] = (int)$rtype;
			}
			if($user_info['is_guest'])
				AjaxErrorMsg($txt['no_like_for_guests']);

			if(empty($rtypes))
				AjaxErrorMsg($txt['no_valid_rating_type']);
			/*
			 * check whether we have enough points available
			 */
			$total_point_cost = self::getCosts($rtypes);
			if(!$user_info['is_admin'] && !$remove_it && $total_point_cost > $pool_avail)
				AjaxErrorMsg($txt['rating_not_enough_points']);

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
			 * make sure all submitted ratings are allowed in the board
			 */
			if(!$remove_it) {
				foreach($rtypes as $type) {
					if(!self::isAllowed($type, $row['id_board']))
						AjaxErrorMsg($txt['rating_type_not_allowed']);
				}
			}
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
				// TODO: fix like stats for the like_giver and like_receiver. This might be a very slow query, but
				// since this feature will most likely go away, right now I do not care.
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
				global $memberContext;

				// remove a rating
				if($like_owner == $uid) {
					if($like_receiver) {
						loadMemberData($like_receiver, false, 'normal', true);
						loadMemberContext($like_receiver);
					}

					$request = smf_db_query('SELECT rtype FROM {db_prefix}likes WHERE id_msg = {int:id_msg} AND id_user = {int:id_user} AND ctype = {int:ctype} LIMIT 1',
						array('id_msg' => $mid, 'id_user' => $uid, 'ctype' => $content_type));

					if(smf_db_affected_rows() > 0 && isset($memberContext[$like_receiver])) {
						list($types) = mysql_fetch_row($request);

						$rtypes = explode(',', $types);
						mysql_free_result($request);

						if(false === self::recordStats($rtypes, $like_receiver, self::RATING_REMOVE, $mid))
							AjaxErrorMsg('Error recording stats');

						smf_db_query('DELETE FROM {db_prefix}likes WHERE id_msg = {int:id_msg} AND id_user = {int:id_user} AND ctype = {int:ctype}',
							array('id_msg' => $mid, 'id_user' => $uid, 'ctype' => $content_type));

						// if we remove a like (unlike) a post, also delete the corresponding activity
						smf_db_query('DELETE a.*, n.* FROM {db_prefix}log_activities AS a LEFT JOIN {db_prefix}log_notifications AS n ON(n.id_act = a.id_act)
						WHERE a.id_member = {int:id_member} AND a.id_type = 1 AND a.id_content = {int:id_content}',
							array('id_member' => $uid, 'id_content' => $mid));

						$context['ratings_output']['likebar'] = self::$rate_bar;

						/*
						 * record the stats
						 */
						$total_point_cost = self::getCosts($rtypes);
						self::updatePool($total_point_cost, Ratings::UPDATE|Ratings::RETURN_POINTS);

					}
				}
			}
			else {
				/* store the rating */
				global $memberContext;
				
				if($like_receiver) {					// we do have a member, but still allow to like posts made by guests
					loadMemberData($like_receiver, false, 'normal', true);		// but banned users shall not receive likes
					loadMemberContext($like_receiver);
				}
				if(($like_receiver && !$memberContext[$like_receiver]['is_banned']) || $like_receiver == 0) {  // posts by guests can be liked

					if(false === self::recordStats($rtypes, $like_receiver, self::RATING_RATE, $mid))
						AjaxErrorMsg('Error recording stats');

					smf_db_query('INSERT INTO {db_prefix}likes(id_msg, id_user, id_receiver, updated, ctype, rtype, comment) 
							VALUES({int:id_message}, {int:id_user}, {int:id_receiver}, {int:updated}, {int:ctype}, {string:rtype}, {string:comment})',
						array('id_message' => $mid, 'id_user' => $uid, 'id_receiver' => $like_receiver, 'updated' => time(), 'ctype' => $content_type, 'rtype' => $like_type, 'comment' => $comment));
						
					$update_mode = $like_type;

					if($modSettings['astream_active']) {
						@require_once($sourcedir . '/lib/Subs-Activities.php');
						aStreamAdd($uid, ACT_LIKE,
								array('member_name' => $context['user']['name'],
								  'topic_title' => $row['subject'],
								  'rtype' => $like_type),
								$row['id_board'], $row['id_topic'], $mid, $like_receiver);
					}
					self::updatePool($total_point_cost, Ratings::UPDATE);
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

	/**
	 * @param $do_db 	boolean. if true, update the database.
	 *
	 * initialize or refresh a member's rating point pool
	 */
	public static function refreshPool($do_db = true)
	{
		global $user_info, $context;

		$pool_avail = 0;
		$request = smf_db_query('SELECT MAX(rating_pool) AS pool FROM {db_prefix}membergroups AS g WHERE g.id_group IN({array_int:groups})',
			array('groups' => $user_info['groups']));

		if(smf_db_affected_rows() > 0) {
			$row = mysql_fetch_assoc($request);
			$pool_avail = $row['pool'];
		}
		mysql_free_result($request);

		$user_info['meta']['rating_pool']['points'] = $pool_avail;
		$user_info['meta']['rating_pool']['refresh'] = $context['time_now'];

		if($do_db)
			updateMemberData($user_info['id'], array('meta' => @serialize($user_info['meta'])));
	}

	/**
	 * cleans the rating point pool
	 */
	public static function cleanPool()
	{
		global $user_info;

		$user_info['meta']['rating_pool']['points'] = 0;
		$user_info['meta']['rating_pool']['refresh'] = 0;

		updateMemberData($user_info['id'], array('meta' => @serialize($user_info['meta'])));
	}
	/**
	 * @return int  the number of available rating points in the member's pool
	 *
	 * get the member's rating pool points. If the pool has not been initialized yet, do it.
	 */
	public static function getPool()
	{
		global $user_info, $context;

		if(!isset($user_info['meta']['rating_pool']['refresh']) || $context['time_now'] - $user_info['meta']['rating_pool']['refresh'] > Ratings::POOL_REFRESH_INTERVAL)
			self::refreshPool();

		//self::cleanPool();
		return $user_info['meta']['rating_pool']['points'];
	}

	/**
	 * @param $points   	int points to subtract from the pool
	 * @param int $mode     mode. defaults to update, can also be a bitwise OR of UPDATE | REFRESH
	 *
	 * update the member's pool of available rating points.
	 */
	public static function updatePool($points, $mode = Ratings::UPDATE)
	{
		global $user_info;

		if($mode & Ratings::REFRESH)
			self::refreshPool($mode & Ratings::UPDATE ? false : true);		// avoid db update if we also do a update afterwards

		if($mode & Ratings::UPDATE) {
			/*
			 * return points to the member's pool (this happens when one removes a rating)
			 */
			if($mode & Ratings::RETURN_POINTS)
				$user_info['meta']['rating_pool']['points'] += $points;
			else {
				/*
				 * make sure, we never get negative pool values
				 */
				if($points <= $user_info['meta']['rating_pool']['points'])
					$user_info['meta']['rating_pool']['points'] -= $points;
				else
					$user_info['meta']['rating_pool']['points'] = 0;
			}
			updateMemberData($user_info['id'], array('meta' => @serialize($user_info['meta'])));
		}
	}

	/**
	 * @param $rating_types array|int either a single rating id or an array of rating ids
	 *
	 * @return int cost in rating points. Can be 0 >= $cost
	 *
	 * calculate the cost (in points) for one or more ratings.
	 */
	public static function getCosts(&$rating_types)
	{
		$cost = 0;
		$ids = is_array($rating_types) ? array_unique($rating_types) : array($rating_types);

		foreach($ids as $id) {
			if(isset(self::$_ratings[$id]))
				$cost += self::$_ratings[$id]['cost'];
		}
		return $cost;
	}

	/**
	 * @param $stats	array a rating stats array (either ratings_given or ratings_received)
	 *
	 * return a default stats record with everything cleared
	 */
	public static function initStats()
	{
		$stats = array(
			'points_positive' => 0,
			'points_negative' => 0,
			'count_positive' => 0,
			'count_negative' => 0,
			'count_global' => 0,
			'rtypes' => array()
		);

		return $stats;
	}

	/**
	 * @param $stats	array - a member's rating stats array (part of meta)
	 */
	public static function validateStats(&$stats)
	{
		if($stats['points_positive'] < 0)
			$stats['points_positive'] = 0;

		if($stats['points_negative'] > 0)
			$stats['points_negative'] = 0;

		$stats['count_positive'] = $stats['count_positive'] >= 0 ? $stats['count_positive'] : 0;
		$stats['count_negative'] = $stats['count_negative'] >= 0 ? $stats['count_negative'] : 0;

		if(count($stats['rtypes'])) {
			foreach($stats['rtypes'] as $key => &$type)
				$stats['rtypes'][$key] = $stats['rtypes'][$key] >= 0 ? $stats['rtypes'][$key] : 0;
		}
	}

	/**
	 * @param $stats		array reference to a stats array (either ratings_given or ratings_received)
	 *
	 * recalculate points and rating counts.
	 */
	public static function recalcStats(&$stats)
	{
		global $context;

		$stats['points_positive'] = $stats['points_negative'] = $stats['count_positive'] = $stats['count_negative'] = $stats['count_global'] = 0;
		$stats['last_refresh'] = $context['time_now'];

		if(!empty($stats['rtypes'])) {
			foreach($stats['rtypes'] as $type => $count) {
				if($count > 0 && isset(self::$_ratings[$type]) && !empty(self::$_ratings[$type]['enabled'])) {
					$points = self::$_ratings[$type]['points'];
					if($points > 0) {
						$stats['count_positive']++;
						$stats['points_positive'] += ($points * $count);
					}
					else if($points < 0) {
						$stats['count_negative']++;
						$stats['points_negative'] += ($points * $count);
					}
					$stats['count_global']++;
				}
			}
		}
	}

	/**
	 * @param $id_member 		int 	the member id
	 * @param string $mode		string  which stats array to update
	 * @param bool $force       boolean force a refresh when true
	 *
	 * refresh a member's rating stats (either ratings_received or ratings_given)
	 * it uses the $stats['rtypes'] array to recalculate the points and rating counts.
	 */
	public static function refreshStats($id_member, $mode = 'ratings_received', $force = false)
	{
		global $context, $memberContext;

		if(!isset($memberContext[$id_member]))
			return;

		$stats = &$memberContext[$id_member][$mode];

		if($force || !isset($stats['last_refresh']) || $context['time_now'] - $stats['last_refresh'] > self::STATS_REFRESH_INTERVAL) {
			self::recalcStats($stats);
			updateMemberData($id_member, array($mode => @serialize($stats)));
		}
	}
	/**
	 * @param $rtypes		array 	rating type IDs. Note that this function does NOT check permissions
	 * 								or enabled state for each of the rating types. The caller must make sure
	 * 								to pass only allowed rating types.
	 * @param $receiver		int		member id of the member who receives the rating
	 * @param $mode			int		either RATING_RATE or RATING_REMOVE
	 * @param $cid			int		the content id (message id). Not really needed, but passed for debugging and logging
	 *
	 * @return				bool	true = success
	 *
	 * $memberContext[$receiver] should be loaded and valid
	 * The rating member is always the active one ($user_info)
	 *
	 * record the stats for a rating in $user_info['ratings_given'] and $memberContext[$receiver]['ratings_received']
	 */
	public static function recordStats($rtypes, $receiver, $mode, $cid)
	{
		global $user_info, $memberContext;
		$ratings_count = 0;

		if(!isset($memberContext[$receiver])) {
			if(loadMemberData($receiver) !== false)
				loadMemberContext($receiver);
			else {
				log_error('Invalid or unknown rating receiver: ' . $receiver, __FILE__, __LINE__);
				return false;
			}
		}

		if(empty($user_info['ratings_given']))
			$user_info['ratings_given'] = self::initStats();
		if(empty($memberContext[$receiver]['ratings_received']))
			$memberContext[$receiver]['ratings_received'] = self::initStats();

		// shortcuts
		$rater = &$user_info['ratings_given'];
		$rated = &$memberContext[$receiver]['ratings_received'];

		foreach($rtypes as $type) {
			if(isset(self::$_ratings[$type])) {
				$ratings_count++;
				$points = self::$_ratings[$type]['points'];

				if(!isset($rater['rtypes'][$type]))
					$rater['rtypes'][$type] = 0;

				if(!isset($rated['rtypes'][$type]))
					$rated['rtypes'][$type] = 0;

				$rater['rtypes'][$type] += ($mode == self::RATING_RATE ? 1 : -1);
				$rated['rtypes'][$type] += ($mode == self::RATING_RATE ? 1 : -1);
				self::recalcStats($rater);
				self::recalcStats($rated);
			}
		}

		if(0 === $ratings_count)
			log_error('Invalid rating(s) for content ID ' . $cid . ' by member ' . $user_info['name'] . ', RTYPES = ' . implode(',', $rtypes), __FILE__, __LINE__);
		else {
			self::validateStats($rater);
			self::validateStats($rated);

			updateMemberData($user_info['id'], array('ratings_given' => @serialize($rater)));
			updateMemberData($receiver, array('ratings_received' => @serialize($rated)));
		}

		// only return success if we had at least one valid rating
		return $ratings_count > 0 ? true : false;
	}
}
