<div>
  {$sortlist = ''}
  {foreach $C.columns as $column}
    {if isset($column.selected) or isset($column.link)}
      {if $column.selected}
        {$sortlist = $sortlist|cat:('<li style="width:80%;"><a class="active" href="'|cat:$column.href|cat:'" rel="nofollow"><span>'|cat:$column.label|cat:' <img src="'|cat:$S.images_url|cat:'/sort_'|cat:$C.sort_direction|cat:'.gif" alt="" /></span></a></li>')}
      {else}
        {$sortlist = $sortlist|cat:('<li style="width:80%;"><a href="'|cat:$column.href|cat:'" rel="nofollow"><span>'|cat:$column.label|cat:'</span></a></li>')}
      {/if}
    {/if}
  {/foreach}
  {if strlen($sortlist)}
    {call collapser id='mlist_sortform' title='Sort'}
      <ul class="centertext buttonlist vertical" style="list-style:none;margin-left:auto;margin-right:auto;">
        {$sortlist}
      </ul>
    </div>
    <br>
  {/if}
  {call collapser id='mlist_sform' title=$T.mlist_search}
  <form action="{$SUPPORT->url_parse('?action=mlist;sa=search')}" method="post" accept-charset="UTF-8">
    <div id="mlist_search" class="flow_hidden">
      <br>
      <div id="search_term_input">
        <input type="text" name="search" value="{$C.old_search}" size="35" style="max-width:95%;" class="input_text" />
      </div>
      <span class="floatleft tinytext">
      {$count = 0}
      {if isset($C.search_fields)}
        {foreach $C.search_fields as $id => $title}
          <label for="fields-{$id}"><input type="checkbox" name="fields[]" id="fields-{$id}" value="{$id}" {(in_array($id, $C.search_defaults)) ? 'checked="checked"' : ''} class="input_check" />{$title}</label><br>
        {/foreach}
      {/if}
      </span>
    </div>
    <br>
    <div class="centertext">
      <input type="submit" name="submit" value="{$T.search}" class="default" />
    </div>
  </form>
  <div class="cContainer_end"></div>
</div>
</div>

