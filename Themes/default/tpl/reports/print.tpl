<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"{($C.right_to_left) ? ' dir="rtl"' : ''}>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>{$C.page_title}</title>
    <link rel="stylesheet" type="text/css" href="{$S.default_theme_url}/css/report.css" />
  </head>
  <body>
  {include "reports/content.tpl"}
  <div class="copyright">{$C.template_copyright}</div>
  </body>
</html>
