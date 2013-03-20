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
 */
// !!!
/*  This template file contains only the sub template fatal_error. It is
  shown when an error occurs, and should show at least a back button and
  $context['error_message'].
*}
{extends 'base.tpl'}
{block content}
<div id="fatal_error">
  <h1 class="bigheader section_header">
    {$C.error_title}
  </h1>
  <div class="blue_container cleantop">
    <div class="mediumpadding content">{$C.error_message}</div>
  </div>
</div>
<div class="centertext mediummargin">
  <input type="button" value="{$T.back}" class="button_submit" onclick="javascript:history.go(-1)" />
</div>
{/block}

{function show_file}
<!DOCTYPE html >
<html>
  <head>
    <title>{$C.file_data.file}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="{$S.theme_url}/css/index.css" />
  </head>
  <body>
    <table border="0" cellpadding="0" cellspacing="3">
      {foreach $C.file_data.contents as $index => $line}
        {$line_num = $index+$C.file_data.min}
        {$is_target = $line_num == $C.file_data.target}
        <tr>
          <td align="right" {($is_target) ? (' style="font-weight: bold; border: 1px solid black;border-width: 1px 0 1px 1px;">==&gt;') : '>'}{$line_num}:</td>
          <td style="white-space: nowrap;{($is_target) ? ' border: 1px solid black;border-width: 1px 1px 1px 0;' : ''}">{$line}</td>
        </tr>
      {/foreach}
   </table>
  </body>
</html>
{/function}