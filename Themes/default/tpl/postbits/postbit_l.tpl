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
  <div class="keyinfo lean tinytext" style="line-height:19px;">
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
  <div>
    <div class="messageicon">
      <img src="{$message.icon_url}" alt="" {($message.can_modify) ? (' id="msg_icon_'|cat:$ID|cat:'"') : ''} />
    </div>
    <h5 style="display:inline;" id="subject_{$ID}">
      {$message.subject}
    </h5>
    <span class="{($message.new) ? 'permalink_new' : 'permalink_old'}"><a onclick="getIntralink($(this),{$ID});return(false);" href="{$message.permahre}" rel="nofollow">{$message.permalink}</a></span>
    <br>{$T.posted_by}&nbsp;<strong>{$message.member.link}</strong>&nbsp;{$message.time}
    <div class="clear"></div>
  </div>
  <div id="msg_'{$ID}_quick_mod"></div>
  <div class="clear"></div>
  </div>
  <div class="post_content lean">
  <div class="post clear_left" style="padding:10px 20px;" id="msg_{$ID}">
    <article>
      {$message.body}
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
  {if !empty($message.member.custom_fields)}
    {$shown = false}
    {foreach from=$message.member.custom_fields item=custom}
      {if $custom.placement == 2 and !empty($custom.value)}
        {if $shown == false}
          {$shown = true}
          <div class="custom_fields_above_signature">
          <ul class="reset nolist">
        {/if}
        <li>{$custom.value}</li>
      {/if}
    {/foreach}
    {if $shown}
     </ul>
    </div>
    {/if}
  {/if}
  {$message.template_hook.before_sig}
  {if !empty($message.member.signature) and empty($O.show_no_signatures) and $C.signature_enabled}
    <div class="signature" id="msg_{$ID}_signature">{$message.member.signature}</div>
  {/if}
  {$message.template_hook.after_sig}
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
  {call quickbuttons m=$message}
  </div>
  <div class="clear"></div>
</div>
</div>