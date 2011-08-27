<?php
function template_main()
{
	global $context, $user_info, $scripturl, $settings, $txt;
	
	if(empty($context['member'])) {
		echo '	<div class="orange_container largepadding">
					',$txt['no_access'],'
				</div>';
	}
	else {
		$member = $context['member'];
	    $loc = array();
	    
		echo '<table style="border:0;width:100%;position:relative;top:-32px;margin-bottom:-32px;"><tr><td style="vertical-align:top;text-align:center;">';
		if(!empty($member['avatar']['image']))
			echo $member['avatar']['image'];
		else
			echo '<img class="avatar" src="',$settings['images_url'], '/unknown.png" alt="avatar" />';

		echo '<br /><br />Karma: ', $member['karma']['good'];
		echo '<br />Posts: ', $member['posts'];
		echo '</td>';
		echo '<td style="width:100%;padding:2px 5px;vertical-align:top;">';
		echo '<div style="margin-right:10px;margin-bottom:8px;font-size:20px;line-height:20px;"><h4 style="color:#fff;">',$member['name'],'</h4></div>';
		if(!empty($member['blurb']))
			echo '<div class="orange_container" style="padding:3px;margin-bottom:3px;"><strong>',$member['blurb'],'</strong></div>';
		echo '<div class="blue_container" style="padding:3px;margin-bottom:5px;">';
		if(!empty($member['group']))
			echo $txt['primary_membergroup'], ': <strong>', $member['group'], '</strong><br />';
		if(!empty($member['post_group']))
			echo $txt['additional_membergroups'], ': <strong>',$member['post_group'],'</strong><br /><br />';
		
		
		if(!empty($member['gender']['name']))
			$loc[0] = $member['gender']['image'].$member['gender']['name'];
			
		if(isset($member['birth_date']) && !empty($member['birth_date'])) {
			$l = idate('Y', time()) - intval($member['birth_date']);
			if($l < 100)
				$loc[1] = $l;
		}
			
		if(!empty($member['location']))
			$loc[2] = 'from '.$member['location'];
			
		if(!empty($loc)) 
			echo implode(', ', $loc), '<br />';
		echo 'Member since: ', $member['registered'],'<br />';
		if(!empty($member['website']['url']))
			echo 'Web site: <a href="',$member['website']['url'],'">', $member['website']['title'], '</a><br />';
		
		echo '<br />',sprintf($txt['like_profile_report'], $member['name'], $member['likesgiven'], $member['liked'], $member['posts']), '<br /><br />';
		
		if($member['online']['is_online'])
			echo $member['name'], ' is <strong style="color:red;">online</strong>';
		else
			echo 'Last activity: ',$member['last_login'];
			
		echo '</div></td></tr></table>';
		echo '<div class="cat_bar">
			<div style="position:absolute;bottom:3px;right:8px;"><a href="',$scripturl,'?action=profile;u=',$member['id'],'">View full profile</a></div><div style="clear:both;"></div></div>';
	}
}
?>
