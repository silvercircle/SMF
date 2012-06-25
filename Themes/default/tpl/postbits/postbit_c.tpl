{$ID = $message.id}
{if $message.is_ignored}
  <div onclick="$('div.post_wrapper[data-mid={$ID}]').show();return(false);" class="orange_container ignoringpost norounded">
      {$T.ignoring_user}&nbsp;
      {$T.show_ignore_user_post}
   </div>
{/if}
<div id="msg{$ID}" class="post_wrapper{($message.is_ignored) ? ' ignored' : '' }" data-mid="{$ID}">
{if $ID != $C.first_message}
  {($message.first_new) ? '<a id="new"></a>' : ''}
{/if}
<div class="keyinfo commentstyle">
  <div class="floatleft " style="max-width:65px;">
    <ul class="reset smalltext" id="msg_{$ID}_extra_info">
    {if !empty($message.member.avatar.image)}
      <li class="medium_avatar">
        {$message.member.avatar.image}
      </li>
    {else}
      <li class="medium_avatar">
        <img src="{$S.images_url}/unknown.png" alt="avatar" />
      </li>
    {/if}
    </ul>
  </div>
  <div>
    {$message.member.link} said<span class="smalltext">&nbsp;{$message.time}</span>&nbsp;-&nbsp;{$message.subject}
    <span class="{($message.new) ? 'permalink_new' : 'permalink_old'}"><a onclick="getIntralink($(this), {$ID});return(false);" href="{$message.permahref}" rel="nofollow">{$message.permalink}</a></span>
    <div id="msg_{$ID}_quick_mod"></div>
  </div>
</div>
<div class="post_content commentstyle">
{if $message.approved == 0 and $message.member.id != 0}
    <div class="red_container mediumpadding mediummargin">
      {$T.post_awaiting_approval}&nbsp;&nbsp;<a onclick="$('#msg_{$ID}').show();return(false);" href="#!">Show me the message</a>
    </div>
{/if}
<div class="post" id="msg_{$ID}" {($message.approved) ? '' : ' style="display:none;"'}>
  <article>
    {$message.body}
  </article>
</div>
{if !empty($message.attachment)}
{include 'postbits/attachments.tpl'}
{/if}
<div class="moderatorbar">
{if $message.likes_count > 0 or !empty($message.likelink)}
  <div class="likebar">
    <div class="floatright">{$message.likelink}</div>
    <span id="likers_msg_{$ID}">{$message.likers}</span>
    <div class="clear_right"></div>
  </div>
{/if}
  {if $S.show_modify and $message.modified.name != ''}
    <div class="orange_container norounded smallpadding tinytext" id="modified_{$ID}">
      {$T.last_edit}: {$message.modified.time} {$T.by} {$message.modified.name}
    </div>
  {/if}
</div>
</div>
<div class="post_bottom">
  <div class="reportlinks">
    {call quickbuttons}
  </div>
  <div class="clear"></div>
</div>
</div>
