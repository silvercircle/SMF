<script>
// <![CDATA[
  function dismissNews(id) {
    sendRequest('action=dismissnews;id=' + id, null);
  }
// ]]>
</script>
<div class="blue_container gradient_darken_down" id="newsitem_container">
    <ol class="commonlist noshadow news" id="newsitem_list">
      {foreach from=$C.news_items item=item}
        <li class="visible" id="newsitem_{$item.id}">
        {if $item.can_dismiss and $C.can_dismiss_news}
          <div class="floatright">
            <a onclick="dismissNews('{$item.id}');return(false);" href="{$SCRIPTURL}?action=dismissnews;id={$item.id}">X</a>
          </div>
        {/if}
        {$item.body}
        </li>
      {/foreach}
    </ol>
</div>
<div class="cContainer_end"></div>
