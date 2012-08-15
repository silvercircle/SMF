<dt>
  <strong>{$T.smileys_current}:</strong>
</dt>
<dd>
  <select name="smiley_set" onchange="document.getElementById('smileypr').src = this.selectedIndex == 0 ? '{$S.images_url}/blank.gif' : '{$M.smileys_url}/' + (this.selectedIndex != 1 ? this.options[this.selectedIndex].value : '{(!empty($S.smiley_sets_default)) ? $S.smiley_sets_default : $M.smiley_sets_default}') + '/smiley.gif';">
  {foreach $C.smiley_sets as $set}
    <option value="{$set.id}"{($set.selected) ? ' selected="selected"' : ''}>{$set.name}</option>
  {/foreach}
  </select> <img id="smileypr" src="{($C.member.smiley_set.id != 'none') ? ($M.smileys_url|cat:'/'|cat:(($C.member.smiley_set.id != '') ? $C.member.smiley_set.id : ((!empty($S.smiley_sets_default)) ? $S.smiley_sets_default : $M.smiley_sets_default))|cat:'/smiley.gif') : ($S.images_url|cat:'/blank.gif')}" alt=":)" style="padding-left: 20px;" />
</dd>