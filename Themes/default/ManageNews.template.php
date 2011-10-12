<?php
/**
 * @name      EosAlpha BBS
 * @copyright 2011 Alex Vie silvercircle(AT)gmail(DOT)com
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright:	2011 Simple Machines (http://www.simplemachines.org)
 * license:  	BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version 1.0pre
 */
function template_edit_news_item()
{
	global $context, $txt;

	echo '
	<div id="admincenter">
	<div class="cat_bar">
	 <h3>Edit news item</h3>
	</div>
	<div class="blue_container cleantop">
	 <div class="content">
	 <form action="',$context['submit_url'],'" method="post" accept-charset="', $context['character_set'], '" name="editnewsitem" id="editnewsitem">
	 <input type="hidden" name="id" value="',$context['news_item']['id'],'" />
	 <textarea style="width:99%;height:20ex;" name="body">',$context['news_item']['body'],'</textarea>
	 <br>
	 <br>
	 <h1 class="bigheader secondary">',$txt['newsitem_display_options'],'</h1>
	 <dl class="settings mediumpadding">
	 <dt>',$txt['newsitem_show_boardindex'],'</dt>
	 <dd><input type="checkbox" name="showindex" value="1" class="input_check" ',$context['news_item']['on_index'] ? 'checked="checked"' : '', ' /></dd>
	 <dt>',$txt['newsitem_show_boards'],'</dt>
	 <dd><input type="text" size="40" name="showboards" value="',$context['news_item']['boards'],'" /></dd>
	 <dt>',$txt['newsitem_show_topics'],'</dt>
	 <dd><input type="text" size="40" name="showtopics" value="',$context['news_item']['topics'],'" /></dd>
	 <dt>',$txt['newsitem_show_groups'],'</dt>
	 <dd><input type="text" size="40" name="showgroups" value="',$context['news_item']['groups'],'" /></dd>
	 </dl>
	 <div class="floatright">
	 <input type="submit" name="submit" class="button_submit" value="',$txt['save'],'" />
	 </div>
	 <div class="clear"></div>
	 <input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
	 </form>
	 </div>
	</div>
	</div>';
}
// Form for editing current news on the site.
function template_edit_news()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
	<div id="admincenter">
		<form action="', $scripturl, '?action=admin;area=news;sa=editnews" method="post" accept-charset="', $context['character_set'], '" name="postmodify" id="postmodify">
			<table class="table_grid" width="100%">
				<thead>
					<tr>
						<th class="glass lefttext" style="width:100%;">', $txt['preview'], '</th>
						<th class="glass centertext"><input type="checkbox" class="input_check" onclick="invertAll(this, this.form);" /></th>
					</tr>
				</thead>
				<tbody>';

	// Loop through all the current news items so you can edit/remove them.
	foreach ($context['news_items'] as $news)
		echo '
					<tr class="windowbg2">
						<td align="left" valign="top">
							<div style="overflow: auto; width: 100%;">', $news['body'], '</div>
							<div class="floatright"><a href="',$scripturl,'?action=admin;area=news;sa=editnewsitem;itemid=',$news['id'],'">Edit</a></div>
						</td><td align="center">
							<input type="checkbox" name="remove[]" value="', $news['id'], '" class="input_check" />
						</td>
					</tr>';

	// This provides an empty text box to add a news item to the site.
	echo '
					<tr id="moreNews" class="windowbg2" style="display: none;">
						<td align="center">
							<div id="moreNewsItems"></div>
						</td>
						<td align="center">
						</td>
						<td align="center">
						</td>
					</tr>
				</tbody>
			</table>
			<br>
			<div class="floatright smalltext">
				<div id="moreNewsItems_link"><a href="',$scripturl,'?action=admin;area=news;sa=editnewsitem;itemid=0">', $txt['editnews_clickadd'], '</a></div>
			</div>
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
		</form>
	</div>
	<br class="clear" />';
}

function template_email_members()
{
	global $context, $settings, $options, $txt, $scripturl;

	// This is some javascript for the simple/advanced toggling stuff.
	echo '
	<script type="text/javascript"><!-- // --><![CDATA[
		function toggleAdvanced(mode)
		{
			// What styles are we doing?
			var divStyle = mode ? "" : "none";

			document.getElementById("advanced_settings_div").style.display = divStyle;
			document.getElementById("gosimple").style.display = divStyle;
			document.getElementById("goadvanced").style.display = mode ? "none" : "";
		}
	// ]]></script>';

	echo '
	<div id="admincenter">
		<form action="', $scripturl, '?action=admin;area=news;sa=mailingcompose" method="post" class="flow_hidden" accept-charset="', $context['character_set'], '">
			<div class="cat_bar">
				<h3 class="catbg">', $txt['admin_newsletters'], '</h3>
			</div>
			<div class="orange_container cleantop mediumpadding">
				', $txt['admin_news_select_recipients'], '
			</div>
			<br>
			<div class="blue_container">
				<div class="content">
					<dl class="settings">
						<dt>
							<strong>', $txt['admin_news_select_group'], ':</strong><br />
							<span class="smalltext">', $txt['admin_news_select_group_desc'], '</span>
						</dt>
						<dd>';

	foreach ($context['groups'] as $group)
				echo '
							<label for="groups_', $group['id'], '"><input type="checkbox" name="groups[', $group['id'], ']" id="groups_', $group['id'], '" value="', $group['id'], '" checked="checked" class="input_check" /> ', $group['name'], '</label> <em>(', $group['member_count'], ')</em><br />';

	echo '
							<br />
							<label for="checkAllGroups"><input type="checkbox" id="checkAllGroups" checked="checked" onclick="invertAll(this, this.form, \'groups\');" class="input_check" /> <em>', $txt['check_all'], '</em></label>';

	echo '
						</dd>
					</dl><br class="clear" />
				</div>
			</div>
			<br />';
			$collapser = array('id' => 'email_members_adv', 'title' => $txt['advanced']);
			template_create_collapsible_container($collapser);
			echo '
				<div class="content">
					<dl class="settings">
						<dt>
							<strong>', $txt['admin_news_select_email'], ':</strong><br />
							<span class="smalltext">', $txt['admin_news_select_email_desc'], '</span>
						</dt>
						<dd>
							<textarea name="emails" rows="5" cols="30" style="' . ($context['browser']['is_ie8'] ? 'width: 635px; max-width: 98%; min-width: 98%' : 'width: 98%') . ';"></textarea>
						</dd>
						<dt>
							<strong>', $txt['admin_news_select_members'], ':</strong><br />
							<span class="smalltext">', $txt['admin_news_select_members_desc'], '</span>
						</dt>
						<dd>
							<input type="text" name="members" id="members" value="" size="30" class="input_text" />
							<span id="members_container"></span>
						</dd>
					</dl>
					<hr class="bordercolor" />
					<dl class="settings">
						<dt>
							<strong>', $txt['admin_news_select_excluded_groups'], ':</strong><br />
							<span class="smalltext">', $txt['admin_news_select_excluded_groups_desc'], '</span>
						</dt>
						<dd>';

	foreach ($context['groups'] as $group)
				echo '
							<label for="exclude_groups_', $group['id'], '"><input type="checkbox" name="exclude_groups[', $group['id'], ']" id="exclude_groups_', $group['id'], '" value="', $group['id'], '" class="input_check" /> ', $group['name'], '</label> <em>(', $group['member_count'], ')</em><br />';

	echo '
							<br />
							<label for="checkAllGroupsExclude"><input type="checkbox" id="checkAllGroupsExclude" onclick="invertAll(this, this.form, \'exclude_groups\');" class="input_check" /> <em>', $txt['check_all'], '</em></label><br />
						</dd>
						<dt>
							<strong>', $txt['admin_news_select_excluded_members'], ':</strong><br />
							<span class="smalltext">', $txt['admin_news_select_excluded_members_desc'], '</span>
						</dt>
						<dd>
							<input type="text" name="exclude_members" id="exclude_members" value="" size="30" class="input_text" />
							<span id="exclude_members_container"></span>
						</dd>
					</dl>
					<hr class="bordercolor" />
					<dl class="settings">
						<dt>
							<label for="email_force"><strong>', $txt['admin_news_select_override_notify'], ':</strong></label><br />
							<span class="smalltext">', $txt['email_force'], '</span>
						</dt>
						<dd>
							<input type="checkbox" name="email_force" id="email_force" value="1" class="input_check" />
						</dd>
					</dl><br class="clear" />
				</div>
			</div>
			<br>
			<div class="righttext">
				<input type="submit" value="', $txt['admin_next'], '" class="button_submit" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
			</div>
		</form>
	</div>
	<br class="clear" />';

	// Make the javascript stuff visible.
	echo '
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/suggest.js?fin20"></script>
	<script type="text/javascript"><!-- // --><![CDATA[
		document.getElementById("advanced_select_div").style.display = "";
		var oMemberSuggest = new smc_AutoSuggest({
			sSelf: \'oMemberSuggest\',
			sSessionId: \'', $context['session_id'], '\',
			sSessionVar: \'', $context['session_var'], '\',
			sSuggestId: \'members\',
			sControlId: \'members\',
			sSearchType: \'member\',
			bItemList: true,
			sPostName: \'member_list\',
			sURLMask: \'action=profile;u=%item_id%\',
			sTextDeleteItem: \'', $txt['autosuggest_delete_item'], '\',
			sItemListContainerId: \'members_container\',
			aListItems: []
		});
		var oExcludeMemberSuggest = new smc_AutoSuggest({
			sSelf: \'oExcludeMemberSuggest\',
			sSessionId: \'', $context['session_id'], '\',
			sSessionVar: \'', $context['session_var'], '\',
			sSuggestId: \'exclude_members\',
			sControlId: \'exclude_members\',
			sSearchType: \'member\',
			bItemList: true,
			sPostName: \'exclude_member_list\',
			sURLMask: \'action=profile;u=%item_id%\',
			sTextDeleteItem: \'', $txt['autosuggest_delete_item'], '\',
			sItemListContainerId: \'exclude_members_container\',
			aListItems: []
		});
	// ]]></script>';
}

function template_email_members_compose()
{
	global $context, $settings, $options, $txt, $scripturl;

	echo '
	<div id="admincenter">
		<form action="', $scripturl, '?action=admin;area=news;sa=mailingsend" method="post" accept-charset="', $context['character_set'], '">
			<div class="cat_bar">
				<h3 class="catbg">
					<a href="', $scripturl, '?action=helpadmin;help=email_members" onclick="return reqWin(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics.png" alt="', $txt['help'], '" class="icon" /></a> ', $txt['admin_newsletters'], '
				</h3>
			</div>
			<div class="information">
				', $txt['email_variables'], '
			</div>
			<div class="windowbg">
				<span class="topslice"><span></span></span>
				<div class="content">
					<p>
						<input type="text" name="subject" size="60" value="', $context['default_subject'], '" class="input_text" />
					</p>
					<p>
						<textarea cols="70" rows="9" name="message" class="editor">', $context['default_message'], '</textarea>
					</p>
					<ul class="reset">
						<li><label for="send_pm"><input type="checkbox" name="send_pm" id="send_pm" class="input_check" onclick="if (this.checked && ', $context['total_emails'], ' != 0 && !confirm(\'', $txt['admin_news_cannot_pm_emails_js'], '\')) return false; this.form.parse_html.disabled = this.checked; this.form.send_html.disabled = this.checked; " /> ', $txt['email_as_pms'], '</label></li>
						<li><label for="send_html"><input type="checkbox" name="send_html" id="send_html" class="input_check" onclick="this.form.parse_html.disabled = !this.checked;" /> ', $txt['email_as_html'], '</label></li>
						<li><label for="parse_html"><input type="checkbox" name="parse_html" id="parse_html" checked="checked" disabled="disabled" class="input_check" /> ', $txt['email_parsed_html'], '</label></li>
					</ul>
					<p>
						<input type="submit" value="', $txt['sendtopic_send'], '" class="button_submit" />
					</p>
				</div>
				<span class="botslice"><span></span></span>
			</div>
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
			<input type="hidden" name="email_force" value="', $context['email_force'], '" />
			<input type="hidden" name="total_emails" value="', $context['total_emails'], '" />
			<input type="hidden" name="max_id_member" value="', $context['max_id_member'], '" />';

	foreach ($context['recipients'] as $key => $values)
		echo '
			<input type="hidden" name="', $key, '" value="', implode(($key == 'emails' ? ';' : ','), $values), '" />';

	echo '
		</form>
	</div>
	<br class="clear" />';
}

function template_email_members_send()
{
	global $context, $settings, $options, $txt, $scripturl;

	echo '
	<div id="admincenter">
		<form action="', $scripturl, '?action=admin;area=news;sa=mailingsend" method="post" accept-charset="', $context['character_set'], '" name="autoSubmit" id="autoSubmit">
			<div class="cat_bar">
				<h3 class="catbg">
					<a href="', $scripturl, '?action=helpadmin;help=email_members" onclick="return reqWin(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics.png" alt="', $txt['help'], '" align="top" /></a> ', $txt['admin_newsletters'], '
				</h3>
			</div>
			<div class="windowbg">
				<span class="topslice"><span></span></span>
				<div class="content">
					<p>
						<strong>', $context['percentage_done'], '% ', $txt['email_done'], '</strong>
					</p>
					<input type="submit" name="b" value="', $txt['email_continue'], '" class="button_submit" />
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
					<input type="hidden" name="subject" value="', $context['subject'], '" />
					<input type="hidden" name="message" value="', $context['message'], '" />
					<input type="hidden" name="start" value="', $context['start'], '" />
					<input type="hidden" name="total_emails" value="', $context['total_emails'], '" />
					<input type="hidden" name="max_id_member" value="', $context['max_id_member'], '" />
					<input type="hidden" name="send_pm" value="', $context['send_pm'], '" />
					<input type="hidden" name="send_html" value="', $context['send_html'], '" />
					<input type="hidden" name="parse_html" value="', $context['parse_html'], '" />';

	// All the things we must remember!
	foreach ($context['recipients'] as $key => $values)
		echo '
					<input type="hidden" name="', $key, '" value="', implode(($key == 'emails' ? ';' : ','), $values), '" />';

	echo '
				</div>
				<span class="botslice"><span></span></span>
			</div>
		</form>
	</div>
	<br class="clear" />
	<script type="text/javascript"><!-- // --><![CDATA[
		var countdown = 2;
		doAutoSubmit();

		function doAutoSubmit()
		{
			if (countdown == 0)
				document.forms.autoSubmit.submit();
			else if (countdown == -1)
				return;

			document.forms.autoSubmit.b.value = "', $txt['email_continue'], ' (" + countdown + ")";
			countdown--;

			setTimeout("doAutoSubmit();", 1000);
		}
	// ]]></script>';
}

?>