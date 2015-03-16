<?xml version="1.0" encoding="UTF-8" ?>
<document>
 <response open="default_overlay" width="60%" max-width="70%" />
 <content>
 <![CDATA[ <!-- > -->
  <div class="title_bar">
    <h1>{$C.file_data.file}</h1>
  </div>
  <div class="flat_container" style="max-height:80%;overflow:auto;">
    <table border="0" cellpadding="0" cellspacing="3">
    {foreach $C.file_data.contents as $index => $line}
      {$line_num = $index + $C.file_data.min}
      {$is_target = ($line_num == $C.file_data.target) ? 1 : 0}
      <tr>
        <td {($is_target) ? 'style="font-weight: bold; border: 1px solid black;border-width: 1px 0 1px 1px;text-align:right;"' : 'style="text-align:right"'}>{$line_num}:</td>
        <td style="white-space: nowrap;{($is_target) ? ' border: 1px solid black;border-width: 1px 1px 1px 0;':''}">{$line}</td>
      </tr>
    {/foreach}
    </table>
  </div>
 ]]>
 </content>
</document>