<dt>
  <strong{(isset($C.modify_error.bad_offset)) ? ' class="error"' : ''}>{$T.time_offset}:</strong><br>
  <span class="smalltext">{$T.personal_time_offset}</span>
</dt>
<dd>
  <input type="text" name="time_offset" id="time_offset" size="5" maxlength="5" value="{$C.member.time_offset}" class="input_text" /> <a href="javascript:void(0);" onclick="currentDate = new Date({$C.current_forum_time_js}); document.getElementById('time_offset').value = autoDetectTimeOffset(currentDate); return false;">{$T.timeoffset_autodetect}</a><br>{$T.current_time}: <em>{$C.current_forum_time}</em>
</dd>
