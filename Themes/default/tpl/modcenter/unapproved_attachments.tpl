{extends "modcenter/modcenter_base.tpl"}
{block modcenter_content}
  <div id="modcenter">
    <form action="{$SCRIPTURL}?action=moderate;area=attachmod;sa=attachments;start={$C.start}" method="post" accept-charset="UTF-8">
      <div class="cat_bar">
        <h3>{$T.mc_unapproved_attachments}</h3>
      </div>
      {if empty($C.unapproved_items)}
        <div class="orange_container cleantop">
          <div class="content">
            <p class="centertext">{$T.mc_unapproved_attachments_none_found}</p>
          </div>
        </div>
      {else}
        <div class="pagesection">
          <div class="pagelinks">{$C.page_index}</div>
        </div>
        <table class="table_grid" width="100%">
          <thead>
            <tr class="catbg">
              <th>{$T.mc_unapproved_attach_name}</th>
              <th>{$T.mc_unapproved_attach_size}</th>
              <th>{$T.mc_unapproved_attach_poster}</th>
              <th>{$T.date}</th>
              <th nowrap="nowrap" align="center"><input type="checkbox" onclick="invertAll(this, this.form);" class="input_check" checked="checked" /></th>
            </tr>
          </thead>
          <tbody>
        {/if}
        {foreach $C.unapproved_items as $item}
          <tr class="{($item.alternate) ? 'windowbg' : 'windowbg2'}">
            <td>
              {$item.filename}
            </td>
            <td align="right">
              {$item.size} {$T.kilobyte}
            </td>
            <td>
              {$item.poster.link}
            </td>
            <td class="smalltext">
              {$item.time}<br>{$T.in} <a href="{$item.message.href}">{$item.message.subject}</a>
            </td>
            <td width="4%" align="center">
              <input type="checkbox" name="item[]" value="{$item.id}" checked="checked" class="input_check" />
            </td>
          </tr>
        {/foreach}
        {if !empty($C.unapproved_items)}
          </tbody>
          </table>
        {/if}
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
  <br class="clear" />
{/block}