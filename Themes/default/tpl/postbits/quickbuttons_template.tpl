{$imgsrc = $C.clip_image_src}
<li>
  <a href="{$SCRIPTURL}?action=moderate;area=postmod;sa=approve;topic=%TOPIC%;msg=%MSG%;%SID%">
    <div class="csrcwrapper16px"><img class="clipsrc approve" src="{$imgsrc}" alt="{$T.approve}" title="{$T.approve}" /></div>
  </a>
</li>
||@@||
<li>
  <a rel="nofollow" onclick="return oQuickReply.quote(%MSG%);" href="{$SCRIPTURL}?action=post;quote=%MSG%;topic=%TOPIC%;last_msg=%LASTMSG%">
    <div class="csrcwrapper16px"><img class="clipsrc reply" src="{$imgsrc}" alt="{$T.quote}" title="{$T.quote}" /></div>
  </a>
</li>
<li id="mquote_%MSG%">
  <a rel="nofollow" href="javascript:void(0);" onclick="return oQuickReply.addForMultiQuote(%MSG%);">
    <div class="csrcwrapper16px"><img class="clipsrc mquote_add" src="{$imgsrc}" alt="{$T.add_mq}" title="{$T.add_mq}" /></div>
  </a>
</li>
||@@||
<li>
  <a rel="nofollow" onclick="oQuickModify.modifyMsg(%MSG%);return(false);" href="{$SCRIPTURL}?action=post;msg=%MSG%;topic=%TOPIC%">
    <div class="csrcwrapper16px"><img class="clipsrc modify" src="{$imgsrc}" alt="{$T.modify}" title="{$T.modify}" /></div>
  </a>
</li>
||@@||
<li>
  <a rel="nofollow" href="{$SCRIPTURL}?action=deletemsg;topic=%TOPIC%;msg=%MSG%;%SID%" onclick="return Eos_Confirm('', '{$T.remove_message}?', $(this).attr('href'));">
    <div class="csrcwrapper16px"><img class="clipsrc remove" src="{$imgsrc}" alt="{$T.remove}" title="{$T.remove}" /></div>
  </a>
</li>
||@@||
<li class="approve_button">
  <a href="{$SCRIPTURL}?action=moderate;area=postmod;sa=unapprove;topic=%TOPIC%;msg=%MSG%;%SID%">
    <div class="csrcwrapper16px"><img class="clipsrc unapprove" src="{$imgsrc}" alt="{$T.unapprove}" title="{$T.unapprove}" /></div>
  </a>
</li>
||@@||
<li>
  <a rel="nofollow" href="{$SCRIPTURL}?action=splittopics;topic=%TOPIC_RAW%.0;at=%MSG%">
    <div class="csrcwrapper16px"><img class="clipsrc split" src="{$imgsrc}" alt="{$T.split}" title="{$T.split}" /></div>
  </a>
</li>
||@@||
<li>
  <a rel="nofollow" href="{$SCRIPTURL}?action=restoretopic;msgs=%MSG%;%SID%">{$T.restore_message}</a>
</li>
||@@||
<li class="inline_mod_check" style="display: none;" id="in_topic_mod_check_%MSG%"></li>
||@@||
<a href="{$SCRIPTURL}?action=reporttm;topic=%TOPIC_RAW%.%MSG_COUNTER%;msg=%MSG">
  <div class="csrcwrapper16px floatleft padded"><img class="clipsrc reporttm" src="{$imgsrc}" alt="{$T.report}" title="{$T.report}" /></div>
</a>
||@@||
<a href="{$SCRIPTURL}?action=profile;area=issuewarning;u=%MEMBER%;msg=%MSG">
  <div class="csrcwrapper16px floatleft padded"><img class="clipsrc warning" src="{$imgsrc}" alt="{$T.issue_warning}" title="{$T.issue_warning}" /></div>
</a>
