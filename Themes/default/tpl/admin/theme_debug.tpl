<div>
  <strong><a href="#" onClick="$('#theme_debug_content').show(); return false;">{$T.theme_debug_title}</a></strong>
  <div id="theme_debug_content" style="display:none; margin:10px;">
    {$C.theme_debug.template_hooks}<br>

    <strong><span class="alert">{$T.theme_debug_template_dirs}</span></strong><br>
    {$C.theme_debug.template_dirs}<br>

    <strong><span class="alert">{$T.theme_debug_templates_used}</span></strong><br>
    {$C.theme_debug.templates_used}<br>

    <strong><span class="alert">{$T.theme_debug_compile_dir}</span></strong>&nbsp;
    {$C.theme_debug.compile_dir}<br>

    <strong><span class="alert">{$T.theme_debug_default_dir}</span></strong>&nbsp;
    {$C.theme_debug.default_theme_dir}<br>

    <strong><span class="alert">{$T.theme_debug_primary_css}</span></strong>&nbsp;
    {$C.theme_debug.primary_css}<br>

    <strong><span class="alert">{$T.theme_debug_allow_overrides}</span></strong>&nbsp;
    {$C.theme_debug.allow_overrides}<br>

    <strong><span class="alert">{$T.theme_debug_smarty_version}</span></strong>&nbsp;
    {$C.theme_debug.smarty_version}<br>
  </div>
</div>