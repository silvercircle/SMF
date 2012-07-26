{extends "modcenter/modcenter_base.tpl"}
{block modcenter_content}
<div id="modcenter">
  <div class="cat_bar2">
    <h3>{$T.moderation_center}</h3>
  </div>
  <div class="orange_container cleantop mediumpadding gradient_darken_down">
    <strong>{$T.hello_guest} {$C.user.name}</strong>
    {$T.mc_description}
  </div>
  <br>
  {$C.alternate = true}
  {$SUPPORT->displayHook('modcenter_blocks')}
</div>
<br class="clear" />
{/block}