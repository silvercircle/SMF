<script type="text/javascript" src="{$S.default_theme_url}/scripts/register.js"></script>
<script type="text/javascript"><!-- // --><![CDATA[
  function verifyAgree()
  {
    if (currentAuthMethod == 'passwd' && document.forms.registration.smf_autov_pwmain.value != document.forms.registration.smf_autov_pwverify.value)
    {
      alert("{$T.register_passwords_differ_js}");
      return false;
    }
    return true;
  }

  var currentAuthMethod = 'passwd';
  function updateAuthMethod()
  {
    // What authentication method is being used?
    if (!document.getElementById('auth_openid') || !document.getElementById('auth_openid').checked)
      currentAuthMethod = 'passwd';
    else
      currentAuthMethod = 'openid';

    // No openID?
    if (!document.getElementById('auth_openid'))
      return true;

    document.forms.registration.openid_url.disabled = currentAuthMethod == 'openid' ? false : true;
    document.forms.registration.smf_autov_pwmain.disabled = currentAuthMethod == 'passwd' ? false : true;
    document.forms.registration.smf_autov_pwverify.disabled = currentAuthMethod == 'passwd' ? false : true;
    document.getElementById('smf_autov_pwmain_div').style.display = currentAuthMethod == 'passwd' ? '' : 'none';
    document.getElementById('smf_autov_pwverify_div').style.display = currentAuthMethod == 'passwd' ? '' : 'none';

    if (currentAuthMethod == 'passwd')
    {
      verificationHandle.refreshMainPassword();
      verificationHandle.refreshVerifyPassword();
      document.forms.registration.openid_url.style.backgroundColor = '';
      document.getElementById('password1_group').style.display = '';
      document.getElementById('password2_group').style.display = '';
      document.getElementById('openid_group').style.display = 'none';
    }
    else
    {
      document.forms.registration.smf_autov_pwmain.style.backgroundColor = '';
      document.forms.registration.smf_autov_pwverify.style.backgroundColor = '';
      document.forms.registration.openid_url.style.backgroundColor = '#FFF0F0';
      document.getElementById('password1_group').style.display = 'none';
      document.getElementById('password2_group').style.display = 'none';
      document.getElementById('openid_group').style.display = '';
    }

    return true;
  }
  // ]]>
</script>
{if !empty($C.registration_errors)}
  <div class="errorbox">
    <h3>{$T.registration_errors_occurred}</h3>
    <ul class="reset">
      {foreach $C.registration_errors as $error}
        <li>{$error}</li>
      {/foreach}
    </ul>
  </div>
{/if}
<form action="{$SUPPORT->url_parse('?action=register2')}" method="post" accept-charset="UTF-8" name="registration" id="registration" onsubmit="return verifyAgree();">
  <h1 class="bigheader section_header">{$T.registration_form}</h1>
  <div class="blue_container cleantop">
    <div class="content">
      <h1 class="bigheader secondary indent">{$T.required_info}</h1>
      <fieldset class="content borderless">
      <dl class="common rightbalance input">
        <dt><strong><label for="smf_autov_username">{$T.username}:</label></strong></dt>
        <dd class="smallpadding">
          <input type="text" name="user" id="smf_autov_username" size="30" tabindex="{$C.tabindex}" maxlength="{$M.username_max_length}" value="{(isset($C.username)) ? $C.username : ''}" class="input_text" />
          {$C.tabindex = $C.tabindex+1}
          <span id="smf_autov_username_div" style="display: none;">
          <a id="smf_autov_username_link" href="#">
            <div data-tip="tooltip_click_to_verify" id="image_anchor" style="display:inline;">
              <img id="smf_autov_username_img" src="{$S.images_url}/icons/field_check.gif" alt="*" />
            </div>
          </a>
          </span>
        <div id="tooltip_bad_username" style="display:none;">
            {$T.tooltip_username_bad}
        </div>
        <div id="tooltip_good_username" style="display:none;">
            {$T.tooltip_username_good}
        </div>
        <div id="tooltip_click_to_verify" style="display:none;">
            Click to verify
        </div>
        </dd>
        <dt><strong><label for="smf_autov_reserve1">{$T.email}:</label></strong></dt>
        <dd class="smallpadding">
          <input type="text" name="email" id="smf_autov_reserve1" size="30" tabindex="{$C.tabindex}" value="{(isset($C.email)) ? $C.email : ''}" class="input_text" />
          {$C.tabindex = $C.tabindex+1}
        </dd>
        <dt><strong><label for="allow_email">{$T.allow_user_email}:</label></strong></dt>
        <dd class="smallpadding">
          <input type="checkbox" name="allow_email" id="allow_email" tabindex="{$C.tabindex}" class="input_check" />
          {$C.tabindex = $C.tabindex+1}
        </dd>
      </dl>
      {if !empty($M.enableOpenID)}
        <dl class="common rightbalance input" id="authentication_group">
          <dt>
            <strong>{$T.authenticate_label}:</strong>
            <a href="{$SCRIPTURL}?action=helpadmin;help=register_openid" onclick="return reqWin(this.href);" class="help">(?)</a>
          </dt>
          <dd class="floatleft">
            <label for="auth_pass" id="option_auth_pass">
              <input type="radio" name="authenticate" value="passwd" id="auth_pass" tabindex="{$C.tabindex}" {(empty($C.openid)) ? 'checked="checked" ' : ''} onclick="updateAuthMethod();" class="input_radio" />
              {$C.tabindex = $C.tabindex+1}
              {$T.authenticate_password}
            </label>
            <label for="auth_openid" id="option_auth_openid">
              <input type="radio" name="authenticate" value="openid" id="auth_openid" tabindex="{$C.tabindex}" {(!empty($C.openid)) ? 'checked="checked" ' : ''} onclick="updateAuthMethod();" class="input_radio" />
              {$C.tabindex = $C.tabindex+1}
              {$T.authenticate_openid}
            </label>
          </dd>
        </dl>
      {/if}
      <dl class="common rightbalance input" id="password1_group">
        <dt><strong><label for="smf_autov_pwmain">{$T.choose_pass}:</label></strong></dt>
        <dd class="smallpadding">
          <input type="password" name="passwrd1" id="smf_autov_pwmain" size="30" tabindex="{$C.tabindex}" class="input_password" />
          {$C.tabindex = $C.tabindex+1}
          <span id="smf_autov_pwmain_div" style="display: none;">
            <img id="smf_autov_pwmain_img" src="{$S.images_url}/icons/field_invalid.gif" alt="*" />
          </span>
        </dd>
      </dl>
      <dl class="common rightbalance input" id="password2_group">
        <dt><strong><label for="smf_autov_pwverify">{$T.verify_pass}:</label></strong></dt>
        <dd class="smallpadding">
          <input type="password" name="passwrd2" id="smf_autov_pwverify" size="30" tabindex="{$C.tabindex}" class="input_password" />
          {$C.tabindex = $C.tabindex+1}
          <span id="smf_autov_pwverify_div" style="display: none;">
            <img id="smf_autov_pwverify_img" src="{$S.images_url}/icons/field_valid.gif" alt="*" />
          </span>
        </dd>
      </dl>
      {if !empty($M.enableOpenID)}
        <dl class="common rightbalance input" id="openid_group">
          <dt><strong>{$T.authenticate_openid_url}:</strong></dt>
          <dd>
            <input type="text" name="openid_identifier" id="openid_url" size="30" tabindex="{$C.tabindex}" value="{(isset($C.openid)) ? $C.openid : ''}" class="input_text openid_login" />
          </dd>
        </dl>
      {/if}
      </fieldset>
  {if !empty($C.profile_fields) or !empty($C.custom_fields)}
      <h1 class="bigheader secondary indent">
        {$T.additional_information}
      </h1>
        <fieldset class="borderless">
          <dl class="common rightbalance input" id="custom_group">
  {/if}
  {if !empty($C.profile_fields)}
    {foreach $C.profile_fields as $key => $field}
      {if $field.type == 'callback_template'}
        {$SUPPORT->callbackTemplate($field.callback_name)}
      {else}
        <dt>
          <strong{(!empty($field.is_error)) ? ' style="color: red;"' : ''}>{$field.label}:</strong>
          {if !empty($field.subtext)}
            <span class="smalltext">{$field.subtext}</span>
          {/if}
        </dt>
        <dd>
        {if !empty($field.preinput)}
          {$field.preinput}
        {/if}
        {$valid_textfields = array('int', 'float', 'text', 'password')}
        {if $field.type == 'label'}
          {$field.value}
        {elseif in_array($field.type, $valid_textfields)}
          <input type="{($field.type == 'password') ? 'password' : 'text'}" name="{$key}" id="{$key}" size="{(empty($field.size)) ? 30 : $field.size}" value="{$field.value}" tabindex="{$C.tabindex}" {$field.input_attr} class="input_{($field.type == 'password') ? 'password' : 'text'}" />
          {$C.tabindex = $C.tabindex+1}
        {elseif $field.type == 'check'}
          <input type="hidden" name="{$key}" value="0" /><input type="checkbox" name="{$key}" id="{$key}" {(!empty($field.value)) ? ' checked="checked"' : ''} value="1" tabindex="{$C.tabindex}" class="input_check aligned" {$field.input_attr} />
          {$C.tabindex = $C.tabindex+1}
        {elseif $field.type == 'select'}
          <select name="{$key}" id="{$key}" tabindex="{$C.tabindex}">
          {$C.tabindex = $C.tabindex+1}
          {if isset($field.options)}
            {if !is_array($field.options)}
              {$field.options = $SUPPORT->_eval($field.options)}
            {/if}
            {if is_array($field.options)}
              {foreach $field.options as $value => $name}
                <option value="{$value}" {($value == $field.value) ? 'selected="selected"' : ''}>{$name}</option>
              {/foreach}
            {/if}
          {/if}
          </select>
        {/if}
        {if !empty($field.postinput)}
          {$field.postinput}
        {/if}
        </dd>
      {/if}
    {/foreach}
  {/if}
  {if !empty($C.custom_fields)}
    {foreach $C.custom_fields as $field}
      <dt>
        <strong{(!empty($field.is_error)) ? ' style="color: red;"' : ''}>{$field.name}:</strong>
        <span class="smalltext">{$field.desc}</span>
      </dt>
      <dd>{$field.input_html}</dd>
    {/foreach}
  {/if}
  {if !empty($C.profile_fields) or !empty($C.custom_fields)}
    </dl>
    </fieldset>
  {/if} 
      </div>
      {$SUPPORT->displayHook('register_form_extend')}
    </div>
  {if $C.visual_verification}
    <br>
    <h1 class="bigheader section_header">{$T.verification}</h1>
    <div class="blue_container cleantop">
      <div class="content">
        <fieldset class="content centertext">
          {$SUPPORT->template_control_verification($C.visual_verification_id, 'all')}
        </fieldset>
      </div>
    </div>
  {/if}
  <div id="confirm_buttons" class="mediumpadding">
    <input type="submit" name="regSubmit" value="{$T.register}" tabindex="{$C.tabindex}" class="default" />
  </div>
  <input type="hidden" name="step" value="2" />
</form>
<script type="text/javascript"><!-- // --><![CDATA[
  var regTextStrings = {
    "username_valid": "{$T.registration_username_available}",
    "username_invalid": "{$T.registration_username_unavailable}",
    "username_check": "{$T.registration_username_check}",
    "password_short": "{$T.registration_password_short}",
    "password_reserved": "{$T.registration_password_reserved}",
    "password_numbercase": "{$T.registration_password_numbercase}",
    "password_no_match": "{$T.registration_password_no_match}",
    "password_valid": "{$T.registration_password_valid}"
  };
  var verificationHandle = new smfRegister("registration", {(empty($M.password_strength)) ? 0 : $M.password_strength}, regTextStrings);
  updateAuthMethod();
  // ]]>
</script>
