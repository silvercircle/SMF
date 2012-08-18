{include "generics/list.tpl"}
<h1 class="bigheader section_header bordered">{$T.trackIP}</h1>
<form action="{$C.base_url}" method="post" accept-charset="UTF-8">
	<div class="blue_container mediumpadding cleantop">
		<div class="content">{$T.enter_ip}:&nbsp;&nbsp;<input type="text" name="searchip" value="{$C.ip}" class="input_text" />&nbsp;&nbsp;<input type="submit" value="{$T.trackIP}" class="default" /></div>
	</div>
</form>
<br>
{if $C.single_ip}
	<h1 class="bigheader section_header bordered">{$T.whois_title} {$C.ip}</h1>
	<div class="blue_container cleantop">
		<div class="content smalltext">
		{foreach $C.whois_servers as $server}
			<a href="{$server.url}" class="new_win"{(isset($C.auto_whois_server) and $C.auto_whois_server.name == $server.name) ? ' style="font-weight: bold;"' : ''}>{$server.name}</a><br>
		{/foreach}
		</div>
	</div>
	<br>
{/if}
<h1 class="bigheader section_header">{$T.members_from_ip} {$C.ip}</h1>
{if empty($C.ips)}
	<div class="orange_container cleantop smallpadding smalltext">
		<em>{$T.no_members_from_ip}</em>
	</div>
{else}
	<table class="table_grid" style="width:100%;">
		<thead>
			<tr>
				<th class="first_th glass" scope="col">{$T.ip_address}</th>
				<th class="last_th glass" scope="col">{$T.display_name}</th>
			</tr>
		</thead>
		<tbody>
		{$alternate = false}
		{foreach $C.ips as $ip => $memberlist}
			<tr class="tablerow{($alternate) ? ' alternate' : ''}">
				<td><a href="{$C.base_url};searchip={$ip}">{$ip}</a></td>
				<td>{', '|implode:$memberlist}</td>
			</tr>
			{$alternate = !$alternate}
		{/foreach}
		</tbody>
	</table>
	<br>
{/if}
{call show_list list_id='track_message_list'}
<br>
{call show_list list_id='track_user_list'}
