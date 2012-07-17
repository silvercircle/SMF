{extends 'profile/profile_base.tpl'}
{block 'profile_content'}
<div id="profileview">
	<div class="cat_bar2">
		<h3>
			{$T.summary}
		</h3>
	</div>
	<div id="basicinfo">
		<div class="blue_container cleantop">
			<div class="content flow_auto">
				<div class="username"><h4>{$C.member.name} <span class="position"> {(!empty($C.member.group)) ? $C.member.group : $C.member.post_group}</span></h4></div>
				{if !empty($C.member.avatar.image)}
					{$C.member.avatar.image}
				{else}
        	<img class="fourtyeight" src="{$S.images_url}/unknown.png" alt="avatar" />
				{/if}
				<ul class="reset">
				{if $C.member.show_email === 'yes' || $C.member.show_email === 'no_through_forum' || $C.member.show_email === 'yes_permission_override'}
					<li><a href="{$SCRIPTURL}?action=emailuser;sa=email;uid={$C.member.id}" title="{($C.member.show_email == 'yes' || $C.member.show_email == 'yes_permission_override') ? $C.member.email : ''}" rel="nofollow">{$T.send_email}</a></li>
				{/if}
				{if !empty($C.custom_fields)}
					{foreach $C.custom_fields as $field)
						{if ($field.placement == 1 || empty($field.output_html)) && !empty($field.value)}
							<li class="custom_field">{$field.output_html}</li>
						{/if}
					{/foreach}
				{/if}
				</ul>
				<span id="userstatus">{($C.can_send_pm) ? ('<a href="'|cat:$C.member.online.href|cat:'" title="'|cat:$C.member.online.label|cat:'" rel="nofollow">') : ''}{$C.member.online.text}{($C.can_send_pm) ? '</a>' : ''}
				{if !empty($C.can_have_buddy) && !$C.user.is_owner}
					{$index = ($C.member.is_buddy) ? 'buddy_remove' : 'buddy_add'}
					<br><a href="{$SCRIPTURL}?action=buddy;u={$C.id_member};{$C.session_var}={$C.session_id}">[{$T.$index}]</a>
				{/if}
				</span>
				<p id="infolinks">';
				{if !$C.user.is_owner && $C.can_send_pm}
					<a href="{$SCRIPTURL}?action=pm;sa=send;u={$C.id_member}">{$T.profile_sendpm_short}</a><br>
				{/if}
					<a href="{$SCRIPTURL}?action=profile;area=showposts;u={$C.id_member}">{$T.showPosts}</a><br>
					<a href="{$SCRIPTURL}?action=profile;area=statistics;u={$C.id_member}">{$T.statPanel}</a>
				</p>
			</div>
		</div>
		{$SUPPORT->displayHook('profile_summary_basicinfo')}
	</div>
	<div id="detailedinfo"><br />
		<div class="yellow_container">
			<div class="content inset_shadow mediumpadding">
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
				{if $M.karmaMode == '1'}
					<dt>{$M.karmaLabel}</dt>
					<dd>{$C.member.karma.good - $C.member.karma.bad}</dd>
				{elseif $M.karmaMode == '2'}
					<dt>{$M.karmaLabel}</dt>
					<dd>+{$C.member.karma.good}/-{$C.member.karma.bad}</dd>
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
				</dl>

	// Any custom fields for standard placement?
	if (!empty($context['custom_fields']))
	{
		$shown = false;
		foreach ($context['custom_fields'] as $field)
		{
			if ($field['placement'] != 0 || empty($field['output_html']))
				continue;

			if (empty($shown))
			{
				echo '
				<dl>';
				$shown = true;
			}

			echo '
					<dt>', $field['name'], ':</dt>
					<dd>', $field['output_html'], '</dd>';
		}

		if (!empty($shown))
			echo '
				</dl>';
	}

	echo '
				<dl class="noborder">';

	// Can they view/issue a warning?
	if ($context['can_view_warning'] && $context['member']['warning'])
	{
		echo '
					<dt>', $txt['profile_warning_level'], ': </dt>
					<dd>
						<a href="', $scripturl, '?action=profile;u=', $context['id_member'], ';area=', $context['can_issue_warning'] ? 'issuewarning' : 'viewwarning', '">', $context['member']['warning'], '%</a>';

		// Can we provide information on what this means?
		if (!empty($context['warning_status']))
			echo '
						<span class="smalltext">(', $context['warning_status'], ')</span>';

		echo '
					</dd>';
	}

	// Is this member requiring activation and/or banned?
	if (!empty($context['activate_message']) || !empty($context['member']['bans']))
	{

		// If the person looking at the summary has permission, and the account isn't activated, give the viewer the ability to do it themselves.
		if (!empty($context['activate_message']))
			echo '
					<dt class="clear"><span class="alert">', $context['activate_message'], '</span>&nbsp;(<a href="' . $scripturl . '?action=profile;save;area=activateaccount;u=' . $context['id_member'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '"', ($context['activate_type'] == 4 ? ' onclick="return confirm(\'' . $txt['profileConfirm'] . '\');"' : ''), '>', $context['activate_link_text'], '</a>)</dt>';

		// If the current member is banned, show a message and possibly a link to the ban.
		if (!empty($context['member']['bans']))
		{
			echo '
					<dt class="clear"><span class="alert">', $txt['user_is_banned'], '</span>&nbsp;[<a href="#" onclick="document.getElementById(\'ban_info\').style.display = document.getElementById(\'ban_info\').style.display == \'none\' ? \'\' : \'none\';return false;">' . $txt['view_ban'] . '</a>]</dt>
					<dt class="clear" id="ban_info" style="display: none;">
						<strong>', $txt['user_banned_by_following'], ':</strong>';

			foreach ($context['member']['bans'] as $ban)
				echo '
						<br /><span class="smalltext">', $ban['explanation'], '</span>';

			echo '
					</dt>';
		}
	}

	echo '
					<dt>', $txt['date_registered'], ': </dt>
					<dd>', $context['member']['registered'], '</dd>';

	// If the person looking is allowed, they can check the members IP address and hostname.
	if ($context['can_see_ip'])
	{
		if (!empty($context['member']['ip']))
		echo '
					<dt>', $txt['ip'], ': </dt>
					<dd><a href="', $scripturl, '?action=profile;area=tracking;sa=ip;searchip=', $context['member']['ip'], ';u=', $context['member']['id'], '">', $context['member']['ip'], '</a></dd>';

		if (empty($modSettings['disableHostnameLookup']) && !empty($context['member']['ip']))
			echo '
					<dt>', $txt['hostname'], ': </dt>
					<dd>', $context['member']['hostname'], '</dd>';
	}

	echo '
					<dt>', $txt['local_time'], ':</dt>
					<dd>', $context['member']['local_time'], '</dd>';

	if (!empty($modSettings['userLanguage']) && !empty($context['member']['language']))
		echo '
					<dt>', $txt['language'], ':</dt>
					<dd>', $context['member']['language'], '</dd>';

	echo '
					<dt>', $txt['lastLoggedIn'], ': </dt>
					<dd>', $context['member']['last_login'], '</dd>
				</dl>';

	// Are there any custom profile fields for the summary?
	if (!empty($context['custom_fields']))
	{
		$shown = false;
		foreach ($context['custom_fields'] as $field)
		{
			if ($field['placement'] != 2 || empty($field['output_html']))
				continue;
			if (empty($shown))
			{
				$shown = true;
				echo '
				<div class="custom_fields_above_signature">
					<ul class="reset nolist">';
			}
			echo '
						<li>', $field['output_html'], '</li>';
		}
		if ($shown)
				echo '
					</ul>
				</div>';
	}

	// Show the users signature.
	if ($context['signature_enabled'] && !empty($context['member']['signature']))
		echo '
				<div class="signature">
					<h5>', $txt['signature'], ':</h5>
					', $context['member']['signature'], '
				</div>';

	echo '
			</div>
		</div>
	</div>
<div class="clear"></div>
</div>';


{/block}