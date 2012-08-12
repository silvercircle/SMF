<div class="cat_bar">
  <h3>
    {$T.editBuddies}
  </h3>
</div>
<table style="width:100%;" class="table_grid" align="center">
  <tr>
    <th class="glass first_th lefttext" scope="col" style="width:20%;">{$T.name}</th>
    <th class="glass" scope="col">{$T.status}</th>
    <th class="glass" scope="col">{$T.email}</th>
    <th class="glass last_th" scope="col"></th>
  </tr>
  {if empty($C.buddies)}
    <tr class="tablerow">
      <td colspan="4" align="center"><strong>{$T.no_buddies}</strong></td>
    </tr>
  {/if}
  {$alternate = false}
  {foreach $C.buddies as $buddy}
    <tr class="tablerow{($alternate) ? ' alternate' : ''}">
      <td>{$buddy.link}</td>
        <td align="center"><a href="{$buddy.online.href}"><img src="{$buddy.online.image_href}" alt="{$buddy.online.label}" title="{$buddy.online.label}" /></a></td>
        <td align="center">{($buddy.show_email == 'no') ? '' : ('<a href="'|cat:$SUPPORT->url_parse('?action=emailuser;sa=email;uid='|cat:$buddy.id)|cat:'" rel="nofollow"><img src="'|cat:$S.images_url|cat:'/email_go.png" alt="'|cat:$T.email|cat:'" title="'|cat:$T.email|cat:' '|cat:$buddy.name|cat:'" /></a>')}</td>
        <td align="center"><a href="{$SUPPORT->url_parse('?action=profile;area=lists;sa=buddies;u='|cat:$C.id_member|cat:';remove='|cat:$buddy.id|cat:';'|cat:$C.session_var|cat:'='|cat:$C.session_id)}"><img src="{$S.images_url}/icons/user_delete.png" alt="{$T.buddy_remove}" title="{$T.buddy_remove}" /></a></td>
    </tr>
    {$alternate = !$alternate}
  {/foreach}
</table>
<br>
<form action="{$SUPPORT->url_parse('?action=profile;u='|cat:$C.id_member|cat:';area=lists;sa=buddies')}" method="post" accept-charset="UTF-8">
  <div class="add_buddy">
    <div class="cat_bar">
      <h3>{$T.buddy_add}</h3>
    </div>
    <div class="blue_container cleantop">
      <div class="content">
        <label for="new_buddy">
          <strong>{$T.who_member}:</strong>
        </label>
        <input type="text" name="new_buddy" id="new_buddy" size="40" class="input_text" />
        <input type="submit" value="{$T.buddy_add_button}" class="default" />
      </div>
    </div>
  </div>
</form>
<script type="text/javascript" src="{$S.default_theme_url}/scripts/suggest.js?fin20"></script>
<script type="text/javascript"><!-- // --><![CDATA[
  var oAddBuddySuggest = new smc_AutoSuggest( {
    sSelf: 'oAddBuddySuggest',
    sSessionId: "{$C.session_id}",
    sSessionVar: "{$C.session_var}",
    sSuggestId: 'new_buddy',
    sControlId: 'new_buddy',
    sSearchType: 'member',
    sTextDeleteItem: "{$T.autosuggest_delete_item}",
    bItemList: false
  } );
  // ]]>
</script>
