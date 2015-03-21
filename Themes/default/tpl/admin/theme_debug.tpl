<div>
  <strong><a href="#" onClick="$('#theme_debug_content').show(); return false;">{$T.theme_debug_title}</a></strong>
  <div id="theme_debug_content" style="display:none; margin:10px;">
    {$C.theme_debug.template_hooks}<br>
    <strong><span class="alert">{$T.theme_debug_template_dirs}</span></strong><br>
    {$C.theme_debug.template_dirs}
  </div>
</div>