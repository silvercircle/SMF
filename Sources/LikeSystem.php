<?php
/*
 * update like statistics for the given post
 * $mid = message id
 * 
 * todo: needed db updates for installer
 * messages: likes_count INT(4), likes_status VARCHAR(120)
 * members:  likes_received INT(4);
 */

loadLanguage('Like');

function LikesUpdate($mid)
{
	global $context;
	global $settings, $user_info, $sourcedir, $smcFunc;
	$first = true;
	$like_string = '';
	$count = 0;
	$likers = array();
	
	$request = $smcFunc['db_query']('', '
		SELECT l.id_msg AS like_message, l.id_user AS like_user, m.real_name AS member_name FROM {db_prefix}likes AS l
			LEFT JOIN {db_prefix}members AS m on m.id_member = l.id_user WHERE l.id_msg = {int:id_message} AND m.id_member <> 0 ORDER BY l.updated DESC LIMIT 4', array('id_message' => $mid));

	while ($row = $smcFunc['db_fetch_assoc']($request)) {
		if(empty($row['member_name']))
			continue;
		$likers[$count] = $row['like_user'].'[**]' . $row['member_name'];
		$count++;
		if($count > 3)
			break;
	}
	$like_string = implode("(**)", $likers);
	$smcFunc['db_free_result']($request);
	
	
	$request = $smcFunc['db_query']('', '
		SELECT COUNT(id_msg) as count
			FROM {db_prefix}likes AS l WHERE l.id_msg = {int:id_msg}', array('id_msg' => $mid));
	
	$count = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);
	$totalcount = $count[0];

	$time = time();	
	
	$smcFunc['db_query']('', '
		INSERT INTO {db_prefix}like_cache VALUES({int:id_msg}, {int:total}, {string:like_status}, {int:updated}) 
			ON DUPLICATE KEY UPDATE updated = {int:updated}, likes_count = {int:total}, like_status = {string:like_status}',
			array('id_msg' => $mid, 'total' => $totalcount, 'updated' => $time, 'like_status' => $like_string));

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
	global $user_info, $scripturl, $like_template, $txt;
	
	$like_template = array();
	$like_template[1] = $txt['1like'];
	$like_template[2] = $txt['2likes'];
	$like_template[3] = $txt['3likes'];
	$like_template[4] = $txt['4likes'];
	$like_template[5] = $txt['5likes'];

	$likers = explode("(**)", $like_status);
	$n = 1;
	$results = array();
	foreach($likers as $liker) {
		if(!empty($liker)) {
			$liker_components = explode("[**]", $liker);
			if(isset($liker_components[0]) && isset($liker_components[1])) {
				if($liker_components[1] === $user_info['name']) {
					$results[0] = $txt['you_liker'];
					continue;
				}
				$results[$n++] = '<a rel="nofollow" class="mcard" data-id="'.$liker_components[0].'" href="'.$scripturl.'?action=profile;u='.intval($liker_components[0]).'">'.$liker_components[1].'</a>';
			}
		}
	}
	$count = count($results);
	if($count == 0)
		return($output);
		
	/*
	 * we have liked it but our entry is too old to be in the top 4, so move 
	 * it to the front and remove the oldest
	 */
	if($have_liked && !isset($results[0])) {
		array_pop($results);
		$results[0] = $txt['you_liker'];
		//$count = count($results);
	}
	
	ksort($results);
	if(isset($results[0]) && $count == 1)
		$output = $txt['you_like_it'];
	else if($total_likes > 4) {
		$output = vsprintf($like_template[5], $results);
		$output .= ('<a rel="nofollow" href="'.$scripturl.'?action=getlikes;m='.$mid.'">'. ($total_likes - $count).$txt['like_others']);
	}
	else
		$output = vsprintf($like_template[$count], $results);
	return($output);
}

function LikesError($msg, $xmlreq)
{
	if($xmlreq) {
		echo $msg;
		die;
	}
	fatal_error($msg, '');
}
?>
