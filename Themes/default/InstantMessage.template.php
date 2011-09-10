<?php
// Version: 1.0; InstantMessage

function template_folder()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt;

	echo '
<form action="', $scripturl, '?action=pm;sa=removemore;f=', $context['folder'], ';start=', $context['start'], '" method="post" onsubmit="if (!confirm(\'', $txt['delete_selected_confirm'], '\')) return false;">
	<table border="0" width="100%" cellspacing="0" cellpadding="3">
		<tr>
			<td valign="bottom">', theme_linktree(), '</td>
		</tr>
	</table>
	<table border="0" width="100%" cellpadding="2" cellspacing="1" class="bordercolor">
		<tr>
			<td align="right" valign="bottom" class="catbg" colspan="4" style="font-size: smaller;">', $context['show_delete'] ? '
				<a href="' . $scripturl . '?action=pm;sa=removeall;f=' . $context['folder'] . '">' . ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/im_delete.gif" alt="' . $txt['delete_all'] . '" border="0" />' : $txt['delete_all']) . '</a>' . $context['menu_separator'] : '', $context['can_send_pm'] ? ($context['from_or_to'] == 'from' ? '
				<a href="' . $scripturl . '?action=pm;f=outbox">' . ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/im_outbox.gif" alt="' . $txt[320] . '" border="0" />' : $txt[320]) . '</a>' . $context['menu_separator'] : '
				<a href="' . $scripturl . '?action=pm">' . ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/im_inbox.gif" alt="' . $txt['inbox'] . '" border="0" />' : $txt['inbox']) . '</a>' . $context['menu_separator']) : '', $context['can_send_pm'] ? '
				<a href="' . $scripturl . '?action=pm;sa=send">' . ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/im_new.gif" alt="' . $txt['new_message'] . '" border="0" />' : $txt['new_message']) . '</a>' . $context['menu_separator'] : '', '
				<a href="javascript:window.location.reload()">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/im_reload.gif" alt="' . $txt['check_new_messages'] . '" border="0" />' : $txt['check_new_messages']), '</a>
			</td>
		</tr>
		<tr class="titlebg">
			<td style="width: 32ex;"><a href="', $scripturl, '?action=pm;f=', $context['folder'], ';start=', $context['start'], ';sort=date', $context['sort_by'] == 'date' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['date'], $context['sort_by'] == 'date' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" border="0" />' : '', '</a></td>
			<td width="46%"><a href="', $scripturl, '?action=pm;f=', $context['folder'], ';start=', $context['start'], ';sort=subject', $context['sort_by'] == 'subject' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['subject'], $context['sort_by'] == 'subject' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" border="0" />' : '', '</a></td>
			<td><a href="', $scripturl, '?action=pm;f=', $context['folder'], ';start=', $context['start'], ';sort=name', $context['sort_by'] == 'name' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', ($context['from_or_to'] == 'from' ? $txt['from'] : $txt['to']), $context['sort_by'] == 'name' ? ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" border="0" />' : '', '</a></td>
			<td align="center" width="24"><input type="checkbox" onclick="invertAll(this, this.form);" class="check" /></td>
		</tr>';
	if (!$context['show_delete'])
		echo '
		<tr>
			<td class="windowbg" colspan="4">', $txt['msg_alert_none'], '</td>
		</tr>';
	$next_alternate = false;
	while ($message = $context['get_pmessage']())
	{
		echo '
		<tr class="', $message['alternate'] == 0 ? 'windowbg' : 'windowbg2', '">
			<td>', $message['time'], '</td>
			<td><a href="#', $message['id'], '">', $message['subject'], '</a></td>
			<td>', ($context['from_or_to'] == 'from' ? $message['member']['link'] : (empty($message['recipients']['to']) ? '' : implode(', ', $message['recipients']['to']))), '</td>
			<td align="center"><input type="checkbox" name="delete[]" id="deletelisting', $message['id'], '" value="', $message['id'], '" class="check" onclick="document.getElementById(\'deletedisplay', $message['id'], '\').checked = this.checked;" /></td>
		</tr>';
		$next_alternate = $message['alternate'];
	}

	echo '
		<tr>
			<td colspan="5" class="catbg" height="25">
				<table width="100%" cellpadding="2" cellspacing="0" border="0"><tr>
					<td><b>', $txt['pages'], ':</b> ', $context['page_index'], '</td>
					<td align="right"><input type="submit" value="', $txt['quickmod_delete_selected'], '" style="font-weight: normal;" /></td>
				</tr></table>
			</td>
		</tr>
	</table>
	<br />';

	if ($context['get_pmessage'](true))
	{
		echo '
		<table cellpadding="4" cellspacing="0" border="0" width="100%">
			<tr class="titlebg">
				<td width="16%">&nbsp;', $txt['author'], '</td>
				<td>', $txt['topic'], '</td>
			</tr>
		</table>
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="bordercolor">';

		while ($message = $context['get_pmessage']())
		{
			$windowcss = $message['alternate'] == 0 ? 'windowbg' : 'windowbg2';

			echo '
		<tr><td style="padding: 1px 1px 0 1px;">
			<a name="', $message['id'], '"></a>
			<table width="100%" cellpadding="3" cellspacing="0" border="0">
				<tr><td colspan="2" class="', $windowcss, '">
					<table width="100%" cellpadding="4" cellspacing="1" style="table-layout: fixed;">
						<tr>
							<td valign="top" width="16%" rowspan="2" style="overflow: hidden;">
								<b>', $message['member']['link'], '</b>
								<div class="smalltext">';
			if (isset($message['member']['title']) && $message['member']['title'] != '')
				echo '
									', $message['member']['title'], '<br />';
			if (isset($message['member']['group']) && $message['member']['group'] != '')
				echo '
									', $message['member']['group'], '<br />';

			if (!$message['member']['is_guest'])
			{
				// Show the post group if and only if they have no other group or the option is on, and they are in a post group.
				if ((empty($settings['hide_post_group']) || $message['member']['group'] == '') && $message['member']['post_group'] != '')
					echo '
									', $message['member']['post_group'], '<br />';
				echo '
									', $message['member']['group_stars'], '<br />';

				// Is karma display enabled?  Total or +/-?
				if ($modSettings['karmaMode'] == '1')
					echo '
									<br />
									', $modSettings['karmaLabel'], ' ', $message['member']['karma']['good'] - $message['member']['karma']['bad'], '<br />';
				elseif ($modSettings['karmaMode'] == '2')
					echo '
									<br />
									', $modSettings['karmaLabel'], ' +', $message['member']['karma']['good'], '/-', $message['member']['karma']['bad'], '<br />';

				// Is this user allowed to modify this member's karma?
				if ($message['member']['karma']['allow'])
					echo '
									<a href="', $scripturl, '?action=modifykarma;sa=applaud;uid=', $message['member']['id'], ';f=', $context['folder'], ';start=', $context['start'], '">', $modSettings['karmaApplaudLabel'], '</a> <a href="', $scripturl, '?action=modifykarma;sa=smite;uid=', $message['member']['id'], ';f=', $context['folder'], ';start=', $context['start'], '">', $modSettings['karmaSmiteLabel'], '</a><br />';

				// Show online and offline buttons?
				if (!empty($modSettings['onlineEnable']) && !$message['member']['is_guest'])
				echo '
									', $context['can_send_pm'] ? '<a href="' . $message['member']['online']['href'] . '" title="' . $message['member']['online']['label'] . '">' : '', $settings['use_image_buttons'] ? '<img src="' . $message['member']['online']['image_href'] . '" alt="' . $message['member']['online']['text'] . '" border="0" align="middle" />' : $message['member']['online']['text'], $context['can_send_pm'] ? '</a>' : '', $settings['use_image_buttons'] ? '<span class="smalltext"> ' . $message['member']['online']['text'] . '</span>' : '', '<br /><br />';

				// Show the member's gender icon?
				if (!empty($settings['show_gender']) && $message['member']['gender']['image'] != '')
					echo '
									', $txt['gender'], ': ', $message['member']['gender']['image'], '<br />';

				// Show how many posts they have made.
				echo '
									', $txt['member_postcount'], ': ', $message['member']['posts'], '<br />
									<br />';

				// Show avatars, images, etc.?
				if (!empty($settings['show_user_images']) && empty($options['show_no_avatars']))
					echo '
									', $message['member']['avatar']['image'], '<br />';

				// Show their personal text?
				if (!empty($settings['show_blurb']) && $message['member']['blurb'] != '')
					echo '
									', $message['member']['blurb'], '<br />
									<br />';
				echo '
									', $message['member']['icq']['link'], '
									', $message['member']['msn']['link'], '
									', $message['member']['yim']['link'], '
									', $message['member']['aim']['link'], '<br />';

				// Show the profile, website, email address, and personal message buttons.
				if ($settings['show_profile_buttons'])
				{
					echo '
									<a href="', $message['member']['href'], '">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/icons/profile_sm.gif" alt="' . $txt['view_profile'] . '" title="' . $txt['view_profile'] . '" border="0" />' : $txt['view_profile']), '</a>';
					if ($message['member']['website']['url'] != '')
						echo '
									<a href="', $message['member']['website']['url'], '" target="_blank">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/www_sm.gif" alt="' . $txt['www'] . '" title="' . $message['member']['website']['title'] . '" border="0" />' : $txt['www']), '</a>';
					if (empty($message['member']['hide_email']))
						echo '
									<a href="mailto:', $message['member']['email'], '">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/email_sm.gif" alt="' . $txt['email'] . '" title="' . $txt['email'] . '" border="0" />' : $txt['email']), '</a>';
					if (!$context['user']['is_guest'] && $context['can_send_pm'])
						echo '
									<a href="', $scripturl, '?action=pm;sa=send;u=', $message['member']['id'], '" title="', $message['member']['online']['label'], '">', $settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/im_' . ($message['member']['online']['is_online'] ? 'on' : 'off') . '.gif" alt="' . $message['member']['online']['label'] . '" border="0" />' : $message['member']['online']['label'], '</a>';
				}
			}
			else
				echo '
									<br />
									<br />
									<a href="mailto:', $message['member']['email'], '">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/email_sm.gif" alt="' . $txt['email'] . '" title="' . $txt['email'] . '" border="0" />' : $txt['email']), '</a>';
			echo '
								</div>
							</td>
							<td class="', $windowcss, '" valign="top" width="85%" height="100%">
								<table width="100%" border="0"><tr>
									<td align="left" valign="middle">
										<b>', $message['subject'], '</b>';

			// Show who the message was sent to.
			echo '
										<div class="smalltext">&#171; <b> ', $txt['sent_to'], ':</b> ';

			// People it was sent directly to....
			if (!empty($message['recipients']['to']))
				echo implode(', ', $message['recipients']['to']);
			// Otherwise, we're just going to say "some people".
			elseif ($context['folder'] != 'outbox')
				echo '(', $txt['pm_undisclosed_recipients'], ')';

			echo ' <b> ', $txt['on'], ':</b> ', $message['time'], ' &#187;</div>';

			// If we're in the outbox, show who it was sent to besides the "To:" people.
			if (!empty($message['recipients']['bcc']))
				echo '
										<div class="smalltext">&#171; <b> ', $txt['pm_bcc'], ':</b> ', implode(', ', $message['recipients']['bcc']), ' &#187;</div>';

			echo '
									</td>
									<td align="right" valign="bottom" height="20" nowrap="nowrap" style="font-size: smaller;">';

			// Show reply buttons if you have the permission to send PMs.
			if ($context['can_send_pm'])
			{
				// You can't really reply if the member is gone.
				if (!$message['member']['is_guest'])
					echo '
										<a href="', $scripturl, '?action=pm;sa=send;f=', $context['folder'], ';pmsg=', $message['id'], ';quote;u=', $context['folder'] == 'outbox' ? '' : $message['member']['id'], '">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/quote.gif" alt="' . $txt['reply_quote'] . '" border="0" />' : $txt['reply_quote']), '</a>', $context['menu_separator'], '
										<a href="', $scripturl, '?action=pm;sa=send;f=', $context['folder'], ';pmsg=', $message['id'], ';reply;u=', $message['member']['id'], '">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/im_reply.gif" alt="' . $txt['reply'] . '" border="0" />' : $txt['reply']), '</a> ', $context['menu_separator'];
				// This is for "forwarding" - even if the member is gone.
				else
					echo '
										<a href="', $scripturl, '?action=pm;sa=send;f=', $context['folder'], ';pmsg=', $message['id'], ';quote">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/quote.gif" alt="' . $txt['reply_quote'] . '" border="0" />' : $txt['reply_quote']), '</a>', $context['menu_separator'];
			}
			echo '
										<a href="', $scripturl, '?action=pm;sa=removemore;f=', $context['folder'], ';delete[0]=', $message['id'], ';start=', $context['start'], ';sesc=', $context['session_id'], '" onclick="return confirm(\'', addslashes($txt['remove_message']), '?\');">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/delete.gif" alt="' . $txt['remove_message'] . '" border="0" />' : $txt['remove_message']), '</a>
										<input type="checkbox" name="delete[]" id="deletedisplay', $message['id'], '" value="', $message['id'], '" class="check" onclick="document.getElementById(\'deletelisting', $message['id'], '\').checked = this.checked;" />
									</td>
								</tr></table>
								<hr width="100%" size="1" class="hrcolor" />
								<div style="overflow: auto; width: 100%;">', $message['body'], '</div>
							</td>
						</tr>
						<tr class="', $windowcss, '">
							<td valign="bottom" class="smalltext" width="85%">';

			// Show the member's signature?
			if (!empty($message['member']['signature']) && empty($options['show_no_signatures']))
				echo '
								<hr width="100%" size="1" class="hrcolor" />
								<div style="overflow: auto; width: 100%; padding-bottom: 3px;" class="signature">', $message['member']['signature'], '</div>';

			echo '
							</td>
						</tr>
					</table>
				</td></tr>
			</table>
		</td></tr>';
		}

		echo '
			<tr><td style="padding: 0 0 1px 0;"></td></tr>
	</table>

	<div class="tborder" style="padding: 3px; margin-top: 1ex;">
		<table class="catbg" cellpadding="3" cellspacing="0" border="0" width="100%">
			<tr>
				<td height="25">', $txt['pages'], ': ', $context['page_index'], '</td>
				<td align="right"><input type="submit" value="', $txt['quickmod_delete_selected'], '" style="font-weight: normal;" /></td>
			</tr>
		</table>
	</div>';
	}

	echo '
	<input type="hidden" name="sc" value="', $context['session_id'], '" />
</form>';
}

function template_send()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt;

	echo '
		<table border="0" width="75%" cellpadding="3" align="center" cellspacing="0">
			<tr>
				<td valign="bottom">', theme_linktree(), '</td>
			</tr>
		</table>
		<table width="75%" cellpadding="3" cellspacing="1" border="0" align="center" class="bordercolor">
			<tr>
				<td align="right" valign="bottom" class="catbg" colspan="4" style="font-size: smaller;">
					<a href="', $scripturl, '?action=pm">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/im_inbox.gif" alt="' . $txt['inbox'] . '" border="0" />' : $txt['inbox']), '</a>', $context['menu_separator'], '
					<a href="', $scripturl, '?action=pm;f=outbox">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/im_outbox.gif" alt="' . $txt[320] . '" border="0" />' : $txt[320]), '</a>', $context['menu_separator'], '
				</td>
			</tr>
		</table>';

	// Show which messages were sent successfully and which failed.
	if (!empty($context['send_log']))
	{
		echo '
		<br />
		<table border="0" width="75%" cellspacing="1" cellpadding="3" class="bordercolor" align="center">
			<tr class="titlebg">
				<td>', $txt['pm_send_report'], '</td>
			</tr>
			<tr>
				<td class="windowbg">';
		foreach ($context['send_log']['sent'] as $log_entry)
			echo '<span style="color: green">', $log_entry, '</span><br />';
		foreach ($context['send_log']['failed'] as $log_entry)
			echo '<span style="color: red">', $log_entry, '</span><br />';
		echo '
				</td>
			</tr>
		</table><br />';
	}

	// Show the preview of the personal message.
	if (isset($context['preview_message']))
	echo '
		<br />
		<table border="0" width="75%" cellspacing="1" cellpadding="3" class="bordercolor" align="center">
			<tr class="titlebg">
				<td>', $context['preview_subject'], '</td>
			</tr>
			<tr>
				<td class="windowbg">
					', $context['preview_message'], '
				</td>
			</tr>
		</table><br />';

	// Main message editing box.
	echo '
		<table border="0" width="75%" align="center" cellpadding="3" cellspacing="1" class="bordercolor">
			<tr class="titlebg">
				<td><img src="', $settings['images_url'], '/icons/im_newmsg.gif" alt="', $txt['new_message'], '" title="', $txt['new_message'], '" border="0" />&nbsp;', $txt['new_message'], '</td>
			</tr><tr>
				<td class="windowbg">
					<form action="', $scripturl, '?action=pm;sa=send2" method="post" name="postmodify" onsubmit="submitonce(this);">
						<table border="0" cellpadding="3" width="100%">';

	// If there were errors for sending the PM, show them.
	if (!empty($context['post_error']['messages']))
	{
		echo '
							<tr>
								<td></td>
								<td align="left">
									<b>', $txt['error_while_submitting'], '</b>
									<div style="color: red; margin: 1ex 0 2ex 3ex;">
										', implode('<br />', $context['post_error']['messages']), '
									</div>
								</td>
							</tr>';
	}

	// To and bcc. Include a button to search for members.
	echo '
							<tr>
								<td align="right"><b', (isset($context['post_error']['no_to']) || isset($context['post_error']['bad_to']) ? ' style="color: #FF0000;"' : ''), '>', $txt['pm_to'], ':</b></td>
								<td class="smalltext">
									<input type="text" name="to" id="to" value="', $context['to'], '" size="40" />&nbsp;
									<a href="', $scripturl, '?action=findmember;input=to;quote=1;sesc=', $context['session_id'], '" onclick="return reqWin(this.href, 350, 400);"><img src="', $settings['images_url'], '/icons/assist.gif" border="0" alt="', $txt['find_members'], '" /></a> <a href="', $scripturl, '?action=findmember;input=to;quote=1;sesc=', $context['session_id'], '" onclick="return reqWin(this.href, 350, 400);">', $txt['find_members'], '</a>
								</td>
							</tr><tr>
								<td align="right"><b', (isset($context['post_error']['bad_bcc']) ? ' style="color: #FF0000;"' : ''), '>', $txt['pm_bcc'], ':</b></td>
								<td class="smalltext">
									<input type="text" name="bcc" id="bcc" value="', $context['bcc'], '" size="40" />&nbsp;
									<a href="', $scripturl, '?action=findmember;input=bcc;quote=1;sesc=', $context['session_id'], '" onclick="return reqWin(this.href, 350, 400);"><img src="', $settings['images_url'], '/icons/assist.gif" border="0" alt="', $txt['find_members'], '" /></a> ', $txt['pm_multiple'], '
								</td>
							</tr>';
	// Subject of personal message.
	echo '
							<tr>
								<td align="right"><b', (isset($context['post_error']['no_subject']) ? ' style="color: #FF0000;"' : ''), '>', $txt['subject'], ':</b></td>
								<td><input type="text" name="subject" value="', $context['subject'], '" size="40" maxlength="50" /></td>
							</tr>';

	// Show BBC buttons, smileys and textbox.
	theme_postbox($context['message']);

	// Send, Preview buttons.
	echo '
							<tr>
								<td align="right" colspan="2">
									<input type="submit" value="', $txt['send_message'], '" onclick="return submitThisOnce(this);" accesskey="s" />
									<input type="submit" name="preview" value="', $txt['preview'], '" onclick="return submitThisOnce(this);" accesskey="p" />';
	echo '
								</td>
							</tr>
							<tr>
								<td></td>
								<td align="left">
									<input type="checkbox" name="outbox" id="outbox" value="1"', $context['copy_to_outbox'] ? ' checked="checked"' : '', ' class="check" /> <label for="outbox">', $txt['pm_save_outbox'], '</label>
								</td>
							</tr>
						</table>
						<input type="hidden" name="sc" value="', $context['session_id'], '" />
						<input type="hidden" name="seqnum" value="', $context['form_sequence_number'], '" />
					</form>
				</td>
			</tr>
		</table>';

	// Show the message you're replying to.
	if ($context['reply'])
		echo '
		<br />
		<br />
		<table width="100%" border="0" cellspacing="1" cellpadding="4" class="bordercolor">
			<tr>
				<td colspan="2" class="windowbg"><b>', $txt['subject'], ': ', $context['quoted_message']['subject'], '</b></td>
			</tr>
			<tr>
				<td class="windowbg2">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td class="windowbg2">', $txt['from'], ': ', $context['quoted_message']['member']['name'], '</td>
							<td class="windowbg2" align="right">', $txt['on'], ': ', $context['quoted_message']['time'], '</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="windowbg">', $context['quoted_message']['body'], '</td>
			</tr>
		</table>';
}

// This template asks the user whether they wish to empty out their folder/messages.
function template_ask_delete()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt;

	echo '
		<table border="0" width="80%" cellpadding="4" cellspacing="1" class="bordercolor" align="center">
			<tr class="titlebg">
				<td>', ($context['delete_all'] ? $txt['delete_message'] : $txt['delete_all']), '</td>
			</tr>
			<tr>
				<td class="windowbg">
					', $txt['delete_all_confirm'], '<br />
					<br />
					<b><a href="', $scripturl, '?action=pm;sa=removeall2;f=', $context['folder'], ';sesc=', $context['session_id'], '">', $txt['yes'], '</a> - <a href="javascript:history.go(-1);">', $txt['no'], '</a></b>
				</td>
			</tr>
		</table>';
}

// This template asks the user what messages they want to prune.
function template_prune()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
	<form action="', $scripturl, '?action=pm;sa=prune" method="post" onsubmit="return confirm(\'', $txt['delete_selected_confirm'], '\');">
		<table width="80%" cellpadding="4" cellspacing="0" border="0" align="center" class="tborder">
			<tr class="titlebg">
				<td>', $txt['delete_message'], '</td>
			</tr>
			<tr class="windowbg">
				<td>', $txt['pm_prune_desc1'], ' <input type="text" name="age" size="3" value="14" /> ', $txt['pm_prune_desc2'], '</td>
			</tr>
			<tr class="windowbg">
				<td align="right"><input type="submit" value="', $txt['delete'], '"></td>
			</tr>
		</table>
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
	</form>';
}

?>