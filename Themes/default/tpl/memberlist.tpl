{extends "base.tpl"}
{block content}
{function _u_bit}
  {$loc = array()}
  <div class="userbit_compact">
    <span class="floatright">
      {$member.group_stars}
      <br>
      {$member.group}
    </span>
    <div class="floatleft">
      <span class="small_avatar">
      {if !empty($member.avatar.image)}
        <img class="fourtyeight" src="{$member.avatar.href}" alt="avatar" />
      {else}
        <img class="fourtyeight" src="{$S.images_url}/unknown.png" alt="avatar" />
      {/if}
      </span>
    </div>
    <div class="userbit_compact_textpart">
      <h2>{$member.link}</h2>
      <br>
      {if !empty($member.gender.name)}
        {$loc[0] = $member.gender.name}
      {/if}
      {if isset($member.birth_date) and !empty($member.birth_date)}
        {$l = idate('Y', time()) - intval($member.birth_date)}
        {if $l < 100}
          {$loc[1] = $l}
        {/if}
      {/if}
      {if !empty($member.location)}
        {$loc[2] = ' '|cat:$T.ufrom|cat:' '|cat:$member.location}
      {/if}
      {if !empty($loc)}
        {', '|implode:$loc}
      {/if}
      <br>
      {$member.posts} {$T.posts} {$T.and} {$member.liked} {$T.likes}
    </div>
  </div>
{/function}
<div class="main_section" id="memberlist">
  <h1 class="bigheader section_header">
    {$T.members_list}
    {if !isset($C.old_search)}
      <span class="floatright">{$C.letter_links}</span>
    {/if}
  </h1>
  <div class="flat_container cleantop smallpadding">
  {if isset($C.page_index)}
    <div class="pagelinks pagesection">{$C.page_index}</div>
  {/if}
  {if !empty($C.members)}
    <div>
      <ol class="commonlist">
        <li class="glass centertext">
          &nbsp;
        </li>
        {$alternate = false}
        {foreach $C.members as $member}
          <li class="tablerow{($alternate) ? ' alternate' :''} smallpadding">
            {call _u_bit member=$member}
            {$alternate = !$alternate}
          </li>
        {/foreach}
      </ol>
      <br class="clear">
    </div>
  {else}
    <div class="blue_container">{$T.search_no_results}</div>
  {/if}
  {if isset($C.page_index)}
    <div class="pagelinks pagesection">{$C.page_index}</div>
  {/if}
  </div>
  <div class="clear"></div>
</div>
{/block}