<form action="{$SCRIPTURL}?action=register" method="post" accept-charset="UTF-8" id="registration">
  <h1 class="bigheader section_header">{$T.registration_agreement}</h1>
  <div class="blue_container cleantop">
    <div class="content">
      {$C.agreement}
    </div>
  </div>
  {$SUPPORT->displayHook('register_agreement_form')}
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
