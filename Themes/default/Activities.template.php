<?php
function template_activitybit(&$a)
{
	global $settings;

	if(isset($a['member'])) {		// if we have member data available, show the avatar, otherwise just the activity
		echo '
	<li class="smalltext">
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
	<li>',$a['formatted_result'],'</li>';
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
	<ol class="commonlist">';
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
?>