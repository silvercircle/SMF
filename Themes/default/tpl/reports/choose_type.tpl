<div id="admincenter">
  <form action="{$SCRIPTURL}?action=admin;area=reports" method="post" accept-charset="UTF-8">
    <div class="cat_bar">
      <h3 class="catbg">{$T.generate_reports}</h3>
    </div>
    <div class="orange_container cleantop mediumpadding smalltext">
      {$T.generate_reports_desc}
    </div>
    <br>
    <div class="cat_bar">
      <h3>{$T.generate_reports_type}</h3>
    </div>
    <div class="blue_container cleantop">
      <div class="content">
        <dl class="generate_report">
        {foreach $C.report_types as $type}
          <dt>
            <input type="radio" id="rt_{$type.id}" name="rt" value="{$type.id}"{($type.is_first) ? ' checked="checked"' : ''} class="input_radio" />
            <strong><label for="rt_{$type.id}">{$type.title}</label></strong>
          </dt>
          {if isset($type.description)}
            <dd>{$type.description}</dd>
          {/if}
        {/foreach}
        </dl>
        <div class="righttext mediumpadding">
          <input type="submit" name="continue" value="{$T.generate_reports_continue}" class="default" />
          {$C.hidden_sid_input}
        </div>
      </div>
    </div>
  </form>
</div>
