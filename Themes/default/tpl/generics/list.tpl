{**
 * @name      EosAlpha BBS
 * @copyright 2011 Alex Vie silvercircle(AT)gmail(DOT)com
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright: 2011 Simple Machines (http://www.simplemachines.org)
 * license:   BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version 1.0pre
 *}

{function list_menu}
  {**
    // This is use if you want your generic lists to have tabs.
    $cur_list['list_menu'] = array(
      // This is the style to use.  Tabs or Buttons (Text 1 | Text 2).
      // By default tabs are selected if not set.
      // The main difference between tabs and buttons is that tabs get highlighted if selected.
      // If style is set to buttons and use tabs is diabled then we change the style to old styled tabs.
      'style' => 'tabs',
      // The posisiton of the tabs/buttons.  Left or Right.  By default is set to left.
      'position' => 'left',
      // This is used by the old styled menu.  We *need* to know the total number of columns to span.
      'columns' => 0,
      // This gives you the option to show tabs only at the top, bottom or both.
      // By default they are just shown at the top.
      'show_on' => 'top',
      // Links.  This is the core of the array.  It has all the info that we need.
      'links' => array(
        'name' => array(
          // This will tell use were to go when they click it.
          'href' => $scripturl . '?action=theaction',
          // The name that you want to appear for the link.
          'label' => $txt['name'],
          // If we use tabs instead of buttons we highlight the current tab.
          // Must use conditions to determine if its selected or not.
          'is_selected' => isset($_REQUEST['name']),
        ),
      ),
    );
  *}
  {$first = ($C.right_to_left) ? 'last' : 'first'}
  {$last = ($C.right_to_left) ? 'first' : 'last'}

  {if !isset($list_menu.style) || isset($list_menu.style) && $list_menu.style == 'tabs'}
    <table cellpadding="0" cellspacing="0" style="margin-{$list_menu.position}: 10px; width: 100%;">
      <tr>{($list_menu.position == 'right') ? '<td>&nbsp;</td>' : ''}
        <td align="{$list_menu.position}">
          <table cellspacing="0" cellpadding="0">
            <tr>
              <td class="{($direction == 'top') ? 'mirror' : 'main'}tab_{$first}">&nbsp;</td>
              {foreach $list_menu.links as $link}
                {if $link.is_selected}
                  <td class="{($direction == 'top') ? 'mirror' : 'main'}tab_active_{$first}">&nbsp;</td>
                  <td valign="top" class="{($direction == 'top') ? 'mirrortab' : 'maintab'}_active_back">
                    <a href="{$link.href}">{$link.label}</a>
                  </td>
                  <td class="{($direction == 'top') ? 'mirror' : 'main'}tab_active_{$last}">&nbsp;</td>
                {else}
                  <td valign="top" class="{($direction == 'top') ? 'mirror' : 'main'}tab_back">
                    <a href="{$link.href}">{$link.label}</a>
                  </td>
                {/if}
              {/foreach}
              <td class="{($direction == 'top') ? 'mirror' : 'main'}tab_{$last}">&nbsp;</td>
            </tr>
          </table>
        </td>{($list_menu.position == 'left') ? '<td>&nbsp;</td>' : ''}
      </tr>
    </table>
  {elseif isset($list_menu.style) and $list_menu.style == 'buttons'}
    {$links = array()}
    {foreach $list_menu.links as $link}
      {$links[] = '<a href="'|cat:$link.href|cat:'">'|cat:$link.label|cat:'</a>'}
    {/foreach}
    <table cellpadding="0" cellspacing="0" style="margin-', $list_menu['position'], ': 10px; width: 100%;">
      <tr>{($list_menu.position == 'right') ? '<td>&nbsp;</td>' : ''}
        <td align="{$list_menu.position}">
          <table cellspacing="0" cellpadding="0">
            <tr>
              <td class="{($direction == 'top') ? 'mirror' : 'main'}tab_{$first}">&nbsp;</td>
              <td class="{($direction == 'top') ? 'mirror' : 'main'}tab_back">{' &nbsp;|&nbsp; '|implode:$links}</td>
              <td class="{($direction == 'top') ? 'mirror' : 'main'}tab_{$last}">&nbsp;</td>
            </tr>
          </table>
        </td>{($list_menu.position == 'left') ? '<td>&nbsp;</td>' : ''}
      </tr>
    </table>
  {/if}
{/function}

{* param: row_position = position, cur_list = list id *}
{function list_additional_rows}
  {foreach $C.$cur_list.additional_rows.$row_position as $row}
      <div class="additional_row{(empty($row.class)) ? '' : (' '|cat:$row.class)}"{(empty($row.style)) ? '' : (' style="'|cat:$row.style|cat:'"')}>{$row.value}</div>
  {/foreach}
{/function}
{* param: list_id = list identifier *}
{function show_list}
  {$lid = $list_id}
  {*$cur_list = &$context[$list_id];*}
  {if isset($C.$lid.list_menu, $C.$lid.list_menu.show_on) and ($C.$lid.list_menushow_on == 'both' or $C.$lid.list_menu.show_on == 'top')}
    {call list_menu list_menu=$C.$lid.list_menu direction='top'}
  {/if}
  {if isset($C.$lid.form)}
    <form action="{$C.$lid.form.href}" method="post" {(empty($C.$lid.form.name)) ? '' : (' name="'|cat:$C.$lid.form.name|cat:'" id="'|cat:$C.$lid.form.name|cat:'"')} accept-charset="UTF-8">
      <div class="generic_list">
  {/if}
  {if !empty($C.$lid.title)}
    <div class="cat_bar2">
      <h3>
        {$C.$lid.title}
      </h3>
    </div>
  {/if}
  {if isset($C.$lid.additional_rows.top_of_list)}
    {call list_additional_rows row_position='top_of_list' cur_list=$lid}
  {/if}
  {if isset($C.$lid.additional_rows.after_title)}
    <div class="orange_container cleantop gradient_darken_down">
      {call list_additional_rows row_position='after_title' cur_list=$lid}
    </div>
  {/if}
  {if !empty($C.$lid.items_per_page) or isset($C.$lid.additional_rows.bottom_of_list)}
    <div class="flow_auto">
    {if !empty($C.$lid.items_per_page)}
      <div class="floatleft">
        <div class="pagelinks">{$C.$lid.page_index}</div>
      </div>
    {/if}
    {if isset($C.$lid.additional_rows.above_column_headers)}
      <div class="floatright mediumpadding">
        {call list_additional_rows row_position='above_column_headers' cur_list=$lid}
      </div>
    {/if}
    </div>
  {/if}
  <table class="table_grid" style="width:{(!empty($C.$lid.width)) ? $C.$lid.width : '100%;'}">
  {$header_count = $C.$lid.headers|count}
  {if !($header_count < 2 and empty($C.$lid.headers.0.label))}
    <thead>
      <tr>
      {$i = 0}
      {foreach $C.$lid.headers as $col_header}
        {$i = $i + 1}
        {if empty($col_header.class) and $i == 1}
          {$col_header.class = 'first_th'}
        {elseif empty($col_header.class) && $i == $header_count}
          {$col_header.class = 'last_th'}
        {/if}
        <th scope="col" {(empty($col_header.class)) ? ' class="glass cleantopr nowrap"' : (' class="glass cleantopr nowrap '|cat:$col_header.class|cat:'"')} {(empty($col_header.style)) ? '' : (' style="'|cat:$col_header.style|cat:'"')} {(empty($col_header.colspan)) ? '' : (' colspan="'|cat:$col_header.colspan|cat:'"')}>{(empty($col_header.href)) ? '' : ('<a href="'|cat:$col_header.href|cat:'" rel="nofollow">')}{(empty($col_header.label)) ? '&nbsp;' : $col_header.label}{(empty($col_header.href)) ? '' : '</a>'}{(empty($col_header.sort_image)) ? '' : (' <img src="'|cat:$S.images_url|cat:'/sort_'|cat:$col_header.sort_image|cat:'.gif" alt="" />')}</th>
      {/foreach}
      </tr>
    </thead>
    <tbody>
  {/if}
  {if empty($C.$lid.rows) and !empty($C.$lid.no_items_label)}
    <tr>
      <td class="windowbg" colspan="{$C.$lid.num_columns}" align="{(!empty($C.$lid.no_items_align)) ? $C.$lid.no_items_align : 'center'}"><div class="padding">{$C.$lid.no_items_label}</div></td>
    </tr>
  {elseif !empty($C.$lid.rows)}
    {$alternate = false}
    {foreach $C.$lid.rows as $id => $row}
      <tr class="tablerow{($alternate) ? ' alternate' : ''}" id="list_{$lid}_{$id}">
      {foreach $row as $row_data}
        <td{(empty($row_data.class)) ? '' : (' class="'|cat:$row_data.class|cat:'"')}{(empty($row_data.style)) ? '' : (' style="'|cat:$row_data.style|cat:'"')}>{$row_data.value}</td>
      {/foreach}
      </tr>
      {$alternate = !$alternate}
    {/foreach}
  {/if}
  </tbody>
  </table>
  {if !empty($C.$lid.items_per_page) or isset($C.$lid.additional_rows.below_table_data) or isset($C.$lid.additional_rows.bottom_of_list)}
    <div class="flow_auto">
    {if !empty($C.$lid.items_per_page)}
      <div class="floatleft">
        <div class="pagelinks">{$C.$lid.page_index}</div>
      </div>
    {/if}
    {if isset($C.$lid.additional_rows.below_table_data)}
      <div class="floatright mediumpadding">
        {call list_additional_rows row_position='below_table_data' cur_list=$lid}
      </div>
    {/if}
    {if isset($C.$lid.additional_rows.bottom_of_list)}
      <div class="floatright mediumpadding">
        {call list_additional_rows row_position='bottom_of_list' cur_list=$lid}
      </div>
    {/if}
    </div>
  {/if}
  {if isset($C.$lid.form)}
    {foreach $C.$lid.form.hidden_fields as $name => $value}
      <input type="hidden" name="{$name}" value="{$value}" />
    {/foreach}
    </div>
  </form>
  {/if}
  {if isset($C.$lid.list_menu, $C.$lid.list_menu.show_on) and ($C.$lid.list_menu.show_on == 'both' or $C.$lid.list_menu.show_on == 'bottom')}
    {call list_menu list_menu=$C.$lid.list_menu position='bottom'}
  {/if}
  {if isset($C.$lid.javascript)}
    <script type="text/javascript"><!-- // --><![CDATA[
    {$C.$lid.javascript}
    // ]]></script>
  {/if}
{/function}