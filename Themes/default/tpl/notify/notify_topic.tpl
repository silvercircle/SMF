<div class="cat_bar">
  <h3>
    {$T.notify}
  </h3>
</div>
<div class="blue_container cleantop norounded">
  <div class="content">
    {($C.notification_set) ? $T.notify_deactivate : $T.notify_request}
      <p>
        <strong><a href="{$SUPPORT->url_parse('?action=notify;sa='|cat:(($C.notification_set) ? 'off' : 'on')|cat:';topic='|cat:$C.current_topic|cat:'.'|cat:$C.start|cat:';'|cat:$C.session_var|cat:'='|cat:$C.session_id)}">{$T.yes}</a> - <a href="{$C.topic_href}">{$T.no}</a></strong>
      </p>
  </div>
</div>
