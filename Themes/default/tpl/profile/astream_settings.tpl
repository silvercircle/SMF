<form method="post" action="{$C.submiturl}">
  <h1 class="bigheader section_header bordered">{$T.activities_label}</h3>
    <div class="orange_container cleantop smalltext mediumpadding">
      {$T.act_optout_desc}
    </div>
  <br>
  <div class="blue_container">
    <div class="content">
      <ol class="commonlist">
      {$alternate = false}
      {foreach $C.activity_types as $t}
        {if !empty($t.longdesc_act)}
          <li class="tablerow{($alternate) ? ' alternate' : ''}">
            <dl class="settings">
              <dt style="width:90%;">
                {$t.longdesc_act}
              </dt>
              <dd style="width:10%;">
                <input type="checkbox" class="input_check" name="act_check_{$t.id}" id="act_check_{$t.id}" {($t.act_optout) ? '' : 'checked="checked"'} />
              </dd>
            </dl>
          </li>
          {$alternate = !$alternate}
        {/if}
      {/foreach}
      </ol>
    </div>
  </div>
  <br>
  <h1 class="bigheader section_header bordered">{$T.notifications_label}</h3>
  <div class="orange_container cleantop smalltext mediumpadding">
    {$T.notify_optout_desc}
  </div>
  <br>
  <div class="blue_container">
    <div class="content">
      <ol class="commonlist">
      {$alternate = false}
      {foreach $C.activity_types as $t}
        {if !empty($t.longdesc_not)}
          <li class="tablerow{($alternate) ? ' alternate' : ''}">
            <dl class="settings">
              <dt style="width:90%;">
                {$t.longdesc_not}
              </dt>
              <dd style="width:10%;">
                <input type="checkbox" class="input_check" name="not_check_{$t.id}" id="not_check_{$t.id}" {($t.notify_optout) ? '' : 'checked="checked"'} />
              </dd>
            </dl>
          </li>
          {$alternate = !$alternate}
        {/if}
      {/foreach}
      </ol>
    </div>
  </div>
  <br>
  <div class="floatright mediumpadding">
   <input type="submit" class="default" value="{$T.change_profile}" />
  </div>
  <div class="clear"></div>
</form>
