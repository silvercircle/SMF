<div class="modblock_{($C.alternate) ? 'left' : 'right'}">
<div class="cat_bar">
  <h3>
    <a href="{$SCRIPTURL}?action=moderate;area=reports">{$T.mc_recent_reports}</a>
  </h3>
</div>
<div class="blue_container cleantop">
  <div class="content modbox">
    <ul class="reset">
    {foreach $C.reported_posts as $report}
      <li class="smalltext">
        <a href="{$report.report_href}">{$report.subject}</a> {$T.mc_reportedp_by} {$report.author.link}
      </li>
    {/foreach}
    {if empty($C.reported_posts)}
      <li>
        <strong class="smalltext">{$T.mc_recent_reports_none}</strong>
      </li>
    {/if}
    </ul>
  </div>
</div>
</div>
{if !$C.alternate}
  <br class="clear" />
{/if}
{$C.alternate = !$C.alternate}
