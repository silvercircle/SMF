<dt>
  <strong>{$T.primary_membergroup}: </strong><br>
  <span class="smalltext">(<a href="{$SCRIPTURL}?action=helpadmin;help=moderator_why_missing" onclick="return reqWin(this.href);">{$T.moderator_why_missing}</a>)</span>
</dt>
<dd>
  <select name="id_group" {($C.user.is_owner and $C.member.group_id == 1) ? ('onchange="if (this.value != 1 &amp;&amp; !confirm(\''|cat:$T.deadmin_confirm|cat:'\')) this.value = 1;"') : ''}>
    {foreach $C.member_groups as $member_group}
      {if !empty($member_group.can_be_primary)}
        <option value="{$member_group.id}"{($member_group.is_primary) ? ' selected="selected"' : ''}>
          {$member_group.name}
        </option>
      {/if}
    {/foreach}
  </select>
</dd>
<dt>
  <strong>{$T.additional_membergroups}:</strong>
</dt>
<dd>
  <span id="additional_groupsList">
    <input type="hidden" name="additional_groups[]" value="0" />
    {foreach $C.member_groups as $member_group}
      {if $member_group.can_be_additional}
        <label for="additional_groups-{$member_group.id}"><input type="checkbox" name="additional_groups[]" value="{$member_group.id}" id="additional_groups-{$member_group.id}"{($member_group.is_additional) ? ' checked="checked"' : ''} class="input_check" /> {$member_group.name}</label><br>
      {/if}
    {/foreach}
  </span>
  <a href="javascript:void(0);" onclick="document.getElementById('additional_groupsList').style.display = 'block'; document.getElementById('additional_groupsLink').style.display = 'none'; return false;" id="additional_groupsLink" style="display: none;">{$T.additional_membergroups_show}</a>
  <script type="text/javascript"><!-- // --><![CDATA[
    document.getElementById("additional_groupsList").style.display = "none";
    document.getElementById("additional_groupsLink").style.display = "";
    // ]]>
  </script>
</dd>