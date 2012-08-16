<h1 class="bigheader secondary indent title bordered">
  {$T.showPermissions}
</h1>
{if $C.member.has_all_permissions}
  <div class="red_container cleantop mediumpadding norounded">{$T.showPermissions_all}</div>
{else}
  <div class="orange_container cleantop mediumpadding norounded smalltext">{$T.showPermissions_help}</div>
  <div id="permissions" class="blue_container cleantop norounded">
    <div clas="content">
    {if !empty($C.no_access_boards)}
      <h1 class="bigheader secondary indent">{$T.showPermissions_restricted_boards}</h1>
      <div class="smalltext mediumpadding">
        {$T.showPermissions_restricted_boards_desc}:
        <br>
        {foreach $C.no_access_boards as $no_access_board}
          <a href="{$SCRIPTURL}?board={$no_access_board.id}.0">{$no_access_board.name}</a>{($no_access_board.is_last) ? '' : ', '}
        {/foreach}
      </div>
    {/if}
    <h1 class="bigheader secondary indent">{$T.showPermissions_general}</h1>
    {if !empty($C.member.permissions.general)}
      <table class="table_grid" style="width:100%;">
      <thead>
        <tr>
          <th class="lefttext glass nowrap" scope="col" style="width:50%;">{$T.showPermissions_permission}</th>
          <th class="lefttext glass nowrap" scope="col" style="width:50%;">{$T.showPermissions_status}</th>
        </tr>
      </thead>
      <tbody>
      {foreach $C.member.permissions.general as $permission}
        <tr>
          <td class="windowbg smalltext" title="{$permission.id}">
            {($permission.is_denied) ? ('<del>'|cat:$permission.name|cat:'</del>') : $permission.name}
          </td>
          <td class="windowbg smalltext">
          {if $permission.is_denied}
            <span class="alert">{$T.showPermissions_denied}:&nbsp;{', '|implode:$permission.groups.denied}</span>
          {else}
            {$T.showPermissions_given}:&nbsp;{', '|implode:$permission.groups.allowed}
          {/if}
          </td>
        </tr>
      {/foreach}
      </tbody>
      </table>
    {else}
      <p class="windowbg2 description">{$T.showPermissions_none_general}</p>
    {/if} 
    <br>     
    <form action="{$SCRIPTURL}?action=profile;u={$C.id_member};area=permissions#board_permissions" method="post" accept-charset="UTF-8">
      <h1 class="bigheader secondary indent">
        <a id="board_permissions"></a>{$T.showPermissions_select}:
        <select name="board" onchange="if (this.options[this.selectedIndex].value) this.form.submit();">
          <option value="0"{($C.board == 0) ? ' selected="selected"' : ''}>{$T.showPermissions_global}&nbsp;</option>
          {if !empty($context.boards)}
            <option value="" disabled="disabled">---------------------------</option>
          {/if}
          {foreach $C.boards as $board}
            <option value="{$board.id}"{($board.selected) ? ' selected="selected"' : ''}>{$board.name} ({$board.profile_name})</option>
          {/foreach}
        </select>
      </h1>
    <form>
    {if !empty($C.member.permissions.board)}
      <table class="table_grid" style="width:100%;">
      <thead>
        <tr>
          <th class="lefttext glass nowrap" scope="col" style="width:50%;">{$T.showPermissions_permission}</th>
          <th class="lefttext glass nowrap" scope="col" style="width:50%;">{$T.showPermissions_status}</th>
        </tr>
        </thead>
        <tbody>
        {foreach $C.member.permissions.board as $permission}
          <tr>
            <td class="windowbg" title="{$permission.id}">
              {($permission.is_denied) ? ('<del>'|cat:$permission.name|cat:'</del>') : $permission.name}
            </td>
            <td class="windowbg2 smalltext">
            {if $permission.is_denied}
              <span class="alert">{$T.showPermissions_denied}:&nbsp;{', '|implode:$permission.groups.denied}</span>
            {else}
              {$T.showPermissions_given}:&nbsp;{', '|implode:$permission.groups.allowed}
            {/if}
            </td>
          </tr>
        {/foreach}
        </tbody>
      </table>
    {else}
      <p class="windowbg2 description">{$T.showPermissions_none_board}</p>
    {/if}
  </div>
</div>
{/if}