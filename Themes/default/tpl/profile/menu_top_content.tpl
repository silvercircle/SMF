<div>
  <div class="avatar big" style="margin-left:auto;margin-right:auto;text-align:center;">
    {if !empty($C.member.avatar.image)}
      {$C.member.avatar.image}
    {else}
      <img class="avatar" src="{$S.images_url}/unknown.png" alt="avatar" />
    {/if}
  </div>
</div>
<div class="spacer_h"></div>