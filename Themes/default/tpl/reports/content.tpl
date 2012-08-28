{foreach $C.tables as $table}
  <table class="table_grid mlist" style="width:100%;margin-bottom:1em;">
  {if !empty($table.title)}
    <thead>
      <tr>
        <th class="glass" scope="col" colspan="{$table.column_count}">{$table.title}</th>
      </tr>
    </thead>
    <tbody>
  {/if}
  {$row_number = 0}
  {$alternate = false}
  {foreach $table.data as $row}
    {if $row_number == 0 and !empty($table.shading.top)}
      <tr class="windowbg table_caption">
    {else}
      <tr class="{(!empty($row.0.separator)) ? 'catbg' : (($alternate) ? 'tablerow alternate' : 'tablerow')}">
    {/if}
    {$column_number = 0}
    {foreach $row as $key => $data}
      {if !empty($data.separator) and $column_number == 0}
        <td colspan="{$table.column_count}" class="smalltext">
          {$data.v}:
        </td>
        {break}
      {/if}
      {if $column_number == 0 and !empty($table.shading.left)}
        <td align="{$table.align.shaded}" class="table_caption"{($table.width.shaded != 'auto') ? (' width="'|cat:$table.width.shaded|cat:'"') : ''}>
          {($data.v == $table.default_value) ? '' : ($data.v|cat:((empty($data.v)) ? '' : ':'))}
        </td>
      {else}
        <td class="smalltext" align="{$table.align.normal}"{($table.width.normal != 'auto') ? (' width="'|cat:$table.width.normal|cat:'"') : ''}{(!empty($data.style)) ? (' style="'|cat:$data.style|cat:'"') : ''}>
          {$data.v}
        </td>
      {/if}
      {$column_number = $column_number+1}
    {/foreach}
    </tr>
    {$row_number = $row_number+1}
    {$alternate = !$alternate}
  {/foreach}
    </tbody>
  </table>
{/foreach}
