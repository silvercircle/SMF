{extends "modcenter/modcenter_base.tpl"}
{block modcenter_content}
{include "generics/list.tpl"}
<div id="modcenter">
  <form action="{$SCRIPTURL}?action=moderate;area=reports;report={$C.report.id}" method="post" accept-charset="UTF-8">
    <div class="cat_bar">
      <h3>
        {$T.mc_viewmodreport|sprintf:$C.report.message_link:$C.report.author.link}
      </h3>
    </div>
      <div class="blue_container cleantop norounded mediumpadding">
          <span class="floatright">
            <a href="{$SCRIPTURL}?action=moderate;area=reports;ignore={!$C.report.ignore};rid={$C.report.id};{$C.session_var}={$C.session_id}" {(!$C.report.ignore) ? ('onclick="return confirm(\''|cat:$T.mc_reportedp_ignore_confirm|cat:'\');"') : ''}>{($C.report.ignore) ? $C.unignore_button : $C.ignore_button}</a>
            <a href="{$SCRIPTURL}?action=moderate;area=reports;close={!$C.report.closed|intval};rid={$C.report.id};{$C.session_var}={$C.session_id}">{$C.close_button}</a>
          </span>
          <span style="line-height:20px;">{$T.mc_modreport_summary|sprintf:$C.report.num_reports:$C.report.last_updated}</span>
        <div class="clear"></div>
      </div>
      <br>
      <div class="post_wrapper">
        <div class="content">
          {$C.report.body}
        </div>
      </div>
      <br>
      <div class="cat_bar">
        <h3>{$T.mc_modreport_whoreported_title}</h3>
      </div>
      <div class="blue_container cleantop">
      <div class="content">
      <ol class="commonlist" style="margin-bottom:0;">
      {foreach $C.report.comments as $comment}
          <li>
            <div class="smalltext">
              {$T.mc_modreport_whoreported_data|sprintf:($comment.member.link|cat:((empty($comment.member.id) and !empty($comment.member.ip)) ? (' ('|cat:$comment.member.ip|cat:')') : '')):$comment.time}
              {$comment.message}
            </div>
          </li>
      {/foreach}
      </ol>
      </div>
      </div>
      <br>
      <div class="cat_bar">
        <h3>{$T.mc_modreport_mod_comments}</h3>
      </div>
      <div class="blue_container">
        <div class="content">
          {if empty($C.report.mod_comments)}
            <p class="centertext">{$T.mc_modreport_no_mod_comment}</p>
          {/if}
          {foreach $C.report.mod_comments as $comment}
            <p>{$comment.member.link}: {$comment.message} <em class="smalltext">({$comment.time})</em></p>
          {/foreach}
          <textarea rows="2" cols="60" style="{($C.browser.is_ie8) ? 'width: 635px; max-width: 60%; min-width: 60%' : 'width: 60%'};" name="mod_comment"></textarea>
          <div class="clear">
            <input type="submit" name="add_comment" value="{$T.mc_modreport_add_mod_comment}" class="button_submit floatright" />
          </div>
          <div class="clear"></div>
        </div>
      </div>
      <br>
      {$alt = false}
      {call show_list list_id='moderation_actions_list'}
      {if !empty($C.entries)}
        <div class="cat_bar">
          <h3>{$T.mc_modreport_modactions}</h3>
        </div>
          <table width="100%" class="table_grid">
            <thead>
              <tr class="catbg">
                <th>{$T.modlog_action}</th>
                <th>{$T.modlog_date}</th>
                <th>{$T.modlog_member}</th>
                <th>{$T.modlog_position}</th>
                <th>{$T.modlog_ip}</th>
              </tr>
            </thead>
            <tbody>
        {foreach $C.entries as $entry}
          <tr class="{($alt) ? 'windowbg2' : 'windowbg'}">
            <td>{$entry.action}</td>
            <td>{$entry.time}</td>
            <td>{$entry.moderator.link}</td>
            <td>{$entry.position}</td>
            <td>{$entry.ip}</td>
          </tr>
          <tr>
            <td colspan="5" class="{($alt) ? 'windowbg2' : 'windowbg'}">
            {foreach $entry.extra as $key => $value}
              <em>{$key}</em>: {$value}
            {/foreach}
            </td>
          </tr>
        {/foreach}
          </tbody>
        </table>
      {/if}
      <input type="hidden" name="{$C.session_var}" value="{$C.session_id}" />
    </form>
  </div>
  <br class="clear" />
{/block}