<div id="report_topic">
  <form action="{$SCRIPTURL}?action=reporttm;topic={$C.current_topic}.{$C.start}" method="post" accept-charset="UTF-8">
    <input type="hidden" name="msg" value="{$C.message_id}" />
    <div class="cat_bar">
      <h3>{$T.report_to_mod}</h3>
    </div>
    <div class="blue_container cleantop inset_shadow">
      <div class="content">
      {if !empty($C.post_errors)}
        <div class="errorbox">
          <ul>
          {foreach $C.post_errors as $error}
            <li class="error">{$error}</li>
          {/foreach}
          </ul>
        </div>
      {/if}
      <p>{$T.report_to_mod_func}</p>
      <br>
      <dl class="settings" id="report_post">
      {if $C.user.is_guest}
        <dt>
          <label for="email_address">{$T.email}</label>:
        </dt>
        <dd>
          <input type="text" id="email_address" name="email" value="{$C.email_address}" size="25" maxlength="255" />
        </dd>
      {/if}
      <dt>
        <label for="report_comment">{$T.enter_comment}</label>:
      </dt>
      <dd>
        <input type="text" id="report_comment" name="comment" size="50" value="{$C.comment_body}" maxlength="255" />
      </dd>
      {if $C.require_verification}
        <dt>
          {$T.verification}:
        </dt>
        <dd>
          {$SUPPORT->template_control_verification($C.visual_verification_id, 'all')}
        </dd>
      {/if}
      </dl>
      <div class="righttext mediumpadding">
        <input type="submit" name="submit" value="{$T.rtm10}" style="margin-left: 1ex;" class="default" />
      </div>
    </div>
  </div>
  {$C.hidden_sid_input}
  </form>
</div>