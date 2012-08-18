{function activitybit}
  {if isset($a.member)}
    <li class="tablerow{($C.alt_row) ? ' alternate' : ''}{($a.unread) ? ' unread' : ' read'}" id="_nn_{$a.id_act}" data-id="{$a.id_act}">
      <div class="floatleft" style="margin-right:10px;">
       <span class="small_avatar">
        {if !empty($a.member.avatar.image)}
          <img class="twentyfour" src="{$a.member.avatar.href}" alt="avatar" />
        {else}
          <img class="twentyfour" src="{$S.images_url}/unknown.png" alt="avatar" />
        {/if}
       </span>
      </div>
      {$a.formatted_result}<br>
      {$a.dateline}
      <div class="clear"></div>
    </li>
  {else}
    <li class="{($a.unread) ? 'unread' : 'read'}" id="_nn_{$a.id_act}" data-id="{$a.id_act}">
      {$a.formatted_result}
    </li>
  {/if}
{/function}
{function notifications_scripts}
  <script type="text/javascript">
    $("ol#notifylist li.unread a._m").die("click");
    function markAllNotificationsRead()
    {
      var ids = "";
      $("#notifylist li").each(function() {
        ids += ($(this).attr("data-id") + ",");
      });
      var sUrl =  smf_prepareScriptUrl(smf_scripturl) + "action=astream;sa=markread;act=" + ids + ";xml";
      sendXMLDocument(sUrl, "", notifyMarkReadHandleResponse);
      setBusy(1);
    }
    function notifyMarkReadHandleResponse(responseXML)
    {
      setBusy(0);
      var data = $(responseXML);
      var response = data.find("response");
      var ids = response.children("[name=\'markedread\']");

      var total = parseInt($("#alerts").html());

      if(ids.length == 1 && $(ids[0]).text() == "all") {
        total = 0;
      }
      else {
        ids.each(function() {
          var id = parseInt($(this).text());
          var sel = "ol#notifylist li#_nn_" + id;
          $(sel).removeClass("unread");
          total--;
        });
      }
      $("#alerts").html(total);
      if(total <= 0)
        $("#alerts").hide();
    }
    $("ol#notifylist li.unread a._m").live("click", function() {
      var id = parseInt($(this).parent().attr("data-id"));
      var sUrl =  smf_prepareScriptUrl(smf_scripturl) + "action=astream;sa=markread;act=" + id + ";xml";
      //sendXMLDocument(sUrl, "", notifyMarkReadHandleResponse);
      //setBusy(1);
      return(true);
    });
    $(document).ready(function() {
      $("ol#notifylist li.unread a._m").each(function() {
        var _s = $(this).attr("href");
        if(_s.indexOf("#")) {
          var _parts = _s.split("#");
          _s = _parts[0] + ";nmdismiss=" + $(this).attr("data-id") + "#" + _parts[1];
        }
        else
          _s += (";nmdismiss=" + $(this).attr("data-id"));
        $(this).attr("href", _s);
      });
    });
  </script>
{/function}