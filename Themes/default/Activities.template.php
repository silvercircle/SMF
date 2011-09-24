<?php
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

function template_showactivity_xml()
{
	global $context, $txt;

	echo '
	<div class="title_bar">
		<h1>',$context['titletext'],'</h1>
	</div>
	<div class="smallpadding">';
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
			alert(_s);
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
?>