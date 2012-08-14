{include "generics/list.tpl"}
{include "generics/topiclist.tpl"}
<div class="cat_bar2">
  <h3>
    <span class="ie6_header floatleft">{$T.notifications_header}</span>
  </h3>
</div>
<div class="orange_container cleantop mediumpadding">{$T.notification_info}</div><br>
{call collapser id='notify_settings_profile' title=$T.notifications_settings}
<div class="content">
  <form action="{$SCRIPTURL}?action=profile;area=notification;save" method="post" accept-charset="UTF-8" id="notify_options" class="flow_hidden">
  {if !empty($M.allow_disableAnnounce)}
    <input type="hidden" name="notify_announcements" value="0" />
    <label for="notify_announcements"><input type="checkbox" id="notify_announcements" name="notify_announcements"{(!empty($C.member.notify_announcements)) ? ' checked="checked"' : ''} class="input_check" /> {$T.notify_important_email}</label>
    <br>
  {/if}
  <input type="hidden" name="default_options[auto_notify]" value="0" />
  <label for="auto_notify"><input type="checkbox" id="auto_notify" name="default_options[auto_notify]" value="1"{(!empty($C.member.options.auto_notify)) ? ' checked="checked"' : ''} class="input_check" /> {$T.auto_notify}</label>
  <br>
  {if empty($M.disallow_sendBody)}
    <input type="hidden" name="notify_send_body" value="0" />
    <label for="notify_send_body"><input type="checkbox" id="notify_send_body" name="notify_send_body"{(!empty($C.member.notify_send_body)) ? ' checked="checked"' : ''} class="input_check" /> {$T.notify_send_body}</label>
    <br>
  {/if}
  <br>
  <label for="notify_regularity">{$T.notify_regularity}:</label>
  <select name="notify_regularity" id="notify_regularity">
    <option value="0"{($C.member.notify_regularity == 0) ? ' selected="selected"' : ''}>{$T.notify_regularity_instant}</option>
    <option value="1"{($C.member.notify_regularity == 1) ? ' selected="selected"' : ''}>{$T.notify_regularity_first_only}</option>
    <option value="2"{($C.member.notify_regularity == 2) ? ' selected="selected"' : ''}>{$T.notify_regularity_daily}</option>
    <option value="3"{($C.member.notify_regularity == 3) ? ' selected="selected"' : ''}>{$T.notify_regularity_weekly}</option>
  </select>
  <br><br>
  <label for="notify_types">{$T.notify_send_types}:</label>
  <select name="notify_types" id="notify_types">
    <option value="1"{($C.member.notify_types == 1) ? ' selected="selected"' : ''}>'{$T.notify_send_type_everything}</option>
    <option value="2"{($C.member.notify_types == 2) ? ' selected="selected"' : ''}>'{$T.notify_send_type_everything_own}</option>
    <option value="3"{($C.member.notify_types == 3) ? ' selected="selected"' : ''}>'{$T.notify_send_type_only_replies}</option>
    <option value="4"{($C.member.notify_types == 4) ? ' selected="selected"' : ''}>'{$T.notify_send_type_nothing}</option>
  </select>
  <br class="clear">
  <div class="floatright smallpadding">
    <input id="notify_submit" type="submit" value="{$T.notify_save}" class="default" />
    {$C.hidden_sid_input}
    <input type="hidden" name="u" value="{$C.id_member}" />
    <input type="hidden" name="sa" value="{$C.menu_item_selected}" />
  </div>
  <br class="clear">
</form>
</div>
</div>
<br>
{call topiclist id='topiclist'}
<br>
{call show_list list_id='board_notification_list'}