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
function template_init()
{
	global $context, $settings;

	/* Use images from default theme when using templates from the default theme?
		if this is 'always', images from the default theme will be used.
		if this is 'defaults', images from the default theme will only be used with default templates.
		if this is 'never' or isn't set at all, images from the default theme will not be used. */
	$settings['use_default_images'] = 'never';

	/*This should probably be the version of SMF it was created for. */
	$settings['theme_version'] = '2.0';
}

// The main sub template above the content.
function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	$h = 'HDC';
	// Show right to left and the character set for ease of translating.
	echo '
<!DOCTYPE html ', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<html id="_S_" lang="en-US">
<head>';
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['primary_css'],'" />';
	if ($context['right_to_left'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/rtl.css" />';

	// Here comes the JavaScript bits!
	if(!empty($modSettings['jQueryFromGoogleCDN']))
		echo '
	<script type="text/javascript" src="', ($context['is_https'] ? 'https://' : 'http://'), 'ajax.googleapis.com/ajax/libs/jquery/',$context['jquery_version'],'/jquery.min.js"></script>';
	else
		echo '
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/min/jquery.js?v=162"></script>';
	echo '
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js',$context['jsver'],'"></script>';
	if(!empty($context['theme_scripts'])) {
		foreach($context['theme_scripts'] as $type => $script) {
			if(!$script['footer'])
				echo '
	<script type="text/javascript" src="',($script['default'] ? $settings['default_theme_url'] : $settings['theme_url']) . '/' . $script['name'] . $context['jsver'], '"></script>
	';
		}
	}
	echo <<<EOT

	<script type="text/javascript">
	// <![CDATA[
	var smf_theme_url = '{$settings['theme_url']}';
	var smf_default_theme_url = '{$settings['default_theme_url']}';
	var smf_images_url = '{$settings['images_url']}';
	var smf_scripturl = '{$scripturl}';
	var smf_iso_case_folding = {$h($context['server']['iso_case_folding'], 'true', 'false')};
	var smf_charset = 'UTF-8';
	var sSessionId = '{$context['session_id']}';
	var sSessionVar = '{$context['session_var']}';
	var disableDynamicTime = {$h(empty($options['disable_dynatime']), 0, 1)};
	var textSizeUnit = 'pt';
	var textSizeStep = 1;
	var textSizeMax = 16;
	var textSizeMin = 8;
	var textSizeDefault = 10;
	var sideBarWidth = 250;
	var sidebar_content_loaded = 0;
	var cookie = readCookie('SMF_textsize');
	var fb_appid = '{$modSettings['fb_appid']}';
	var ssp_imgpath = '{$settings['images_url']}/share';
	var textsize = cookie ? parseInt(cookie) : textSizeDefault;
	var anchor = document.getElementsByTagName('SCRIPT')[0];
	var t2 = document.createElement('SCRIPT');
	t2.type = "text/javascript";
	t2.async = true;
	t2.src = '{$settings['default_theme_url']}/scripts/footer.js{$context['jsver']}';
	anchor.parentNode.insertBefore(t2, anchor);
	// ]]>
	</script>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="{$context['page_description_html_safe']}" />
	{$h(!empty($context['meta_keywords']), '<meta name="keywords" content="' . $context['meta_keywords'] . '" />', '')}
	<title>{$context['page_title_html_safe']}</title>
EOT;
	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex" />';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '" data-href="',(isset($context['share_url']) ? $context['share_url'] : ''), '" />';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="search" href="', $scripturl, '?action=search" />';

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?type=rss;action=.xml" />';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (isset($context['current_topic']))
		echo '
	<link rel="prev" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=prev" />
	<link rel="next" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=next" />';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0" />';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];
	echo '
	<style>
	 #main_content_section {max-width:',isset($options['content_width']) ? $options['content_width'] : '95%', ';}';
	if(isset($context['css_overrides'])) {
		foreach($context['css_overrides'] as $k)
			echo '
	',$k,'
	';
	}
	echo '
	</style>
</head>
<body>';
}

function template_body_above()
{
	global $context, $settings, $scripturl, $txt, $user_info, $modSettings;

	$alerts = $user_info['notify_count'] > 0 ? $user_info['notify_count'] : '';
	$scope = 0;
	$search_label = $txt['search_all_boards'];
	$astream_link = '<a rel="nofollow" data-board="all" href="'.$scripturl . '?action=astream;sa=get;all">Recent activity</a>';

	if (isset($context['current_topic']) && $context['current_topic']) {
		$search_label = $txt['search_topic'];
		$scope = 2;
	}
	// If we're on a certain board, limit it to this board ;).
	elseif (isset($context['current_board'])) {
		$search_label = $txt['search_board'];
		$scope = 1;
		$astream_link = '<a data-board="'.$context['current_board'].'" href="'.$scripturl . '?action=astream;sa=get;b=' . $context['current_board']. '">Recent activity</a>';
	}
	echo '
	<div id="__t_script" style="display:none;"></div>
	<div id="jsconfirm" style="width:450px;" class="jqmWindow"><div class="jqmWindow_container"><div class="glass jsconfirm title"></div><div class="jsconfirm content"></div><div class="floatright mediummargin"><input type="submit" id="c_yes" value="Yes" class="button_submit" /><input type="reset" id="c_no" value="No" class="button_reset" /><input type="submit" id="c_ok" value="Ok" class="button_submit" /></div><div class="clear"></div></div></div>
	<div id="ajaxbusy" style="display:none;"><img src="',$settings['images_url'],'/ajax-loader.gif" alt="loader" /></div>
	<div id="mcard" style="display:none;"><div onclick="mcardClose();" id="mcard_close">X</div><div id="mcard_inner"></div></div>
	<div id="wrap">
	<header>
	<div id="header">
	<div id="upper_section" class="smalltext">
		<div class="floatleft" style="overflow:hidden;max-height:87px;"><img src="',$settings['images_url'],'/logo.png" alt="logo" /></div>
		<div class="clear"></div>
	</div>
		<div class="notibar">
			<div class="notibar right">
			<div class="floatright">
			 <span id="curfontsize"></span>
			</div>
			<div class="floatright" style="position:relative;">';
	if($modSettings['astream_active']) {
		echo '
			<span onclick="getAStream($(this));return(false);" class="button notify">',$astream_link,'</span>';
		if(!$context['user']['is_guest'])
			echo '
			<span id="notification_anchor" onclick="getNotifications($(this));return(false);" class="button notify"><a>Your notifications</a></span><span style="',($alerts > 0 ? '':'display:none;'),'position:relative;top:-14px;right:14px;" id="alerts">',$alerts,'</span><div id="notification_target" style="display:inline;position:relative;"></div>';
	}
	echo '
			</div>
			</div>
			<div class="notibar_intro"></div>
		</div>
	<nav>';
	// Show the menu here, according to the menu sub template.
	template_menu();

	echo '</nav>
	<script>
		// <![CDATA[
    	setTextSize(textsize);
		// ]]>
	</script>
	</div></header>';
	// The main content should go here.
	echo '
	<div id="content_section">
	<div id="main_content_section">';
	// Custom banners and shoutboxes should be placed here, before the linktree.

	theme_linktree();
	if(isset($context['need_pager_script_fragment'])) {
		$pager_entry_script = <<<EOT
	jQuery(document).ready(function() {
		$('.pagelinks .prefix').click(function() {
			if($('#directpager').length <= 0) {
				$(this).attr('data-save', $(this).html());
				$(this).html('<form action="' + $(this).attr('data-urltemplate') + '" id="directpager" method="post">{$txt["page_go_to"]}<input name="directpager_pagenr" id="directpager_pagenr" size=3 /></form>');
				$('#directpager_pagenr').focus();
			}
			$('#directpager').submit(function() {

				var newstart = (parseInt($('#directpager_pagenr').val()) - 1) * parseInt($(this).parent().attr('data-perpage'));
				if(newstart < 0)
					newstart = 0;
				$(this).attr('action', $(this).attr('action').replace(/\[\[PAGE\]\]/g, newstart));
				$(this).submit();
				return(false);
			});
		});

		$('.pagelinks .prefix').live('mouseleave',function(event) {
			$(this).html($(this).attr('data-save'));
		});
		return;
	});
EOT;
		registerFooterScriptFragment('pager_entry', $pager_entry_script);

	}
	$sidebar_allowed = isset($context['is_board_index']);			// todo: make this more flexible and define a set of pages where the sidebar can show up
	$sidebar_vis = (isset($_COOKIE['smf_sidebar_disabled']) && $_COOKIE['smf_sidebar_disabled'] == 1) ? false : true;
	// Show the navigation tree.
	echo $context['template_hooks']['global']['above'];

	if($context['news_item_count'] && isset($context['is_board_index'])) {
		//$collapser = array('id' => 'news_boardindex', 'title' => 'NEWS', 'bodyclass' => 'blue_container');
		//template_create_collapsible_container($collapser);
		echo '
		<div class="blue_container">
		<div class="content smallpadding inset_shadow">
		<ol class="commonlist noshadow news">';
		template_news_listitems();
		echo '
		</ol>
		</div>
		</div>
		<div class="cContainer_end"></div>';
	}
	echo '<aside>
		  <div id="sidebar" style="width:260px;display:',$sidebar_allowed ? 'inline' : 'none',';">';
	if($sidebar_allowed)
		template_sidebar_content();
	echo '</div>
		  </aside>
	      <div id="container" style="margin-right:',$sidebar_allowed ? '270px' : '0',';">
		  <script>
  		  // <![CDATA[
  		  		$("#sidebar").css("display", ',$sidebar_vis && $sidebar_allowed ? '"inline"' : '"none"', ');
  		  		$("#container").css("margin-right", ',$sidebar_vis && $sidebar_allowed ? 'sideBarWidth + 20 + "px"' : "0", ');
		  // ]]>
	      </script>';
}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings, $fbxml, $twitter_widgets, $plusone;
	echo '<div class="clear"></div>
		</div></div></div>';

	// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
	echo '
  	<script type="text/javascript">
	// <![CDATA[
	';

	if(isset($context['need_synhlt']))  // include syntax highlighter js when needed.
		echo '
	var t3 = document.createElement(\'SCRIPT\');
	t3.type = "text/javascript";
	t3.async = true;
	t3.src = "',$settings['theme_url'],'/scripts/shlt.js?ver=1.1.0";
	anchor.parentNode.insertBefore(t3, anchor);';

	$context['inline_footer_script'] .= $txt['jquery_timeago_loc'];
	if(isset($modSettings['embed_GA']) && $modSettings['embed_GA'] && ($context['user']['is_guest'] || (empty($options['disable_analytics']) ? 1 : !$options['disable_analytics'])))	{
		echo '
	var _gaq = _gaq || [];
	_gaq.push([\'_setAccount\', \'',$modSettings['GA_tracker_id'], '\']);
	_gaq.push([\'_setDomainName\', \'',$modSettings['GA_domain_name'],'\']);
   	_gaq.push([\'_trackPageview\']);

	var ga = document.createElement(\'script\');
	var sa = document.getElementsByTagName(\'script\')[0];
	ga.async = true;
	ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
	sa.parentNode.insertBefore(ga, sa);
	';
	}
	echo '
	// ]]>
	</script>
	',$context['template_hooks']['global']['footer'],'
	<footer>
	<div class="clear" id="footer_section">';

	// Show the load time?
	if ($context['show_load_time'])
		$loadtime = $context['load_time']. 's CPU, '.$context['load_queries'] . $txt['queries'];

	$time_now = forum_time(false);
	$tz = date_default_timezone_get();
	echo '
	<div class="righttext floatright">',$loadtime,'<br>Forum time: ',strftime($modSettings['time_format'], $time_now) . ' '. $tz,'</div>
	<div class="copyright">', my_theme_copyright(), '</div>
	<div><a id="button_xhtml" href="http://validator.w3.org/check?uri=referer" target="_blank" class="new_win" title="Valid HTML"><span>HTML</span></a> |
	', !empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']) ? '<a id="button_rss" href="' . $scripturl . '?action=.xml;type=rss" class="new_win"><span>' . $txt['rss'] . '</span></a>' : '';
	if($context['mobile'])
		echo '
	| <a href="',$scripturl,'?mobile=0" >Full version</a>';
	else
		echo '
	| <a href="',$scripturl,'?mobile=1" >Mobile</a>';
	echo '
	</div>
	</div>
	</footer>';
	if(1) { // piwik, todo: make configurable in admin area!
		echo <<<EOT
	<script>
	var pkBaseURL = (("https:" == document.location.protocol) ? "https://piwik.miranda.or.at/" : "http://piwik.miranda.or.at/");
	document.write(unescape("%3Cscript src=\'" + pkBaseURL + "piwik.js\' type=\'text/javascript\'%3E%3C/script%3E"));
	</script>
	<script>
	try {
		var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 1);
		piwikTracker.trackPageView();
		piwikTracker.enableLinkTracking();
	}
	catch( err ) {
	}
	</script>
	<noscript>
	  <div style="width:0px;height:0px;"><img src="http://piwik.miranda.or.at/piwik.php?idsite=1" style="border:0" alt="" /></div>
	</noscript>
EOT;
	}
}

function template_html_below()
{
	echo '
	</div>';
	template_footer_scripts();
	echo '
	</body>
	</html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree($force_show = false)
{
	global $context, $shown_linktree;
	static $ltree = '';

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;

	if(!empty($ltree)) {
		$ltree = str_ireplace('linktree_upper', 'linktree_lower', $ltree);
		echo $ltree;
		return;
	}
	$ltree = '<div class="navigate_section"><ul class="linktree" id="linktree_'. (empty($shown_linktree) ? 'upper' : 'lower'). '">';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	$tree_items = count($context['linktree']);
	foreach ($context['linktree'] as $link_num => $tree)
	{
		$ltree .= ('
		<li'. (($link_num == $tree_items - 1) ? ' class="last"' : ''). '>');

		// Show something before the link?
		if (isset($tree['extra_before']))
			$ltree .= $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		$ltree .= (isset($tree['url']) ? ('
			<a itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>') : ('<span>') . $tree['name'] . '</span>');

		// Show something after the link...?
		if (isset($tree['extra_after']))
			$ltree .= $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != $tree_items - 1)
			$ltree .= ' &rarr;';

		$ltree .= '
		</li>';
	}
	$ltree .= '
	</ul></div>';

	echo($ltree);
	$shown_linktree = true;
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context;

	$context['menu_buttons']['blog']['title'] = "Blog";
	$context['menu_buttons']['blog']['show'] = true;
	$context['menu_buttons']['blog']['href'] = "http://blog.miranda.or.at";

	echo '
		<div id="main_menu">
			<ul class="dropmenu" id="menu_nav">';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		$has_subitems = !empty($button['sub_buttons']);
		if(!isset($button['active_button']))
			$button['active_button'] = false;
		echo '
				<li class="', $button['active_button'] ? 'active' : '', '" id="button_', $act, '">
					<a class="firstlevel" href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
						<span class="', isset($button['is_last']) ? 'last ' : '', 'firstlevel">', $button['title'];
		echo '</span>';
		echo '</a>';
		if($has_subitems)
			echo '<span onclick="onMenuArrowClick($(this));" style="display:inline-block;" id="_',$act,'" class="m_downarrow">&nbsp;</span>';
		if ($has_subitems)
		{
			echo '
					<ul>';

			foreach ($button['sub_buttons'] as $childbutton)
			{
				echo '
						<li>
							<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', '>
								<span', isset($childbutton['is_last']) ? ' class="last"' : '', '>', $childbutton['title'], !empty($childbutton['sub_buttons']) ? '...' : '', '</span>
							</a>';
				// 3rd level menus :)
				if (!empty($childbutton['sub_buttons']))
				{
					echo '
							<ul>';

					foreach ($childbutton['sub_buttons'] as $grandchildbutton)
						echo '
								<li>
									<a href="', $grandchildbutton['href'], '"', isset($grandchildbutton['target']) ? ' target="' . $grandchildbutton['target'] . '"' : '', '>
										<span', isset($grandchildbutton['is_last']) ? ' class="last"' : '', '>', $grandchildbutton['title'], '</span>
									</a>
								</li>';

					echo '
							</ul>';
				}

				echo '
						</li>';
			}
			echo '
					</ul>';
		}
		echo '
				</li>';
	}

	echo '
			</ul><div class="clear"></div>
		</div>';
}

// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $strip_options = array())
{
	global $context, $txt;

	if (!is_array($strip_options))
		$strip_options = array();

	// List the buttons in reverse order for RTL languages.
	if ($context['right_to_left'])
		$button_strip = array_reverse($button_strip, true);

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
			$buttons[] = '
				<li><a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_strip_' . $key . (isset($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a></li>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

	if(!isset($strip_options['class']))
		$strip_options['class'] = 'buttonlist';

	echo '
		<div class="',$strip_options['class'], !empty($direction) ? ' float' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul class="',$strip_options['class'],'">',
	implode('', $buttons), '
			</ul>
		</div>';
}
function my_theme_copyright($get_it = false)
{
	global $forum_copyright, $forum_version;

	if (!isset($forum_version))
		return;

	$forum_copyright = sprintf($forum_copyright, $forum_version);
	echo '
	<span>' . $forum_copyright . '</span>';
}

function socialbar($l, $t)
{
	socialbar_passive($l, $t);
	return;
}

function socialbar_passive($l, $t)
{
	global $plusone;

	echo '<div class="bmbar"><div class="title">Share this topic: </div>';
	$url = $l;
	$plusone++;

	//echo '<div class="floatleft"><a role="button" rel="nofollow" class="share_button share_fb" href="http://www.facebook.com/sharer.php?u=',$url,'">Share</a>
	//<a role="button" rel="nofollow" class="share_button share_tw" href="http://twitter.com/share?text=',$t,'&amp;url=',$url,'">Tweet</a>
	//<a role="button" rel="nofollow" class="share_button share_digg" href="http://digg.com/submit?phase=2&amp;title=',$t,'&amp;url=',$url,'">Digg</a>
	//<a role="button" rel="nofollow" class="share_button share_buzz" href="http://www.google.com/buzz/post?url=',$url,'">Buzz</a></div>&nbsp;&nbsp;
	//<div class="floatright" style="max-width:65px;overflow:hidden;"><div class="g-plusone" data-href="',$url,'" data-size="medium" data-count="true"></div></div>
	//<div class="clear"></div>';
	echo '<div id="socialshareprivacy"></div><div class="clear"></div>';
	echo '</div><div class="clear"></div>';
}

function template_sidebar_content()
{
	global $context, $txt, $modSettings, $scripturl, $settings, $user_info, $options;

	$widgetstyle = 'framed_region cleantop tinypadding';
	echo $context['template_hooks']['global']['sidebar_top'];
	$collapser = array('id' => 'user_panel', 'title' => 'User panel', 'bodyclass' => $widgetstyle);
	echo '<script>
		   // <![CDATA[
		   sidebar_content_loaded = 1;
           // ]]>
		  </script>';

	template_create_collapsible_container($collapser);
	//<h1 class="bigheader greyback" style="margin-top:0;">User panel</h1>';

	// If the user is logged in, display stuff like their name, new messages, etc.
	// for the logo -> <img style="margin-left:30px;margin-top:10px;float:left;display:inline-block;" src="'.$settings['images_url'].'/bloglogo.png" alt="logo" />
	echo '
		<div class="blue_container inset_shadow smallpadding">';
	if ($context['user']['is_logged'])
	{
		echo '<div class="smalltext user">';

		if (!empty($context['user']['avatar']))
			echo '
				<div class="avatar floatleft">', $context['user']['avatar']['image'], '</div>';
		else
			echo '
				<div class="avatar floatleft"><img src="',$settings['images_url'],'/unknown.png" alt="avatar" /></div>';
		echo '
				 <ul class="reset" style="line-height:110%;">
					<li class="greeting"><a href="',$scripturl,'?action=profile;u=',$context['user']['id'],'">', $context['user']['name'], '</a></li>
					<li class="smalltext">',$user_info['posts'],' ',$txt['posts'],'<li>
					<li class="smalltext">',$user_info['likesreceived'],' ',$txt['likes'],'<li>
					<li class="smalltext"><span class="smalltext floatright"><a href="',$scripturl,'?action=logout;',$context['session_var'],'=',$context['session_id'], '">Sign out</a></span><li>
				 </ul>
				 <div class="clear">
					<a href="', $scripturl, '?action=unread">', $txt['unread_since_visit'], '</a><br>
					<a href="', $scripturl, '?action=unreadreplies">', $txt['show_unread_replies'], '</a>
				 </div>';

		echo '<div style="margin-top:3px;">';
		// Is the forum in maintenance mode?
		if ($context['in_maintenance'] && $context['user']['is_admin'])
			echo '
					<div class="errorbox smallpadding">', $txt['maintain_mode_on'], '</div>';

		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '
					<div>', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '</div>';

		if (!empty($context['open_mod_reports']) && $context['show_open_reports'])
			echo '
					<div><a href="', $scripturl, '?action=moderate;area=reports">', sprintf($txt['mod_reports_waiting'], $context['open_mod_reports']), '</a></div>';

		echo '</div></div></div>';
	}
	// Otherwise they're a guest - this time ask them to either register or login - lazy bums...
	else {
		echo '
				<div class="smalltext">
				<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/min/sha1.js',$context['jsver'],'"></script>
				<div>
					<form id="guest_form" action="', $scripturl, '?action=login2" method="post" accept-charset="UTF-8" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
					<div class="orange_container centertext">', sprintf($txt['welcome_guest'], $txt['guest_title']), '</div>
					<table>
					<tr>
					<td class="nowrap"><strong>',$txt['username'],':</strong></td>
					<td><input type="text" name="user" size="20" class="input_text" /></td>
					</tr>
					<tr>
					<td class="nowrap"><strong>',$txt['password'],':</strong></td>
					<td><input type="password" name="passwrd" size="20" class="input_password" /></td>
					</tr>
					</table>
					<span style="line-height:20px;">',$txt['always_logged_in'],'<input type="checkbox" name="cookielength" value="-1"></span>
					<input style="width:90%;margin-left:5%;margin-top:10px;" type="submit" value="', $txt['login'], '" class="button_submit" /><br />';

		if (!empty($modSettings['enableOpenID']))
			echo '
					<br /><input type="text" name="openid_identifier" id="openid_url" size="25" class="input_text openid_login" />';

		echo '
					<input type="hidden" name="hash_passwrd" value="" />
					</form>
					<br>';
		if(!(!empty($modSettings['registration_method']) && $modSettings['registration_method'] == 3))
			echo '
					<div class="orange_container">
					',$txt['login_or_register'],'
					</div>';
		else
			echo '
					<div class="orange_container">
					',$txt['registration_disabled'],'
					</div>';
		echo '
					</div>
				</div>
			</div>';
	}
	echo '</div>
		<div class="cContainer_end"></div>';

	// Show statistical style information...
	if ($settings['show_stats_index'] && isset($context['show_stats']))
	{
		$collapser = array('bodyclass' => $widgetstyle, 'id'=> 'stats_panel','title' => $txt['forum_stats']);
		template_create_collapsible_container($collapser);
		echo '
			<div class="blue_container inset_shadow smallpadding">
			<div class="smallpadding smalltext">
				<dl class="common">
				 <dt>', $txt['posts'], ': </dt><dd class="righttext">',$context['common_stats']['total_posts'], '</dd>
				 <dt>', $txt['topics'], ': </dt><dd class="righttext">', $context['common_stats']['total_topics'], '</dd>
				 <dt>', $txt['members'], ': </dt><dd class="righttext">', $context['common_stats']['total_members'], '</dd>';
		if(!empty($settings['show_latest_member']))
			echo '<dt>', $txt['latest_member'] . ': </dt><dd class="righttext"><strong>', $context['common_stats']['latest_member']['link'] . '</strong></dd>';
		echo '</dl>';
		echo '
				<div>
				  <div class="floatright righttext"><a href="', URL::action($scripturl . '?action=recent') . '">', $txt['recent_view'], '</a>', $context['show_stats'] ? '
				  </div>
				 <a href="' . URL::action($scripturl . '?action=stats') .'">' . $txt['more_stats'] . '</a>' : '', '
				</div>
			</div>
			</div>
			</div>
			<div class="cContainer_end"></div>';
	}

	// social panel in the side bar
	if(($context['user']['is_guest'] || (empty($options['use_share_bar']) ? 1 : !$options['use_share_bar']))) {
		$collapser = array('id' => 'social_panel', 'title' => 'Socialize', 'bodyclass' => $widgetstyle, 'framed' => 'smallpadding');
		template_create_collapsible_container($collapser);
		echo '
		<div class="blue_container inset_shadow smallpadding">
		<div id="socialshareprivacy"></div>
		<div class="clear"></div>
		</div>
		</div>
		<div class="cContainer_end"></div>';
	}

	// This is the "Recent Posts" bar.
	if (!empty($settings['number_recent_posts']) && (!empty($context['latest_posts']) || !empty($context['latest_post'])))
	{
		$collapser = array('bodyclass' => $widgetstyle, 'id' => 'recent_panel', 'title' => '<a href="'. $scripturl. '?action=recent">'. $txt['recent_posts']. '</a>', 'framed' => 'smallpadding');
		template_create_collapsible_container($collapser);
		echo '
			<div class="blue_container inset_shadow smallpadding">
			<div class="smalltext" id="recent_posts_content" style="line-height:120%;">
				<div class="entry-title" style="display: none;">', $context['forum_name_html_safe'], ' - ', $txt['recent_posts'], '</div>
				<div class="entry-content" style="display: none;">
					<a rel="alternate" type="application/rss+xml" href="', $scripturl, '?action=.xml;type=webslice">', $txt['subscribe_webslice'], '</a>
				</div>';

		// Only show one post.
		if ($settings['number_recent_posts'] == 1)
		{
			// latest_post has link, href, time, subject, short_subject (shortened with...), and topic. (its id.)
			echo '
				<strong><a href="', $scripturl, '?action=recent">', $txt['recent_posts'], '</a></strong>
				<p id="infocenter_onepost" class="smalltext">
					', $txt['recent_view'], ' &quot;', $context['latest_post']['link'], '&quot; ', $txt['recent_updated'], ' (', $context['latest_post']['time'], ')<br />
				</p>';
		}
		// Show lots of posts.
		elseif (!empty($context['latest_posts']))
		{
			echo '
			   	<ol class="commonlist smalltext" style="padding:0;margin:0;">';
			/* Each post in latest_posts has:
					board (with an id, name, and link.), topic (the topic's id.), poster (with id, name, and link.),
					subject, short_subject (shortened with...), time, link, and href. */
			foreach ($context['latest_posts'] as $post)
				echo '
				<li class="smallpadding">
					<a href = "',$post['href'],'" title = "',$post['subject'],'">',$post['short_subject'],'</a><br>
					<span class="nowrap floatright tinytext">', $post['time'], '</span><strong class="tinytext">', $post['poster']['link'],'</strong><br>
				</li>';
			echo '
				</ol>';
		}
		echo '
			</div>
			</div>
			</div>
			<div class="cContainer_end"></div>
			';
	}

	// Show information about events, birthdays, and holidays on the calendar.
	if ($context['show_calendar'])
	{
		$title = $context['calendar_only_today'] ? $txt['calendar_today'] : ($txt['calendar']. ' (Next '.$modSettings['cal_days_for_index'].' days)');
		$collapser = array('bodyclass'=> $widgetstyle, 'id' => 'cal_panel', 'title' => '<a href="'. URL::action($scripturl . '?action=calendar') . '">'. $title . '</a>', 'framed' => 'smallpadding');
		template_create_collapsible_container($collapser);
		echo '
			<div class="blue_container inset_shadow smallpadding">
			<div class="smalltext">';

		// Holidays like "Christmas", "Chanukah", and "We Love [Unknown] Day" :P.
		if (!empty($context['calendar_holidays']))
			echo '
				<div class="holiday">', $txt['calendar_prompt'], '</div>', implode(', ', $context['calendar_holidays']), '<br><div class="cContainer_end"></div>';

		// People's birthdays. Like mine. And yours, I guess. Kidding.
		if (!empty($context['calendar_birthdays']))	{
			echo '
				<div class="birthday">', $context['calendar_only_today'] ? $txt['birthdays'] : $txt['birthdays_upcoming'], '</div> ';
			/* Each member in calendar_birthdays has:
				   id, name (person), age (if they have one set?), is_last. (last in list?), and is_today (birthday is today?) */
			foreach ($context['calendar_birthdays'] as $member)
				echo '
				<a href="', URL::user($member['id'], $member['name']), '">', $member['is_today'] ? '<strong>' : '', $member['name'], $member['is_today'] ? '</strong>' : '', isset($member['age']) ? ' (' . $member['age'] . ')' : '', '</a>', $member['is_last'] ? '<br />' : ', ';
		}
		// Events like community get-togethers.
		if (!empty($context['calendar_events']))
		{
			echo '
				<span class="event">', $context['calendar_only_today'] ? $txt['events'] : $txt['events_upcoming'], '</span> ';
			/* Each event in calendar_events should have:
					title, href, is_last, can_edit (are they allowed?), modify_href, and is_today. */
			foreach ($context['calendar_events'] as $event)
				echo '
					', $event['can_edit'] ? '<a href="' . $event['modify_href'] . '" title="' . $txt['calendar_edit'] . '"><img src="' . $settings['images_url'] . '/icons/modify_small.gif" alt="*" /></a> ' : '', $event['href'] == '' ? '' : '<a href="' . $event['href'] . '">', $event['is_today'] ? '<strong>' . $event['title'] . '</strong>' : $event['title'], $event['href'] == '' ? '' : '</a>', $event['is_last'] ? '<br />' : ', ';
		}
		echo '
			</div>
			</div>
			</div>
			<div class="cContainer_end"></div>
			';
	}
	echo $context['template_hooks']['global']['sidebar_bottom'];
}

/*
 * create a collapsible container with an id, a title and html content
 * caller is responsible to provide the final </div> unless you pass a box
 * content in $_c['content'].
 *
 * you can optionally pass css classes for the the header bar and the body
 * object. By default, the cContainer_* classes define the style.
 * gracefully degrades for people without JavaScript - always expanded.
 * id MUST be globally unique for the page
 * relies on jQuery
 */
function template_create_collapsible_container(array &$_c)
{
	global $settings;

	$id = $_c['id']; 		// just bein' lazy :)
	// one cookie to rule them all (it stores all collapsed ids, separated by ',')
	// duplicate ids will break this, so be careful
	$state = isset($_COOKIE['SF_collapsed']) ? array_search($id, explode(',', $_COOKIE['SF_collapsed'])) : false;

	if(!isset($_c['headerclass']))
		$_c['headerclass'] = 'cContainer_header';
	if(!isset($_c['headerstyle']))
		$_c['headerstyle'] = '';
	else
		$_c['headerstyle'] = ' style="'.$_c['headerstyle'].'"';
	echo '
		<div class="',$_c['headerclass'],'"',$_c['headerstyle'],'>
		<div class="csrcwrapper16px floatright"><img onclick="cContainer($(this));" class="cContainer_c clipsrc ',($state ? '_expand' : '_collapse'),'" id="',$id,'" src="',$settings['images_url'].'/clipsrc.png" alt="*" /></div>';
	echo '<h3>',$_c['title'],'</h3>
		</div>';

	if(!isset($_c['bodyclass']))
		$_c['bodyclass'] = 'cContainer_body';
	if(!isset($_c['bodystyle']))
		$_c['bodystyle'] = '';
	else
		$_c['bodystyle'] = ' style="'.$_c['bodystyle'].'"';

	echo '
		<div id="',$id,'_body" class="',$_c['bodyclass'],'"',$_c['bodystyle'],'>
		<script>
		// <![CDATA[
			$("#',$id,'_body").css("display", "',$state ? 'none' : 'normal','");
		// ]]>
		</script>';
	if(isset($_c['content']))
		echo $_c['content'],'
		</div>
		<div class="cContainer_end"></div>
		';
}

function template_create_dropselector(&$_c)
{
	global $context;

	$id = $_c['id'];
	echo '
	<div class="dropselect">
	<div class="downarrow" id="',$id,'">
	</div>
	TESTING
	<div class="dropselect_content left" id="',$id,'_content">';
	if(isset($_c['content']))
		echo $_c['content'];
	elseif(isset($_c['items'])) {
		echo '
	<ol class="commonlist">';
		foreach($_c['items'] as $item)
			echo '
		<li>
		 ',$item['html'],'
		</li>';
		echo '
	</ol>';
	}
	echo '
	</div>
	</div>';
	if(!isset($context['footer_script_fragments']['dropselector'])) {
		$script = <<<EOT
	$("div.downarrow").hover(function() {
		var id = $(this).attr("id");
		$("#" + id + "_content").show();
	},
	function() {

	});
	$("div.dropselect_content").live("mouseleave", function(event) {
		$(this).hide();
	});
EOT;

		registerFooterScriptFragment('dropselector', $script);
	}
}

function template_footer_scripts()
{
	global $context, $settings;

	if(!empty($context['theme_scripts'])) {
		foreach($context['theme_scripts'] as $type => $script) {
			if($script['footer'])
				echo '
	<script type="text/javascript" src="',($script['default'] ? $settings['default_theme_url'] : $settings['theme_url']) . '/' . $script['name'] . $context['jsver'], '"></script>';
		}
	}
	if(!empty($context['inline_footer_script']))
		echo '
	<script type="text/javascript">
	<!-- // --><![CDATA[
	',$context['inline_footer_script'],'

	';
	if(isset($context['footer_script_fragments'])) {
		foreach($context['footer_script_fragments'] as $this_script)
			echo $this_script;
	}
	echo '
	// ]]>
	</script>
	';
}
?>