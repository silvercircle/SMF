{$PROFILECONTEXT->loadWarningVariables()}
<script type="text/javascript"><!-- // --><![CDATA[
  function setWarningBarPos(curEvent, isMove, changeAmount)
  {
    barWidth = {$C.warningBarWidth};
    // Are we passing the amount to change it by?
    if (changeAmount)
    {
      if (document.getElementById('warning_level').value == 'SAME')
        percent = {$C.member.warning} + changeAmount;
      else
        percent = parseInt(document.getElementById('warning_level').value) + changeAmount;
    }
    // If not then it\'s a mouse thing.
    else
    {
      if (!curEvent)
        var curEvent = window.event;

      // If it\'s a movement check the button state first!
      if (isMove)
      {
        if (!curEvent.button || curEvent.button != 1)
          return false
      }

      // Get the position of the container.
      contain = document.getElementById('warning_contain');
      position = 0;
      while (contain != null)
      {
        position += contain.offsetLeft;
        contain = contain.offsetParent;
      }

      // Where is the mouse?
      if (curEvent.pageX)
      {
        mouse = curEvent.pageX;
      }
      else
      {
        mouse = curEvent.clientX;
        mouse += document.documentElement.scrollLeft != "undefined" ? document.documentElement.scrollLeft : document.body.scrollLeft;
      }

      // Is this within bounds?
      if (mouse < position || mouse > position + barWidth)
        return;

      percent = Math.round(((mouse - position) / barWidth) * 100);

      // Round percent to the nearest 5 - by kinda cheating!
      percent = Math.round(percent / 5) * 5;
    }

    // What are the limits?
    minLimit = {$C.min_allowed};
    maxLimit = {$C.max_allowed};

    percent = Math.max(percent, minLimit);
    percent = Math.min(percent, maxLimit);

    size = barWidth * (percent/100);

    setInnerHTML(document.getElementById('warning_text'), percent + "%");
    document.getElementById('warning_level').value = percent;
    document.getElementById('warning_progress').style.width = size + "px";

    // Get the right color.
    color = "black";

    {foreach $C.colors as $limit => $color}
      if (percent >= {$limit})
        color = "{$color}";
    {/foreach}
    document.getElementById('warning_progress').style.backgroundColor = color;
    effectText = "";

    {foreach $C.level_effects as $limit => $text}
      if (percent >= {$limit})
        effectText = "{$text}";
    {/foreach}

    setInnerHTML(document.getElementById('cur_level_div'), effectText);
  }

    // Disable notification boxes as required.
  function modifyWarnNotify()
  {
    disable = !document.getElementById('warn_notify').checked;
    document.getElementById('warn_sub').disabled = disable;
    document.getElementById('warn_body').disabled = disable;
    document.getElementById('warn_temp').disabled = disable;
    document.getElementById('new_template_link').style.display = disable ? 'none' : '';
  }

  function changeWarnLevel(amount)
  {
    setWarningBarPos(false, false, amount);
  }

  // Warn template.
  function populateNotifyTemplate()
  {
    index = document.getElementById('warn_temp').value;
    if (index == -1)
      return false;

    // Otherwise see what we can do...';

    {foreach $C.notification_templates as $k => $type}
      if (index == {$k})
      document.getElementById('warn_body').value = "{$type.body|strtr:$C.replace_helper_array}";
    {/foreach}
  }
  // ]]>
</script>
<form action="{$SUPPORT->url_parse('?action=profile;u='|cat:$C.id_member|cat:';area=issuewarning')}" method="post" class="flow_hidden" accept-charset="UTF-8">
  <div class="cat_bar">
    <h3>
      <span class="floatleft">
        {($C.user.is_owner) ? $T.profile_warning_level : $T.profile_issue_warning}
      </span>
    </h3>
  </div>
  {if !$C.user.is_owner}
    <div class="orange_container cleantop mediumpadding smalltext">{$T.profile_warning_desc}</div>
    <br>
    <div class="blue_container">
  {else}
    <div class="blue_container cleantop">
  {/if}
  <div class="content">
    <dl class="settings">
    {if !$C.user.is_owner}
      <dt>
        <strong>{$T.profile_warning_name}:</strong>
      </dt>
      <dd>
        <strong>{$C.member.name}</strong>
      </dd>
    {/if}
    <dt>
      <strong>{$T.profile_warning_level}:</strong>
      {if $C.warning_limit}
        <br>
        <span class="smalltext">{$T.profile_warning_limit_attribute|sprintf:$C.warning_limit}</span>
      {/if}
    </dt>
    <dd>
      <div id="warndiv1" style="display: none;">
        <div>
          <span class="floatleft" style="padding: 0 0.5em"><a href="#" onclick="changeWarnLevel(-5); return false;">[-]</a></span>
          <div class="floatleft" id="warning_contain" style="font-size: 8pt; height: 12pt; width: {$C.warningBarWidth}px; border: 1px solid black; background-color: white; padding: 1px; position: relative;" onmousedown="setWarningBarPos(event, true);" onmousemove="setWarningBarPos(event, true);" onclick="setWarningBarPos(event);">
            <div id="warning_text" style="padding-top: 1pt; width: 100%; z-index: 2; color: black; position: absolute; text-align: center; font-weight: bold;">{$C.member.warning}%</div>
            <div id="warning_progress" style="width: {$C.member.warning}%; height: 12pt; z-index: 1; background-color: {$C.current_color};">&nbsp;</div>
          </div>
          <span class="floatleft" style="padding: 0 0.5em"><a href="#" onclick="changeWarnLevel(5); return false;">[+]</a></span>
          {$level = $C.current_level}
          <div class="clear_left smalltext">{$T.profile_warning_impact}: <span id="cur_level_div">{$C.level_effects.$level}</span></div>
        </div>
        <input type="hidden" name="warning_level" id="warning_level" value="SAME" />
      </div>
        <div id="warndiv2">
        <input type="text" name="warning_level_nojs" size="6" maxlength="4" value="{$C.member.warning}" class="input_text" />&nbsp;{$T.profile_warning_max}
        <div class="smalltext">{$T.profile_warning_impact}:<br>
          {foreach $C.level_effects as $limit => $effect}
            {$T.profile_warning_effect_text|cat:$limit:$effect}<br>
          {/foreach}
        </div>
        </div>
    </dd>
    {if !$C.user.is_owner}
      <dt>
        <strong>{$T.profile_warning_reason}:</strong><br>
        <span class="smalltext">{$T.profile_warning_reason_desc}</span>
      </dt>
      <dd>
        <input type="text" name="warn_reason" id="warn_reason" value="{$C.warning_data.reason}" size="50" style="width: 80%;" class="input_text" />
        {if isset($C.warning_data.msg) and !empty($C.warning_data.msg)}
          <input type="hidden" name="warn_msg" id="warn_msg" value="{$C.warning_data.msg}" size="10" style="width: 80%;" class="input_text" />
        {/if}
      </dd>
      </dl>
      <hr>
      <dl class="settings">
        {if $C.can_issue_topicban or !empty($C.warning_data.topicban_id_topic)}
          <dt>
            <strong>{$T.profile_warning_issue_topicban}:</strong>
          </dt>
          <dd>
            <input type="checkbox" name="warn_topicban" id="warn_topicban" {($C.warning_data.topicban) ? 'checked="checked"' : ''} class="input_check" />
            <input type="hidden" name="warn_topicban_id_topic" value="{$C.warning_data.topicban_id_topic}" />
          </dd>
          <dt>
            <strong>{$T.profile_warning_topicban_expire}:</strong>
            <br>
            <span class="tinytext">{$T.profile_warning_topicban_expire_desc}</span>
          </dt>
          <dd>
            <input type="text" name="warn_topicban_expire" id="warn_topicban_expire" value="{$C.warning_data.topicban_expire}" class="input_text" size="5" />
          </dd>       
        {/if}
        {if isset($C.member_is_topic_banned)}
          <dt>
            <strong><span class="alert">{$T.profile_warning_is_topic_banned}</span></strong>
          </dt>
          <dd>
          </dd>
        {/if}
        <dt>
          <strong>{$T.profile_warning_notify}:</strong>
        </dt>
        <dd>
          <input type="checkbox" name="warn_notify" id="warn_notify" onclick="modifyWarnNotify();" {($C.warning_data.notify) ? 'checked="checked"' : ''} class="input_check" />
        </dd>
        <dt>
          <strong>{$T.profile_warning_notify_subject}:</strong>
        </dt>
        <dd>
          <input type="text" name="warn_sub" id="warn_sub" value="{(empty($C.warning_data.notify_subject)) ? $T.profile_warning_notify_template_subject : $C.warning_data.notify_subject}" size="50" style="width: 80%;" class="input_text" />
        </dd>
        <dt>
          <strong>{$T.profile_warning_notify_body}:</strong>
        </dt>
        <dd>
          <select name="warn_temp" id="warn_temp" disabled="disabled" onchange="populateNotifyTemplate();" style="font-size: x-small;">
          <option value="-1">{$T.profile_warning_notify_template}</option>
          <option value="-1">------------------------------</option>
          {foreach $C.notification_templates as $id_template => $template}
            <option value="{$id_template}">{$template.title}</option>
          {/foreach}
          </select>
          <span class="smalltext" id="new_template_link" style="display: none;"><ul class="buttonlist floatright smallpadding"><li><a href="{$SUPPORT->url_parse('?action=moderate;area=warnings;sa=templateedit;tid=0')}" target="_blank" class="new_win">{$T.profile_warning_new_template}</a></li></ul></span><br>
          <br>
          <textarea name="warn_body" id="warn_body" cols="40" rows="8">{$C.warning_data.notify_body}</textarea>
        </dd>
    {/if}
    </dl>
    <div class="righttext">
      {$C.hidden_sid_input}
      <input type="submit" name="save" value="{($C.user.is_owner) ? $T.change_profile : $T.profile_warning_issue}" class="default" />
    </div>
  </div>
</div>
</form>
<br>
<div class="cat_bar">
  <h3>
    {$T.profile_warning_previous}
  </h3>
</div>
<div class="blue_container cleantop">
  <table class="table_grid" style="width:100%;">
    <thead>
      <tr>
        <th class="glass first_th" scope="col" style="width:20%;">{$T.profile_warning_previous_issued}</th>
        <th class="glass" scope="col" style="width:30%;">{$T.profile_warning_previous_time}</th>
        <th class="glass" scope="col">{$T.profile_warning_previous_reason}</th>
        <th class="glass last_th" scope="col" style="width:6%;">{$T.profile_warning_previous_level}</th>
      </tr>
    </thead>
    <tbody>
    {$alternate = 0}
    {foreach $C.previous_warnings as $warning}
      <tr class="tablerow{($alternate) ? ' alternate' : ''}">
        <td class="smalltext">{$warning.issuer.link}</td>
        <td class="smalltext">{$warning.time}</td>
        <td class="smalltext">
          <div class="floatleft">
              {$warning.reason}
          </div>
          {if !empty($warning.id_notice)}
            <div class="floatright">
              <a href="{$SUPPORT->url_parse('?action=moderate;area=notice;nid='|cat:$warning.id_notice)}" onclick="window.open(this.href, '', 'scrollbars=yes,resizable=yes,width=400,height=250');return false;" target="_blank" class="new_win" title="{$T.profile_warning_previous_notice}"><img src="{$S.images_url}/filter.gif" alt="" /></a>
            </div>
          {/if}
        </td>
        <td class="smalltext">{$warning.counter}</td>
      </tr>
      {$alternate = !$alternate}
    {/foreach}
    {if empty($C.previous_warnings)}
      <tr class="tablerow">
        <td class="centertext" colspan="4">
          {$T.profile_warning_previous_none}
        </td>
      </tr>
    {/if}
    </tbody>
  </table>
  <div class="pagesection pagelinks">{$C.page_index}</div>
</div>
<script type="text/javascript"><!-- // --><![CDATA[
  document.getElementById('warndiv1').style.display = "";
  document.getElementById('warndiv2').style.display = "none";

  {if !$C.user.is_owner}
    modifyWarnNotify();
  {/if}
  // ]]>
</script>