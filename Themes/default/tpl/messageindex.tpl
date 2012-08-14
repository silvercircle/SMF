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
 * *}
{extends 'base.tpl'}
{block content}
{include 'generics.tpl'}
{include 'boardbits.tpl'}
{$widgetstyle = 'framed_region cleantop tinypadding'}
<div id="messageindex">
<a id="top"></a>
<h1 class="bigheader">{$C.name}</h1>
{if !empty($O.show_board_desc) and !empty($C.description)}
  <div class="smalltext">{$C.description}</div>
{/if}

{* the child boards (if we have one and want them to be visible) *}
{if !empty($C.boards) and (!empty($O.show_children) or $C.start == 0)}
  <br>
  {call collapser id=$C.current_board|cat:'_childboards' title=$T.parent_boards widgetstyle=$widgetstyle}
  <div class="framed_region smallpadding">
    <ol id="board_{$C.current_board}_children" class="commonlist category">
    {$C.alt_row = false}
    {foreach from=$C.boards item=board}
      {if $board.ignored == 0}
        {call boardbit board=$board}
        {$C.alt_row = !$C.alt_row}
      {/if}
    {/foreach}
    </ol>
  </div>
  </div>
{/if}
{if $C.hidden_boards.hidden_count}
  <br>
  <div id="show_hidden_boards" class="orange_container norounded gradient_darken_down tinytext">
    <span class="floatright">{$C.hidden_boards.setup_notice}</span><strong> {$C.hidden_boards.notice|sprintf:$C.hidden_boards.hidden_count:'<a onclick="$(\'div#hidden_boards\').fadeIn();return(false);" href="!#">'}</strong>
  </div>
  <div class="category" id="hidden_boards" style="display:none;">
    <div class="framed_region cleantop root_cat" id="category_0">
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
{$can_quickmod = isset($C.can_quick_mod) and !empty($C.can_quick_mod)}
{if $C.act_as_cat == 0}
  {if $C.no_topic_listing == 0}
    <div class="pagesection top smallpadding">
      <div class="pagelinks floatleft">{$C.page_index}&nbsp;&nbsp;<a class="navPages" href="#bot">{$T.go_down}</a></div>
      {$SUPPORT->button_strip($C.normal_buttons, 'right')}
    </div>
    {*If Quick Moderation is enabled start the form. *}
    {if $can_quickmod and $O.display_quick_mod > 0 and !empty($C.topics)}
      <form action="{$SCRIPTURL}?action=quickmod;board={$C.current_board}.{$C.start}" method="post" accept-charset="UTF-8" name="quickModForm" id="quickModForm">
    {/if}
    <div class="framed_region">
      <table class="topic_table">
      {* Are there actually any topics to show? *}
      {if !empty($C.topics)}
        <thead>
        <tr class="mediumpadding">
          <th scope="col" colspan="2" class="first_th glass cleantopr" style="width:8%;">&nbsp;</th>
          <th scope="col" class="lefttext nowrap glass cleantopr">{$C.subject_sort_header}</th>
          <th scope="col" class="nowrap glass cleantopr">{$C.views_sort_header}</th>
          <th scope="col" class="centertext nowrap glass cleantopr">{$C.lastpost_sort_header}</th>
          {* Show a "select all" box for quick moderation? *}
          {if $can_quickmod}
            <th scope="col" class="glass cleantopr last_th" style="width:24px;"><input type="checkbox" class="input_check cb_invertall" /></th>
          {/if}
      {else}
        {* No topics.... just say, "sorry bub". *}
        <thead>
        <tr>
          <th class="red_container"><strong>{$T.msg_alert_none}</strong></th>
      {/if}
        </tr>
        </thead>
      {if !empty($S.display_who_viewing)}
        <tr class="tablerow mediumpadding">
          <td colspan="{($can_quickmod) ? '6' : '5'}" class="smalltext">
          <div class="flat_container borderless smallpadding">
          {if $S.display_who_viewing == 1}
            {count($C.view_members)} {(count($C.view_members) == 1) ? $T.who_member : $T.members}
          {else}
            {$C.full_members_viewing_list}
          {/if}
          {$T.who_and}{$C.view_num_guests} {($C.view_num_guests == 1) ? $T.guest : $T.guests}{$T.who_viewing_board}
          </div>
          </td>
        </tr>
      {/if}
      {* If this person can approve items and we have some awaiting approval tell them. *}
      {if !empty($C.unapproved_posts_message)}
        <tr class="windowbg2">
          <td colspan="{($can_quick_mod) ? '6' : '5' }}">
            <span class="alert">!</span>{$C.unapproved_posts_message}
          </td>
        </tr>
      {/if}
      {$C.alt_row = false}
      {foreach from=$C.topics item=topic}
        {call topicbit topic=$topic}
        {$C.alt_row = !$C.alt_row}
      {/foreach}
      {if $can_quickmod and $O.display_quick_mod and !empty($C.topics)}
        <tr>
          <td colspan="6" class="righttext">
            <select class="qaction" name="qaction"{($C.can_move) ? ' onchange="this.form.moveItTo.disabled = (this.options[this.selectedIndex].value != \'move\');"' : ''}>
              <option value="">--------</option>
              {($C.can_remove) ? ('<option value="remove">'|cat:$T.quick_mod_remove|cat:'</option>') : ''}
              {($C.can_lock) ? ('<option value="lock">'|cat:$T.quick_mod_lock|cat:'</option>') : ''}
              {($C.can_sticky) ? ('<option value="sticky">'|cat:$T.quick_mod_sticky|cat:'</option>') : ''}
              {($C.can_move) ? ('<option value="move">'|cat:$T.quick_mod_move|cat:': </option>') : ''}
              {($C.can_merge) ? ('<option value="merge">'|cat:$T.quick_mod_merge|cat:'</option>') : ''}
              {($C.can_restore) ? ('<option value="restore">'|cat:$T.quick_mod_restore|cat:'</option>') : ''}
              {($C.can_approve) ? ('<option value="approve">'|cat:$T.quick_mod_approve|cat:'</option>') : ''}
              {($C.user.is_logged) ? ('<option value="markread">'|cat:$T.quick_mod_markread|cat:'</option>') : ''}
            </select>
            {* Show a list of boards they can move the topic to. *}
            {if $C.can_move}
              <select class="qaction" id="moveItTo" name="move_to" disabled="disabled">
              {foreach from=$C.move_to_boards item=cat}
                <optgroup label="{$cat.name}">
                {foreach from=$cat.boards item=board}
                  <option value="{$board.id}"{($board.selected) ? ' selected="selected"' : ''}>{($board.child_level > 0) ? (("=="|str_repeat:($board.child_level - 1))|cat:'=&gt;') : ''} {$board.name}</option>
                {/foreach}
              </optgroup>
              {/foreach}
            </select>
            {/if}
            <input type="submit" value="{$T.quick_mod_go}" onclick="return document.forms.quickModForm.qaction.value != '' && confirm('{$T.quickmod_confirm}');" class="button_submit qaction" />
          </td>
        </tr>
      {/if}
        </tbody>
      </table>
    </div>
  {/if}
  <a id="bot"></a>
  {if $can_quickmod and $O.display_quick_mod > 0 and !empty($C.topics)}
    <input type="hidden" name="{$C.session_var}" value="{$C.session_id}" />
    </form>
  {/if}
  <div class="pagesection bottom smallpadding">
    {$SUPPORT->button_strip($C.normal_buttons, 'right')}
    <div class="pagelinks floatleft">{$C.page_index}&nbsp;&nbsp;<a class="navPages" href="#top">{$T.go_up}</a></div>
  </div>
  {/if} {* if C.act_as_cat == 0 % *}
  {include 'linktree.tpl'}
  
  <div class="tborder" id="topic_icons">
    <div class="floatright smallpadding" id="message_index_jump_to">&nbsp;</div>
    <div class="clear"></div>
  </div>
</div>
{/block}
{block footerscripts}
<script>
  // <![CDATA[
  if (typeof(window.XMLHttpRequest) != "undefined")
    aJumpTo[aJumpTo.length] = new JumpTo( {
    sContainerId: "message_index_jump_to",
    sJumpToTemplate: '<label class="smalltext" for="%select_id%">{$C.jump_to.label}:</label> %dropdown_list%',
    iCurBoardId: {$C.current_board},
    iCurBoardChildLevel: {$C.jump_to.child_level},
    sCurBoardName: "{$C.jump_to.board_name}",
    sBoardChildLevelIndicator: "==",
    sBoardPrefix: "=> ",
    sCatSeparator: "-----------------------------",
    sCatPrefix: "",
    sGoButtonLabel: "{$T.quick_mod_go}"
  } );

  // Javascript for inline editing.
  // Hide certain bits during topic edit.
  hide_prefixes.push("lockicon", "stickyicon", "pages", "newicon");

  // Use it to detect when we\'ve stopped editing.
  document.onclick = modify_topic_click;

  var mouse_on_div;
  function modify_topic_click()
  {
    if (in_edit_mode == 1 && mouse_on_div == 0)
      modify_topic_save("{$C.session_id}", "{$C.session_var}");
  }

  function modify_topic_keypress(oEvent)
  {
    if (typeof(oEvent.keyCode) != "undefined" && oEvent.keyCode == 13)
    {
      modify_topic_save("{$C.session_id}", "{$C.session_var}");
      if (typeof(oEvent.preventDefault) == "undefined")
        oEvent.returnValue = false;
      else
        oEvent.preventDefault();
    }
  }

  // For templating, shown when an inline edit is made.
  function modify_topic_show_edit(subject)
  {
    // Just template the subject.
    setInnerHTML(cur_subject_div, '<input type="text" name="subject" value="' + subject + '" size="60" style="width: 95%;" maxlength="80" onkeypress="modify_topic_keypress(event)" class="input_text" /><input type="hidden" name="topic" value="' + cur_topic_id + '" /><input type="hidden" name="msg" value="' + cur_msg_id.substr(4) + '" />');
  }

  // And the reverse for hiding it.
  function modify_topic_hide_edit(subject)
  {
    // Re-template the subject!
    setInnerHTML(cur_subject_div, '<a href="{$SCRIPTURL}?topic=' + cur_topic_id + '.0">' + subject + '<' +'/a>');
  }
  // ]]>
</script>
{/block}
