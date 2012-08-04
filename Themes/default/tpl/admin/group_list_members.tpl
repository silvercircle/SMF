<div id="admincenter">
{$area = (isset($C.admin_area)) ? (';area='|cat:$C.admin_area) : ''}
<form action="{$SCRIPTURL}?action={$C.current_action}{$area};sa=members;group={$C.group.id}" method="post" accept-charset="UTF-8">
  <div class="cat_bar">
    <h3>{$C.page_title}</h3>
  </div>
  <div class="blue_container smalltext">
    <div class="content">
      <dl class="settings">
        <dt>
          <strong>{$T.name}:</strong>
        </dt>
        <dd>
          <a href="#!" class="member group_{$C.group.id}">{$C.group.name}</a> {$C.group.stars}
        </dd>
        {if !empty($C.group.description)}
          <dt>
            <strong>{$T.membergroups_members_description}:</strong>
          </dt>
          <dd>
            {$C.group.description}
          </dd>
        {/if}
        <dt>
          <strong>{$T.membergroups_members_top}:</strong>
        </dt>
        <dd>
          {$C.total_members}
        </dd>
        {if !empty($C.group.moderators)}
          {$moderators = array()}
          {foreach $C.group.moderators as $moderator}
            {$moderators[] = '<a href="'|cat:$SCRIPTURL|cat:'?action=profile;u='|cat:$moderator.id|cat:'">'|cat:$moderator.name|cat:'</a>'}
          {/foreach}
          <dt>
            <strong>{$T.membergroups_members_group_moderators}:</strong>
          </dt>
          <dd>
            {', '|implode:$moderators}
          </dd>
        {/if}
      </dl>
    </div>
  </div>
  <br>
  <div class="cat_bar">
    <h3>{$T.membergroups_members_group_members}</h3>
  </div>
  <div class="pagelinks">{$C.page_index}</div>
  <table class="table_grid" style="width:100%;">
    <thead>
      <tr>
        <th class="first_th glass"><a href="{$SCRIPTURL}?action={$C.current_action}{$area};sa=members;start={$C.start};sort=name{($C.sort_by == 'name' and $C.sort_direction == 'up') ? ';desc' : ''};group={$C.group.id}">{$T.name}{($C.sort_by == 'name') ? (' <img src="'|cat:$S.images_url|cat:'/sort_'|cat:$C.sort_direction|cat:'.gif" alt="" />') : ''}</a></th>
        <th class="glass"><a href="{$SCRIPTURL}?action={$C.current_action}{$area};sa=members;start={$C.start};sort=email{($C.sort_by == 'email' and $C.sort_direction == 'up') ? ';desc' : ''};group={$C.group.id}">{$T.email}{($C.sort_by == 'email') ? (' <img src="'|cat:$S.images_url|cat:'/sort_'|cat:$C.sort_direction|cat:'.gif" alt="" />') : ''}</a></th>
        <th class="glass"><a href="{$SCRIPTURL}?action={$C.current_action}{$area};sa=members;start={$C.start};sort=active{($C.sort_by == 'active' and $C.sort_direction == 'up') ? ';desc' : ''};group={$C.group.id}">{$T.membergroups_members_last_active}{($C.sort_by == 'active') ? (' <img src="'|cat:$S.images_url|cat:'/sort_'|cat:$C.sort_direction|cat:'.gif" alt="" />') : ''}</a></th>
        <th class="glass"><a href="{$SCRIPTURL}?action={$C.current_action}{$area};sa=members;start={$C.start};sort=registered{($C.sort_by == 'registered' and $C.sort_direction == 'up') ? ';desc' : ''};group={$C.group.id}">{$T.date_registered}{($C.sort_by == 'registered') ? (' <img src="'|cat:$S.images_url|cat:'/sort_'|cat:$C.sort_direction|cat:'.gif" alt="" />') : ''}</a></th>
        <th class="glass"{(empty($C.group.assignable)) ? ' colspan="2"' : ''}><a href="{$SCRIPTURL}?action={$C.current_action}{$area};sa=members;start={$C.start};sort=posts{($C.sort_by == 'posts' and $C.sort_direction == 'up') ? ';desc' : ''};group={$C.group.id}">{$T.posts}{($C.sort_by == 'posts') ? (' <img src="'|cat:$S.images_url|cat:'/sort_'|cat:$C.sort_direction|cat:'.gif" alt="" />') : ''}</a></th>
        {if !empty($C.group.assignable)}
          <th class="glass last_th"><input type="checkbox" class="input_check" onclick="invertAll(this, this.form);" /></td>
        {/if}
      </tr>
    </thead>
    <tbody>
    {if empty($C.members)}
    <tr>
      <td  class="orange_container mediumpadding" colspan="6" align="center">{$T.membergroups_members_no_members}</td>
    </tr>
    {/if}
    {foreach $C.members as $member}
      <tr class="windowbg2">
        <td>{$member.name}</td>
        <td class="centertext">
        {if $member.show_email == 'no'}
          <em>{$T.hidden}</em>
        {elseif $member.show_email == 'yes_permission_override'}
          <a href="mailto:{$member.email}"><em>{$member.email}</em></a>
        {elseif $member.show_email == 'no_through_forum'}
          <a href="{$SCRIPTURL}?action=emailuser;sa=email;uid={$member.id}" rel="nofollow">{$txt.email}</a>
        {else}
          <a href="mailto:{$member.email}">{$member.email}</a>
        {/if}
        </td>
        <td class="windowbg">{$member.last_online}</td>
        <td class="windowbg">{$member.registered}</td>
        <td{(empty($C.group.assignable)) ? ' colspan="2"' : ''}>{$member.posts}</td>
        {if !empty($C.group.assignable)}
          <td align="center" style="width:4%;"><input type="checkbox" name="rem[]" value="{$member.id}" class="input_check" {($C.user.id == $member.id and $C.group.id == 1) ? ('onclick="if (this.checked) return confirm(\''|cat:$T.membergroups_members_deadmin_confirm|cat:'\')" ') : ''} /></td>
        {/if}
      </tr>
    {/foreach}
    </tbody>
  </table>
  <div class="pagesection">
    <div class="pagelinks floatleft">{$C.page_index}</div>
    {if !empty($C.group.assignable)}
      <div class="floatright"><input type="submit" name="remove" value="{$T.membergroups_members_remove}" class="button_submit" /></div>
    {/if}
  </div>
  <br>
  {if !empty($C.group.assignable)}
    <div class="cat_bar">
      <h3>{$T.membergroups_members_add_title}</h3>
    </div>
    <div class="blue_container">
      <div class="content">
        <strong>{$T.membergroups_members_add_desc}:</strong>
        <input size="80" type="text" name="toAdd" id="toAdd" value="" class="input_text" />
        <input type="submit" name="add" value="{$T.membergroups_members_add}" class="button_submit" />
        <div id="toAddItemContainer"></div>
      </div>
    </div>
  {/if}
  <input type="hidden" name="{$C.session_var}" value="{$C.session_id}" />
</form>
</div>
<br class="clear" />
{if !empty($C.group.assignable)}
    <script type="text/javascript" src="{$S.default_theme_url}/scripts/suggest.js?fin20"></script>
    <script type="text/javascript"><!-- // --><![CDATA[
      var oAddMemberSuggest = new smc_AutoSuggest({
        sSelf: 'oAddMemberSuggest',
        sSessionId: "{$C.session_id}",
        sSessionVar: "{$C.session_var}",
        sSuggestId: 'to_suggest',
        sControlId: 'toAdd',
        sSearchType: 'member',
        sPostName: 'member_add',
        sURLMask: 'action=profile;u=%item_id%',
        sTextDeleteItem: "{$T.autosuggest_delete_item}",
        bItemList: true,
        sItemListContainerId: 'toAddItemContainer'
      });
    // ]]></script>
{/if}
