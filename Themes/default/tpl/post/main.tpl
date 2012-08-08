{extends "base.tpl"}
{block content}
{include "generics.tpl"}
{include "generics/editor_control.tpl"}
<script type="text/javascript"><!-- // --><![CDATA[
  {if $C.browser.is_firefox}
      function reActivate()
      {
        document.forms.postmodify.message.readOnly = false;
      }
      window.addEventListener("pageshow", reActivate, false);
  {/if}
  var icon_urls = {
  {foreach $C.icons as $icon}
        "{$icon.value}":  "{$icon.url}" {($icon.is_last) ? '' : ','}
  {/foreach}
  };
  function showimage()
  {
    document.images.icons.src = icon_urls[document.forms.postmodify.icon.options[document.forms.postmodify.icon.selectedIndex].value];
  }
  {if $C.make_poll}
  function pollOptions()
  {
    var expire_time = document.getElementById('poll_expire');

    if (isEmptyText(expire_time) || expire_time.value == 0)
    {
      document.forms.postmodify.poll_hide[2].disabled = true;
      if (document.forms.postmodify.poll_hide[2].checked)
        document.forms.postmodify.poll_hide[1].checked = true;
    }
    else
      document.forms.postmodify.poll_hide[2].disabled = false;
  }

  var pollOptionNum = 0, pollTabIndex;
  function addPollOption()
  {
    if (pollOptionNum == 0)
    {
      for (var i = 0, n = document.forms.postmodify.elements.length; i < n; i++)
        if (document.forms.postmodify.elements[i].id.substr(0, 8) == 'options-')
        {
          pollOptionNum++;
          pollTabIndex = document.forms.postmodify.elements[i].tabIndex;
        }
    }
    pollOptionNum++
    setOuterHTML(document.getElementById('pollMoreOptions'), {$SUPPORT->JavaScriptEscape('<li><label for="options-')} + pollOptionNum + {$SUPPORT->JavaScriptEscape('">'|cat:$T.option|cat:' ')} + pollOptionNum + {$SUPPORT->JavaScriptEscape('</label>: <input type="text" name="options[')} + pollOptionNum + {$SUPPORT->JavaScriptEscape(']" id="options-')} + pollOptionNum + {$SUPPORT->JavaScriptEscape('" value="" size="80" maxlength="255" tabindex="')} + pollTabIndex + {$SUPPORT->JavaScriptEscape('" class="input_text" /></li><li id="pollMoreOptions"></li>')});
  }
  {/if}
  // If we are making a calendar event we want to ensure we show the current days in a month etc... this is done here.
  {if $C.make_event}
      var monthLength = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

      function generateDays()
      {
        var dayElement = document.getElementById('day'), yearElement = document.getElementById('year'), monthElement = document.getElementById('month');
        var days, selected = dayElement.selectedIndex;

        monthLength[1] = yearElement.options[yearElement.selectedIndex].value % 4 == 0 ? 29 : 28;
        days = monthLength[monthElement.value - 1];

        while (dayElement.options.length)
          dayElement.options[0] = null;

        for (i = 1; i <= days; i++)
          dayElement.options[dayElement.length] = new Option(i, i);

        if (selected < days)
          dayElement.selectedIndex = selected;
      }
  {/if}
  // ]]>
</script>
<form action="{$SCRIPTURL}?action={$C.destination};{(empty($C.current_board)) ? '' : ('board='|cat:$C.current_board)}" method="post" accept-charset="UTF-8" name="postmodify" id="postmodify" class="flow_hidden" onsubmit="{($C.becomes_approved) ? '' : 'alert(\''|cat:$T.js_post_will_require_approval|cat:'\');'} submitonce(this);smc_saveEntities('postmodify', ['subject', '{$C.post_box_name}', 'guestname', 'evtitle', 'question'], 'options');" enctype="multipart/form-data">
<div id="preview_section"{(isset($C.preview_message)) ? '' : ' style="display: none;"'}>
  <div class="cat_bar">
    <h3>
      <span id="preview_subject">{(empty($C.preview_subject)) ? '' : $C.preview_subject} ({$T.preview})</span>
    </h3>
  </div>
  <div class="post_wrapper blue_container cleantop">
    <div id="preview_body" class="post fontstyle_{$U.font_class}">
      {(empty($C.preview_message)) ? '' : $C.preview_message}
    </div>
  </div>
</div>
<br>
{if $C.make_event and (!$C.event.new or !empty($C.current_board))}
  <input type="hidden" name="eventid" value="{$C.event.id}" />
{/if}
<h1 class="bigheader">{$C.page_title}</h1>
<div class="generic_container">
  <br>
  <div class="blue_container" style="margin:0 auto;padding:10px;max-width:900px;">{(isset($C.current_topic)) ? ('<input type="hidden" name="topic" value="'|cat:$C.current_topic|cat:'" />') : ''}
    <div class="errorbox"{(empty($C.post_error.messages)) ? ' style="display: none"' : ''} id="errors">
      <dl>
        <dt>
          <strong style="{(empty($C.error_type) or $C.error_type != 'serious') ? 'display: none;' : ''}" id="error_serious">{$T.error_while_submitting}</strong>
        </dt>
        <dt class="error" id="error_list">
          {(empty($C.post_error.messages)) ? '' : ('<br>'|implode:$C.post_error.messages)}
        </dt>
      </dl>
    </div>
    {if !$C.becomes_approved}
      <p class="information">
        <em>{$T.wait_for_approval}</em>
        <input type="hidden" name="not_approved" value="1" />
      </p>
    {/if}
    <p class="information"{($C.draft_locked) ? '' : ' style="display: none"'} id="lock_warning">
      {$T.topic_locked_no_reply}
    </p>
    <dl id="post_header">
    {if isset($C.name) and isset($C.email)}
      <dt>
        <span {(isset($C.post_error.long_name) or isset($C.post_error.no_name) or isset($C.post_error.bad_name)) ? ' class="error"' : ''} id="caption_guestname">{$T.name}:</span>
      </dt>
      <dd>
        <input type="text" name="guestname" size="25" value="{$C.name}" tabindex="{$C.tabindex}" class="input_text" />
        {$C.tabindex = $C.tabindex+1}
      </dd>
      {if empty($M.guest_post_no_email)}
        <dt>
          <span {(isset($C.post_error.no_email) or isset($C.post_error.bad_email)) ? ' class="error"' : ''} id="caption_email">{$T.email}:</span>
        </dt>
        <dd>
          <input type="text" name="email" size="25" value="{$C.email}" tabindex="{$C.tabindex}" class="input_text" />
          {$C.tabindex = $C.tabindex+1}
        </dd>
      {/if}
    {/if}
    <dt>
      <span {(isset($C.post_error.no_subject)) ? ' class="error"' : ''} id="caption_subject">{$T.subject}:</span>
    </dt>
    <dd>
      {(isset($C.prefix_selector)) ? $C.prefix_selector : ''}<input type="text" name="subject" {($C.subject == '') ? '' : (' value="'|cat:$C.subject|cat:'"')} tabindex="{$C.tabindex}" size="80" maxlength="80" class="input_text" />
      {$C.tabindex = $C.tabindex+1}
    </dd>
    {if !(isset($C.previous_posts) and count($C.previous_posts) > 0)}
      {if isset($C.tagging_ui)}
        {$C.tagging_ui}
      {/if}
    {/if}
    <dt class="clear_left">
      {$T.message_icon}
    </dt>
    <dd>
      <select name="icon" id="icon" onchange="showimage()">
      {foreach $C.icons as $icon}
        <option value="{$icon.value}" {($icon.value == $C.icon) ? ' selected="selected"' : ''}>{$icon.name}</option>
      {/foreach}
      </select>
      <img src="{$C.icon_url}" name="icons" hspace="15" alt="" />
    </dd>
    </dl>
    <hr class="clear">
    {if $C.make_event}
      <div id="post_event">
        <fieldset id="event_main">
        <legend><span {(isset($C.post_error.no_event)) ? ' class="error"' : ''} id="caption_evtitle">{$T.calendar_event_title}</span></legend>
        <input type="text" name="evtitle" maxlength="255" size="60" value="{$C.event.title}" tabindex="{$C.tabindex}" class="input_text" />
        {$C.tabindex = $C.tabindex+1}
        <div class="smalltext">
          <input type="hidden" name="calendar" value="1" />{$T.calendar_year}
          <select name="year" id="year" tabindex="{$C.tabindex}" onchange="generateDays();">
            {$C.tabindex = $C.tabindex+1}
            {section name=years start=$M.cal_minyear loop=$M.cal_maxyear+1 step=1}
              <option value="{$smarty.section.years.index}" {($smarty.section.years.index == $C.event.year) ? ' selected="selected"' : ''}>{$smarty.section.years.index}&nbsp;</option>
            {/section}
          </select>
          {$T.calendar_month}
          <select name="month" id="month" onchange="generateDays();">
            {section name=months start=1 loop=13 step=1}
              {$index = $smarty.section.months.index}
              <option value="{$smarty.section.months.index}" {($smarty.section.months.index == $C.event.month) ? ' selected="selected"' : ''}>{$T.months.$index}&nbsp;</option>
            {/section}
          </select>
          {$T.calendar_day}
          <select name="day" id="day">
            {section name=days start=1 loop=$C.event.last_day+1 step=1}
              <option value="{$smarty.section.days.index}" {($smarty.section.days.index == $C.event.day) ? ' selected="selected"' : ''}>{$smarty.section.days.index}&nbsp;</option>
            {/section}
          </select>
        </div>
        </fieldset>
        {if !empty($M.cal_allowspan) or ($C.event.new and $C.is_new_post)}
          <fieldset id="event_options">
            <legend>{$T.calendar_event_options}</legend>
            <div class="event_options smalltext">
              <ul class="event_options">
                {if !empty($M.cal_allowspan)}
                  <li>
                    {$T.calendar_numb_days}
                      <select name="span">
                        {section name=days start=1 loop=$M.cal_maxspan+1 step=1}
                          <option value="{$smarty.section.days.index}" {($smarty.section.days.index == $C.event.span) ? ' selected="selected"' : ''}>{$smarty.section.days.index}&nbsp;</option>
                        {/section}
                      </select>
                  </li>
                {/if}
                {if $C.event.new and $C.is_new_post}
                  <li>
                    {$T.calendar_post_in}
                    <select name="board">
                    {foreach $C.event.categories as $category}
                      <optgroup label="{$category.name}">
                        {foreach $category.boards as $board}
                          <option value="{$board.id}" {($board.selected) ? ' selected="selected"' : ''}>{($board.child_level > 0) ? (('=='|str_repeat:($board.child_level - 1))|cat:'=&gt;') : ''} {$board.name}&nbsp;</option>
                        {/foreach}
                      </optgroup>
                    {/foreach}
                    </select>
                  </li>
                {/if}
              </ul>
            </div>
          </fieldset>
        {/if}
       </div>
    {/if}
    {if $C.make_poll}
      <div id="edit_poll">
        <fieldset id="poll_main">
          <legend><span {(isset($C.poll_error.no_question)) ? ' class="error"' : ''}>{$T.poll_question}</span></legend>
          <input type="text" name="question" value="{(isset($C.question)) ? $C.question : ''}" tabindex="{$C.tabindex}" size="80" class="input_text" />
            {$C.tabindex = $C.tabindex+1}
            <ul class="poll_main">
            {foreach $C.choices as $choice}
              <li>
                <label for="options-{$choice.id}">{$T.option} {$choice.number}</label>:
                <input type="text" name="options[{$choice.id}]" id="options-{$choice.id}" value="{$choice.label}" tabindex="{$C.tabindex}" size="80" maxlength="255" class="input_text" />
                {$C.tabindex = $C.tabindex+1}
              </li>
            {/foreach}
            <li id="pollMoreOptions"></li>
            </ul>
            <strong><a href="javascript:addPollOption(); void(0);">({$T.poll_add_option})</a></strong>
        </fieldset>
        <fieldset id="poll_options">
        <legend>{$T.poll_options}</legend>
        <dl class="settings poll_options">
          <dt>
            <label for="poll_max_votes">{$T.poll_max_votes}:</label>
          </dt>
          <dd>
            <input type="text" name="poll_max_votes" id="poll_max_votes" size="2" value="{$C.poll_options.max_votes}" class="input_text" />
          </dd>
          <dt>
            <label for="poll_expire">{$T.poll_run}:</label><br>
            <em class="smalltext">{$T.poll_run_limit}</em>
          </dt>
          <dd>
            <input type="text" name="poll_expire" id="poll_expire" size="2" value="{$C.poll_options.expire}" onchange="pollOptions();" maxlength="4" class="input_text" />{$T.days_word}
          </dd>
          <dt>
            <label for="poll_change_vote">{$T.poll_do_change_vote}:</label>
          </dt>
          <dd>
            <input type="checkbox" id="poll_change_vote" name="poll_change_vote" {(!empty($C.poll.change_vote)) ? ' checked="checked"' : ''} class="input_check" />
          </dd>
          {if $C.poll_options.guest_vote_enabled}
            <dt>
              <label for="poll_guest_vote">{$T.poll_guest_vote}:</label>
            </dt>
            <dd>
              <input type="checkbox" id="poll_guest_vote" name="poll_guest_vote" {(!empty($C.poll_options.guest_vote)) ? ' checked="checked"' : ''} class="input_check" />
            </dd>
          {/if}
          <dt>
            {$T.poll_results_visibility}:
          </dt>
          <dd>
            <input type="radio" name="poll_hide" id="poll_results_anyone" value="0" {($C.poll_options.hide == 0) ? ' checked="checked"' : ''} class="input_radio" /> <label for="poll_results_anyone">{$T.poll_results_anyone}</label><br>
            <input type="radio" name="poll_hide" id="poll_results_voted" value="1" {($C.poll_options.hide == 1) ? ' checked="checked"' : ''} class="input_radio" /> <label for="poll_results_voted">{$T.poll_results_voted}</label><br>
            <input type="radio" name="poll_hide" id="poll_results_expire" value="2" {($C.poll_options.hide == 2) ? ' checked="checked"' : ''} {(empty($C.poll_options.expire)) ? 'disabled="disabled"' : ''} class="input_radio" /> <label for="poll_results_expire">{$T.poll_results_after}</label>
          </dd>
        </dl>
        </fieldset>
      </div>
    {/if}
    <div id="editor_main_content">
      {if $C.show_bbc}
        <div id="bbcBox_message">
        </div>
      {/if}
      {call control_richedit editor_id=$C.post_box_name smileyContainer='smileyBox_message' bbcContainer='bbcBox_message'}
    </div>
    {if isset($C.last_modified)}
      <div class="padding smalltext">
        <strong>{$T.last_edit}:</strong>
        {$C.last_modified}
      </div>
    {/if}
    <br>
    {call collapser id='editor_options' title=$T.post_additionalopt}
    <div id="postMoreOptions" class="smalltext">
      <ul class="post_options">
        {if $C.can_notify}
          <li><input type="hidden" name="notify" value="0" /><label class="aligned" for="check_notify"><input type="checkbox" name="notify" id="check_notify" {($C.notify or !empty($O.auto_notify)) ? ' checked="checked"' : ''} value="1" class="input_check aligned" /> {$T.notify_replies}</label></li>
        {/if}
        {if $C.can_lock}
          <li><input type="hidden" name="lock" value="0" /><label for="check_lock"><input type="checkbox" name="lock" id="check_lock" {($C.locked) ? ' checked="checked"' : ''} value="1" class="input_check" /> {$T.lock_topic}</label></li>
        {/if}
        {if isset($C.can_lock_message) and !empty($C.can_lock_message)}
          <li><label for="lock_message"><input type="checkbox" name="lock_message" class="input_check" value="1" {($C.message_locked) ? ' checked="checked"' : ''} /> {$T.lock_message}</label></li>
        {/if}
        <li><label for="check_back"><input type="checkbox" name="goback" id="check_back" {($C.back_to_topic or !empty($O.return_to_post)) ? ' checked="checked"' : ''} value="1" class="input_check" /> {$T.back_to_topic}</label></li>
        {if $C.can_sticky}
          <li><input type="hidden" name="sticky" value="0" /><label for="check_sticky"><input type="checkbox" name="sticky" id="check_sticky" {($C.sticky) ? ' checked="checked"' : ''} value="1" class="input_check" /> {$T.sticky_after}</label></li>
        {/if}
        <li><label for="check_smileys"><input type="checkbox" name="ns" id="check_smileys" {($C.use_smileys) ? '' : ' checked="checked"'} value="NS" class="input_check" /> {$T.dont_use_smileys}</label></li>
        {if $C.can_move}
          <li><input type="hidden" name="move" value="0" /><label for="check_move"><input type="checkbox" name="move" id="check_move" value="1" class="input_check" {(!empty($C.move)) ? ' checked="checked" ' : ''} /> {$T.move_after2}</label></li>
        {/if}
        {if $C.can_announce and $C.is_first_post}
          <li><label for="check_announce"><input type="checkbox" name="announce_topic" id="check_announce" value="1" class="input_check" {(!empty($C.announce)) ? 'checked="checked" ' : ''} /> {$T.announce_topic}</label></li>
        {/if}
        {if $C.show_approval}
          <li><label for="approve"><input type="checkbox" name="approve" id="approve" value="2" class="input_check" {($C.show_approval === 2) ? 'checked="checked"' : ''} /> {$T.approve_this_post}</label></li>
        {/if}
        {if isset($C.can_stick_firstpost) and !empty($C.can_stick_firstpost)}
          <li><label for="stickfirst"><input type="checkbox" name="stickfirst" id="stickfirst" value="1" class="input_check" {(isset($C.first_is_sticky) and !empty($C.first_is_sticky)) ? 'checked="checked"' : ''} /> {$T.first_post_sticky}</label></li>
        {/if}
        {if $C.can_merge_with_last}
          <li><label for="want_automerge"><input type="checkbox" name="want_automerge" id="want_automerge" class="input_check" checked="checked" value="1" class="input_check" /> {$T.want_automerge}</label></li>
        {/if}
        {if $M.astream_active}
          <li><label for="noactivity"><input type="checkbox" name="noactivity" id="noactivity" value="1" class="input_check" />{$T.no_activity_record}</label></li>
        {/if}
        {if $C.can_tag_users}
          <li><label for="allowtags"><input type="checkbox" name="allowtags" id="allowtags" value="1" class="input_check" />{$T.disable_user_tagging}</label></li>
        {/if}
      </ul>
      {if isset($C.can_stick_firstpost) and $C.can_stick_firstpost and isset($C.first_has_layout)}
        <hr>
        <select name="firstlayout">
          <option value="0" {($C.first_has_layout == 0) ? ' selected="selected"' : ''}>Standard layout</option>
          <option value="1" {($C.first_has_layout == 1) ? ' selected="selected"' : ''}>Standard layout, first post with blog-style</option>
          <option value="2" {($C.first_has_layout == 2) ? ' selected="selected"' : ''}>Blog-like display with simplified comments</option>
        </select>
      {/if}
    </div>
    {if !empty($C.current_attachments)}
      <dl id="postAttachment">
        <dt>
          {$T.attached}:
        </dt>
        <dd class="smalltext">
          <input type="hidden" name="attach_del[]" value="0" />
            {$T.uncheck_unwatchd_attach}:
        </dd>
        {foreach $C.current_attachments as $attachment}
          <dd class="smalltext">
            <label for="attachment_{$attachment.id}"><input type="checkbox" id="attachment_{$attachment.id}" name="attach_del[]" value="{$attachment.id}" {(empty($attachment.unchecked)) ? ' checked="checked"' : ''} class="input_check" />{$attachment.name}{(empty($attachment.approved)) ? (' ('|cat:$T.awaiting_approval|cat:')') : ''}</label>
          </dd>
        {/foreach}
      </dl>
    {/if}
    {if $C.can_post_attachment}
      <dl id="postAttachment2">
        <dt>
          {$T.attach}:
        </dt>
        <dd class="smalltext">
          <input type="file" size="60" name="attachment[]" id="attachment1" class="input_file" /> (<a href="javascript:void(0);" onclick="cleanFileInput('attachment1');">{$T.clean_attach}</a>)
          {if $C.num_allowed_attachments > 1}
            <script type="text/javascript"><!-- // --><![CDATA[
            var allowed_attachments = {$C.num_allowed_attachments};
            var current_attachment = 1;
            function addAttachment()
            {
              allowed_attachments = allowed_attachments - 1;
              current_attachment = current_attachment + 1;
              if (allowed_attachments <= 0)
                return alert("{$T.more_attachments_error}");

              setOuterHTML(document.getElementById('moreAttachments'), '<dd class="smalltext"><input type="file" size="60" name="attachment[]" id="attachment' + current_attachment + '" class="input_file" /> (<a href="javascript:void(0);" onclick="cleanFileInput(\'attachment' + current_attachment + '\');">{$T.clean_attach}</a>)' + '</dd><dd class="smalltext" id="moreAttachments"><a href="#" onclick="addAttachment(); return false;">({$T.more_attachments})<' + '/a><' + '/dd>');
              return true;
            }
              // ]]></script>
          </dd>
          <dd class="smalltext" id="moreAttachments"><a href="#" onclick="addAttachment(); return false;">({$T.more_attachments}</a></dd>
          {/if}
        <dd class="smalltext">
        {if !empty($M.attachmentCheckExtensions)}
          {$T.allowed_types}: {$C.allowed_extensions}<br>
        {/if}
        {if !empty($C.attachment_restrictions)}
          {$T.attach_restrictions} {', '|implode:$C.attachment_restrictions}<br>
        {/if}
        {if !$C.can_post_attachment_unapproved}
          <span class="alert">{$T.attachment_requires_approval}</span><br>
        {/if}
        </dd>
      </dl>
    {/if}
    {if $C.require_verification}
      <div class="post_verification">
        <span {(!empty($C.post_error.need_qr_verification)) ? ' class="error"' : ''}>
          <strong>{$T.verification}:</strong>
        </span>
        {$SUPPORT->template_control_verification($C.visual_verification_id, 'all')}
      </div>
    {/if}
    <div class="clear"></div>
  </div>
  <p class="smalltext" id="shortcuts">
    {($C.browser.is_firefox) ? $T.shortcuts_firefox : $T.shortcuts}
  </p>
  <p id="post_confirm_buttons" class="righttext">
    {call control_richedit_buttons editor_id=$C.post_box_name}
    {if $C.make_event and !$C.event.new}
      <input type="submit" name="deleteevent" value="{$T.event_delete}" onclick="return confirm('{$T.event_delete_confirm}');" class="button_submit" />
    {/if}
  </p>
  </div>
  <br>
</div>
<br class="clear">
{if isset($C.topic_last_message)}
  <input type="hidden" name="last_msg" value="{$C.topic_last_message}" />
{/if}
<input type="hidden" name="additional_options" id="additional_options" value="{($C.show_additional_options) ? '1' : '0'}" />
{$C.hidden_sid_input}
<input type="hidden" name="seqnum" value="{$C.form_sequence_number}" />
</form>
<script type="text/javascript"><!-- // --><![CDATA[';
  var current_board = {(empty($C.current_board)) ? 'null' : $C.current_board};
  var make_poll = {($C.make_poll) ? 'true' : 'false'};
  var txt_preview_title = "{$T.preview_title}";
  var txt_preview_fetch = "{$T.preview_fetch}";
  var new_replies = new Array();
  var reply_counter = {(empty($counter)) ? 0 : $counter};
  
  function previewPost()
  {
    if (window.XMLHttpRequest)
    {
      // !!! Currently not sending poll options and option checkboxes.
      var x = new Array();
      var textFields = ['subject', {$SUPPORT->JavaScriptEscape($C.post_box_name)}, 'icon', 'guestname', 'email', 'evtitle', 'question', 'topic'];
      var numericFields = [
          'board', 'topic', 'last_msg',
          'eventid', 'calendar', 'year', 'month', 'day',
          'poll_max_votes', 'poll_expire', 'poll_change_vote', 'poll_hide'
      ];
      var checkboxFields = [
        'ns', 'allowtags'
      ];

      for (var i = 0, n = textFields.length; i < n; i++)
        if (textFields[i] in document.forms.postmodify)
        {
          // Handle the WYSIWYG editor.
          if (textFields[i] == {$SUPPORT->JavaScriptEscape($C.post_box_name)} && {$SUPPORT>JavaScriptEscape('oEditorHandle_'|cat:$C.post_box_name)} in window && oEditorHandle_{$C.post_box_name}.bRichTextEnabled)
            x[x.length] = 'message_mode=1&' + textFields[i] + '=' + oEditorHandle_{$C.post_box_name}.getText(false).replace(/&#/g, '&#38;#').php_to8bit().php_urlencode();
          else
            x[x.length] = textFields[i] + '=' + document.forms.postmodify[textFields[i]].value.replace(/&#/g, '&#38;#').php_to8bit().php_urlencode();
        }
      for (var i = 0, n = numericFields.length; i < n; i++)
        if (numericFields[i] in document.forms.postmodify && 'value' in document.forms.postmodify[numericFields[i]])
          x[x.length] = numericFields[i] + '=' + parseInt(document.forms.postmodify.elements[numericFields[i]].value);

      for (var i = 0, n = checkboxFields.length; i < n; i++)
            if (checkboxFields[i] in document.forms.postmodify && document.forms.postmodify.elements[checkboxFields[i]].checked)
              x[x.length] = checkboxFields[i] + '=' + document.forms.postmodify.elements[checkboxFields[i]].value;

      sendXMLDocument(smf_prepareScriptUrl(smf_scripturl) + 'action=post2' + (current_board ? ';board=' + current_board : '') + (make_poll ? ';poll' : '') + ';preview;xml', x.join('&'), onDocSent);

      document.getElementById('preview_section').style.display = '';
      setInnerHTML(document.getElementById('preview_subject'), txt_preview_title);
      setInnerHTML(document.getElementById('preview_body'), txt_preview_fetch);
      return false;
    }
    else
      return submitThisOnce(document.forms.postmodify);
  }
  function onDocSent(XMLDoc)
  {
    if (!XMLDoc)
    {
      document.forms.postmodify.preview.onclick = new function ()
      {
        return true;
      }
      document.forms.postmodify.preview.click();
    }
    // Show the preview section.
    var preview = XMLDoc.getElementsByTagName('smf')[0].getElementsByTagName('preview')[0];
    setInnerHTML(document.getElementById('preview_subject'), preview.getElementsByTagName('subject')[0].firstChild.nodeValue);
    
    var bodyText = '';
    for (var i = 0, n = preview.getElementsByTagName('body')[0].childNodes.length; i < n; i++)
      bodyText += preview.getElementsByTagName('body')[0].childNodes[i].nodeValue;

    setInnerHTML(document.getElementById('preview_body'), bodyText);
    bbc_refresh();
    if(typeof(prettyPrint) != 'undefined') {
      prettyPrint();
    }

    // Show a list of errors (if any).
    var errors = XMLDoc.getElementsByTagName('smf')[0].getElementsByTagName('errors')[0];
    var errorList = new Array();
    for (var i = 0, numErrors = errors.getElementsByTagName('error').length; i < numErrors; i++)
      errorList[errorList.length] = errors.getElementsByTagName('error')[i].firstChild.nodeValue;
    
    document.getElementById('errors').style.display = numErrors == 0 ? 'none' : '';
    document.getElementById('error_serious').style.display = errors.getAttribute('serious') == 1 ? '' : 'none';
    setInnerHTML(document.getElementById('error_list'), numErrors == 0 ? '' : errorList.join('<br />'));

    // Show a warning if the topic has been locked.
    document.getElementById('lock_warning').style.display = errors.getAttribute('topic_locked') == 1 ? '' : 'none';

    // Adjust the color of captions if the given data is erroneous.
    var captions = errors.getElementsByTagName('caption');
    for (var i = 0, numCaptions = errors.getElementsByTagName('caption').length; i < numCaptions; i++)
      if (document.getElementById('caption_' + captions[i].getAttribute('name')))
        document.getElementById('caption_' + captions[i].getAttribute('name')).className = captions[i].getAttribute('class');

    if (errors.getElementsByTagName('post_error').length == 1)
      document.forms.postmodify.{$C.post_box_name}.style.border = '1px solid red';
    else if (document.forms.postmodify.{$C.post_box_name}.style.borderColor == 'red' || document.forms.postmodify.{$C.post_box_name}.style.borderColor == 'red red red red')
    {
      if ('runtimeStyle' in document.forms.postmodify.{$C.post_box_name})
        document.forms.postmodify.{$C.post_box_name}.style.borderColor = '';
      else
        document.forms.postmodify.{$C.post_box_name}.style.border = null;
    }
    // Set the new last message id.
    if ('last_msg' in document.forms.postmodify)
      document.forms.postmodify.last_msg.value = XMLDoc.getElementsByTagName('smf')[0].getElementsByTagName('last_msg')[0].firstChild.nodeValue;

    // Remove the new image from old-new replies!
    for (i = 0; i < new_replies.length; i++)
      document.getElementById('image_new_' + new_replies[i]).style.display = 'none';
    
    new_replies = new Array();
    var ignored_replies = new Array(), ignoring;
    var newPosts = XMLDoc.getElementsByTagName('smf')[0].getElementsByTagName('new_posts')[0] ? XMLDoc.getElementsByTagName('smf')[0].getElementsByTagName('new_posts')[0].getElementsByTagName('post') : { length: 0 };
    var numNewPosts = newPosts.length;
    if (numNewPosts != 0)
    {
      var newPostsHTML = '<span id="new_replies"><' + '/span>';
      for (var i = 0; i < numNewPosts; i++)
      {
        new_replies[new_replies.length] = newPosts[i].getAttribute("id");

        ignoring = false;
        if (newPosts[i].getElementsByTagName("is_ignored")[0].firstChild.nodeValue != 0)
          ignored_replies[ignored_replies.length] = ignoring = newPosts[i].getAttribute("id");

        newPostsHTML += '<div class="windowbg' + (++reply_counter % 2 == 0 ? '2' : '') + ' core_posts"><span class="topslice"><span></span></span><div class="content" id="msg' + newPosts[i].getAttribute("id") + '"><div class="floatleft"><h5>{$T.posted_by}: ' + newPosts[i].getElementsByTagName("poster")[0].firstChild.nodeValue + '</h5><span class="smalltext">&#171;&nbsp;<strong>{$T.on}:</strong> ' + newPosts[i].getElementsByTagName("time")[0].firstChild.nodeValue + '&nbsp;&#187;</span> <img src="' + smf_images_url + '/new.png" alt="{$T.preview_new}" id="image_new_' + newPosts[i].getAttribute("id") + '" /></div>';

        {if $C.can_quote}
          newPostsHTML += '<ul class="reset buttonlist" id="msg_' + newPosts[i].getAttribute("id") + '_quote"><li class="quote_button"><a href="#postmodify" onclick="return insertQuoteFast(\'' + newPosts[i].getAttribute("id") + '\');"><span>{$T.bbc_quote}</span><' + '/a></li></ul>';
        {/if}
        newPostsHTML += '<br class="clear">';
        
        if (ignoring)
          newPostsHTML += '<div id="msg_' + newPosts[i].getAttribute("id") + '_ignored_prompt" class="smalltext">{$T.ignoring_user}<a href="#" id="msg_' + newPosts[i].getAttribute("id") + '_ignored_link" style="display: none;">{$T.show_ignore_user_post}</a></div>';

        newPostsHTML += '<div class="list_posts smalltext" id="msg_' + newPosts[i].getAttribute("id") + '_body">' + newPosts[i].getElementsByTagName("message")[0].firstChild.nodeValue + '<' + '/div></div></div>';
       }
      setOuterHTML(document.getElementById('new_replies'), newPostsHTML);
    }

    var numIgnoredReplies = ignored_replies.length;
    if (numIgnoredReplies != 0)
    {
    }

    if (typeof(smf_codeFix) != 'undefined')
      smf_codeFix();
  }
  var want_auto_preview = {$C.auto_preview};
  if(want_auto_preview)
    previewPost();
  // ]]>
</script>
{if isset($C.previous_posts) and count($C.previous_posts) > 0}
  <div id="recent" class="flow_hidden main_section">
    <div class="cat_bar rounded_top">
      <h3>{$T.topic_summary}</h3>
    </div>
    <span id="new_replies"></span>
    {$ignored_posts = array()}
    {foreach $C.previous_posts as $post}
      {$ignoring = false}
      {if !empty($post.is_ignored)}
        {$ignored_posts[] = $post.id}
        {$ignoring = $post.id}
      {/if}
      <div class="post_wrapper {($post.alternate) ? 'alternate' : ''} core_posts">
        <div class="content" id="msg{$post.id}">
          <div class="floatleft">
            <h5>{$T.posted_by}: {$post.poster}</h5>
            <span class="smalltext">&#171;&nbsp;<strong>{$T.on}:</strong>{$post.time}&nbsp;&#187;</span>
          </div>
          {if $C.can_quote}
            <ul class="buttonlist" id="msg_{$post.id}_quote">
              <li class="quote_button"><a href="#postmodify" onclick="return insertQuoteFast('{$post.id}');"><span>{$T.bbc_quote}</span></a></li>
            </ul>
          {/if}
          <br class="clear">
          {if $ignoring}
            <div id="msg_{$post.id}_ignored_prompt" class="smalltext">
              {$T.ignoring_user}
              <a href="#" id="msg_{$post.id}_ignored_link" style="display: none;">{$T.show_ignore_user_post}</a>
          </div>
          {/if}
          <div class="list_posts smalltext" id="msg_{$post.id}_body">{$post.message}</div>
        </div>
      </div>
    {/foreach}
  </div>
  <script type="text/javascript"><!-- // --><![CDATA[
    var aIgnoreToggles = new Array();

    {foreach $ignored_posts as $post_id}
    {/foreach}

    function insertQuoteFast(messageid)
    {
      getXMLDocument(smf_prepareScriptUrl(smf_scripturl) + 'action=quotefast;quote=' + messageid + ';xml;pb={$C.post_box_name};mode=' + (oEditorHandle_{$C.post_box_name}.bRichTextEnabled ? 1 : 0), onDocReceived);
      return true;
    }
    function onDocReceived(XMLDoc)
    {
      var text = '';
        for (var i = 0, n = XMLDoc.getElementsByTagName('quote')[0].childNodes.length; i < n; i++)
          text += XMLDoc.getElementsByTagName('quote')[0].childNodes[i].nodeValue;
        oEditorHandle_{$C.post_box_name}.insertText(text, false, true);
      }
      
      var this_post = {(empty($C.quoted_id)) ? 0 : $C.quoted_id};
      function getMultiQuotes()
      {
        var _c = readCookie("mquote") || "";
        if(_c.length > 1) {
          var _s = _c.split(",");
          for (var i = 0; i < _s.length; i++)
            loadMultiQuoteById(_s[i]);
          createCookie("mquote", "", -1);
        }
      }
      function loadMultiQuoteById(mid)
      {
         var message_id = parseInt(mid);
        if(parseInt(message_id) != parseInt(this_post))
          insertQuoteFast(message_id);
      }
      $(document).ready(function() {
        getMultiQuotes();
      });
    // ]]></script>
{/if}
{/block}