<form action="{$SCRIPTURL}?action=activate;sa=resend" method="post" accept-charset="UTF-8">
  <div class="cat_bar">
    <h3>{$C.page_title}</h3>
  </div>
  <div class="blue_container mediumpadding">
    <dl class="input">
      <dt>{$T.invalid_activation_username}:</dt>
      <dd><input type="text" name="user" size="40" value="{$C.default_username}" class="input_text" /></dd>
    </dl>
    <h1 class="bigheader secondary borderless">{$T.invalid_activation_new}</h1>
    <dl class="input">
      <dt>{$T.invalid_activation_new_email}:</dt>
      <dd><input type="text" name="new_email" size="40" class="input_text" /></dd>
      <dt>{$T.invalid_activation_password}:</dt>
      <dd><input type="password" name="passwd" size="30" class="input_password" /></dd>
    </dl>
    {if $C.can_activate}
      <h1 class="bigheader secondary borderless">{$T.invalid_activation_known}</h1>
      <dl class="input">
        <dt>{$T.invalid_activation_retry}:</dt>
        <dd><input type="text" name="code" size="30" class="input_text" /></dd>
      </dl>
    {/if}
    <p><input type="submit" value="{$T.invalid_activation_resend}" class="default" /></p>
  </div>
</form>