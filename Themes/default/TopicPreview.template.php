<?php
function template_main()
{
	global $context, $scripturl, $txt;

	if($context['preview']) {
		echo '
			<div class="title_bar">
				<h1>',$context['preview']['first_subject'],'</h1>
			</div>
			<div class="smallpadding" id="mcard_content">';
		echo '
			<div class="orange_container" style="padding:3px;margin-bottom:3px;"><strong>',$txt['started_by'], ': ', $context['member_started']['name'], ', ', $context['preview']['first_time'],'</strong></div>';
		echo '
			<div class="blue_container mediumpadding" style="margin-bottom:5px;">', $context['preview']['first_body'], '</div>';
		
		if($context['member_lastpost']) {
			echo '<div class="orange_container" style="padding:3px;margin-bottom:3px;"><strong>',$txt['last_post'],' ',$txt['by'], ': ', $context['member_lastpost']['name'], ', ', $context['preview']['last_time'],'</strong></div>';
			echo '<div class="blue_container mediumpadding" style="margin-bottom:5px;">', $context['preview']['last_body'], '</div>';
		}
		echo '
			</div>
			<div class="cat_bar">
			<div style="position:absolute;bottom:3px;right:8px;">
			<a href="',$scripturl,'?topic=',$context['preview']['id_topic'],'">',$txt['read_topic'],'</a>
			&nbsp;|&nbsp;<a href="',$scripturl,'?topic=',$context['preview']['id_topic'],'.msg',$context['preview']['new_from'],'#new">',$txt['visit_new'],'</a>
			</div>
				<div class="clear"></div>
			</div>';
	}
	else
		echo '<div class="orange_container largepadding">',$txt['no_access'], '</div>';
}
?>
