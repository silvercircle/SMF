<script type="text/javascript" src="{$S.default_theme_url}/scripts/sha1.js"></script>
  <form action="{$SCRIPTURL}?action=login2" name="frmLogin" id="frmLogin" method="post" accept-charset="UTF-8" {(empty($C.disable_login_hashing)) ? " onsubmit=\"hashLoginPassword(this, '{$C.session_id}');\" " : ''}>
    <div class="login">
      <div class="cat_bar2">
        <h3>
          {$T.login}
        </h3>
      </div>
      <div class="blue_container cleantop">
      <div class="content">
      {if isset($C.is_kick_guest)}
        <p class="red_container centertext mediummargin norounded">
          {(empty($C.kick_message)) ? $T.only_members_can_access : $C.kick_message}<br>
          {$T.login_below} <a href="{$SCRIPTURL}?action=register">{$T.register_an_account}</a> {$T.login_with_forum|sprintf:$C.forum_name_html_safe}
        </p>
      {/if}
      {if !empty($C.login_errors)}
        {foreach $C.login_errors as $error}
          <p class="error">{$error}</p>
        {/foreach}
      {/if}
      {if isset($C.description)}
        <p class="description">{$C.description}</p>
      {/if}
      <dl class="input">
        <dt>{$T.username}:</dt>
          <dd><input type="text" name="user" size="20" value="{(isset($C.default_username)) ? $C.default_username : ''}" class="input_text" /></dd>
          <dt>{$T.password}:</dt>
          <dd><input type="password" name="passwrd" value="{(isset($C.default_password)) ? $C.default_password : ''}" size="20" class="input_password" /></dd>
        </dl>
        {if !empty($M.enableOpenID)}
          <p><strong>&mdash;{$T.or}&mdash;</strong></p>
          <dl class="input">
            <dt>{$T.openid}:</dt>
            <dd><input type="text" name="openid_identifier" class="input_text openid_login" size="17" />&nbsp;<em><a href="{$SCRIPTURL}?action=helpadmin;help=register_openid" onclick="return reqWin(this.href);" class="help">(?)</a></em></dd>
          </dl>
          <hr>
        {/if}
        <dl class="input">
          <dt>{$T.mins_logged_in}:</dt>
          <dd><input type="text" name="cookielength" size="4" maxlength="4" value="{$M.cookieTime}"{(isset($C.never_expire) and !empty($C.never_expire)) ? ' disabled="disabled"' : ''} class="input_text" /></dd>
          <dt>{$T.always_logged_in}:</dt>
          <dd><input type="checkbox" name="cookieneverexp"{(isset($C.never_expire) and !empty($C.never_expire)) ? ' checked="checked"' : ''} class="input_check" onclick="this.form.cookielength.disabled = this.checked;" /></dd>
          {if isset($C.login_show_undelete)}
            <dt class="alert">{$T.undelete_account}:</dt>
            <dd><input type="checkbox" name="undelete" /></dd>
          {/if}
        </dl>
        <div class="centertext">
          <p><input type="submit" value="{$T.login}" class="default" /></p>
          <p class="smalltext"><a href="{$SCRIPTURL}?action=reminder">{$T.forgot_your_password}</a></p>
          <input type="hidden" name="hash_passwrd" value="" />
        </div>
      </div>
      </div>
    </div>
  </form>
  <script type="text/javascript">
    <!-- // --><![CDATA[
      document.forms.frmLogin.{(isset($C.default_username) and $C.default_username != '') ? 'passwrd' : 'user'}.focus();
    // ]]>
  </script>