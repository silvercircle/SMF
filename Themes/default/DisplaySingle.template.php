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
	global $context, $settings, $options, $txt, $scripturl, $topic;

	echo '
		<div class="jqmWindow" style="display:none;" id="interpostlink_helper">
		<div class="jqmWindow_container">
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

	// Build the normal button array.
	$normal_buttons = array(
		'reply' => array('test' => 'can_reply', 'text' => 'reply', 'custom' => 'onclick="return oQuickReply.quote(0);" ', 'image' => 'reply.gif', 'lang' => true, 'url' => $scripturl . '?action=post;topic=' . $context['current_topic'] . '.' . $context['start'] . ';last_msg=' . $context['topic_last_message'], 'active' => true),
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
			<div id="forumposts">';

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
		<div class="clear"></div><form data-alt="',$scripturl,'?action=post;msg=%id_msg%;topic=',$context['current_topic'],'.',$context['start'], '" action="', $scripturl, '?action=quickmod2;topic=', $context['current_topic'], '.', $context['start'], '" method="post" accept-charset="UTF-8" name="quickModForm" id="quickModForm" style="margin: 0;" onsubmit="return oQuickModify.bInEditMode ? oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\') : false">';

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
	$remove_url = $scripturl . '?action=removetopic2;topic=' . $context['current_topic'] . '.0;' . $context['session_var'] . '=' . $context['session_id'];
	$mod_buttons = array(
		'move' => array('test' => 'can_move', 'text' => 'move_topic', 'image' => 'admin_move.gif', 'lang' => true, 'url' => $scripturl . '?action=movetopic;topic=' . $context['current_topic'] . '.0'),
		'delete' => array('test' => 'can_delete', 'text' => 'remove_topic', 'image' => 'admin_rem.gif', 'lang' => true, 'custom' => 'onclick="return Eos_Confirm(\'\',\'' . $txt['are_sure_remove_topic'] . '\',\''.$remove_url.'\');"', 'url' => $remove_url),
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
		<div id="moderationbuttons">', template_button_strip($mod_buttons, 'right', array('id' => 'moderationbuttons_strip', 'class' => 'plainbuttonlist')), '</div>';


	if ($context['can_reply'] && !empty($options['display_quick_reply']))
	{
		echo '
			<a id="quickreply"></a>
			<div class="clear"></div>
			<div style="display:none;overflow:hidden;" id="quickreplybox">';
		echo '
					<div class="cat_bar">
					 <strong>',$txt['post_reply'],'</strong>&nbsp;&nbsp;<a href="',$scripturl,'?action=helpadmin;help=quickreply_help','" onclick="return reqWin(this.href);" class="help tinytext">',$txt['post_reply_help'],'</a>
					</div>
					<div class="flat_container mediumpadding">';
		echo '
							<input type="hidden" name="_qr_board" value="', $context['current_board'], '" />
							<input type="hidden" name="topic" value="', $context['current_topic'], '" />
							<input type="hidden" name="subject" value="', $context['response_prefix'], $context['subject'], '" />
							<input type="hidden" name="icon" value="xx" />
							<input type="hidden" name="from_qr" value="1" />
							<input type="hidden" name="notify" value="', $context['is_marked_notify'] || !empty($options['auto_notify']) ? '1' : '0', '" />
							<input type="hidden" name="not_approved" value="', !$context['can_reply_approved'], '" />
							<input type="hidden" name="goback" value="', empty($options['return_to_post']) ? '0' : '1', '" />
							<input type="hidden" name="last_msg" value="', $context['topic_last_message'], '" />
							<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
							<input type="hidden" name="seqnum" value="', $context['form_sequence_number'], '" />';

		// Guests just need more.
		if ($context['user']['is_guest'])
			echo '
							<strong>', $txt['name'], ':</strong> <input type="text" name="guestname" value="', $context['name'], '" size="25" class="input_text" tabindex="', $context['tabindex']++, '" />
							<strong>', $txt['email'], ':</strong> <input type="text" name="email" value="', $context['email'], '" size="25" class="input_text" tabindex="', $context['tabindex']++, '" /><br />';

		// Is visual verification enabled?
		if ($context['require_verification'])
			echo '
							<strong>', $txt['verification'], ':</strong>', template_control_verification($context['visual_verification_id'], 'quick_reply'), '<br />';

		if(isset($context['user']['avatar']['image']) && !empty($context['user']['avatar']['image']))
			echo '
					<div class="floatleft blue_container smallpadding avatar">',
			$context['user']['avatar']['image'],'
					</div>';
		echo '
							<div class="quickReplyContent" style="margin-left:150px;">';
		echo $context['is_locked'] ? '<div class="red_container tinytext">' . $txt['quick_reply_warning'] . '</div>' : '',
		$context['oldTopicError'] ? '<div class="red_container tinytext">' . sprintf($txt['error_old_topic'], $modSettings['oldTopicDays']) . '</div>' : '', '
						', $context['can_reply_approved'] ? '' : '<em>' . $txt['wait_for_approval'] . '</em>', '
						', !$context['can_reply_approved'] && $context['require_verification'] ? '<br />' : '';
		echo '
								<textarea id="quickReplyMessage" style="width:99%;" rows="18" name="message" tabindex="', $context['tabindex']++, '"></textarea>';
		if($context['automerge'])
			echo '
								<input type="checkbox" name="want_automerge" id="want_automerge" checked="checked" value="1" />',$txt['want_automerge'];
		echo '
								</div>
								<div class="righttext padding">
								<input type="submit" name="post" value="', $txt['post'], '" onclick="return submitThisOnce(this);" accesskey="s" tabindex="', $context['tabindex']++, '" class="button_submit" />
								<input type="submit" name="preview" value="', $txt['go_advanced'], '" onclick="return submitThisOnce(this);" accesskey="p" tabindex="', $context['tabindex']++, '" class="button_submit" />
								<input type="submit" name="cancel" value="', 'Cancel', '" onclick="return(oQuickReply.cancel());" accesskey="p" tabindex="', $context['tabindex']++, '" class="button_submit" />
						</div>
				</div>
				<br>
			</div>';
	}
	theme_linktree();
	// Show the jumpto box, or actually...let Javascript do it.
	echo '
			<div class="plainbox" id="display_jump_to">&nbsp;</div>';

	$context['inline_footer_script'] .= '
	var oQuickReply = new QuickReply({
		bDefaultCollapsed: '. (!empty($options['display_quick_reply']) && $options['display_quick_reply'] == 2 ? 'false' : 'true'). ',
		iTopicId: '. $context['current_topic']. ',
		iStart: '. $context['start']. ',
		sScriptUrl: smf_scripturl,
		sImagesUrl: "'. $settings['images_url']. '",
		sContainerId: "quickReplyOptions",
		sImageId: "quickReplyExpand",
		sImageCollapsed: "collapse.gif",
		sImageExpanded: "expand.gif",
		iMarkedForMQ: ' . $context['multiquote_posts_count'] . ',
		sJumpAnchor: "quickreplybox",
		bEnabled: ' . (!empty($options['display_quick_reply']) ? 'true' : 'false') . '
	});
	';

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

function template_single_post_xml() {
	global $context, $settings, $options, $txt, $scripturl, $topic;

	echo '<', '?xml version="1.0" encoding="UTF-8" ?', '>
<document>
  <response open="default_overlay" width="70%" />
    <content>
    <![CDATA[';

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
			<div id="mcard_content">';

	echo '
		<div class="clear"></div>
		<form data-alt="', $scripturl, '?action=post;msg=%id_msg%;topic=', $context['current_topic'], '.', $context['start'], '" action="', $scripturl, '?action=quickmod2;topic=', $context['current_topic'], '.', $context['start'], '" method="post" accept-charset="UTF-8" name="quickModForm" id="quickModForm" style="margin: 0;" onsubmit="return oQuickModify.bInEditMode ? oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\') : false">
	      <div class="posts_container framed_region">';

	$ignoredMsgs = array();
	$removableMessageIDs = array();

	// Get all the messages...
	while ($message = $context['get_message']()) {
		if ($message['can_remove'])
			$removableMessageIDs[] = $message['id'];
		template_postbit_normal($message);
	}
	echo '
				<input type="hidden" name="goadvanced" value="1" />
				</div>
				</form>
			</div>
			<a id="lastPost"></a>';

	$context['inline_footer_script'] .= '
	var smf_likelabel = \'' . $txt['like_label'] . '\';
	var smf_unlikelabel = \'' . $txt['unlike_label'] . '\'
	';
	// Restore topic. eh?  No monkey business.
	if ($context['can_restore_topic'])
		$mod_buttons[] = array('text' => 'restore_topic', 'image' => '', 'lang' => true, 'url' => $scripturl . '?action=restoretopic;topics=' . $context['current_topic'] . ';' . $context['session_var'] . '=' . $context['session_id']);

	if (!empty($options['display_quick_mod']) && $context['can_remove_post'])
		$context['inline_footer_script'] .= '
	var oInTopicModeration = new InTopicModeration({
		sSelf: \'oInTopicModeration\',
		sCheckboxContainerMask: \'in_topic_mod_check_\',
		aMessageIds: [\'' . implode('\', \'', $removableMessageIDs) . '\'],
		sSessionId: \'' . $context['session_id'] . '\',
		sSessionVar: \'' . $context['session_var'] . '\',
		sButtonStrip: \'moderationbuttons\',
		sButtonStripDisplay: \'moderationbuttons_strip\',
		bUseImageButton: false,
		bCanRemove: ' . ($context['can_remove_post'] ? 'true' : 'false') . ',
		sRemoveButtonLabel: \'' . $txt['quickmod_delete_selected'] . '\',
		sRemoveButtonImage: \'delete_selected.gif\',
		sRemoveButtonConfirm: \'' . $txt['quickmod_confirm'] . '\',
		bCanRestore: ' . ($context['can_restore_msg'] ? 'true' : 'false') . ',
		sRestoreButtonLabel: \'' . $txt['quick_mod_restore'] . '\',
		sRestoreButtonImage: \'restore_selected.gif\',
		sRestoreButtonConfirm: \'' . $txt['quickmod_confirm'] . '\',
		sFormId: \'quickModForm\'
	});
	';

	$context['inline_footer_script'] .= '
	if (\'XMLHttpRequest\' in window)
	{
		var oQuickModify = new QuickModify({
		sScriptUrl: smf_scripturl,
		bShowModify: ' . ($settings['show_modify'] ? 'true' : 'false') . ',
		iTopicId: ' . $context['current_topic'] . ',
		sTemplateBodyEdit: ' . JavaScriptEscape('
			<div id="quick_edit_body_container">
			<div id="error_box" style="padding: 4px;" class="error"></div>
			<textarea class="editor" name="message" rows="20" style="' . ($context['browser']['is_ie8'] ? 'width: 635px; max-width: 100%; min-width: 100%' : 'width: 100%') . '; margin-bottom: 10px;" tabindex="' . $context['tabindex']++ . '">%body%</textarea><br />
			<input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '" />
			<input type="hidden" name="topic" value="' . $context['current_topic'] . '" />
			<input type="hidden" name="msg" value="%msg_id%" />
			<input type="hidden" style="width: 50%;" name="subject" value="%subject%" size="50" maxlength="80" tabindex="' . $context['tabindex']++ . '" class="input_text" />
			<div class="righttext">
				<span class="button floatright" onclick="return oQuickModify.goAdvanced(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\');" />Go Advanced</a></span>
				<span class="button floatright" onclick="return oQuickModify.modifyCancel();" >' . $txt['modify_cancel'] . '</span>
				<span class="button floatright" onclick="return oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\');" accesskey="s">' . $txt['save'] . '</span>
			</div>
			</div>') . ',
		sTemplateSubjectEdit: ' . JavaScriptEscape('<input type="text" style="width: 50%;" name="subject_edit" value="%subject%" size="50" maxlength="80" tabindex="' . $context['tabindex']++ . '" class="input_text" />') . ',
		sTemplateBodyNormal: ' . JavaScriptEscape('%body%') . ',
		sTemplateSubjectNormal: ' . JavaScriptEscape('<a href="' . $scripturl . '?topic=' . $context['current_topic'] . '.msg%msg_id%#msg%msg_id%" rel="nofollow">%subject%</a>') . ',
		sTemplateTopSubject: ' . JavaScriptEscape($txt['topic'] . ': %subject% &nbsp;(' . $txt['read'] . ' ' . $context['num_views'] . ' ' . $txt['times'] . ')') . ',
		sErrorBorderStyle: ' . JavaScriptEscape('1px solid red') . '
		});

		aJumpTo[aJumpTo.length] = new JumpTo({
			sContainerId: "display_jump_to",
			sJumpToTemplate: "<label class=\"smalltext\" for=\"%select_id%\">' . $context['jump_to']['label'] . ':<" + "/label> %dropdown_list%",
			iCurBoardId: ' . $context['current_board'] . ',
			iCurBoardChildLevel: ' . $context['jump_to']['child_level'] . ',
			sCurBoardName: "' . $context['jump_to']['board_name'] . '",
			sBoardChildLevelIndicator: "==",
			sBoardPrefix: "=> ",
			sCatSeparator: "-----------------------------",
			sCatPrefix: "",
			sGoButtonLabel: "' . $txt['go'] . '"
		});
	}
	';

	if (!empty($ignoredMsgs)) {
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
		var tid = ' . $context['current_topic'] . ';
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
	var topic_id = ' . $context['current_topic'] . ';
	';
	echo '
	]]>
  </content>
</document>';
}

function template_processlink()
{
	global $context, $txt;

	echo <<<EOT

	<div class="cat_bar2">
		<h3>{$txt['inactive_link_title']}</h3>
	</div>

	<div class="blue_container cleantop">
		<div class="content">
			{$txt['inactive_link_explain']}
			<br>
			<br>
		    <div class="centertext">
		    	<strong>{$context['target_link']}</strong>
		    </div>
		</div>
	</div>
EOT;

}
?>
