<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines
 * @copyright 2011 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0
 */

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	// Show the news fader?  (assuming there are things to show...)
	if ($settings['show_newsfader'] && !empty($context['fader_news_lines']))
	{
		echo '
	<div id="newsfader">
		<div class="cat_bar rounded_top">
			<h3 class="catbg">
				<img id="newsupshrink" src="', $settings['images_url'], '/collapse.gif" alt="*" title="', $txt['upshrink_description'], '" style="display: none;" />
				', $txt['news'], '
			</h3>
		</div>
		<ul class="windowbg rounded_bottom reset" id="smfFadeScroller"', empty($options['collapse_news_fader']) ? '' : ' style="display: none;"', '>';

			foreach ($context['news_lines'] as $news)
				echo '
			<li>', $news, '</li>';

	echo '
		</ul>
	</div>
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/fader.js"></script>
	<script type="text/javascript"><!-- // --><![CDATA[

		// Create a news fader object.
		var oNewsFader = new smf_NewsFader({
			sSelf: \'oNewsFader\',
			sFaderControlId: \'smfFadeScroller\',
			sItemTemplate: ', JavaScriptEscape('<strong>%1$s</strong>'), ',
			iFadeDelay: ', empty($settings['newsfader_time']) ? 5000 : $settings['newsfader_time'], '
		});

		// Create the news fader toggle.
		var smfNewsFadeToggle = new smc_Toggle({
			bToggleEnabled: true,
			bCurrentlyCollapsed: ', empty($options['collapse_news_fader']) ? 'false' : 'true', ',
			aSwappableContainers: [
				\'smfFadeScroller\'
			],
			aSwapImages: [
				{
					sId: \'newsupshrink\',
					srcExpanded: smf_images_url + \'/collapse.gif\',
					altExpanded: ', JavaScriptEscape($txt['upshrink_description']), ',
					srcCollapsed: smf_images_url + \'/expand.gif\',
					altCollapsed: ', JavaScriptEscape($txt['upshrink_description']), '
				}
			],
			oThemeOptions: {
				bUseThemeSettings: ', $context['user']['is_guest'] ? 'false' : 'true', ',
				sOptionName: \'collapse_news_fader\',
				sSessionVar: ', JavaScriptEscape($context['session_var']), ',
				sSessionId: ', JavaScriptEscape($context['session_id']), '
			},
			oCookieOptions: {
				bUseCookie: ', $context['user']['is_guest'] ? 'true' : 'false', ',
				sCookieName: \'newsupshrink\'
			}
		});
	// ]]></script>';
	}

	echo '
	<div id="boardindex_table">';

	/* Each category in categories is made up of:
	id, href, link, name, is_collapsed (is it collapsed?), can_collapse (is it okay if it is?),
	new (is it new?), collapse_href (href to collapse/expand), collapse_image (up/down image),
	and boards. (see below.) */
	foreach ($context['categories'] as $category)
	{
		// If theres no parent boards we can see, avoid showing an empty category (unless its collapsed)
		if (empty($category['boards']) && !$category['is_collapsed'])
			continue;

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
				<h2>
				', $category['link'], '
			  	</h2>
		      </div>
			</div>';

		// Assuming the category hasn't been collapsed...
		//if (!$category['is_collapsed'])
		//{
		echo '
			<ol class="commonlist category" ',$category['is_collapsed'] ? 'style="display:none;" ' : '', ' id="category_', $category['id'], '_boards">
			<li class="brow glass cleantop">
				<div class="floatright centertext lastpost">',$txt['last_post'],'</div>
				<div class="floatright centertext stats">',$txt['content_label'],'</div>
				<div class="centertext">',$txt['board'],'</div>
			</li>
			';
			/* Each board in each category's boards has:
			new (is it new?), id, name, description, moderators (see below), link_moderators (just a list.),
			children (see below.), link_children (easier to use.), children_new (are they new?),
			topics (# of), posts (# of), link, href, and last_post. (see below.) */
			$alternate = 1;
			foreach ($category['boards'] as $board)
				template_boardbit($board);
		echo '
			</ol>
			<div class="cContainer_end"></div>';
		//}
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
			<td><div class="csrcwrapper24px"><img class="clipsrc _on" src="', $settings['images_url'], '/', $context['theme_variant_url'], 'clipsrc.png" alt="" /></div></td><td class="nowrap smalltext" style="padding-left:28px;">', $txt['new_posts'], '</td>
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
}

function template_info_center()
{
	global $context, $settings, $txt, $scripturl, $modSettings;

	// "Users online" - in order of activity.
	echo '<div class="clear"></div>';
	if(isset($context['show_who'])) {
		echo '
				<div class="cat_bar2">
					<h3>
						', $context['show_who'] ? '<a href="' . $scripturl . '?action=who' . '">' : '', $txt['online_users'], $context['show_who'] ? '</a>' : '', '
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

		echo ($context['show_who'] ? '<br>'.$txt['who_showby'].'<a href="'.$scripturl.'?action=who;show=all;sort=user">'.$txt['username'].'</a> | <a href="'.$scripturl.'?action=who;show=all;sort=time">'.$txt['who_lastact'].'</a>' : '');
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