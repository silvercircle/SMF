<div id="admincenter">
  <div class="cat_bar">
    <h3>{$T.results}</h3>
  </div>
  <br>
  <div id="report_buttons">
  {if !empty($C.report_buttons)}
    {$SUPPORT->button_strip($C.report_buttons, 'right')}
  {/if}
  </div>
  <br class="clear">
  <br>
  {include "reports/content.tpl"}
</div>
<br class="clear">