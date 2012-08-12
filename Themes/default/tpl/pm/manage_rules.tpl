<form action="{$SCRIPTURL}?action=pm;sa=manrules" method="post" accept-charset="UTF-8" name="manRules" id="manrules">
<div class="cat_bar2">
  <h3>{$T.pm_manage_rules}</h3>
</div>
<div class="orange_container cleantop mediumpadding">
  {$T.pm_manage_rules_desc}
</div>
<br>
<table class="table_grid" style="width:100%;">
  <thead>
    <tr>
      <th class="lefttext glass first_th">
        {$T.pm_rule_title}
      </th>
      <th width="4%" class="centertext glass last_th">
        {if !empty($C.rules)}
          <input type="checkbox" onclick="invertAll(this, this.form);" class="input_check" />
        {/if}
      </th>
    </tr>
  </thead>
  <tbody>
  {if empty($C.rules)}
    <tr class="tablerow">
      <td colspan="2" class="centertext">
        {$T.pm_rules_none}
      </td>
    </tr>
  {/if}
  {$alternate = false}
  {foreach $C.rules as $rule}
    <tr class="tablerow{($alternate) ? ' alternate' : ''}">
      <td>
        <a href="{$SCRIPTURL}?action=pm;sa=manrules;add;rid={$rule.id}">{$rule.name}</a>
      </td>
      <td class="centertext">
        <input type="checkbox" name="delrule[{$rule.id}]" class="input_check" />
      </td>
    </tr>
    {$alternate = !$alternate}
  {/foreach}
  </tbody>
</table>
<div class="righttext floatright mediumpadding">
  <ul class="buttonlist">
    <li>
      <a class="active" href="{$SCRIPTURL}?action=pm;sa=manrules;add;rid=0">{$T.pm_add_rule}</a>
    </li>
    {if !empty($C.rules)}
      <li>
      <a href="{$SCRIPTURL}?action=pm;sa=manrules;apply;{$C.session_var}={$C.session_id}" onclick="return confirm('{$T.pm_js_apply_rules_confirm}');">{$T.pm_apply_rules}</a>
      </li>
    {/if}
    {if !empty($C.rules)}
      {$C.hidden_sid_input}
      <input type="submit" name="delselected" value="{$T.pm_delete_selected_rule}" onclick="return confirm('{$T.pm_js_delete_rule_confirm}');" class="button_submit" />
    {/if}
  </ul>
</div>
</form>