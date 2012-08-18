{include "generics/list.tpl"}
<h1 class="bigheader section_header bordered">{$T.view_ips_by} {$C.member.name}</h1>
  <div id="tracking" class="blue_container cleantop">
    <div class="content">
      <dl>
        <dt>{$T.most_recent_ip}:
          {(empty($C.last_ip2)) ? '' : ('<br><span class="smalltext">(<a href="{$SCRIPTURL}?action=helpadmin;help=whytwoip" onclick="return reqWin(this.href);">'|cat:$T.why_two_ip_address|cat:'</a>)</span>')}
        </dt>
        <dd>
          <a href="{$SCRIPTURL}?action=profile;area=tracking;sa=ip;searchip={$C.last_ip};u={$C.member.id}">{$C.last_ip}</a>
          {if !empty($C.last_ip2)}
            <a href="{$SCRIPTURL}?action=profile;area=tracking;sa=ip;searchip={$C.last_ip2};u={$C.member.id}">{$C.last_ip2}</a>
          {/if}
        </dd>
        <dt>{$T.ips_in_messages}:</dt>
        <dd class="tinytext">
          {(count($C.ips) > 0) ? (', '|implode:$C.ips) : ('('|cat:$T.none|cat:')')}
        </dd>
        <dt>{$T.ips_in_errors}:</dt>
        <dd class="tinytext">
          {(count($C.error_ips) > 0) ? (', '|implode:$C.error_ips) : ('('|cat:$T.none|cat:')')}
        </dd>
        <dt>{$T.members_in_range}:</dt>
        <dd class="tinytext">
          {(count($C.members_in_range) > 0) ? (', '|implode:$C.members_in_range) : ('('|cat:$T.none|cat:')')}
        </dd>
      </dl>
    </div>
  </div>
  <br>
  {call show_list list_id='track_user_list'}

