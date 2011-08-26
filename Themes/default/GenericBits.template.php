<?php
// output a board row (used in BoardIndex, MessageIndex for child boards)
// todo: use this for subscribed boards as well
// todo: add threadbit
function template_boardbit(&$board)
{
	global $alternate;
	global $context, $settings, $options, $txt, $scripturl, $modSettings;
	
	$_c = ($alternate = !$alternate) ? 'windowbg' : 'windowbg2';
	echo '
	<li id="board_', $board['id'], '" class="',$_c,'">
	<div class="lastpost">';
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
		 <div class="icon" style="float:left;">
		  <a href="', ($board['is_redirect'] || $context['user']['is_guest'] ? $board['href'] : $scripturl . '?action=unread;board=' . $board['id'] . '.0;children'), '">';

		// If the board or children is new, show an indicator.
		if ($board['new'] || $board['children_new'])
			echo '
 		  <img src="', $settings['images_url'], '/', $context['theme_variant_url'], 'on', $board['new'] ? '' : '2', '.png" alt="', $txt['new_posts'], '" title="', $txt['new_posts'], '" />';
		// Is it a redirection board?
		elseif ($board['is_redirect'])
			echo '
		  <img src="', $settings['images_url'], '/', $context['theme_variant_url'], 'redirect.png" alt="*" title="*" />';
		// No new posts at all! The agony!!
		else
			echo '
		  <img src="', $settings['images_url'], '/', $context['theme_variant_url'], 'off.png" alt="', $txt['old_posts'], '" title="', $txt['old_posts'], '" />';

	echo '
		  </a>
		</div>
		<div style="padding-left:32px;">
		  <a class="brd_rsslink" href="',$scripturl,'?action=.xml;type=rss;board=',$board['id'],'">&nbsp;</a>';
			
		// Show the "Moderators: ". Each has name, href, link, and id. (but we're gonna use link_moderators.)
		if (!empty($board['moderators']))
			echo '
		  <span class="brd_moderators" title="',$txt['moderated_by'],'"><span class="brd_moderators_chld" style="display:none;">', $txt['moderated_by'], ': ',implode(', ', $board['link_moderators']), '</span></span>';
		echo '
		  <a href="', $board['href'], '" id="b', $board['id'], '"><h3>', $board['name'], '</h3></a>';

	// Has it outstanding posts for approval?
	if ($board['can_approve_posts'] && ($board['unapproved_posts'] || $board['unapproved_topics']))
		echo '
		  <a href="', $scripturl, '?action=moderate;area=postmod;sa=', ($board['unapproved_topics'] > 0 ? 'topics' : 'posts'), ';brd=', $board['id'], ';', $context['session_var'], '=', $context['session_id'], '" title="', sprintf($txt['unapproved_posts'], $board['unapproved_topics'], $board['unapproved_posts']), '" class="moderation_link">(!)</a>';

	echo '
		  <div class="smalltext">', $board['description'] , '</div>';

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
				$child['link'] = '<a href="' . $child['href'] . '" ' . ($child['new'] ? 'class="new_posts" ' : '') . 'title="' . ($child['new'] ? $txt['new_posts'] : $txt['old_posts']) . ' (' . $txt['board_topics'] . ': ' . comma_format($child['topics']) . ', ' . $txt['posts'] . ': ' . comma_format($child['posts']) . ')"><h4>' . $child['name'] . ($child['new'] ? '</h4></a> <a href="' . $scripturl . '?action=unread;board=' . $child['id'] . '" title="' . $txt['new_posts'] . ' (' . $txt['board_topics'] . ': ' . comma_format($child['topics']) . ', ' . $txt['posts'] . ': ' . comma_format($child['posts']) . ')"><img src="' . $settings['lang_images_url'] . '/new.gif" class="new_posts" alt="" />' : '') . '</a>';
			else
				$child['link'] = '<a href="' . $child['href'] . '" title="' . comma_format($child['posts']) . ' ' . $txt['redirects'] . '"><h4>' . $child['name'] . '</h4></a>';

			// Has it posts awaiting approval?
			if ($child['can_approve_posts'] && ($child['unapproved_posts'] || $child['unapproved_topics']))
				$child['link'] .= ' <a href="' . $scripturl . '?action=moderate;area=postmod;sa=' . ($child['unapproved_topics'] > 0 ? 'topics' : 'posts') . ';brd=' . $child['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '" title="' . sprintf($txt['unapproved_posts'], $child['unapproved_topics'], $child['unapproved_posts']) . '" class="moderation_link">(!)</a>';

			$children[] = $child['new'] ? '<strong>' . $child['link'] . '</strong>' : $child['link'];
		}
		echo '
		<div class="td_children" id="board_', $board['id'], '_children">
			<strong>', $txt['parent_boards'], '</strong>: ', implode(', ', $children), '
		</div>';
	}
		echo '
	  </div>
	 </div>
	 <div style="clear:left;"></div>
	</li>';
}
?>
