{function boardlisting}
{$i = 0}
{$limit = ceil($C.num_boards / 2)}
{$nextcolumn = false}
<div class="boardlisting left">
  {if isset($C.categories)}
  <ul>
  {foreach $C.categories as $category}
    {if count($category.boards) > $limit}
      {$nextcolumn = true}
    {/if}
    {if $nextcolumn}
      </ul>
      </div>
      <div class="boardlisting right">
      <ul>
    {/if}
    <li class="category">
      <strong><a href="javascript:void(0);" onclick="selectBoards([{', '|implode:$category.child_ids}]); return false;">{$category.name}</a></strong>
    </li>
    {$i = $i + 1}
    {if $i >= $limit}
      {$nextcolumn = true}
    {/if}
    {foreach $category.boards as $board}
      <li class="board">
        <label for="{$prefix}{$board.id}"><input type="checkbox" id="{$prefix}{$board.id}" name="{$prefix}[{$board.id}]" value="{$board.id}"{($board.selected) ? ' checked="checked"' : ''} class="input_check" />
          {($board.child_level > 0) ? ('<span class="smalltext">&#9492;&nbsp;'|cat:('-'|str_repeat:$board.child_level)) : '<strong>'}{$board.name}{($board.child_level == 0) ? '</strong>' : '</span>'}
        </label>
      </li>
      {$i = $i + 1}
      {if $i >= $limit}
        {$nextcolumn = true}
      {/if}
    {/foreach}
  {/foreach}
  </ul>
  {/ul}
</div>
{/function}