<div class="cat_bar">
  <h3>
    {$T.editIgnoreList}
  </h3>
</div>
<table class="table_grid" style="width:100%;">
  <tr>
    <th class="glass first_th lefttext" scope="col" style="width:20%;">{$T.name}</th>
    <th class="glass" scope="col">{$T.status}</th>
    <th class="glass" scope="col">{$T.email}</th>
    <th class="glass last_th" scope="col"></th>
  </tr>
  {if empty($C.ignore_list)}
    <tr class="windowbg2">
      <td colspan="4" align="center"><strong>{$T.no_ignore}</strong></td>
    </tr>
  {/if}
  {$alternate = false}
  {foreach $C.ignore_list as $member}
    <tr class="tablerow{($alternate) ? ' alternate' : ''}">
      <td>{$member.link}</td>
      <td align="center"><a href="{$member.online.href}"><img src="{$member.online.image_href}" alt="{$member.online.label}" title="{$member.online.label}" /></a></td>
      <td align="center">{($member.show_email == 'no') ? '' : ('<a href="'|cat:$SUPPORT->url_parse('?action=emailuser;sa=email;uid='|cat:$member.id)|cat:'" rel="nofollow"><img src="'|cat:$S.images_url|cat:'/email_go.png" alt="'|cat:$T.email|cat:'" title="'|cat:$T.email|cat:' '|cat:$member.name|cat:'" /></a>')}</td>
      <td align="center"><a href="{$SUPPORT->url_parse('?action=profile;area=lists;sa=ignore;u='|cat:$C.id_member|cat:';remove='|cat:$member.id|cat:';'|cat:$C.session_var|cat:'='|cat:$C.session_id)}"><img src="{$S.images_url}/icons/user_delete.png" alt="{$T.ignore_remove}" title="{$T.ignore_remove}" /></a></td>
    </tr>
    {$alternate = !$alternate}
  {/foreach}
</table>
<br>
<form action="{$SUPPORT->url_parse('?action=profile;u='|cat:$C.id_member|cat:';area=lists;sa=ignore')}" method="post" accept-charset="UTF-8">
  <div class="add_buddy">
    <div class="cat_bar">
      <h3>{$T.ignore_add}</h3>
    </div>
    <div class="blue_container cleantop">
      <div class="content">
        <label for="new_buddy">
          <strong>{$T.who_member}:</strong>
        </label>
        <input type="text" name="new_ignore" id="new_ignore" size="40" class="input_text" />
        <input type="submit" value="{$T.ignore_add_button}" class="default" />
      </div>
    </div>
  </div>
</form>
<script type="text/javascript" src="{$S.default_theme_url}/scripts/suggest.js?fin20"></script>
<script type="text/javascript"><!-- // --><![CDATA[
    var oAddIgnoreSuggest = new smc_AutoSuggest( {
      sSelf: 'oAddIgnoreSuggest',
      sSessionId: "{$C.session_id}",
      sSessionVar: "{$C.session_var}",
      sSuggestId: 'new_ignore',
      sControlId: 'new_ignore',
      sSearchType: 'member',
      sTextDeleteItem: "{$T.autosuggest_delete_item}",
      bItemList: false
    } );
  // ]]>
</script>
