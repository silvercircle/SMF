<div class="modblock_{($C.alternate) ? 'left' : 'right'}">
<h1 class="bigheader section_header bordered"><a href="{$SCRIPTURL}?action=groups;sa=requests">{$T.mc_group_requests}</a></h1>
<div class="blue_container cleantop">
  <div class="content modbox">
    <ul class="reset">
    {foreach $C.group_requests as $request}
      <li class="smalltext">
        <a href="{$request.request_href}">{$request.group.name}</a> {$T.mc_groupr_by} {$request.member.link}
      </li>
    {/foreach}
    {if empty($C.group_requests)}
      <li>
        <strong class="smalltext">{$T.mc_group_requests_none}</strong>
      </li>
    {/if}
    </ul>
  </div>
</div>
</div>
{if !$C.alternate}
  <br class="clear" />
{/if}
{$C.alternate = !$C.alternate}
