  {* only a shortcut
   * menu_context is defined in generic_menu_above.tpl 
   *}
  {$tab_context = $menu_context.tab_data}
  <div class="cat_bar2">
    <h3>
    {foreach $C.tabs as $id => $tab}
      {if !empty($tab.disabled)}
        {$tab_context.tabs.$id.disabled = true}
        {continue}
      {/if}
      {if !isset($tab_context.tabs.$id)}
        {$tab_context.tabs.$id.label = $tab.label}
      {elseif !isset($tab_context.tabs.$id.label)}
        {$tab_context.tabs.$id.label = $tab.label}
      {/if}
      {if isset($tab.url) && !isset($tab_context.tabs.$id.url)}
        {$tab_context.tabs.$id.url = $tab.url}
      {/if}
      {if isset($tab.add_params) && !isset($tab_context.tabs.$id.add_params)}
        {$tab_context.tabs.$id.add_params = $tab.add_params}
      {/if}
      {if !empty($tab.is_selected)}
        {$tab_context.tabs.$id.is_selected = true}
      {/if}
      {if !empty($tab.help)}
        {$tab_context.tabs.$id.help = $tab.help}
      {/if}
      {if !empty($tab.is_last) && !isset($tab_context.override_last)}
        {$tab_context.tabs.$id.is_last = true}
      {/if}
    {/foreach}
    {foreach $tab_context.tabs as $sa => $tab}
      {if !empty($tab.is_selected) || (isset($menu_context.current_subsection) && $menu_context.current_subsection == $sa)}
        {$selected_tab = $tab}
        {$tab_context.tabs.$sa.is_selected = true}
      {/if}
    {/foreach}
    {$tab_context.title}
    </h3>
  </div>
  <div class="orange_container cleantop mediumpadding gradient_darken_down">
    {(!empty($selected_tab.description)) ? $selected_tab.description : $tab_context.description}
  </div>
  <br>
  <div id="adm_submenus">
    <ul class="dropmenu">
    {foreach $tab_context.tabs as $sa => $tab}
      {if !empty($tab.disabled)}
        {continue}
      {/if}
      {if !empty($tab.is_selected)}
        <li class="active">
          <a class="firstlevel" href="{(isset($tab.url)) ? $tab.url : ($menu_context.base_url|cat:';area='|cat:$menu_context.current_area|cat:';sa='|cat:$sa)}{$menu_context.extra_parameters}{(isset($tab.add_params)) ? $tab.add_params : ''}"><span class="firstlevel">{$tab.label}</span></a>
        </li>
      {else}
        <li>
          <a class="firstlevel" href="{(isset($tab.url)) ? $tab.url : ($menu_context.base_url|cat:';area='|cat:$menu_context.current_area|cat:';sa='|cat:$sa)}{$menu_context.extra_parameters}{(isset($tab.add_params)) ? $tab.add_params : ''}"><span class="firstlevel">{$tab.label}</span></a>
        </li>
      {/if}
    {/foreach}
    </ul>
    <div class="clear"></div>
  </div>

