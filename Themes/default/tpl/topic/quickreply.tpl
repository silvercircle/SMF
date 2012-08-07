  <a id="quickreply"></a>
  <div class="clear"></div>
  <div style="display:none;overflow:hidden;" id="quickreplybox">
    <div class="cat_bar">
      <strong>{$T.post_reply}</strong>&nbsp;&nbsp;<a href="{$SCRIPTURL}?action=helpadmin;help=quickreply_help" onclick="return reqWin(this.href);" class="help tinytext">{$T.post_reply_help}</a>
    </div>
    <div class="flat_container mediumpadding">
      <input type="hidden" name="_qr_board" value="{$C.current_board}" />
      <input type="hidden" name="topic" value="{$topic}" />
      <input type="hidden" name="subject" value="{$C.response_prefix}{$C.subject}" />
      <input type="hidden" name="icon" value="xx" />
      <input type="hidden" name="from_qr" value="1" />
      <input type="hidden" name="notify" value="{($C.is_marked_notify or !empty($O.auto_notify)) ? '1' : '0'}" />
      <input type="hidden" name="not_approved" value="{!$C.can_reply_approved}" />
      <input type="hidden" name="goback" value="{(empty($O.return_to_post)) ? '0' : '1'}" />
      <input type="hidden" name="last_msg" value="{$C.topic_last_message}" />
      <input type="hidden" name="{$C.session_var}" value="{$C.session_id}" />
      <input type="hidden" name="seqnum" value="{$C.form_sequence_number}" />
      {* Guests just need more. *}
      {if $C.user.is_guest}
        <strong>{$T.name}:</strong> <input type="text" name="guestname" value="{$C.name}" size="25" class="input_text" tabindex="{$tabindex++}" />
        <strong>{$T.email}:</strong> <input type="text" name="email" value="{$C.email}" size="25" class="input_text" tabindex="{$tabindex++}" />
        <br>
      {/if}
      {* Is visual verification enabled? *}
      {if $C.require_verification}
        <div class="centertext">
        <strong>{$T.verification}:</strong>
        {$SUPPORT->template_control_verification($C.visual_verification_id, 'quick_reply')}
        </div>
        <br>
      {/if}
      {if !empty($C.user.avatar.image)}
        <div class="floatleft blue_container smallpadding avatar">
          {$C.user.avatar.image}
        </div>
      {/if}
      <div class="quickReplyContent" style="margin-left:150px;">
        {($C.is_locked) ? ('<div><span class="alert tinytext">'|cat:$T.quick_reply_warning|cat:'</span></div>') : '' }
        {($C.oldTopicError) ? "<div><span class=\"alert tinytext\">{$T.error_old_topic|sprintf:$M.oldTopicDays}</span></div>" : ''}
        {($C.can_reply_approved) ? '' : "<em>{$T.wait_for_approval}</em>"}
        {(!$C.can_reply_approved and $C.require_verification) ? '<br>' : ''}
        <textarea id="quickReplyMessage" style="width:99%;" rows="18" name="message" tabindex="{$tabindex++}"></textarea>
        {if $C.automerge}
          <input class="aligned" type="checkbox" name="want_automerge" id="want_automerge" checked="checked" value="1" />
          <label class="aligned" for="want_automerge">{$T.want_automerge}</label>
        {/if}
      </div>
      <div class="righttext smallpadding">
        <input type="submit" name="post" value="{$T.post}" onclick="return submitThisOnce(this);" accesskey="s" tabindex="{$tabindex++}" class="default" />
        <input type="submit" name="preview" value="{$T.go_advanced}" onclick="return submitThisOnce(this);" accesskey="p" tabindex="{$tabindex++}" class="button_submit" />
        <input type="submit" name="cancel" value="Cancel" onclick="return(oQuickReply.cancel());" accesskey="p" tabindex="{$tabindex++}" class="button_submit" />
      </div>
    </div>
    <br>
  </div>
