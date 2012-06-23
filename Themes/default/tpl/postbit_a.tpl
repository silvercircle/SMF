{$ID = $message.id}
{if $message.is_ignored}
  <div onclick="$(\"div.post_wrapper[data-mid={$ID}]\").show();return(false);" class="orange_container ignoringpost mediummargin">
      {$T.ignoring_user}&nbsp;
      {$T.show_ignore_user_post}
   </div>
{/if}
<div id="msg{$ID}" class="post_wrapper{($message.is_ignored) ? ' ignored' : '' }" data-mid="{$ID}">
{if $ID != $C.first_message}
  {($message.first_new) ? '<a id="new"></a>' : ''}
{/if}
<div class="keyinfo clean">
  <div>
    <span class="{($m.new) ? 'permalink_new' : 'permalink_old'}"><a onclick="getIntralink($(this), {$ID});return(false);" href="{m.permahref }" rel="nofollow">{$m.permalink}</a></span>
    Posted by: {$m.member.link }}&nbsp;
    <span class="smalltext">{$m.time }}</span>
  </div>
  <span style="display:none;" id="subject_{{ message.id }}">
    {$m.subject}
  </span>
</div>
<div id="msg_{$ID}_quick_mod"></div>
  <div class="post clear_left" style="margin:0;padding:0;" id="msg_{$ID}">
  {if $m.approved == 0 and $m.member.id != 0 and $m.member.id == $C.user.id}
    <div class="approve_post">
      {$T.post_awaiting_approval}
    </div>
  {/if}
  <article>
    {$m.body}
  </article>
  </div>
{if !empty($message.attachment)}
  <div id="msg_{$ID}_footer" class="attachments smalltext">
    <ol class="post_attachments">
    {$last_approved_state = 1}
    {foreach from=$message.attachment item=attachment}
      <li>
      {if $attachment.is_approved != $last_approved_state}
        {$last_approved_state = 0}
        <fieldset>
          <legend>{$T.attach_awaiting_approve}
          {if $C.can_approve}
            &nbsp;[<a href="{$SCRIPTURL}?action=attachapprove;sa=all;mid={$ID};{$C.session_var}={$C.session_id}">{$T.approve_all}</a>]
          {/if}
          </legend>
      {/if}
      {if $attachment.is_image}
        {if $attachment.thumbnail.has_thumb}
          <a rel="prettyPhoto[gallery]" href="{$attachment.href};image" id="link_{$attachment.id}" class="attach_thumb"><img src="{$attachment.thumbnail.href}" alt="" id="thumb_{$attachment.id}" /></a>
        {else}
          <img src="{$attachment.href};image" alt="" width="{$attachment.width}" height="{$attachment.height}"/>
        {/if}
      {/if}
      <a href="{$attachment.href}">{$attachment.name}</a><br>
      {if $attachment.is_approved == 0 and $C.can_approve}
        [<a href="{$SCRIPTURL}?action=attachapprove;sa=approve;aid={$attachment.id};{$C.session_var}={$C.session_id}">{$T.approve}</a>]&nbsp;|&nbsp;[<a href="{$SCRIPTURL}?action=attachapprove;sa=reject;aid={$attachment.id};{$C.session_var}={$C.session_id}">{$T.delete}</a>]
      {/if}
      {$attachment.size}{($attachment.is_image) ? (', '|cat:$attachment.real_width|cat:'x'|cat:$attachment.real_height|cat:'<br>'|cat:$T.attach_viewed) : ('<br>'|cat:$T.attach_downloaded|cat:' '|cat:$attachment.downloads|cat:' '|cat:$T.attach_times|cat:'.<br>')}
      </li>
    {/foreach}
    {if $last_approved_state == 0}
      </fieldset>
    {/if}
    </ol>
  </div>
{/if} {* attachments *}
<div class="moderatorbar" style="margin-left:10px;">
</div>
{if $m.likes_count > 0 or !empty($m.likelink)}
  <div class="likebar">
    <div class="floatright">{$m.likelink}</div>
    <span id="likers_msg_{$ID}">{$m.likers }}</span>
    <div class="clear_right"></div>
  </div>
{/if}
<div class="post_bottom" style="background-color:transparent;">
  <div style="display:inline;">
    <span class="modified" id="modified_{$ID}">
    {if $S.show_modify and !empty($m.modified.name)}
      <em>{$T.last_edit}: {$m.modified.time }} {$T.by} {$m.modified.name}</em>
    {/if}
    </span>
  </div>
  <div class="reportlinks">
  <ul class="floatright plainbuttonlist">
  {if $m.can_approve}
    <li><a rel="nofollow" href="{$SCRIPTURL}?action=moderate;area=postmod;sa=approve;topic={$C.current_topic}.{$C.start};msg={$ID};{$C.session_var}={$C.session_id}">{$T.approve}</a></li>
  {/if}
  {if $m.can_modify}
    <li><a rel="nofollow" onclick="oQuickModify.modifyMsg('{$ID}');return(false);" href="{$SCRIPTURL}?action=post;msg={$ID};topic={$C.current_topic}.{$C.start}">{$T.modify}</a></li>
  {/if}
  {if $m.can_remove}
    <li><a rel="nofollow" href="{$SCRIPTURL}?action=deletemsg;topic={$C.current_topic}.{$C.start};msg={$ID};{$C.session_var}={$C.session_id}" onclick="return Eos_Confirm('', '{$T.remove_message}?', $(this).attr('href'));">{$T.remove}</a></li>
  {/if}
  {if $C.can_split and $C.real_num_replies > 0}
    <li><a rel="nofollow" href="{$SCRIPTURL}?action=splittopics;topic={$C.current_topic}.0;at={$ID}">{$T.split}</a></li>
  {/if}
  {if $C.can_restore_msg}
    <li><a rel="nofollow" href="{$SCRIPTURL}?action=restoretopic;msgs={$ID};{$C.session_var}={$C.session_id}">{$T.restore_message}</a></li>
  {/if}
  {if !empty($O.display_quick_mod) and $m.can_remove}
    <li class="inline_mod_check" style="display: none;" id="in_topic_mod_check_{$ID}"></li>
  {/if}
  </ul>
  </div><div class="clear"></div></div>
</div>
