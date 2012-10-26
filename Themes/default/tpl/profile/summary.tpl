{*
 * @name      EosAlpha BBS
 * @copyright 2011 Alex Vie silvercircle(AT)gmail(DOT)com
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright: 2011 Simple Machines (http://www.simplemachines.org)
 * license:   BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version 1.0pre
 * 
 * profile summary template
 *}
<div id="profileview">
	<div id="detailedinfo">
		<h1 class="bigheader secondary title indent bordered">
			{$T.member_about} {($C.user.is_owner) ? $T.you_member : $C.member.name}
		</h1>
		<div class="blue_container cleantop">
			<div class="content mediumpadding">
				<dl>
				{if $C.user.is_owner || $C.user.is_admin}
					<dt>{$T.username}</dt>
					<dd>{$C.member.username}</dd>
				{/if}
				{if !isset($C.disabled_fields.posts)}
					<dt>{$T.profile_posts}</dt>
					<dd>{$C.member.posts} ({$C.member.posts_per_day} {$T.posts_per_day})</dd>
				{/if}
				{if $C.member.show_email == 'yes'}
					<dt>{$T.email}: </dt>
					<dd><a href="{$SCRIPTURL}?action=emailuser;sa=email;uid={$C.member.id}">{$C.member.email}</a></dd>
				{elseif $C.member.show_email == 'yes_permission_override'}
					<dt>{$T.email}: </dt>
					<dd><em><a href="{$SCRIPTURL}?action=emailuser;sa=email;uid={$C.member.id}">{$C.member.email}</a></em></dd>
				{/if}
				{if !empty($M.titlesEnable) && !empty($C.member.title)}
					<dt>{$T.custom_title}: </dt>
					<dd>{$C.member.title}</dd>
				{/if}
				{if !empty($C.member.blurb)}
					<dt>{$T.personal_text}: </dt>
					<dd>{$C.member.blurb}</dd>
				{/if}
				{if !isset($C.disabled_fields.gender) && !empty($C.member.gender.name)}
					<dt>{$T.gender}: </dt>
					<dd>{$C.member.gender.name}</dd>
				{/if}
				<dt>{$T.age}:</dt>
				<dd>{$C.member.age}{($C.member.today_is_birthday) ? (' &nbsp; <img src="'|cat:$S.images_url|cat:'/cake.png" alt="" />') : ''}</dd>
				{if !isset($C.disabled_fields.location) && !empty($C.member.location)}
					<dt>{$T.location}:</dt>
					<dd>{$C.member.location}</dd>
				{/if}
				{$SUPPORT->displayHook('profile_summary_extend_basic')}
				</dl>
				{if !empty($C.custom_fields)}
					{$shown = false}
					{foreach $C.custom_fields as $field}
						{if $field.placement != 0 || empty($field.output_html)}
							{continue}
						{/if}
						{if empty($shown)}
							<dl>
							{$shown = true}
						{/if}
						<dt>{$field.name}:</dt>
						<dd>{$field.output_html}</dd>
					{/foreach}
					{if !empty($shown)}
						</dl>
					{/if}
				{/if}
				<dl class="noborder">
				{if $C.can_view_warning && $C.member.warning}
					<dt>{$T.profile_warning_level}: </dt>
					<dd>
						<a href="{$SCRIPTURL}?action=profile;u={$C.id_member};area={($C.can_issue_warning) ? 'issuewarning' : 'viewwarning'}">{$C.member.warning}%</a>
						{if !empty($C.warning_status)}
							<span class="smalltext">({$C.warning_status})</span>
						{/if}
					</dd>
				{/if}
				{if !empty($C.activate_message) || !empty($C.member.bans)}
					{if !empty($C.activate_message)}
						<dt class="clear"><span class="alert">{$C.activate_message}</span>&nbsp;(<a href="{$SCRIPTURL}?action=profile;save;area=activateaccount;u={$C.id_member};{$C.session_var}={$C.session_id}"{($C.activate_type == 4) ? (' onclick="return confirm(\''|cat:$T.profileConfirm|cat:'\');"') : ''}>{$C.activate_link_text}</a>)</dt>
					{/if}
					{if !empty($C.member.bans)}
						<dt class="clear"><span class="alert">{$T.user_is_banned}</span>&nbsp;[<a href="#" onclick="document.getElementById('ban_info').style.display = document.getElementById('ban_info').style.display == 'none' ? '' : 'none';return false;">{$T.view_ban}</a>]</dt>
						<dt class="clear" id="ban_info" style="display: none;">
							<strong>{$T.user_banned_by_following}:</strong>
							{foreach $C.member.bans as $ban}
								<br /><span class="smalltext">{$ban.explanation}</span>
							{/foreach}
						</dt>
					{/if}
				{/if}
				<dt>{$T.date_registered}: </dt>
				<dd>{$C.member.registered}</dd>
				{if $C.can_see_ip}
					{if !empty($C.member.ip)}
						<dt>{$T.ip}: </dt>
						<dd><a href="{$SCRIPTURL}?action=profile;area=tracking;sa=ip;searchip={$C.member.ip};u={$C.member.id}">{$C.member.ip}</a></dd>
					{/if}
					{if empty($M.disableHostnameLookup) && !empty($C.member.ip)}
						<dt>{$T.hostname}: </dt>
						<dd>{$C.member.hostname}</dd>
					{/if}
				{/if}
				<dt>{$T.local_time}:</dt>
				<dd>{$C.member.local_time}</dd>
				{if !empty($M.userLanguage) && !empty($C.member.language)}
					<dt>{$T.language}:</dt>
					<dd>{$C.member.language}</dd>
				{/if}
				<dt>{$T.lastLoggedIn}: </dt>
				<dd>{$C.member.last_login}</dd>
				</dl>
				{if !empty($C.custom_fields)}
					{$shown = false}
					{foreach $C.custom_fields as $field}
						{if $field.placement != 2 || empty($field.output_html)}
							{continue}
						{/if}
						{if empty($shown)}
							{$shown = true}
							<div class="custom_fields_above_signature">
							<ul class="reset nolist">
						{/if}
						<li>{$field.output_html}</li>
					{/foreach}
					{if $shown}
						</ul>
						</div>
					{/if}
				{/if}
				{$SUPPORT->displayHook('profile_summary_extend_advanced')}
				{if $C.signature_enabled && !empty($C.member.signature)}
					<div class="signature">
						<h5>{$T.signature}:</h5>
						{$C.member.signature}
					</div>
				{/if}
			</div>
		</div>
	</div>
<div class="clear"></div>
</div>