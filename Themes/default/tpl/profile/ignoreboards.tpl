{include "generics/boardlisting.tpl"}
<script type="text/javascript"><!-- // --><![CDATA[
  function selectBoards(ids)
  {
    var toggle = true;
    for (i = 0; i < ids.length; i++)
      toggle = toggle & document.forms.creator["ignore_brd" + ids[i]].checked;

    for (i = 0; i < ids.length; i++)
      document.forms.creator["ignore_brd" + ids[i]].checked = !toggle;
  }
  // ]]>
</script>
<form action="{$SUPPORT->url_parse('?action=profile;area=ignoreboards;save')}" method="post" accept-charset="UTF-8" name="creator" id="creator">
   <h1 class="bigheader section_header bordered">
      {$T.ignoreboards}
   </h1>
  <div class="orange_container smalltext cleantop norounded gradient_darken_down mediumpadding">{$T.ignoreboards_info}</div>
  <br>
  <div class="blue_container">
    <div class="content flow_hidden">
      {call boardlisting prefix='ignore_brd'}
      {call savebutton}
    </div>
  </div>
</form>
<br>
