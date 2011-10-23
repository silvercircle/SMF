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
	global $context, $scripturl, $txt;
	echo '<', '?xml version="1.0" encoding="', $context['character_set'], '" ?', '>
<document>
 <response open="default_overlay" width="0" />
 <onready>
  <![CDATA[
  	<script>
  	</script>
  ]]>
 </onready>
 <content>
 <![CDATA[
';
	if($context['preview']) {
		echo '
	<div class="title_bar">
	 <h1>',$context['preview']['first_subject'],'</h1>
	</div>
	<div class="smallpadding" id="mcard_content">';
		echo '
	 <div class="orange_container" style="margin-bottom:3px;"><strong>',$txt['started_by'], ': ', $context['member_started']['name'], ', ', $context['preview']['first_time'],'</strong></div>';
		echo '
	 <div class="blue_container smallpadding" style="margin-bottom:5px;">', $context['preview']['first_body'], '</div>';
		
		if($context['member_lastpost'])
			echo '
	 <div class="orange_container" style="margin-bottom:3px;"><strong>',$txt['last_post'],' ',$txt['by'], ': ', $context['member_lastpost']['name'], ', ', $context['preview']['last_time'],'</strong></div>
	 <div class="blue_container smallpadding" style="margin-bottom:5px;">', $context['preview']['last_body'], '</div>';
		echo '
	</div>
	<div class="cat_bar">
	  <div style="position:absolute;bottom:3px;right:8px;">
	   <a href="',$scripturl,'?topic=',$context['preview']['id_topic'],'">',$txt['read_topic'],'</a>
		&nbsp;|&nbsp;<a href="',$scripturl,'?topic=',$context['preview']['id_topic'],'.msg',$context['preview']['new_from'],'#new">',$txt['visit_new'],'</a>
	  </div>
	<div class="clear">
	</div>
	</div>';
	}
	echo '
 ]]>
 </content>
</document>';
}
?>