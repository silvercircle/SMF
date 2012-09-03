<div id="send_topic">
  <form action="{$SCRIPTURL}?action=emailuser;sa=email" method="post" accept-charset="UTF-8">
  <h1 class="bigheader section_header bordered">
    {$C.page_title}
  </h1>
  <div class="blue_container cleantop norounded">
    <div class="content">
      <dl class="settings send_mail">
        <dt>
          <strong>{$T.sendtopic_receiver_name}:</strong>
        </dt>
        <dd>
          {$C.recipient.link}
        </dd>
        {if $C.can_view_receipient_email}
          <dt>
            <strong>{$T.sendtopic_receiver_email}:</strong>
          </dt>
          <dd>
            {$C.recipient.email_link}
          </dd>
        </dl>
        <hr>
        <dl class="settings send_mail">
        {/if}
        {if $C.user.is_guest}
          <dt>
            <label for="y_name"><strong>{$T.sendtopic_sender_name}:</strong></label>
          </dt>
          <dd>
            <input type="text" id="y_name" name="y_name" size="24" maxlength="40" value="{$C.user.name}" class="input_text" />
          </dd>
          <dt>
            <label for="y_email"><strong>{$T.sendtopic_sender_email}:</strong></label><br>
            <span class="smalltext">{$T.send_email_disclosed}</span>
          </dt>
          <dd>
            <input type="text" id="y_mail" name="y_email" size="24" maxlength="50" value="{$C.user.email}" class="input_text" />
          </dt>
        {else}
          <dt>
            <strong>{$T.sendtopic_sender_email}:</strong><br>
            <span class="smalltext">{$T.send_email_disclosed}</span>
          </dt>
          <dd>
            <em>{$C.user.email}</em>
          </dd>
        {/if}
        <dt>
          <label for="email_subject"><strong>{$T.send_email_subject}:</strong></label>
        </dt>
        <dd>
          <input type="text" id="email_subject" name="email_subject" size="50" maxlength="100" class="input_text" />
        </dd>
        <dt>
          <label for="email_body"><strong>{$T.message}:</strong></label>
        </dt>
        <dd>
          <textarea id="email_body" name="email_body" rows="10" cols="20" style="{($C.browser.is_ie8) ? 'width: 635px; max-width: 90%; min-width: 90%' : 'width: 90%'};"></textarea>
        </dd>
      </dl>
      <div class="righttext mediumpadding">
        <input type="submit" name="send" value="{$T.sendtopic_send}" class="default" />
      </div>
    </div>
  </div>
  {foreach $C.form_hidden_vars as $key => $value}
    <input type="hidden" name="{$key}" value="{$value}" />
  {/foreach}
  {$C.hidden_sid_input}
  </form>
</div>
<br class="clear">
