<?php
function template_main()
{
		global $context, $user_info, $scripturl, $settings, $txt;
	
		if($context['preview']) {
		echo '<table style="border:0;width:100%;max-width:600px;position:relative;top:-32px;margin-bottom:-32px;"><tr><td style="vertical-align:top;text-align:center;">';
		/*if(!empty($member_started['avatar']['image']))
			echo $member_started['avatar']['image'];
		else
			echo '<img class="avatar" src="',$settings['images_url'], '/unknown.png" alt="avatar" />';*/

		echo '</td>';
		echo '<td style="width:100%;padding:2px 5px;vertical-align:top;">';
		echo '<div style="margin-right:10px;margin-bottom:8px;font-size:12px;line-height:20px;"><h4 style="color:#fff;">',$context['preview']['first_subject'],'</h4></div>';
		echo '<div class="orange_container" style="padding:3px;margin-bottom:3px;"><strong>',$txt['started_by'], ': ', $context['member_started']['name'], ', ', $context['preview']['first_time'],'</strong></div>';
		echo '<div class="blue_container" style="padding:3px;margin-bottom:5px;">', $context['preview']['first_body'];
		echo '</div></td></tr></table>';
		
		if($context['member_lastpost']) {
			echo '<table style="border:0;width:100%;max-width:600px;position:relative;"><tr><td style="vertical-align:top;text-align:center;">';
			/*if(!empty($member_lastpost['avatar']['image']))
				echo $member_lastpost['avatar']['image'];
			else
				echo '<img class="avatar" src="',$settings['images_url'], '/unknown.png" alt="avatar" />';*/
			echo '</td>';

			echo '<td style="width:100%;padding:2px 5px;vertical-align:top;">';
			echo '<div class="orange_container" style="padding:3px;margin-bottom:3px;"><strong>',$txt['last_post'],' ',$txt['by'], ': ', $context['member_lastpost']['name'], ', ', $context['preview']['last_time'],'</strong></div>';
			echo '<div class="blue_container" style="padding:3px;margin-bottom:5px;">', $context['preview']['last_body'];
			echo '</div></td></tr></table>';
		}
		echo '<div class="title_bar">
			<div style="position:absolute;bottom:3px;right:8px;">bottom links</div><div style="clear:both;"></div></div>';
		}
		else
			echo '<div style="text-alig:center;padding:20px 10px;">',$txt['no_access'], '</div>';
}
?>
