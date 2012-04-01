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
// todo: use this for subscribed boards as well

function template_board_children(&$board)
{
	global $modSettings, $scripturl, $txt, $context, $settings;

	$children = array();
	foreach ($board['children'] as $child)
	{
		if (!$child['is_redirect']) {
			$child['link'] = '<h4 class="childlink"><a data-tip="tip_b_'.$child['id'].'" href="' . $child['href'] . '" class="boardlink easytip">' . $child['name'] . '</a></h4>';
			$child['img'] = '<div class="csrcwrapper16px" style="left:-12px;margin-bottom:-16px;"><img class="clipsrc '.($child['new'] ? '_child_new' : '_child_old').'" src="' . $settings['images_url'] . '/'. $context['theme_variant_url'] . 'theme/sprite.png" alt="*" title="*" /></div>';
			$child['tip'] = '<div id="tip_b_'.$child['id'].'" style="display:none;">' . (!empty($child['description']) ? $child['description'] . '<br>' : '') . ($child['new'] ? $txt['new_posts'] : $txt['old_posts']) . ' (' . $txt['board_topics'] . ': ' . comma_format($child['topics']) . ', ' . $txt['posts'] . ': ' . comma_format($child['posts']) . ')' . '</div>';
		}
		else {
			$child['link'] = '<a class="boardlink" href="' . $child['href'] . '" title="' . comma_format($child['posts']) . ' ' . $txt['redirects'] . '"><h4>' . $child['name'] . '</h4></a>'.'&nbsp;<span class="tinytext lowcontrast">('.$child['description'].')</span>';
			$child['img'] = $child['tip'] = '';
		}
		if ($child['can_approve_posts'] && ($child['unapproved_posts'] || $child['unapproved_topics']))
			$child['link'] .= ' <a href="' . $scripturl . '?action=moderate;area=postmod;sa=' . ($child['unapproved_topics'] > 0 ? 'topics' : 'posts') . ';brd=' . $child['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '" title="' . sprintf($txt['unapproved_posts'], $child['unapproved_topics'], $child['unapproved_posts']) . '" class="moderation_link">(!)</a>';

		$children[] = array(
			'link' => $child['link'],
			'new' => $child['new'],
			'img' => $child['img'],
			'tip' => $child['tip']
			);
	}
	echo '
	<div class="td_children" id="board_', $board['id'], '_children">
		<table>
		  <tr>';
		  $n = 0;
		  $columns = $modSettings['tidy_child_display_columns'];
		  $width = 100 / $columns;
		  foreach($children as &$child) {
			  echo '<td style="width:',$width,'%;" class="tinytext"><div style="padding-left:12px;">',$child['img'],$child['link'],'</div>',$child['tip'],'</td>';
			  if(++$n >= $columns) {
				  $n = 0;
				  echo '</tr><tr>';
			  }
		  }
		  echo '
		  </tr>
		</table>
	</div>';
}

function template_boardbit(&$board)
{
	global $context, $txt, $scripturl, $options, $settings;
	if($board['act_as_cat'])
		return template_boardbit_subcat($board);
	echo '
	<li id="board_', $board['id'], '" class="boardrow rowgradient">';
	if(!$board['is_page']) {
		echo'
		<div class="info">
		 <div class="icon floatleft">
		  <a href="', ($board['is_redirect'] || $context['user']['is_guest'] ? $board['href'] : $scripturl . '?action=unread;board=' . $board['id'] . '.0;children'), '">
		  <div class="csrcwrapper24px">';

		if(!empty($board['boardicon'])) {
			echo '
		  <img src="', $settings['images_url'], '/', $context['theme_variant_url'], 'boards/',$board['boardicon'],'.png" alt="', $txt['new_posts'], '" title="', $txt['new_posts'], '" />';
			if($board['new'] || $board['children_new'])
				echo '<img style="position:absolute;bottom:-4px;right:-3px;" src="',$settings['images_url'], '/new.png" />';
		}
		else {
		// If the board or children is new, show an indicator.
			if ($board['is_redirect'])
				echo '
		  <img class="clipsrc _redirect" src="', $settings['images_url'], '/', $context['theme_variant_url'], 'clipsrc.png" alt="*" title="*" />';
		// No new posts at all! The agony!!
			else
				echo '
		  <img class="clipsrc _off" src="', $settings['images_url'], '/', $context['theme_variant_url'], 'clipsrc.png" alt="', $txt['old_posts'], '" title="', $txt['old_posts'], '" />';
			if($board['new'] || $board['children_new'])
				echo '
		  <img style="position:absolute;bottom:-4px;right:-3px;" src="',$settings['images_url'], '/new.png" />';
		}
	echo '</div>
		  </a>
		</div>
		<div style="padding-left:32px;">
		  <a class="brd_rsslink" href="',$scripturl,'?action=.xml;type=rss;board=',$board['id'],'">&nbsp;</a>';
			
		// Show the "Moderators: ". Each has name, href, link, and id. (but we're gonna use link_moderators.)
		if (!empty($board['moderators']))
			echo '
		  <span onclick="brdModeratorsPopup($(this));" class="brd_moderators" title="',$txt['moderated_by'],'"><span class="brd_moderators_chld" style="display:none;">', $txt['moderated_by'], ': ',implode(', ', $board['link_moderators']), '</span></span>';
		echo '
		  <h3>
		   <a class="boardlink easytip" data-tip="tip_b_',$board['id'],'" href="', $board['href'], '" id="b', $board['id'], '">', $board['name'], '</a>
		  </h3>
		  <div style="display:none;" id="tip_b_',$board['id'],'">',$board['description'],'</div>';

	// Has it outstanding posts for approval?
	if ($board['can_approve_posts'] && ($board['unapproved_posts'] || $board['unapproved_topics']))
		echo '
		  <a href="', $scripturl, '?action=moderate;area=postmod;sa=', ($board['unapproved_topics'] > 0 ? 'topics' : 'posts'), ';brd=', $board['id'], ';', $context['session_var'], '=', $context['session_id'], '" title="', sprintf($txt['unapproved_posts'], $board['unapproved_topics'], $board['unapproved_posts']), '" class="moderation_link">(!)</a>';

	echo '
		 <div class="tinytext">', $board['posts'],' <span class="lowcontrast">',$txt['posts'], ' ', $txt['in'], '</span> ',$board['topics'],' <span class="lowcontrast">',$txt['topics'],'</span></div>
		<div class="lastpost tinytext lowcontrast">';
	if (!empty($board['last_post']['id']))
			echo (empty($options['post_icons_index']) ? '' : '
		<img src="'.$board['first_post']['icon_url'].'" alt="icon" />'), '
		',$board['last_post']['prefix'],$board['last_post']['topiclink'], '<br />
		<a class="lp_link" title="',$txt['last_post'],'" href="',$board['last_post']['href'],'">',$board['last_post']['time'], '</a>
		<span class="tinytext lowcontrast" ',(empty($options['post_icons_index']) ? '' : 'style="padding-left:20px;"'),'>',$txt['last_post'],'&nbsp;',$txt['by'],':&nbsp;</span>', $board['last_post']['member']['link'];
	else
		echo $txt['not_applicable'];
	echo '
		</div>
		</div>
		</div>';
	}
	else {
		echo '
		<div class="info fullwidth">
	 	<div class="icon floatleft">
	  	<div class="csrcwrapper24px"><img class="clipsrc _page" src="', $settings['images_url'], '/', $context['theme_variant_url'], 'clipsrc.png" alt="*" title="*" /></div>
	 	</div>
		<div style="padding-left:32px;">
		<h3><a class="boardlink" href="',URL::topic(intval(substr($board['redirect'], 1)), $board['name'], 0),'">',$board['name'],'</a></h3>
	    <div class="tinytext lowcontrast">', $board['description'] , '</div>
	    </div>
	    </div>';
	}
	// Show the "Child Boards: ". (there's a link_children but we're going to bold the new ones...)
	if (!empty($board['children']))
	{
		template_board_children($board);
	}
	else
		echo '
		<div></div>';

	echo '
	 <div class="clear_left"></div>
	</li>';
}

function template_boardbit_subcat(&$board)
{
	global $context, $txt, $scripturl, $modSettings, $options, $settings;
	echo '
	<li id="board_', $board['id'], '" class="subcatrow">';
		echo'
		<div class="blue_container">
		<div class="info subcat">
		 <div class="icon floatleft">
		  <a href="', ($board['is_redirect'] || $context['user']['is_guest'] ? $board['href'] : $scripturl . '?action=unread;board=' . $board['id'] . '.0;children'), '">
		  <div class="csrcwrapper24px">';

		if(!empty($board['boardicon'])) {
			echo '
		  <img src="', $settings['images_url'], '/', $context['theme_variant_url'], 'boards/',$board['boardicon'],'.png" alt="', $txt['new_posts'], '" title="', $txt['new_posts'], '" />';
			if($board['new'] || $board['children_new'])
				echo '<img style="position:absolute;bottom:-4px;right:-3px;" src="',$settings['images_url'], '/new.png" />';
		}
		else {
		// If the board or children is new, show an indicator.
			echo '
		  <img class="clipsrc _subcat" src="', $settings['images_url'], '/', $context['theme_variant_url'], 'clipsrc.png" alt="', $txt['old_posts'], '" title="', $txt['old_posts'], '" />';
		 	if($board['new'] || $board['children_new'])
			echo '
		  <img style="position:absolute;bottom:-4px;right:-3px;" src="',$settings['images_url'], '/new.png" />';
		}
	echo '</div>
		  </a>
		</div>
		<div style="padding-left:32px;">
	    <h3 class="subcatlink"><a class="boardlink" href="', $board['href'], '" id="b', $board['id'], '">', $board['name'], '</a></h3>';

	// Has it outstanding posts for approval?
	if ($board['can_approve_posts'] && ($board['unapproved_posts'] || $board['unapproved_topics']))
		echo '
		  <a href="', $scripturl, '?action=moderate;area=postmod;sa=', ($board['unapproved_topics'] > 0 ? 'topics' : 'posts'), ';brd=', $board['id'], ';', $context['session_var'], '=', $context['session_id'], '" title="', sprintf($txt['unapproved_posts'], $board['unapproved_topics'], $board['unapproved_posts']), '" class="moderation_link">(!)</a>';

	echo '
		 <div class="tinytext lowcontrast">', $board['description'] , '</div>';
	echo '
		</div>
		</div>';
	// Show the "Child Boards: ". (there's a link_children but we're going to bold the new ones...)
	if (!empty($board['children']))
		template_board_children($board);
	else
		echo '
		<div></div>';

	if (!empty($board['last_post']['id']))
			echo '
		<div class="tinytext nowrap righttext" style="position:static;max-width:auto;">
		<a class="lp_link" title="',$txt['last_post'],'" href="',$board['last_post']['href'],'">',$board['last_post']['time'], '</a><span class="tinytext lowcontrast">',$txt['last_post'],' in: </span>',$board['last_post']['prefix'],$board['last_post']['topiclink'], '
		&nbsp;<span class="tinytext lowcontrast">',$txt['by'],':&nbsp;</span>', $board['last_post']['member']['link'],'&nbsp;
		</div>';
	echo '
	 <div class="clear_left"></div>
	 </div>
	</li>';
}

function template_topicbit(&$topic)
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	// Is this topic pending approval, or does it have any posts pending approval?
	if ($context['can_approve_posts'] && $topic['unapproved_posts'])
		$color_class = !$topic['approved'] ? 'approvetbg' : 'approvebg';
	// We start with locked and sticky topics.
	elseif ($topic['is_sticky'] && $topic['is_locked'])
		$color_class = 'stickybg locked_sticky';
	// Sticky topics should get a different color, too.
	elseif ($topic['is_sticky'])
		$color_class = 'stickybg';
	// Locked topics get special treatment as well.
	elseif ($topic['is_locked'])
		$color_class = 'lockedbg';
	// Last, but not least: regular topics.
	else
		$color_class = 'rowgradient';

	echo '
	<tr>
	  <td class="icon1 ', $color_class, '">';
		echo '
	  <span class="small_avatar ',$topic['class'],'">';
		if(!empty($topic['first_post']['member']['avatar'])) {
			echo '
		<a href="', $scripturl, '?action=profile;u=', $topic['first_post']['member']['id'], '">
		  ', $topic['first_post']['member']['avatar'], '
		</a>';
		}
		else {
			echo '
		<a href="', $scripturl, '?action=profile;u=', $topic['first_post']['member']['id'], '">
		  <img src="',$settings['images_url'],'/unknown.png" alt="avatar" />
		</a>';
		}
				/*
				 * own avatar as overlay when 
				 * a) avatar is set
				 * b) we have posted in this topic
				 * c) we have NOT started the topic
				 */
		if($topic['is_posted_in'] && ($topic['first_post']['member']['id'] != $context['user']['id']) && isset($context['user']['avatar']['image']))
			echo '
		<span class="avatar_overlay">',$context['user']['avatar']['image'],'</span>';
		echo '</span>';

		$is_new = $topic['new'] && $context['user']['is_logged'];
		echo '
		</td>
		<td class="icon2 ', $color_class, '">
			<img src="', $topic['first_post']['icon_url'], '" alt="" />
		</td>
		<td class="subject ',$color_class,'">
			<div ', (!empty($topic['quick_mod']['modify']) ? 'id="topic_' . $topic['first_post']['id'] . '" ondblclick="modify_topic(\'' . $topic['id'] . '\', \'' . $topic['first_post']['id'] . '\');"' : ''), '>
			<span class="tpeek" data-id="'.$topic['id'].'" id="msg_' . $topic['first_post']['id'] . '">', $topic['prefix'], ($is_new ? '<strong>' : '') , $topic['first_post']['link'], (!$context['can_approve_posts'] && !$topic['approved'] ? '&nbsp;<em>(' . $txt['awaiting_approval'] . ')</em>' : ''), ($is_new ? '</strong>' : ''), '</span>';

	// Is this topic new? (assuming they are logged in!)
		if ($is_new)
			echo '
			<a href="', $topic['new_href'], '" id="newicon' . $topic['first_post']['id'] . '"><img src="', $settings['images_url'], '/new.png" alt="', $txt['new'], '" /></a>';

		echo '
			<p>', $topic['first_post']['member']['link'],', ',$topic['first_post']['time'], '
			  <small id="pages' . $topic['first_post']['id'] . '">', $topic['pages'], '</small>
			</p>
			</div>
		</td>
		<td class="stats nowrap ', $color_class, '">';
			if($topic['replies'])
				echo '
			<a rel="nofollow" title="',$txt['who_posted'],'" onclick="whoPosted($(this));return(false);" class="whoposted" data-topic="',$topic['id'], '" href="',$scripturl,'?action=xmlhttp;sa=whoposted;t=',$topic['id'],'" >', $topic['replies'], ' ', $txt['replies'], '</a>';
			else
				echo $topic['replies'], ' ', $txt['replies'];
			echo '
			<br />
				', $topic['views'], ' ', $txt['views'], '
		</td>
		<td class="lastpost ', $color_class, '">',
			$txt['by'], ': ', $topic['last_post']['member']['link'], '<br />
			<a class="lp_link" title="', $txt['last_post'], '" href="', $topic['last_post']['href'], '">',$topic['last_post']['time'], '</a>
		</td>';

	// Show the quick moderation options?
	if (!empty($context['can_quick_mod']))
	{
		echo '
			<td class="moderation ', $color_class, '" style="text-align:center;">';
		if ($options['display_quick_mod'])
			echo '
				<input type="checkbox" name="topics[]" value="', $topic['id'], '" class="input_check cb_inline" />';
		echo '
			</td>';
	}
	echo '
		</tr>';
}

function template_topicbit_generic(&$topic)
{
	global $alternate;
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	if ($topic['is_sticky'] && $topic['is_locked'])
		$color_class = 'stickybg locked_sticky';
	// Sticky topics should get a different color, too.
	elseif ($topic['is_sticky'])
		$color_class = 'stickybg';
	// Locked topics get special treatment as well.
	elseif ($topic['is_locked'])
		$color_class = 'lockedbg';
	// Last, but not least: regular topics.
	else
		$color_class = '';

	// Some columns require a different shade of the color class.
	$alternate_class = $color_class . '2';

	echo '
	<tr>
	  <td class="icon1 ', $color_class, '">';
		echo '
	  <span class="small_avatar ',$topic['class'],'">';
		if(!empty($topic['first_post']['member']['avatar'])) {
			echo '
		<a href="', $scripturl, '?action=profile;u=', $topic['first_post']['member']['id'], '">
		  ', $topic['first_post']['member']['avatar'], '
		</a>';
		}
		else {
			echo '
		<a href="', $scripturl, '?action=profile;u=', $topic['first_post']['member']['id'], '">
		  <img src="',$settings['images_url'],'/unknown.png" alt="avatar" />
		</a>';
		}
				/*
				 * own avatar as overlay when
				 * a) avatar is set
				 * b) we have posted in this topic
				 * c) we have NOT started the topic
				 */
		if($topic['is_posted_in'] && ($topic['first_post']['member']['id'] != $context['user']['id']) && isset($context['user']['avatar']['image']))
			echo '
		<span class="avatar_overlay">',$context['user']['avatar']['image'],'</span>';
		echo '</span>';

		$is_new = $topic['new'] && $context['user']['is_logged'];
		echo '
		</td>
		<td class="icon2 ', $color_class, '">
			<img src="', $topic['first_post']['icon_url'], '" alt="" />
		</td>
		<td class="subject ',$alternate_class,'">
			<div>
			<span class="tpeek" data-id="'.$topic['id'].'" id="msg_' . $topic['first_post']['id'] . '">', $topic['prefix'], ($is_new ? '<strong>' : '') , $topic['first_post']['link'], ($is_new ? '</strong>' : ''), '</span>';

	// Is this topic new? (assuming they are logged in!)
		if ($is_new)
			echo '
			<a href="', $topic['new_href'], '" id="newicon' . $topic['first_post']['id'] . '"><img src="', $settings['images_url'], '/new.png" alt="', $txt['new'], '" /></a>';

		echo '
			<p>', $topic['first_post']['member']['link'],', ',$topic['first_post']['time'], '
			  <small id="pages' . $topic['first_post']['id'] . '">', $topic['pages'], '</small>
			</p>
			</div>
		</td>
		<td class="stats ', $color_class, '">';
			if($topic['replies'])
				echo '
			<a title="',$txt['who_posted'],'" onclick="whoPosted($(this));return(false);" class="whoposted" data-topic="',$topic['id'], '" href="',$scripturl,'?action=xmlhttp;sa=whoposted;t=',$topic['id'],'" >', $topic['replies'], ' ', $txt['replies'], '</a>';
			else
				echo $topic['replies'], ' ', $txt['replies'];
			echo '
			<br />
				', $topic['views'], ' ', $txt['views'], '
		</td>
		<td class="lastpost ', $color_class, '">',
			$txt['by'], ': ', $topic['last_post']['member']['link'], '<br />
			<a class="lp_link" title="', $txt['last_post'], '" href="', $topic['last_post']['href'], '">',$topic['last_post']['time'], '</a>
		</td>';

	echo '
		</tr>';
}

/**
 * @param $member
 * @return void
 *
 * create a compact "userbit" with 3 lines of text alongside the avatar
 */
function template_userbit_compact(&$member)
{
	global $settings, $txt;
	$loc = array();

	echo '
	<div class="userbit_compact">
	<div class="floatleft">
	<span class="small_avatar">';
	if(!empty($member['avatar']['image'])) {
		echo '
	<img class="fourtyeight" src="', $member['avatar']['href'], '" alt="avatar" />';
	}
	else {
		echo '
	<img class="fourtyeight" src="',$settings['images_url'],'/unknown.png" alt="avatar" />';
	}
	echo '
	</span>
	</div>
	<div class="userbit_compact_textpart">
	<h2>', $member['link'],'</h2>
	',$member['group'], '<br>';

	if(!empty($member['gender']['name']))
		$loc[0] = $member['gender']['name'];

	if(isset($member['birth_date']) && !empty($member['birth_date'])) {
		$l = idate('Y', time()) - intval($member['birth_date']);
		if($l < 100)
			$loc[1] = $l;
	}
	if(!empty($member['location']))
		$loc[2] = ' '.$txt['ufrom'].' '.$member['location'];
	if(!empty($loc))
		echo implode(', ', $loc);

	  echo '
	<br>',$member['posts'], ' ', $txt['posts'], ' ', $txt['and'], ' ', $member['liked'], ' ',$txt['likes'],'
	</div>
	</div>';
}
?>
