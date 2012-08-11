  <div id="modcenter">
    <form action="{$SCRIPTURL}?action=moderate;area=warnings;sa=templateedit;tid={$C.id_template}" method="post" accept-charset="UTF-8">
      <div class="cat_bar">
        <h3 class="catbg">{$C.page_title}</h3>
      </div>
      <div class="orange_container cleantop mediumpadding gradient_darken_down">
        {$T.mc_warning_template_desc}
      </div>
      <div class="blue_container cleantop">
        <div class="content">
          <dl class="settings">
            <dt>
              <strong><label for="template_title">{$T.mc_warning_template_title}</label>:</strong>
            </dt>
            <dd>
              <input type="text" id="template_title" name="template_title" value="{$C.template_data.title}" size="30" class="input_text" />
            </dd>
            <dt>
              <strong><label for="template_body">{$T.profile_warning_notify_body}</label>:</strong><br />
              <span class="smalltext">{$T.mc_warning_template_body_desc}</span>
            </dt>
            <dd>
              <textarea id="template_body" name="template_body" rows="10" cols="45" class="smalltext">{$C.template_data.body}</textarea>
            </dd>
          </dl>
          {if $C.template_data.can_edit_personal}
            <input type="checkbox" name="make_personal" id="make_personal" {($C.template_data.personal) ? 'checked="checked"' : ''} class="input_check" />
              <label for="make_personal">
                <strong>{$T.mc_warning_template_personal}</strong>
              </label>
              <br>
              <span class="smalltext">{$T.mc_warning_template_personal_desc}</span>
              <br>
          {/if}
          <div class="floatright mediumpadding">
            <input type="submit" name="save" value="{$C.page_title}" class="default" />
          </div>
          <div class="clear"></div>
        </div>
      </div>
      <input type="hidden" name="{$C.session_var}" value="{$C.session_id}" />
    </form>
  </div>
  <br class="clear">