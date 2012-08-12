<dt>
  <strong>{$T.dob}:</strong><br>
  <span class="smalltext">{$T.dob_year} - {$T.dob_month} - {$T.dob_day}</span>
</dt>
<dd>
  <input type="text" name="bday3" size="4" maxlength="4" value="{$C.member.birth_date.year}" class="input_text" /> -
  <input type="text" name="bday1" size="2" maxlength="2" value="{$C.member.birth_date.month}" class="input_text" /> -
  <input type="text" name="bday2" size="2" maxlength="2" value="{$C.member.birth_date.day}" class="input_text" />
</dd>
