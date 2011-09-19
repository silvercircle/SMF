<?php
function template_activitybit(&$a)
{
	global $settings;

	if(isset($a['member'])) {		// if we have member data available, show the avatar, otherwise just the activity
		echo '
	<li data-id="',$a['id_act'],'">
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
	  ',$a['formatted_result'],'<br>
	  ',$a['dateline'],'
	  <div class="clear"></div>
	</li>';
	}
	else
		echo '
	<li data-id="',$a['id_act'],'">
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
 */
function template_notifications_scripts()
{
	echo '
	<script>
	// <![CDATA[
	function markAllNotificationsRead()
	{
	}
	// ]]>
	</script>';
}

function template_notifications_xml()
{
	global $context, $txt, $scripturl;

	echo '
	<div class="inlinePopup notifications" id="notificationsBody">
	<h1 class="bigheader">',$txt['act_recent_notifications'],'</h1>
	';
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
	<div class="red_container centertext">'
	,$txt['act_no_unread_notifications'],'
	</div>';
	echo '
	<div class="yellow_container smalltext">
	<dl class="common">
	<dt>
	<a onclick="markAllNotificationsRead();return(false);" href="',$scripturl,'?action=astream;sa=markread;act=all">',$txt['act_mark_all_read'],'</a>
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
?>