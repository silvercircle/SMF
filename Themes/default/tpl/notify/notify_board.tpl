<div class="cat_bar">
  <h3>
    {$T.notify}
  </h3>
</div>
<div class="blue_container cleantop norounded">
  <div class="content">
    {$(C.notification_set) ? $T.notifyboard_turnoff : $T.notifyboard_turnon}
    <p>
      <strong><a href="{$SUPPORT->url_parse('?action=notifyboard;sa='|cat:(($C.notification_set) ? 'off' : 'on')|cat:';board='|cat:$C.current_board|cat:'.'|cat:$C.start|cat:';'|cat:$C.session_var|cat:'='|cat:$C.session_id)}">{$T.yes}</a> - <a href="{$C.board_href}">{$T.no}</a></strong>
    </p>
  </div>
</div>
