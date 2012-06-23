{*
 * @name      EosAlpha BBS
 * @copyright 2011 Alex Vie silvercircle(AT)gmail(DOT)com
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright: 2011 Simple Machines (http://www.simplemachines.org)
 * license:   BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version 1.0pre
 * 
 * display a topic page
 *}
{extends 'base.tpl'}
{block content}
{$tabindex = $C.tabindex}
{$topic = $C.current_topic}
<div class="jqmWindow" style="display:none;" id="interpostlink_helper">
  <div class="jqmWindow_container">
    <div class="glass jsconfirm title">
      {$T.quick_post_link_title}
    </div>
    <div class="blue_container norounded lefttext smalltext mediumpadding mediummargin">
      {$T.quick_post_link_text}
      <dl class="common left" style="line-height:24px;">
        <dt><strong>{$T.quick_post_link_bbcode}</strong></dt><dd><input size="78" type="text" id="interpostlink_helper_content" value="" /></dd>
        <dt><strong>{$T.quick_post_link_full}</strong></dt><dd><input size="78" type="text" id="interpostlink_helper_content_full" value="" /></dd>
      </dl>
    </div>
    <div class="centertext smalltext smallpadding"><span class="button default centered" onclick="$('#interpostlink_helper').css('position','static');$('#interpostlink_helper').hide();setDimmed(0);">{$T.quick_post_link_dismiss}</span></div>
  </div>
</div>
<div id="share_bar" style="display:none;position:absolute;right:0;white-space:nowrap;width:auto;">
  <div class="bmbar">
    <span role="button" class="button icon share_this share_fb" data-href="http://www.facebook.com/sharer.php?u=%%uri%%">Share</span>
    <span role="button" class="button icon share_this share_tw" data-href="http://twitter.com/share?text=%%txt%%&amp;url=%%uri%%">Tweet</span>
    <span role="button" class="button icon share_this share_digg" data-href="http://digg.com/submit?phase=2&amp;title=%%txt%%&amp;url=%%uri%%">Digg</span>
    <div class="clear"></div>
  </div>
</div>
{if !empty($C.report_sent)}
  <div class="windowbg" id="profile_success">
    {$T.report_sent}
  </div>
{/if}
<div id="forumposts">
  <form data-alt="{$SCRIPTURL}?action=post;msg=%id_msg%;topic={$topic}.{$C.start}" action="{$SCRIPTURL}?action=quickmod2;topic={$topic}.{$C.start}" method="post" accept-charset="UTF-8" name="quickModForm" id="quickModForm" style="margin: 0;" onsubmit="return oQuickModify.bInEditMode ? oQuickModify.modifySave('{$C.session_id}', '{$C.session_var}') : false">
    <div class="posts_container nopadding" id="posts_container">
      {* Get all the messages... *}
      {foreach from=$C.message_ids item=msg}
        {$message = $SUPPORT->getMessage()}
        {include 'postbits/postbit_'|cat:$message.postbit_template_class|cat:'.tpl'}
      {/foreach}
    </div>
    <input type="hidden" name="goadvanced" value="1" />
  </form>
</div>
<a id="lastPost"></a>
{$C.template_hooks.display.below_posts}
<div id="moderationbuttons">{$SUPPORT->button_strip($C.mod_buttons, 'right', $C.mod_buttons_style)}</div>
{if $C.can_reply and !empty($O.display_quick_reply)}
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
      <input type="hidden" name="notify" value="{($C.is_marked_notify or !empty($O.auto_notify)) ? '1' : '0' }}" />
      <input type="hidden" name="not_approved" value="{!$C.can_reply_approved}" />
      <input type="hidden" name="goback" value="{(empty($O.return_to_post)) ? '0' : '1' }}" />
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
        <strong>{$T.verification}:</strong>
        {include 'visual_verification.tpl'}  {$SUPPORT->template_control_verification($C.visual_verification_id, 'quick_reply')}
        <br>
      {/if}
      {if !empty($C.user.avatar.image)}
        <div class="floatleft blue_container smallpadding avatar">
          {$C.user.avatar.image}
        </div>
      {/if}
      <div class="quickReplyContent" style="margin-left:150px;">
        {($C.is_locked) ? ('<div class="red_container tinytext">'|cat:$T.quick_reply_warning|cat:'</div>') : '' }
        {($C.oldTopicError) ? "<div class=\"red_container tinytext\">{$T.error_old_topic|sprintf:$M.oldTopicDays}</div>" : ''}
        {($C.can_reply_approved) ? '' : "<em>{$T.wait_for_approval}</em>"}
        {(!$C.can_reply_approved and $C.require_verification) ? '<br>' : ''}
        <textarea id="quickReplyMessage" style="width:99%;" rows="18" name="message" tabindex="{$tabindex++}"></textarea>
        {if $C.automerge}
          <input type="checkbox" name="want_automerge" id="want_automerge" checked="checked" value="1" />{$T.want_automerge}
        {/if}
      </div>
      <div class="righttext padding">
        <input type="submit" name="post" value="{$T.post}" onclick="return submitThisOnce(this);" accesskey="s" tabindex="{$tabindex++}" class="button_submit" />
        <input type="submit" name="preview" value="{$T.go_advanced}" onclick="return submitThisOnce(this);" accesskey="p" tabindex="{$tabindex++}" class="button_submit" />
        <input type="submit" name="cancel" value="Cancel" onclick="return(oQuickReply.cancel());" accesskey="p" tabindex="{$tabindex++}" class="button_submit" />
      </div>
    </div>
    <br>
  </div>
{/if} {* quick reply *}
{$C.template_hooks.display.footer}
{* Show the lower breadcrumbs *}
{include 'linktree.tpl'}
<div class="plainbox" id="display_jump_to">&nbsp;</div>
{/block}
{block footerscripts}
{include 'topic_js.tpl'}
{/block}
