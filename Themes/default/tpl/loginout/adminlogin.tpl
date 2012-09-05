<script type="text/javascript" src="{$S.default_theme_url}/scripts/sha1.js"></script>
<form action="{$SCRIPTURL}{$C.get_data}" method="post" accept-charset="UTF-8" name="frmLogin" id="frmLogin" onsubmit="hashAdminPassword(this, {'\''|cat:$C.user.username|cat:'\', \''|cat:$C.session_id|cat:'\');'}">
  <div class="login" id="admin_login">
    <div class="cat_bar2">
      <h3>
        {$T.login}
      </h3>
    </div>
    <div class="blue_container centertext cleantop">
      {if !empty($C.incorrect_password)}
        <div class="error">{$T.admin_incorrect_password}</div>
      {/if}
      <strong>{$T.password}:</strong>
      <input type="password" name="admin_pass" size="24" class="input_password" />
      <a href="{$SCRIPTURL}?action=helpadmin;help=securityDisable_why" onclick="return reqWin(this.href);" class="help"><strong>[{$T.help}]&nbsp;&nbsp;</strong></a><br />
      <input type="submit" style="margin-top: 1em;" value="{$T.login}" class="default" />
      {$C.post_data}
    </div>
  </div>
  <input type="hidden" name="admin_hash_pass" value="" />
</form>
<script type="text/javascript">
  <!-- // --><![CDATA[
    document.forms.frmLogin.admin_pass.focus();
  // ]]>
</script>