<div id="main_menu">
<ul class="dropmenu" id="menu_nav">
{foreach from=$C.menu_buttons key=act item=mainbutton}
  {$has_subitems = !empty($mainbutton.sub_buttons)}
  <li class="{(!empty($mainbutton.active_button)) ? 'active' : ''}" id="button_{$act}">
    <a class="firstlevel" href="{$mainbutton.href}" {(isset($mainbutton.target)) ? 'target="{$mainbutton.target}"' : ''}>
      <span class="{(isset($mainbutton.is_last)) ? 'last ' : ''}firstlevel">{$mainbutton.title}</span>
    </a>
    {if $has_subitems}
      <span onclick="onMenuArrowClick($(this));" style="display:inline-block;" id="_{$act}" class="m_downarrow">&nbsp;</span>
      <ul>
      {foreach from=$mainbutton.sub_buttons item=sub_button}
      <li>
        <a href="{$sub_button.href}" {(isset($sub_button.target)) ? 'target="{$sub_button_target}"' : ''}>
          <span class="{(isset($sub_button.is_last)) ? 'last' : ''}">{$sub_button.title}{(isset($sub_button.sub_buttons)) ? '...' : ''}</span>
        </a>
      {* 3rd level menus :) *}
      {if isset($sub_button.sub_buttons)}
      <ul>
      {foreach from=$sub_button.sub_buttons item=sub_sub_button}
        <li>
          <a href="{$sub_sub_button.href}" {(isset($sub_sub_button.target)) ? 'target="{$sub_sub_button.target}"' : ''}>
            <span class="{(isset($sub_sub_button.is_last)) ? 'last' : ''}">{$sub_sub_button.title}</span>
          </a>
        </li>
      {/foreach}
      </ul>
      {/if}
      </li>
    {/foreach}
    </ul>
  {/if}
  </li>
{/foreach}
</ul><div class="clear"></div>
</div>
