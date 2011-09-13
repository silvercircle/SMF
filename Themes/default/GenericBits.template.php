<?php
// output a board row (used in BoardIndex, MessageIndex for child boards)
// todo: use this for subscribed boards as well
function template_boardbit(&$board)
{
	global $alternate;
	global $context, $settings, $options, $txt, $scripturl, $modSettings;
	
	$_c = ($alternate = !$alternate) ? 'windowbg' : 'windowbg2';
	echo '
	<li id="board_', $board['id'], '" class="',$_c,'">';
	if(!$board['is_page']) {
	echo '
		<div class="lastpost smalltext">';
	if (!empty($board['last_post']['id']))
		echo '
		<img src="',$board['first_post']['icon_url'],'" alt="icon" />
		',$txt['in'], ': ', $board['last_post']['prefix'],'&nbsp;',$board['last_post']['topiclink'], '<br />
		<a class="lp_link" title="',$txt['last_post'],'" href="',$board['last_post']['href'],'">',$board['last_post']['time'], '</a>
		<span style="padding-left:20px;">', $txt['by'], ': </span>', $board['last_post']['member']['link'];
	else
		echo $txt['not_applicable'];
	echo '
		</div>
		<div class="stats">
		 ', comma_format($board['posts']), ' ', $board['is_redirect'] ? $txt['redirects'] : $txt['posts'], ' <br />
		 ', $board['is_redirect'] ? '' : comma_format($board['topics']) . ' ' . $txt['board_topics'], '
		</div>
		<div class="info">
		 <div class="icon floatleft">
		  <a href="', ($board['is_redirect'] || $context['user']['is_guest'] ? $board['href'] : $scripturl . '?action=unread;board=' . $board['id'] . '.0;children'), '">
		  <div class="csrcwrapper24px">';

		// If the board or children is new, show an indicator.
		if ($board['new'] || $board['children_new'])
			echo '
 		  <img class="clipsrc _on" src="', $settings['images_url'], '/', $context['theme_variant_url'], 'clipsrc.png" alt="', $txt['new_posts'], '" title="', $txt['new_posts'], '" />';
		// Is it a redirection board?
		elseif ($board['is_redirect'])
			echo '
		  <img class="clipsrc _redirect" src="', $settings['images_url'], '/', $context['theme_variant_url'], 'clipsrc.png" alt="*" title="*" />';
		// No new posts at all! The agony!!
		else
			echo '
		  <img class="clipsrc _off" src="', $settings['images_url'], '/', $context['theme_variant_url'], 'clipsrc.png" alt="', $txt['old_posts'], '" title="', $txt['old_posts'], '" />';

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
		  <h3><a href="', $board['href'], '" id="b', $board['id'], '">', $board['name'], '</a></h3>';

	// Has it outstanding posts for approval?
	if ($board['can_approve_posts'] && ($board['unapproved_posts'] || $board['unapproved_topics']))
		echo '
		  <a href="', $scripturl, '?action=moderate;area=postmod;sa=', ($board['unapproved_topics'] > 0 ? 'topics' : 'posts'), ';brd=', $board['id'], ';', $context['session_var'], '=', $context['session_id'], '" title="', sprintf($txt['unapproved_posts'], $board['unapproved_topics'], $board['unapproved_posts']), '" class="moderation_link">(!)</a>';

	echo '
		  <div class="smalltext">', $board['description'] , '</div>';
	}
	else {
		echo '
		<div class="info">
	 	<div class="icon floatleft">
	  	<div class="csrcwrapper24px"><img class="clipsrc _page" src="', $settings['images_url'], '/', $context['theme_variant_url'], 'clipsrc.png" alt="*" title="*" /></div>
	 	</div>
		<div style="padding-left:32px;">
		<h3><a href="',$scripturl,'?topic=',intval(substr($board['redirect'], 1)),'">',$board['name'],'</a></h3>
	    <div class="smalltext">', $board['description'] , '</div>';
	}
	// Show the "Child Boards: ". (there's a link_children but we're going to bold the new ones...)
	if (!empty($board['children']))
	{
		// Sort the links into an array with new boards bold so it can be imploded.
		$children = array();
		/* Each child in each board's children has:
				id, name, description, new (is it new?), topics (#), posts (#), href, link, and last_post. */
		foreach ($board['children'] as $child)
		{
			if (!$child['is_redirect'])
				$child['link'] = '<h4><a href="' . $child['href'] . '" ' . ($child['new'] ? 'class="new_posts" ' : 'class="no_new_posts" ') . 'title="' . ($child['new'] ? $txt['new_posts'] : $txt['old_posts']) . ' (' . $txt['board_topics'] . ': ' . comma_format($child['topics']) . ', ' . $txt['posts'] . ': ' . comma_format($child['posts']) . ')">' . $child['name'] . '</a></h4>'.'&nbsp;<span title="'.$child['description'].'">('.$child['short_description'].')</span>';
			else
				$child['link'] = '<a href="' . $child['href'] . '" title="' . comma_format($child['posts']) . ' ' . $txt['redirects'] . '"><h4>' . $child['name'] . '</h4></a>'.'&nbsp;('.$child['description'].')';

			// Has it posts awaiting approval?
			if ($child['can_approve_posts'] && ($child['unapproved_posts'] || $child['unapproved_topics']))
				$child['link'] .= ' <a href="' . $scripturl . '?action=moderate;area=postmod;sa=' . ($child['unapproved_topics'] > 0 ? 'topics' : 'posts') . ';brd=' . $child['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '" title="' . sprintf($txt['unapproved_posts'], $child['unapproved_topics'], $child['unapproved_posts']) . '" class="moderation_link">(!)</a>';

			$children[] = $child['link'];
		}
		echo '
		<div class="td_children" id="board_', $board['id'], '_children">
			<table>
			  <tr>';
			  $n = 0;
			  $columns = $modSettings['tidy_child_display_columns'];
			  foreach($children as $child) {
				  echo '<td>',$children[$n++],'</td>';
				  if($n >= $columns) {
					  $n = 0;
					  echo '</tr><tr>';
				  }
			  }
			  echo '
			  </tr>
			</table>
		</div>';
	}
		echo '
	  </div>
	 </div>
	 <div class="clear_left"></div>
	</li>';
}

function template_topicbit(&$topic)
{
	global $alternate;
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
			<div ', (!empty($topic['quick_mod']['modify']) ? 'id="topic_' . $topic['first_post']['id'] . '" ondblclick="modify_topic(\'' . $topic['id'] . '\', \'' . $topic['first_post']['id'] . '\');"' : ''), '>
			<span class="tpeek" data-id="'.$topic['id'].'" id="msg_' . $topic['first_post']['id'] . '">', $topic['prefix'], ($is_new ? '<strong>' : '') , $topic['first_post']['link'], (!$context['can_approve_posts'] && !$topic['approved'] ? '&nbsp;<em>(' . $txt['awaiting_approval'] . ')</em>' : ''), ($is_new ? '</strong>' : ''), '</span>';

	// Is this topic new? (assuming they are logged in!)
		if ($is_new)
			echo '
			<a href="', $topic['new_href'], '" id="newicon' . $topic['first_post']['id'] . '"><img src="', $settings['lang_images_url'], '/new.gif" alt="', $txt['new'], '" /></a>';

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
/**
 * @param $member
 * @return void
 *
 * create a compact "userbit" with 3 lines of text alongside the avatar
 */
function template_userbit_compact(&$member)
{
	global $scripturl, $settings;

	echo '
	  <span class="small_avatar floatleft">';
		if(!empty($member['avatar']['image'])) {
			echo '
		<a href="', $scripturl, '?action=profile;u=', $member['id'], '">
		  <img class="fourtyeight" src="', $member['avatar']['href'], '" alt="avatar" />
		</a>';
		}
		else {
			echo '
		<a href="', $scripturl, '?action=profile;u=', $member['id'], '">
		  <img class="fourtyeight" src="',$settings['images_url'],'/unknown.png" alt="avatar" />
		</a>';
		}
	echo '
	  </span>
	  <div style="padding-left:10px;">
	  <strong><h3>', $member['link'],'</h3></strong>
	  </div>
	  <div class="clear"></div>';
}
?>
