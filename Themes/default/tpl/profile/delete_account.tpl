<form action="{$SCRIPTURL}?action=profile;area=deleteaccount;save" method="post" accept-charset="UTF-8" name="creator" id="creator">
  <h1 class="bigheader secondary indent title bordered">
    {$T.deleteAccount}</span>
  </h1>
  {if !$C.user.is_owner}
    <div class="red_container smalltext mediumpadding cleantop norounded">{$T.deleteAccount_desc}</div>
  {/if}
  <div class="blue_container cleantop">
    <div class="content">
    {if $C.needs_approval}
      <div id ="profile_error" class="alert">{$T.deleteAccount_approval}</div>
    {/if}
    {if $C.user.is_owner}
      <div class="alert">{$T.own_profile_confirm}</div>
      <div>
        <strong{(isset($C.modify_error.bad_password) or isset($C.modify_error.no_password)) ? ' class="error"' : ''}>{$T.current_password}: </strong>
        <input type="password" name="oldpasswrd" size="20" class="input_password" />&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="submit" value="{$T.yes}" class="default" />
        {$C.hidden_sid_input}
        <input type="hidden" name="u" value="{$C.id_member}" />
        <input type="hidden" name="sa" value="{$C.menu_item_selected}" />
      </div>
    {else}
      <div class="alert"><strong>{$T.deleteAccount_warning}</strong></div><br>
      {if $C.can_delete_posts}
        <div>
          {$T.deleteAccount_posts}:
          <select name="remove_type">
            <option value="none">{$T.deleteAccount_none}</option>
            <option value="posts">{$T.deleteAccount_all_posts}</option>
            <option value="topics">{$T.deleteAccount_topics}</option>
          </select>
        </div>
      {/if}
      <br>
      <div>
        <label for="deleteAccount"><input type="checkbox" name="deleteAccount" id="deleteAccount" value="1" class="input_check" onclick="if (this.checked) return confirm('{$T.deleteAccount_confirm}');" /> {$T.deleteAccount_member}.</label>
      </div>
      <div class="floatright">
        <input type="submit" value="{$T.delete}" class="default" />
        {$C.hidden_sid_input}
        <input type="hidden" name="u" value="{$C.id_member}" />
        <input type="hidden" name="sa" value="{$C.menu_item_selected}" />
      </div>
      <div class="clear"></div>
    {/if}
    </div>
  </div>
</form>
