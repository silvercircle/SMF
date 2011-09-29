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
 * implements activity stream helper functions. Activity stream is an (optional) core
 * feature.
 *
 * also implements notification helper functions
 */
if (!defined('SMF'))
	die('Hacking attempt...');

// activity types, reflect the id_type in log_activities and activity_types tables
// mods will most likely be able to register their own activity types and provide
// formatting for them.
define('ACT_LIKE', 1);			// user liked a post (for activities)
define('ACT_NEWTOPIC', 2);
define('ACT_REPLIED', 3);
define('ACT_MODIFY_POST', 4);
define('ACT_NEWMEMBER', 5);
define('ACT_PM', 6);
// privacy levels (note that admin can always see all activity, that's why they are admins)
define('ACT_PLEVEL_PUBLIC', 0);		// everyone can see this
define('ACT_PLEVEL_USER', 1);		// user can see his own activities, other users cannot see this
define('ACT_PLEVEL_MOD', 2);		// forum moderators can see this
define('ACT_PLEVEL_ADMIN', 3);		// only admins can see it
define('ACT_PLEVEL_PRIVATE', 4);	// nobody except owner or receiver can see it (e.g. pm activities).

/**
 * vsprintf for associative arrays
 * takes a formatting string like:  %member_name$s did something in %id_topic$s
 * values of $data are matched on their key names
 */
function _vsprintf($format, &$data)
{
	preg_match_all( '/ (?<!%) % ( (?: [[:alpha:]_-][[:alnum:]_-]* | ([-+])? [0-9]+ (?(2) (?:\.[0-9]+)? | \.[0-9]+ ) ) ) \$ [-+]? \'? .? -? [0-9]* (\.[0-9]+)? \w/x', $format, $match, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
	$offset = 0;
    $keys = array_keys($data);
    foreach($match as &$value) {
		if(($key = array_search($value[1][0], $keys, true)) !== false || (is_numeric($value[1][0]) && ($key = array_search((int)$value[1][0], $keys, true)) !== false)) {
			$len = strlen($value[1][0]);
			$format = substr_replace($format, 1 + $key, $offset + $value[1][1], $len);
			$offset -= $len - strlen(1 + $key);
		}
	}
	return vsprintf($format, $data);
}

/**
 * add a stream activity
 *
 * @param int $id_member	the member id who owns this activity (= who did it)
 * @param int $atype		activity type (numeric)
 * @param $params			array with parameters, mostly for formatting
 * @param int $id_board		the board id where it happened (if applicable)
 * @param int $id_topic		the topic id where it happened (if applicable)
 * @param int $id_content	the content id. this can be a message id but could also be a user id
 * 							(e.g. when a member posts on the profile of another member). depends on the context
 * @param int $id_owner     the content owner (id_member)
 * @param int $priv_level   privacy level for is_private.
 * @param int $dont_notify  do not send the owner a notification for the activity.
 *
 * @return unique id of the inserted activity type
 */
function aStreamAdd($id_member, $atype, $params, $id_board = 0, $id_topic = 0, $id_content = 0, $id_owner = 0, $priv_level = 0, $dont_notify = false)
{
	$act_must_notify = array(ACT_LIKE, ACT_REPLIED);	// these activity types will trigger a *mandatory*
	if(0 == $id_member || 0 == $id_owner)				// notification for $id_owner unless $dont_notify indicates otherwise
		return;

	smf_db_insert('',
		'{db_prefix}log_activities',
		array(
			'id_member' => 'int', 'id_type' => 'int', 'updated' => 'int',
			'params' => 'string', 'is_private' => 'int', 'id_board' => 'int',
			'id_topic' => 'int', 'id_content' => 'int', 'id_owner' => 'int'
		),
		array(
			(int)$id_member, (int)$atype, time(),
			serialize($params), $priv_level, (int)$id_board, (int)$id_topic, (int)$id_content, (int)$id_owner
		),
	    array('id_act')
	);
	$id_act = smf_db_insert_id('{db_prefix}log_activities', 'id_act');

	// if this activity triggers a notification for the id_owner, use the $id_act to link it
	// to the notifications table.
	if($id_owner && in_array($atype, $act_must_notify) && !$dont_notify)
		aStreamAddNotification($id_owner, $id_act);
	return($id_act);
}

/**
 * @param $users    array member_id or array of member_ids
 * @param $id_act   int id of the activity to send as notification
 *
 * this takes a single id_member or an array of such ids plus an activity id
 * and sends out notifications to the members.
 */
function aStreamAddNotification(&$users, $id_act)
{
	$users = !is_array($users) ? array($users) : array_unique($users);

	$values = array();
	foreach($users as $user) {
		if((int)$user)
			$values[] = '('.(int)$user.', '.(int)$id_act.')';
	}
	if(count($values)) {
		$q = 'INSERT INTO {db_prefix}log_notifications (id_member, id_act) VALUES ' . join(',', $values);
		smf_db_query($q);

		foreach($users as $user)
			updateMemberData($user, array('last_login' => time()));
	}
}

/**
 * @param $params (array with relevant stream entry data)
 *
 * standard formatter for activity stream entries. Gets the formatting string from the language
 * file. format must be: acfmt_activity_id_x (where x is the subtype)
 *
 * there will be a hook to add stream activity types and matching corresponding
 * formatter functions in the future.
 *
 */
function actfmt_default(&$params)
{
	global $user_info, $txt;

	$key = $params['f_neutral'];
	if((int)$params['id_member'] === (int)$user_info['id'] && $params['id_owner'] && (int)$params['id_owner'] === (int)$user_info['id'])
		$key = $params['f_you_your'];
	else if((int)$params['id_member'] === (int)$user_info['id'])
		$key = $params['f_you'];
	else if($params['id_owner'] && (int)$params['id_owner'] === (int)$user_info['id'])
	    $key = $params['f_your'];

	$_k = 'acfmt_' . $params['id_desc'] . '_' . trim($key);
	if(isset($txt[$_k]))
		return(_vsprintf($txt[$_k], $params));
	else {
		$_s = sprintf($txt['activity_missing_format'], $params['id_type']);
		log_error($_s);
		return($_s);
	}
}
/**
 * @param $row - a full row from log_activities and activity_type
 * @return void
 *
 * this expects a full row of log_activities.* and activity_types.formatter
 * in $row and will format it, using the formatter callback function
 * we move things like id_topic, id_board et all into the array so the
 * formatting function can use them.
 */
function aStreamFormatActivity(&$row, $is_notification = false)
{
	global $scripturl, $txt;
	
	$params = unserialize($row['params']);
	unset($row['params']);
	// populate the array with the remaining database columns
	$params = array_merge($params, $row);
	$callback = $row['formatter'];
	if(function_exists($callback)) {
		$out = call_user_func_array($callback, array(&$params));
		$out = preg_replace('/@SCRIPTURL@/', $scripturl, $out);
		$row['formatted_result'] = preg_replace('/@NM@/', $is_notification ? ';nmdismiss=' . $row['id_act'] : '', $out);
	}
	else {
		$_s = sprintf($txt['activity_missing_callback'], $params['id_type']);
		$row['formatted_result'] = $_s;
		log_error($_s);
	}
}

/**
 * @param $content_ids  array of content ids
 *
 * remove all activities and linked notifications that relate to the given set
 * of content ids.
 */
function aStreamRemoveByContent($content_ids, $types = array())
{
	// types that define a activity related to a post
	// this is needed, because id_content does not *have* to be a id_msg, it could be a pm id or even user id
	// depending on the activity type.
	if(empty($types))
		$types = array(ACT_LIKE, ACT_MODIFY_POST, ACT_NEWTOPIC, ACT_REPLIED);

	smf_db_query('
		DELETE a.*, n.* FROM {db_prefix}log_activities AS a LEFT JOIN {db_prefix}log_notifications AS n ON(n.id_act = a.id_act)
		WHERE a.id_content IN ({array_int:content_ids}) AND a.id_type IN ({array_int:types})',
		array('content_ids' => $content_ids, 'types' => $types));
}

/**
 * @param $topic_ids  array of topic ids
 *
 * remove all activities and linked notifications that relate to the given set
 * of topic ids
 *
 * used when a topic is really deleted (not recycled)
 */
function aStreamRemoveByTopic($topic_ids)
{
	smf_db_query('
		DELETE a.*, n.* FROM {db_prefix}log_activities AS a LEFT JOIN {db_prefix}log_notifications AS n ON(n.id_act = a.id_act)
		WHERE a.id_topic IN ({array_int:topic_ids})',
		array('topic_ids' => $topic_ids));
}
?>
