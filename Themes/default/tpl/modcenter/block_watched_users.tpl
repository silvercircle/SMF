<div class="modblock_{($C.alternate) ? 'left' : 'right'}">
<div class="cat_bar">
  <h3>
    <a href="{$SCRIPTURL}?action=moderate;area=userwatch">{$T.mc_watched_users}</a>
  </h3>
</div>
<div class="blue_container cleantop">
  <div class="content modbox">
    <ul class="reset">
    {foreach $C.watched_users as $user}
      <li>
        <span class="smalltext">{((!empty($user.last_login)) ? $T.mc_seen : $T.mc_seen_never)|sprintf:$user.link:$user.last_login}</span>
      </li>
    {/foreach}
    {if empty($C.watched_users)}
      <li>
        <strong class="smalltext">{$T.mc_watched_users_none}</strong>
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
