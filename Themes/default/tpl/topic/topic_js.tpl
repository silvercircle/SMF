{* all javascript that is needed on topic pages *}
{if !empty($C.removableMessageIDs)}
  {$msgids = "\", \""|implode:$C.removableMessageIDs}
{else}
  {$msgids = array()}
{/if}
<script>
// <![CDATA
  var smf_likelabel = "{$T.like_label}";
  var smf_unlikelabel = "{$T.unlike_label}";

  var oQuickReply = new QuickReply( { 
    bDefaultCollapsed: false,
    iTopicId: {$C.current_topic},
    iStart: {$C.start},
    sScriptUrl: smf_scripturl,
    sImagesUrl: "{$S.images_url}",
    sContainerId: "quickReplyOptions",
    iMarkedForMQ: {$C.multiquote_posts_count},
    sErrorInEditMsg: "{$T.inline_edit_inprogress}",
    sErrorTitle: "{$T.error_occured}",
    sJumpAnchor: "quickreplybox",
    bEnabled: {(!empty($O.display_quick_reply)) ? 'true' : 'false'}
  
  } );

  {if !empty($O.display_quick_mod) and $C.can_remove_post}
    var oInTopicModeration = new InTopicModeration( { 
      sSelf: 'oInTopicModeration',
      sCheckboxContainerMask: 'in_topic_mod_check_',
      aMessageIds: ["{$msgids}"],
      sSessionId: "{$C.session_id}",
      sSessionVar: "{$C.session_var}",
      sButtonStrip: 'moderationbuttons',
      sButtonStripDisplay: 'moderationbuttons_strip',
      bUseImageButton: false,
      bCanRemove: {($C.can_remove_post) ? 'true' : 'false'},
      sRemoveButtonLabel: "{$T.quickmod_delete_selected}",
      sRemoveButtonImage: 'delete_selected.gif',
      sRemoveButtonConfirm: "{$T.quickmod_confirm}",
      bCanRestore: {($C.can_restore_msg) ? 'true' : 'false'},
      sRestoreButtonLabel: "{$T.quick_mod_restore}",
      sRestoreButtonImage: 'restore_selected.gif',
      sRestoreButtonConfirm: "{$T.quickmod_confirm}",
      sFormId: 'quickModForm'
    } );
  {/if}

  if ('XMLHttpRequest' in window)
  {
    var oQuickModify = new QuickModify( { 
      sScriptUrl: smf_scripturl,
      bShowModify: {($S.show_modify) ? 'true' : 'false'},
      iTopicId: {$C.current_topic},
      sTemplateBodyEdit: {$SUPPORT->JavaScriptEscape("<div id=\"quick_edit_body_container\">
        <div id=\"error_box\" style=\"padding: 4px;\" class=\"error\"></div>
        <textarea class=\"editor\" name=\"message\" rows=\"20\" style=\"{($C.browser.is_ie8) ? 'width: 635px; max-width: 100%; min-width: 100%' : 'width: 100%'}; margin-bottom: 10px;\" tabindex=\"{$tabindex++}\">%body%</textarea><br>
        <input type=\"hidden\" name=\"{$C.session_var}\" value=\"{$C.session_id}\" />
        <input type=\"hidden\" name=\"topic\" value=\"{$C.current_topic}\" />
        <input type=\"hidden\" name=\"msg\" value=\"%msg_id%\" />
        <input type=\"hidden\" style=\"width: 50%;\" name=\"subject\" value=\"%subject%\" size=\"50\" maxlength=\"80\" tabindex=\"{$tabindex++}\" class=\"input_text\" />
        <div class=\"righttext\">
          <span class=\"button floatright\" onclick=\"return oQuickModify.goAdvanced('{$C.session_id}', '{$C.session_var}');\" />{$T.go_advanced}</a></span>
          <span class=\"button floatright\" onclick=\"return oQuickModify.modifyCancel();\" >{$T.modify_cancel}</span>
          <span class=\"button floatright\" onclick=\"return oQuickModify.modifySave('{$C.session_id}', '{$C.session_var}');\" accesskey=\"s\">{$T.save}</span>
        </div>
        </div>")},
      sTemplateSubjectEdit: {$SUPPORT->JavaScriptEscape('<input type="text" style="width: 50%;" name="subject_edit" value="%subject%" size="50" maxlength="80" tabindex="'|cat:$tabindex|cat:'" class="input_text" />')},
      sTemplateBodyNormal: {$SUPPORT->JavaScriptEscape('%body%')},
      sTemplateSubjectNormal: {$SUPPORT->JavaScriptEscape("<a href=\"{$SCRIPTURL}?topic={$C.current_topic}.msg%msg_id%#msg%msg_id%\" rel=\"nofollow\">%subject%</a>")},
      sTemplateTopSubject: {$SUPPORT->JavaScriptEscape($T.topic|cat:': %subject% &nbsp;('|cat:$T.read|cat:' '|cat:$C.num_views|cat:' '|cat:$T.times|cat:')')},
      sErrorBorderStyle: {$SUPPORT->JavaScriptEscape('1px solid red')}
    } );

    aJumpTo[aJumpTo.length] = new JumpTo( {
      sContainerId: "display_jump_to",
      sJumpToTemplate: "<label class=\"smalltext\" for=\"%select_id%\">{$C.jump_to.label}:</label> %dropdown_list%",
      iCurBoardId: {$C.current_board},
      iCurBoardChildLevel: {$C.jump_to.child_level},
      sCurBoardName: "{$C.jump_to.board_name}",
      sBoardChildLevelIndicator: "==",
      sBoardPrefix: "=> ",
      sCatSeparator: "-----------------------------",
      sCatPrefix: "",
      sGoButtonLabel: "{$T.go}"
    });
  }

  function getIntralink(e, mid) { 
    var tid = {$C.current_topic};
    var _sid = "#subject_" + mid;
    var el = $("#interpostlink_helper");
    el.css("position", "fixed");
    var _content = "[ilink topic=" + tid + " post=" + mid + "]" + $(_sid).html().trim() + "[/ilink]";
    $("#interpostlink_helper_content").val(_content);
    $("#interpostlink_helper_content_full").val(e.attr("href"));
    centerElement(el, -200);
    el.css("z-index", 9999);
    setDimmed(1);
    el.show();
    $("#interpostlink_helper_content").focus();
    $("#interpostlink_helper_content").select(); 
  }

  $(document).keydown(function(e) { 
    if(e.keyCode == 27 && $("#interpostlink_helper").css("display") != "none") {
      $("#interpostlink_helper").css("position", "static");
      $("#interpostlink_helper").hide();
      setDimmed(0);
    } 
  } );
  var topic_id = {$C.current_topic};
// ]]>
</script>