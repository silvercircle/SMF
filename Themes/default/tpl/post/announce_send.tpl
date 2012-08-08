{extends "base.tpl"}
{block content}
<div id="announcement">
  <form action="{$SCRIPTURL}?action=announce;sa=send" method="post" accept-charset="UTF-8" name="autoSubmit" id="autoSubmit">
    <div class="blue_container">
      <div class="content">
        <p>{$T.announce_sending} <a href="{$SUPPORT->url_parse($SCRIPTURL|cat:'?topic='|cat:$C.current_topic|cat:'.0')}" target="_blank" class="new_win">{$C.topic_subject}</a></p>
          <p><strong>{$C.percentage_done}% {$T.announce_done}</strong></p>
          <div id="confirm_buttons">
            <input type="submit" name="b" value="{$T.announce_continue}" class="default" />
            {$C.hidden_sid_input}
            <input type="hidden" name="topic" value="{$C.current_topic}" />
            <input type="hidden" name="move" value="{$C.move}" />
            <input type="hidden" name="goback" value="{$C.go_back}" />
            <input type="hidden" name="start" value="{$C.start}" />
            <input type="hidden" name="membergroups" value="{$C.membergroups}" />
          </div>
        </div>
      </div>
    </form>
  </div>
  <br>
  <script type="text/javascript"><!-- // --><![CDATA[
    var countdown = 2;
    doAutoSubmit();

    function doAutoSubmit()
    {
      if (countdown == 0)
        document.forms.autoSubmit.submit();
      else if (countdown == -1)
        return;

      document.forms.autoSubmit.b.value = "{$T.announce_continue} (" + countdown + ")";
      countdown--;

      setTimeout("doAutoSubmit();", 1000);
    }
  // ]]></script>
{/block}
