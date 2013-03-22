{function format_rating_stats}
  <dl class="smallpadding">
    <dt class="nowrap lowcontrast floatleft">{$T.ratings_overall_received}</dt>
    <dd class="floatright">{$C.rating_stats.count_global}</dd>
    <dt class="nowrap lowcontrast floatleft">{$T.ratings_positive_negative_count}</dt>
    <dd class="floatright nowrap"><span class="ratings_positive"><strong>{$C.rating_stats.count_positive}</strong></span> / <span class="ratings_negative"><strong>{$C.rating_stats.count_negative}</strong></span></dd>
    <dt class="nowrap lowcontrast floatleft">{$T.ratings_neutral_count}</dt>
    <dd class="floatright nowrap"><strong>{$C.rating_stats.count_global - $C.rating_stats.count_positive - $C.rating_stats.count_negative}</strong></dd>
  </dl>
  <dl class="smallpadding">
    <dt class="nowrap lowcontrast floatleft">{$T.ratings_points}</dt>
    <dd class="floatright nowrap"><span class="ratings_positive">+<strong>{$C.rating_stats.points_positive}</strong></span> / <span class="ratings_negative">-<strong>{$C.rating_stats.points_negative}</strong></span></dd>
  </dl>
  <div class="smallpadding">
    {foreach from=$C.rating_labels item=label name=theloop}
      <span style="line-height:22px;">
      <span class="rating_count">{$label.count}</span><span class="rating_x_times">&#215;</span>{$label.label}
      {if !$smarty.foreach.theloop.last}
          &nbsp;&nbsp;
      {/if}
      </span>
    {/foreach}
  </div>
{/function}