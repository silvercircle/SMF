<h1 class="bigheader section_header bordered">
  {$T.mc_topicbans_view}
</h1>
<div class="orange_container smalltext smallpadding norounded cleantop">
  {$C.topicban_view_desc}
</div>
{if isset($C.error)}
  <br>
  <div class="red_container norounded smalltext mediumpadding centertext">
    {$C.error}
  </div>
{else}
  <div class="pagesection pagelinks">
  	{$C.pages}
  </div>
  {if $C.total_items}
  <table class="table_grid" id="topicbans_table" style="width:100%;">
    <thead>
      <th class="glass first_th lefftext nowrap">
        {$T.who_member}
      </th>
      <th class="glass lefttext" style="width:100%;">
        {$T.topic}
      </th>
      <th class="glass nowrap">
        {$T.mc_topicban_list_issue_time}
      </th>
    </thead>
    <tbody>
      {foreach $C.topicbans as $ban}
        <tr>
        <td class="nowrap" style="width:25%;">
          {$ban.member.link}
        </td>
        <td>
          {$ban.topic.link}
          <br>
          <span class="tinytext">{$ban.reason}</span>
        </td>
        <td class="nowrap">
          {$ban.issue_time}
          <br>
          <span class="tinytext">{$ban.expires}</span>
        </td>
        </tr>
      {/foreach}
    </tbody>
  </table>
  {else}
  <div class="red_container mediumpadding smalltext norounded centertext">
    {$T.mc_topicban_list_empty}
  </div>
  {/if}
{/if}