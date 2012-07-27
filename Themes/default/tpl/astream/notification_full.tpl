{extends "base.tpl"}
{block content}
{include "astream/bits.tpl"}
  <div class="cat_bar2 norounded">
    <h3>{$T.act_recent_notifications}</h3>
  </div>
  {if $C.act_results}
    <ol id="notifylist" class="commonlist notifications">
      {foreach $C.activities as $activity}
        {call activitybit a=$activity}
      {/foreach}
    </ol>
  {else}
    <div class="red_container cleantop centertext smalltext">'
      {$T.act_no_unread_notifications}
    </div>
  {/if}
  <div class="yellow_container smalltext cleantop">
    <dl class="common">
    <dt>
    {if $C.act_results and $C.unread_count > 0}
      <a onclick="markAllNotificationsRead();return(false);" href="',$scripturl,'?action=astream;sa=markread;act=all">',$txt['act_mark_all_read'],'</a>
    {/if}
    </dt>
    <dd class="righttext">
      {if !$C.view_all}
        <a href="{$SCRIPTURL}?action=astream;sa=notifications;view=all">{$T.act_view_all}</a>
      {/if}
    </dd>
    </dl>
  </div>
  {call notifications_scripts}
{/block}