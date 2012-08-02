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
{include 'postbits/bits.tpl'}
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
{$C.template_hooks.display.header}
<a id="top"></a>
{($C.first_new_message) ? '<a id="new"></a>' : ''}
{* the topic info bar (topic starter info, share bar, some useful links) *}
<div class="bmbar gradient_darken_down">
  <div class="userbit_compact topic">
    <div class="floatleft">
      <span class="small_avatar">
      {if !empty($C.topicstarter.avatar.image)}
        <img class="fourtyeight" src="{$C.topicstarter.avatar.href}" alt="avatar" />
      {else}
        <img class="fourtyeight" src="{$S.images_url}/unknown.png" alt="avatar" />
      {/if}
      </span>
    </div>
    {$status_parts = array()}
    {$imgsrc = $C.clip_image_src}
    <div class="userbit_compact_textpart">
      <h1 class="bigheader topic">
        {if $C.is_locked}
          {$status_parts[] = '<div class="csrcwrapper16px floatleft"><img class="clipsrc locked" src="'|cat:$imgsrc|cat:'" alt="" title="'|cat:$T.locked_topic|cat:'" /></div>'}
        {/if}
        {if $C.is_sticky}
          {$status_parts[] = '<div class="csrcwrapper16px floatleft"><img class="clipsrc sticky" src="'|cat:$imgsrc|cat:'" alt="" title="'|cat:$T.sticky_topic|cat:'" /></div>'}
        {/if}
        <span class="floatright smalltext">{""|implode:$status_parts}</span>
        {$C.prefix}{$C.subject}&nbsp;({$T.read} {$C.num_views} {$T.times})
      </h1>
      {$T.started_by}&nbsp;{$C.topicstarter.link}, {$C.topicstarter.start_time}
      {if !empty($C.tags_active)}
        <div id="tagstrip" class="tinytext righttext">
          <span id="tags">
            {foreach from=$C.topic_tags item=tag}
              <a class="tag" href="{$SCRIPTURL}?action=tags;tagid={$tag.ID_TAG}">{$tag.tag}</a>
              {if $C.can_delete_tags}
                <a href="{$SCRIPTURL}?action=tags;sa=deletetag;tagid={$tag.ID}"><span onclick="sendRequest('action=xmlhttp;sa=tags;deletetag=1;tagid={$tag.ID}', $('#tags'));return(false);" class="xtag">&nbsp;&nbsp;</span></a>
              {/if}
            {/foreach}
          </span>
          {if $C.can_add_tags}
            &nbsp;<a rel="nofollow" id="addtag" onclick="$('#tagform').remove();sendRequest('action=xmlhttp;sa=tags;addtag=1;topic={$topic}', $('#addtag'));return(false);" data-id="{$topic}" href="{$SCRIPTURL}?action=tags;sa=addtag;topic={$topic}">{$T.smftags_addtag}</a>
          {/if}
        </div>
        <br>
      {/if} {* tags_active *}
      {$notify_href = $SCRIPTURL|cat:'?action=notify;sa='|cat:( ($C.is_marked_notify) ? 'off' : 'on')|cat:';topic='|cat:$topic|cat:'.'|cat:$C.start|cat:';'|cat:$C.session_var|cat:'='|cat:$C.session_id}
      {$notify_confirm = "return Eos_Confirm('', '{($C.is_marked_notify) ? $T.notification_disable_topic : $T.notification_enable_topic}', $(this).attr('href'));"}
      <div class="floatright">
        {if $C.user.is_logged}
          {($C.is_marked_notify) ? ($T.you_are_subscribed|cat:', ') : ''}<a href="{$notify_href}" onclick="{$notify_confirm}">{($C.is_marked_notify) ? $T.unnotify : $T.you_are_not_subscribed}</a>
        {/if}
      </div>
      {if $S.display_who_viewing}
        <div id="whoisviewing" class="tinytext">
        {if $S.display_who_viewing == 1}
          {count($C.view_members)} {(count($C.view_members) == 1) ? $T.who_member : $T.members}
        {else}
          {$C.full_members_viewing_list}
        {/if}
        {* Now show how many guests are here too. *}
        {$T.who_and}{$C.view_num_guests} {($C.view_num_guests == 1) ? $T.guest : $T.guests}{$T.who_viewing_topic}
        </div>
      {/if}
      <div class="clear"></div>
    </div>
  </div>
  <div class="floatright tinytext righttext" style="padding:0;line-height:100%;margin:0;">
  {if $C.can_send_topic}
    <a href="{$SCRIPTURL}?action=emailuser;sa=sendtopic;topic={$topic}.0">{$T.email_topic}</a><br>
  {/if}
  <a rel="nofollow" href="{$SCRIPTURL}?action=printpage;topic={$topic}.0">{$T.view_printable}</a>
  </div>
  {if $C.use_share}
    <div class="title">{$T.share_topic}:</div>
    <div id="socialshareprivacy"></div><div class="clear"></div>
  {/if}
  <div class="clear"></div>
  {$C.template_hooks.display.extend_topicheader}
</div>
{$C.template_hooks.display.above_posts}
{* Is this topic also a poll? *}
{if $C.is_poll}
  {include "topic/polldisplay.tpl"}
{/if} {* is_poll *}
{* Does this topic have some events linked to it? *}
{if !empty($C.linked_calendar_events)}
  <div class="orange_container">
    <h3>
      {$T.calendar_linked_events}
    </h3>
    <ul class="reset">
    {foreach from=$C.linked_calendar_events item=event}
      <li>
        {($event.can_edit) ? ("<a href=\"{$event.modify_href}\"> <img src=\"{$S.images_url}/icons/modify_small.gif\" alt=\"\" title=\"{$T.modify}\" class=\"edit_event\" /></a>") : ''} <strong>{$event.title}</strong>: {$event.start_date}{($event.start_date != $event.end_date) ? (" - "|cat:$event.end_date) : ''}
      </li>
    {/foreach}
    </ul>
  </div>
{/if} {* C.linked_calendar_events *}
{* Show the page index... "Pages: [1]". *}
<div class="pagesection top">
  {if $C.multiquote_posts_count > 0}
    <div class="floatright tinytext red_container alert mediummargin mq_remove_msg">
      {$T.posts_marked_mq|sprintf:$C.multiquote_posts_count}&nbsp;<a href="#" onclick="return oQuickReply.clearAllMultiquote({$topic});">{$T.remove}</a>
    </div>
  {/if}        
  <div class="nextlinks">{$C.previous_next}</div>
  {$SUPPORT->button_strip($C.normal_buttons, 'right')}
  <div class="pagelinks floatleft">{$C.page_index}{$C.menu_separator}&nbsp;&nbsp;<a class="navPages topdown" href="#lastPost">{$T.go_down}</a></div>
</div>
{* Show the topic information - icon, subject, etc. *}
<div id="forumposts">
  <form data-alt="{$SCRIPTURL}?action=post;msg=%id_msg%;topic={$topic}.{$C.start}" action="{$SCRIPTURL}?action=quickmod2;topic={$topic}.{$C.start}" method="post" accept-charset="UTF-8" name="quickModForm" id="quickModForm" style="margin: 0;" onsubmit="return oQuickModify.bInEditMode ? oQuickModify.modifySave('{$C.session_id}', '{$C.session_var}') : false">
    <div class="posts_container nopadding" id="posts_container">
      {* Get all the messages... *}
      {$C.alternate = 0}
      {foreach from=$C.message_ids item=msg}
        {$SUPPORT->getMessage()}
        {*{include 'postbits/postbit_'|cat:$message.postbit_template_class|cat:'.tpl'}*}
        {call 'postbit_'|cat:$message.postbit_template_class}
        {$C.alternate = !$C.alternate}
      {/foreach}
    </div>
    <input type="hidden" name="goadvanced" value="1" />
  </form>
</div>
<a id="lastPost"></a>
{$C.template_hooks.display.below_posts}
{if $C.can_reply and !empty($O.display_quick_reply)}
  {include 'topic/quickreply.tpl'}
{/if} {* quick reply *}
{* Show the page index... "Pages: [1]". *}
<div class="pagesection bottom">
  {$SUPPORT->button_strip($C.normal_buttons, 'right')}
  {if $C.multiquote_posts_count > 0}
    <div class="floatright clear_right tinytext red_container alert mediummargin mq_remove_msg">{$T.posts_marked_mq|sprintf:$C.multiquote_posts_count}&nbsp;<a href="#" onclick="return oQuickReply.clearAllMultiquote({$C.current_topic});">{$T.remove}</a></div>
  {/if}
  <div class="pagelinks floatleft">{$C.page_index}{$C.menu_separator}&nbsp;&nbsp;<a class="navPages topdown" href="#top">{$T.go_up}</a></div>
  <div class="nextlinks_bottom">{$C.previous_next}</div>
</div>
{* Added by Related Topics *}
{if !empty($C.related_topics)}
  <h1 class="bigheader">{$T.related_topics}</h1>
  <div class="tborder topic_table">
    <table class="table_grid mlist">
      <thead>
      <tr>
      {if !empty($C.related_topics)}
        <th scope="col" class="blue_container smalltext first_th" style="width:8%;">&nbsp;</th>
        <th scope="col" class="blue_container smalltext">{$T.subject} / {$T.started_by}</th>
        <th scope="col" class="blue_container smalltext centertext" style="width:14%;">{$T.replies}</th>
        <th scope="col" class="blue_container smalltext last_th" style="width:22%;">{$T.last_post}</th>
      {else}
        <th scope="col" class="red_container smalltext first_th">&nbsp;</th>
        <th class="smalltext red_container" colspan="3"><strong>{$T.msg_alert_none}</strong></th>
        <th scope="col" class="red_container smalltext last_th" width="8%">&nbsp;</th>
      {/if}
      </tr>
      </thead>
      <tbody>
      {foreach from=$C.related_topics item=topic}
        {$color_class = 'gradient_darken_down'}
        <tr>
          <td class="icon1 {$color_class}">
            <img src="{$S.images_url}/topic/{$topic.class}.gif" alt="" />
          </td>
          <td class="subject {$color_class}">
            <div {(!empty($topic.quick_mod.modify)) ? ("id=\"topic_{$topic.first_post.id}\" onmouseout=\"mouse_on_div = 0;\" onmouseover=\"mouse_on_div = 1;\" ondblclick=\"modify_topic('{$topic.id}', '{$topic.first_post.id}', '{$C.session_id}', '{$C.session_var}');\"") : ''}>
              {($T.is_sticky) ? '<strong>' : ''}<span id="msg_{$topic.first_post.id}">{$topic.first_post.link}{($topic.board.can_approve_posts and $topic.approved == 0) ? "&nbsp;<em>({$T.awaiting_approval})</em>" : ''}</span>{($T.is_sticky) ? '</strong>' : ''}
              {if $topic.new and $C.user.is_logged}
                <a href="{$topic.new_href}" id="newicon{$topic.first_post.id}"><img src="{$S.images_url}/new.png" alt="{$T.new}" /></a>
              {/if}
              <p>{$T.started_by} {$topic.first_post.member.link}
                <small id="pages{$topic.first_post.id}">{$topic.pages}</small>
                <small>{$topic.board.link}</small>
              </p>
            </div>
          </td>
          <td style="padding:2px 5px;" class="nowrap stats {$color_class}">
            {$topic.replies} {$T.replies}
            <br>
            {$topic.views} {$T.views}
          </td>
          <td class="lastpost {$color_class}">
            {$T.by}: {$topic.last_post.member.link}
            <br>
            <a class="lp_link" title="{$T.last_post}" href="{$topic.last_post.href}">{$topic.last_post.time}</a>
          </td>
        </tr>
      {/foreach}
    </table>
  </div>
  <br>
{/if} {* related topics *}
{$C.template_hooks.display.footer}
{* Show the lower breadcrumbs *}
{include '../linktree.tpl'}
<div class="plainbox floatright" id="display_jump_to">&nbsp;</div>
<br>
<div id="moderationbuttons">{$SUPPORT->button_strip($C.mod_buttons, 'left', $C.mod_buttons_style)}</div>
{/block}
{block footerscripts}
{include 'topic/topic_js.tpl'}
{/block}