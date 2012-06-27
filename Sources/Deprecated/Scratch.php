<?php
if (!defined('SMF'))
	die('No access');
function foo()
{
	smf_db_query('UPDATE {db_prefix}members AS m 
		SET m.likes_given = (SELECT COUNT(l.id_user) FROM {db_prefix}likes AS l WHERE l.id_user = m.id_member), 
			m.likes_received = (SELECT COUNT(l1.id_receiver) FROM {db_prefix}likes AS l1 WHERE l1.id_receiver = m.id_member)
		WHERE m.id_member = {int:id_member}',array('id_member' => $id));
}
?>

{function unread_replies}
  <div id="recent">
  {$showCheckboxes = !empty($O.display_quick_mod) and $S.show_mark_read}
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
        <div class="pagelinks floatleft">{$C.page_index}</div>
    </div>
    <div class="tborder topic_table" id="unreadreplies">
      <table class="table_grid mlist">
      <thead>
        <tr>
          <th scope="col" class="glass cleantop first_th" style="width:8%;" colspan="2">&nbsp;</th>
          <th class="glass cleantopr" scope="col">
            {$C.subject_sort_header}
          </th>
          <th class="blue_container centertext" scope="col" style="width:14%;">
            {$C.views_sort_header}
          </th>
          {if $showCheckboxes}
            <th class="blue_container" scope="col" style="width:22%;">
              {$C.lastpost_sort_header}
            </th>
            <th class="blue_container last_th">
              <input type="checkbox" onclick="invertAll(this, this.form, \'topics[]\');" class="input_check" />
            </th>
          {else}
            <th scope="col" class="blue_container last_th" style="width:22%;">
              {$C.lastpost_sort_header}
            </th>
          {/if}
        </tr>
      </thead>
      <tbody>
      {foreach $C.topics as $topic}
        {call topicbit topic=$topic}
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

