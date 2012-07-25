{function attachments}
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
      {$attachment.size}{($attachment.is_image) ? (', '|cat:$attachment.real_width|cat:'x'|cat:$attachment.real_height|cat:'<br>'|cat:$T.attach_viewed|cat:': '|cat:$attachment.downloads|cat:' '|cat:$T.attach_times) : ('<br>'|cat:$T.attach_downloaded|cat:' '|cat:$attachment.downloads|cat:' '|cat:$T.attach_times|cat:'.<br>')}
      </li>
    {/foreach}
    {if $last_approved_state == 0}
      </fieldset>
    {/if}
    </ol>
  </div>
{/function}

{function postbit_n}
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
<div class="keyinfo std gradient_darken_down">
  <div class="floatleft" style="width:200px;text-align:center;margin-right:20px;" itemscope="itemscope" itemtype="http://data-vocabulary.org/Person">
    <h4 class="poster">{$message.member.link}</h4>
  </div>
  <div class="messageicon">
    <img src="{$message.icon_url}" alt="" {($message.can_modify) ? (' class="iconrequest" id="micon_'|cat:$ID|cat:'"') : ''} />
  </div>
  <h5 style="display:inline;" id="subject_{$ID}">
    {$message.subject}
  </h5>
  <span class="tinytext {($message.new) ? 'permalink_new' : 'permalink_old'}"><a {($C.perma_request) ? '' : ('onclick="getIntralink($(this),'|cat:$ID|cat:');return(false);"')} href="{$message.permahref}" rel="nofollow">{$message.permalink}</a></span>
  <span class="tinytext">&nbsp;{$message.time}</span>
  <div id="msg_{$ID}_quick_mod"></div>
</div>
<div class="clear"></div>
{* Show information about the poster of this message. *}
<div class="poster std">
  <ul class="reset tinytext" id="msg_{$ID}_extra_info">
  {if !$message.member.is_guest}
    {if $S.show_user_images and empty($O.show_no_avatars)}
      {if !empty($message.member.avatar.image)}
        <li class="avatar">
          {$message.member.avatar.image}
        </li>
      {else}
        <li class="avatar">
          <img src="{$S.images_url}/unknown.png" alt="avatar" />
        </li>
      {/if}
    {/if}
    <li class="membergroup">{$message.member.group_stars}</li>
    {if !empty($message.member.title)}
      <li class="title">{$message.member.title}</li>
    {/if}
    {if $M.karmaMode == '1'}
      <li class="karma">{$M.karmaLabel} {$message.member.karma.good - $message.member.karma.bad}</li>
    {elseif $M.karmaMode == '2'}
      <li class="karma">{$M.karmaLabel} +{$message.member.karma.good}/-{$message.member.karma.bad}</li>
    {/if}
    {if !empty($message.member.blurb)}
      <li class="blurb">{$message.member.blurb}</li>
    {/if}
    {if !empty($message.member.custom_fields)}
      {$shown = false}
      {foreach from=$message.member.custom_fields item=custom}
        {if $custom.placement == 1 and !empty($custom.value)}
          {if shown == false}
            {$shown = true}
            <li class="im_icons">
            <ul>
          {/if}
          <li>{$custom.value}</li>
        {/if}
      {/foreach}
      {if $shown}
        </ul>
        </li>
      {/if}
    {/if} 
    {if !empty($message.member.custom_fields)}
      {foreach from=$message.member.custom_fields item=custom}
        {if empty($custom.placement) and !empty($custom.value)}
          <li class="custom">{$custom.title}: {$custom.value}</li>
        {/if}
      {/foreach}
    {/if}
    {* Are we showing the warning status? *}
    {if $message.member.can_see_warning}
      <li class="warning">{($C.can_issue_warning) ? ('<a href="'|cat:$SCRIPTURL|cat:'?action=profile;area=issuewarning;u='|cat:$message.member.id|cat:'">') : ''}<img src="{$S.images_url }}/warning_{$message.member.warning_status}.gif" alt="{$message.member.warning_status_desc}" />{($C.can_issue_warning) ? '</a>' : ''}<span class="warn_{$message.member.warning_status}">{$message.member.warning_status_desc1}</span></li>
    {/if}
    {if !empty($M.onlineEnable) and $message.member.is_guest == 0 and $message.member.online.is_online}
      <li><br>{($C.can_send_pm) ? "<a href=\"{$message.member.online.href}\">" : '' }{$message.member.online.text}{($C.can_send_pm) ? '</a>' : ''}</li>
    {/if}
  {elseif $message.member.allow_show_email}
    <li class="email"><a href="{$SCRIPTURL}?action=emailuser;sa=email;msg={$ID}" rel="nofollow">{$T.email}</a></li>
  {/if} {* is guest *}
  </ul>
  {$message.template_hook.poster_details}
</div>
<div class="post_content std">
<div class="post" id="msg_{$ID}">
<article>
{$message.body}
</article>
</div>
{if !empty($message.attachment)}
{call attachments}
{/if}
<div class="moderatorbar">
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
  <div class="signature" id="msg_{$ID}_signature">__<br>{$message.member.signature}</div>
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
  <div class="clear_left"></div>
</div>
<div class="post_bottom{($message.mq_marked) ? ' mq' : ''}">
  <div class="reportlinks lefttext">
    {call quickbuttons}
  </div>
</div>
{$SUPPORT->displayHook('postbit_below')}
{$message.template_hook.postbit_below}
</div>
{/function}

{function postbit_l}
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
  <div class="keyinfo lean tinytext gradient_darken_down" style="line-height:19px;">
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
    <span class="{($message.new) ? 'permalink_new' : 'permalink_old'}"><a onclick="getIntralink($(this),{$ID});return(false);" href="{$message.permahref}" rel="nofollow">{$message.permalink}</a></span>
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
    {call attachments}
  {/if}
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
  {call quickbuttons}
  </div>
  <div class="clear"></div>
</div>
</div>
{/function}

{function postbit_c}
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
  {call attachments}
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
{/function}

{function postbit_a}
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
  {call attachments}
{/if}
<div class="moderatorbar" style="margin-left:10px;">
</div>
{if $message.likes_count > 0 or !empty($message.likelink)}
  <div class="likebar">
    <div class="floatright">{$message.likelink}</div>
    <span id="likers_msg_{$ID}">{$message.likers}</span>
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
{/function}