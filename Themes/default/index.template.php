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

/*	This template is, perhaps, the most important template in the theme. It
	contains the main template layer that displays the header and footer of
	the forum, namely with main_above and main_below. It also contains the
	menu sub template, which appropriately displays the menu; the init sub
	template, which is there to set the theme up; (init can be missing.) and
	the linktree sub template, which sorts out the link tree.

	The init sub template should load any data and set any hardcoded options.

	The main_above sub template is what is shown above the main content, and
	should contain anything that should be shown up there.

	The main_below sub template, conversely, is shown after the main content.
	It should probably contain the copyright statement and some other things.

	The linktree sub template should display the link tree, using the data
	in the $context['linktree'] variable.

	The menu sub template should display all the relevant buttons the user
	wants and or needs.

	For more information on the templating system, please see the site at:
	http://www.simplemachines.org/
*/

// Initialize the template... mainly little settings.
function template_init()
{
	global $context, $settings, $options, $txt;

	/* Use images from default theme when using templates from the default theme?
		if this is 'always', images from the default theme will be used.
		if this is 'defaults', images from the default theme will only be used with default templates.
		if this is 'never' or isn't set at all, images from the default theme will not be used. */
	$settings['use_default_images'] = 'never';

	/* What document type definition is being used? (for font size and other issues.)
		'xhtml' for an XHTML 1.0 document type definition.
		'html' for an HTML 4.01 document type definition. */
	$settings['doctype'] = 'html';

	/* The version this template/theme is for.
		This should probably be the version of SMF it was created for. */
	$settings['theme_version'] = '2.0';

	/* Set a setting that tells the theme that it can render the tabs. */
	$settings['use_tabs'] = true;

	/* Use plain buttons - as opposed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status separate from topic icons? */
	$settings['separate_sticky_lock'] = true;

	/* Set the following variable to true if this theme requires the optional theme strings file to be loaded. */
	$settings['require_theme_strings'] = false;
}

// The main sub template above the content.
function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html ', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>';

	// The ?fin20 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index', $context['theme_variant'], '.css?fin20" />';

	// Some browsers need an extra stylesheet due to bugs/compatibility issues.
	//foreach (array('ie7', 'ie6', 'webkit') as $cssfix)
	//	if ($context['browser']['is_' . $cssfix])
	//		echo '
	//<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/', $cssfix, '.css" />';

	// RTL languages require an additional stylesheet.
	if ($context['right_to_left'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/rtl.css" />';

	// Here comes the JavaScript bits!
		echo '
		<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/min/jquery.js?fin20"></script>
		<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/min/script.js?fin20"></script>';
	if(isset($_REQUEST['action']) && $_REQUEST['action'] === 'admin')
		echo '
		<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/admin.js?fin20"></script>';
	echo '
		<script type="text/javascript">
		// <![CDATA[
var smf_theme_url = "', $settings['theme_url'], '";
var smf_default_theme_url = "', $settings['default_theme_url'], '";
var smf_images_url = "', $settings['images_url'], '";
var smf_scripturl = "', $scripturl, '";
var smf_iso_case_folding = ', $context['server']['iso_case_folding'] ? 'true' : 'false', ';
var smf_charset = "', $context['character_set'], '";', $context['show_pm_popup'] ? '
var fPmPopup = function ()
{
	if (confirm("' . $txt['show_personal_messages'] . '"))
		window.open(smf_prepareScriptUrl(smf_scripturl) + "action=pm");
}
addLoadEvent(fPmPopup);' : '', '
var sSessionId = \'', $context['session_id'], '\';
var sSessionVar = \'', $context['session_var'], '\';
var disableDynamicTime = ',empty($options['disable_dynatime']) ? 0 : 1,';
var textSizeUnit = \'pt\';
var textSizeStep = 1;
var textSizeMax = 16;
var textSizeMin = 8;
var textSizeDefault = 10;
var cookie = readCookie(\'SMF_textsize\');
var textsize = cookie ? parseInt(cookie) : textSizeDefault;
var anchor = document.getElementsByTagName(\'SCRIPT\')[0];
var t2 = document.createElement(\'SCRIPT\');
t2.type = "text/javascript";
t2.async = true;
t2.src = "',$settings['theme_url'],'/scripts/min/footer.js?ver=1.1.0";
anchor.parentNode.insertBefore(t2, anchor);
	// ]]>
	</script>';
	echo '
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title_html_safe'], '" />', !empty($context['meta_keywords']) ? '
	<meta name="keywords" content="' . $context['meta_keywords'] . '" />' : '', '
	<title>', $context['page_title_html_safe'], '</title>';

	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex" />';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '" />';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help" />
	<link rel="search" href="', $scripturl, '?action=search" />
	<link rel="contents" href="', $scripturl, '" />';

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
	//if (!empty($context['current_board']))
		//echo '<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0" />';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	echo '
</head>
<body>';
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
	<div id="ajaxbusy" style="display:none;"><img src="',$settings['images_url'],'/ajax-loader.gif" alt="loader" /></div>
	<div id="mcard" style="display:none;"><div id="mcard_close">X</div><div id="mcard_inner"></div></div>
	<div id="wrap" style="max-width:',empty($settings['forum_width']) ? '3000px' : $settings['forum_width'],';">
	<header>
	<div id="header">
	<div id="upper_section" class="middletext"><div style="float:left;color:#ddd;text-shadow:black 2px 2px 10px;font-size:35px;font-family:Comic Sans MS;padding:20px 30px;"><strong><em>SMF pLayGround</em></strong><br />
		<div style="font-size:16px;height:26px;padding-top:20px;">...Test</div>
	</div>';

	// If the user is logged in, display stuff like their name, new messages, etc.
	// for the logo -> <img style="margin-left:30px;margin-top:10px;float:left;display:inline-block;" src="'.$settings['images_url'].'/bloglogo.png" alt="logo" />
	if ($context['user']['is_logged'])
	{
		echo '<div class="user" style="padding:5px 5px 0 0;">';

		if (!empty($context['user']['avatar']))
			echo '
				<div class="avatar">', $context['user']['avatar']['image'], '</div>';
		echo '
				<div><ul class="reset">
					<li class="greeting">', $txt['hello_member_ndt'], ' <span>', $context['user']['name'], '</span>&nbsp;&nbsp;<a href="',$scripturl,'?action=logout;',$context['session_var'],'=',$context['session_id'], '" style="font-size:11px;">[',$txt['logout'], ']</a></li>
					<li><a href="', $scripturl, '?action=unread">', $txt['unread_since_visit'], '</a></li>
					<li><a href="', $scripturl, '?action=unreadreplies">', $txt['show_unread_replies'], '</a></li>
					<li>', $context['current_time'], '</li></ul></div>';

		echo '<div style="margin-top:3px;"><ul class="reset"><li></li>';
		// Is the forum in maintenance mode?
		if ($context['in_maintenance'] && $context['user']['is_admin'])
			echo '
					<li class="notice">', $txt['maintain_mode_on'], '</li>';

		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '
					<li>', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '</li>';

		if (!empty($context['open_mod_reports']) && $context['show_open_reports'])
			echo '
					<li><a href="', $scripturl, '?action=moderate;area=reports">', sprintf($txt['mod_reports_waiting'], $context['open_mod_reports']), '</a></li>';

		echo '</ul></div></div>';
	}
	// Otherwise they're a guest - this time ask them to either register or login - lazy bums...
	elseif (!empty($context['show_login_bar']))
	{
		echo '
				<div class="user">
				<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/sha1.js"></script>
				<div class="loginbar"><form id="guest_form" action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
					<div style="float:left;margin:5px;">', sprintf($txt['welcome_guest'], $txt['guest_title']), '</div>
					<div style="margin:5px;"><input type="text" name="user" size="10" class="input_text" />
					<input type="password" name="passwrd" size="10" class="input_password" />
					<select name="cookielength">
						<option value="60">', $txt['one_hour'], '</option>
						<option value="1440">', $txt['one_day'], '</option>
						<option value="10080">', $txt['one_week'], '</option>
						<option value="43200">', $txt['one_month'], '</option>
						<option value="-1" selected="selected">', $txt['forever'], '</option>
					</select>
					<input type="submit" value="', $txt['login'], '" class="button_submit" /><br />
					<div class="info">', $txt['quick_login_dec'], '</div></div>';

		if (!empty($modSettings['enableOpenID']))
			echo '
					<br /><input type="text" name="openid_identifier" id="openid_url" size="25" class="input_text openid_login" />';

		echo '
					<input type="hidden" name="hash_passwrd" value="" />
				</form></div></div>';
	}

	echo '
			<div class="news normaltext">';
	// Show a random news item? (or you could pick one from news_lines...)
	if (!empty($settings['enable_news']))
		echo '
				<h2>', $txt['news'], ': </h2>
				<p>', $context['random_news_line'], '</p>';

	echo '
			</div>
		</div><nav>';


	// Show the menu here, according to the menu sub template.
	template_menu();

	echo '</nav>
	<script>
		// <![CDATA[
    	setTextSize(textsize);
	// ]]></script>
	</div></header>';

	// The main content should go here.
	echo '
	<div id="content_section">
		<div id="main_content_section">';

	// Custom banners and shoutboxes should be placed here, before the linktree.

	theme_linktree();
	// Show the navigation tree.
	$scope = 0;
	echo '<form onmouseout="return false;" onsubmit="submitSearchBox();" style="float:right;margin-right:30px;margin-bottom:-20px;" id="search_form" action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">';
			// Search within current topic?
			$search_label = 'Search';
			if (isset($context['current_topic']) && $context['current_topic']) {
				$search_label = 'Search this topic';
				$scope = 2;
			}
			// If we're on a certain board, limit it to this board ;).
			elseif (isset($context['current_board'])) {
				$search_label = 'Search this board';
				$scope = 1;
			}
			echo '<input style="width:215px;padding-left:26px;margin:0;" onclick="var s_event = arguments[0] || window.event;openAdvSearch(s_event);return(false);" type="text" onfocus="if(!this._haschanged){this.value=\'\'};this._haschanged=true;" name="search" value="',$search_label,'" class="searchfield" />
				<div id="adv_search" style="width:246px;position:absolute;top:0;display:none;padding:0;padding-top:30px;" class="smalltext">
				<div class="orange_container">
				&nbsp;&nbsp;&nbsp;Search posts by member<br />
				<div style="text-align:center;margin-bottom:10px;"><input style="width:90%;" class="input_text" type="text" name="userspec" id="userspec" value="*" /></div>
				<input type="checkbox" name="show_complete" id="show_complete" value="0" />Show results as messages<br />';
				if($scope == 2) {
					echo '<div style="padding-left:20px;"><input type="radio" name="type" id="i_topic" class="input_radio" checked="checked" />Search this topic<br />
						<input type="radio" name="type" id="i_board" class="input_radio" />Search this board<br />
						<input type="radio" name="type" id="i_site" class="input_radio" />Search everything
						<input type="hidden" id="s_topic" name="topic" value="', $context['current_topic'], '" />
						<input type="hidden" id="s_board" name="brd[', $context['current_board'], ']" value="', $context['current_board'], '" /></div>';
				}
				else if($scope == 1) {
						echo '<div style="padding-left:20px;"><input name="type" type="radio" id="i_board" checked="checked" class="input_radio" />Search this board<br />
						<input type="radio" name="type" id="i_site" class="input_radio" />Search everything
						<input type="hidden" id="s_board" name="brd[', $context['current_board'], ']" value="', $context['current_board'], '" /></div>';
				}
				echo '<input style="width:100%;margin:10px 0;" type="submit" name="submit" value="', 'Search now', '" class="button_submit" />
			 	  <div style="text-align:center;"><a href="',$scripturl,'?action=search" >Go advanced</a></div>';
				echo '</div></div>
				<noscript>
				<input style="margin:0;" type="submit" name="submit" value="', $txt['go'], '" class="button_submit" />
				</noscript>';
	echo '</form><div style="clear:both;"></div>';
}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings, $fbxml, $twitter_widgets, $plusone;

	echo '
		</div></div>';

	// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
	echo '
  	<div id="fb-root"></div>
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
	/*
	if($fbxml) {
			echo '
			window.fbAsyncInit = function() {
   				FB.init({appId: \'109862169045977\', status: true, cookie: true, xfbml: true});
  			};
  			(function() {
    			var e = document.createElement(\'script\'); e.async = true;
    			e.src = \'http://connect.facebook.net/en_US/all.js\';
				document.getElementById(\'fb-root\').appendChild(e);
  			}());
			';
	}
	if($twitter_widgets) {
			echo '
			var t1 = document.createElement(\'SCRIPT\');

			t1.src = \'http://platform.twitter.com/widgets.js\'; 
			t1.type = "text/javascript"; 
			t1.async = true;
			anchor.parentNode.insertBefore(t1, anchor);
			';
	}
	*/
	if($plusone) {
		echo '
		var t4 = document.createElement(\'SCRIPT\');

		t4.src = \'http://apis.google.com/js/plusone.js\'; 
		t4.type = "text/javascript"; 
		t4.async = true;
		anchor.parentNode.insertBefore(t4, anchor);
		';
	}
	echo $txt['jquery_timeago_loc'],'
	// ]]>
	</script>
	<div id="footer_section">';
	// Show the load time?
	if ($context['show_load_time'])
		$loadtime = $context['load_time']. 's CPU, '.$context['load_queries'] . $txt['queries'];
		
	$time_now = forum_time(false);
	$tz = date_default_timezone_get();
	echo '<div style="float:right;text-align:right;" class="smalltext">',$loadtime,'<br />Forum time: ',strftime($modSettings['time_format'], $time_now) . ' '. $tz,'</div>';
	
	
	echo '	<div class="copyright">', my_theme_copyright(), '</div>
			<div><a id="button_xhtml" href="http://validator.w3.org/check?uri=referer" target="_blank" class="new_win" title="Valid HTML"><span>HTML</span></a> | 
			', !empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']) ? '<a id="button_rss" href="' . $scripturl . '?action=.xml;type=rss" class="new_win"><span>' . $txt['rss'] . '</span></a> | ' : '', '
			<a id="button_wap2" href="', $scripturl , '?wap2" class="new_win"><span>', $txt['wap2'], '</span></a>
			</div>';

	echo '
	</div>';
}

function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
</div></body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree($force_show = false)
{
	global $context, $settings, $options, $shown_linktree;
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
	foreach ($context['linktree'] as $link_num => $tree)
	{
		$ltree .= ('
		<li'. (($link_num == count($context['linktree']) - 1) ? ' class="last"' : ''). '>');

		// Show something before the link?
		if (isset($tree['extra_before']))
			$ltree .= $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		$ltree .= ($settings['linktree_link'] && isset($tree['url']) ? ('
			<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>') : ('<span>') . $tree['name'] . '</span>');

		// Show something after the link...?
		if (isset($tree['extra_after']))
			$ltree .= $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1)
			$ltree .= ' &gt;';

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
	global $context, $settings, $options, $scripturl, $txt;

	$context['menu_buttons']['blog']['title'] = "Blog";
	$context['menu_buttons']['blog']['show'] = true;
	$context['menu_buttons']['blog']['href'] = "http://blog.miranda.or.at";
	
	echo '
		<div id="main_menu">
			<div style="float:right;line-height:24px;font-size:10px;font-family:Verdana;">
				<span style="color:white;" id="curfontsize"></span>
				<span title="',$txt['font_increase'], '" onclick="setTextSize(textsize + 1);return(false);" class="fontinc">&nbsp;</span>
				<span title="',$txt['font_decrease'], '" onclick="setTextSize(textsize - 1);return(false);" class="fontdec">&nbsp;</span>
			</div>
			<ul class="dropmenu" id="menu_nav">';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		if(!isset($button['active_button']))
			$button['active_button'] = false;
		echo '
				<li id="button_', $act, '">
					<a class="', $button['active_button'] ? 'active ' : '', 'firstlevel" href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
						<span class="', isset($button['is_last']) ? 'last ' : '', 'firstlevel">', $button['title'], '</span>
					</a>';
		if (!empty($button['sub_buttons']))
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
			</ul><div style="clear:both;"></div>
		</div>';
}

// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $strip_options = array())
{
	global $settings, $context, $txt, $scripturl;

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

	echo '
		<div class="buttonlist', !empty($direction) ? ' float' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul>',
				implode('', $buttons), '
			</ul>
		</div>';
}
function my_theme_copyright($get_it = false)
{
	global $forum_copyright, $context, $boardurl, $forum_version, $txt, $modSettings;

	// Don't display copyright for things like SSI.
	if (!isset($forum_version))
		return;

	// Put in the version...
	//$forum_copyright = sprintf($forum_copyright, $forum_version);
	$forum_copyright = 'Forum software based on SMF 2.0, &copy;2011 by <a href="http://www.simplemachines.org">Simple Machines</a> and contributors. <a href="http://www.simplemachines.org/about/smf/license.php">License terms.</a>';

	echo '
			<span class="smalltext" style="display: inline; visibility: visible;">' . $forum_copyright . '
			</span>';
}

/**
 * create JavaScript to inject asynchronous and XHTML compliant
 * facebook "like" and Twitter "tweet" buttons
 * @params:
 * 	$l:	article permalink to share
 *	$fb:	if true, generate facebook button
 *	$tw:	if true, generate tweet button
 */
function async_like_and_tweet($l, $fb = true, $tw = true, $layout="standard")
{
    global $fbxml, $twitter_widgets, $social_privacy, $plusone;
    
    echo '
	<script type="text/javascript">
	//<![CDATA[
	';
    if($fb) {
       $fbxml = 1;
       echo '(function() {
 
        document.write(\'<fb:like style="min-width:500px;min-height:21px;" width="500" href="',$l,'" layout="',$layout,'" send="true" show_faces="false" action="recommend" font="verdana"></fb:like>\');
    	})();';
    }
    if($tw) {
	$twitter_widgets = 1;
	$plusone++;
	echo '
		document.write(\'<div style="float:right;max-width:65px;overflow:hidden;"><div style="max-width:65px;" class="g-plusone" data-href="',$l,'" data-size="medium" data-count="true"></div></div>\');
   	    document.write(\'<a href="http://twitter.com/share" style="border:none;" class="twitter-share-button" data-count="horizontal" data-url="',$l,'"></a>\');
	';
    }	
    echo '//]]>
       </script>';
}

function socialbar($l, $t)
{
	global $social_privacy;
	
	if(1|| $social_privacy) {
		socialbar_passive($l, $t);
		return;
	}
	echo '<div class="bmbar">';
	async_like_and_tweet($l);
	echo '<div style="clear:both;"></div></div>';	
}

function socialbar_passive($l, $t)
{
	global $social_privacy, $plusone;
	
	echo '<div class="bmbar"><div class="title">Share this topic: </div>';
		$url = $l;
		$plusone++;
		
		//$fb = "<span class=\"share_button share_fb\" onclick=\"share_popup(\'http://www.facebook.com/sharer.php?u=".$url."\', 500,400);\">Share</span>";
		//$tw = "<span class=\"share_button share_tw\" onclick=\"share_popup(\'http://twitter.com/share?url=".$url."&amp;text=".$title."\', 550,300);\">Tweet</span>";
		echo '<div style="float:left;"><a role="button" rel="nofollow" class="share_button share_fb" href="http://www.facebook.com/sharer.php?u=',$url,'">Share</a>
			<a role="button" rel="nofollow" class="share_button share_tw" href="http://twitter.com/share?text=',$t,'&amp;url=',$url,'">Tweet</a>
			<a role="button" rel="nofollow" class="share_button share_digg" href="http://digg.com/submit?phase=2&amp;title=',$t,'&amp;url=',$url,'">Digg</a>
			<a role="button" rel="nofollow" class="share_button share_buzz" href="http://www.google.com/buzz/post?url=',$url,'">Buzz</a></div>&nbsp;&nbsp;
			<script type="text/javascript">
			//<![CDATA[
            	document.write(\'<div style="float:right;max-width:65px;overflow:hidden;"><g:plusone href="',$url,'" size="medium"></g:plusone></div>\');
    		//]]>
       		</script>
       		<div style="clear:both;"></div>';
	echo '</div><div style="clear:both;"></div>';
}
?>