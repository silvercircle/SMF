<dt>
  <strong>{$T.time_format}:</strong><br>
    <a href="{$SCRIPTURL}?action=helpadmin;help=time_format" onclick="return reqWin(this.href);" class="help"><strong>[{$T.help}]&nbsp;&nbsp;</strong></a>
    <span class="smalltext">&nbsp;{$T.date_format}</span>
</dt>
<dd>
  <select name="easyformat" onchange="document.forms.creator.time_format.value = this.options[this.selectedIndex].value;" style="margin-bottom: 4px;">
  {foreach $C.easy_timeformats as $time_format}
    <option value="{$time_format.format}"{($time_format.format == $C.member.time_format) ? ' selected="selected"' : ''}>{$time_format.title}</option>
  {/foreach}
  </select><br>
  <input type="text" name="time_format" value="{$C.member.time_format}" size="30" class="input_text" />
</dd>
<dt>
  <strong>{$T.disable_dynatime}</strong><br>
</dt>
<dd>
  <input type="hidden" name="default_options[disable_dynatime]" value="0" />
  <label for="disable_dynatime"><input type="checkbox" name="default_options[disable_dynatime]" id="disable_dynatime" value="1"{(!empty($C.member.options.disable_dynatime)) ? ' checked="checked"' : ''} class="input_check" /></label>
</dd>
