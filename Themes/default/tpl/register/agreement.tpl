<form action="{$SCRIPTURL}?action=register" method="post" accept-charset="UTF-8" id="registration">
  <div class="cat_bar2">
    <h3>{$T.registration_agreement}</h3>
  </div>
  <div class="blue_container cleantop">
    <div class="content">
      {$C.agreement}
    </div>
  </div>
  <div id="confirm_buttons">
  {if $C.show_coppa}
    <input type="submit" name="accept_agreement" value="{$C.coppa_agree_above}" class="default" /><br><br>
    <input type="submit" name="accept_agreement_coppa" value="{$C.coppa_agree_below}" class="default" />
  {else}
    <input type="submit" name="accept_agreement" value="{$T.agreement_agree}" class="default" />
  {/if}
  </div>
  <input type="hidden" name="step" value="1" />
</form>
