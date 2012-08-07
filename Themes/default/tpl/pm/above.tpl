<div id="personal_messages">
{if !empty($C.limit_bar)}
  <div class="cat_bar">
    <h3>
      <span class="floatleft">{$T.pm_capacity}:</span>
        <span class="floatleft capacity_bar">
          <span class="{($C.limit_bar.percent > 85) ? 'full' : (($C.limit_bar.percent > 40) ? 'filled' : 'empty')}9999" style="width: {$C.limit_bar.percent / 10}em;"></span>
        </span>
      <span class="floatright', $context['limit_bar']['percent'] > 90 ? ' alert' : '', '">', $context['limit_bar']['text'], '</span>
    </h3>
  </div>
{/if}
{if isset($C.pm_sent)}
  <div class="windowbg" id="profile_success">
    {$T.pm_sent}
  </div>
{/if}
{$SUPPORT->displayHook('pm_above')}
