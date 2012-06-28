{* board bits list one board row, either on board index itself, or on the child forums
 * on the message index*}
{function boardbit}
{if $board.act_as_cat}
  {call boardbit_subcat board=$board}
{else}  
  <li id="board_{$board.id}" class="boardrow gradient_darken_down">
  {if empty($board.is_page)}
    <div class="info">
    <div class="icon floatleft">
      <a href="{($board.is_redirect or $C.user.is_guest) ? $board.href : ($SCRIPTURL|cat:'?action=unread;board='|cat:$board.id|cat:'.0;children')}">
      <div class="csrcwrapper24px">
      {if !empty($board.boardicon)}
        <img src="{$S.images_url}/boards/{$board.boardicon}.png" alt="*" />
        {if $board.new > 0 or $board.children_new > 0}
          <img style="position:absolute;bottom:-4px;right:-3px;" src="{$S.images_url}/new.png" alt="{$T.new_posts}" title="{$T.new_posts}" />
        {/if}
      {else}
        {if $board.is_redirect}
          <img class="clipsrc _redirect" src="{$C.clip_image_src}" alt="*" title="*" />
        {else}
          <img class="clipsrc _off" src="{$C.clip_image_src}" alt="*" />
        {/if}
        {if $board.new > 0 or !empty($board.children_new)}
          <img style="position:absolute;bottom:-4px;right:-3px;" src="{$S.images_url}/new.png" alt="{$T.new_posts}" title="{$T.new_posts}" />
        {/if}
      {/if}
      </div>
      </a>
    </div>
    <div style="padding-left:32px;">
      <a class="brd_rsslink" href="{$SCRIPTURL}?action=.xml;type=rss;board={$board.id}">&nbsp;</a>
      {if !empty($board.moderators)}
        <span onclick="brdModeratorsPopup($(this));" class="brd_moderators" title="{$T.moderated_by}"><span class="brd_moderators_chld" style="display:none;">{$T.moderated_by}: {', '|implode:$board.link_moderators}</span></span>
      {/if}
      <h3>
        <a class="boardlink easytip" data-tip="tip_b_{$board.id}" href="{$board.href}" id="b{$board.id}">{$board.name}</a>
      </h3>
      <div style="display:none;" id="tip_b_{$board.id}">{$board.description}</div>
      {if $board.can_approve_posts and ($board.unapproved_posts or $board.unapproved_topics)}
        <a href="{$SCRIPTURL}?action=moderate;area=postmod;sa={($board.unapproved_topics > 0) ? 'topics' : 'posts'};brd={$board.id};{$C.session_var}={$C.session_id}" title="{$T.unapproved_posts|sprintf:$board.unapproved_topics:$board.unapproved_posts}" class="moderation_link">(!)</a>
      {/if}
      <div class="tinytext">{$board.posts} <span class="lowcontrast">{$T.posts} {$T.in}</span> {$board.topics} <span class="lowcontrast">{$T.topics}</span></div>
      <div class="lastpost tinytext lowcontrast">
      {if !empty($board.last_post.id)}
        {(empty($O.post_icons_index)) ? '' : ("<img src=\"{$board.first_post.icon_url}\" alt=\"icon\" />")}
        {$board.last_post.prefix}{$board.last_post.topiclink}<br>
        <a class="lp_link" title="{$T.last_post}" href="{$board.last_post.href}">{$board.last_post.time}</a>
        <span class="tinytext lowcontrast" {(empty($O.post_icons_index)) ? '' : 'style="padding-left:20px;"'}>{$T.last_post}&nbsp;{$T.by}&nbsp;</span>{$board.last_post.member.link}
      {else}
        {$T.not_applicable}
      {/if}
    </div>
    </div>
    </div>
  {else}
    <div class="info fullwidth">
    <div class="icon floatleft">
      <div class="csrcwrapper24px"><img class="clipsrc _page" src="{$C.clip_image_src}" alt="*" title="*" /></div>
    </div>
    <div style="padding-left:32px;">
      <h3><a class="boardlink" href="{$board.page_link}">{$board.name}</a></h3>
      <div class="tinytext lowcontrast">{$board.description}</div>
    </div>
    </div>
  {/if}
  {* Show the "Child Boards: ". (there's a link_children but we're going to bold the new ones...) *}
  {if !empty($board.children)}
    {call board_children board=$board}
  {else}
    <div></div>
  {/if}
 <div class="clear_left"></div>
</li>
{/if}
{/function}

{function boardbit_subcat}
  <li id="board_'{$board.id}" class="subcatrow">
    <div class="gradient_darken_down smallpadding">
    <div class="info subcat">
     <div class="icon floatleft">
      <a href="{($board.is_redirect or $C.user.is_guest) ? $board.href : ($SCRIPTURL|cat:'?action=unread;board='|cat:$board.id|cat:'.0;children')}">
      <div class="csrcwrapper24px">
    {if !empty($board.boardicon)}
      <img src="{$S.images_url}/boards/{$board.boardicon}.png" alt="*" />
      {if $board.new > 0 or !empty($board.children_new)}
        <img style="position:absolute;bottom:-4px;right:-3px;" src="{$S.images_url}/new.png" alt="{$T.new_posts}" title="{$T.new_posts}" />
      {/if}
    {else}
      <img class="clipsrc _subcat" src="{$S.images_url}/clipsrc.png" alt="*" />
      {if $board.new or !empty($board.children_new)}
        <img style="position:absolute;bottom:-4px;right:-3px;" src="{$S.images_url}/new.png" alt="{$T.new_posts}" title="{$T.new_posts}" />
      {/if}
    {/if}
    </div>
    </a>
    </div>
    <div style="padding-left:32px;">
    <h3 class="subcatlink"><a class="boardlink" href="{$board.href}" id="b{$board.id}">{$board.name}</a></h3>
    {if $board.can_approve_posts and (!empty($board.unapproved_posts) or !empty($board.unapproved_topics))}
      <a href="{$SCRIPTURL}?action=moderate;area=postmod;sa={($board.unapproved_topics > 0) ? 'topics' : 'posts'};brd={$board.id};{$C.session_var}={$C.session_id}" title="{$T.unapproved_posts|sprintf:$board.unapproved_topics:$board.unapproved_posts}" class="moderation_link">(!)</a>
    {/if}
    <div class="tinytext lowcontrast">{$board.description}</div>
    </div>
    </div>
    {if !empty($board.children)}
      {call board_children board=$board}
    {else}
      <div></div>
    {/if}

    {if !empty($board.last_post.id)}
      <div class="tinytext nowrap righttext" style="position:static;max-width:auto;">
      <a class="lp_link" title="{$T.last_post}" href="{$board.last_post.href}">{$board.last_post.time}</a><span class="tinytext lowcontrast">{$T.last_post} in: </span>{$board.last_post.prefix}{$board.last_post.topiclink}
      &nbsp;<span class="tinytext lowcontrast">{$T.by}&nbsp;</span>{$board.last_post.member.link}&nbsp;
    </div>
    {/if}
   <div class="clear_left"></div>
   </div>
  </li>
{/function}

{function board_children}
  <div class="td_children" id="board_{$board.id}_children"><div style="margin-top:-4px;margin-left:-5x;">&#9492;</div>
  <table style="display:table;margin-left:12px;margin-top:-14px;width:99%;">
  <tr>
  {$columns = $M.tidy_child_display_columns}
  {$n = 1}
  {$width = 100 / $columns}
  {foreach from=$board.children item=child}
    {if $child.is_redirect == 0}
      {$link = "<h4 class=\"childlink\"><a data-tip=\"tip_b_{$child.id}\" href=\"{$child.href}\" class=\"child_boardlink easytip\">{$child.name}</a></h4>"}
      {$img = "<div class=\"csrcwrapper16px\" style=\"left:-12px;margin-bottom:-16px;\"><img class=\"clipsrc {($child.new) ? '_child_new' : '_child_old'}\" src=\"{$C.sprite_image_src}\" alt=\"\" /></div>"}
      {$tip = "<div id=\"tip_b_{$child.id}\" style=\"display:none;\">{(!empty($child.description)) ? ($child.description|cat:'<br>') : ''}{($child.new) ? $T.new_posts : $T.old_posts} (' {$T.board_topics}': {$child.topics|comma_format}, {$T.posts}: {$child.posts|comma_format})</div>"}
    {else}
      {$link = "<a class=\"child_boardlink\" href=\"{$child.href}\" title=\"{$child.posts|comma_format} {$T.redirects}\"><h4>{$child.name}</h4></a>&nbsp;<span class=\"tinytext lowcontrast\">({$child.description})</span>"}
      {$img = ''}
      {$tip = ''}
    {/if}

    {if $child.can_approve_posts and (!empty($child.unapproved_posts) or !empty($child.unapproved_topics))}
      {$link = link|cat:" <a href=\"{$SCRIPTURL}?action=moderate;area=postmod;sa={($child.unapproved_topics > 0) ? 'topics' : 'posts'};brd={$child.id};{$C.session_var}={$C.session_id}\" title=\"{$T.unapproved_posts|sprintf:$child.unapproved_topics:$child.unapproved_posts}\" class=\"moderation_link\>(!)</a>"}
    {/if}
    <td style="width: {$width}%;" class="tinytext"><div style="padding-left:12px;">{$img}{$link}</div>{$tip}</td>
    {$n = $n + 1}
    {if $n gt 4}
      {$n = 1}
      </tr><tr>
    {/if}
  {/foreach}
  </tr>
  </table>
  </div>
{/function}
