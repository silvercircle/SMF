<div id="send_topic">
  <form action="{$SCRIPTURL}?action=emailuser;sa=sendtopic;topic={$C.current_topic}.{$C.start}" method="post" accept-charset="UTF-8">
    <h1 class="bigheader section_header bordered">
      {$C.page_title}
    </h1>
    <div class="blue_container cleantop norounded">
      <div class="content">
        <fieldset id="sender" class="send_topic">
          <dl class="settings send_topic">
            <dt>
              <label for="y_name"><strong>{$T.sendtopic_sender_name}:</strong></label>
            </dt>
            <dd>
              <input type="text" id="y_name" name="y_name" size="30" maxlength="40" value="{$C.user.name}" class="input_text" />
            </dd>
            <dt>
              <label for="y_email"><strong>{$T.sendtopic_sender_email}:</strong></label>
            </dt>
            <dd>
              <input type="text" id="y_email" name="y_email" size="30" maxlength="50" value="{$C.user.email}" class="input_text" />
            </dd>
            <dt>
              <label for="comment"><strong>{$T.sendtopic_comment}:</strong></label>
            </dt>
            <dd>
              <input type="text" id="comment" name="comment" size="30" maxlength="100" class="input_text" />
            </dd>
          </dl>
        </fieldset>
        <fieldset id="recipient" class="send_topic">
          <dl class="settings send_topic">
            <dt>
              <label for="r_name"><strong>{$T.sendtopic_receiver_name}:</strong></label>
            </dt>
            <dd>
              <input type="text" id="r_name" name="r_name" size="30" maxlength="40" class="input_text" />
            </dd>
            <dt>
              <label for="r_email"><strong>{$T.sendtopic_receiver_email}:</strong></label>
            </dt>
            <dd>
              <input type="text" id="r_email" name="r_email" size="30" maxlength="50" class="input_text" />
            </dd>
          </dl>
        </fieldset>
        <div class="righttext mediumpadding">
          <input type="submit" name="send" value="{$T.sendtopic_send}" class="default" />
        </div>
      </div>
    </div>
    {$C.hidden_sid_input}
  </form>
</div>
