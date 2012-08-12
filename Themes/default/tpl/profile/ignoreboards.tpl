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
  <div class="cat_bar">
    <h3>
      <span class="ie6_header floatleft">{$T.ignoreboards}</span>
    </h3>
  </div>
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
