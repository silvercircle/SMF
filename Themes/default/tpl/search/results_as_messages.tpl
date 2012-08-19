{extends "base.tpl"}
{block content}
{if isset($C.did_you_mean) or empty($C.topics)}
  <div id="search_results">
    <h1 class="bigheader">
      {$T.search_adjust_query}
    </h1>
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
<h1 class="bigheader">
  {$T.mlist_search_results}: {$C.search_params.search}
</h1>
<div class="pagesection pagelinks">
  <span>{$C.page_index}</span>
</div>
{if empty($C.topics)}
  <div class="blue_container">({$T.search_no_results})</div>
{/if}
{$imgsrc = $C.clip_image_src}
{section name=items start=0 loop=100}
  {$topic = $SEARCHCONTEXT->getTopic()}
  {if $topic == false}
    {break}
  {/if}
  {foreach $topic.matches as $message}
    {$ID = $message.id}
    <div class="post_wrapper" data-mid="{$ID}">
    <div class="keyinfo std">
      <div class="messageicon floatleft">
        &nbsp;&nbsp;<img src="{$message.icon_url}" alt="" />&nbsp;&nbsp;
      </div>
      <h3 style="display:inline;" id="subject_{$ID}">
        {$message.subject}
      </h3>
      <span class="smalltext">&nbsp;{$message.time}</span>
      <div id="msg_{$ID}_quick_mod"></div>
    </div>
    <div class="post_content lean">
    {if !empty($message.member)}
      <div class="spacer_h"></div>
      <div class="smalltext" style="line-height:19px;">
      {if !empty($message.sequence_number)}
        <div class="floatright"><strong style="font-size:1.5em;">#{$message.counter}</strong></div>
      {/if}
      {if !empty($S.show_user_images) and empty($O.show_no_avatars)}
        {if !empty($message.member.avatar.image)}
          <span class="small_avatar floatleft">
            {$message.member.avatar.image}
          </span>
        {else}
          <span class="small_avatar floatleft">
            <img src="{$S.images_url}/unknown.png" alt="avatar" />
          </span>
        {/if}
      {/if}
      &nbsp;{$T.posted_by}&nbsp;<strong>{$message.member.link}</strong>
      {if !isset($C.is_display_std)}
        &nbsp;{$T.in}&nbsp;{$message.link}&nbsp;({$T.started_by}&nbsp;<strong>{$topic.first_post.member.link}</strong>,&nbsp;{$topic.first_post.time})<br>
        &nbsp;{$T.board}:&nbsp;<strong>{$topic.board.link}</strong><br>
      {/if}
      <div class="clear"></div>
      </div>
      <hr class="dashed" />
    {/if}
    <div class="post" id="msg_{$ID}">
    {if isset($message.approved)}
      {if !$message.approved and $message.member.id != 0 and $message.member.id == $C.user.id}
      <div class="approve_post">
        {$T.post_awaiting_approval}
      </div>
      {/if}
    {/if}
    <article>
      {$message.body}
    </article>
    </div>
    <div class="moderatorbar">
    {if (isset($message.likes_count) and $message.likes_count > 0) || !empty($message.likelink)}
      <div class="likebar">
        <div class="floatright">{$message.likelink}</div>
        <span id="likers_msg_{$ID}">{$message.likers}</span>
       <div class="clear"></div>
      </div>
    {/if}
    </div>
    </div>
    <div class="post_bottom">
      <div style="display:inline;">
        <ul class="floatright plainbuttonlist">
        {if $topic.can_quote or $topic.can_reply}
          <li>
            <a rel="nofollow" role="button" href="{$SCRIPTURL}?action=post;quote={$ID};topic={$topic.id}.0">
              <div class="csrcwrapper16px"><img class="clipsrc reply" src="{$imgsrc}" alt="{$T.quote}" title="{$T.quote}" /></div>
            </a>
          </li>
        {/if}
        {if $topic.can_mark_notify}
          <li>
            <a rel="nofollow" role="button" href="{$SCRIPTURL}?action=notify;topic={$topic.id}.0">
              <div class="csrcwrapper16px"><img class="clipsrc subscribe" src="{$imgsrc}" alt="{$T.notify}" title="{$T.notify}" /></div>
            </a>
          </li>
        {/if}
        </ul>
        <span class="modified" id="modified_{$ID}">
          {if $S.show_modify and !empty($message.modified.name)}
            <em>{$T.last_edit}: {$message.modified.time} {$T.by} {$message.modified.name}</em>
          {/if}
        </span>
      </div>
      <div class="clear"></div>
    </div>
    <div class="clear"></div>
  </div>
  {/foreach}
{/section}
<div class="pagesection pagelinks">
  <span>{$C.page_index}</span>
</div>
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
    // ]]></script>
{/block}