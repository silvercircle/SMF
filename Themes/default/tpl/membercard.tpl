{extends "base_xml.tpl"}
{block xml_content}
<response open="default_overlay" width="500px" offset="-150" />
  <content>
  <![CDATA[ <!-- > -->
    {if !isset($C.member)}
      <div class="orange_container largepadding">
        {$T.no_access}
      </div>
    {else}
      {$member = $C.member}
      <div class="title_bar" style="padding-left:100px;">
      </div>
      <table style="border:0;width:100%;">
        <tr>
          <td style="vertical-align:top;text-align:center;">
            <div style="position:relative;top:-26px;margin-bottom:-26px;">
            {if !empty($member.avatar.image)}
              {$member.avatar.image}
            {else}
              <img class="avatar" src="{$S.images_url}/unknown.png" alt="avatar" />
            {/if}
            </div>
            {$member.group_stars}
            <br />
            {$url = "?action=profile;area=showposts;u={$member.id}"}
            {$T.posts}: <a class="important" href="{$SUPPORT->url_parse($url)}">{$member.posts}</a>
          </td>
          <td style="width:100%;vertical-align:top;" class="smallpadding">
            <h1 style="position:relative;top:-28px;margin-bottom:-16px;">
              {$member.name}
            </h1>
            {if !empty($member.blurb)}
              <div class="orange_container" style="padding:3px;margin-bottom:3px;"><strong>{$member.blurb}</strong></div>
            {/if}
            <div class="blue_container inset_shadow smallpadding" style="margin-bottom:5px;">
              {if !empty($member.group)}
                {$T.primary_membergroup}: <strong>{$member.group}</strong><br />
              {/if}
              {if !empty($member.post_group)}
                {$T.additional_membergroups}: <strong>{$member.post_group}</strong><br /><br />
              {/if}
              {if !empty($member.loc)}
                {", "|implode:$member.loc}<br />
              {/if}
              {$T.date_registered}: {$member.registered}<br />
              <br />
              {if !empty($M.karmaMode)}
                {$T.like_profile_report|sprintf:$member.name:$SUPPORT->url_parse("?action=profile;area=showposts;sa=likesout;u={$member.id}"):$member.likesgiven:$SUPPORT->url_parse("?action=profile;area=showposts;sa=likes;u={$member.id}"):$member.liked:$SUPPORT->url_parse("?action=profile;area=showposts;u={$member.id}"):$member.posts}
              {/if}
              <br />
              <br />
              {if !empty($member.online.is_online)}
                <strong style="color:green;">{$T.online}</strong>
              {else}
                {$T.lastLoggedIn}: {$member.last_login}
              {/if}
            </div>
          </td>
        </tr>
      </table>
      <div class="glass">
        {$url = "?action=profile;u="|cat:$member.id}
        <div class="floatright">
          <a href="{$SUPPORT->url_parse($url)}">{$T.view_full_profile}</a>
        </div>
        {if $C.can_send_pm}
          <a href="{$C.pm_contact_link}"><img src="{$S.images_url}/icons/pm_read.png" alt="*" title="{$T.profileSendIm}" /></a>
        {/if}
        <div class="clear"></div>
      </div>
    {/if}
  ]]>
  </content>
{/block}
