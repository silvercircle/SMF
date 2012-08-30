{**
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
 *}
{extends 'base.tpl'}
{block content}
{function recentposts}
  {include 'postbits/compact.tpl'}
  <div id="recent" class="main_section">
    <h1 class="bigheader">
        {$T.recent_posts}
    </h1>
    <div class="pagelinks mediumpadding">
      <span>{$C.page_index}</span>
    </div>
    <div class="posts_container std">
    {foreach $C.posts as $message}
      {call postbit_compact}
    {/foreach}
    </div>
    <div class="pagelinks mediumpadding">
      <span>{$C.page_index}</span>
    </div>
  </div>
{/function}

{function unread_topics}
  <div id="recent" class="main_content">
  {if !empty($C.page_header)}
    <h1 class="bigheader">
      {$C.page_header}
    </h1>
  {/if}
  {$showCheckboxes = !empty($O.display_quick_mod) and $S.show_mark_read}
  {$C.can_quick_mod = $showCheckboxes}
  {if $showCheckboxes}
    <form action="{$SCRIPTURL}?action=quickmod" method="post" accept-charset="UTF-8" name="quickModForm" id="quickModForm" style="margin: 0;">
      <input type="hidden" name="{$C.session_var}" value="{$C.session_id}" />
      <input type="hidden" name="qaction" value="markread" />
      <input type="hidden" name="redirect_url" value="action=unread{(!empty($C.showing_all_topics)) ? ';all' : ''}{$C.querystring_board_limits}" />
  {/if}

  {if !empty($C.topics)}
    <div class="pagesection">
    {if !empty($C.mark_read_buttons)}
      {$SUPPORT->button_strip($C.mark_read_buttons, 'right')}
    {/if}
      <div class="pagelinks floatleft">{$C.page_index}</div>
    </div>
    <div class="topic_table framed_region" id="unread">
      <table class="topic_table">
      <thead>
        <tr>
          <th scope="col" style="width:2%;" class="glass first_th" colspan="2">&nbsp;</th>
          <th class="glass" scope="col">
            {$C.subject_sort_header}
          </th>
          <th scope="col" style="width:14%;" class="glass centertext">
            {$C.views_sort_header}
          </th>
          {if $showCheckboxes}
            <th scope="col" style="width:22%;" class="glass">
              {$C.lastpost_sort_header}
            </th>
            <th class="glass valign last_th">
              <input type="checkbox" onclick="invertAll(this, this.form, 'topics[]');" class="input_check aligned" />
            </th>
          {else}
            <th scope="col" class="glass cleantopr smalltext last_th" style="width:22%;">
              {$C.lastpost_sort_header}
            </th>
          {/if}
        </tr>
      </thead>
      <tbody>
      {$C.alt_row = false}
      {foreach $C.topics as $topic}
        {call topicbit topic=$topic}
        {$C.alt_row = !$C.alt_row}
      {/foreach}
      {if !empty($C.topics) and !$C.showing_all_topics}
        {*{$C.mark_read_buttons.readall = array('text' => 'unread_topics_all', 'image' => 'markreadall.gif', 'lang' => true, 'url' => $SCRIPTURL|cat:'?action=unread;all'|cat:$C.querystring_board_limits, 'active' => true)}*}
      {/if}
      {if empty($C.topics)}
        <tr style="display: none;"><td></td></tr>
      {/if}
      </tbody>
    </table>
    </div>
    <div class="pagesection" id="readbuttons">
    {if !empty($C.mark_read_buttons)}
      {$SUPPORT->button_strip($C.mark_read_buttons, 'right')}
    {/if}
    <div class="pagelinks floatleft">{$C.page_index}</div>
  </div>
  {else}
    <div class="framed_region smallpadding">
      <div class="blue_container gradient_darken_down largepadding centertext">
        <h1>
          {($C.showing_all_topics) ? $T.msg_alert_no_unread : $T.unread_topics_visit_none}
        </h1>
      </div>
    </div>
  {/if}
  {if $showCheckboxes}
    </form>
  {/if}
  </div>
{/function}

{function unread_replies}
  <div id="recent">
  {$showCheckboxes = !empty($O.display_quick_mod) and $S.show_mark_read}
  {$C.can_quick_mod = $showCheckboxes}
  {if $showCheckboxes}
    <form action="{$SCRIPTURL}?action=quickmod" method="post" accept-charset="UTF-8" name="quickModForm" id="quickModForm" style="margin: 0;">
      <input type="hidden" name="{$C.session_var}" value="{$C.session_id}" />
      <input type="hidden" name="qaction" value="markread" />
      <input type="hidden" name="redirect_url" value="action=unread{(!empty($C.showing_all_topics)) ? ';all' : ''}{$C.querystring_board_limits}" />
  {/if}
  {if !empty($C.topics)}
    <div class="pagesection">
      {if !empty($C.mark_read_buttons)}
        {$SUPPORT->button_strip($C.mark_read_buttons, 'right')}
      {/if}
        <div class="pagelinks floatleft">{$C.page_index}</div>
    </div>
    <div class="tborder topic_table" id="unreadreplies">
      <table class="table_grid mlist">
      <thead>
        <tr>
          <th scope="col" class="glass first_th" style="width:8%;" colspan="2">&nbsp;</th>
          <th class="glass cleantopr" scope="col">
            {$C.subject_sort_header}
          </th>
          <th class="glass centertext" scope="col" style="width:14%;">
            {$C.views_sort_header}
          </th>
          {if $showCheckboxes}
            <th scope="col" style="width:22%;" class="glass">
              {$C.lastpost_sort_header}
            </th>
            <th class="glass valign last_th">
              <input type="checkbox" onclick="invertAll(this, this.form, 'topics[]');" class="input_check aligned" />
            </th>
          {else}
            <th scope="col" class="glass smalltext last_th" style="width:22%;">
              {$C.lastpost_sort_header}
            </th>
          {/if}
        </tr>
      </thead>
      <tbody>
      {$C.alt_row = false}
      {foreach $C.topics as $topic}
        {call topicbit topic=$topic}
        {$C.alt_row = !$C.alt_row}
      {/foreach}
      </tbody>
    </table>
  </div>
  <div class="pagesection">
    {if !empty($C.mark_read_buttons)}
      {$SUPPORT->button_strip($C.mark_read_buttons, 'right')}
    {/if}
    <div class="pagelinks floatleft">{$C.page_index}</div>
  </div>
  {else}
    <div class="framed_region smallpadding">
      <div class="blue_container gradient_darken_down largepadding centertext">
        <h1>
          {($C.showing_all_topics) ? $T.msg_alert_none : $T.unread_topics_visit_none}
        </h1>
      </div>
    </div>
  {/if}
  {if $showCheckboxes}
    </form>
  {/if}
  </div>
{/function}

{foreach $C.template_functions as $function}
  {call $function}
{/foreach}
{/block}