<script type="text/javascript" src="{$S.default_theme_url}/scripts/profile.js"></script>
{if $C.browser.is_chrome && !$C.user.is_owner}
  <script type="text/javascript"><!-- // --><![CDATA[
    disableAutoComplete();
  // ]]>
  </script>
{/if}
{if !empty($C.post_errors)}
  {include 'profile/error_message.tpl'}
{/if}
{if !empty($C.profile_updated)}
  <div class="windowbg" id="profile_success">
    {$C.profile_updated}
  </div>
{/if}
{$SUPPORT->displayHook('profile_above')}
{if !$C.user.is_owner}
<div class="blue_container">
	<div class="content">
    <span class="floatright righttext">
      {$C.member.group_stars}
      <br>
      <span class="tinytext">{$C.member.name} {$T.is}</span> <span class="member {($C.member.online.is_online) ? 'online' : 'offline'} tinytext"><strong>{$C.member.online.text}</strong></span>
      <br>
      {$parts = array()}
      {if !empty($C.can_send_pm) and !$C.user.is_owner}
        {$parts.pm = '<a rel="nofollow" href="'|cat:($SUPPORT->url_parse('?action=pm;sa=send;u='|cat:$C.member.id))|cat:'">PM</a>'}
          {*<img src="{$S.images_url}/icons/pm_read.png" alt="{$T.profileSendIm}" title="{$T.profileSendIm}">*}
      {/if}
      {if $C.member.show_email === 'yes' || $C.member.show_email === 'no_through_forum' || $C.member.show_email === 'yes_permission_override'}
        {$parts.email = '<a rel="nofollow" href="'|cat:$SUPPORT->url_parse('?action=emailuser;sa=email;uid='|cat:$C.member.id)|cat:'">'|cat:$T.email|cat:'</a>'}
      {/if}
      <span class="smalltext">{$T.contact_member} {$C.member.name}: {' | '|implode:$parts}</span>
    </span>
    {$real_primary_gid = (isset($C.member.orig_group_id)) ? $C.member.orig_group_id : $C.member.group_id}
    {$real_gid = (empty($real_primary_gid)) ? $C.member.post_group_id : $real_primary_gid}
    <h1 style="font-size:130%;"><span class="member group_{$real_gid}">{$C.member.name}</span></h1>
    <a href="{$SUPPORT->url_parse('?action=groups;sa=members;group='|cat:$real_gid)}">{(!empty($real_primary_gid)) ? $C.member.group : $C.member.post_group}</a>
    <div class="clear"></div>
  </div>
</div>
<div class="spacer_h"></div>
{/if}