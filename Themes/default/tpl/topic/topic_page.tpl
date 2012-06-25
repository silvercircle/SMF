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
{$C.template_hooks.display.header}
<a id="top"></a>
{($C.first_new_message) ? '<a id="new"></a>' : ''}
{* Is this topic also a poll? *}
{$C.template_hooks.display.above_posts}
{* Is this topic also a poll? *}
{if $C.is_poll}
  <br>
  <div id="poll">
    <div class="cat_bar">
      <h3>
        <span class="floatleft"><img src="{$S.images_url}/topic/{($C.poll.is_locked) ? 'normal_poll_locked' : 'normal_poll'}.gif" alt="" class="icon" />{$T.poll}</span>
      </h3>
    </div>
    <div class="blue_container cleantop">
      <div class="content" id="poll_options">
        <h4 id="pollquestion">
          {$C.poll.question}
        </h4>
        {* Are they not allowed to vote but allowed to view the options? *}
        {if $C.poll.show_results or $C.allow_vote == 0}
          <dl class="options">
          {* Show each option with its corresponding percentage bar. *}
          {foreach from=$C.poll.options item=option}
            <dt class="smalltext{($option.voted_this) ? ' voted' : ''}">{$option.option}</dt>
            <dd class="smalltext statsbar{($option.voted_this) ? ' voted' : ''}">
            {if $C.allow_poll_view}
              {$option.bar_ndt}
              <span class="percentage">{$option.votes} ({$option.percent}%)</span>
            {/if}
            </dd>
          {/foreach}
          </dl>
          {if $C.allow_poll_view}
            <p><strong>{$T.poll_total_voters}:</strong> {$C.poll.total_votes}</p>
          {/if}
        {else} {* show_results *}
          <form action="{$SCRIPTURL}?action=vote;topic={$topic}.{$C.start};poll={$C.poll.id}" method="post" accept-charset="UTF-8">
          {if $C.poll.allowed_warning}
            <p class="smallpadding">{$C.poll.allowed_warning}</p>
          {/if}
          <ul class="reset options">
          {foreach from=$C.poll.options item=option}
            <li class="smalltext">{$option.vote_button} <label for="{$option.id}">{$option.option}</label></li>
          {/foreach}
          </ul>
          <div class="submitbutton">
            <input type="submit" value="{$T.poll_vote}" class="button_submit" />
            <input type="hidden" name="{$C.session_var}" value="{$C.session_id}" />
          </div>
          </form>
        {/if} {* show_results *}
        {if !empty($C.poll.expire_time)}
          <p><strong>{($C.poll.is_expired) ? $T.poll_expired_on : $T.poll_expires_on}:</strong> {$C.poll.expire_time}</p>
        {/if}
      </div>
    </div>
  </div>
  <div id="pollmoderation">
    {$SUPPORT->button_strip($C.poll_buttons)}
  </div>
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
  <form data-alt="{$SCRIPTURL}?action=post;msg=%id_msg%;topic={$topic}.{$C.start}" action="{$SCRIPTURL}?action=quickmod2;topic={$topic}.{$C.start}" method="post" accept-charset="UTF-8" name="quickModForm" id="quickModForm" style="margin: 0;" onsubmit="return oQuickModify.bInEditMode ? oQuickModify.modifySave('{$C.session_id}', '{$C.session_var}') : false">
  {* Get all the messages... *}
  {foreach from=$C.message_ids item=msg}
    {$SUPPORT->getMessage()}
    {include 'postbits/postbit_'|cat:$message.postbit_template_class|cat:'.tpl'}
    {if $message.id == $C.first_message}
      {if !empty($C.use_share)}
        <div class="title">{$T.share_topic}:</div>
        <div id="socialshareprivacy"></div><div class="clear"></div>
      {/if}
      {if !empty($C.tags_active)}
        <div id="tagstrip" class="tinytext">
          <span id="tags">
            {foreach from=$C.topic_tags item=tag}
              <a class="tag" href="{$SCRIPTURL}?action=tags;tagid={$tag.ID_TAG}">{$tag.tag}</a>
              {if $C.can_delete_tags}
                <a href="{$SCRIPTURL}?action=tags;sa=deletetag;tagid={$tag.ID}"><span onclick="sendRequest('action=xmlhttp;sa=tags;deletetag=1;tagid={$tag.ID}', $('#tags'));return(false);" class="xtag">&nbsp;&nbsp;</span></a>
              {else}
                &nbsp;&nbsp;
              {/if}
            {/foreach}
          </span>
          {if $C.can_add_tags}
            &nbsp;<a rel="nofollow" id="addtag" onclick="$('#tagform').remove();sendRequest('action=xmlhttp;sa=tags;addtag=1;topic={$topic}', $('#addtag'));return(false);" data-id="{$topic}" href="{$SCRIPTURL}?action=tags;sa=addtag;topic={$topic}">{$T.smftags_addtag}</a>
          {else}
            &nbsp;
          {/if}
        </div>
        <br>
      {/if} {* tags_active *}
      {$C.num_replies} Replies
      <div class="clear"></div>
      <div class="posts_container commentstyle" id="posts_container">
    {/if}
  {/foreach}
 </div>
<input type="hidden" name="goadvanced" value="1" />
</form>
</div>
<a id="lastPost"></a>
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
{if $C.can_reply and !empty($O.display_quick_reply)}
  {include 'topic/quickreply.tpl'}
{/if} {* quick reply *}
<div class="pagesection bottom">
  {$SUPPORT->button_strip($C.normal_buttons, 'right')}
  {if $C.multiquote_posts_count > 0}
    <div class="floatright clear_right tinytext mediummargin mq_remove_msg">{$T.posts_marked_mq|sprintf:$C.multiquote_posts_count}&nbsp;<a href="#" onclick="return oQuickReply.clearAllMultiquote({$C.current_topic});">{$T.remove}</a></div>
  {/if}
  <div class="pagelinks floatleft">{$C.page_index} {($M.topbottomEnable) ? ($C.menu_separator|cat:' &nbsp;&nbsp;<a href="#top"><strong>'|cat:$T.go_up|cat:'</strong></a>') : ''}</div>
  <div class="nextlinks_bottom">{$C.previous_next}</div>
</div>
{include '../linktree.tpl'}
<div id="moderationbuttons">
  {$SUPPORT->button_strip($C.mod_buttons, 'bottom', $C.mod_buttons_style)}
</div>
<div class="plainbox" id="display_jump_to">&nbsp;</div>
{/block}  
{block footerscripts}
{include 'topic/topic_js.tpl'}
{/block}