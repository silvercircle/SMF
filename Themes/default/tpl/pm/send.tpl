{extends 'pm/base.tpl'}
{block 'pm_content'}
{include 'generics/editor_control.tpl'}
{if !empty($context.send_log)}
  <div class="cat_bar">
    <h3>{$T.pm_send_report}</h3>
  </div>
  <div class="blue_container">
    <div class="content">
      {if !empty($C.send_log.sent)}
        {foreach $C.send_log.sent as $log_entry}
          <span class="error">{$log_entry}</span><br>
        {/foreach}
      {/if}
      {if !empty($C.send_log.failed)}
        {foreach $C.send_log.failed as $log_entry}
          <span class="error">{$log_entry}</span><br>
        {/foreach}
      {/if}
    </div>
  </div>
  <br>
{/if}
{if isset($C.preview_message)}
  <div class="cat_bar">
    <h3>{$C.preview_subject}</h3>
  </div>
  <div class="blue_container cleantop">
    <div class="content">
      {$C.preview_message}
    </div>
  </div>
  <br>
{/if}
<div class="cat_bar2">
  <h3>
    {$T.new_message}
  </h3>
</div>
<form action="{$SCRIPTURL}?action=pm;sa=send2" method="post" accept-charset="UTF-8" name="postmodify" id="postmodify" class="flow_hidden" onsubmit="submitonce(this);smc_saveEntities('postmodify', ['subject', 'message']);">
  <div>
    <div class="blue_container cleantop mediumpadding"><br class="clear">
    {if !empty($C.post_error.messages)}
      <div class="errorbox">
        <strong>{$T.error_while_submitting}</strong>
        <ul class="reset">
        {foreach $C.post_error.messages as $error}
          <li class="error">{$error}</li>
        {/foreach}
        </ul>
      </div>
    {/if}
    <dl id="post_header">
    <dt>
      <span {(isset($C.post_error.no_to) or isset($C.post_error.bad_to)) ? ' class="error"' : ''}>{$T.pm_to}:</span>
    </dt>
    <dd id="pm_to" class="clear_right">
      <input type="text" name="to" id="to_control" value="{$C.to_value}" tabindex="{$C.tabindex}" size="40" style="width: 130px;" class="input_text" />
      {$C.tabindex = $C.tabindex+1}
      <span class="smalltext" id="bcc_link_container" style="display: none;"></span>
      <div id="to_item_list_container"></div>
    </dd>
    <dt  class="clear_left" id="bcc_div">
      <span {(isset($C.post_error.no_to) or isset($C.post_error.bad_bcc)) ? ' class="error"' : ''}>{$T.pm_bcc}:</span>
    </dt>
    <dd id="bcc_div2">
      <input type="text" name="bcc" id="bcc_control" value="{$C.bcc_value}" tabindex="{$C.tabindex}" size="40" style="width: 130px;" class="input_text" />
      {$C.tabindex = $C.tabindex+1}
      <div id="bcc_item_list_container"></div>
    </dd>
    <dt class="clear_left">
      <span {(isset($C.post_error.no_subject)) ? ' class="error"' : ''}>{$T.subject}:</span>
    </dt>
    <dd id="pm_subject">
      <input type="text" name="subject" value="{$C.subject}" tabindex="{$C.tabindex}" size="60" maxlength="60" />
      {$C.tabindex = $C.tabindex+1}
    </dd>
    </dl>
    <hr class="clear">
    <div id="editor_main_content">
      <div class="floatright">
        <div id="smiley_popup_anchor" style="position:relative;">
          <span id="editor_main_content_zoom" class="button">{$T.zoom_editor}</span>
          <span onclick="popupSmileySelector($(this));return(false);" id="editor_main_smiley_popup" class="button">Smileys</span>
        </div>
      </div>
      {if $C.show_bbc}
        <div id="bbcBox_message"></div>
      {/if}
      {call control_richedit editor_id=$C.post_box_name smileyContainer='smileyBox_message' bbcContainer='bbcBox_message'}
    </div>
    {if $C.require_verification}
      <div class="post_verification">
        <strong>{$T.pm_visual_verification_label}:</strong>
        {$SUPPORT->template_control_verification($C.visual_verification_id, 'all')}
      </div>
    {/if}
    <p><label for="outbox"><input type="checkbox" name="outbox" id="outbox" value="1" tabindex="{$C.tabindex}" {($C.copy_to_outbox) ? ' checked="checked"' : ''} class="input_check aligned" />{$T.pm_save_outbox}</label></p>
    {$C.tabindex = $C.tabindex+1}
    <p id="shortcuts" class="smalltext">
      {($C.browser.is_firefox) ? $T.shortcuts_firefox : $T.shortcuts}
    </p>
    <p id="post_confirm_strip" class="righttext">
      {call control_richedit_buttons editor_id=$C.post_box_name}
    </p>
      {$C.hidden_sid_input}
      <input type="hidden" name="seqnum" value="{$C.form_sequence_number}" />
      <input type="hidden" name="replied_to" value="{(!empty($C.quoted_message.id)) ? $C.quoted_message.id : 0}" />
      <input type="hidden" name="pm_head" value="{(!empty($C.quoted_message.pm_head)) ? $C.quoted_message.pm_head : 0}" />
      <input type="hidden" name="f" value="{(isset($C.folder)) ? $C.folder : ''}" />
      <input type="hidden" name="l" value="{(isset($C.current_label_id)) ? $C.current_label_id : -1}" />
      <br class="clear">
    </div>
  </div>
</form>
{if $C.reply}
<br>
<div class="cat_bar">
  <h3>{$T.subject}: {$C.quoted_message.subject}</h3>
</div>
<div class="blue_container">
  <div class="content">
    <div class="clear">
      <span class="smalltext floatright">{$T.on}: {$C.quoted_message.time}</span>
      <strong>{$T.from}: {$C.quoted_message.member.name}</strong>
    </div>
    <hr>
      {$C.quoted_message.body}
  </div>
</div>
<br class="clear">
{/if}
<script type="text/javascript" src="{$S.default_theme_url}/scripts/PersonalMessage.js?fin20"></script>
<script type="text/javascript" src="{$S.default_theme_url}/scripts/suggest.js?fin20"></script>
<script type="text/javascript"><!-- // --><![CDATA[
  var txtlabel_zoom = "{$T.zoom_editor}";
  var txtlabel_restore = "{$T.restore_editor}";
  var oPersonalMessageSend = new smf_PersonalMessageSend( {
        sSelf: 'oPersonalMessageSend',
        sSessionId: "{$C.session_id}",
        sSessionVar: "{$C.session_var}",
        sTextDeleteItem: "{$T.autosuggest_delete_item}",
        sToControlId: 'to_control',
        aToRecipients: [
        {foreach $C.recipients.to as $i => $member}
          {
            sItemId: {$SUPPORT->JavaScriptEscape($member.id)},
            sItemName: {$SUPPORT->JavaScriptEscape($member.name)}
          } {($i == count($C.recipients.to) - 1) ? '' : ','}
        {/foreach}
        ],
        aBccRecipients: [
        {foreach $C.recipients.bcc as $i => $member}
          {
            sItemId: {$SUPPORT->JavaScriptEscape($member.id)},
            sItemName: {$SUPPORT->JavaScriptEscape($member.name)}
          } {($i == count($C.recipients.bcc) - 1) ? '' : ','}
        {/foreach}
        ],
        sBccControlId: 'bcc_control',
        sBccDivId: 'bcc_div',
        sBccDivId2: 'bcc_div2',
        sBccLinkId: 'bcc_link',
        sBccLinkContainerId: 'bcc_link_container',
        bBccShowByDefault: {(empty($C.recipients.bcc) and empty($C.bcc_value)) ? 'false' : 'true'},
        sShowBccLinkTemplate: {$SUPPORT->JavaScriptEscape('<a href="#" id="bcc_link">'|cat:$T.make_bcc|cat:'</a> <a href="'|cat:$SCRIPTURL|cat:'?action=helpadmin;help=pm_bcc" onclick="return reqWin(this.href);">(?)</a>')}
      } );
    // ]]></script>
{/block}