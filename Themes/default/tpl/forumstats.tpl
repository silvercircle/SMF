{extends "base.tpl"}
{block content}
  <div id="statistics" class="main_section">
    <h1 class="bigheader">{$C.page_title}</h1>
    <div class="cat_bar2" style="margin-top:10px;">
      <h3>
        <img src="{$S.images_url}/stats_info.gif" class="icon" alt="" /> {$T.general_stats}
      </h3>
    </div>
    <div class="blue_container cleantop">
    <div class="flow_hidden">
      <div id="stats_left">
          <div class="content top_row">
            <dl class="stats">
              <dt>{$T.total_members}:</dt>
              <dd>{($C.show_member_list) ? ('<a href="'|cat:$SCRIPTURL|cat:'?action=mlist">'|cat:$C.num_members|cat:'</a>') : $C.num_members}</dd>
              <dt>{$T.total_posts}:</dt>
              <dd>{$C.num_posts}</dd>
              <dt>{$T.total_topics}:</dt>
              <dd>{$C.num_topics}</dd>
              <dt>{$T.total_cats}:</dt>
              <dd>{$C.num_categories}</dd>
              <dt>{$T.users_online}:</dt>
              <dd>{$C.users_online}</dd>
              <dt>{$T.most_online}:</dt>
              <dd>{$C.most_members_online.number} - {$C.most_members_online.date}</dd>
              <dt>{$T.users_online_today}:</dt>
              <dd>{$C.online_today}</dd>
              {if !empty($M.hitStats)}
                <dt>{$T.num_hits}:</dt>
                <dd>{$C.num_hits}</dd>
              {/if}
            </dl>
            <div class="clear"></div>
        </div>
      </div>
      <div id="stats_right">
          <div class="content top_row">
            <dl class="stats">
              <dt>{$T.average_members}:</dt>
              <dd>{$C.average_members}</dd>
              <dt>{$T.average_posts}:</dt>
              <dd>{$C.average_posts}</dd>
              <dt>{$T.average_topics}:</dt>
              <dd>{$C.average_topics}</dd>
              <dt>{$T.total_boards}:</dt>
              <dd>{$C.num_boards}</dd>
              <dt>{$T.latest_member}:</dt>
              <dd>{$C.common_stats.latest_member.link}</dd>
              <dt>{$T.average_online}:</dt>
              <dd>{$C.average_online}</dd>
              <dt>{$T.gender_ratio}:</dt>
              <dd>{$C.gender.ratio}</dd>
              {if !empty($M.hitStats)}
                <dt>{$T.average_hits}:</dt>
                <dd>{$C.average_hits}</dd>
              {/if}
            </dl>
            <div class="clear"></div>
          </div>
      </div>
    </div></div><br />
    <div class="flow_hidden">
      <div id="top_posters">
        <div class="cat_bar">
          <h3>
            <img src="{$S.images_url}/stats_posters.gif" class="icon" alt="" /> {$T.top_posters}
          </h3>
        </div>
          <div class="blue_container cleantop">
            <div class="content">
              <dl class="stats">
              {foreach $C.top_posters as $poster}
                <dt>
                  {$poster.link}
                </dt>
                <dd class="statsbar">
                {if !empty($poster.post_percent)}
                  <div class="bar" style="width: {$poster.post_percent}px;"></div>
                {/if}
                <span class="righttext">{$poster.num_posts}</span>
                </dd>
              {/foreach}
              </dl>
              <div class="clear"></div>
            </div>
          </div>
      </div>
      <div id="top_boards">
        <div class="cat_bar">
          <h3>
            <img src="{$S.images_url}/stats_board.gif" class="icon" alt="" /> {$T.top_boards}
          </h3>
        </div>
          <div class="blue_container cleantop">
            <div class="content">
              <dl class="stats">
              {foreach $C.top_boards as $board}
                <dt>
                  {$board.link}
                </dt>
                <dd class="statsbar">
                {if !empty($board.post_percent)}
                  <div class="bar" style="width: {$board.post_percent}px;"></div>
                {/if}
                <span class="righttext">{$board.num_posts}</span>
                </dd>
              {/foreach}
              </dl>
              <div class="clear"></div>
            </div>
          </div>
      </div>
    </div>
    <br />
    <div class="flow_hidden">
      <div id="top_topics_replies">
        <div class="cat_bar2">
          <h3>
            <img src="{$S.images_url}/stats_replies.gif" class="icon" alt="" /> {$T.top_topics_replies}
          </h3>
        </div>
          <div class="blue_container cleantop">
            <div class="content">
              <dl class="stats">
              {foreach $C.top_topics_replies as $topic}
                <dt>
                  {$topic.link}
                </dt>
                <dd class="statsbar">
                {if !empty($topic.post_percent)}
                  <div class="bar" style="width: {$topic.post_percent}px;"></div>
                {/if}
                <span class="righttext">{$topic.num_replies}</span>
                </dd>
              {/foreach}
              </dl>
              <div class="clear"></div>
            </div>
          </div>
      </div>
      <div id="top_topics_views">
        <div class="cat_bar">
          <h3>
            <img src="{$S.images_url}/stats_views.gif" class="icon" alt="" /> {$T.top_topics_views}
          </h3>
        </div>
        <div class="blue_container cleantop">
          <div class="content">
            <dl class="stats">
            {foreach $C.top_topics_views as $topic}
              <dt>{$topic.link}</dt>
              <dd class="statsbar">
              {if !empty($topic.post_percent)}
                <div class="bar" style="width: {$topic.post_percent}px;"></div>
              {/if}
              <span class="righttext">{$topic.num_views}</span>
              </dd>
            {/foreach}
            </dl>
            <div class="clear"></div>
          </div>
        </div>
      </div>
    </div>
    <br />
    <div class="flow_hidden">
      <div id="top_topics_starter">
        <div class="cat_bar">
          <h3>
            <img src="{$S.images_url}/stats_replies.gif" class="icon" alt="" /> {$T.top_starters}
          </h3>
        </div>
        <div class="blue_container cleantop">
          <div class="content">
            <dl class="stats">
            {foreach $C.top_starters as $poster}
              <dt>
                {$poster.link}
              </dt>
              <dd class="statsbar">
              {if !empty($poster.post_percent)}
                <div class="bar" style="width: {$poster.post_percent}px;"></div>
              {/if}
              <span class="righttext">{$poster.num_topics}</span>
              </dd>
            {/foreach}
            </dl>
            <div class="clear"></div>
          </div>
        </div>
      </div>
      <div id="most_online">
        <div class="cat_bar">
          <h3>
            <img src="{$S.images_url}/stats_views.gif" class="icon" alt="" /> {$T.most_time_online}
          </h3>
        </div>
        <div class="blue_container cleantop">
          <div class="content">
            <dl class="stats">
            {foreach $C.top_time_online as $poster}
              <dt>
                {$poster.link}
              </dt>
              <dd class="statsbar">
              {if !empty($poster.time_percent)}
                <div class="bar" style="width: {$poster.time_percent}px;"></div>
              {/if}
              <span>{$poster.time_online}</span>
              </dd>
            {/foreach}
            </dl>
            <div class="clear"></div>
          </div>
        </div>
      </div>
    </div>
    <br class="clear" />
    <div class="cat_bar2">
    <h3>
      <img src="{$S.images_url}/stats_history.gif" class="icon" alt="" /> {$T.forum_history}
    </h3>
    </div>
    <div class="blue_container cleantop">
    {if !empty($C.yearly)}
      <table class="table_grid mlist smalltext" id="stats">
        <thead>
          <tr>
            <th class="glass lefttext" style="width:auto;">{$T.yearly_summary}</th>
            <th style="width:15%;" class="glass nowrap">{$T.stats_new_topics}</th>
            <th style="width:15%;" class="glass nowrap">{$T.stats_new_posts}</th>
            <th style="width:15%;" class="glass nowrap">{$T.stats_new_members}</th>
            <th style="width:15%;" class="glass nowrap centertext">{$T.smf_stats_14}</th>
            {if !empty($M.hitStats)}
              <th style="width:200px;" class="glass nowrap">{$T.page_views}</th>
            {/if}
          </tr>
        </thead>
        <tbody>
        {foreach $C.yearly as $id => $year}
          <tr class="windowbg2 centertext" id="year_{$id}">
            <th class="lefttext" style="width:30%;">
              <h1 class="bigheader secondary borderless">{$year.year}</h1>
            </th>
            <th>{$year.new_topics}</th>
            <th>{$year.new_posts}</th>
            <th>{$year.new_members}</th>
            <th>{$year.most_members_online}</th>
            {if !empty($M.hitStats)}
              <th>{$year.hits}</th>
            {/if}
          </tr>
          {foreach $year.months as $month}
            <tr class="windowbg2" id="tr_month_{$month.id}">
              <th class="stats_month">
                <div class="csrcwrapper16px floatleft"><img class="clipsrc {($month.expanded) ? '_collapse' : '_expand'}" src="{$C.clip_image_src}" alt="" id="img_{$month.id}" /></div> <a id="m{$month.id}" href="{$month.href}" onclick="return doingExpandCollapse;">{$month.month} {$month.year}</a>
              </th>
              <th class="centertext">{$month.new_topics}</th>
              <th class="centertext">{$month.new_posts}</th>
              <th class="centertext">{$month.new_members}</th>
              <th class="centertext">{$month.most_members_online}</th>
              {if !empty($M.hitStats)}
                <th class="centertext">{$month.hits}</th>
              {/if}
            </tr>
            {if $month.expanded}
              {foreach $month.days as $day}
                <tr class="windowbg2" id="tr_day_{$day.year}-{$day.month}-{$day.day}">
                  <td class="stats_day">{$day.year}-{$day.month}-{$day.day}</td>
                  <td class="centertext">{$day.new_topics}</td>
                  <td class="centertext">{$day.new_posts}</td>
                  <td class="centertext">{$day.new_members}</td>
                  <td class="centertext">{$day.most_members_online}</td>
                  {if !empty($M.hitStats)}
                    <td class="centertext">{$day.hits}</td>
                  {/if}
                </tr>
              {/foreach}
            {/if}
          {/foreach}
        {/foreach}
        </tbody>
      </table>
    </div>
  </div>
  <script type="text/javascript" src="{$S.default_theme_url}/scripts/stats.js"></script>
  <script type="text/javascript">
    <!-- // --><![CDATA[
    var oStatsCenter = new smf_StatsCenter({
      sTableId: 'stats',

      reYearPattern: /year_(\d+)/,
      sYearImageCollapsed: 'expand.gif',
      sYearImageExpanded: 'collapse.gif',
      sYearImageIdPrefix: 'year_img_',
      sYearLinkIdPrefix: 'year_link_',

      reMonthPattern: /tr_month_(\d+)/,
      sMonthImageCollapsed: 'expand.gif',
      sMonthImageExpanded: 'collapse.gif',
      sMonthImageIdPrefix: 'img_',
      sMonthLinkIdPrefix: 'm',

      reDayPattern: /tr_day_(\d+-\d+-\d+)/,
      sDayRowClassname: 'windowbg2',
      sDayRowIdPrefix: 'tr_day_',
      aCollapsedYears: [
      {foreach $C.collapsed_years as $id => $year}
        '{$year}'{($id != count($C.collapsed_years) - 1) ? ',' : ''}
      {/foreach}
      ],
      aDataCells: [
        'date',
        'new_topics',
        'new_posts',
        'new_members',
        'most_members_online',
        {(empty($M.hitStats)) ? '' : '\'hits\''}
      ]
    } );
    // ]]>
  </script>
  {/if}
{/block}