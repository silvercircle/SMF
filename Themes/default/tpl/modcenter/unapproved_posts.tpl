<div id="modcenter">
  <form action="', $scripturl, '?action=moderate;area=postmod;start=', $context['start'], ';sa=', $context['current_view'], '" method="post" accept-charset="UTF-8">
    <br>
    {if empty($C.unapproved_items)}
      <div class="red_container norounded smallpadding">
        {$index = 'mc_unapproved_'|cat:$C.current_view|cat:'_none_found'}
        <p class="centertext">{$T.$index}</p>
      </div>
    {else}
      <div class="pagesection">
        <div class="pagelinks">{$C.page_index}</div>
      </div>
    {/if}
    {foreach $C.unapproved_items as $item}
      <div class="cat_bar">
        <h3>
          <span class="smalltext floatleft">{$item.counter}&nbsp;</span>
          <span class="smalltext floatleft"><a href="{$SCRIPTURL}#c{$item.category.id}">{$item.category.name}</a> / <a href="{$SCRIPTURL}?board={$item.board.id}.0">{$item.board.name}</a> / <a href="{$SCRIPTURL}?topic={$item.topic.id}.msg{$item.id}#msg{$item.id}">{$item.subject}</a></span>
          <span class="smalltext floatright">{$T.mc_unapproved_by} {$item.poster.link}, {$item.time}</span>
        </h3>
      </div>
      <div class="{($item.alternate) ? 'windowbg' : 'windowbg2'}">
        <div class="content">
          <div class="post">{$item.body}</div>
          <span class="floatright">
            <a href="{$SCRIPTURL}?action=moderate;area=postmod;sa={$C.current_view};start={$C.start};{$C.session_var}={$C.session_id};approve={$item.id}">{$C.approve_button}</a>
            {if $item.can_delete}
              {$C.menu_separator}
              <a href="{$SCRIPTURL}?action=moderate;area=postmod;sa={$C.current_view};start={$C.start};{$C.session_var}={$C.session_id};delete={$item.id}">{$C.remove_button}</a>
            {/if}
            <input type="checkbox" name="item[]" value="{$item.id}" checked="checked" class="input_check" />
          </span>
          <br class="clear" />
        </div>
      </div>
    {/foreach}
    <div class="pagesection">
      <div class="floatright">
        <select name="do" onchange="if (this.value != 0 &amp;&amp; confirm(\'{$T.mc_unapproved_sure}\')) submit();">
          <option value="0">{$T.with_selected}:</option>
          <option value="0">-------------------</option>
          <option value="approve">&nbsp;--&nbsp;{$T.approve}</option>
          <option value="delete">&nbsp;--&nbsp;{$T.delete}</option>
        </select>
        <noscript><input type="submit" name="submit" value="{$T.go}" class="button_submit" /></noscript>
      </div>
      {if !empty($C.unapproved_items)}
        <div class="floatleft">
          <div class="pagelinks">{$C.page_index}</div>
        </div>
      {/if}
    </div>
    <input type="hidden" name="{$C.session_var}" value="{$C.session_id}" />
  </form>
  </div>
  <br class="clear">