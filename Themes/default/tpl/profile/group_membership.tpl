<form action="{$SUPPORT->url_parse('?action=profile;area=groupmembership;save')}" method="post" accept-charset="UTF-8" name="creator" id="creator">
  <h1 class="bigheader secondary indent title bordered">
    {$T.profile}
  </h1>
  <div class="orange_container cleantop mediumpadding">{$T.groupMembership_info}</div>
  <div class="spacer_h"></div>
  {if !empty($C.update_message)}
    <div id="profile_success">
      {$C.update_message}
    </div>
  {/if}
  {if !empty($C.group_request)}
    <div class="groupmembership">
      <div class="blue_container">
        <div class="content">
          <h1 class="bigheader secondary indent">{$T.request_group_membership}</h1>
          <div class="smalltext smallpadding">{$T.request_group_membership_desc}</div>
          <br>
          <textarea name="reason" rows="4" style="{($C.browser.is_ie8) ? 'width: 635px; max-width: 99%; min-width: 99%' : 'width: 99%'};"></textarea>
          <div class="righttext" style="margin: 0.5em 0.5% 0 0.5%;">
            <input type="hidden" name="gid" value="{$C.group_request.id}" />
            <input type="submit" name="req" value="{$T.submit_request}" class="default" />
          </div>
        </div>
      </div>
    </div>
  {else}
    <table class="table_grid" style="width:100%;">
      <thead>
      <tr>
        <th class="glass first_th" scope="col" {($C.can_edit_primary) ? ' colspan="2"' : ''}>{$T.current_membergroups}</th>
        <th class="glass last_th" scope="col"></th>
      </tr>
      </thead>
      <tbody>
      {$alternate = true}
      {foreach $C.groups.member as $group}
        <tr class="tablerow{($alternate) ? ' alternate' : ''}" id="primdiv_{$group.id}">
        {if $C.can_edit_primary}
          <td style="width:20px;">
            <input type="radio" name="primary" id="primary_{$group.id}" value="{$group.id}" {($group.is_primary) ? 'checked="checked"' : ''} onclick="highlightSelected('primdiv_{$group.id}');" {($group.can_be_primary) ? '' : 'disabled="disabled"'} class="input_radio" />
          </td>
        {/if}
        <td>
          <label for="primary_{$group.id}"><strong><span class="member group_{$group.id}">{$group.name}</span></strong>{(!empty($group.desc)) ? ('<br /><span class="smalltext">'|cat:$group.desc|cat:'</span>') : ''}</label>
        </td>
        <td style="width:15%;" class="righttext">
          {if $group.can_leave}
            <a href="{$SCRIPTURL}?action=profile;save;u={$C.id_member};area=groupmembership;{$C.session_var}={$C.session_id};gid={$group.id}">{$T.leave_group}</a>
          {/if}
        </td>
      </tr>
      {$alternate = !$alternate}
      {/foreach}
      </tbody>
    </table>
    {if $C.can_edit_primary}
      <div class="mediumpadding righttext">
        <input type="submit" value="{$T.make_primary}" class="default" />
      </div>
    {/if}
    {if !empty($C.groups.available)}
      <br>
      <table border="0" style="width:100%;" class="table_grid">
        <thead>
          <tr>
            <th class="glass first_th" scope="col">
              {$T.available_groups}
            </th>
            <th class="glass last_th" scope="col"></th>
          </tr>
        </thead>
        <tbody>
        {$alternate = true}
        {foreach $C.groups.available as $group}
          <tr class="tablerow{($alternate) ? ' alternate' : ''}">
            <td class="lefttext nowrap">
              <strong><span class="member group_{$group.id}">{$group.name}</span></strong>{(!empty($group.desc)) ? ('<br><span class="smalltext">'|cat:$group.desc|cat:'</span>') : ''}
            </td>
            <td class="righttext nowrap">
            {if $group.type == 3}
              <ul class="buttonlist"><li><a href="{$SCRIPTURL}?action=profile;save;u={$C.id_member};area=groupmembership;{$C.session_var}={$C.session_id};gid={$group.id},6">{$T.join_group}</a></li></ul>
            {elseif $group.type == 2 and $group.pending}
              {$T.approval_pending}
            {elseif $group.type == 2}
              <a href="{$SCRIPTURL}?action=profile;u={$C.id_member};area=groupmembership;request={$group.id}">{$T.request_group}</a>
            {/if}
            </td>
          </tr>
        {$alternate = !$alternate}
        {/foreach}
        </tbody>
      </table>
    {/if}
    <script type="text/javascript"><!-- // --><![CDATA[
      var prevClass = "";
      var prevDiv = "";
      function highlightSelected(box)
      {
        $('tr.tablerow').removeClass('highlight2');
        $('#' + box).addClass('highlight2');
      }
      {$primary = $C.primary_group}
      {if isset($C.groups.member.$primary)}
        highlightSelected("primdiv_{$primary}");
      {/if}
    // ]]>
    </script>
  {/if}
  {$C.hidden_sid_input}
  <input type="hidden" name="u" value="{$C.id_member}" />
</form>
