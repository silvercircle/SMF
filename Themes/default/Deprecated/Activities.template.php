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