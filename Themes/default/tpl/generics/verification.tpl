{function control_verification}
  {if empty($C.controls.verification.$verify_id.tracking) || $reset}
    {$C.controls.verification.$verify_id.tracking = 0}
  {/if}
  {$total_items = count($C.controls.verification.$verify_id.questions) + (($C.controls.verification.$verify_id.show_visual) ? 1 : 0)}
  {if $C.controls.verification.$verify_id.tracking > $total_items}
    {$C.controls.verification.$verify_id.result = false}
  {else}
    {section name=items start=0 loop=$total_items step=1}
      {if $display_type == 'single' and $C.controls.verification.$verify_id.tracking != $smarty.section.items.index}
        {continue}
      {/if}
      {if $display_type != 'single'}
        <div id="verification_control_{$smarty.section.items.index}" class="verification_control">
      {/if}
      {if $smarty.section.items.index == 0 and $C.controls.verification.$verify_id.show_visual}
        {if $C.use_graphic_library}
          <img src="{$C.controls.verification.$verify_id.image_href}" alt="{$T.visual_verification_description}" id="verification_image_{$verify_id}" />
        {else}
          <img src="{$C.controls.verification.$verify_id.image_href};letter=1" alt="{$T.visual_verification_description}" id="verification_image_{$verify_id}_1" />
          <img src="{$C.controls.verification.$verify_id.image_href};letter=2" alt="{$T.visual_verification_description}" id="verification_image_{$verify_id}_2" />
          <img src="{$C.controls.verification.$verify_id.image_href};letter=3" alt="{$T.visual_verification_description}" id="verification_image_{$verify_id}_3" />
          <img src="{$C.controls.verification.$verify_id.image_href};letter=4" alt="{$T.visual_verification_description}" id="verification_image_{$verify_id}_4" />
          <img src="{$C.controls.verification.$verify_id.image_href};letter=5" alt="{$T.visual_verification_description}" id="verification_image_{$verify_id}_5" />
          <img src="{$C.controls.verification.$verify_id.image_href};letter=6" alt="{$T.visual_verification_description}" id="verification_image_{$verify_id}_6" />
        {/if}
        <div class="smalltext" style="margin: 4px 0 8px 0;">
            <a href="{$C.controls.verification.$verify_id.image_href};sound" id="visual_verification_{$verify_id}_sound" rel="nofollow">{$T.visual_verification_sound}</a> / <a href="#" id="visual_verification_{$verify_id}_refresh">{$T.visual_verification_request_new}</a>{($display_type != 'quick_reply') ? '<br />' : ''}<br />
            {$T.visual_verification_description}:{($display_type != 'quick_reply') ? '<br />' : ''}
            <input type="text" name="{$verify_id}_vv[code]" value="{(!empty($C.controls.verification.$verify_id.text_value)) ? $C.controls.verification.$verify_id.text_value : ''}" size="30" tabindex="{$C.tabindex}" class="input_text" />
            {$C.tabindex = $C.tabindex + 1}
        </div>
      {else}
        {$qIndex = ($C.controls.verification.$verify_id.show_visual) ? $smarty.section.items.index - 1 : $smarty.section.items.index}
        <div class="smalltext">
          {$C.controls.verification.$verify_id.questions.$qIndex.q}:<br />
            <input type="text" name="{$verify_id}_vv[q][{$C.controls.verification.$verify_id.questions.$qIndex.id}]" size="30" value="{$C.controls.verification.$verify_id.questions.$qIndex.a}" {($C.controls.verification.$verify_id.questions.$qIndex.is_error) ? 'style="border: 1px red solid;"' : ''} tabindex="{$C.tabindex}" class="input_text" />
            {$C.tabindex = $C.tabindex + 1}
        </div>
      {/if}
      {if $display_type != 'single'}
        </div>
      {/if}
      {if $display_type == 'single' and $C.controls.verification.$verify_id.tracking == $smarty.section.items.index}
        {break}
      {/if}
    {/section}
    {$C.controls.verification.$verify_id.tracking = $C.controls.verification.$verify_id.tracking + 1}
    {if $display_type == 'single'}
      {*return true;*}
    {/if}
  {/if}
{/function}