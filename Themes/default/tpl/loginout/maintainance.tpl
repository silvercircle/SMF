<script type="text/javascript" src="{$S.default_theme_url}/scripts/sha1.js"></script>
<form action="{$SCRIPTURL}?action=login2" method="post" accept-charset="UTF-8" {(empty($C.disable_login_hashing)) ? " onsubmit=\"hashLoginPassword(this, '{$C.session_id}');\" " : ''}>
  <div class="login" id="maintenance_mode">
    <div class="cat_bar2">
      <h3>{$C.title}</h3>
    </div>
    <div class="blue_container mediumpadding">
      <img class="floatleft" src="{$S.images_url}/construction.png" width="40" height="40" alt="{$T.in_maintain_mode}" />
      {$C.description}
      <br class="clear">
    </div>
    <br>
    <div class="cat_bar2">
      <h3>{$T.admin_login}</h3>
    </div>
    <div class="blue_container mediumpadding">
      <dl class="input">
        <dt>{$T.username}:</dt>
        <dd><input type="text" name="user" size="20" class="input_text" /></dd>
        <dt>{$T.password}:</dt>
        <dd><input type="password" name="passwrd" size="20" class="input_password" /></dd>
        <dt>{$T.mins_logged_in}:</dt>
        <dd><input type="text" name="cookielength" size="4" maxlength="4" value="{$M.cookieTime}" class="input_text" /></dd>
        <dt>{$T.always_logged_in}:</dt>
        <dd><input type="checkbox" name="cookieneverexp" class="input_check" /></dd>
      </dl>
      <p class="centertext"><input type="submit" value="{$T.login}" class="button_submit" /></p>
    </div>
    <input type="hidden" name="hash_passwrd" value="" />
  </div>
</form>