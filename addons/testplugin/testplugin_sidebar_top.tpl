{if !empty($C.activities)}
<div class="smallpadding gradient_darken_down">
  <h1 class="bigheader secondary">Recent activity</h1>
  <ol class="commonlist notifications tinypadding">
  {foreach $C.activities as $activity}
    <li>
    {$activity.formatted_result}
    <div class="floatright">{$activity.dateline}</div>
    <div class="clear"></div>
    </li>
  {/foreach}
  </ol>
</div>
<div class="cContainer_end"></div>
{/if}