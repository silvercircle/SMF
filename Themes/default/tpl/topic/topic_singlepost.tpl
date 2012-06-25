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
        {$SUPPORT->getMessage()}
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
{/if} {* quick reply *}
  {include 'topic/quickreply.tpl'}
{$C.template_hooks.display.footer}
{* Show the lower breadcrumbs *}
{include '../linktree.tpl'}
<div class="plainbox" id="display_jump_to">&nbsp;</div>
{/block}
{block footerscripts}
{include 'topic/topic_js.tpl'}
{/block}
