{*
 * @name      EosAlpha BBS
 * @copyright 2011 Alex Vie silvercircle(AT)gmail(DOT)com
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright: 2011 Simple Machines (http://www.simplemachines.org)
 * license:   BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version 1.0pre
 *
 * side bar template for the board index
 *}
{$widgetstyle = 'framed_region cleantop tinypadding'}
{$C.template_hooks.global.sidebar_top}
{$SUPPORT->displayHook('sidebar_top')}
<script>
// <![CDATA[
  sidebar_content_loaded = 1;
// ]]>
</script>
{call collapser id='user_panel' title='User panel' widgetstyle='framed_region cleantop tinypadding'}
{* If the user is logged in, display stuff like their name, new messages, etc. *}
<div class="blue_container norounded gradient_darken_down nopadding">
{if $C.user.is_logged}
  <div class="smallpadding">
  <div class="smalltext user">
  {if !empty($C.user.avatar.image)}
    <div class="avatar floatleft">{$C.user.avatar.image}</div>
  {else}
    <div class="avatar floatleft"><img src="{$S.images_url}/unknown.png" alt="avatar" /></div>
  {/if}
  <ul class="reset" style="line-height:110%;">
    <li class="greeting"><a href="{$SUPPORT->url_user($C.user.id, $C.user.name)}">{$C.user.name}</a></li>
    <li class="smalltext">{$U.posts} {$T.posts}<li>
    <li class="smalltext">{$U.likesreceived} {$T.likes}<li>
    <li class="floatright smalltext"><a href="{$SCRIPTURL}?action=logout;{$C.session_var}={$C.session_id}">Sign out</a></li>
  </ul>

  <div class="clear"></div>
  </div>
  {if !empty($C.num_buddies)}
    <div class="smalltext" style="margin-bottom:5px;">
    <h1 class="bigheader secondary">{$C.num_buddies} {($C.num_buddies > 1) ? $T.buddies : $T.buddy} {$T.online}.</h1>
    <span class="tinytext">{", "|implode:$C.buddies_online}</span>
    </div>
  {/if}
  <div style="margin-top:3px;">
  {* Is the forum in maintenance mode? *}
  {if $C.in_maintenance and $C.user.is_admin}
    <div class="errorbox smallpadding">{$T.maintain_mode_on}</div>
  {/if}
  {* Are there any members waiting for approval? *}
  {if !empty($C.unapproved_members)}
    <div>{($C.unapproved_members == 1) ? $T.approve_thereis : $T.approve_thereare} <a href="{$SCRIPTURL}?action=admin;area=viewmembers;sa=browse;type=approve">{($C.unapproved_members == 1) ? $T.approve_member : ($C.unapproved_members|cat:' '|cat:$T.approve_members)}</a>{$T.approve_members_waiting}</div>
  {/if}
  {if $C.open_mod_reports > 0 and $C.show_open_reports > 0}
    <div><a href="{$SCRIPTURL}?action=moderate;area=reports">{$T.mod_reports_waiting|sprintf:$C.open_mod_reports}</a></div>
  {/if}
  </div>
  </div>
  </div>
  {$SUPPORT->displayHook('sidebar_userblock')}
  {* Otherwise they're a guest - this time ask them to either register or login - lazy bums...*}
{else}
  <div class="smalltext smallpadding">
  <script type="text/javascript" src="{$S.default_theme_url}/scripts/min/sha1.js{$C.jsver}"></script>
  <div>
    <form id="guest_form" action="{$SCRIPTURL}?action=login2" method="post" accept-charset="UTF-8" {(empty($C.disable_login_hasing)) ? " onsubmit=\"hashLoginPassword(this, '{$C.session_id}');\" " : ''}>
      <h1 class="bigheader secondary">{$T.welcome_guest|sprintf:$T.guest_title}</h1>
        <table>
          <tr>
            <td class="nowrap"><strong>{$T.username}:</strong></td>
            <td><input type="text" name="user" size="20" class="input_text" /></td>
          </tr>
          <tr>
            <td class="nowrap"><strong>{$T.password}:</strong></td>
            <td><input type="password" name="passwrd" size="20" class="input_password" /></td>
          </tr>
        </table>
        <span style="line-height:20px;">{$T.always_logged_in}<input type="checkbox" name="cookielength" value="-1"></span>
        <input style="width:90%;margin-left:5%;margin-top:10px;" type="submit" value="{$T.login}" class="button_submit" /><br />
        {if $M.enableOpenID}
          <br>
          <input type="text" name="openid_identifier" id="openid_url" size="25" class="input_text openid_login" />
        {/if}
        <input type="hidden" name="hash_passwrd" value="" />
    </form>
    <br>
    {if !(!empty($M.registration_method) and $M.registration_method == 3)}
        {$T.login_or_register}
    {else}
      <div class="orange_container">
        {$T.registration_disabled}
      </div>
    {/if}
    </div>
    </div>
  </div>
{/if}
</div>
<div class="cContainer_end"></div>
{$SUPPORT->displayHook('sidebar_below_userblock')}
{* Show statistical style information... *}
{if $S.show_stats_index and !empty($C.show_stats)}
  {call collapser id='stats_panel' title=$T.forum_stats widgetstyle=$widgetstyle}
  <div class="blue_container norounded smallpadding gradient_darken_down">
    <div class="nopadding smalltext">
      {if !empty($C.visible_team_members)}
        <h1 class="bigheader secondary">{$T.team_members_online}</h1>
        <ol class="commonlist" style="margin-bottom:0;">
        {foreach $C.visible_team_members as $id_member}
          {$member = $C.team_members.$id_member}
          <li>
          <div class="userbit_compact">
            <div class="floatleft">
              <span class="small_avatar">
              {if !empty($member.avatar.image)}
                <img class="twentyfour" src="{$member.avatar.href}" alt="avatar" />
              {else}
                <img class="twentyfour" src="{$S.images_url}/unknown.png" alt="avatar" />
              {/if}
              </span>
            </div>
            <div class="userbit_compact_textpart small">
              <h2>{$member.link}</h2><br>
              {if !empty($member.blurb)}
                {$member.blurb}
              {/if}
            </div>
          </div>
          </li>
        {/foreach}
        </ol>
        <div class="clear"></div>
      {/if}
      <h1 class="bigheader secondary">{$T.stats_header}</h1>
      <dl class="common">
        <dt>{$T.posts}: </dt><dd class="righttext">{$C.common_stats.total_posts}</dd>
        <dt>{$T.topics}: </dt><dd class="righttext">{$C.common_stats.total_topics}</dd>
        <dt>{$T.members}: </dt><dd class="righttext">{$C.common_stats.total_members}</dd>
        {if $S.show_latest_member}
          <dt>{$T.latest_member}: </dt><dd class="righttext"><strong><a href="{$SUPPORT->url_user($C.common_stats.latest_member.id, $C.common_stats.latest_member.name)}">{$C.common_stats.latest_member.name}</a></strong></dd>
        {/if}
      </dl>
      <div>
        <div class="clear">
          {($C.show_stats) ? "<a href=\"{$SUPPORT->url_action($SCRIPTURL|cat:'?action=stats')}\">{$T.more_stats}</a>" : ''}
        </div>
      </div>
    </div>
    {$SUPPORT->displayHook('sidebar_infoblock')}
    </div>
  </div>
  <div class="cContainer_end"></div>
{/if}
{* social panel in the side bar *}
{if $C.user.is_guest or ((empty($O.use_share_bar)) ? 1 : !$O.use_share_bar)}
  {call collapser id='social_panel' title='Socialize' widgetstyle=$widgetstyle}
  <div class="blue_container norounded smallpadding gradient_darken_down">
    <div id="socialshareprivacy"></div>
    <div class="clear"></div>
  </div>
  </div>
  <div class="cContainer_end"></div>
{/if}
{* This is the "Recent Posts" bar. *}
{if !empty($S.number_recent_posts) and (!empty($C.latest_posts) or !empty($C.latest_post))}
  {call collapser id='recent_panel' title='<a href="'|cat:$SUPPORT->url_parse($SCRIPTURL|cat:'?action=recent')|cat:'">'|cat:$T.recent_posts|cat:'</a>' widgetstyle=$widgetstyle}
  <div class="blue_container norounded nopadding gradient_darken_down">
    <div class="smalltext" id="recent_posts_content" style="line-height:120%;">
      <div class="entry-title" style="display: none;">{$C.forum_name_html_safe} - {$T.recent_posts}</div>
      <div class="entry-content" style="display: none;">
        <a rel="alternate" type="application/rss+xml" href="{$SCRIPTURL}?action=.xml;type=webslice">{$T.subscribe_webslice}</a>
      </div>
      {if $S.number_recent_posts == 1}
        <strong><a href="{$SCRIPTURL}?action=recent">{$T.recent_posts}</a></strong>
        <p id="infocenter_onepost" class="smalltext">
          {$T.recent_view} &quot;{$C.latest_post.link}&quot; {$T.recent_updated} ({$C.latest_post.time})<br />
      </p>
      {elseif !empty($C.latest_posts)}
        <ol class="commonlist smalltext" style="padding:0;margin:0;">
        {foreach from=$C.latest_posts item=post}
          <li class="smallpadding">
            <a href = "{$post.href}" title="{$post.subject}">{$post.short_subject}</a><br>
            <span class="nowrap floatright tinytext">{$post.time}</span><strong class="tinytext">{$post.poster.link}</strong><br>
          </li>
        {/foreach}
        </ol>
      {/if}
    </div>
    <span class="smalltext smallpadding"><strong><a href="{$SUPPORT->url_action($SCRIPTURL|cat:'?action=recent')}">{$T.recent_view}</a></strong></span>
   </div>
  </div>
  <div class="cContainer_end"></div>
{/if}
{* Show information about events, birthdays, and holidays on the calendar. *}
{if $C.show_calendar}
  {$title = ($C.calendar_only_today) ? $T.calendar_today : ($T.calendar|cat:' (Next '|cat:$M.cal_days_for_index|cat:' days)')}
  {call collapser id='cal_panel' title='<a href="'|cat:$SUPPORT->url_action($SCRIPTURL|cat:'?action=calendar')|cat:'">'|cat:$title|cat:'</a>' widgetstyle=$widgetstyle}
  <div class="blue_container norounded smallpadding gradient_darken_down">
    <div class="smalltext">
    {* Holidays like "Christmas", "Chanukah", and "We Love [Unknown] Day" :P. *}
    {if !empty($C.calendar_holidays)}
      <div class="holiday">{$T.calendar_prompt}</div>{", "|implode:$C.calendar_holidays}<br>
      <div class="cContainer_end"></div>
    {/if}
    {* People's birthdays. Like mine. And yours, I guess. Kidding. *}
    {if !empty($C.calendar_birthdays)}
      <div class="birthday">{($C.calendar_only_today) ? $T.birthdays : $T.birthdays_upcoming}</div>
      {foreach from=$C.calendar_birthdays item=member}
        <a href="{$SUPPORT->url_user($member.id, $member.name)}">{($member.is_today) ? '<strong>' : ''}{$member.name}{($member.is_today) ? '</strong>' : ''}{(!empty($member.age)) ? (' ('|cat:$member.age|cat:')') : ''}</a>{($member.is_last) ? '<br>' : ', '}
      {/foreach}
    {/if}
    {* Events like community get-togethers. *}
    {if !empty($C.calendar_events)}
      <span class="event">{($C.calendar_only_today) ? $T.events : $T.events_upcoming}</span>
      {foreach from=$C.calendar_events item=event}
        {($event.can_edit) ? ('<a href="'|cat:$event.modify_href|cat:'" title="'|cat:$T.calendar_edit|cat:'"><img src="'|cat:$S.images_url|cat:'/icons/modify_small.gif" alt="*" /></a> ') : ''}{($event.href == '') ? '' : ('<a href="'|cat:$event.href|cat:'">')}{($event.is_today) ? ('<strong>'|cat:$event.title|cat:'</strong>') : $event.title}{($event.href == '') ? '' : '</a>'}{($event.is_last) ? '<br>' : ', '}
      {/foreach}
    {/if}
    </div>
    </div>
    </div>
    <div class="cContainer_end"></div>
{/if}
{$C.template_hooks.global.sidebar_bottom}
{$SUPPORT->displayHook('sidebar_bottom')}
