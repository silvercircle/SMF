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
function template_admin_register()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
	<div id="admincenter">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['admin_browse_register_new'], '</h3>
		</div>
		<form class="windowbg2" action="', $scripturl, '?action=admin;area=regcenter" method="post" accept-charset="UTF-8" name="postForm" id="postForm">
			<span class="topslice"><span></span></span>
			<script type="text/javascript"><!-- // --><![CDATA[
				function onCheckChange()
				{
					if (document.forms.postForm.emailActivate.checked || document.forms.postForm.password.value == \'\')
					{
						document.forms.postForm.emailPassword.disabled = true;
						document.forms.postForm.emailPassword.checked = true;
					}
					else
						document.forms.postForm.emailPassword.disabled = false;
				}
			// ]]></script>
			<div class="content" id="register_screen">';

	if (!empty($context['registration_done']))
		echo '
				<div class="windowbg" id="profile_success">
					', $context['registration_done'], '
				</div>';

	echo '
				<dl class="register_form" id="admin_register_form">
					<dt>
						<strong><label for="user_input">', $txt['admin_register_username'], ':</label></strong>
						<span class="smalltext">', $txt['admin_register_username_desc'], '</span>
					</dt>
					<dd>
						<input type="text" name="user" id="user_input" tabindex="', $context['tabindex']++, '" size="50" maxlength="'.$modSettings['username_max_length'],'" class="input_text" />
					</dd>
					<dt>
						<strong><label for="email_input">', $txt['admin_register_email'], ':</label></strong>
						<span class="smalltext">', $txt['admin_register_email_desc'], '</span>
					</dt>
					<dd>
						<input type="text" name="email" id="email_input" tabindex="', $context['tabindex']++, '" size="30" class="input_text" />
					</dd>
					<dt>
						<strong><label for="password_input">', $txt['admin_register_password'], ':</label></strong>
						<span class="smalltext">', $txt['admin_register_password_desc'], '</span>
					</dt>
					<dd>
						<input type="password" name="password" id="password_input" tabindex="', $context['tabindex']++, '" size="30" class="input_password" onchange="onCheckChange();" />
					</dd>';

	if (!empty($context['member_groups']))
	{
		echo '
					<dt>
						<strong><label for="group_select">', $txt['admin_register_group'], ':</label></strong>
						<span class="smalltext">', $txt['admin_register_group_desc'], '</span>
					</dt>
					<dd>
						<select name="group" id="group_select" tabindex="', $context['tabindex']++, '">';

		foreach ($context['member_groups'] as $id => $name)
			echo '
							<option value="', $id, '">', $name, '</option>';

		echo '
						</select>
					</dd>';
	}

	echo '
					<dt>
						<strong><label for="emailPassword_check">', $txt['admin_register_email_detail'], ':</label></strong>
						<span class="smalltext">', $txt['admin_register_email_detail_desc'], '</span>
					</dt>
					<dd>
						<input type="checkbox" name="emailPassword" id="emailPassword_check" tabindex="', $context['tabindex']++, '" checked="checked" disabled="disabled" class="input_check" />
					</dd>
					<dt>
						<strong><label for="emailActivate_check">', $txt['admin_register_email_activate'], ':</label></strong>
					</dt>
					<dd>
						<input type="checkbox" name="emailActivate" id="emailActivate_check" tabindex="', $context['tabindex']++, '"', !empty($modSettings['registration_method']) && $modSettings['registration_method'] == 1 ? ' checked="checked"' : '', ' onclick="onCheckChange();" class="input_check" />
					</dd>
				</dl>
				<div class="righttext">
					<input type="submit" name="regSubmit" value="', $txt['register'], '" tabindex="', $context['tabindex']++, '" class="button_submit" />
					<input type="hidden" name="sa" value="register" />
				</div>
			</div>
			<span class="botslice"><span></span></span>
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
		</form>
	</div>
	<br class="clear" />';
}

// Form for editing the agreement shown for people registering to the forum.
function template_edit_agreement()
{
	global $context, $settings, $options, $scripturl, $txt;

	// Just a big box to edit the text file ;).
	echo '
		<div class="cat_bar">
			<h3 class="catbg">', $txt['registration_agreement'], '</h3>
		</div>';

	// Warning for if the file isn't writable.
	if (!empty($context['warning']))
		echo '
		<p class="error">', $context['warning'], '</p>';

	echo '
		<div class="windowbg2" id="registration_agreement">
			<span class="topslice"><span></span></span>
			<div class="content">';

	// Is there more than one language to choose from?
	if (count($context['editable_agreements']) > 1)
	{
		echo '
				<div class="information">
					<form action="', $scripturl, '?action=admin;area=regcenter" id="change_reg" method="post" accept-charset="UTF-8" style="display: inline;">
						<strong>', $txt['admin_agreement_select_language'], ':</strong>&nbsp;
						<select name="agree_lang" onchange="document.getElementById(\'change_reg\').submit();" tabindex="', $context['tabindex']++, '">';

		foreach ($context['editable_agreements'] as $file => $name)
			echo '
							<option value="', $file, '" ', $context['current_agreement'] == $file ? 'selected="selected"' : '', '>', $name, '</option>';

		echo '
						</select>
						<div class="righttext">
							<input type="hidden" name="sa" value="agreement" />
							<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
							<input type="submit" name="change" value="', $txt['admin_agreement_select_language_change'], '" tabindex="', $context['tabindex']++, '" class="button_submit" />
						</div>
					</form>
				</div>';
	}

	echo '
				<form action="', $scripturl, '?action=admin;area=regcenter" method="post" accept-charset="UTF-8">';

	// Show the actual agreement in an oversized text box.
	echo '
					<p class="agreement">
						<textarea cols="70" rows="20" name="agreement" id="agreement">', $context['agreement'], '</textarea>
					</p>
					<p>
						<label for="requireAgreement"><input type="checkbox" name="requireAgreement" id="requireAgreement"', $context['require_agreement'] ? ' checked="checked"' : '', ' tabindex="', $context['tabindex']++, '" value="1" class="input_check" /> ', $txt['admin_agreement'], '.</label>
					</p>
					<div class="righttext">
						<input type="submit" value="', $txt['save'], '" tabindex="', $context['tabindex']++, '" class="button_submit" />
						<input type="hidden" name="agree_lang" value="', $context['current_agreement'], '" />
						<input type="hidden" name="sa" value="agreement" />
						<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
					</div>
				</form>
			</div>
			<span class="botslice"><span></span></span>
		</div>
		<br class="clear" />';
}

function template_edit_reserved_words()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
		<div class="cat_bar">
			<h3 class="catbg">', $txt['admin_reserved_set'], '</h3>
		</div>
		<form id="registration_agreement" class="windowbg2" action="', $scripturl, '?action=admin;area=regcenter" method="post" accept-charset="UTF-8">
			<span class="topslice"><span></span></span>
			<div class="content">
				<h4>', $txt['admin_reserved_line'], '</h4>
				<p class="reserved_names">
					<textarea cols="30" rows="6" name="reserved" id="reserved">', implode("\n", $context['reserved_words']), '</textarea>
				</p>
				<ul class="reset">
					<li><label for="matchword"><input type="checkbox" name="matchword" id="matchword" tabindex="', $context['tabindex']++, '" ', $context['reserved_word_options']['match_word'] ? 'checked="checked"' : '', ' class="input_check" /> ', $txt['admin_match_whole'], '</label></li>
					<li><label for="matchcase"><input type="checkbox" name="matchcase" id="matchcase" tabindex="', $context['tabindex']++, '" ', $context['reserved_word_options']['match_case'] ? 'checked="checked"' : '', ' class="input_check" /> ', $txt['admin_match_case'], '</label></li>
					<li><label for="matchuser"><input type="checkbox" name="matchuser" id="matchuser" tabindex="', $context['tabindex']++, '" ', $context['reserved_word_options']['match_user'] ? 'checked="checked"' : '', ' class="input_check" /> ', $txt['admin_check_user'], '</label></li>
					<li><label for="matchname"><input type="checkbox" name="matchname" id="matchname" tabindex="', $context['tabindex']++, '" ', $context['reserved_word_options']['match_name'] ? 'checked="checked"' : '', ' class="input_check" /> ', $txt['admin_check_display'], '</label></li>
				</ul>
				<div class="righttext">
					<input type="submit" value="', $txt['save'], '" name="save_reserved_names" tabindex="', $context['tabindex']++, '" style="margin: 1ex;" class="button_submit" />
				</div>
			</div>
			<span class="botslice"><span></span></span>
			<input type="hidden" name="sa" value="reservednames" />
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
		</form>
		<br class="clear" />';
}

?>
