{include "postbits/compact.tpl"}
<br>
<h1 class="bigheader secondary indent title">
  {(!isset($C.attachments) and empty($C.is_topics)) ? $T.showMessages : ((!empty($C.is_topics)) ? $T.showTopics : $T.showAttachments)} {$T.by} {$C.member.name}
 </h1>
{if $C.results_counter}
  <div class="pagelinks pagesection">
    {$C.page_index}</span>
  </div>
{/if}
{if !isset($C.attachments)}
  {if $C.is_topics}
    <table class="topic_table mlist" style="width:100%;">
      <thead>
      <tr class="mediumpadding" style="margin:2px;">
        <th scope="col" class="glass first_th" style="width:8%;" colspan="2">&nbsp;</th>
        <th scope="col" class="glass lefttext">{$T.subject}</th>
        <th scope="col" class="glass nowrap">{$T.replies}</th>
        <th scope="col" class="glass centertext nowrap">{$T.last_post}</th>
      </tr>
      </thead>
      <tbody>
      {if !empty($C.topics)}
        {$C.alt_row = false}
        {foreach $C.topics as $topic}
          {call topicbit topic=$topic}
          {$C.alt_row = !$C.alt_row}
        {/foreach}
      {else}
        <tr>
          <td colspan="5" class="windowbg centertext">{$T.member_has_no_topics}</td>
        </tr>
      {/if}
      </tbody>
    </table>
  {else}
    <div class="posts_container">
      {$C.alt_row = false}
      {foreach $C.posts as $post}
        {call postbit_compact message=$post}
        {$C.alt_row = !$C.alt_row}
      {/foreach}
    </div>
  {/if}
{else}
  <table class="table_grid mlist" style="width:100%;">
    <thead>
      <tr>
        <th class="glass lefttext" scope="col" style="width:25%;">
          <a href="{$SUPPORT->url_parse('?action=profile;u='|cat:$C.current_member|cat:';area=showposts;sa=attach;sort=filename'|cat:(($C.sort_direction == 'down' and $C.sort_order == 'filename') ? ';asc' : ''))}">
            {$T.show_attach_filename}
            {($C.sort_order == 'filename') ? ('<img src="'|cat:$S.images_url|cat:'/sort_'|cat:(($C.sort_direction == 'down') ? 'down' : 'up')|cat:'.gif" alt="" />') : ''}
          </a>
        </th>
        <th class="glass" scope="col" style="width:12%;">
          <a href="{$SUPPORT->url_parse('?action=profile;u='|cat:$C.current_member|cat:';area=showposts;sa=attach;sort=downloads'|cat:(($C.sort_direction == 'down' and $C.sort_order == 'downloads') ? ';asc' : ''))}">
            {$T.show_attach_downloads}
            {($C.sort_order == 'downloads') ? ('<img src="'|cat:$S.images_url|cat:'/sort_'|cat:(($C.sort_direction == 'down') ? 'down' : 'up')|cat:'.gif" alt="" />') : ''}
          </a>
        </th>
        <th class="glass lefttext" scope="col" style="width:30%;">
          <a href="{$SUPPORT->url_parse('?action=profile;u='|cat:$C.current_member|cat:';area=showposts;sa=attach;sort=subject'|cat:(($C.sort_direction == 'down' and $C.sort_order == 'subject') ? ';asc' : ''))}">
            {$T.message}
            {($C.sort_order == 'subject') ? ('<img src="'|cat:$S.images_url|cat:'/sort_'|cat:(($C.sort_direction == 'down') ? 'down' : 'up')|cat:'.gif" alt="" />') : ''}
          </a>
        </th>
        <th class="glass last_th lefttext" scope="col">
          <a href="{$SUPPORT->url_parse('?action=profile;u='|cat:$C.current_member|cat:';area=showposts;sa=attach;sort=posted'|cat:(($C.sort_direction == 'down' and $C.sort_order == 'posted') ? ';asc' : ''))}">
          {$T.show_attach_posted}
            {($C.sort_order == 'posted') ? ('<img src="'|cat:$S.images_url|cat:'/sort_'|cat:(($C.sort_direction == 'down') ? 'down' : 'up')|cat:'.gif" alt="" />') : ''}
          </a>
        </th>
      </tr>
    </thead>
    <tbody>
    {$alternate = false}
    {foreach $C.attachments as $attachment}
      <tr class="tablerow{($alternate) ? ' alternate' : ''}{($attachment.approved) ?  ' approvebg' : ''}">
        <td><a href="{$SCRIPTURL}?action=dlattach;topic={$attachment.topic}.0;attach={$attachment.id}">{$attachment.filename}</a>{(!$attachment.approved) ? ('&nbsp;<em>('|cat:$T.awaiting_approval|cat:')</em>') : ''}</td>
        <td class="centertext">{$attachment.downloads}</td>
        <td><a href="{$SCRIPTURL}?topic={$attachment.topic}.msg{$attachment.msg}#msg{$attachment.msg}" rel="nofollow">{$attachment.subject}</a></td>
        <td>{$attachment.posted}</td>
      </tr>
      {$alternate = !$alternate}
    {/foreach}
    {if (isset($C.attachments) and empty($C.attachments)) or (!isset($C.attachments) and empty($C.posts))}
      <tr>
        <td class="tborder windowbg2 padding centertext" colspan="4">
          {(isset($C.attachments)) ? $T.show_attachments_none : (($C.is_topics) ? $T.show_topics_none : $T.show_posts_none)}
        </td>
      </tr>
    {/if}
    </tbody>
  </table>
{/if}
{if $C.results_counter}
  <div class="pagelinks pagesection">
    {$C.page_index}
  </div>
{/if}