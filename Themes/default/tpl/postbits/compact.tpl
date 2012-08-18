{**
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
 *}
{function postbit_compact}
  {$ID = $message.id}
  {$imgsrc = $C.clip_image_src}
  <div class="post_wrapper" data-mid="{$ID}">
  {if !empty($C.is_display_std)}
    {$message.can_quote = $C.can_quote}
    {$message.can_reply = $C.can_reply}
    {$message.can_delete = $message.can_remove}
    {$message.can_mark_notify = $C.can_mark_notify}
    {$message.topic.id = $C.current_topic}
    {$message.board.link = ''}
  {/if}
  {if !empty($C.first_message) and $message['id'] != $C.first_message}
    <a id="msg{$ID}"></a>{($message.first_new) ? '<a id="new"></a>' : ''}
  {/if}
  <div class="keyinfo std">
    <div class="messageicon floatleft">
      &nbsp;&nbsp;<img src="{$message.icon_url}" alt="" />&nbsp;&nbsp;
    </div>
    <h3 style="display:inline;" id="subject_{$ID}">
      {$message.subject}
    </h3>
    <span class="smalltext">&nbsp;{$message.time}</span>
    <span class="tinytext floatright"><a href="{$message.permahref}" rel="nofollow">{$message.permalink}</a></span>
    <div id="msg_{$ID}_quick_mod"></div>
  </div>
  <div class="post_content lean">
  {if !empty($message.member)}
    <div class="spacer_h"></div>
    <div class="smalltext" style="line-height:19px;">
    {if !empty($message.sequence_number)}
      <div class="floatright"><strong style="font-size:1.5em;">#{$message.counter}</strong></div>
    {/if}
    {if !empty($S.show_user_images) and empty($O.show_no_avatars)}
      {if !empty($message.member.avatar.image)}
        <span class="small_avatar floatleft">
          {$message.member.avatar.image}
        </span>
      {else}
        <span class="small_avatar floatleft">
          <img src="{$S.images_url}/unknown.png" alt="avatar" />
        </span>
      {/if}
    {/if}
    &nbsp;{$T.posted_by}&nbsp;<strong>{$message.member.link}</strong>
    {if !isset($C.is_display_std)}
      &nbsp;{$T.in}&nbsp;{$message.topic.link}&nbsp;({$T.started_by}&nbsp;<strong>{$message.first_poster.link}</strong>&nbsp;{$message.first_poster.time}<br>
      &nbsp;{$T.board}:&nbsp;<strong>{$message.board.link}</strong><br>
    {/if}
    <div class="clear"></div>
  </div>
  <hr class="dashed" />
  {/if}
  <div class="post fontstyle_{$U.font_class}" id="msg_{$ID}">
  {if isset($message.approved)}
    {if !$message.approved and $message.member.id != 0 and $message.member.id == $C.user.id}
    <div class="approve_post">
      {$T.post_awaiting_approval}
    </div>
    {/if}
  {/if}
  <article>
    {$message.body}
  </article>
  </div>
  <div class="moderatorbar">
  {if (isset($message.likes_count) and $message.likes_count > 0) || !empty($message.likelink)}
    <div class="likebar">
      <div class="floatright">{$message.likelink}</div>
      <span id="likers_msg_{$ID}">{$message.likers}</span>
     <div class="clear"></div>
    </div>
  {/if}
  </div>
  </div>
  <div class="post_bottom">
    <div style="display:inline;">
      <ul class="floatright plainbuttonlist">
      {if $message.can_quote or $message.can_reply}
        <li>
          <a rel="nofollow" role="button" href="{$SCRIPTURL}?action=post;quote={$ID};topic={$message.topic.id}.{$C.start}">
            <div class="csrcwrapper16px"><img class="clipsrc reply" src="{$imgsrc}" alt="{$T.quote}" title="{$T.quote}" /></div>
          </a>
        </li>
      {/if}
      {if $message.can_mark_notify}
        <li>
          <a rel="nofollow" role="button" href="{$SCRIPTURL}?action=notify;topic={$message.topic.id}.{$C.start}">
            <div class="csrcwrapper16px"><img class="clipsrc subscribe" src="{$imgsrc}" alt="{$T.notify}" title="{$T.notify}" /></div>
          </a>
        </li>
      {/if}
      {if $message.can_delete}
        <li>
          <a rel="nofollow" href="{$SCRIPTURL}?action=deletemsg;topic={$message.topic.id}.{$C.start};msg={$ID};{$C.session_var}={$C.session_id}" onclick="return Eos_Confirm('', '{$T.remove_message}?', $(this).attr('href'));">
            <div class="csrcwrapper16px"><img class="clipsrc remove" src="{$imgsrc}" alt="{$T.remove}" title="{$T.remove}" /></div>
          </a>
        </li>
      {/if}
      </ul>
      <span class="modified" id="modified_{$ID}">
        {if $S.show_modify and !empty($message.modified.name)}
          <em>{$T.last_edit}: {$message.modified.time} {$T.by} {$message.modified.name}</em>
        {/if}
      </span>
    </div>
    <div class="clear"></div>
  </div>
  <div class="clear"></div>
</div>
{/function}