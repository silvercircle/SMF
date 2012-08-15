<div id="profileview">
  <div id="generalstats">
    <div class="cat_bar2">
        <h3>
          {$T.statPanel_generalStats} - {$C.member.name}
        </h3>
    </div>
    <div class="blue_container cleantop mediumpadding">
      <div class="content">
      <dl class="common leftbalance">
        <dt class="nowrap">{$T.statPanel_total_time_online}:</dt>
        <dd>{$C.time_logged_in}</dd>
        <dt class="nowrap">{$T.statPanel_total_posts}:</dt>
        <dd>{$C.num_posts} {$T.statPanel_posts}</dd>
        <dt>{$T.statPanel_total_topics}:</dt>
        <dd>{$C.num_topics} {$T.statPanel_topics}</dd>
        <dt>{$T.statPanel_users_polls}:</dt>
        <dd>{$C.num_polls} {$T.statPanel_polls}</dd>
        <dt>{$T.statPanel_users_votes}:</dt>
        <dd>{$C.num_votes} {$T.statPanel_votes}</dd>
      </dl>
      </div>
    </div>
  </div>
  <div id="activitytime" class="flow_hidden">
    <div class="cat_bar2">
      <h3>
        {$T.statPanel_activityTime}
      </h3>
    </div>
    <div class="content blue_container cleantop mediumpadding">
    {if empty($C.posts_by_time)}
      <span>{$T.statPanel_noPosts}</span>
    {else}
      <ul class="activity_stats flow_hidden">
      {foreach $C.posts_by_time as $time_of_day}
        <li{($time_of_day.is_last) ? ' class="last"' : ''}>
          <div class="bar" style="padding-top: {(100 - $time_of_day.relative_percent)}px;" title="{$T.statPanel_activityTime_posts|sprintf:$time_of_day.posts:$time_of_day.posts_percent}">
            <div style="height: {$time_of_day.relative_percent}px;">
              <span>{$T.statPanel_activityTime_posts|sprintf:$time_of_day.posts:$time_of_day.posts_percent}</span>
            </div>
          </div>
          <span class="stats_hour tinytext">{$time_of_day.hour_format}</span>
        </li>
      {/foreach}
      </ul>
    {/if}
    <span class="clear"></span>
    </div>
  </div>
  <div class="flow_hidden">
    <div id="popularposts">
      <div class="cat_bar2">
        <h3>
          {$T.statPanel_topBoards}
        </h3>
      </div>
      <div class="content blue_container cleantop mediumpadding">
      {if empty($C.popular_boards)}
        <span>{$T.statPanel_noPosts}</span>
      {else}
        <dl>
        {foreach $C.popular_boards as $board}
          <dt>{$board.link}</dt>
          <dd>
            <div class="profile_pie" style="background-position: -{(int)(($board.posts_percent / 5)) * 20}px 0;" title="{$T.statPanel_topBoards_memberposts|sprintf:$board.posts:$board.total_posts_member:$board.posts_percent}">
              {$T.statPanel_topBoards_memberposts|sprintf:$board.posts:$board.total_posts_member:$board.posts_percent}
            </div>
            <span>{(empty($C.hide_num_posts)) ? $board.posts : ''}</span>
          </dd>
        {/foreach}
        </dl>
      {/if}
      </div>
    </div>
    <div id="popularactivity">
      <div class="cat_bar2">
        <h3>
          {$T.statPanel_topBoardsActivity}
        </h3>
      </div>
      <div class="content blue_container cleantop mediumpadding">
      {if empty($C.board_activity)}
        <span>{$T.statPanel_noPosts}</span>
      {else}
        <dl>
        {foreach $C.board_activity as $activity}
          <dt>{$activity.link}</dt>
          <dd>
            <div class="profile_pie" style="background-position: -{(int)(($activity['percent'] / 5)) * 20}px 0;" title="{$T.statPanel_topBoards_posts|sprintf:$activity.posts:$activity.total_posts:$activity.posts_percent}">
              {$T.statPanel_topBoards_posts|sprintf:$activity.posts:$activity.total_posts:$activity.posts_percent}
            </div>
            <span>{$activity.percent}%</span>
          </dd>
        {/foreach}
        </dl>
      {/if}
      </div>
    </div>
  </div>
</div>
<br class="clear">