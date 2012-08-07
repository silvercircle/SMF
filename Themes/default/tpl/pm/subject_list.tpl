{if $C.display_mode != 1}
  <div class="cat_bar2">
    <div class="floatright tinytext">
      <a href="{$SCRIPTURL}?action=pm;view;f={$C.folder};start={$C.start};sort={$C.sort_by}{($C.sort_direction == 'up') ? '' : ';desc'}{($C.current_label_id != -1) ? (';l='|cat:$C.current_label_id) : ''}">{$T.pm_change_view}</a>
    </div>
    <h3>{$C.pmboxname} ({$T.pm_conversation_view})</h3>
  </div>
{/if}
<div class="spacer_h"></div>
<div class="framed_region">
  <table width="100%" class="topic_table">
  <thead>
    <tr>
      <th class="centertext glass cleantopr first_th" style="width:4%;">
        <a href="{$SCRIPTURL}?action=pm;view;f={$C.folder};start={$C.start};sort={$C.sort_by}{($C.sort_direction == 'up') ? '' : ';desc'}{($C.current_label_id != -1) ? (';l='|cat:$C.current_label_id) : ''}"></a>
      </th>
      <th class="glass cleantopr lefttext" style="width:22%;">
        <a href="{$SCRIPTURL}?action=pm;f={$C.folder};start={$C.start};sort=date{($C.sort_by == 'date' and $C.sort_direction == 'up') ? ';desc' : ''}{($C.current_label_id != -1) ? (';l='|cat:$C.current_label_id) : ''}">{$T.date}{($C.sort_by == 'date') ? (' <img src="'|cat:$S.images_url|cat:'/sort_'|cat:$C.sort_direction|cat:'.gif" alt="" />') : ''}</a>
      </th>
      <th class="glass cleantopr lefttext" style="width:46%;">
        <a href="{$SCRIPTURL}?action=pm;f={$C.folder};start={$C.start};sort=subject{($C.sort_by == 'subject' and $C.sort_direction == 'up') ? ';desc' : ''}{($C.current_label_id != -1) ? (';l='|cat:$C.current_label_id) : ''}">{$T.subject}{($C.sort_by == 'subject') ? (' <img src="'|cat:$S.images_url|cat:'/sort_'|cat:$C.sort_direction|cat:'.gif" alt="" />') : ''}</a>
      </th>
      <th class="glass cleantopr lefttext">
        <a href="{$SCRIPTURL}?action=pm;f={$C.folder};start={$C.start};sort=name{($C.sort_by == 'name' and $C.sort_direction == 'up') ? ';desc' : ''}{($C.current_label_id != -1) ? (';l='|cat:$C.current_label_id) : ''}">{($C.from_or_to == 'from') ? $T.from : $T.to}{($C.sort_by == 'name') ? (' <img src="'|cat:$S.images_url|cat:'/sort_'|cat:$C.sort_direction|cat:'.gif" alt="" />') : ''}</a>
      </th>
      <th class="centertext glass cleantopr last_th" style="width:4%;">
        <input type="checkbox" onclick="invertAll(this, this.form);" class="input_check" />
      </th>
    </tr>
  </thead>
  <tbody>
  {if !$C.show_delete}
    <tr class="tablerow">
      <td colspan="5">{$T.msg_alert_none}</td>
    </tr>
  {/if}
  {$alternate = false}
  {section name=items start=0 loop=100}
    {$topic = $PMCONTEXT->getPmMessage('subject')}
    {if $topic == false}
      {break}
    {/if}
    <tr class="{($alternate) ? 'tablerow' : 'tablerow alternate'}">
      <td class="centertext" style="width:4%;">
      <script type="text/javascript"><!-- // --><![CDATA[
        currentLabels[{$message.id}] = { 
          {if !empty($message.labels)}
            {$first = true}
            {foreach $message.labels as $label}
              {($first) ? '' : ','}
              "{$label.id}": "{$label.name}"
              {$first = false}
            {/foreach}
          {/if}
        };
      // ]]>
      </script>
      {($message.is_replied_to) ? ('<img src="'|cat:$S.images_url|cat:'/icons/pm_replied.png" style="margin-right: 4px;" alt="'|cat:$T.pm_replied|cat:'" />') : ('<img src="'|cat:$S.images_url|cat:'/icons/pm_read.png" style="margin-right: 4px;" alt="'|cat:$T.pm_read|cat:'" />')}</td>
      <td>{$message.time}</td>
      <td>{$message.subject_row}</td>
      <td>{($C.from_or_to == 'from') ? $message.member.link : ((empty($message.recipients.to)) ? '' : (', '|implode:$message.recipients.to))}</td>
      <td class="centertext"><input type="checkbox" name="pms[]" id="deletelisting{$message.id}" value="{$message.id}"{($message.is_selected) ? ' checked="checked"' : ''} onclick="if (document.getElementById('deletedisplay{$message.id}')) document.getElementById('deletedisplay{$message.id}').checked = this.checked;" class="input_check" /></td>
    </tr>
    {$alternate = !$alternate}
  {/section}
  </tbody>
  </table>
  </div>
  <div class="pagesection pagelinks">
    {$C.page_index}
  <div class="floatright">&nbsp;
  {if $C.show_delete}
    {if !empty($C.currently_using_labels) and $C.folder != 'sent'}
      <select name="pm_action" onchange="if (this.options[this.selectedIndex].value) this.form.submit();" onfocus="loadLabelChoices();">
      <option value="">{$T.pm_sel_label_title}:</option>
      <option value="" disabled="disabled">---------------</option>
      <option value="" disabled="disabled">{$T.pm_msg_label_apply}:</option>
      {foreach $C.labels as $label}
        {if $label.id != $C.current_label_id}
          <option value="add_{$label.id}">&nbsp;{$label.name}</option>
        {/if}
      {/foreach}
      <option value="" disabled="disabled">{$T.pm_msg_label_remove}:</option>
      {foreach $C.labels as $label}
        <option value="rem_{$label.id}">&nbsp;{$label.name}</option>
      {/foreach}
      </select>
      <noscript>
          <input type="submit" value="{$T.pm_apply}" class="default" />
      </noscript>
    {/if}
    <input type="submit" name="del_selected" value="{$T.quickmod_delete_selected}" onclick="if (!confirm('{$T.delete_selected_confirm}')) return false;" class="default" />
  {/if}
  </div>
</div>