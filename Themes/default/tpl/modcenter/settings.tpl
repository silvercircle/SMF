  <div id="modcenter">
    <form action="{$SCRIPTURL}?action=moderate;area=settings" method="post" accept-charset="UTF-8">
      <h1 class="bigheader section_header">{$T.mc_prefs_title}</h1>
      <div class="orange_container cleantop mediumpadding gradient_darken_down">
        {$T.mc_prefs_desc}
      </div>
      <br>
      <div class="blue_container mediumpadding">
        <div class="content">
          <dl class="settings">
            <dt>
              <strong>{$T.mc_prefs_homepage}:</strong>
            </dt>
            <dd>
            {foreach $C.homepage_blocks as $k => $v}
              <label for="mod_homepage_{$k}"><input type="checkbox" id="mod_homepage_{$k}" name="mod_homepage[{$k}]" {($k|in_array:$C.mod_settings.user_blocks) ? ' checked="checked"' : ''} class="input_check" /> {$v}</label><br>
            {/foreach}
            </dd>
            {if $C.can_moderate_boards}
              <dt>
                <strong><label for="mod_show_reports">{$T.mc_prefs_show_reports}</label>:</strong>
              </dt>
              <dd>
                <input type="checkbox" id="mod_show_reports" name="mod_show_reports" {($C.mod_settings.show_reports) ? 'checked="checked"' : ''} class="input_check" />
              </dd>
              <dt>
                <strong><label for="mod_notify_report">{$T.mc_prefs_notify_report}</label>:</strong>
              </dt>
              <dd>
                <select id="mod_notify_report" name="mod_notify_report">
                  <option value="0" {($C.mod_settings.notify_report == 0) ? 'selected="selected"' : ''}>{$T.mc_prefs_notify_report_never}</option>
                  <option value="1" {($C.mod_settings.notify_report == 1) ? 'selected="selected"' : ''}>{$T.mc_prefs_notify_report_moderator}</option>
                  <option value="2" {($C.mod_settings.notify_report == 2) ? 'selected="selected"' : ''}>{$T.mc_prefs_notify_report_always}</option>
                </select>
              </dd>
            {/if}
            {if $C.can_moderate_approvals}
              <dt>
                <strong><label for="mod_notify_approval">{$T.mc_prefs_notify_approval}</label>:</strong>
              </dt>
              <dd>
                <input type="checkbox" id="mod_notify_approval" name="mod_notify_approval" {($C.mod_settings.notify_approval) ? 'checked="checked"' : ''} class="input_check" />
              </dd>
            {/if}
          </dl>
          <div class="righttext">
            <input type="hidden" name="{$C.session_var}" value="{$C.session_id}" />
            <input type="submit" name="save" value="{$T.save}" class="button_submit" />
          </div>
        </div>
      </div>
    </form>
  </div>
  <br class="clear" />
