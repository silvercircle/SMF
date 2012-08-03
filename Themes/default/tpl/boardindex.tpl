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
 * board index template (the forum "home page"). extends base.twig, does not
 * include the side bar.
 *}
{extends "base.tpl"}
{block 'content'}
  {include 'boardbits.tpl'}
  {$C.template_hooks.boardindex.above_boardlisting}
  {$SUPPORT->displayHook('above_index')}
  <div id="boardindex">
  {foreach from=$C.categories item=category}
  {if !empty($category.boards)}
    {if empty($category.is_root)}
      <div class="category" id="category_{$category.id}">
      <div class="cat_bar2">
      {if $category.can_collapse}
        <div class="csrcwrapper16px floatright"><a onclick="catCollapse($(this));return(false);" data-id="{$category.id}" class="collapse floatright" href="{$category.collapse_href}">{$category.collapse_image}</a></div>
      {/if}
      {if $C.user.is_guest == 0 and $category.new > 0}
        <a class="unreadlink" href="{$SCRIPTURL}?action=unread;c={$category.id}">{$T.view_unread_category}</a>
      {/if}
      <h3>
        {$category.link}
      </h3>
      </div>
      </div>
    {/if}
    <div class="framed_region cleantop {($category.is_root) ? 'root_cat' : 'normal_cat'}" style="{($category.is_collapsed) ? 'display:none;' : ''}" id="category_{$category.id}_boards">
      <ol class="commonlist category">
        {if !empty($category.desc)}
          <li class="cat_desc">
            <h3>{$category.desc}</h3>
          </li>
        {/if}
        {$C.alt_row = false}
        {foreach from=$category.boards item=board}
          {call boardbit board=$board}
          {$C.alt_row = !$C.alt_row}
        {/foreach}
      </ol>
    </div>
    <div class="cContainer_end"></div>
  {/if}
  {/foreach}

  {if $C.hidden_boards.hidden_count}
    <div id="show_hidden_boards" class="orange_container norounded gradient_darken_down tinytext"><span class="floatright">{$C.hidden_boards.setup_notice}</span><strong>{$C.hidden_boards.notice|sprintf:$C.hidden_boards.hidden_count:'<a onclick="$(\'div#category_0\').fadeIn();return(false);" href="!#">'}</strong></div>
    <div class="category" id="category_{$C.hidden_boards.id}" style="display:none;">
    <div class="framed_region cleantop root_cat" id="category_{$C.hidden_boards.id}_boards">
      <ol class="commonlist category">
        {$C.alt_row = false}
        {foreach from=$C.hidden_boards.boards item=board}
            {call boardbit board=$board}
            {$C.alt_row = !$C.alt_row}
        {/foreach}
      </ol>
    </div>
    </div>
    <div class="cContainer_end"></div>
  {/if}
  </div>
  {if $C.user.is_logged}
    <div id="posting_icons" class="floatleft">
    <table>
    <tr>
      <td>
      <div>
        <div style="left:-25px;margin-right:-25px;" class="csrcwrapper24px"><img class="clipsrc _off" src="{$S.images_url}/clipsrc.png" alt="" />
          <img alt="" style="position:absolute;bottom:-4px;right:-28px;" src="{$S.images_url}/new.png" />
        </div>
      </div>
      </td>
      <td class="nowrap smalltext" style="padding-left:28px;">{$T.new_posts}</td>
      <td><div class="csrcwrapper24px"><img class="clipsrc _off" src="{$S.images_url}/clipsrc.png" alt="" /></div></td><td class="nowrap smalltext" style="padding-left:28px;">{$T.old_posts}</td>
      <td><div class="csrcwrapper24px"><img class="clipsrc _redirect" src="{$S.images_url}/clipsrc.png" alt="" /></div></td><td class="nowrap smalltext" style="padding-left:28px;">{$T.redirect_board}</td>
      <td><div class="csrcwrapper24px"><img class="clipsrc _page" src="{$S.images_url}/clipsrc.png" alt="" /></div></td><td class="nowrap smalltext" style="padding-left:28px;">{$T.a_page}</td>
    </tr>
    </table>
    </div>
    {if isset($S.show_mark_read) and !empty($C.categories)}
      <div class="mark_read">
        {$SUPPORT->button_strip($C.mark_read_button, 'right')}
      </div>
    {/if}
  {else}
    <div id="posting_icons" class="flow_hidden">
    <table>
    <tr>
      <td><div class="csrcwrapper24px"><img class="clipsrc _off" src="{$S.images_url}/clipsrc.png" alt="" /></div></td><td class="nowrap smalltext" style="padding-left:28px;">{$T.old_posts}</td>
      <td><div class="csrcwrapper24px"><img class="clipsrc _redirect" src="{$S.images_url}/clipsrc.png" alt="" /></div></td><td class="nowrap smalltext" style="padding-left:28px;">{$T.redirect_board}</td>
      <td><div class="csrcwrapper24px"><img class="clipsrc _page" src="{$S.images_url}/clipsrc.png" alt="" /></div></td><td class="nowrap smalltext" style="padding-left:28px;">{$T.a_page}</td>
    </tr>
    </table>
    </div>
  {/if}
  {* here goes the info center *}
  {$C.template_hooks.boardindex.below_boardlisting}
  {$SUPPORT->displayHook('below_index')}
  <div class="clear_left"></div>
  {if isset($C.show_who)}
    <div class="cat_bar2">
      <h3 class="lefttext">
        {($C.show_who) ? ("<a href=\"{$SUPPORT->url_action($SCRIPTURL|cat:'?action=who')}\">") : ''} {$T.online_users} {($C.show_who) ? '</a>' : ''}
      </h3>
    </div>
    <div class="blue_container smallpadding smalltext cleantop">
      {$T.who_summary|sprintf:$C.num_guests:$C.num_users_online:$M.lastActive}

      {if isset($C.show_who_formatted)}
        {$C.show_who_formatted}
      {/if}

      {($C.show_who) ? ('<br>'|cat:$T.who_showby|cat:'<a href="'|cat:$SUPPORT->url_action($SCRIPTURL|cat:'?action=who;show=all;sort=user')|cat:'">'|cat:$T.username|cat:'</a> | <a href="'|cat:$SUPPORT->url_action($SCRIPTURL|cat:'?action=who;show=all;sort=time')|cat:'">'|cat:$T.who_lastact|cat:'</a>') : ''}
      <p class="inline smalltext">
      {* Assuming there ARE users online... each user in users_online has an id, username, name, group, href, and link. *}
      {if !empty($C.users_online)}
        {$T.users_active|sprintf:$M.lastActive}:<br>{', '|implode:$C.list_users_online}

        {* Showing membergroups? *}
        {if !empty($S.show_group_key) and !empty($C.membergroups)}
          <br />[{']&nbsp;&nbsp;['|implode:$C.membergroups}]
        {/if}
      {/if}
      </p>
      <div class="last smalltext">
        {$T.most_online_today}: <strong>{$M.mostOnlineToday|comma_format}</strong>.
        {$T.most_online_ever}: {$M.mostOnline|comma_format} ({$M.mostDate|timeformat})
      </div>
      {if !empty($C.online_today)}
        <h1 class="bigheader secondary">The following members were online today</h1>
        {', '|implode:$C.online_today}
      {/if} 
    </div>
  {/if}
{/block}
