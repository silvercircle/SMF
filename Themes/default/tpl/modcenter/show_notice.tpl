<!DOCTYPE html >
<html {($C.ight_to_left) ? ' dir="rtl"' : ''}>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>{$C.page_title}</title>
    <link rel="stylesheet" type="text/css" href="{$S.primary_css}" />
  </head>
  <body>
    <div class="cat_bar">
      <h3>{$T.show_notice}</h3>
    </div>
    <div class="cat_bar">
      <h3>{$T.show_notice_subject}: {$C.notice_subject}</h3>
    </div>
    <div class="blue_container cleantop">
      <div class="content">
        <dl>
          <dt>
            <strong>{$T.show_notice_text}:</strong>
          </dt>
          <dd>
            {$C.notice_body}
          </dd>
        </dl>
      </div>
    </div>
  </body>
</html>
