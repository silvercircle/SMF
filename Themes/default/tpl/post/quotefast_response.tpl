<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" {($C.right_to_left) ? ' dir="rtl"' : ''}>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>{$T.retrieving_quote}</title>
    <script type="text/javascript" src="{$S.default_theme_url}/scripts/script.js"></script>
  </head>
  <body>
    {$T.retrieving_quote}
    <div id="temporary_posting_area" style="display: none;"></div>
    <script type="text/javascript"><!-- // --><![CDATA[';
    {if $C.close_window}
      window.close();
    {else}
      // Lucky for us, Internet Explorer has an "innerText" feature which basically converts entities <--> text. Use it if possible ;).
      var quote = "{$C.quote.text}";
      var stage = 'createElement' in document ? document.createElement("DIV") : document.getElementById("temporary_posting_area");

      if ('DOMParser' in window && !('opera' in window))
      {
        var xmldoc = new DOMParser().parseFromString("<temp>" + {$C.quote.mozilla}.replace(/\n/g, "_SMF-BREAK_").replace(/\t/g, "_SMF-TAB_") + "</temp>", "text/xml");
        quote = xmldoc.childNodes[0].textContent.replace(/_SMF-BREAK_/g, "\n").replace(/_SMF-TAB_/g, "\t");
      }
      else if ('innerText' in stage)
      {
        setInnerHTML(stage, quote.replace(/\n/g, "_SMF-BREAK_").replace(/\t/g, "_SMF-TAB_").replace(/</g, "&lt;").replace(/>/g, "&gt;"));
        quote = stage.innerText.replace(/_SMF-BREAK_/g, "\n").replace(/_SMF-TAB_/g, "\t");
      }

      if ('opera' in window)
        quote = quote.replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&quot;/g, '"').replace(/&amp;/g, "&");

      window.opener.oEditorHandle_{$C.post_box_name}.InsertText(quote);
      window.focus();
      setTimeout("window.close();", 400);
    {/if}
    // ]]></script>
  </body>
</html>
