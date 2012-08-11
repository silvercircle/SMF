<div class="cat_bar">
  <h3>{($C.delete_all) ? $T.delete_message : $T.delete_all}</h3>
</div>
<div class="blue_container">
  <div class="content">
    <p>{$T.delete_all_confirm}</p><br />
    <strong><a href="{$SCRIPTURL}?action=pm;sa=removeall2;f={$C.folder};{($C.current_label_id != -1) ? (';l='|cat:$C.current_label_id) : ''};{$C.session_var}={$C.session_id}">{$T.yes}</a> - <a href="javascript:history.go(-1);">{$T.no}</a></strong>
  </div>
</div>