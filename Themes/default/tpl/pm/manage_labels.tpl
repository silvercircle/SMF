<form action="{$SCRIPTURL}?action=pm;sa=manlabels" method="post" accept-charset="UTF-8">
  <div class="cat_bar2">
    <h3>{$T.pm_manage_labels}</h3>
  </div>
  <div class="orange_container cleantop mediumpadding">
    {$T.pm_labels_desc}
  </div>
  <br>
  <table width="100%" class="table_grid">
  <thead>
    <tr>
      <th class="lefttext first_th glass">
        {$T.pm_label_name}
      </th>
      <th class="centertext glass last_th" style="width:4%;">
      {if count($C.labels) > 2}
        <input type="checkbox" class="input_check" onclick="invertAll(this, this.form);" />
      {/if}
      </th>
    </tr>
  </thead>
  <tbody>
  {if count($C.labels) < 2}
    <tr class="tablerow">
      <td colspan="2" class="centertext">{$T.pm_labels_no_exist}</td>
    </tr>
  {else}
    {$alternate = true}
    {foreach $C.labels as $label}
      {if $label.id == -1}
        {continue}
      {/if}
      <tr class="tablerow{($alternate) ? ' alternate' : ''}">
        <td>
          <input type="text" name="label_name[{$label.id}]" value="{$label.name}" size="30" maxlength="30" class="input_text" />
        </td>
        <td class="centertext"><input type="checkbox" class="input_check" name="delete_label[{$label.id}]" /></td>
      </tr>
      {$alternate = !$alternate}
    {/foreach}
  {/if}
  </tbody>
</table>
{if !count($C.labels) < 2}
  <div class="mediumpadding righttext">
    <input type="submit" name="save" value="{$T.save}" class="default" />
    <input type="submit" name="delete" value="{$T.quickmod_delete_selected}" onclick="return confirm('{$T.pm_labels_delete}');" class="button_submit" />
  </div>
{/if}
{$C.hidden_sid_input}
</form>
<form action="{$SCRIPTURL}?action=pm;sa=manlabels" method="post" accept-charset="UTF-8" style="margin-top: 1ex;">
<div class="cat_bar">
  <h3>{$T.pm_label_add_new}</h3>
</div>
<div class="blue_container cleantop">
  <div class="content">
    <dl class="settings">
      <dt>
        <strong><label for="add_label">{$T.pm_label_name}</label>:</strong>
      </dt>
      <dd>
        <input type="text" id="add_label" name="label" value="" size="30" maxlength="30" class="input_text" />
      </dd>
    </dl>
    <div class="floatright mediumpadding">
      <input type="submit" name="add" value="{$T.pm_label_add_new}" class="default" />
    </div>
    <br class="clear">
  </div>
</div>
{$C.hidden_sid_input}
</form>
