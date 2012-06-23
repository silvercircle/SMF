{* If linktree is empty, just return - also allow an override. *}

{* if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
  return; *}

{if !empty($C.ltree_done)}
  {$C.ltree_done = 'linktree_upper'|str_ireplace:'linktree_lower':$C.ltree_done}
  {$C.ltree_done}
{else}
{$ltree_done = '<div class="navigate_section gradient_darken_down"><ul class="linktree tinytext" id="linktree_upper">'}

{* Each tree item has a URL and name. Some may have extra_before and extra_after. *}
{$item_count = count($C.linktree)}
{foreach from=$C.linktree item=tree_item}
  {if $smarty.foreach.tree_item.index == item_count - 1}
    {$ltree_done = $ltree_done|cat:'<li class="last">'}
  {else}
    {$ltree_done = $ltree_done|cat:'<li>'}
  {/if}   
  {* Show something before the link? *}
  {if isset($tree_item.extra_before)}
    {$ltree_done = $ltree_done|cat:$tree_item.extra_before}
  {/if}
  {* Show the link, including a URL if it should have one. *}
  {if isset($tree_item.url)}
    {$ltree_done = $ltree_done|cat:'<a itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" href="'|cat:$tree_item.url|cat:'"><span>'|cat:$tree_item.name|cat:'</span></a>'}
  {else}
    {$ltree_done = $ltree_done|cat:'<span>'|cat:$tree_item.name|cat:'</span>'}
  {/if}
  {* Show something after the link...? *}
  {if isset($tree_item.extra_after)}
    {$ltree_done = $ltree_done|cat:$tree_item.extra_after}
  {/if}
  {* Don't show a separator for the last one. *}
  {if $smarty.foreach.tree_item.index != $item_count - 1}
    {$ltree_done = $ltree_done|cat:' &rarr;'}
  {/if}
  {$ltree_done = $ltree_done|cat:'</li>'}
{/foreach}
{$ltree_done = $ltree_done|cat:'</ul></div>'}

{$ltree_done}
{$C.ltree_done = $ltree_done}
{/if}