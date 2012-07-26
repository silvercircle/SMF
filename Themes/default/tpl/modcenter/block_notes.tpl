<div class="modblock_{($C.alternate) ? 'left' : 'right'}">
<form action="{$SCRIPTURL}?action=moderate;area=index" method="post">
  <div class="cat_bar">
    <h3>{$T.mc_notes}</h3>
  </div>
  <div class="blue_container cleantop">
    <div class="content modbox">
    {if !empty($C.notes)}
      <ul class="reset moderation_notes">';
      {foreach $C.notes as $note}
        <li class="smalltext"><a href="{$note.delete_href}"><img src="{$S.images_url}/pm_recipient_delete.gif" alt="" /></a> <strong>{$note.author.link}:</strong>{$note.text}</li>
      {/foreach}
      </ul>
      <div class="pagesection notes">
        <span class="smalltext">{$C.page_index}</span>
      </div>
    {/if}
    <div class="floatleft post_note">
      <input type="text" name="new_note" value="{$T.mc_click_add_note}" style="width: 95%;" onclick="if (this.value == \'{$T.mc_click_add_note}\') this.value = \'\';" class="input_text" />
    </div>
    <div class="floatright">
      <input type="submit" name="makenote" value="{$T.mc_add_note}" class="button_submit" />
    </div>
    <br class="clear" />
    </div>
  </div>
  <input type="hidden" name="{$C.session_var}" value="{$C.session_id}" />
</form>
</div>
{if !$C.alternate}
  <br class="clear" />
{/if}
{$C.alternate = !$C.alternate}
