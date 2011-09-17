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

define('ACT_LIKE', 1);			// user liked a post (for activities)
define('ACT_NEWTOPIC', 2);
define('ACT_REPLIED', 3);

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
 * @param $id_member		the member id who owns this activity (= who did it)
 * @param $atype			activity type (numeric)
 * @param $params			array with parameters, mostly for formatting
 * @param int $id_board		the board id where it happened (if applicable)
 * @param int $id_topic		the topic id where it happened (if applicable)
 * @param int $id_content	the content id. this can be a message id but could also be a user id
 * 							(e.g. when a member posts on the profile of another member).
 */
function aStreamAdd($id_member, $atype, $params, $id_board = 0, $id_topic = 0, $id_content = 0, $id_owner = 0)
{
	smf_db_query( '
		INSERT INTO {db_prefix}log_activities (id_member, id_type, updated, params, is_private, id_board, id_topic, id_content, id_owner)
			VALUES({int:id_member}, {int:id_type}, {int:updated}, {string:params}, {int:private}, {int:board}, {int:topic}, {int:content}, {int:id_owner})',
			array('id_member' => (int)$id_member, 'id_type' => (int)$atype, 'updated' => time(),
			'params' => serialize($params), 'private' => 0, 'board' => (int)$id_board, 'topic' => $id_topic, 'content' => $id_content, 'id_owner' => $id_owner));
}

function aStreamGetMemberIntro(&$params)
{
	global $txt, $user_info;

	if($params['id_member'] == $user_info['id'])
		return($txt['activity_format_member_intro_you']);
	else
		return($txt['activity_format_member_intro_you']);
}
/**
 * @param $params (array with relevant stream entry data)
 *
 * standard formatter for activity stream entries. Gets the formatting string from the language
 * file. format must be: activity_format_x (where x = the numeric activity type)
 *
 * a matching activity_format_x_you must exist to format stream entries that belong
 * to the current user (e.g. You liked a post in [topic]).
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
	else
		return(sprintf($txt['activity_missing_format'], $params['id_type']));
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
function aStreamFormatActivity(&$row)
{
	global $scripturl, $txt;
	
	$params = unserialize($row['params']);
	unset($row['params']);
	// populate the array with the remaining database columns
	$params = array_merge($params, $row);
	$callback = $row['formatter'];
	if(function_exists($callback)) {
		$out = call_user_func_array($callback, array(&$params));
		$row['formatted_result'] = preg_replace('/@SCRIPTURL@/', $scripturl, $out);
	}
	else
		$row['formatted_result'] = $txt['unknown activity stream type'];
}
?>
