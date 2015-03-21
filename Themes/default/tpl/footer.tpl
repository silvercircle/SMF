  <div class="clear" id="footer_section">
    {$SUPPORT->displayHook('theme_debug_output')}
    <div>
      {*customization space in the footer*}
      {$SUPPORT->displayHook('footer_area')}
    </div>
  <div class="righttext floatright">{$loadtime}<br>
    <a onclick="Eos_Confirm('', '{$T.clear_cookies_warning}', Clear_Cookies);" href="#">{$T.clear_cookies}</a> | {$T.forum_time}{$C.template_time_now_formatted} {$C.template_timezone}</div>
  <div class="copyright">
    <span>{$C.template_copyright}</span>
  </div>
  <div>
    <a id="button_xhtml" href="http://validator.w3.org/check?uri=referer" target="_blank" class="new_win" title="Valid HTML"><span>HTML</span></a> |
    {($C.template_allow_rss) ? ('<a id="button_rss" href="'|cat:$SCRIPTURL|cat:'?action=.xml;type=rss" class="new_win"><span>'|cat:$T.rss|cat:'</span></a>') : ''}
  {if $C.mobile}
    | <a href="{$SCRIPTURL}?mobile=0">Full version</a>
  {else}
    | <a href="{$SCRIPTURL}?mobile=1">Mobile</a>
  {/if}
  </div>
  </div>