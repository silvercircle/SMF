<br>
<div id="poll">
  <div class="cat_bar">
    <h3>
      <span class="floatleft"><img src="{$S.images_url}/topic/{($C.poll.is_locked) ? 'normal_poll_locked' : 'normal_poll'}.gif" alt="" class="icon" />{$T.poll}</span>
    </h3>
  </div>
  <div class="blue_container cleantop">
    <div class="content" id="poll_options">
      <h1 class="bigheader secondary" id="pollquestion">
        {$C.poll.question}
      </h1>
      {* Are they not allowed to vote but allowed to view the options? *}
      {if $C.poll.show_results or $C.allow_vote == 0}
        <dl class="options">
        {* Show each option with its corresponding percentage bar. *}
        {foreach from=$C.poll.options item=option}
          <dt class="smalltext{($option.voted_this) ? ' voted' : ''}">{$option.option}</dt>
          <dd class="smalltext statsbar{($option.voted_this) ? ' voted' : ''}">
          {if $C.allow_poll_view}
            {$option.bar_ndt}
            <span class="percentage">{$option.votes} ({$option.percent}%)</span>
          {/if}
          </dd>
        {/foreach}
        </dl>
        {if $C.allow_poll_view}
          <p><strong>{$T.poll_total_voters}:</strong> {$C.poll.total_votes}</p>
        {/if}
      {else} {* show_results *}
        <form action="{$SCRIPTURL}?action=vote;topic={$topic}.{$C.start};poll={$C.poll.id}" method="post" accept-charset="UTF-8">
        {if $C.poll.allowed_warning}
          <p class="smallpadding">{$C.poll.allowed_warning}</p>
        {/if}
        <ul class="reset options">
        {foreach from=$C.poll.options item=option}
          <li class="smalltext">{$option.vote_button} <label for="{$option.id}">{$option.option}</label></li>
        {/foreach}
        </ul>
        <div class="submitbutton">
          <input type="submit" value="{$T.poll_vote}" class="default" />
          <input type="hidden" name="{$C.session_var}" value="{$C.session_id}" />
        </div>
        </form>
      {/if} {* show_results *}
      {if !empty($C.poll.expire_time)}
        <p><strong>{($C.poll.is_expired) ? $T.poll_expired_on : $T.poll_expires_on}:</strong> {$C.poll.expire_time}</p>
      {/if}
    </div>
  </div>
</div>
<div id="pollmoderation mediumpadding">
  {$SUPPORT->button_strip($C.poll_buttons)}
</div>
<div class="clear"></div>