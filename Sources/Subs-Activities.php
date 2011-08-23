<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines http://www.simplemachines.org
 * @copyright 2011 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0
 */

define('ACT_LIKE', 1);			// user liked a post (for activities)
define('ACT_LIKED', 1);			// a user's post was liked by another member (for notification / alerts)

// vsprintf for associative arrays, originally found somewhere on the net, slightly modified to fit the purpose here
// takes a formatting string like:  %member_name$s did something in %id_topic$s
// values of $data are matched on their key names
// Example: _vsprintf(%member_name$s did something in %id_topic$s, array('member_name' => 'foo', 'id_topic' => 1202)
// Output: foo did something in 1202
function _vsprintf( $format, array $data)
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

function stream_add_activity($id_member, $atype, $params, $id_board)
{
	global $smcFunc;
	
	$smcFunc['db_query']('', '
		INSERT INTO {db_prefix}log_activities (id_member, id_type, updated, params, is_private, id_board) 
			VALUES({int:id_member}, {int:id_type}, {int:updated}, {string:params}, {int:private}, {int:board})',
			array('id_member' => $id_member, 'id_type' => $atype, 'updated' => time(),
			'params' => serialize($params), 'private' => 0, 'board' => $id_board));
			
	//$out = _vsprintf($txt['actfmt_like_given'], $params);
	//echo preg_replace('/@SCRIPTURL@/', $scripturl, $out);
	//echo @serialize($params);
	//stream_format_test();
}

function actfmt_like_out(&$params)
{
	global $txt;
	
	$out = _vsprintf($txt['actfmt_like_given'], $params);
	return $out;
}

// this expects a full row of log_activities.* and activity_types.formatter
// in $row and will format it, using the formatter callback function
function stream_format_activity(&$row)
{
	global $scripturl;
	
	$params = unserialize($row['params']);
	
	$callback = $row['formatter'];
	if(function_exists($callback)) {
		$out = $callback($params);
		$row['formatted_result'] = preg_replace('/@SCRIPTURL@/', $scripturl, $out);
	}
	else
		$row['formatted_result'] = 'unknown activity stream type';
}

// format a activity row
function stream_format_test()
{
	loadLanguage('Activities');
	
	global $smcFunc, $txt, $scripturl;

	$result = $smcFunc['db_query']('','
		SELECT a.*, t.formatter FROM {db_prefix}log_activities AS a 
		LEFT JOIN {db_prefix}activity_types AS t ON (t.id_type = a.id_type)');
		
	while($row = $smcFunc['db_fetch_assoc']($result)) {
		stream_format_activity($row);
		echo $row['formatted_result'];
	}
		
	$smcFunc['db_free_result']($result);
}
?>
