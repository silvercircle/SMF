<form action="{$SCRIPTURL}?action=moderate;area=reports{($C.view_closed) ? ';sa=closed' : ''};start={$C.start}" method="post" accept-charset="UTF-8">
  <div class="cat_bar">
    <h3>
      {($C.view_closed) ? $T.mc_reportedp_closed : $T.mc_reportedp_active}
    </h3>
  </div>
  <div class="pagesection">
    <div class="pagelinks">{$C.page_index}</div>
  </div>
  {foreach $C.reports as $report}
    <div class="post_wrapper">
      <div class="content">
        <div>
          <div class="floatleft">
            <strong><a href="{$report.topic_href}">{$report.subject}</a></strong> {$T.mc_reportedp_by} <strong>{$report.author.link}</strong>
          </div>
          <div class="floatright">
            <a href="{$report.report_href}">{$C.details_button}</a>
            <a href="{$SCRIPTURL}?action=moderate;area=reports{($C.view_closed) ? ';sa=closed' : ''};ignore={!$report.ignore};rid={$report.id};start={$C.start};{$C.session_var}={$C.session_id}" {(!$report.ignore) ? ('onclick="return confirm(\''|cat:$T.mc_reportedp_ignore_confirm|cat:'\');"') : ''}>{($report.ignore) ? $C.unignore_button : $C.ignore_button}</a>
            <a href="{$SCRIPTURL}?action=moderate;area=reports{($C.view_closed) ? ';sa=closed' : ''};close={!$report.closed};rid={$report.id};start={$C.start};{$C.session_var}={$C.session_id}">{$C.close_button}</a>
            {(!$C.view_closed) ? ('<input type="checkbox" name="close[]" value="'|cat:$report.id|cat:'" class="input_check" />') : ''}
          </div>
        </div><br>
        <div class="smalltext">
          &#171; {$T.mc_reportedp_last_reported}: {$report.last_updated} &#187;<br />
          {$comments = array()}
          {foreach $report.comments as $comment}
            {$id = $comment.member.id}
            {$comments.$id = $comment.member.link}
          {/foreach}
          &#171; {$T.mc_reportedp_reported_by}: {', '|implode:$comments} &#187;
        </div>
        <hr>
        {$report.body}
      </div>
    </div>
  {/foreach}
  {if empty($C.reports)}
    <div class="orange_container mediumpadding">
      <p class="centertext">{$T.mc_reportedp_none_found}</p>
    </div>
  {/if}
  <div class="pagesection">
    <div class="pagelinks floatleft">
        {$C.page_index}
    </div>
    <div class="floatright">
        {($C.view_closed) ? ('<input type="submit" name="close_selected" value="'|cat:$T.mc_reportedp_close_selected|cat:'" class="button_submit" />') : ''}
    </div>
  </div>
  <input type="hidden" name="{$C.session_var}" value="{$C.session_id}" />
</form>
<br class="clear">