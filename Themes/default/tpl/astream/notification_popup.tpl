<?xml version="1.0" encoding="UTF-8" ?>
{include 'astream/bits.tpl'}
<document>
 <response open="private_handler" fn="_h_notify_popup" width="500px" offset="-150" />
 <content>
 <![CDATA[ <!-- > -->
  <div class="blue_container blue_topbar light_shadow notifications" id="notificationsBody">
  {if $C.act_results}
    <ol id="notifylist" class="commonlist notifications" style="max-height:400px;overflow:auto;">
      <li class="header"><h1 class="bigheader secondary smallpadding">{$T.act_recent_notifications}</h1></li>
      {$C.alt_row = false}
      {foreach $C.activities as $activity}
        {call activitybit a=$activity}
        {$C.alt_row = !$C.alt_row}
      {/foreach}
    </ol>
  {else if $C.unread_pm == 0 and $C.open_mod_reports == 0}
    <div class="smallpadding">
      <h1 class="bigheader secondary">
        {$T.act_no_unread_notifications}
      </h1>
    </div>
  {/if}
  {if $C.unread_pm > 0 or $C.open_mod_reports > 0}
    <ol class="commonlist notifications" style="max-height:400px;overflow:auto;">
    {if $C.unread_pm > 0}
      <li class="header"><h1 class="bigheader secondary">{$T.personal_messages}</h1></li>
      <li class="unread borderless"><a href="{$C.pmlink}">{$T.show_personal_messages|sprintf:$C.unread_pm}</a></li>
    {/if}
    {if !empty($C.open_mod_reports)}
      <li class="header"><h1 class="bigheader secondary">Moderation center</h1></li>
      <li class="unread borderless"><a href="{$C.modlink}">{$T.mod_reports_waiting|sprintf:$C.open_mod_reports}</a></li>
    {/if}
    </ol>
  {/if}
  <div class="yellow_container smalltext cleantop">
    <dl class="common">
    <dt>
    {if $C.act_results}
      <a onclick="markAllNotificationsRead();return(false);" href="{$SCRIPTURL}?action=astream;sa=markread;act=all">{$T.act_mark_all_read}</a>
    {/if}
    </dt>
    <dd class="righttext">
      <a href="{$SCRIPTURL}?action=astream;sa=notifications;view=all">{$T.act_view_all}</a>
    </dd>
    </dl>
  </div>
  {$SUPPORT->displayHook('notification_popup')}
  </div>
  <div class="clear"></div>
  {call notifications_scripts}
  ]]>
  </content>
  <handler>
  <![CDATA[
    function _h_notify_popup(content, data)
    {
      var wrapper = $('<div id="notify_wrapper" class="popup_wrapper" style="float:right;position:relative;margin-right:20px;"></div>');
      wrapper.html(content);
      $('#notification_target').after(wrapper);
      $('div#notify_wrapper abbr.timeago').timeago();
      $('#notificationsBody').live('mouseleave',function(event) {
        $('#notify_wrapper').remove();
      });
      return;
    }
 ]]>
 </handler>
</document>