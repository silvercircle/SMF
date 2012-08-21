<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" {($C.right_to_left) ? ' dir="rtl"' : ''}>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>{$C.page_title}</title>
    <meta name="robots" content="noindex" />
    <link rel="stylesheet" type="text/css" href="{$S.theme_url}/css/base.css{$C.jsver}" />
    <link rel="stylesheet" type="text/css" href="{$S.primary_css}.css{$C.jsver}" />
    <style type="text/css">
    </style>
  </head>
  <body style="margin: 1ex;">
    <div class="popuptext" style="text-align: center;">
    {if $C.browser.is_ie}
      <object style="width:80%;height:30px;" classid="clsid:22D6F312-B0F6-11D0-94AB-0080C74C7E95" type="audio/x-wav">
        <param name="AutoStart" value="1" />
        <param name="FileName" value="{$C.verification_sound_href}" />
      </object>
    {else}
      <object style="width:80%;height:30px;" type="audio/x-wav" data="{$C.verification_sound_href}">
        <a href="{$C.verification_sound_href}" rel="nofollow">{$C.verification_sound_href}</a>
      </object>
    {/if}
    <br>
    <a href="{$C.verification_sound_href};sound" rel="nofollow">{$T.visual_verification_sound_again}</a><br>
    <a href="{$C.verification_sound_href}" rel="nofollow">{$T.visual_verification_sound_direct}</a>
    </div>
  </body>
</html>
