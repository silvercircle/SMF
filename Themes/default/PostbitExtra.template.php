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
function template_postbit_compact(&$message)
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $topic;
	
	// Show the message anchor and a "new" anchor if this message is new.
	echo '
	<div class="post_wrapper" data-mid="',$message['id'], '">';

	if(isset($context['is_display_std'])) {
		$message['can_quote'] = $context['can_quote'];
		$message['can_reply'] = $context['can_reply'];
		$message['can_delete'] = $message['can_remove'];
		$message['can_mark_notify'] = $context['can_mark_notify'];
		$message['topic']['id'] = $context['current_topic'];
		$message['board']['link'] = '';
	}

	if (isset($context['first_message']) && $message['id'] != $context['first_message'])
		echo '
	<a id="msg', $message['id'], '"></a>', $message['first_new'] ? '<a id="new"></a>' : '';
	
	// Show information about the poster of this message.
	echo '
		<div class="keyinfo">
		 <div class="messageicon">
		  <img src="', $message['icon_url'] . '" alt="" />
		 </div>
		 <h5 style="display:inline;" id="subject_', $message['id'], '">'
		  , $message['subject'], '
		 </h5>
		 <span class="smalltext">&nbsp;',$message['time'], '</span>
	 	 <span class="permalink_old"><a href="', $message['permahref'], '" rel="nofollow">',$message['permalink'],'</a></span>
		 <div id="msg_', $message['id'], '_quick_mod"></div>
		</div>';

	// Show the post itself, finally!
	echo '
		<div class="post_content lean">';
	if(isset($message['member'])) {
		echo '
		<div class="blue_container cleantop cleanvert">
		<div class="content inset_shadow" style="line-height:19px;">';
		if (!empty($settings['show_user_images']) && empty($options['show_no_avatars'])) {
			if(!empty($message['member']['avatar']['image']))
				echo '
		<span class="small_avatar floatleft">
		<a href="', $scripturl, '?action=profile;u=', $message['member']['id'], '">
		', $message['member']['avatar']['image'], '
		</a>
		</span>';
			else
				echo '
		<span class="small_avatar floatleft">
			<a href="', $scripturl, '?action=profile;u=', $message['member']['id'], '">
			<img src="',$settings['images_url'],'/unknown.png" alt="avatar" />
			</a>
		</span>';
		}
		echo
		 '&nbsp;',$txt['posted_by'], '&nbsp;<strong>',$message['member']['link'], '</strong>';
		if(!isset($context['is_display_std']))
			echo '
		 &nbsp;',$txt['in'],'&nbsp;',$message['topic']['link'],'&nbsp;(',$txt['started_by'],'&nbsp;<strong>',$message['first_poster']['link'],'</strong>,&nbsp;',$message['first_poster']['time'],')<br>
		 &nbsp;',$txt['board'], ':&nbsp;<strong>',$message['board']['link'],'</strong><br>';
		echo '
		<div class="clear"></div>
		</div>
		</div>';
	}
	echo '
		<div class="post" id="msg_', $message['id'], '">';

	if(isset($message['approved'])) {
		if (!$message['approved'] && $message['member']['id'] != 0 && $message['member']['id'] == $context['user']['id'])
			echo '
		<div class="approve_post">
		 ', $txt['post_awaiting_approval'], '
		</div>';
	}
	echo '
		<article>
		 ', $message['body'], '
		</article>
		</div>
		<div class="moderatorbar">';
	if((isset($message['likes_count']) && $message['likes_count'] > 0) || !empty($message['likelink']))
		echo '
		<div class="likebar">
		 <div class="floatright">',$message['likelink'],'</div>
		 <span id="likers_msg_',$message['id'],'">',$message['likers'],'</span>
		 <div class="clear"></div>
		</div>';

	echo '
		</div>';
	echo '
		</div>
		<div class="post_bottom">
		 <div style="display:inline;">
		<ul class="floatright reset quickbuttons" style="line-height:100%;">';
		if($message['can_quote'] || $message['can_reply'])
			echo '
			<li><a rel="nofollow" role="button" href="', $scripturl, '?action=post;quote=', $message['id'], ';topic=', $message['topic']['id'], '.', $context['start'], '">', $txt['quote'], '</a></li>';
		if($message['can_mark_notify'])
			echo '
			<li><a rel="nofollow" role="button" href="', $scripturl, '?action=notify;topic=', $message['topic']['id'], '.', $context['start'], '">', $txt['notify'], '</a></li>';
		if($message['can_delete'])
			echo '
			<li><a rel="nofollow" href="', $scripturl, '?action=deletemsg;topic=', $message['topic']['id'], '.', $context['start'], ';msg=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '" onclick="return confirm_(\'\', \'', $txt['remove_message'], '?\', $(this).attr(\'href\'));">', $txt['remove'], '</a></li>';
		echo '
		</ul>';
			echo '
		  <span class="modified" id="modified_', $message['id'], '">';
			if ($settings['show_modify'] && !empty($message['modified']['name']))
				echo '
		  <em>', $txt['last_edit'], ': ', $message['modified']['time'], ' ', $txt['by'], ' ', $message['modified']['name'], '</em>';

				echo '
		  </span>';
			echo '
		 </div>
		 <div class="reportlinks">
		 </div>
		 <ul class="floatright reset smalltext quickbuttons">
		 </ul>';
	echo '
		 <div class="clear"></div>
		 </div>';

	echo '
	    <div class="clear"></div>
		</div>
		';
}
?>
