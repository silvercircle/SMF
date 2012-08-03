{function collapser}
{$state = $id|array_search:$C.collapsed_containers}
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
  {$imgsrc = $C.clip_image_src}
  {$ID = $message.id}
  {$t_href = $C.current_topic|cat:'.'|cat:$C.start}
  {$_s = $C.session_var|cat:'='|cat:$C.session_id}
  {if $message.can_approve}
    <li><a href="{$SCRIPTURL}?action=moderate;area=postmod;sa=approve;topic={$t_href};msg={$ID};{$_s}">
      <div class="csrcwrapper16px"><img class="clipsrc approve" src="{$imgsrc}" alt="{$T.approve}" title="{$T.approve}" /></div>
    </a></li>
  {/if}
  {if $C.can_quote}
    <li>
      <a rel="nofollow" onclick="return oQuickReply.quote({$ID});" href="{$SCRIPTURL}?action=post;quote={$ID};topic={$t_href};last_msg={$C.topic_last_message}">
        <div class="csrcwrapper16px"><img class="clipsrc reply" src="{$imgsrc}" alt="{$T.quote}" title="{$T.quote}" /></div>
      </a></li>
      <li id="mquote_{$ID}"><a rel="nofollow" href="javascript:void(0);" onclick="return oQuickReply.addForMultiQuote({$ID});">
        <div class="csrcwrapper16px"><img class="clipsrc mquote_add" src="{$imgsrc}" alt="{$T.add_mq}" title="{$T.add_mq}" /></div>
      </a></li>
  {/if}
  {if $message.can_modify}
    <li><a rel="nofollow" onclick="oQuickModify.modifyMsg({$ID});return(false);" href="{$SCRIPTURL}?action=post;msg={$ID};topic={$t_href}">
      <div class="csrcwrapper16px"><img class="clipsrc modify" src="{$imgsrc}" alt="{$T.modify}" title="{$T.modify}" /></div>
    </a></li>
  {/if}
  {if $message.can_remove}
    <li><a rel="nofollow" href="{$SCRIPTURL}?action=deletemsg;topic={$t_href};msg={$ID};{$_s}" onclick="return Eos_Confirm('', '{$T.remove_message}?', $(this).attr('href'));">
      <div class="csrcwrapper16px"><img class="clipsrc remove" src="{$imgsrc}" alt="{$T.remove}" title="{$T.remove}" /></div>
    </a></li>
  {/if}
  {if $message.can_unapprove}
    <li class="approve_button"><a href="{$SCRIPTURL}?action=moderate;area=postmod;sa=unapprove;topic={$t_href};msg={$ID};{$_s}">
      <div class="csrcwrapper16px"><img class="clipsrc unapprove" src="{$imgsrc}" alt="{$T.unapprove}" title="{$T.unapprove}" /></div>
    </a></li>
  {/if}
  {if $C.can_split and $C.real_num_replies}
    <li><a rel="nofollow" href="{$SCRIPTURL}?action=splittopics;topic={$C.current_topic}.0;at={$ID}">
      <div class="csrcwrapper16px"><img class="clipsrc split" src="{$imgsrc}" alt="{$T.split}" title="{$T.split}" /></div>
    </a></li>
  {/if}
  {if $C.can_restore_msg}
    <li><a rel="nofollow" href="{$SCRIPTURL}?action=restoretopic;msgs={$ID};{$_s}">{$T.restore_message}</a></li>
  {/if}
  {if $O.display_quick_mod and $message.can_remove}
    <li class="inline_mod_check" style="display: none;" id="in_topic_mod_check_{$ID}"></li>
  {/if}
  </ul>
  {if $C.can_report_moderator}
    <a href="{$SCRIPTURL}?action=reporttm;topic={$C.current_topic}.{$message.counter};msg={$ID}">
      <div class="csrcwrapper16px floatleft padded"><img class="clipsrc reporttm" src="{$imgsrc}" alt="{$T.report}" title="{$T.report}" /></div>
    </a>
  {/if}
  {if $C.can_issue_warning and $message.is_message_author == 0 and $message.member.is_guest == 0}
    <a href="{$SCRIPTURL}?action=profile;area=issuewarning;u={$message.member.id }};msg={$ID}">
      <div class="csrcwrapper16px floatleft padded"><img class="clipsrc warning" src="{$imgsrc}" alt="{$T.issue_warning}" title="{$T.issue_warning}" /></div>
    </a>
  {/if}
  {if $C.can_moderate_forum and !empty($message.member.ip)}
    <a href="{$SCRIPTURL}?action={($message.member.is_guest) ? 'trackip' : ('profile;area=tracking;sa=ip;u='|cat:$message.member.id|cat:';searchip='|cat:$message.member.ip)}">
      <div class="csrcwrapper16px floatleft padded"><img class="clipsrc network" src="{$imgsrc}" alt="{$message.member.ip}" title="{$message.member.ip}" /></div>
    </a>
  {elseif $message.can_see_ip}
    <a href="{$SCRIPTURL}?action=helpadmin;help=see_member_ip" onclick="return reqWin(this.href);" class="help">{$message.member.ip}</a>
  {/if}
  <div class="clear"></div>
{/function}
{function topicbit}
  {$imgsrc = $C.clip_image_src}
  {$color_class = ($C.alt_row) ? ' alternate' : ''}
  {$iconlegend = ''}
  {* Is this topic pending approval, or does it have any posts pending approval? *}
  {if $C.can_approve_posts and !empty($topic.unapproved_posts)}
    {$color_class = ' altbg'}
    {$iconlegend = $iconlegend|cat:('<div class="csrcwrapper16px floatleft"><img class="clipsrc unapproved" src="'|cat:$imgsrc|cat:'" alt="" title="'|cat:$T.awaiting_approval|cat:'" /></div>')}
  {elseif $topic.is_sticky or $topic.is_locked}
    {$color_class = ' altbg'}
  {/if}

  {if $topic.is_locked}
    {$iconlegend = $iconlegend|cat:('<div class="csrcwrapper16px floatleft"><img class="clipsrc locked" src="'|cat:$imgsrc|cat:'" alt="" title="'|cat:$T.locked_topic|cat:'" /></div>')}
  {/if}    
  {if $topic.is_sticky}
    {$iconlegend = $iconlegend|cat:('<div class="csrcwrapper16px floatleft hspaced"><img class="clipsrc sticky" src="'|cat:$imgsrc|cat:'" alt="" title="'|cat:$T.sticky_topic|cat:'" /></div>')}
  {/if}
  {if $topic.is_poll}
    {$iconlegend = $iconlegend|cat:('<div class="csrcwrapper16px floatleft hspaced"><img class="clipsrc poll" src="'|cat:$imgsrc|cat:'" alt="" title="'|cat:$T.poll|cat:'" /></div>')}
  {/if}
  {if $topic.is_posted_in}
    {$iconlegend = $iconlegend|cat:('<div class="csrcwrapper16px floatleft hspaced"><img class="clipsrc postedin" src="'|cat:$imgsrc|cat:'" alt="" title="'|cat:$T.participation_caption|cat:'" /></div>')}
  {/if}

  {if $topic.is_very_hot}
    {$iconlegend = $iconlegend|cat:('<div class="csrcwrapper16px floatleft hspaced"><img class="clipsrc veryhot" src="'|cat:$imgsrc|cat:'" alt="" title="'|cat:$C.very_hot_topic_message|cat:'" /></div>')}
  {elseif $topic.is_hot}
    {$iconlegend = $iconlegend|cat:('<div class="csrcwrapper16px floatleft hspaced"><img class="clipsrc hot" src="'|cat:$imgsrc|cat:'" alt="" title="'|cat:$C.hot_topic_message|cat:'" /></div>')}
  {/if}

  {if $topic.is_old}
    {$iconlegend = $iconlegend|cat:('<div class="csrcwrapper16px floatleft hspaced"><img class="clipsrc old" src="'|cat:$imgsrc|cat:'" alt="" title="'|cat:$C.old_topic_message|cat:'" /></div>')}
  {/if}
  <tr>
    <td class="icon1 topicrow{$color_class}">
    {if !empty($S.show_user_images) and $O.show_no_avatars == 0}
      <span class="small_avatar">
      {if !empty($topic.first_post.member.avatar)}
        {$topic.first_post.member.avatar}
      {else}
        <img src="{$S.images_url}/unknown.png" alt="avatar" />
      {/if}
      {if $topic.is_posted_in and $topic.first_post.member.id != $C.user.id and !empty($C.user.avatar.image)}
        <span class="avatar_overlay">{$C.user.avatar.image}</span>
      {/if}
      </span>
    {/if}
    {$is_new = $topic.new and $C.user.is_logged}
    </td>
    <td class="icon2 topicrow{$color_class}">
      <img src="{$topic.first_post.icon_url}" alt="" />
    </td>
    <td class="subject topicrow{$color_class}">
      <div {(!empty($topic.quick_mod.modify)) ? ('id="topic_'|cat:$topic.first_post.id|cat:'" ondblclick="modify_topic(\''|cat:$topic.id|cat:'\', \''|cat:$topic.first_post.id|cat:'\');"') : ''}>
      <span class="topiclink tpeek" data-id="{$topic.id}" id="msg_{$topic.first_post.id}">{$topic.prefix}{($is_new) ? '<strong>' : ''}{$topic.first_post.link}{($C.can_approve_posts and $topic.approved == 0) ? ('&nbsp;<em>('|cat:$T.awaiting_approval|cat:')</em>') : ''}{($is_new) ? '</strong>' : ''}</span>
      {if $is_new}
        <a href="{$topic.new_href}" id="newicon{$topic.first_post.id}"><img src="{$S.images_url}/new.png" alt="{$T.new}" /></a>
      {/if}
      <div class="floatright">
        <div class="floatright iconlegend_container" style="position:relative;top:-2px;opacity:0.4;">{$iconlegend}</div>
        {if !empty($topic.board.id)}
          <div class="tinytext" style="margin-top:16px;"><span class="lowcontrast">{$T.in} <a href="{$topic.board.href}">{$topic.board.name}</a></span></div>
        {/if}
      </div>
      <p>
        {$topic.first_post.member.link}, {$topic.first_post.time}
        <small id="pages{$topic.first_post.id}">{$topic.pages}</small>
      </p>
      </div>
    </td>
    <td class="stats nowrap topicrow{$color_class}">
      {if $topic.replies}
        <a rel="nofollow" title="{$T.who_posted}" onclick="whoPosted($(this));return(false);" class="whoposted" data-topic="{$topic.id}" href="{$SCRIPTURL}?action=xmlhttp;sa=whoposted;t={$topic.id}">{$topic.replies} {$T.replies}</a>
      {else}
        {$topic.replies} {$T.replies}
      {/if}
      <br />
      {$topic.views} {$T.views}
    </td>
    <td class="lastpost topicrow{$color_class}">
      {$T.by}: {$topic.last_post.member.link}<br />
      <a class="lp_link" title="{$T.last_post}" href="{$topic.last_post.href}">{$topic.last_post.time}</a>
    </td>
    {if !empty($C.can_quick_mod)}
      <td class="moderation topicrow{$color_class}" style="text-align:center;">
        {if $O.display_quick_mod}
          <input type="checkbox" name="topics[]" value="{$topic.id}" class="input_check cb_inline" />
        {/if}
      </td>
    {/if}
    </tr>
{/function}
