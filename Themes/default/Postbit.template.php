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
function template_postbit_normal(&$message)
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $topic;
	
	if ($message['is_ignored'])
		echo '
		<div onclick="$(\'div.post_wrapper[data-mid=',$message['id'],']\').show();return(false);" class="orange_container ignoringpost mediummargin">
			', $txt['ignoring_user'], '&nbsp;
			', $txt['show_ignore_user_post'], '
		</div>';

	echo '
	<div id="msg',$message['id'], '" class="post_wrapper',($message['is_ignored'] ? ' ignored' : ''),'" data-mid="',$message['id'], '">';

	if ($message['id'] != $context['first_message'])
		echo $message['first_new'] ? '<a id="new"></a>' : '';

	echo '
	<div class="keyinfo std">
	 <div class="messageicon">
	  <img src="', $message['icon_url'] . '" alt=""', $message['can_modify'] ? ' class="iconrequest" id="micon_' . $message['id'] . '"' : '', ' />
	 </div>
	 <h5 style="display:inline;" id="subject_', $message['id'], '">
	  ', $message['subject'], '
	 </h5>
	 <span class="tinytext ',($message['new'] ? 'permalink_new' : 'permalink_old'),'"><a onclick="getIntralink($(this),',$message['id'],');return(false);" href="', $message['permahref'], '" rel="nofollow">',$message['permalink'],'</a>',($context['use_share'] ? '&nbsp;&nbsp;<span style="cursor:pointer" onclick="sharePost($(this));">Share</span>' : ''),'</span>
	 <span class="tinytext">&nbsp;',$message['time'], '</span>
	 <div id="msg_', $message['id'], '_quick_mod"></div>
    </div>
	<div class="clear"></div>';

	// Show information about the poster of this message.
	echo '
	<div itemscope="itemscope" itemtype="http://data-vocabulary.org/Person" class="poster std">
	<h4>', $message['member']['link'], '</h4>
	<ul class="reset tinytext" id="msg_', $message['id'], '_extra_info">';

	// Don't show these things for guests.
	if (!$message['member']['is_guest'])
	{
		// Show avatars, images, etc.?
		if (!empty($settings['show_user_images']) && empty($options['show_no_avatars'])) {
			if(!empty($message['member']['avatar']['image']))
				echo '
		<li class="avatar">
		<a href="', $scripturl, '?action=profile;u=', $message['member']['id'], '">
		', $message['member']['avatar']['image'], '
		</a>
		</li>';
			else
				echo '
		<li class="avatar">
			<a href="', $scripturl, '?action=profile;u=', $message['member']['id'], '">
			<img src="',$settings['images_url'],'/unknown.png" alt="avatar" />
			</a>
		</li>';
		}
		echo '
		<li class="membergroup">', $message['member']['group_stars'], '</li>';
		//if (!empty($message['member']['post_group']))
		//	echo '
		//<li class="membergroup"><span style="color:',$message['member']['post_group_color'], ';">',$message['member']['post_group'], '</span></li>';
		// Show the member's custom title, if they have one.
		if (!empty($message['member']['title']))
			echo '
		<li class="title">', $message['member']['title'], '</li>';
		// Show how many posts they have made.

		// Is karma display enabled?  Total or +/-?
		if ($modSettings['karmaMode'] == '1')
			echo '
		<li class="karma">', $modSettings['karmaLabel'], ' ', $message['member']['karma']['good'] - $message['member']['karma']['bad'], '</li>';
		elseif ($modSettings['karmaMode'] == '2')
			echo '
		<li class="karma">', $modSettings['karmaLabel'], ' +', $message['member']['karma']['good'], '/-', $message['member']['karma']['bad'], '</li>';

		// Is this user allowed to modify this member's karma?
		if ($message['member']['karma']['allow'])
			echo '
		<li class="karma_allow">
		<a href="', $scripturl, '?action=modifykarma;sa=applaud;uid=', $message['member']['id'], ';topic=', $context['current_topic'], '.' . $context['start'], ';m=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $modSettings['karmaApplaudLabel'], '</a>
		<a href="', $scripturl, '?action=modifykarma;sa=smite;uid=', $message['member']['id'], ';topic=', $context['current_topic'], '.', $context['start'], ';m=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $modSettings['karmaSmiteLabel'], '</a>
		</li>';

		// Show their personal text?
		if ($message['member']['blurb'] != '')
			echo '
		<li class="blurb">', $message['member']['blurb'], '</li>';

		// Any custom fields to show as icons?
		if (!empty($message['member']['custom_fields']))
		{
			$shown = false;
			foreach ($message['member']['custom_fields'] as $custom)
			{
				if ($custom['placement'] != 1 || empty($custom['value']))
					continue;
				if (empty($shown))
				{
					$shown = true;
					echo '
				<li class="im_icons">
					<ul>';
				}
				echo '
						<li>', $custom['value'], '</li>';
			}
			if ($shown)
				echo '
					</ul>
				</li>';
		}

		// Any custom fields for standard placement?
		if (!empty($message['member']['custom_fields']))
		{
			foreach ($message['member']['custom_fields'] as $custom)
				if (empty($custom['placement']) || empty($custom['value']))
					echo '
		<li class="custom">', $custom['title'], ': ', $custom['value'], '</li>';
		}

		// Are we showing the warning status?
		if ($message['member']['can_see_warning'])
			echo '
		<li class="warning">', $context['can_issue_warning'] ? '<a href="' . $scripturl . '?action=profile;area=issuewarning;u=' . $message['member']['id'] . '">' : '', '<img src="', $settings['images_url'], '/warning_', $message['member']['warning_status'], '.gif" alt="', $txt['user_warn_' . $message['member']['warning_status']], '" />', $context['can_issue_warning'] ? '</a>' : '', '<span class="warn_', $message['member']['warning_status'], '">', $txt['warn_' . $message['member']['warning_status']], '</span></li>';

		if (!empty($modSettings['onlineEnable']) && !$message['member']['is_guest'] && $message['member']['online']['is_online'])
			echo '
			<li><br>', $context['can_send_pm'] ? '<a href="' . $message['member']['online']['href'] . '">' : '', $message['member']['online']['text'], $context['can_send_pm'] ? '</a>' : '', '</li>';
	}
	// Otherwise, show the guest's email.
	elseif (!empty($message['member']['email']) && in_array($message['member']['show_email'], array('yes', 'yes_permission_override', 'no_through_forum')))
		echo '
		<li class="email"><a href="', $scripturl, '?action=emailuser;sa=email;msg=', $message['id'], '" rel="nofollow">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/email_sm.gif" alt="' . $txt['email'] . '" title="' . $txt['email'] . '" />' : $txt['email']), '</a></li>';

	// Done with the information about the poster... on to the post itself.
	echo '
		</ul>';
	echo $message['template_hook']['poster_details'],'
		</div>
		<div class="post_content std">
		<div class="post" id="msg_', $message['id'], '">';

	echo '
		<article>
			', $message['body'],'
		</article>
		</div>';
						
	// Assuming there are attachments...
	if (!empty($message['attachment']))
	{
		echo '
		<div id="msg_', $message['id'], '_footer" class="attachments smalltext">
		<ol class="post_attachments">';

		$last_approved_state = 1;
		foreach ($message['attachment'] as $attachment)
		{
			echo '
			<li>';
			// Show a special box for unapproved attachments...
			if ($attachment['is_approved'] != $last_approved_state)
			{
				$last_approved_state = 0;
				echo '
			<fieldset>
				<legend>', $txt['attach_awaiting_approve'];

				if ($context['can_approve'])
					echo '
					&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=all;mid=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve_all'], '</a>]';

				echo '
				</legend>';
			}

			if ($attachment['is_image'])
			{
				if ($attachment['thumbnail']['has_thumb'])
					//echo '<a href="', $attachment['href'], ';image" id="link_', $attachment['id'], '" onclick="', $attachment['thumbnail']['javascript'], '"><img src="', $attachment['thumbnail']['href'], '" alt="" id="thumb_', $attachment['id'], '" /></a><br />';
							echo '<a rel="prettyPhoto[gallery]" href="', $attachment['href'], ';image" id="link_', $attachment['id'], '" class="attach_thumb"><img src="', $attachment['thumbnail']['href'], '" alt="" id="thumb_', $attachment['id'], '" /></a>';
				else
					echo '
				<img src="' . $attachment['href'] . ';image" alt="" width="' . $attachment['width'] . '" height="' . $attachment['height'] . '"/>';
			}
			echo '
				<a href="' . $attachment['href'] . '">' . $attachment['name'] . '</a><br />';

			if (!$attachment['is_approved'] && $context['can_approve'])
				echo '
				[<a href="', $scripturl, '?action=attachapprove;sa=approve;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve'], '</a>]&nbsp;|&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=reject;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['delete'], '</a>] ';
			echo '
									', $attachment['size'], ($attachment['is_image'] ? ', ' . $attachment['real_width'] . 'x' . $attachment['real_height'] . '<br />' . $txt['attach_viewed'] : '<br />' . $txt['attach_downloaded']) . ' ' . $attachment['downloads'] . ' ' . $txt['attach_times'] . '.<br />
									
				</li>
									';
		}

		// If we had unapproved attachments clean up.
		if ($last_approved_state == 0)
			echo '
			</fieldset>';

		echo '
			</ol>
			</div>';
	}

	echo '
		<div class="moderatorbar">';
				
	// Are there any custom profile fields for above the signature?
	if (!empty($message['member']['custom_fields']))
	{
		$shown = false;
		foreach ($message['member']['custom_fields'] as $custom)
		{
			if ($custom['placement'] != 2 || empty($custom['value']))
				continue;
			if (empty($shown))
			{
				$shown = true;
				echo '
		<div class="custom_fields_above_signature">
		<ul class="reset nolist">';
			}
			echo '
			<li>', $custom['value'], '</li>';
		}
		if ($shown)
			echo '
		</ul>
		</div>';
	}
	echo $message['template_hook']['before_sig'];
	// Show the member's signature?
	if (!empty($message['member']['signature']) && empty($options['show_no_signatures']) && $context['signature_enabled'])
		echo '
		<div class="signature" id="msg_', $message['id'], '_signature">', $message['member']['signature'], '</div>';

	echo $message['template_hook']['after_sig'];
	if($message['likes_count'] > 0 || !empty($message['likelink']))
		echo '
	<div class="likebar">
	 <div class="floatright">',$message['likelink'],'</div>
	 <span id="likers_msg_',$message['id'],'">',$message['likers'],'</span>
	 <div class="clear_right"></div>
	</div>';
	if ($settings['show_modify'] && !empty($message['modified']['name']))
		echo '
		<div class="orange_container norounded smallpadding tinytext" id="modified_', $message['id'], '">',
		$txt['last_edit'], ': ', $message['modified']['time'], ' ', $txt['by'], ' ', $message['modified']['name'],'
		</div>';
	echo '
		</div>
		<div class="clear_left"></div>
		</div>';
	echo '
		<div class="post_bottom',$message['mq_marked'] ? ' mq' : '','">
		<div class="reportlinks lefttext">';
	template_postbit_quickbuttons($message);
	// Maybe they want to report this post to the moderator(s)?
	// Okay, are you at least logged in?  Then we can show something about why IPs are logged...
	//elseif (!$context['user']['is_guest'])
	//	echo '
	//						<a href="', $scripturl, '?action=helpadmin;help=see_member_ip" onclick="return reqWin(this.href);" class="help">', $txt['logged'], '</a>';
	echo '
		</div>
		</div>
		</div>
		',$message['template_hook']['postbit_below'];
}

function template_postbit_lean(&$message)
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $topic;
	
	if ($message['is_ignored'])
		echo '
		<div onclick="$(\'div.post_wrapper[data-mid=',$message['id'],']\').show();return(false);" class="orange_container ignoringpost mediummargin">
			', $txt['ignoring_user'], '&nbsp;
			', $txt['show_ignore_user_post'], '
		</div>';

	echo '
	<div id="msg',$message['id'], '" class="post_wrapper',($message['is_ignored'] ? ' ignored' : ''),'" data-mid="',$message['id'], '">';
	if ($message['id'] != $context['first_message'])
		echo '
			<a id="msg', $message['id'], '"></a>', $message['first_new'] ? '<a id="new"></a>' : '';
	
	// Show information about the poster of this message.
	echo '
	<div class="keyinfo">';
				echo '<div>
					  <div class="messageicon">
					  <img src="', $message['icon_url'] . '" alt=""', $message['can_modify'] ? ' id="msg_icon_' . $message['id'] . '"' : '', ' />
					  </div>
					  <h5 style="display:inline;" id="subject_', $message['id'], '">
					  ',$message['subject'],'
					  </h5>	  
					  <span class="smalltext">&nbsp;',$message['time'], '</span>						  
					  <span class="',($message['new'] ? 'permalink_new' : 'permalink_old'),'"><a onclick="getIntralink($(this),',$message['id'],');return(false);" href="', $message['permahref'], '" rel="nofollow">',$message['permalink'],'</a>',($context['use_share'] ? '&nbsp;&nbsp;<span style="cursor:pointer;" onclick="sharePost($(this));">Share</span>' : ''),'</span>
					  </div>
					  <div id="msg_', $message['id'], '_quick_mod"></div>';

	// Done with the information about the poster... on to the post itself.
	echo '
	</div>
	<div class="post_content lean">
	<div class="floatright horizontal_userblock" style="text-align:center;">
			<h4>', $message['member']['link'], '</h4>
			<div class="smalltext" id="msg_', $message['id'], '_extra_info">';

	// Show the member's primary group (like 'Administrator') if they have one.
	if (!empty($message['member']['group']))
		echo '
			<div class="membergroup">', $message['member']['group'], '</div>';
	else
		echo '';

	if (!$message['member']['is_guest'])
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

	echo '<div class="clear"></div>';
	// Show the member's custom title, if they have one.
	if (!empty($message['member']['title']))
		echo '
			<li class="title">', $message['member']['title'], '</li>';

	if (!empty($modSettings['onlineEnable']) && !$message['member']['is_guest'])
		echo '<div class="centertext">', $context['can_send_pm'] ? '<a href="' . $message['member']['online']['href'] . '">' : '', $message['member']['online']['text'], $context['can_send_pm'] ? '</a>' : '', '</div>';

	echo '</div>
		</div>';

	// Show the post itself, finally!
	echo '
		<div class="post clear_left" style="padding:10px 20px;" id="msg_', $message['id'], '">';

	echo '
		<article>
		', $message['body'], '
		</article>
		</div>';

	// Assuming there are attachments...
	if (!empty($message['attachment']))
	{
		echo '
						<div id="msg_', $message['id'], '_footer" class="attachments smalltext">
							<ol class="post_attachments">';

		$last_approved_state = 1;
		foreach ($message['attachment'] as $attachment)
		{
			echo '<li>';
			// Show a special box for unapproved attachments...
			if ($attachment['is_approved'] != $last_approved_state)
			{
				$last_approved_state = 0;
				echo '
								<fieldset>
									<legend>', $txt['attach_awaiting_approve'];

				if ($context['can_approve'])
					echo '&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=all;mid=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve_all'], '</a>]';

				echo '</legend>';
			}

			if ($attachment['is_image'])
			{
				if ($attachment['thumbnail']['has_thumb'])
					//echo '<a href="', $attachment['href'], ';image" id="link_', $attachment['id'], '" onclick="', $attachment['thumbnail']['javascript'], '"><img src="', $attachment['thumbnail']['href'], '" alt="" id="thumb_', $attachment['id'], '" /></a><br />';
							echo '<a rel="prettyPhoto[gallery]" href="', $attachment['href'], ';image" id="link_', $attachment['id'], '" class="attach_thumb"><img src="', $attachment['thumbnail']['href'], '" alt="" id="thumb_', $attachment['id'], '" /></a>';
				else
					echo '
									<img src="' . $attachment['href'] . ';image" alt="" width="' . $attachment['width'] . '" height="' . $attachment['height'] . '"/>';
			}
			echo '
									<a href="' . $attachment['href'] . '">' . $attachment['name'] . '</a><br />';

			if (!$attachment['is_approved'] && $context['can_approve'])
				echo '
									[<a href="', $scripturl, '?action=attachapprove;sa=approve;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve'], '</a>]&nbsp;|&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=reject;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['delete'], '</a>] ';
			echo '
									', $attachment['size'], ($attachment['is_image'] ? ', ' . $attachment['real_width'] . 'x' . $attachment['real_height'] . '<br />' . $txt['attach_viewed'] : '<br />' . $txt['attach_downloaded']) . ' ' . $attachment['downloads'] . ' ' . $txt['attach_times'] . '.<br />
									
									</li>
									';
		}

		// If we had unapproved attachments clean up.
		if ($last_approved_state == 0)
			echo '
								</fieldset>';

		echo '
							</ol>
						</div>';
	}

	echo '
		<div class="moderatorbar" style="margin-left:10px;">';
	// Are there any custom profile fields for above the signature?
	if (!empty($message['member']['custom_fields']))
	{
		$shown = false;
		foreach ($message['member']['custom_fields'] as $custom)
		{
			if ($custom['placement'] != 2 || empty($custom['value']))
				continue;
			if (empty($shown))
			{
				$shown = true;
				echo '
						<div class="custom_fields_above_signature">
							<ul class="reset nolist">';
			}
			echo '
								<li>', $custom['value'], '</li>';
		}
		if ($shown)
			echo '
							</ul>
						</div>';
	}

	// Show the member's signature?
	if (!empty($message['member']['signature']) && empty($options['show_no_signatures']) && $context['signature_enabled'])
		echo '
						<div class="signature" id="msg_', $message['id'], '_signature">', $message['member']['signature'], '</div>';

	if($message['likes_count'] > 0 || !empty($message['likelink']))
		echo '<div class="likebar">
			<div class="floatright">',$message['likelink'],'</div>
			<span id="likers_msg_',$message['id'],'">',$message['likers'],'</span>
			<div class="clear"></div>
			</div>';

	echo '
		<span class="modified tinytext" id="modified_', $message['id'], '">';
	if ($settings['show_modify'] && !empty($message['modified']['name']))
		echo '
			<em>', $txt['last_edit'], ': ', $message['modified']['time'], ' ', $txt['by'], ' ', $message['modified']['name'], '</em>';

		echo '
		</span>
		</div>
		</div>';
	echo '<div class="post_bottom">';
			echo '
				<div class="reportlinks">';
	template_postbit_quickbuttons($message);
	echo '
		</div><div class="clear"></div></div>';
	echo '
	</div>';
}

function template_postbit_comment(&$message)
{
	global $context, $settings, $options, $txt, $scripturl, $topic;

	if ($message['is_ignored'])
		echo '
		<div onclick="$(\'div.post_wrapper[data-mid=',$message['id'],']\').show();return(false);" class="orange_container ignoringpost mediummargin">
			', $txt['ignoring_user'], '&nbsp;
			', $txt['show_ignore_user_post'], '
		</div>';

	echo '
	<div id="msg',$message['id'], '" class="post_wrapper commentstyle',(!$message['approved'] ? ' approval' : ''),($message['is_ignored'] ? ' ignored' : ''),'" data-mid="',$message['id'], '">';

	if ($message['id'] != $context['first_message'])
		echo
	 	$message['first_new'] ? '<a id="new"></a>' : '';

	// Show information about the poster of this message.
	echo '
	<div class="keyinfo commentstyle">
	<div class="floatleft " style="max-width:65px;">
	<ul class="reset smalltext" id="msg_', $message['id'], '_extra_info">';
	// Done with the information about the poster... on to the post itself.
	if(!empty($message['member']['avatar']['image']))
		echo '
		<li class="medium_avatar">
		<a href="', $scripturl, '?action=profile;u=', $message['member']['id'], '">
		', $message['member']['avatar']['image'], '
		</a>
		</li>';
	else
		echo '
		<li class="medium_avatar">
			<a href="', $scripturl, '?action=profile;u=', $message['member']['id'], '">
			<img src="',$settings['images_url'],'/unknown.png" alt="avatar" />
			</a>
		</li>';
	echo '
	</ul>
	</div>
	<div>',
	 $message['member']['link'],' said<span class="smalltext">&nbsp;',$message['time'], '</span>&nbsp;-&nbsp;',$message['subject'],'
	 <span class="',($message['new'] ? 'permalink_new' : 'permalink_old'),'"><a onclick="getIntralink($(this),',$message['id'],');return(false);" href="', $message['permahref'], '" rel="nofollow">',$message['permalink'],'</a>',($context['use_share'] ? '&nbsp;&nbsp;<span style="cursor:pointer" onclick="sharePost($(this));">Share</span>' : ''),'</span>
	 <div id="msg_', $message['id'], '_quick_mod"></div>
	</div>
	</div>
	<div class="post_content commentstyle">';

	// Show the post itself, finally!
	if (!$message['approved'] && $message['member']['id'] != 0)
		echo '
		<div class="red_container mediumpadding mediummargin">
			', $txt['post_awaiting_approval'], '&nbsp;&nbsp;<a onclick="$(\'#msg_',$message['id'],'\').show();return(false);" href="#!">Show me the message</a>
		</div>';
	echo '
		<div class="post" id="msg_', $message['id'], '"', ($message['approved'] ? '' : ' style="display:none;"'), '>';

	echo '
		<article>
			', $message['body'],'
		</article>
		</div>';

	// Assuming there are attachments...
	if (!empty($message['attachment']))
	{
		echo '
		<div id="msg_', $message['id'], '_footer" class="attachments smalltext">
		<ol class="post_attachments">';

		$last_approved_state = 1;
		foreach ($message['attachment'] as $attachment)
		{
			echo '
			<li>';
			// Show a special box for unapproved attachments...
			if ($attachment['is_approved'] != $last_approved_state)
			{
				$last_approved_state = 0;
				echo '
			<fieldset>
				<legend>', $txt['attach_awaiting_approve'];

				if ($context['can_approve'])
					echo '
					&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=all;mid=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve_all'], '</a>]';

				echo '
				</legend>';
			}

			if ($attachment['is_image'])
			{
				if ($attachment['thumbnail']['has_thumb'])
					//echo '<a href="', $attachment['href'], ';image" id="link_', $attachment['id'], '" onclick="', $attachment['thumbnail']['javascript'], '"><img src="', $attachment['thumbnail']['href'], '" alt="" id="thumb_', $attachment['id'], '" /></a><br />';
							echo '<a rel="prettyPhoto[gallery]" href="', $attachment['href'], ';image" id="link_', $attachment['id'], '" class="attach_thumb"><img src="', $attachment['thumbnail']['href'], '" alt="" id="thumb_', $attachment['id'], '" /></a>';
				else
					echo '
				<img src="' . $attachment['href'] . ';image" alt="" width="' . $attachment['width'] . '" height="' . $attachment['height'] . '"/>';
			}
			echo '
				<a href="' . $attachment['href'] . '">' . $attachment['name'] . '</a><br />';

			if (!$attachment['is_approved'] && $context['can_approve'])
				echo '
				[<a href="', $scripturl, '?action=attachapprove;sa=approve;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve'], '</a>]&nbsp;|&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=reject;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['delete'], '</a>] ';
			echo '
									', $attachment['size'], ($attachment['is_image'] ? ', ' . $attachment['real_width'] . 'x' . $attachment['real_height'] . '<br />' . $txt['attach_viewed'] : '<br />' . $txt['attach_downloaded']) . ' ' . $attachment['downloads'] . ' ' . $txt['attach_times'] . '.<br />

				</li>
									';
		}

		// If we had unapproved attachments clean up.
		if ($last_approved_state == 0)
			echo '
			</fieldset>';

		echo '
			</ol>
			</div>';
	}

	echo '
		<div class="moderatorbar">';

		if($message['likes_count'] > 0 || !empty($message['likelink']))
						echo '
		<div class="likebar">
		  <div class="floatright">',$message['likelink'],'</div>
		  <span id="likers_msg_',$message['id'],'">',$message['likers'],'</span>
		  <div class="clear">
		</div>
		</div>';
	echo '
		<span class="tinytext" id="modified_', $message['id'], '">';
	if ($settings['show_modify'] && !empty($message['modified']['name']))
		echo '
		<em>', $txt['last_edit'], ': ', $message['modified']['time'], ' ', $txt['by'], ' ', $message['modified']['name'], '</em>';
	echo '
		</span>
		</div>
		</div>
		<div class="post_bottom">
		<div class="reportlinks">';

	template_postbit_quickbuttons($message);
	echo '
		</div><div class="clear"></div>
		</div></div>';
}

function template_postbit_clean(&$message)
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $topic;

	if ($message['is_ignored'])
		echo '
		<div onclick="$(\'div.post_wrapper[data-mid=',$message['id'],']\').show();return(false);" class="orange_container ignoringpost mediummargin">
			', $txt['ignoring_user'], '&nbsp;
			', $txt['show_ignore_user_post'], '
		</div>';

	echo '
		<div data-mid="',$message['id'], '">';

	if ($message['id'] != $context['first_message'])
		echo '
			<a id="msg', $message['id'], '"></a>', $message['first_new'] ? '<a id="new"></a>' : '';

	// Show information about the poster of this message.
	echo '
		<div class="keyinfo clean">
		  <div>
	 	  <span class="',($message['new'] ? 'permalink_new' : 'permalink_old'),'"><a onclick="getIntralink($(this),',$message['id'],');return(false);" href="', $message['permahref'], '" rel="nofollow">',$message['permalink'],'</a>',($context['use_share'] ? '&nbsp;&nbsp;<span style="cursor:pointer" onclick="sharePost($(this));">Share</span>' : ''),'</span>
		  Posted by: ',$message['member']['link'],',&nbsp;
		  <span class="smalltext">',$message['time'], '</span>
		  </div>
		  <span style="display:none;" id="subject_', $message['id'], '">
		 ', $message['subject'], '
		  </span>
		</div>
		<div id="msg_', $message['id'], '_quick_mod"></div>';

	// Show the post itself, finally!
	echo '
						<div class="post clear_left" style="margin:0;padding:0;" id="msg_', $message['id'], '">';

	if (!$message['approved'] && $message['member']['id'] != 0 && $message['member']['id'] == $context['user']['id'])
		echo '
							<div class="approve_post">
								', $txt['post_awaiting_approval'], '
							</div>';
	echo '
							<article>
							', $message['body'], '
							</article>
						</div>';

	// Assuming there are attachments...
	if (!empty($message['attachment']))
	{
		echo '
						<div id="msg_', $message['id'], '_footer" class="attachments smalltext">
							<ol class="post_attachments">';

		$last_approved_state = 1;
		foreach ($message['attachment'] as $attachment)
		{
			echo '<li>';
			// Show a special box for unapproved attachments...
			if ($attachment['is_approved'] != $last_approved_state)
			{
				$last_approved_state = 0;
				echo '
								<fieldset>
									<legend>', $txt['attach_awaiting_approve'];

				if ($context['can_approve'])
					echo '&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=all;mid=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve_all'], '</a>]';

				echo '</legend>';
			}

			if ($attachment['is_image'])
			{
				if ($attachment['thumbnail']['has_thumb'])
					//echo '<a href="', $attachment['href'], ';image" id="link_', $attachment['id'], '" onclick="', $attachment['thumbnail']['javascript'], '"><img src="', $attachment['thumbnail']['href'], '" alt="" id="thumb_', $attachment['id'], '" /></a><br />';
							echo '<a rel="prettyPhoto[gallery]" href="', $attachment['href'], ';image" id="link_', $attachment['id'], '" class="attach_thumb"><img src="', $attachment['thumbnail']['href'], '" alt="" id="thumb_', $attachment['id'], '" /></a>';
				else
					echo '
									<img src="' . $attachment['href'] . ';image" alt="" width="' . $attachment['width'] . '" height="' . $attachment['height'] . '"/>';
			}
			echo '
									<a href="' . $attachment['href'] . '">' . $attachment['name'] . '</a><br />';

			if (!$attachment['is_approved'] && $context['can_approve'])
				echo '
									[<a href="', $scripturl, '?action=attachapprove;sa=approve;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve'], '</a>]&nbsp;|&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=reject;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['delete'], '</a>] ';
			echo '
									', $attachment['size'], ($attachment['is_image'] ? ', ' . $attachment['real_width'] . 'x' . $attachment['real_height'] . '<br />' . $txt['attach_viewed'] : '<br />' . $txt['attach_downloaded']) . ' ' . $attachment['downloads'] . ' ' . $txt['attach_times'] . '.<br />

									</li>
									';
		}

		// If we had unapproved attachments clean up.
		if ($last_approved_state == 0)
			echo '
								</fieldset>';

		echo '
							</ol>
						</div>';
	}

	echo '
					<div class="moderatorbar" style="margin-left:10px;">';
	echo '
		</div>';
	if($message['likes_count'] > 0 || !empty($message['likelink']))
		echo '<div class="likebar">
			<div class="floatright">',$message['likelink'],'</div>
			<span id="likers_msg_',$message['id'],'">',$message['likers'],'</span>
			<div class="clear"></div></div>';

	echo '<div class="post_bottom" style="background-color:transparent;">
			<div style="display:inline;">';
			// Show online and offline buttons?

			echo '<span class="modified" id="modified_', $message['id'], '">';
			// Show Last Edit: Time by Person if this post was edited.
			if ($settings['show_modify'] && !empty($message['modified']['name']))
				echo '
					<em>', $txt['last_edit'], ': ', $message['modified']['time'], ' ', $txt['by'], ' ', $message['modified']['name'], '</em>';

				echo '
				</span>';
			echo '</div>
				<div class="reportlinks">
				<ul class="floatright plainbuttonlist">';

	// Maybe we can approve it, maybe we should?
	if ($message['can_approve'])
		echo '
								<li><a rel="nofollow" href="', $scripturl, '?action=moderate;area=postmod;sa=approve;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve'], '</a></li>';

	// Can the user modify the contents of this post?
	if ($message['can_modify'])
		echo '
			<li><a rel="nofollow" onclick="oQuickModify.modifyMsg(\'', $message['id'], '\');return(false);" href="', $scripturl, '?action=post;msg=', $message['id'], ';topic=', $context['current_topic'], '.', $context['start'], '">', $txt['modify'], '</a></li>';

	// How about... even... remove it entirely?!
	if ($message['can_remove'])
		echo '
			<li><a rel="nofollow" href="', $scripturl, '?action=deletemsg;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '" onclick="return Eos_Confirm(\'\', \'', $txt['remove_message'], '?\', $(this).attr(\'href\'));">', $txt['remove'], '</a></li>';

	// What about splitting it off the rest of the topic?
	if ($context['can_split'] && !empty($context['real_num_replies']))
		echo '
								<li><a rel="nofollow" href="', $scripturl, '?action=splittopics;topic=', $context['current_topic'], '.0;at=', $message['id'], '">', $txt['split'], '</a></li>';

	// Can we restore topics?
	if ($context['can_restore_msg'])
		echo '
								<li><a rel="nofollow" href="', $scripturl, '?action=restoretopic;msgs=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['restore_message'], '</a></li>';

	// Show a checkbox for quick moderation?
	if (!empty($options['display_quick_mod']) && $message['can_remove'])
		echo '
								<li class="inline_mod_check" style="display: none;" id="in_topic_mod_check_', $message['id'], '"></li>';

		echo '
						</ul>

						';

	echo '
						</div><div class="clear"></div></div>';


	echo "</div>";
}
function template_postbit_quickbuttons(&$message)
{
	global $context, $scripturl, $txt, $settings, $options;
	$imgsrc = $settings['images_url'].'/clipsrc.png';

	echo '
	<ul class="floatright plainbuttonlist">';

	if ($message['can_approve'])
		echo '
		<li><a href="', $scripturl, '?action=moderate;area=postmod;sa=approve;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '"><div class="csrcwrapper16px"><img class="clipsrc approve" src="',$imgsrc,'" alt="',$txt['approve'],'" title="',$txt['approve'],'" /></div></a></li>';
	if ($context['can_quote'])
		echo '
	<li><a rel="nofollow" onclick="return oQuickReply.quote(',$message['id'],');" href="', $scripturl, '?action=post;quote=', $message['id'], ';topic=', $context['current_topic'], '.', $context['start'], ';last_msg=', $context['topic_last_message'], '"><div class="csrcwrapper16px"><img class="clipsrc reply" src="',$imgsrc,'" alt="',$txt['quote'],'" title="',$txt['quote'],'" /></div></a></li>
	<li id="mquote_' . $message['id'] . '"><a rel="nofollow" href="javascript:void(0);" onclick="return oQuickReply.addForMultiQuote(' . $context['current_topic'], ', ', $message['id'],');"><div class="csrcwrapper16px"><img class="clipsrc mquote_add" src="',$imgsrc,'" alt="',$txt['add_mq'],'" title="',$txt['add_mq'],'" /></div></a></li>';

	// Can the user modify the contents of this post?
	if ($message['can_modify'])
		echo '
	<li><a rel="nofollow" onclick="oQuickModify.modifyMsg(\'', $message['id'], '\');return(false);" href="', $scripturl, '?action=post;msg=', $message['id'], ';topic=', $context['current_topic'], '.', $context['start'], '"><div class="csrcwrapper16px"><img class="clipsrc modify" src="',$imgsrc,'" alt="',$txt['modify'],'" title="',$txt['modify'],'" /></div></a></li>';

	// How about... even... remove it entirely?!
	if ($message['can_remove'])
		echo '
	<li><a rel="nofollow" href="', $scripturl, '?action=deletemsg;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '" onclick="return Eos_Confirm(\'\', \'', $txt['remove_message'], '?\', $(this).attr(\'href\'));"><div class="csrcwrapper16px"><img class="clipsrc remove" src="',$imgsrc,'" alt="',$txt['remove'],'" title="',$txt['remove'],'" /></div></a></li>';

	if ($message['can_unapprove'])
		echo '
	<li class="approve_button"><a href="', $scripturl, '?action=moderate;area=postmod;sa=unapprove;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '"><div class="csrcwrapper16px"><img class="clipsrc unapprove" src="',$imgsrc,'" alt="',$txt['unapprove'],'" title="',$txt['unapprove'],'" /></div></a></li>';

	// What about splitting it off the rest of the topic?
	if ($context['can_split'] && !empty($context['real_num_replies']))
		echo '
	<li><a rel="nofollow" href="', $scripturl, '?action=splittopics;topic=', $context['current_topic'], '.0;at=', $message['id'], '"><div class="csrcwrapper16px"><img class="clipsrc split" src="',$imgsrc,'" alt="',$txt['split'],'" title="',$txt['split'],'" /></div></a></li>';

	// Can we restore topics?
	if ($context['can_restore_msg'])
		echo '
	<li><a rel="nofollow" href="', $scripturl, '?action=restoretopic;msgs=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['restore_message'], '</a></li>';

	// Show a checkbox for quick moderation?
	if (!empty($options['display_quick_mod']) && $message['can_remove'])
		echo '
	<li class="inline_mod_check" style="display: none;" id="in_topic_mod_check_', $message['id'], '"></li>';

	echo '
	</ul>';

	if ($context['can_report_moderator'])
		echo '
	<a href="',$scripturl, '?action=reporttm;topic=', $context['current_topic'], '.', $message['counter'], ';msg=', $message['id'], '"><div class="csrcwrapper16px floatleft padded"><img class="clipsrc reporttm" src="',$imgsrc,'" alt="',$txt['report'],'" title="',$txt['report'],'" /></div></a>';

	// Can we issue a warning because of this post?  Remember, we can't give guests warnings.
	if ($context['can_issue_warning'] && !$message['is_message_author'] && !$message['member']['is_guest'])
		echo '
	<a href="', $scripturl, '?action=profile;area=issuewarning;u=', $message['member']['id'], ';msg=', $message['id'], '"><div class="csrcwrapper16px floatleft padded"><img class="clipsrc warning" src="',$imgsrc,'" alt="',$txt['issue_warning'],'" title="',$txt['issue_warning'],'" /></div></a>';

	// Show the IP to this user for this post - because you can moderate?
	if ($context['can_moderate_forum'] && !empty($message['member']['ip']))
		echo '
	<a href="', $scripturl, '?action=', !empty($message['member']['is_guest']) ? 'trackip' : 'profile;area=tracking;sa=ip;u=' . $message['member']['id'], ';searchip=', $message['member']['ip'], '"><div class="csrcwrapper16px floatleft padded"><img class="clipsrc network" src="',$imgsrc,'" alt="',$message['member']['ip'],'" title="',$message['member']['ip'],'" /></div></a>';
	// Or, should we show it because this is you?
	elseif ($message['can_see_ip'])
		echo '
	<a href="', $scripturl, '?action=helpadmin;help=see_member_ip" onclick="return reqWin(this.href);" class="help">', $message['member']['ip'], '</a>';
	echo '
	<div class="clear"></div>';
}
?>
