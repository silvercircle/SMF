<form action="{(!empty($C.profile_custom_submit_url)) ? $C.profile_custom_submit_url : ($SCRIPTURL|cat:'?action=profile;area='|cat:$C.menu_item_selected|cat:';u='|cat:$C.id_member|cat:';save')}" method="post" accept-charset="UTF-8" name="creator" id="creator" enctype="multipart/form-data" onsubmit="return checkProfileSubmit();">
    <h1 class="bigheader secondary indent title bordered">
    {if !empty($C.profile_header_text)}
      {$C.profile_header_text}
    {else}
      {$T.profile}
    {/if}
    </h1>
  {if $C.page_desc}
    <div class="orange_container cleantop norunded mediumpadding">{$C.page_desc}</div>
  {/if}
  <div class="blue_container cleantop">
    <div class="content">
    {if !empty($C.profile_prehtml)}
      <div>{$C.profile_prehtml}</div>
    {/if}
    {if !empty($C.profile_fields)}
      <dl>
    {/if}
    {$lastItem = 'hr'}
    {foreach $C.profile_fields as $key => $field}
      {if $lastItem == 'hr' and $field.type == 'hr'}
        {continue}
      {/if}
      {$lastItem = $field.type}
      {if $field.type == 'hr'}
        </dl>
        <hr style="width:100%;" class="hrcolor clear" />
        <dl>
      {elseif $field.type == 'callback'}
        {if isset($field.callback_func)}
          {$SUPPORT->callbackFunc($field.callback_func)}
        {/if}
      {elseif $field.type == 'callback_template'}
        {$SUPPORT->callbackTemplate($field.callback_name)}
      {else}
        <dt>
        <strong {(!empty($field.is_error)) ? ' class="error"' : ''}>{$field.label}</strong>
        {if !empty($field.subtext)}
          <br>
          <span class="smalltext">{$field.subtext}</span>
        {/if}
        </dt>
        <dd>
        {if !empty($field.preinput)}
          {$field.preinput}
        {/if}
        {if $field.type == 'label'}
          {$field.value}
        {elseif in_array($field.type, array('int', 'float', 'text', 'password'))}
          <input type="{($field.type == 'password') ? 'password' : 'text'}" name="{$key}" id="{$key}" size="{(empty($field.size)) ? 30 : $field.size}" value="{$field.value}" {$field.input_attr} class="input_{($field.type == 'password') ? 'password' : 'text'}" />
        {elseif $field.type == 'check'}
          <input type="hidden" name="{$key}" value="0" /><input type="checkbox" name="{$key}" id="{$key}" {(!empty($field.value)) ? ' checked="checked"' : ''} value="1" class="input_check" {$field.input_attr} />
        {elseif $field.type == 'select'}
          <select name="{$key}" id="{$key}">
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
    </dl>
    {if !empty($C.custom_fields)}
      {if $lastItem != 'hr'}
        <hr style="width:100%;" class="hrcolor clear" />
      {/if}
      <dl>
      {foreach $C.custom_fields as $field}
        <dt>
          <strong>{$field.name}: </strong><br>
          <span class="smalltext">{$field.desc}</span>
        </dt>
        <dd>
          {$field.input_html}
        </dd>
      {/foreach}
      </dl>
    {/if}
    {if !empty($C.profile_posthtml)}
      <div>{$C.profile_posthtml}</div>
    {elseif $lastItem != 'hr'}
      <hr style="width:100%;" class="hrcolor clear" />
    {/if}
    {if $C.require_password}
      <dl>
      <dt>
        <strong{(isset($C.modify_error.bad_password) or isset($C.modify_error.no_password)) ? ' class="error"' : ''}>{$T.current_password}: </strong><br>
        <span class="smalltext">{$T.required_security_reasons}</span>
      </dt>
      <dd>
        <input type="password" name="oldpasswrd" size="20" style="margin-right: 4ex;" class="input_password" />
      </dd>
      </dl>
    {/if}
    <div class="righttext">
    {if !empty($C.submit_button_text)}
      <input type="submit" value="{$C.submit_button_text}" class="default" />
    {else}
      <input type="submit" value="{$T.change_profile}" class="default" />
    {/if}
    {$C.hidden_sid_input}
    <input type="hidden" name="u" value="{$C.id_member}" />
    <input type="hidden" name="sa" value="{$C.menu_item_selected}" />
    </div>
  </div>
</div>
<br>
</form>
<script type="text/javascript"><!-- // --><![CDATA[
  function checkProfileSubmit()
  {
  {if $C.require_password}
    // Did you forget to type your password?
    if (document.forms.creator.oldpasswrd.value == "")
    {
      alert("{$T.required_security_reasons}");
      return false;
    }
  {/if}
  {if !empty($C.profile_onsubmit_javascript)}
    {$C.profile_javascript}
  {/if}
  }
  // Any totally custom stuff?
  {if !empty($C.profile_javascript)}
    {$C.profile_javascript}
  {/if}
// ]]></script>
