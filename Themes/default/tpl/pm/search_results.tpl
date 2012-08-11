{include "generics.tpl"}
<div class="cat_bar">
  <h3>{$T.pm_search_results}</h3>
</div>
<div class="pagesection pagelinks">
  {$C.page_index}
</div>
{if empty($C.search_params.show_complete) and !empty($C.personal_messages)}
  <table class="table_grid" style="width:100%;">
    <thead>
    <tr>
      <th class="glass lefttext first_th" style="width:30%;">{$T.date}</th>
      <th class="glass lefttext" style="width:50%;">{$T.subject}</th>
      <th class="glass lefttext last_th" style="width:20%;">{$T.from}</th>
    </tr>
    </thead>
    <tbody>
{/if}
{$alternate = true}
{foreach $C.personal_messages as $message}
  {if !empty($C.search_params.show_complete)}
    {*<div class="cat_bar">
      <h3>
        <span class="floatright">{$T.search_on}: {$message.time}</span>
        <span class="floatleft">{$message.counter}&nbsp;&nbsp;<a href="{$message.href}">{$message.subject}</a></span>
      </h3>
    </div>
    <div class="cat_bar">
      <h3>{$T.from}: {$message.member.link}, {$T.to}:
        {if !empty($message.recipients.to)}
          {', '|implode:$message.recipients.to}
        {elseif $C.folder != 'sent'}
          ({$T.pm_undisclosed_recipients})
        {/if}
      </h3>
    </div>
    <div class="windowbg{$alternate ? ' alternate': ''}">
      <div class="content">
        {$message.body}
        <p class="pm_reply righttext smalltext">
        {if $C.can_send_pm}
          {if !$message.member.is_guest}
            <a href="', $scripturl, '?action=pm;sa=send;f=', $context['folder'], $context['current_label_id'] != -1 ? ';l=' . $context['current_label_id'] : '', ';pmsg=', $message['id'], ';quote;u=', $context['folder'] == 'sent' ? '' : $message['member']['id'], '">', $quote_button , '</a>', $context['menu_separator'], '
            <a href="', $scripturl, '?action=pm;sa=send;f=', $context['folder'], $context['current_label_id'] != -1 ? ';l=' . $context['current_label_id'] : '', ';pmsg=', $message['id'], ';u=', $message['member']['id'], '">', $reply_button , '</a> ', $context['menu_separator'];
          {else}
            <a href="', $scripturl, '?action=pm;sa=send;f=', $context['folder'], $context['current_label_id'] != -1 ? ';l=' . $context['current_label_id'] : '', ';pmsg=', $message['id'], ';quote">', $quote_button , '</a>', $context['menu_separator'];
          {/if}
        {/if}
        </p>
        </div>
      </div>
      *}
      {include "pm/pmbit.tpl"}
  {else}
    <tr class="tablerow{($alternate) ? ' alternate' : ''}">
      <td>{$message.time}</td>
      <td>{$message.link}</td>
      <td>{$message.member.link}</td>
    </tr>
  {/if}
  {$alternate = !$alternate}
{/foreach}
{if empty($C.search_params.show_complete) and !empty($C.personal_messages)}
    </tbody>
  </table>
{/if}
{if empty($C.personal_messages)}
  <div class="red_container norounded mediumpadding mediummargin">
    <div class="content centertext">
      {$T.pm_search_none_found}
    </div>
  </div>
{/if}
<div class="pagesection pagelinks">
  {$C.page_index}
</div>