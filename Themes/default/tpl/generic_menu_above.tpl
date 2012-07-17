<div id="main_container">
  <div id="left_admsection"><span id="admin_menu"></span>

  {$C.cur_menu_id = (isset($C.cur_menu_id)) ? $C.cur_menu_id + 1 : 1}
  {$context_id = 'menu_data_'|cat:$C.cur_menu_id}
  {$menu_context = $C.$context_id}

  {$firstSection = true}
  {foreach $menu_context.sections as $section}
    <div class="flat_container minpadding" style="margin-bottom:15px;border-right:0;border-radius:3px 0 0 3px;">
      <div class="cat_bar2">
        <h3>
          {$section.title}
        </h3>
      </div>
      <ul class="smalltext left_admmenu" style="padding:5px 0;">
      {foreach $section.areas as $i => $area}
        {if empty($area.label)}
          {continue}
        {/if}
        {if $i == $menu_context.current_area}
          <li class="active"><a href="{(isset($area.url)) ? $area.url : ($menu_context.base_url|cat:';area='|cat:$i|cat:$menu_context.extra_parameters)}">{$area.label}</a></li>
          {if empty($C.tabs)}
            {$C.tabs = (isset($area.subsections)) ? $area.subsections : array()}
          {/if}
        {else}
          <li><a href="{(isset($area.url)) ? $area.url : ($menu_context.base_url|cat:';area='|cat:$i|cat:$menu_context.extra_parameters)}">{$area.label}</a></li>
        {/if}
      {/foreach}
      </ul>
    </div>
    {$firstSection = false}
  {/foreach}
  </div>
  <div id="main_admsection">
  {if !empty($C.tabs) && empty($C.force_disable_tabs)}
    {include 'generic_menu_tabs.tpl'}
  {/if}
