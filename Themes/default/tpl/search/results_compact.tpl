{if isset($C.did_you_mean) or empty($C.topics)}
  <div id="search_results">
    <div class="cat_bar">
      <h3>
        {$T.search_adjust_query}
      </h3>
  </div>
  <div class="blue_container">
  {if isset($C.did_you_mean)}
    <p>{$T.search_did_you_mean}<a href="{$SUPPORT->url_parse('?action=search2;params='|cat:$C.did_you_mean_params)}">{$C.did_you_mean}</a>.</p>
  {/if}
  <form action="{$SCRIPTURL}?action=search2" method="post" accept-charset="UTF-8">
    <strong>{$T.search_for}:</strong>
    <input type="text" name="search"{(!empty($C.search_params.search)) ? (' value="'|cat:$C.search_params.search|cat:'"') : ''} maxlength="{$C.search_string_limit}" size="40" class="input_text" />
    <input type="submit" name="submit" value="{$T.search_adjust_submit}" class="button_submit" />
    <input type="hidden" name="searchtype" value="{(!empty($C.search_params.searchtype)) ? $C.search_params.searchtype : 0}" />
    <input type="hidden" name="userspec" value="{(!empty($C.search_params.userspec)) ? $C.search_params.userspec : ''}" />
    <input type="hidden" name="show_complete" value="{(!empty($C.search_params.show_complete)) ? 1 : 0}" />
    <input type="hidden" name="subject_only" value="{(!empty($C.search_params.subject_only)) ? 1 : 0}" />
    <input type="hidden" name="minage" value="{(!empty($C.search_params.minage)) ? $C.search_params.minage : '0'}" />
    <input type="hidden" name="maxage" value="{(!empty($C.search_params.maxage)) ? $C.search_params.maxage : '9999'}" />
    <input type="hidden" name="sort" value="{(!empty($C.search_params.sort)) ? $C.search_params.sort : 'relevance'}" />
    {if !empty($C.search_params.brd)}
      {foreach $C.search_params.brd  as $board_id}
        <input type="hidden" name="brd[{$board_id}]" value="{$board_id}" />
      {/foreach}
    {/if}
  </form>
  </div>
  </div>
  <br>
{/if}
{if !empty($O.display_quick_mod)}
  <form action="{$SCRIPTURL}?action=quickmod" method="post" accept-charset="UTF-8" name="topicForm">
{/if}
<h1 class="bigheader">
  <span class="floatright">
    {if !empty($O.display_quick_mod)}
      <input type="checkbox" onclick="invertAll(this, this.form, 'topics[]');" class="input_check" />
    {/if}
  </span>
  {$T.mlist_search_results}:&nbsp;{$C.search_params.search}
</h1>
<div class="pagesection pagelinks">
  {$C.page_index}
</div>
{section name=items start=0 loop=100}
  {$_s = $SEARCHCONTEXT->getTopic()}
  {if $_s == false}
    {break}
  {/if}
  <div class="blue_container smallpadding" style="margin-bottom:10px;">
    <div class="core_posts">
      <div class="flow_auto">
      {foreach $topic.matches as $message}
        {if !empty($message.member.avatar.image)}
          <div class="user floatleft"><div class="avatar" style="margin-right:10px;">{$message.member.avatar.image}</div></div>
        {else}
          <div class="user floatleft">
            <div class="avatar" style="margin:0 10px 0 0;">
              <a href="{$message.member.href}">
                <img src="{$S.images_url}/unknown.png" alt="avatar" />
              </a>
            </div>
          </div>
        {/if}
        <div style="margin-left:90px;">
        {if !empty($O.display_quick_mod)}
          <div class="floatright">
            <input type="checkbox" name="topics[]" value="{$topic.id}" class="input_check" />
          </div>
        {/if}
        <strong>{$message.counter}. <a href="{$message.href}">{$message.subject_highlighted}</a> {$T.in}: {$topic.board.link}</strong><br>
        <div class="smalltext">{$T.topic} {$T.by} <strong>{$message.member.link}</strong>,&nbsp;<em>{$message.time}</em>&nbsp;</div>
        {if $message.body_highlighted != ''}
          <hr style="margin:2px 0;">
          <div class="smalltext">{$message.body_highlighted}</div>
        {/if}
        </div>
      {/foreach}
      </div>
    </div>
  </div>
{/section}
{if !empty($C.topics)}
  <div class="pagesection pagelinks">
    {$C.page_index}
  </div>
{/if}
{if !empty($O.display_quick_mod) and !empty($C.topics)}
  <div class="smalltext blue_container" style="padding: 4px;">
    <div class="floatright">
    <select name="qaction"{($C.can_move) ? (' onchange="this.form.moveItTo.disabled = (this.options[this.selectedIndex].value != \'move\');"') : ''}>
      <option value="">--------</option>
      {($C.can_remove) ? ('<option value="remove">'|cat:$T.quick_mod_remove|cat:'</option>') : ''}
      {($C.can_lock) ? ('<option value="lock">'|cat:$T.quick_mod_lock|cat:'</option>') : ''}
      {($C.can_sticky) ? ('<option value="sticky">'|cat:$T.quick_mod_sticky|cat:'</option>') : ''}
      {($C.can_move) ? ('<option value="move">'|cat:$T.quick_mod_move|cat:': </option>') : ''}
      {($C.can_merge) ? ('<option value="merge">'|cat:$T.quick_mod_merge|cat:'</option>') : ''}
      <option value="markread">{$T.quick_mod_markread}</option>
    </select>
    {if $C.can_move}
      <select id="moveItTo" name="move_to" disabled="disabled">
      {foreach $C.move_to_boards as $category}
        <optgroup label="{$category.name}">
          {foreach $category.boards as $board}
            <option value="{$board.id}"{($board.selected) ? ' selected="selected"' : ''}>{($board.child_level > 0) ? ('=='|str_repeat:$board.child_level-1|cat:'=&gt;') : ''} {$board.name}</option>
          {/foreach}
        </optgroup>
      {/foreach}
      </select>
    {/if}
    <input type="hidden" name="redirect_url" value="{$SUPPORT->url_parse('?action=search2;params='|cat:$C.params)}" />
    <input type="submit" style="font-size: 0.8em;" value="{$T.quick_mod_go}" onclick="return this.form.qaction.value != '' &amp;&amp; confirm('{$T.quickmod_confirm}');" class="button_submit" />
  </div>
  <br class="clear">
  </div>
{/if}
{if !empty($O.display_quick_mod) and !empty($C.topics)}
  <input type="hidden" name="{$C.session_var}" value="{$C.session_id}" />
  </form>
{/if}
<br class="clear">
<div class="smalltext righttext" id="search_jump_to">&nbsp;</div>
<script type="text/javascript"><!-- // --><![CDATA[
  if (typeof(window.XMLHttpRequest) != "undefined")
    aJumpTo[aJumpTo.length] = new JumpTo({
      sContainerId: "search_jump_to",
      sJumpToTemplate: "<label class=\"smalltext\" for=\"%select_id%\">{$C.jump_to.label}:<" + "/label> %dropdown_list%",
      iCurBoardId: 0,
      iCurBoardChildLevel: 0,
      sCurBoardName: "{$C.jump_to.board_name}",
      sBoardChildLevelIndicator: "==",
      sBoardPrefix: "=> ",
      sCatSeparator: "-----------------------------",
      sCatPrefix: "",
      sGoButtonLabel: "{$T.quick_mod_go}"
    } );
  // ]]>
</script>
