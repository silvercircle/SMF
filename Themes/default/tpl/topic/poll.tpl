{* add a poll *}
{extends "base.tpl"}
{block content}
{$C.poll_script}
<div id="edit_poll">
<br>
  <form action="{$SCRIPTURL}?action=editpoll2{($C.is_edit) ? '' : ';add'};topic={$C.current_topic}.{$C.start}" method="post" accept-charset="UTF-8" onsubmit="submitonce(this); smc_saveEntities('postmodify', ['question'], 'options-');" name="postmodify" id="postmodify">
    <div class="cat_bar">
      <h3 class="catbg">{$C.page_title}</h3>
    </div>
    {if !empty($C.poll_error.messages)}
      <div class="errorbox">
        <dl class="poll_error">
          <dt>
            {($C.is_edit) ? $T.error_while_editing_poll : $T.error_while_adding_poll}:
          </dt>
          <dt>
            {(empty($C.poll_error.messages)) ? '' : '<br />'|implode:$C.poll_error.messages}
          </dt>
        </dl>
      </div>
    {/if}
    <div>
      <div class="blue_container">
        <input type="hidden" name="poll" value="{$C.poll.id}" />
          <fieldset id="poll_main">
            <legend><span {(isset($C.poll_error.no_question)) ? ' class="error"' : ''}>{$T.poll_question}:</span></legend>
            <input type="text" name="question" size="80" value="{$C.poll.question}" class="input_text" />
            <ul class="poll_main">
            {foreach $C.choices as $choice}
              <li>
                <label for="options-{$choice.id}" {(isset($C.poll_error.poll_few)) ? ' class="error"' : ''}>{$T.option} {$choice.number}</label>:
                <input type="text" name="options[{$choice.id}]" id="options-{$choice.id}" value="{$choice.label}" class="input_text" size="80" maxlength="255" />
                {if $choice.votes != -1}
                  ({$choice.votes} {$T.votes})
                {/if}
              </li>
            {/foreach}
            <li id="pollMoreOptions"></li>
            </ul>
            <strong><a href="javascript:addPollOption(); void(0);">({$T.poll_add_option})</a></strong>
          </fieldset>
          <fieldset id="poll_options">
            <legend>{$T.poll_options}:</legend>
            <dl class="settings poll_options">
            {if $C.can_moderate_poll}
              <dt>
                <label for="poll_max_votes">{$T.poll_max_votes}:</label>
              </dt>
              <dd>
                <input type="text" name="poll_max_votes" id="poll_max_votes" size="2" value="{$C.poll.max_votes}" class="input_text" />
              </dd>
              <dt>
                <label for="poll_expire">{$T.poll_run}:</label><br>
                <em class="smalltext">{$T.poll_run_limit}</em>
              </dt>
              <dd>
                <input type="text" name="poll_expire" id="poll_expire" size="2" value="{$C.poll.expiration}" onchange="this.form.poll_hide[2].disabled = isEmptyText(this) || this.value == 0; if (this.form.poll_hide[2].checked) this.form.poll_hide[1].checked = true;" maxlength="4" class="input_text" /> {$T.days_word}
              </dd>
              <dt>
                <label for="poll_change_vote">{$T.poll_do_change_vote}:</label>
              </dt>
              <dd>
                <input type="checkbox" id="poll_change_vote" name="poll_change_vote"{(!empty($C.poll.change_vote)) ? ' checked="checked"' : ''} class="input_check" />
              </dd>
              {if $C.poll.guest_vote_allowed}
                <dt>
                  <label for="poll_guest_vote">{$T.poll_guest_vote}:</label>
                </dt>
                <dd>
                  <input type="checkbox" id="poll_guest_vote" name="poll_guest_vote"{(!empty($C.poll.guest_vote)) ? ' checked="checked"' : ''} class="input_check" />
                </dd>
              {/if}
            {/if}
            <dt>
              {$T.poll_results_visibility}:
            </dt>
              <dd>
                <input type="radio" name="poll_hide" id="poll_results_anyone" value="0"{($C.poll.hide_results == 0) ? ' checked="checked"' : ''} class="input_radio" /> <label for="poll_results_anyone">{$T.poll_results_anyone}</label><br>
                <input type="radio" name="poll_hide" id="poll_results_voted" value="1"{($C.poll.hide_results == 1) ? ' checked="checked"' : ''} class="input_radio" /> <label for="poll_results_voted">{$T.poll_results_voted}</label><br>
                <input type="radio" name="poll_hide" id="poll_results_expire" value="2"{($C.poll.hide_results == 2) ? ' checked="checked"' : ''} {(empty($C.poll.expiration)) ? 'disabled="disabled"' : ''} class="input_radio" /> <label for="poll_results_expire">{$T.poll_results_after}</label>
              </dd>
            </dl>
          </fieldset>
          {if $C.is_edit}
            <fieldset id="poll_reset">
              <legend>{$T.reset_votes}</legend>
              <input type="checkbox" name="resetVoteCount" value="on" class="input_check" /> {$T.reset_votes_check}
            </fieldset>
          {/if}
          <div class="righttext padding">
            <input type="submit" name="post" value="{$T.save}" onclick="return submitThisOnce(this);" accesskey="s" class="button_submit" />
          </div>
        </div>
      </div>
      <input type="hidden" name="seqnum" value="{$C.form_sequence_number}" />
      <input type="hidden" name="{$C.session_var}" value="{$C.session_id}" />
    </form>
  </div>
  <br class="clear">
{/block}