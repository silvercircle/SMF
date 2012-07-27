<?xml version="1.0" encoding="UTF-8" ?>
<document>
 <response open="default_overlay" width="50%" />
  <content>
  <![CDATA[ <!-- > -->
  <div class="title_bar">
    <h1>{$C.titletext}</h1>
  </div>
  <div class="smallpadding" id="mcard_content">
    {include "astream/bits.tpl"}
    {include "astream/astream_output.tpl"}
    <div class="yellow_container smalltext cleantop smallpadding">
    {if isset($C.viewall_url)}
      <a href="{$C.viewall_url}" >View all</a>
    {/if}
    </div>
  </div>
  ]]>
  </content>
</document>
