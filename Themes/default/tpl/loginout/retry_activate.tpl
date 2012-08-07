{extends "base.tpl"}
{block content}
<form action="{$SCRIPTURL}?action=activate;u={$C.member_id}" method="post" accept-charset="UTF-8">
  <div class="cat_bar">
    <h3>{$C.page_title}</h3>
  </div>
  <div class="blue_container mediumpadding">
  <dl class="input">
  {if empty($C.member_id)}
    <dt>{$T.invalid_activation_username}:</dt>
    <dd><input type="text" name="user" size="30" class="input_text" /></dd>
  {/if}
    <dt>{$T.invalid_activation_retry}:</dt>
    <dd><input type="text" name="code" size="30" class="input_text" /></dd>
  </dl>
  <p><input type="submit" value="{$T.invalid_activation_submit}" class="default" /></p>
  </div>
</form>
{/block}