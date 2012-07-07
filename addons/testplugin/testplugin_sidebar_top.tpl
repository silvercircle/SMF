<div class="blue_container smallpadding gradient_darken_down">
  <h1 class="bigheader secondary">Recent activity</h1>
  <ol class="commonlist">
  {foreach $C.activities as $activity}
    <li>
    {$activity.formatted_result}
    </li>
  {/foreach}
  </ol>
</div>
<div class="cContainer_end"></div>
