{$imgsrc = $C.clip_image_src}
<div class="post_wrapper" data-mid="{$message.id}">
  <div class="keyinfo std gradient_darken_down">
    <div class="floatleft" style="width:200px;text-align:center;margin-right:20px;" itemscope="itemscope" itemtype="http://data-vocabulary.org/Person">
      <h4 class="poster">{$message.member.link}</h4>
    </div>
  <h5 style="display:inline;" id="subject_{$message.id}">
    {$message.subject}
  </h5>
  <span class="tinytext">&nbsp;&nbsp;-&nbsp;&nbsp;{$T.sent_to}:&nbsp;
    {if !empty($message.recipients.to)}
      {', '|implode:$message.recipients.to}
    {elseif $context.folder != 'sent'}
      ({$T.pm_undisclosed_recipients})
    {/if}
    {$message.time}
  </span>
  {if $C.folder != 'sent' and !empty($C.currently_using_labels) and $C.display_mode}
    <div class="labels righttext floatright">
    {if !empty($C.currently_using_labels)}
      <select name="pm_actions[{$message.id}]" onchange="if (this.options[this.selectedIndex].value) form.submit();">
        <option value="">{$T.pm_msg_label_title}:</option>
        <option value="" disabled="disabled">---------------</option>
        {if !$message.fully_labeled}
          <option value="" disabled="disabled">{$T.pm_msg_label_apply}:</option>
          {foreach $C.labels as $label}
            {if !isset($message.labels.$label.id)}
              <option value="{$label.id}">&nbsp;{$label.name}</option>
            {/if}
          {/foreach}
        {/if}
        {$minusone = -1}
        {if !empty($message.labels) and (count($message.labels) > 1 or !isset($message.labels.$minusone))}
          <option value="" disabled="disabled">{$T.pm_msg_label_remove}:</option>
          {foreach $message.labels as $label}
            <option value="{$label.id}">&nbsp;{$label.name}</option>
          {/foreach}
        {/if}
      </select>
      <noscript>
        <input type="submit" value="{$T.pm_apply}" class="button_submit" />
      </noscript>
    {/if}
    </div>
  {/if}
  {if !empty($message.recipients.bcc)}
    <br>
    <span class="smalltext">&#171; <strong> {$T.pm_bcc}:</strong> {', '|implode:$message.recipients.bcc}</span>
  {/if}
  </div>
  <div class="clear"></div>
  <div class="poster std">
    <ul class="reset smalltext" id="msg_{$message.id}_extra_info">
    {if !empty($message.member.title)}
      <li class="title">{$message.member.title}</li>
    {/if}
    {if !empty($message.member.group)}
      <li class="membergroup">{$message.member.group}</li>
    {/if}
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
      {* todo: here goes reputation...
      {if $M.karmaMode == '1'}
        <li class="karma">{$M.karmaLabel} {$message.member.karma.good - $message.member.karma.bad}</li>
      {elseif $M.karmaMode == '2'}
        <li class="karma">{$M.karmaLabel} +{$message.member.karma.good}/-{$message.member.karma.bad}</li>
      {/if}
      *}
      {if !empty($message.member.blurb)}
        <li class="blurb">{$message.member.blurb}</li>
      {/if}
      {if !empty($message.member.custom_fields)}
        {$shown = false}
        {foreach from=$message.member.custom_fields item=custom}
          {if $custom.placement == 1 and !empty($custom.value)}
            {if $shown == false}
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
    {elseif !empty($message.member.allow_show_email)}
      <li class="email"><a href="{$SCRIPTURL}?action=emailuser;sa=email;msg={$ID}" rel="nofollow">{$T.email}</a></li>
    {/if} {* is guest *}
    {$SUPPORT->displayHook('pmbit_extend_userblock')}
    </ul>
  </div>
  <div class="post_content std">
    {if !empty($message.is_replied_to)}
      <div style="margin:3px;" class="flat_container tinytext lowcontrast">{$T.pm_is_replied_to}</div>
    {/if}
    <div class="post">
      <div class="inner" id="msg_{$message.id}">
        {$message.body}
      </div>
      {if !empty($message.member.custom_fields)}
        {$shown = false}
        {foreach $message.member.custom_fields as $custom}
          {if $custom.placement != 2 or empty($custom.value)}
            {continue}
          {/if}
          {if !$shown}
            {$shown = true}
            <div class="custom_fields_above_signature">
            <ul class="reset nolist">
          {/if}
          <li>{$custom.value}</li>
        {/foreach}
        {if $shown}
          </ul>
        </div>
        {/if}
      {/if}
      {if !empty($message.member.signature) and empty($O.show_no_signatures) and $C.signature_enabled}
        <br>
        <div class="signature">
          {$message.member.signature}
        </div>
      {/if}
    </div>
   </div>
  <div class="post_bottom">
    <ul class="floatright plainbuttonlist">
    {if $C.can_send_pm}
      {$label = ($C.current_label_id != -1) ? (';l='|cat:$C.current_label_id) : ''}
      {if !$message.member.is_guest}
        {if $message.number_recipients > 1 and $C.display_mode != 2}
          <li>
            <a href="{$SCRIPTURL}?action=pm;sa=send;f={$C.folder}{$label};pmsg={$message.id};quote;u=all">{$T.reply_to_all}</a>
          </li>
        {/if}
        <li>
          <a href="{$SCRIPTURL}?action=pm;sa=send;f={$C.folder}{$label};pmsg={$message.id};u={$message.member.id}">
            <div class="csrcwrapper16px">
              <img class="clipsrc reply" src="{$imgsrc}" alt="{$T.reply}" title="{$T.reply}" />
            </div>
          </a>
        </li>
        <li>
          <a href="{$SCRIPTURL}?action=pm;sa=send;f={$C.folder}{$label};pmsg={$message.id};quote{($C.folder == 'sent') ? '' : (';u='|cat:$message.member.id)}">
            <div class="csrcwrapper16px">
              <img class="clipsrc mquote_add" src="{$imgsrc}" alt="{$T.quote}" title="{$T.quote}" />
            </div>
          </a>
        </li>
      {else}
        <li class="forward_button">
          <a href="{$SCRIPTURL}?action=pm;sa=send;f={$C.folder}{$label};pmsg={$message.id};quote">{$T.reply_quote}</a></li>
      {/if}
    {/if}
    <li class="remove_button">
      <a href="{$SCRIPTURL}?action=pm;sa=pmactions;pm_actions[{$message.id}]=delete;f={$C.folder};start={$C.start}{($C.current_label_id != -1) ? (';l='|cat:$C.current_label_id) : ''};{$C.session_var}={$C.session_id}" onclick="return Eos_Confirm('', '{$T.remove_message}?', $(this).attr('href'));">
        <div class="csrcwrapper16px">
          <img class="clipsrc remove" src="{$imgsrc}" alt="{$T.remove}" title="{$T.remove}" />
        </div>
      </a>
    </li>
    {if empty($C.display_mode)}
      <li class="inline_mod_check">
        <input type="checkbox" name="pms[]" id="deletedisplay{$message.id}" value="{$message.id}" onclick="document.getElementById('deletelisting{$message.id}').checked = this.checked;" class="input_check it_check" />
      </li>
    {/if}
    </ul>
    {if !empty($M.enableReportPM) and $C.folder != 'sent'}
      <a href="{$SCRIPTURL}?action=pm;sa=report;l={$C.current_label_id};pmsg={$message.id}">
        <div class="csrcwrapper16px floatleft padded">
          <img class="clipsrc reporttm" src="{$imgsrc}" alt="{$T.pm_report_to_admin}" title="{$T.pm_report_to_admin}" />
        </div>
      </a>
    {/if}
    <div class="clear"></div>
  </div>
</div>
