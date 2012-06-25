{$ID = $message.id}
{if $message.is_ignored}
  <div onclick="$('div.post_wrapper[data-mid={$ID}]').show();return(false);" class="orange_container ignoringpost norounded">
      {$T.ignoring_user}&nbsp;
      {$T.show_ignore_user_post}
   </div>
{/if}
<div id="msg{$ID}" data-mid="{$ID}">
{if $ID != $C.first_message}
  {($message.first_new) ? '<a id="new"></a>' : ''}
{/if}
<div class="keyinfo clean">
  <div>
    <span class="{($message.new) ? 'permalink_new' : 'permalink_old'}"><a onclick="getIntralink($(this), {$ID});return(false);" href="{$message.permahref}" rel="nofollow">{$message.permalink}</a></span>
    Posted by: {$message.member.link}&nbsp;
    <span class="smalltext">{$message.time}</span>
  </div>
  <span style="display:none;" id="subject_{$ID}">
    {$message.subject}
  </span>
</div>
<div id="msg_{$ID}_quick_mod"></div>
  <div class="post clear_left" style="margin:0;padding:0;" id="msg_{$ID}">
  {if $message.approved == 0 and $message.member.id != 0 and $message.member.id == $C.user.id}
    <div class="approve_post">
      {$T.post_awaiting_approval}
    </div>
  {/if}
  <article>
    {$message.body}
  </article>
  </div>
{if !empty($message.attachment)}
{include 'postbits/attachments.tpl'}
{/if}
<div class="moderatorbar" style="margin-left:10px;">
</div>
{if $message.likes_count > 0 or !empty($message.likelink)}
  <div class="likebar">
    <div class="floatright">{$message.likelink}</div>
    <span id="likers_msg_{$ID}">{$message.likers }}</span>
    <div class="clear_right"></div>
  </div>
{/if}
<div class="post_bottom" style="background-color:transparent;">
  <div style="display:inline;">
    <span class="modified" id="modified_{$ID}">
    {if $S.show_modify and !empty($message.modified.name)}
      <em>{$T.last_edit}: {$message.modified.time }} {$T.by} {$message.modified.name}</em>
    {/if}
    </span>
  </div>
  <div class="reportlinks">
  <ul class="floatright plainbuttonlist">
  {if $message.can_approve}
    <li><a rel="nofollow" href="{$SCRIPTURL}?action=moderate;area=postmod;sa=approve;topic={$C.current_topic}.{$C.start};msg={$ID};{$C.session_var}={$C.session_id}">{$T.approve}</a></li>
  {/if}
  {if $message.can_modify}
    <li><a rel="nofollow" onclick="oQuickModify.modifyMsg('{$ID}');return(false);" href="{$SCRIPTURL}?action=post;msg={$ID};topic={$C.current_topic}.{$C.start}">{$T.modify}</a></li>
  {/if}
  {if $message.can_remove}
    <li><a rel="nofollow" href="{$SCRIPTURL}?action=deletemsg;topic={$C.current_topic}.{$C.start};msg={$ID};{$C.session_var}={$C.session_id}" onclick="return Eos_Confirm('', '{$T.remove_message}?', $(this).attr('href'));">{$T.remove}</a></li>
  {/if}
  {if $C.can_split and $C.real_num_replies > 0}
    <li><a rel="nofollow" href="{$SCRIPTURL}?action=splittopics;topic={$C.current_topic}.0;at={$ID}">{$T.split}</a></li>
  {/if}
  {if $C.can_restore_msg}
    <li><a rel="nofollow" href="{$SCRIPTURL}?action=restoretopic;msgs={$ID};{$C.session_var}={$C.session_id}">{$T.restore_message}</a></li>
  {/if}
  {if !empty($O.display_quick_mod) and $message.can_remove}
    <li class="inline_mod_check" style="display: none;" id="in_topic_mod_check_{$ID}"></li>
  {/if}
  </ul>
  </div><div class="clear"></div></div>
</div>
