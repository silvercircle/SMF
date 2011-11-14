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
function template_single_post()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $topic;

	echo '
		<div class="jqmWindow" style="display:none;" id="interpostlink_helper">
		<div class="glass jsconfirm title">',
		$txt['quick_post_link_title'], '
		</div>
		<div class="flat_container lefttext smalltext">',
		$txt['quick_post_link_text'], '
		<dl class="common left" style="line-height:24px;">
		<dt><strong>', $txt['quick_post_link_bbcode'], '</strong></dt><dd><input size="78" type="text" id="interpostlink_helper_content" value="" /></dd>
		<dt><strong>', $txt['quick_post_link_full'], '</strong></dt><dd><input size="78" type="text" id="interpostlink_helper_content_full" value="" /></dd>
		</dl>
		</div>
		<div class="centertext smalltext smallpadding"><input type="button" class="button_submit" onclick="$(\'#interpostlink_helper\').css(\'position\',\'static\');$(\'#interpostlink_helper\').hide();setDimmed(0);" value="', $txt['quick_post_link_dismiss'], '" /></div>
		</div>
		<div id="share_bar" style="display:none;position:absolute;right:0;white-space:nowrap;width:auto;">
		<div class="bmbar">
		 <span role="button" class="button icon share_this share_fb" data-href="http://www.facebook.com/sharer.php?u=%%uri%%">Share</span>
		 <span role="button" class="button icon share_this share_tw" data-href="http://twitter.com/share?text=%%txt%%&amp;url=%%uri%%">Tweet</span>
		 <span role="button" class="button icon share_this share_digg" data-href="http://digg.com/submit?phase=2&amp;title=%%txt%%&amp;url=%%uri%%">Digg</span>
		 <div class="clear"></div>
       	</div>
       	</div>';
       	
	// Show the anchor for the top and for the first message. If the first message is new, say so.
	echo '
		<a id="top"></a>
		', $context['first_new_message'] ? '<a id="new"></a>' : '';

	// Is this topic also a poll?
	if ($context['is_poll'])
	{
		echo '
			<br>
			<div id="poll">
				<div class="cat_bar">
					<h3>
						<span class="ie6_header floatleft"><img src="', $settings['images_url'], '/topic/', $context['poll']['is_locked'] ? 'normal_poll_locked' : 'normal_poll', '.gif" alt="" class="icon" /> ', $txt['poll'], '</span>
					</h3>
				</div>
				<div class="blue_container">
					<div class="content" id="poll_options">
						<h4 id="pollquestion">
							', $context['poll']['question'], '
						</h4>';

		// Are they not allowed to vote but allowed to view the options?
		if ($context['poll']['show_results'] || !$context['allow_vote'])
		{
			echo '
					<dl class="options">';

			// Show each option with its corresponding percentage bar.
			foreach ($context['poll']['options'] as $option)
			{
				echo '
						<dt class="middletext', $option['voted_this'] ? ' voted' : '', '">', $option['option'], '</dt>
						<dd class="middletext statsbar', $option['voted_this'] ? ' voted' : '', '">';

				if ($context['allow_poll_view'])
					echo '
							', $option['bar_ndt'], '
							<span class="percentage">', $option['votes'], ' (', $option['percent'], '%)</span>';

				echo '
						</dd>';
			}

			echo '
					</dl>';

			if ($context['allow_poll_view'])
				echo '
						<p><strong>', $txt['poll_total_voters'], ':</strong> ', $context['poll']['total_votes'], '</p>';
		}
		// They are allowed to vote! Go to it!
		else
		{
			echo '
						<form action="', $scripturl, '?action=vote;topic=', $context['current_topic'], '.', $context['start'], ';poll=', $context['poll']['id'], '" method="post" accept-charset="', $context['character_set'], '">';

			// Show a warning if they are allowed more than one option.
			if ($context['poll']['allowed_warning'])
				echo '
							<p class="smallpadding">', $context['poll']['allowed_warning'], '</p>';

			echo '
							<ul class="reset options">';

			// Show each option with its button - a radio likely.
			foreach ($context['poll']['options'] as $option)
				echo '
								<li class="middletext">', $option['vote_button'], ' <label for="', $option['id'], '">', $option['option'], '</label></li>';

			echo '
							</ul>
							<div class="submitbutton">
								<input type="submit" value="', $txt['poll_vote'], '" class="button_submit" />
								<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
							</div>
						</form>';
		}

		// Is the clock ticking?
		if (!empty($context['poll']['expire_time']))
			echo '
						<p><strong>', ($context['poll']['is_expired'] ? $txt['poll_expired_on'] : $txt['poll_expires_on']), ':</strong> ', $context['poll']['expire_time'], '</p>';

		echo '
					</div>
				</div>
			</div>
			<div id="pollmoderation">';

		// Build the poll moderation button array.
		$poll_buttons = array(
			'vote' => array('test' => 'allow_return_vote', 'text' => 'poll_return_vote', 'image' => 'poll_options.gif', 'lang' => true, 'url' => $scripturl . '?topic=' . $context['current_topic'] . '.' . $context['start']),
			'results' => array('test' => 'show_view_results_button', 'text' => 'poll_results', 'image' => 'poll_results.gif', 'lang' => true, 'url' => $scripturl . '?topic=' . $context['current_topic'] . '.' . $context['start'] . ';viewresults'),
			'change_vote' => array('test' => 'allow_change_vote', 'text' => 'poll_change_vote', 'image' => 'poll_change_vote.gif', 'lang' => true, 'url' => $scripturl . '?action=vote;topic=' . $context['current_topic'] . '.' . $context['start'] . ';poll=' . $context['poll']['id'] . ';' . $context['session_var'] . '=' . $context['session_id']),
			'lock' => array('test' => 'allow_lock_poll', 'text' => (!$context['poll']['is_locked'] ? 'poll_lock' : 'poll_unlock'), 'image' => 'poll_lock.gif', 'lang' => true, 'url' => $scripturl . '?action=lockvoting;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
			'edit' => array('test' => 'allow_edit_poll', 'text' => 'poll_edit', 'image' => 'poll_edit.gif', 'lang' => true, 'url' => $scripturl . '?action=editpoll;topic=' . $context['current_topic'] . '.' . $context['start']),
			'remove_poll' => array('test' => 'can_remove_poll', 'text' => 'poll_remove', 'image' => 'admin_remove_poll.gif', 'lang' => true, 'custom' => 'onclick="return Eos_Confirm(\'\', \'' . $txt['poll_remove_warn'] . '\', $(this).attr(\'href\'));"', 'url' => $scripturl . '?action=removepoll;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		);

		template_button_strip($poll_buttons);

		echo '
			</div>';
	}
	// Does this topic have some events linked to it?
	if (!empty($context['linked_calendar_events']))
	{
		echo '
			<div class="orange_container">
				<h3>', $txt['calendar_linked_events'], '</h3>
					<ul class="reset">';

		foreach ($context['linked_calendar_events'] as $event)
			echo '
					<li>
					', ($event['can_edit'] ? '<a href="' . $event['modify_href'] . '"> <img src="' . $settings['images_url'] . '/icons/modify_small.gif" alt="" title="' . $txt['modify'] . '" class="edit_event" /></a> ' : ''), '<strong>', $event['title'], '</strong>: ', $event['start_date'], ($event['start_date'] != $event['end_date'] ? ' - ' . $event['end_date'] : ''), '
					</li>';

		echo '
					</ul>
			</div>';
	}
	// Build the normal button array.
	$normal_buttons = array(
		'reply' => array('test' => 'can_reply', 'text' => 'reply', 'image' => 'reply.gif', 'lang' => true, 'url' => $scripturl . '?action=post;topic=' . $context['current_topic'] . '.' . $context['start'] . ';last_msg=' . $context['topic_last_message'], 'active' => true),
		'add_poll' => array('test' => 'can_add_poll', 'text' => 'add_poll', 'image' => 'add_poll.gif', 'lang' => true, 'url' => $scripturl . '?action=editpoll;add;topic=' . $context['current_topic'] . '.' . $context['start']),
		'notify' => array('test' => 'can_mark_notify', 'text' => $context['is_marked_notify'] ? 'unnotify' : 'notify', 'image' => ($context['is_marked_notify'] ? 'un' : '') . 'notify.gif', 'lang' => true, 'custom' => 'onclick="return Eos_Confirm(\'\', \'' . ($context['is_marked_notify'] ? $txt['notification_disable_topic'] : $txt['notification_enable_topic']) . '\', $(this).attr(\'href\'));"', 'url' => $scripturl . '?action=notify;sa=' . ($context['is_marked_notify'] ? 'off' : 'on') . ';topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'mark_unread' => array('test' => 'can_mark_unread', 'text' => 'mark_unread', 'image' => 'markunread.gif', 'lang' => true, 'url' => $scripturl . '?action=markasread;sa=topic;t=' . $context['mark_unread_time'] . ';topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'send' => array('test' => 'can_send_topic', 'text' => 'send_topic', 'image' => 'sendtopic.gif', 'lang' => true, 'url' => $scripturl . '?action=emailuser;sa=sendtopic;topic=' . $context['current_topic'] . '.0'),
		'print' => array('text' => 'print', 'image' => 'print.gif', 'lang' => true, 'custom' => 'rel="nofollow"', 'url' => $scripturl . '?action=printpage;topic=' . $context['current_topic'] . '.0'),
	);

	// Allow adding new buttons easily.
	HookAPI::callHook('integrate_display_buttons', array(&$normal_buttons));
	
	// Show the topic information - icon, subject, etc.
	echo '
			<div id="forumposts">
				<div>
					<h1 class="bigheader">
						', $txt['topic'], ': ', $context['prefix'], $context['subject'], ' &nbsp;(', $txt['read'], ' ', $context['num_views'], ' ', $txt['times'], ')
					</h1>
				</div>';

	// social share bar
	if($context['use_share'])
		socialbar($scripturl . '?topic=' . $topic, urlencode($context['subject']));
				
	if($context['tags_active']) {
	echo '
		<div id="tagstrip"><span id="tags">';
	foreach ($context['topic_tags'] as $i => $tag) {
		echo '<a href="' . $scripturl . '?action=tags;tagid=' . $tag['ID_TAG']  . '">' . $tag['tag'] . '</a>';
		if($context['can_delete_tags'])
			echo '<a href="' . $scripturl . '?action=tags;sa=deletetag;tagid=' . $tag['ID']  . '"><span onclick="sendRequest(\'action=xmlhttp;sa=tags;deletetag=1;tagid=' . $tag['ID']. '\', $(\'#tags\'));return(false);" class="xtag">&nbsp;&nbsp;</span></a>';
		else
			echo '&nbsp;&nbsp;';
	}
	echo '</span>';
		
	if($context['can_add_tags'])
		echo '
			&nbsp;<a rel="nofollow" id="addtag" onclick="$(\'#tagform\').remove();sendRequest(\'action=xmlhttp;sa=tags;addtag=1;topic=',$topic,'\', $(\'#addtag\'));return(false);" data-id="',$topic,'" href="' . $scripturl . '?action=tags;sa=addtag;topic=',$topic, '">' . $txt['smftags_addtag'] . '</a>';
	else
		echo '&nbsp;';
	echo '
		</div>';
	}
	echo '
		<div class="clear"></div><form data-alt="',$scripturl,'?action=post;msg=%id_msg%;topic=',$context['current_topic'],'.',$context['start'], '" action="', $scripturl, '?action=quickmod2;topic=', $context['current_topic'], '.', $context['start'], '" method="post" accept-charset="', $context['character_set'], '" name="quickModForm" id="quickModForm" style="margin: 0;" onsubmit="return oQuickModify.bInEditMode ? oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\') : false">';

	$ignoredMsgs = array();
	$removableMessageIDs = array();

	// Get all the messages...
  	while ($message = $context['get_message']())
	{
		if ($message['can_remove'])
			$removableMessageIDs[] = $message['id'];
		$context['postbit_callbacks']['firstpost']($message);
	}
	echo '
				<input type="hidden" name="goadvanced" value="1" />
				</form>
			</div>
			<a id="lastPost"></a>';
			
	$context['inline_footer_script'] .= '
	var smf_likelabel = \''.$txt['like_label'].'\';
	var smf_unlikelabel = \''.$txt['unlike_label'].'\'
	';
	// Show the lower breadcrumbs.
	theme_linktree();
	$mod_buttons = array(
		'move' => array('test' => 'can_move', 'text' => 'move_topic', 'image' => 'admin_move.gif', 'lang' => true, 'url' => $scripturl . '?action=movetopic;topic=' . $context['current_topic'] . '.0'),
		'delete' => array('test' => 'can_delete', 'text' => 'remove_topic', 'image' => 'admin_rem.gif', 'lang' => true, 'custom' => 'onclick="return confirm(\'' . $txt['are_sure_remove_topic'] . '\');"', 'url' => $scripturl . '?action=removetopic2;topic=' . $context['current_topic'] . '.0;' . $context['session_var'] . '=' . $context['session_id']),
		'lock' => array('test' => 'can_lock', 'text' => empty($context['is_locked']) ? 'set_lock' : 'set_unlock', 'image' => 'admin_lock.gif', 'lang' => true, 'url' => $scripturl . '?action=lock;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'sticky' => array('test' => 'can_sticky', 'text' => empty($context['is_sticky']) ? 'set_sticky' : 'set_nonsticky', 'image' => 'admin_sticky.gif', 'lang' => true, 'url' => $scripturl . '?action=sticky;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'merge' => array('test' => 'can_merge', 'text' => 'merge', 'image' => 'merge.gif', 'lang' => true, 'url' => $scripturl . '?action=mergetopics;board=' . $context['current_board'] . '.0;from=' . $context['current_topic']),
		'calendar' => array('test' => 'calendar_post', 'text' => 'calendar_link', 'image' => 'linktocal.gif', 'lang' => true, 'url' => $scripturl . '?action=post;calendar;msg=' . $context['topic_first_message'] . ';topic=' . $context['current_topic'] . '.0'),
	);

	// Restore topic. eh?  No monkey business.
	if ($context['can_restore_topic'])
		$mod_buttons[] = array('text' => 'restore_topic', 'image' => '', 'lang' => true, 'url' => $scripturl . '?action=restoretopic;topics=' . $context['current_topic'] . ';' . $context['session_var'] . '=' . $context['session_id']);

	// Allow adding new mod buttons easily.
	HookAPI::callHook('integrate_mod_buttons', array(&$mod_buttons));

	echo '
			<div id="moderationbuttons">', template_button_strip($mod_buttons, 'bottom', array('id' => 'moderationbuttons_strip')), '</div>';

	// Show the jumpto box, or actually...let Javascript do it.
	echo '
			<div class="plainbox" id="display_jump_to">&nbsp;</div>';

	if (!empty($options['display_quick_mod']) && $context['can_remove_post'])
		$context['inline_footer_script'] .= '
	var oInTopicModeration = new InTopicModeration({
		sSelf: \'oInTopicModeration\',
		sCheckboxContainerMask: \'in_topic_mod_check_\',
		aMessageIds: [\''. implode('\', \'', $removableMessageIDs). '\'],
		sSessionId: \''. $context['session_id']. '\',
		sSessionVar: \''. $context['session_var']. '\',
		sButtonStrip: \'moderationbuttons\',
		sButtonStripDisplay: \'moderationbuttons_strip\',
		bUseImageButton: false,
		bCanRemove: '. ($context['can_remove_post'] ? 'true' : 'false'). ',
		sRemoveButtonLabel: \''. $txt['quickmod_delete_selected']. '\',
		sRemoveButtonImage: \'delete_selected.gif\',
		sRemoveButtonConfirm: \''. $txt['quickmod_confirm']. '\',
		bCanRestore: '.($context['can_restore_msg'] ? 'true' : 'false'). ',
		sRestoreButtonLabel: \''. $txt['quick_mod_restore']. '\',
		sRestoreButtonImage: \'restore_selected.gif\',
		sRestoreButtonConfirm: \''. $txt['quickmod_confirm']. '\',
		sFormId: \'quickModForm\'
	});
	';

	$context['inline_footer_script'] .= '
	if (\'XMLHttpRequest\' in window)
	{
		var oQuickModify = new QuickModify({
		sScriptUrl: smf_scripturl,
		bShowModify: '.($settings['show_modify'] ? 'true' : 'false'). ',
		iTopicId: '.$context['current_topic'].',
		sTemplateBodyEdit: '.JavaScriptEscape('
			<div id="quick_edit_body_container">
			<div id="error_box" style="padding: 4px;" class="error"></div>
			<textarea class="editor" name="message" rows="20" style="' . ($context['browser']['is_ie8'] ? 'width: 635px; max-width: 100%; min-width: 100%' : 'width: 100%') . '; margin-bottom: 10px;" tabindex="' . $context['tabindex']++ . '">%body%</textarea><br />
			<input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '" />
			<input type="hidden" name="topic" value="' . $context['current_topic'] . '" />
			<input type="hidden" name="msg" value="%msg_id%" />
			<input type="hidden" style="width: 50%;" name="subject" value="%subject%" size="50" maxlength="80" tabindex="' . $context['tabindex']++ . '" class="input_text" />
			<div class="righttext">
				<span class="button floatright" onclick="return oQuickModify.goAdvanced(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\');" />Go Advanced</a></span>
				<span class="button floatright" onclick="return oQuickModify.modifyCancel();" >'.$txt['modify_cancel'].'</span>
				<span class="button floatright" onclick="return oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\');" accesskey="s">'.$txt['save'].'</span>
			</div>
			</div>'). ',
		sTemplateSubjectEdit: '.JavaScriptEscape('<input type="text" style="width: 50%;" name="subject_edit" value="%subject%" size="50" maxlength="80" tabindex="' . $context['tabindex']++ . '" class="input_text" />'). ',
		sTemplateBodyNormal: '. JavaScriptEscape('%body%'). ',
		sTemplateSubjectNormal: '.JavaScriptEscape('<a href="' . $scripturl . '?topic=' . $context['current_topic'] . '.msg%msg_id%#msg%msg_id%" rel="nofollow">%subject%</a>').',
		sTemplateTopSubject: '.JavaScriptEscape($txt['topic'] . ': %subject% &nbsp;(' . $txt['read'] . ' ' . $context['num_views'] . ' ' . $txt['times'] . ')').',
		sErrorBorderStyle: '.JavaScriptEscape('1px solid red'). '
		});

		aJumpTo[aJumpTo.length] = new JumpTo({
			sContainerId: "display_jump_to",
			sJumpToTemplate: "<label class=\"smalltext\" for=\"%select_id%\">'.$context['jump_to']['label'].':<" + "/label> %dropdown_list%",
			iCurBoardId: '.$context['current_board'].',
			iCurBoardChildLevel: '.$context['jump_to']['child_level'].',
			sCurBoardName: "'.$context['jump_to']['board_name'].'",
			sBoardChildLevelIndicator: "==",
			sBoardPrefix: "=> ",
			sCatSeparator: "-----------------------------",
			sCatPrefix: "",
			sGoButtonLabel: "'.$txt['go'].'"
		});
	}
	';

	if (!empty($ignoredMsgs))
	{
		$context['inline_footer_script'] .= '';
	}

	if ($context['can_reply'])
		$context['inline_footer_script'] .= '
	function mquote(msg_id,remove) {
		if (!window.XMLHttpRequest)
			return true;
				
		var elementButton = "mquote_" + msg_id;
		var elementButtonDelete = "mquote_remove_" + msg_id;
		var exdate = new Date();
		(remove == "remove") ? exdate.setDate(exdate.getDate() - 1) : exdate.setDate(exdate.getDate() + 1);
		document.getElementById(elementButton).style.display = (remove == "remove") ? "inline" : "none";
		document.getElementById(elementButtonDelete).style.display = (remove == "remove") ? "none" : "inline";
		document.cookie = "mquote" + msg_id + "=; expires="+exdate.toGMTString()+"; path=/";
	}
	';

	$context['inline_footer_script'] .= '
	function getIntralink(e, mid) {
		var tid = '.$context['current_topic'].';
		var _sid = "#subject_" + mid;
		var el = $("#interpostlink_helper");
		el.css("position", "fixed");
		var _content = "[ilink topic=" + tid + " post=" + mid + "]" + $(_sid).html().trim() + "[/ilink]";
		$("#interpostlink_helper_content").val(_content);
		$("#interpostlink_helper_content_full").val(e.attr("href"));
		centerElement(el, -200);
		el.css("z-index", 9999);
		setDimmed(1);
		el.show();
		$("#interpostlink_helper_content").focus();
		$("#interpostlink_helper_content").select();
	}
	$(document).keydown(function(e) {
		if(e.keyCode == 27 && $("#interpostlink_helper").css("display") != "none") {
        	$("#interpostlink_helper").css("position", "static");
        	$("#interpostlink_helper").hide();
			setDimmed(0);
    	}
	});
	var topic_id = '.$context['current_topic'].';
	';
}
?>
