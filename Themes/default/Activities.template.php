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
function template_activitybit(&$a)
{
	global $settings;

	if(isset($a['member'])) {		// if we have member data available, show the avatar, otherwise just the activity
		echo '
	<li class="',($a['unread'] ? 'unread' : 'read'),'" id="_nn_',$a['id_act'],'" data-id="',$a['id_act'],'">
	  <div class="floatleft" style="margin-right:10px;">
	   <span class="small_avatar">';
	if(!empty($a['member']['avatar']['image'])) {
		echo '
	    <img class="twentyfour" src="', $a['member']['avatar']['href'], '" alt="avatar" />';
	}
	else {
		echo '
	    <img class="twentyfour" src="',$settings['images_url'],'/unknown.png" alt="avatar" />';
	}
	echo '
	   </span>
	  </div>
	  ',$a['formatted_result'],'<br />
	  ',$a['dateline'],'
	  <div class="clear"></div>
	</li>';
	}
	else
		echo '
	<li class="',($a['unread'] ? 'unread' : 'read'),'" id="_nn_',$a['id_act'],'" data-id="',$a['id_act'],'">
	',$a['formatted_result'],'
	</li>';
}

function template_showactivity()
{
	global $context, $txt;

	if($context['act_results']) {
	echo '
	<div class="pagelinks">',$context['pages'],'</div>
	<ol class="commonlist notifications">
	<li class="glass centertext">',$context['titletext'],'</li>';
	foreach($context['activities'] as $activity)
		template_activitybit($activity);
	echo '
	</ol>
	<div class="pagelinks">',$context['pages'],'</div>';
	}
	else
		echo '
	<div class="red_container">
	',$txt['act_no_results'],'
	</div>';
}

function template_showactivity_xml()
{
	global $context, $txt;

	echo '<', '?xml version="1.0" encoding="UTF-8" ?', '>
<document>
 <response open="default_overlay" width="50%" />
  <content>
  <![CDATA[
';	
	echo '
	<div class="title_bar">
		<h1>',$context['titletext'],'</h1>
	</div>
	<div class="smallpadding" id="mcard_content">';
	if($context['act_results']) {
		echo '
	<ol class="commonlist notifications">';
	foreach($context['activities'] as $activity)
		template_activitybit($activity);
	echo '
	</ol>';
	}
	else
		echo '
	<div class="red_container">
	',$txt['act_no_results'],'
	</div>';
	echo '
	</div>
	<div class="yellow_container smalltext cleantop smallpadding">';
	if(isset($context['viewall_url']))
		echo '
		<a href="',$context['viewall_url'],'" >View all</a>';
	echo '
	</div>
	]]>
  </content>
</document>';
}

function template_showactivity_profile()
{
	global $context, $txt;

	if($context['act_results']) {
	echo '
	<ol class="commonlist">
	<li class="glass centertext">',$context['titletext'],'</li>';
	foreach($context['activities'] as $activity)
		template_activitybit($activity);
	echo '
	</ol>';
	}
	else
		echo '
	<div class="red_container">
	',$txt['act_no_results'],'
	</div>';
}

/**
 * output the js code for dealing with the inline list of notifications
 * this contains code for marking notifications as read among a few other things
 * this code isn't needed elsewhere, so it goes to the template instead of the
 * global scripts to avoid bloat.
 */
function template_notifications_scripts()
{
	echo '
	<script type="text/javascript">
	// <![CDATA[
	$("ol#notifylist li.unread a._m").die("click");
	function markAllNotificationsRead()
	{
		var ids = "";
		$("#notifylist li").each(function() {
			ids += ($(this).attr("data-id") + ",");
		});
		var sUrl = 	smf_prepareScriptUrl(smf_scripturl) + "action=astream;sa=markread;act=" + ids + ";xml";
		sendXMLDocument(sUrl, "", notifyMarkReadHandleResponse);
		setBusy(1);
	}
	function notifyMarkReadHandleResponse(responseXML)
	{
		setBusy(0);
		var data = $(responseXML);
		var response = data.find("response");
		var ids = response.children("[name=\'markedread\']");

		var total = parseInt($("#alerts").html());

		if(ids.length == 1 && $(ids[0]).text() == "all") {
			total = 0;
		}
		else {
			ids.each(function() {
				var id = parseInt($(this).text());
				var sel = "ol#notifylist li#_nn_" + id;
				$(sel).removeClass("unread");
				total--;
			});
		}
		$("#alerts").html(total);
		if(total <= 0)
			$("#alerts").hide();
	}
	$("ol#notifylist li.unread a._m").live("click", function() {
		var id = parseInt($(this).parent().attr("data-id"));
		var sUrl = 	smf_prepareScriptUrl(smf_scripturl) + "action=astream;sa=markread;act=" + id + ";xml";
		//sendXMLDocument(sUrl, "", notifyMarkReadHandleResponse);
		//setBusy(1);
		return(true);
	});
	$(document).ready(function() {
		$("ol#notifylist li.unread a._m").each(function() {
			var _s = $(this).attr("href");
			if(_s.indexOf("#")) {
				var _parts = _s.split("#");
				_s = _parts[0] + ";nmdismiss=" + $(this).attr("data-id") + "#" + _parts[1];
			}
			else
				_s += (";nmdismiss=" + $(this).attr("data-id"));
			$(this).attr("href", _s);
		});
	});
	// ]]>
	</script>';
}

/**
 * output the inline list of notifications
 * (xhttp response).
 */
function template_notifications_xml()
{
	global $context, $txt, $scripturl;

	echo '
	<div class="inlinePopup notifications" id="notificationsBody">
	<div class="cat_bar2 norounded">
	<h3>',$txt['act_recent_notifications'],'</h3>
	</div>
	';
	if($context['act_results']) {
		echo '
	<ol id="notifylist" class="commonlist notifications">';
	foreach($context['activities'] as $activity)
		template_activitybit($activity);
	echo '
	</ol>';
	}
	else
		echo '
	<div class="red_container cleantop centertext smalltext">'
	  ,$txt['act_no_unread_notifications'],'
	</div>';
	echo '
	<div class="yellow_container smalltext cleantop">
	<dl class="common">
	<dt>';
	if($context['act_results'])
		echo '
		<a onclick="markAllNotificationsRead();return(false);" href="',$scripturl,'?action=astream;sa=markread;act=all">',$txt['act_mark_all_read'],'</a>';
	echo '
	</dt>
	<dd class="righttext">
	<a href="',$scripturl,'?action=astream;sa=notifications;view=all">',$txt['act_view_all'],'</a>
	</dd>
	</dl>
	</div>';
	echo '
	</div>';
	template_notifications_scripts();
}
function template_notifications()
{
	global $context, $txt, $scripturl;

	echo '
	<div class="cat_bar2 norounded">
	<h3>',$txt['act_recent_notifications'],'</h3>
	</div>
	';
	if($context['act_results']) {
		echo '
	<ol id="notifylist" class="commonlist notifications">';
	foreach($context['activities'] as $activity)
		template_activitybit($activity);
	echo '
	</ol>';
	}
	else
		echo '
	<div class="red_container cleantop centertext smalltext">'
	  ,$txt['act_no_unread_notifications'],'
	</div>';
	echo '
	<div class="yellow_container smalltext cleantop">
	<dl class="common">
	<dt>';
	if($context['act_results'] && $context['unread_count'] > 0)
		echo '
		<a onclick="markAllNotificationsRead();return(false);" href="',$scripturl,'?action=astream;sa=markread;act=all">',$txt['act_mark_all_read'],'</a>';
	echo '
	</dt>
	<dd class="righttext">';
	if(!$context['view_all'])
		echo '
	<a href="',$scripturl,'?action=astream;sa=notifications;view=all">',$txt['act_view_all'],'</a>';
	echo '
	</dd>
	</dl>
	</div>';
	template_notifications_scripts();
}

/**
 * opt-out settings for activities and notifications in the profile area
 */
function template_showactivity_settings()
{
	global $context, $txt;
	
	echo <<<EOT
	<form method="post" action="{$context['submiturl']}">
	<div class="cat_bar">
	   <h3>{$txt['activities_label']}</h3>
	</div>
	<div class="orange_container cleantop">
	 <div class="content">
	 {$txt['act_optout_desc']}
	 </div>
	</div>
	<br>
	<div class="blue_container">
	 <div class="content">
	 <ol class="commonlist">
EOT;
	 foreach($context['activity_types'] as $t) {
		 if(!empty($t['longdesc_act']))
			 echo '
	  <li>
	  <dl class="settings">
	  <dt style="width:90%;">
	  ',$t['longdesc_act'],'
	  </dt>
	  <dd style="width:10%;">
	  <input type="checkbox" class="input_check" name="act_check_', trim ($t['id']), '" id="act_check_',trim($t['id']),'" ',($t['act_optout'] ? '' : 'checked="checked"'), ' />
	  </dd>
	  </dl>
	  </li>';
	 }
	echo <<< EOT
	 </ol>
	 </div>
	</div>
	<br>
	<div class="cat_bar">
	   <h3>{$txt['notifications_label']}</h3>
	</div>
	<div class="orange_container cleantop">
	 <div class="content">
	 {$txt['notify_optout_desc']}
	 </div>
	</div>
	<br>
	<div class="blue_container">
	 <div class="content">
	 <ol class="commonlist">
EOT;
	 reset($context['activity_types']);
	 foreach ($context['activity_types'] as $t) {
		if (!empty($t['longdesc_not']))
			echo '
	  <li>
	  <dl class="settings">
	  <dt style="width:90%;">
	  ', $t['longdesc_not'], '
	  </dt>
	  <dd style="width:10%;">
	  <input type="checkbox" class="input_check" name="not_check_', trim ($t['id']), '" id="not_check_', trim($t['id']), '" ', ($t['notify_optout'] ? '' : 'checked="checked"'), ' />
	  </dd>
	  </dl>
	  </li>';
	}
	echo '
	 </ol>
	 </div>
	</div>
	<br>
	<div class="floatright">
	 <input type="submit" class="button_submit" value="', $txt['change_profile'], '" />
	</div>
	<div class="clear"></div>
	</form>';
}
?>