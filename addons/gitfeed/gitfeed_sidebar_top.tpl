{call collapser id='gitfeed_panel' title='Latest commits' widgetstyle='sidebar_section_container cleantop tinypadding' headerclass="sidebar_section_header"}
<div class="nopadding">
  {if isset($C.gitfeed_global)}
    {if $C.gitfeed_global.failed == true}
        <div class="smalltext smallpadding">
          {$C.gitfeed_global.message}
        </div>
    {else}
        <ol class="commonlist tinytext" style="padding:0;">
          {foreach $C.gitfeed as $commit}
            <li>
              <a href="{$commit.href}" class="easytip{($commit.recent == true) ? ' recent_commit' : ''}" data-tip="tip_{$commit.sha}">{$commit.message_short}</a>
              <span class="floatright">
                {$commit.dateline}
              </span>
              <div id="tip_{$commit.sha}" style="display:none;">
                {$commit.message}
              </div>
              <div class="clear"></div>
            </li>
          {/foreach}
          <li class="righttext">
            <div class="righttext">
                Red = recent commit (max. 3 days old)
            </div>
            <a href="{$C.gitfeed_global.see_all.href}">{$C.gitfeed_global.see_all.txt}</a>
          </li>
        </ol>
    {/if}
  {/if}
</div>
</div>
<div class="cContainer_end"></div>
<script>
//<![CDATA[
//]]>
</script>