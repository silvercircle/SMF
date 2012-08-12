<form action="{$SCRIPTURL}?action=pm;sa=report;l={$C.current_label_id}" method="post" accept-charset="UTF-8">
  <input type="hidden" name="pmsg" value="{$C.pm_id}" />
    <div class="cat_bar">
      <h3>{$T.pm_report_title}</h3>
    </div>
    <div class="orange_container cleantop norounded mediumpadding smalltext">
      {$T.pm_report_desc}
    </div>
    <br>
    <div class="blue_container">
      <div class="content">
        <dl class="settings">
        {if $C.admin_count > 1}
          <dt>
            <strong>{$T.pm_report_admins}:</strong>
          </dt>
          <dd>
            <select name="ID_ADMIN">
              <option value="0">{$T.pm_report_all_admins}</option>
              {foreach $C.admins as $id => $name}
                <option value="{$id}">{$name}</option>
              {/foreach}
            </select>
          </dd>
        {/if}
        <dt>
          <strong>{$T.pm_report_reason}:</strong>
        </dt>
        <dd>
          <textarea name="reason" rows="4" cols="70" style="{($C.browseris_ie8) ? 'width: 635px; max-width: 80%; min-width: 80%' : 'width: 80%'};"></textarea>
        </dd>
        </dl>
        <div class="righttext">
          <input type="submit" name="report" value="{$T.pm_report_message}" class="default" />
        </div>
      </div>
    </div>
  {$C.hidden_sid_input}
</form>
