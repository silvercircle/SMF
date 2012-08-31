<h1 class="bigheader section_header bordered">
  {$C.page_title}
</h1>
<div class="orange_container cleantop norounded smalltext smallpadding">
  {$T.mc_lift_topic_ban_desc}
</div>
<br>
{if !empty($C.op_errors)}
  <div class="errorbox">
    <strong>{$T.error_occured}</strong>
    <ul>
    <li>
      {'</li><li>'|implode:$C.op_errors}
    </li>
    </ul>
  </div>
{/if}
{if !empty($C.success)}
<div class="message_success">
  {$C.success}
</div>
{/if}
{if isset($C.submit_url)}
  <form method="post" accept-charset="utf8" action="{$C.submit_url}">
{/if}
{$C.hidden_sid_input}
{if isset($C.topicban_message)}
  <div class="blue_container">
    <div class="content">
      {$C.topicban_message}
      <br>
      {if $C.is_ban}
        <br>
        <dl class="std leftbalance">
          <dt>
            <strong>{$T.mc_topicban_expire}</strong>
            <br>
            <span class="tinytext">{$T.mc_topicban_expire_explain}</span>
          </dt>
          <dd>
            <input type="text" size="5" name="mc_expire" value="0" class="input_text" />
          </dd>
        </dl>
      {/if}
    </div>
  </div>
{/if}
<div class="floatright smallpadding">
  {if isset($C.back_url)}
    <ul class="buttonlist floatright">
      <li>
        <a {(isset($C.submit)) ? '': 'class="active"'} href="{$C.back_url}"><span>{$C.back_label}</span></a>
      </li>
    </ul>
    {if isset($C.submit)}
      <input type="submit" name="submit" class="default" value="{$C.submit_label}" />&nbsp;&nbsp;
    {/if}
  {/if}
</div>
{if isset($C.submit_url)}
  </form>
{/if}