<div class="modblock_{($C.alternate) ? 'left' : 'right'}">
<div class="cat_bar">
  <h3>
    <span class="ie6_header floatleft"><a href="{$SCRIPTURL}?action=helpadmin;help=live_news" onclick="return reqWin(this.href);" class="help"><strong>{$T.help}&nbsp;&nbsp;</strong></a>{$T.mc_latest_news}</span>
  </h3>
</div>
<div class="blue_container cleantop">
  <div class="content">
    <div id="smfAnnouncements" class="smalltext">{$T.mc_cannot_connect_sm}</div>
  </div>
</div>
<script type="text/javascript" src="', $scripturl, '?action=viewsmfile;filename=current-version.js"></script>
<script type="text/javascript" src="', $scripturl, '?action=viewsmfile;filename=latest-news.js"></script>
<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/admin.js?fin20"></script>
<script type="text/javascript"><!-- // --><![CDATA[
  var oAdminIndex = new smf_AdminIndex({
    sSelf: 'oAdminCenter',
    bLoadAnnouncements: true,
    sAnnouncementTemplate: {$SUPPORT->JavaScriptEscape("<dl>%content%</dl>")},
    sAnnouncementMessageTemplate: {$SUPPORT->JavaScriptEscape("<dt><a href=\"%href%\">%subject%</a>"|cat:$T.on|cat:" %time%</dt><dd>%message%</dd>")},
    sAnnouncementContainerId: 'smfAnnouncements'
  });
  // ]]>
</script>
</div>
{if !$C.alternate}
  <br class="clear" />
{/if}
{$C.alternate = !$C.alternate}
