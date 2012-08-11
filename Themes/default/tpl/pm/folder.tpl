<script type="text/javascript"><!-- // --><![CDATA[
  var allLabels = {};
  var currentLabels = {};
  function loadLabelChoices()
  {
    var listing = document.forms.pmFolder.elements;
    var theSelect = document.forms.pmFolder.pm_action;
    var add, remove, toAdd = { length: 0 }, toRemove = { length: 0 };

    if (theSelect.childNodes.length == 0)
      return;

    if (!('-1' in allLabels))
    {
      for (var o = 0; o < theSelect.options.length; o++)
        if (theSelect.options[o].value.substr(0, 4) == "rem_")
          allLabels[theSelect.options[o].value.substr(4)] = theSelect.options[o].text;
    }

    for (var i = 0; i < listing.length; i++)
    {
      if (listing[i].name != "pms[]" || !listing[i].checked)
        continue;

      var alreadyThere = [], x;
      for (x in currentLabels[listing[i].value])
      {
        if (!(x in toRemove))
        {
          toRemove[x] = allLabels[x];
          toRemove.length++;
        }
        alreadyThere[x] = allLabels[x];
      }

      for (x in allLabels)
      {
        if (!(x in alreadyThere))
        {
          toAdd[x] = allLabels[x];
          toAdd.length++;
        }
      }
    }

    while (theSelect.options.length > 2)
      theSelect.options[2] = null;

    if (toAdd.length != 0)
    {
      theSelect.options[theSelect.options.length] = new Option("{$T.pm_msg_label_apply}", "");
      setInnerHTML(theSelect.options[theSelect.options.length - 1], "{$T.pm_msg_label_apply}");
      theSelect.options[theSelect.options.length - 1].disabled = true;

      for (i in toAdd)
      {
        if (i != "length")
          theSelect.options[theSelect.options.length] = new Option(toAdd[i], "add_" + i);
      }
    }

    if (toRemove.length != 0)
    {
      theSelect.options[theSelect.options.length] = new Option("{$T.pm_msg_label_remove}", "");
      setInnerHTML(theSelect.options[theSelect.options.length - 1], "{$T.pm_msg_label_remove}");
      theSelect.options[theSelect.options.length - 1].disabled = true;

      for (i in toRemove)
      {
        if (i != "length")
          theSelect.options[theSelect.options.length] = new Option(toRemove[i], "rem_" + i);
      }
    }
  }
// ]]></script>
<form style="padding-right:5px;" action="{$SCRIPTURL}?action=pm;sa=pmactions;{($C.display_mode == 2) ? 'conversation;' : ''}f={$C.folder};start={$C.start}{($C.current_label_id != -1) ? (';l='|cat:$C.current_label_id) : ''}" method="post" accept-charset="UTF-8" name="pmFolder">
{if $C.display_mode == 2}
  {include "pm/subject_list.tpl"}
  <div class="clear"><br></div>
{else}
  <div class="cat_bar2">
    <div class="floatright tinytext">
      <a href="{$SCRIPTURL}?action=pm;view;f={$C.folder};start={$C.start};sort={$C.sort_by}{($C.sort_direction == 'up') ? '' : ';desc'}{($C.current_label_id != -1) ? (';l='|cat:$C.current_label_id) : ''}">{$T.pm_change_view}</a>
    </div>
    <h3>{$C.pmboxname} ({($C.display_mode == 1) ? $T.pm_single_view : $T.pm_flat_view})</h3>
  </div>
{/if}
<div class="clear"></div>
  <div class="posts_container">
  {if $PMCONTEXT->getPmMessage('message', true) == true}
    {if $C.display_mode == 2}
      <div class="pagesection">
        {$SUPPORT->button_strip($C.conversation_buttons, 'right')}
      </div>
    {/if}
    {section name=items start=0 loop=100}
      {$result = $PMCONTEXT->getPmMessage('message')}
      {if $result == false}
        {break}
      {/if}
      {include "pm/pmbit.tpl"}
    {/section}
    {if empty($C.display_mode)}
      <div class="pagesection">
        <div class="floatleft pagelinks">{$C.page_index}</div>
        <div class="floatright"><input type="submit" name="del_selected" value="{$T.quickmod_delete_selected}" style="font-weight: normal;" onclick="if (!confirm('{$T.delete_selected_confirm}')) return false;" class="default" /></div>
      </div>
    {elseif $C.display_mode == 2 and isset($C.conversation_buttons)}
      <div class="pagesection">
        {$SUPPORT->button_strip($C.conversation_buttons, 'right')}
      </div>
    {/if}
  {else}
    <div class="red_container norounded mediumpadding">
      {$T.pm_no_messages}
    </div>
  {/if}
  </div>
  {if $C.display_mode == 1}
    {include "pm/subject_list.tpl"}
    <br>
  {/if}
  {$C.hidden_sid_input}
</form>