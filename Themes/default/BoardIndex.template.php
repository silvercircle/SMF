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
function template_main()
{
	global $context, $settings, $txt, $scripturl;

	echo $context['template_hooks']['boardindex']['above_boardlisting'],'
	<div id="boardindex">';
	/* Each category in categories is made up of:
	id, href, link, name, is_collapsed (is it collapsed?), can_collapse (is it okay if it is?),
	new (is it new?), collapse_href (href to collapse/expand), collapse_image (up/down image),
	and boards. (see below.) */
	foreach ($context['categories'] as &$category)
	{
		// If theres no parent boards we can see, avoid showing an empty category (unless its collapsed)
		if (empty($category['boards']) && !$category['is_collapsed'])
			continue;

		if(!$category['is_root']) {
		echo '
			<div class="category" id="category_', $category['id'], '">
 			  <div class="cat_bar2">';

		// If this category even can collapse, show a link to collapse it.
		if ($category['can_collapse'])
			echo '
					<div class="csrcwrapper16px floatright"><a onclick="catCollapse($(this));return(false);" data-id="',$category['id'], '" class="collapse floatright" href="', $category['collapse_href'], '">', $category['collapse_image'], '</a></div>';

		if (!$context['user']['is_guest'] && $category['new'])
			echo '
					<a class="unreadlink" href="', $scripturl, '?action=unread;c=', $category['id'], '">', $txt['view_unread_category'], '</a>';

		echo '
				<h3>
				', $category['link'], '
			  	</h3>
		      </div>
			</div>';
		}
		echo '
			<div class="framed_region cleantop ', $category['is_root'] ? 'root_cat' : 'normal_cat', '" style="',' ',$category['is_collapsed'] ? 'display:none;" ' : '', '" id="category_', $category['id'], '_boards">
			<ol class="commonlist category">';
		if(!empty($category['desc']))
			echo '
			<li class="cat_desc">
			<h3>',$category['desc'],'</h3>
			</li>';

		foreach ($category['boards'] as &$board)
			template_boardbit($board);
		echo '
			</ol>
			</div>
			<div class="cContainer_end"></div>';
	}
	if($context['hidden_boards']['hidden_count']) {
		echo '
			<div id="show_hidden_boards" class="orange_container norounded gradient_darken_down tinytext"><span class="floatright">',$context['hidden_boards']['setup_notice'],'</span><strong>',sprintf($context['hidden_boards']['notice'], $context['hidden_boards']['hidden_count'],'<a onclick="$(\'div#category_0\').fadeIn();return(false);" href="!#">'), '</strong></div>
			<div class="category" id="category_', $context['hidden_boards']['id'], '" style="display:none;">
				<div class="framed_region cleantop root_cat" id="category_', $context['hidden_boards']['id'], '_boards">
					<ol class="commonlist category">';
		foreach ($context['hidden_boards']['boards'] as &$board)
			template_boardbit($board);
		echo '
					</ol>
				</div>
			</div>
			<div class="cContainer_end"></div>';
	}
	echo '
	</div>';

	if ($context['user']['is_logged'])
	{
		echo '
	<div id="posting_icons" class="floatleft">';

		// Mark read button.
		$mark_read_button = array(
			'markread' => array('text' => 'mark_as_read', 'image' => 'markread.gif', 'lang' => true, 'url' => $scripturl . '?action=markasread;sa=all;' . $context['session_var'] . '=' . $context['session_id']),
		);

		echo '
		<table>
		<tr>
			<td>
			<div>
				<div style="left:-25px;margin-right:-25px;" class="csrcwrapper24px"><img class="clipsrc _off" src="', $settings['images_url'], '/', $context['theme_variant_url'], 'clipsrc.png" alt="" />
			    <img alt="" style="position:absolute;bottom:-4px;right:-28px;" src="',$settings['images_url'], '/new.png" />
				</div>
			</div>
			</td>
			<td class="nowrap smalltext" style="padding-left:28px;">', $txt['new_posts'], '</td>
			<td><div class="csrcwrapper24px"><img class="clipsrc _off" src="', $settings['images_url'], '/', $context['theme_variant_url'], 'clipsrc.png" alt="" /></div></td><td class="nowrap smalltext" style="padding-left:28px;">', $txt['old_posts'], '</td>
			<td><div class="csrcwrapper24px"><img class="clipsrc _redirect" src="', $settings['images_url'], '/', $context['theme_variant_url'], 'clipsrc.png" alt="" /></div></td><td class="nowrap smalltext" style="padding-left:28px;">', $txt['redirect_board'], '</td>
			<td><div class="csrcwrapper24px"><img class="clipsrc _page" src="', $settings['images_url'], '/', $context['theme_variant_url'], 'clipsrc.png" alt="" /></div></td><td class="nowrap smalltext" style="padding-left:28px;">', $txt['a_page'], '</td>
		</tr>
		</table>
	</div>';

		// Show the mark all as read button?
		if ($settings['show_mark_read'] && !empty($context['categories']))
			echo '<div class="mark_read">', template_button_strip($mark_read_button, 'right'), '</div>';
	}
	else
	{
		echo '
	<div id="posting_icons" class="flow_hidden">
		<table>
		<tr>
			<td><div class="csrcwrapper24px"><img class="clipsrc _off" src="', $settings['images_url'], '/', $context['theme_variant_url'], 'clipsrc.png" alt="" /></div></td><td class="nowrap smalltext" style="padding-left:28px;">', $txt['old_posts'], '</td>
			<td><div class="csrcwrapper24px"><img class="clipsrc _redirect" src="', $settings['images_url'], '/', $context['theme_variant_url'], 'clipsrc.png" alt="" /></div></td><td class="nowrap smalltext" style="padding-left:28px;">', $txt['redirect_board'], '</td>
			<td><div class="csrcwrapper24px"><img class="clipsrc _page" src="', $settings['images_url'], '/', $context['theme_variant_url'], 'clipsrc.png" alt="" /></div></td><td class="nowrap smalltext" style="padding-left:28px;">', $txt['a_page'], '</td>
		</tr>
		</table>
	</div>';
	}

	template_info_center();
	echo $context['template_hooks']['boardindex']['below_boardlisting'];
}

function template_info_center()
{
	global $context, $settings, $txt, $scripturl, $modSettings;

	// "Users online" - in order of activity.
	echo '<div class="clear_left"></div>';
	if(isset($context['show_who'])) {
		echo '
				<div class="cat_bar2">
					<h3 class="lefttext">
						', $context['show_who'] ? '<a href="' . URL::action($scripturl . '?action=who') . '">' : '', $txt['online_users'], $context['show_who'] ? '</a>' : '', '
					</h3>
				</div>
				<div class="blue_container smallpadding smalltext cleantop">',
					sprintf($txt['who_summary'], $context['num_guests'], $context['num_users_online'], $modSettings['lastActive']);

		// Handle hidden users and buddies.
		$bracketList = array();
		if ($context['show_buddies'])
			$bracketList[] = comma_format($context['num_buddies']) . ' ' . ($context['num_buddies'] == 1 ? $txt['buddy'] : $txt['buddies']);
		if (!empty($context['num_spiders']))
			$bracketList[] = comma_format($context['num_spiders']) . ' ' . ($context['num_spiders'] == 1 ? $txt['spider'] : $txt['spiders']);
		if (!empty($context['num_users_hidden']))
			$bracketList[] = comma_format($context['num_users_hidden']) . ' ' . $txt['hidden'];

		if (!empty($bracketList))
			echo ' (' . implode(', ', $bracketList) . ')';

		echo ($context['show_who'] ? '<br>'.$txt['who_showby'].'<a href="'.URL::action($scripturl.'?action=who;show=all;sort=user').'">'.$txt['username'].'</a> | <a href="'.URL::action($scripturl.'?action=who;show=all;sort=time').'">'.$txt['who_lastact'].'</a>' : '');
		echo '
			<p class="inline smalltext">';

		// Assuming there ARE users online... each user in users_online has an id, username, name, group, href, and link.
		if (!empty($context['users_online']))
		{
			echo '
					', sprintf($txt['users_active'], $modSettings['lastActive']), ':<br />', implode(', ', $context['list_users_online']);

			// Showing membergroups?
			if (!empty($settings['show_group_key']) && !empty($context['membergroups']))
				echo '
					<br />[' . implode(']&nbsp;&nbsp;[', $context['membergroups']) . ']';
		}

		echo '
				</p>
				<div class="last smalltext">
					', $txt['most_online_today'], ': <strong>', comma_format($modSettings['mostOnlineToday']), '</strong>.
					', $txt['most_online_ever'], ': ', comma_format($modSettings['mostOnline']), ' (', timeformat($modSettings['mostDate']), ')
				</div></div>';
	}
}
?>