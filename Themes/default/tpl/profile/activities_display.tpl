{include "astream/bits.tpl"}
{if $C.act_results}
  <ol class="commonlist smalltext">
    <li class="glass centertext">{$C.titletext}</li>
    {$C.alt_row = false}
    {foreach $C.activities as $activity}
      {call activitybit a=$activity}
      {$C.alt_row = !$C.alt_row}
    {/foreach}
  </ol>
{else}
  <div class="red_container">
    {$T.act_no_results}
  </div>
{/if}
