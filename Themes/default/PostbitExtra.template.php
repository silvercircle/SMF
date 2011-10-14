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
function template_postbit_compact(&$message, $ignoring)
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $topic;
	
	// Show the message anchor and a "new" anchor if this message is new.
	echo '<div class="post_wrapper light_shadow" data-mid="',$message['id'], '">';

	if (isset($context['first_message']) && $message['id'] != $context['first_message'])
		echo '
			<a id="msg', $message['id'], '"></a>', $message['first_new'] ? '<a id="new"></a>' : '';
	
	// Show information about the poster of this message.
	echo '<div itemscope="itemscope" itemtype="http://data-vocabulary.org/Person" class="keyinfo" style="margin-left:0px;padding:3px 0 3px 10px;">
		<div class="floatright horizontal_userblock" style="text-align:center;">
			<h4>', $message['member']['link'], '</h4>
			<div class="smalltext" id="msg_', $message['id'], '_extra_info">';

	// Show the member's primary group (like 'Administrator') if they have one.
	if (!empty($message['member']['group']))
		echo '
			<div class="membergroup">', $message['member']['group'], '</div>';
	else
		echo '';
		
	if ($message['member'])
	{
		// Show avatars, images, etc.?
		if (!empty($settings['show_user_images']) && empty($options['show_no_avatars'])) {
			if(!empty($message['member']['avatar']['image']))
				echo '
							<div class="avatar floatleft">
								<a href="', $scripturl, '?action=profile;u=', $message['member']['id'], '">
									', $message['member']['avatar']['image'], '
								</a>
							</div>';
			else
				echo '
							<div class="avatar">
								<a href="', $scripturl, '?action=profile;u=', $message['member']['id'], '">
									<img src="',$settings['images_url'],'/unknown.png" alt="avatar" />
								</a>
							</div>';
		}
	}
	// Otherwise, show the guest's email.
	elseif (!empty($message['member']['email']) && in_array($message['member']['show_email'], array('yes', 'yes_permission_override', 'no_through_forum')))
		echo '
							<li class="email"><a href="', $scripturl, '?action=emailuser;sa=email;msg=', $message['id'], '" rel="nofollow">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/email_sm.gif" alt="' . $txt['email'] . '" title="' . $txt['email'] . '" />' : $txt['email']), '</a></li>';

	
	// Show the member's custom title, if they have one.
	if (!empty($message['member']['title']))
		echo '
			<li class="title">', $message['member']['title'], '</li>';

	echo '</div>
		</div>';
				echo '<div><div class="messageicon">
					  <img src="', $message['icon_url'] . '" alt="" />
					  </div>
					  <h5 style="display:inline;" id="subject_', $message['id'], '">
					  <a href="', $message['href'], '" rel="nofollow">', $message['subject'], '</a>
					  </h5>	  
					  <span class="smalltext">&nbsp;',$message['time'], '</span>						  
					  </div>
					  <span class="smalltext floatright">', !empty($message['counter']) ? $txt['reply_noun'] . ' #' . $message['counter'] : '','</span>
					  <div id="msg_', $message['id'], '_quick_mod"></div>';

	// Done with the information about the poster... on to the post itself.
	echo '</div>';						

	// Ignoring this user? Hide the post.
	if ($ignoring)
		echo '
				<div id="msg_', $message['id'], '_ignored_prompt">
					', $txt['ignoring_user'], '
					<a href="#" id="msg_', $message['id'], '_ignored_link" style="display: none;">', $txt['show_ignore_user_post'], '</a>
				</div>';

	// Show the post itself, finally!
	echo '
						<div class="post clear_left" style="padding:10px 20px;" id="msg_', $message['id'], '">';

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
						</div>';

	echo '
					<div class="moderatorbar" style="margin-left:10px;">';

	echo '
		</div>';
	if(isset($message['likes_count']) && ($message['likes_count'] > 0 || !empty($message['likelink']))) 
		echo '<div class="likebar blue_container norounded">
			<div class="floatright">',$message['likelink'],'</div>
			<span id="likers_msg_',$message['id'],'">',$message['likers'],'</span>
			<div class="clear"></div></div>';
					
	echo '<div class="post_bottom">
			<div style="display:inline;">';
			// Show online and offline buttons?
				
			echo '<span class="modified" id="modified_', $message['id'], '">';
			// Show "� Last Edit: Time by Person �" if this post was edited.
			if ($settings['show_modify'] && !empty($message['modified']['name']))
				echo '
					<em>', $txt['last_edit'], ': ', $message['modified']['time'], ' ', $txt['by'], ' ', $message['modified']['name'], '</em>';

				echo '
				</span>';
			echo '</div>
				<div class="reportlinks">
				<ul class="floatright reset smalltext quickbuttons">';

	// Okay, are you at least logged in?  Then we can show something about why IPs are logged...
	//elseif (!$context['user']['is_guest'])
	//	echo '
	//						<a href="', $scripturl, '?action=helpadmin;help=see_member_ip" onclick="return reqWin(this.href);" class="help">', $txt['logged'], '</a>';
	echo '
						</div><div class="clear"></div></div>';


	echo "</div>";
}
?>
