{function collapser}
{$state = (isset($_COOKIE.SF_collapsed)) ? ($id|array_search:$collapsed_containers) : false}
{$headerclass = 'cContainer_header'}
{$headerstyle = ''}
<div class="{$headerclass}" {$headerstyle}>
  <div class="csrcwrapper16px floatright"><img onclick="cContainer($(this));" class="cContainer_c clipsrc {($state) ? '_expand' : '_collapse'}" id="{$id}" src="{$S.images_url}/clipsrc.png" alt="*" /></div>
    <h3>{$title}</h3>
  </div>
  {$bodyclass = 'cContainer_body'}
  {$bodystyle = ''}
  <div id="{$id}_body" class="{$bodyclass}" {$bodystyle}>
  <script>
  // <![CDATA[

    $("#{$id}_body").css("display", "{($state) ? 'none' : 'normal'}");

  // ]]>  
  </script>
{/function}
{function quickbuttons}
  <ul class="floatright plainbuttonlist">
  {$imgsrc = $S.images_url|cat:'/clipsrc.png'}
  {$ID = $m.id}
  {if $m.can_approve}
    <li><a href="{$SCRIPTURL}?action=moderate;area=postmod;sa=approve;topic={$C.current_topic}.{$C.start};msg={$ID};{$C.session_var}={$C.session_id}">
      <div class="csrcwrapper16px"><img class="clipsrc approve" src="{$imgsrc}" alt="{$T.approve}" title="{$T.approve}" /></div>
    </a></li>
  {/if}
  {if $C.can_quote}
    <li>
      <a rel="nofollow" onclick="return oQuickReply.quote({$ID});" href="{$SCRIPTURL}?action=post;quote={$ID};topic={$C.current_topic}.{$C.start};last_msg={$C.topic_last_message}">
        <div class="csrcwrapper16px"><img class="clipsrc reply" src="{$imgsrc}" alt="{$T.quote}" title="{$T.quote}" /></div>
      </a></li>
      <li id="mquote_{$ID}"><a rel="nofollow" href="javascript:void(0);" onclick="return oQuickReply.addForMultiQuote({$ID});">
        <div class="csrcwrapper16px"><img class="clipsrc mquote_add" src="{$imgsrc}" alt="{$T.add_mq}" title="{$T.add_mq}" /></div>
      </a></li>
  {/if}
  {if $m.can_modify}
    <li><a rel="nofollow" onclick="oQuickModify.modifyMsg({$ID});return(false);" href="{$SCRIPTURL}?action=post;msg={$ID};topic={$C.current_topic}.{$C.start}">
      <div class="csrcwrapper16px"><img class="clipsrc modify" src="{$imgsrc}" alt="{$T.modify}" title="{$T.modify}" /></div>
    </a></li>
  {/if}
  {if $m.can_remove}
    <li><a rel="nofollow" href="{$SCRIPTURL}?action=deletemsg;topic={$C.current_topic}.{$C.start};msg={$ID};{$C.session_var}={$C.session_id}" onclick="return Eos_Confirm('', \'{$T.remove_message}?\', $(this).attr('href'));">
      <div class="csrcwrapper16px"><img class="clipsrc remove" src="{$imgsrc}" alt="{$T.remove}" title="{$T.remove}" /></div>
    </a></li>
  {/if}
  {if $m.can_unapprove}
    <li class="approve_button"><a href="{$SCRIPTURL}?action=moderate;area=postmod;sa=unapprove;topic={$C.current_topic}.{$C.start};msg={$ID};{$C.session_var}={$C.session_id}">
      <div class="csrcwrapper16px"><img class="clipsrc unapprove" src="{$imgsrc}" alt="{$T.unapprove}" title="{$T.unapprove}" /></div>
    </a></li>
  {/if}
  {if $C.can_split and $C.real_num_replies}
    <li><a rel="nofollow" href="{$SCRIPTURL}?action=splittopics;topic={$C.current_topic }}.0;at={$ID}">
      <div class="csrcwrapper16px"><img class="clipsrc split" src="{$imgsrc}" alt="{$T.split}" title="{$T.split}" /></div>
    </a></li>
  {/if}
  {if $C.can_restore_msg}
    <li><a rel="nofollow" href="{$SCRIPTURL}?action=restoretopic;msgs={$ID};{$C.session_var}={$C.session_id}">{$T.restore_message}</a></li>
  {/if}
  {if $O.display_quick_mod and $m.can_remove}
    <li class="inline_mod_check" style="display: none;" id="in_topic_mod_check_{$ID}"></li>
  {/if}
  </ul>
  {if $C.can_report_moderator}
    <a href="{$SCRIPTURL}?action=reporttm;topic={$C.current_topic}.{$m.counter};msg={$ID}">
      <div class="csrcwrapper16px floatleft padded"><img class="clipsrc reporttm" src="{$imgsrc}" alt="{$T.report}" title="{$T.report}" /></div>
    </a>
  {/if}
  {if $C.can_issue_warning and $m.is_message_author == 0 and $m.member.is_guest == 0}
    <a href="{$SCRIPTURL}?action=profile;area=issuewarning;u={$m.member.id }};msg={$ID}">
      <div class="csrcwrapper16px floatleft padded"><img class="clipsrc warning" src="{$imgsrc}" alt="{$T.issue_warning}" title="{$T.issue_warning}" /></div>
    </a>
  {/if}
  {if $C.can_moderate_forum and !empty($m.member.ip)}
    <a href="{$SCRIPTURL}?action={($message.member.is_guest) ? 'trackip' : ('profile;area=tracking;sa=ip;u='|cat:$m.member.id|cat:';searchip='|cat:$m.member.ip)}">
      <div class="csrcwrapper16px floatleft padded"><img class="clipsrc network" src="{$imgsrc}" alt="{$m.member.ip}" title="{$m.member.ip}" /></div>
    </a>
  {elseif $m.can_see_ip}
    <a href="{$SCRIPTURL}?action=helpadmin;help=see_member_ip" onclick="return reqWin(this.href);" class="help">{$m.member.ip}</a>
  {/if}
  <div class="clear"></div>
{/function}