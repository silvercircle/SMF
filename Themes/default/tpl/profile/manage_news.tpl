<h1 class="bigheader secondary title indent bordered">{$T.dismissed_news_items}</h1>
<div class="orange_container cleantop norounded smalltext mediumpadding">
  {$T.dismissed_news_item_explain}
</div>
<div class="blue_container norounded gradient_darken_down mediumpadding">
  <div class="content">
    {if $C.have_items}
      <ol class="commonlist news">
        {foreach $C.dismissed_items as $item}
          <li>
            {$item.body}
          </li>
        {/foreach}
      </ol>
    {else}
      <div class="red_container norounded gradient_darken_down mediumpadding centertext">
        <strong>{$T.no_dismissed_news_items}</strong>
      </div>
    {/if}
  </div>
</div>
<br>
<div class="centertext floatright">
  <ul class="buttonlist"><li>{$C.reactivate_link}</li></ul>
</div>
