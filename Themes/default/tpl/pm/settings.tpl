<dt>
  <label for="pm_prefs">{$T.pm_display_mode}:</label>
</dt>
<dd>
  <select name="pm_prefs" id="pm_prefs" onchange="if (this.value == 2 &amp;&amp; !document.getElementById('copy_to_outbox').checked) alert('{$T.pm_recommend_enable_outbox}');">
    <option value="0"{($C.display_mode == 0) ? ' selected="selected"' : ''}>{$T.pm_display_mode_all}</option>
    <option value="1"{($C.display_mode == 1) ? ' selected="selected"' : ''}>{$T.pm_display_mode_one}</option>
    <option value="2"{($C.display_mode == 2) ? ' selected="selected"' : ''}>{$T.pm_display_mode_linked}</option>
  </select>
</dd>
<dt>
  <label for="view_newest_pm_first">{$T.recent_pms_at_top}</label>
</dt>
<dd>
  <input type="hidden" name="default_options[view_newest_pm_first]" value="0" />
  <input type="checkbox" name="default_options[view_newest_pm_first]" id="view_newest_pm_first" value="1"{(!empty($C.member.options.view_newest_pm_first)) ? ' checked="checked"' : ''} class="input_check" />
</dd>
</dl>
<hr>
<dl>
  <dt>
    <label for="pm_receive_from">{$T.pm_receive_from}</label>
  </dt>
  <dd>
  <select name="pm_receive_from" id="pm_receive_from">
    <option value="0"{(empty($C.receive_from) or (empty($M.enable_buddylist) and $C.receive_from < 3)) ? ' selected="selected"' : ''}>{$T.pm_receive_from_everyone}</option>
    {if !empty($M.enable_buddylist)}
      <option value="1"{(!empty($C.receive_from) and $C.receive_from == 1) ? ' selected="selected"' : ''}>{$T.pm_receive_from_ignore}</option>
      <option value="2"{(!empty($C.receive_from) and $C.receive_from == 2) ? ' selected="selected"' : ''}>{$T.pm_receive_from_buddies}</option>
    {/if}
    <option value="3"{(!empty($C.receive_from) and $C.receive_from > 2) ? ' selected="selected"' : ''}>{$T.pm_receive_from_admins}</option>
  </select>
  </dd>
  <dt>
    <label for="pm_email_notify">{$T.email_notify}</label>
  </dt>
  <dd>
    <select name="pm_email_notify" id="pm_email_notify">
      <option value="0"{(empty($C.send_email)) ? ' selected="selected"' : ''}>{$T.email_notify_never}</option>
      <option value="1"{(!empty($C.send_email) and ($C.send_email == 1 or (empty($M.enable_buddylist) and $C.send_email > 1))) ? ' selected="selected"' : ''}>{$T.email_notify_always}</option>
      {if !empty($M.enable_buddylist)}
        <option value="2"{(!empty($C.send_email) and $C.send_email > 1) ? ' selected="selected"' : ''}>{$T.email_notify_buddies}</option>
      {/if}
    </select>
  </dd>
</dl>
<hr>
<dl>
<dt>
  <label for="copy_to_outbox">{$T.copy_to_outbox}</label>
</dt>
<dd>
  <input type="hidden" name="default_options[copy_to_outbox]" value="0" />
  <input type="checkbox" name="default_options[copy_to_outbox]" id="copy_to_outbox" value="1"{(!empty($C.member.options.copy_to_outbox)) ? ' checked="checked"' : ''} class="input_check" />
</dd>
<dt>
  <label for="pm_remove_inbox_label">{$T.pm_remove_inbox_label}</label>
</dt>
<dd>
  <input type="hidden" name="default_options[pm_remove_inbox_label]" value="0" />
  <input type="checkbox" name="default_options[pm_remove_inbox_label]" id="pm_remove_inbox_label" value="1"{(!empty($C.member.options.pm_remove_inbox_label)) ? ' checked="checked"' : ''} class="input_check" />
</dd>