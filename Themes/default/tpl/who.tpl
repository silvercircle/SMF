{extends 'base.tpl'}
{block content}
<div class="main_section" id="whos_online">
  <form action="{$SCRIPTURL}?action=who" method="post" id="whoFilter" accept-charset="UTF-8">
    <h1 class="bigheader">{$T.who_title}</h1>
    <div class="table_grid mediumpadding" id="mlist">
      <div class="pagesection">
        <div class="pagelinks floatleft">{$C.page_index}</div>
        <div class="selectbox floatright">{$T.who_show1}
          <select name="show_top" onchange="document.forms.whoFilter.show.value = this.value; document.forms.whoFilter.submit();">
            {foreach from=$C.show_methods item=method key=label}
              <option value="{$label}"{($label == $C.show_by) ? ' selected="selected"' : ''}>{$method}</option>
            {/foreach}
          </select>
          <noscript>
            <input type="submit" name="submit_top" value="{$T.go}" class="button_submit" />
          </noscript>
        </div>
      </div>
      <table class="table_grid">
        <thead>
          <tr>
            <th scope="col" class="centertext glass"><a href="{$SCRIPTURL}?action=who;start={$C.start};show={$C.show_by};sort=user{($C.sort_direction != 'down' and $C.sort_by == 'user') ? '' : ';asc'}" rel="nofollow">{$T.who_user} {($C.sort_by == 'user') ? "<img src=\"{$S.images_url}/sort_{$C.sort_direction}.gif\" alt=\"\" />" : ''}</a></th>
            <th scope="col" class="centertext glass"><a href="{$SCRIPTURL}?action=who;start={$C.start};show={$C.show_by};sort=time{($C.sort_direction == 'down' and $C.sort_by == 'time') ? ';asc' : ''}" rel="nofollow">{$T.who_time} {($C.sort_by == 'time') ? "<img src=\"{$S.images_url}/sort_{$C.sort_direction}.gif\" alt=\"\" />" : ''}</a></th>
            <th scope="col" class="centertext glass">{$T.who_action}</th>
          </tr>
        </thead>
        <tbody>
        {$alternate = 0}
        {$seq = 0}
        {foreach $C.members as $member}
          <tr class="topicrow{($alternate) ? ' alternate' : ''}">
            <td style="width:60%;">
            {if $member.is_guest == 0 and !empty($member.online)}
              <span class="contact_info floatright">
                {($C.can_send_pm) ? ('<a href="{$member.online.href}" title="{$member.online.label}">') : ''} {($S.use_image_buttons) ? ('<img src="'|cat:$member.online.image_href|cat: '" alt="'|cat: $member.online.text|cat:'" />') : $member.online.text}{($C.can_send_pm) ? '</a>' : ''}
              </span>
            {/if}
            <span class="member{($member.is_hidden) ? ' hidden' : '' }">
              {$color = (empty($member.color)) ? '' : " style=\"color: {$member.color};\""}
              {($member.is_guest == 1) ? $member.name : "<a href=\"{$member.href}\" title=\"{$T.profile_of} {$member.name}\" {$color}><strong>{$member.name}</strong></a>"}
            </span>
            {if !empty($member.ip)}
              <a href="{$SCRIPTURL}?action={($member.is_guest) ? 'trackip' : 'profile;area=tracking;sa=ip;u='|cat:$member.id};searchip={$member.ip}">({$member.ip})</a>&nbsp;&nbsp;<span style="cursor:pointer;" onclick="$('#tip_{$seq}').fadeIn();return(false);" class="tinytext lowcontrast">{$member.user_agent_short}</span>
              <div class="tinytext" style="display:none;" id="tip_{$seq}">{$member.user_agent}</div>
            {/if}
            </td>
            <td class="nowrap">{$member.time}</td>
            <td style="width:100%;" class="nowrap">{$member.action}</td>
          </tr>
          {$alternate = !$alternate}
          {$seq = $seq + 1}
        {/foreach}
        {* No members *}
        {if empty($C.members)}
          <tr class="windowbg2">
            <td class="red_container mediumpadding" colspan="3" align="center">
              {$T.who_no_online}
              {if $C.show_by == 'guests'}
                {$T.who_no_online_guests}
              {elseif $C.show_by == 'spiders'}
                {$T.who_no_online_spiders}
              {else}
                {$T.who_no_online_members}
              {/if}
            </td>
          </tr>
        {/if}
        </tbody>
      </table>
    </div>
    <div class="pagesection">
      <div class="pagelinks floatleft">{$C.page_index}</div>
      <div class="selectbox floatright">{$T.who_show1}
        <select name="show" onchange="document.forms.whoFilter.submit();">
          {foreach from=$C.show_methods key=label item=method}
            <option value="{$label}"{($label == $C.show_by) ? ' selected="selected"' : ''}>{$method}</option>
          {/foreach}
        </select>
        <noscript>
          <input type="submit" value="{$T.go}" class="button_submit" />
        </noscript>
      </div>
    </div>
  </form>
</div>
{/block}
