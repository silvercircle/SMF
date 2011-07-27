<?php
//rateTopic Template File
//RateTopic.template.php

function template_RateTopicSave()
{
	global $context, $settings, $options, $txt;

	echo '<', '?xml version="1.0" encoding="', $context['character_set'], '"?', '>
<ratetopic>
	<confirm id="'.$_REQUEST['postId'].'">'.$context['Rate_Topic']['confirmMsg'].'</confirm>
	<newrlt><![CDATA[';
		foreach($context['Rate_nlt'] as $row){
			echo $row['totals'].'x<span class="rate_show" style="'.($context['browser']['is_ie']?'':'padding-top: 4px; ').'background-image: url('.$settings['images_url'], '/post/'.$context['topic_rate'][$row['id_image']]['image'].'.gif );" title="'.$context['topic_rate'][$row['id_image']]['title'].'"><img src="'.$settings['default_theme_url'].'/ratetopic/blank.gif" alt="'.$context['topic_rate'][$row['id_image']]['title'].'" height="16" width="16" /></span> ';
		}
	echo ']]></newrlt>
</ratetopic>';
}

function template_RateTopicGet()
{
	global $context, $settings, $options, $txt;

	echo '<', '?xml version="1.0" encoding="', $context['character_set'], '"?', '>
<ratetopic>
	<divinfo id="'.$_REQUEST['userId'].'"><![CDATA[
	<table class="windowbg" style="border: 1px solid #696969;">
		<tr>
			<td colspan="2" align="right"><span class="meaction" onclick="tt_HideInit()">[X]</span></td>
		</tr>
		<tr>
			<td>'.$txt['rateTopic']['was_rated'].'</td><td>'.$txt['rateTopic']['gave_rated'].'</td>
		</tr>
		<tr>
			<td valign="top">
				<table width="150px">
					';
					foreach($context['Rate_Topic']['was_rated'] as $row){
						echo '<tr>
								<td><span class="rate_show" style="'.($context['browser']['is_ie']?'':'padding-top: .5px; ').'background-image: url('.$settings['images_url'], '/post/'.$context['topic_rate'][$row['id_image']]['image'].'.gif );"><img src="'.$settings['default_theme_url'].'/ratetopic/blank.gif" alt="'.$context['topic_rate'][$row['id_image']]['title'].'" height="16" width="16" /></span> '.$row['totals'].'x '.$context['topic_rate'][$row['id_image']]['title'].'</td>
							</tr>
							';
					}
	echo '
				</table>
			</td>
			<td valign="top">
				<table width="150px">
					';
					foreach($context['Rate_Topic']['gave_rated'] as $row){
						echo '<tr>
								<td valign="top"><span class="rate_show" style="'.($context['browser']['is_ie']?'':'padding-top: .5px; ').'background-image: url('.$settings['images_url'], '/post/'.$context['topic_rate'][$row['id_image']]['image'].'.gif );"><img src="'.$settings['default_theme_url'].'/ratetopic/blank.gif" alt="'.$context['topic_rate'][$row['id_image']]['title'].'" height="16" width="16" /></span> '.$row['totals'].'x '.$context['topic_rate'][$row['id_image']]['title'].'</td>
							</tr>
							';
					}
	echo '
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="smalltext">'.$txt['rateTopic']['given_by'].'</td>
		</tr>
		<tr>
			<td valign="top" colspan="2">
				<table width="100%">
					';
		$odd = 0;
		foreach($context['Rate_Topic']['rated_from'] as $row){
			if(($odd%2) == 0)
				echo '<tr>';

			echo '
					<td class="smalltext"><span>'.$row['totals'].' x '.$row['realName'].'</span></td>';
					
			if(($odd%2) == 1)
				echo '		
					</tr>
				';
			$odd++;
		}
		if(($odd%2) == 1)
			echo '		
					</tr>
				';
					

	echo '
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="smalltext">'.$txt['rateTopic']['given_to'].'</td>
		</tr>
		<tr>
			<td valign="top" colspan="2">
				<table width="100%">
					';
			$odd = 0;
		foreach($context['Rate_Topic']['rated_to'] as $row){
			if(($odd%2) == 0)
				echo '<tr>';

			echo '
					<td class="smalltext"><span>'.$row['totals'].' x '.$row['realName'].'</span></td>';
					
			if(($odd%2) == 1)
				echo '		
					</tr>
				';
			$odd++;
		}
		if(($odd%2) == 1)
			echo '		
					</tr>
				';
	echo '
				</table>
			</td>
		</tr>		
	</table>
	]]></divinfo>
</ratetopic>';
}

?>