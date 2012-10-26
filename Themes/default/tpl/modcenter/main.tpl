<div id="modcenter">
  <h1 class="bigheader section_header">{$T.moderation_center}</h1>
  <div class="orange_container cleantop mediumpadding gradient_darken_down">
    <strong>{$T.hello_guest} {$C.user.name}</strong>
    {$T.mc_description}
  </div>
  <br>
  {$C.alternate = true}
  {$SUPPORT->displayHook('modcenter_blocks')}
</div>
<br class="clear" />
