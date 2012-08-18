{if $C.act_results}
  {if isset($C.astream_full_display)}
    <div class="pagelinks mediummargin">{$C.pages}</div>
  {/if}
  <div class="framed_region">
    <ol class="commonlist notifications" style="padding:0;">
      {if isset($C.astream_full_display)}
        <li class="glass centertext">{$C.titletext}</li>
      {/if}
      {$C.alt_row = false}
      {foreach $C.activities as $activity}
        {call activitybit a=$activity}
        {$C.alt_row = !$C.alt_row}
      {/foreach}
    </ol>
  </div>
  {if isset($C.astream_full_display)}
    <div class="pagelinks mediummargin">{$C.pages}</div>
  {/if}
{else}
  <div class="red_container">
    {$T.act_no_results}
  </div>
{/if}
