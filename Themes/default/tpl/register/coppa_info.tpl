<div class="cat_bar">
  <h3>{$C.page_title}</h3>
</div>
<div class="blue_container cleantop">
  <div class="content">
    <p>{$C.coppa.body}</p>
    <p>
      <span><a href="{$SCRIPTURL}?action=coppa;form;member={$C.coppa.id}" target="_blank" class="new_win">{$T.coppa_form_link_popup}</a> | <a href="{$SCRIPTURL}?action=coppa;form;dl;member={$C.coppa.id}">{$T.coppa_form_link_download}</a></span>
    </p>
    <p>{($C.coppa.many_options) ? $T.coppa_send_to_two_options : $T.coppa_send_to_one_option}</p>
    {if !empty($C.coppa.post)}
      <h4>1) {$T.coppa_send_by_post}</h4>
      <div class="coppa_contact">
        {$C.coppa.post}
      </div>
    {/if}
    {if !empty($C.coppa.fax)}
      <h4>{(!empty($C.coppa.post)) ? '2' : '1'}) {$T.coppa_send_by_fax}</h4>
      <div class="coppa_contact">
        {$C.coppa.fax}
      </div>
    {/if}
    {if $C.coppa.phone}
      <p>{$C.coppa.phone}</p>
    {/if}
  </div>
</div>