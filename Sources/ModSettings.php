<?php
/******************************************************************************
* ModSettings.php                                                             *
*******************************************************************************
* SMF: Simple Machines Forum                                                  *
* Open-Source Project Inspired by Zef Hemel (zef@zefhemel.com)                *
* =========================================================================== *
* Software Version:           SMF 1.0.1                                       *
* Software by:                Simple Machines (http://www.simplemachines.org) *
* Copyright 2001-2005 by:     Lewis Media (http://www.lewismedia.com)         *
* Support, News, Updates at:  http://www.simplemachines.org                   *
*******************************************************************************
* This program is free software; you may redistribute it and/or modify it     *
* under the terms of the provided license as published by Lewis Media.        *
*                                                                             *
* This program is distributed in the hope that it is and will be useful,      *
* but WITHOUT ANY WARRANTIES; without even any implied warranty of            *
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                        *
*                                                                             *
* See the "license.txt" file for details of the Simple Machines license.      *
* The latest version can always be found at http://www.simplemachines.org.    *
******************************************************************************/
if (!defined('SMF'))
	die('Hacking attempt...');

/*	This file is here to make it easier for installed mods to have settings
	and options.  Please look just below for information on adding settings.
*/

function defineSettings()
{
	global $txt, $db_prefix;

	loadLanguage('ModSettings');

	// Load all the boards for the calendar and recycle topics.
	$calBoards = array('');
	$request = db_query("
		SELECT b.ID_BOARD, b.name AS bName, c.name AS cName
		FROM {$db_prefix}boards AS b, {$db_prefix}categories AS c
		WHERE b.ID_CAT = c.ID_CAT
		ORDER BY c.catOrder, b.boardOrder", __FILE__, __LINE__);
	while ($row = mysql_fetch_assoc($request))
		$calBoards[$row['ID_BOARD']] = $row['cName'] . ' - ' . $row['bName'];
	mysql_free_result($request);

	/* Adding options to this list isn't hard.  The basic format for a checkbox is:
		array('check', 'nameInModSettingsAndSQL'),

	   And for a text box:
		array('text', 'nameInModSettingsAndSQL')
	   (NOTE: You have to add an entry for this at the bottom!)

	   In these cases, it will look for $txt['nameInModSettingsAndSQL'] as the description,
	   and $helptxt['nameInModSettingsAndSQL'] as the help popup description.

	Here's a quick explanation of how to add a new item:

	 * A text input box.  For textual values.
	ie.	array('text', 'nameInModSettingsAndSQL', 'OptionalInputBoxWidth',
			&$txt['OptionalDescriptionOfTheOption'], 'OptionalReferenceToHelpAdmin'),

	 * A text input box.  For numerical values.
	ie.	array('int', 'nameInModSettingsAndSQL', 'OptionalInputBoxWidth',
			&$txt['OptionalDescriptionOfTheOption'], 'OptionalReferenceToHelpAdmin'),

	 * A text input box.  For floating point values.
	ie.	array('float', 'nameInModSettingsAndSQL', 'OptionalInputBoxWidth',
			&$txt['OptionalDescriptionOfTheOption'], 'OptionalReferenceToHelpAdmin'),

	 * A check box.  Either one or zero. (boolean)
	ie.	array('check', 'nameInModSettingsAndSQL', null, &$txt['descriptionOfTheOption'],
			'OptionalReferenceToHelpAdmin'),

	 * A selection box.  Used for the selection of something from a list.
	ie.	array('select', 'nameInModSettingsAndSQL', array('valueForSQL' => &$txt['displayedValue']),
			&$txt['descriptionOfTheOption'], 'OptionalReferenceToHelpAdmin'),
	Note that just saying array('first', 'second') will put 0 in the SQL for 'first'.

	 * A password input box. Used for passwords, no less!
	ie.	array('password', 'nameInModSettingsAndSQL', 'OptionalInputBoxWidth',
			&$txt['descriptionOfTheOption'], 'OptionalReferenceToHelpAdmin'),

	For each option:
		type (see above), variable name, size/possible values, description, helptext.
	OR	make type 'rule' for an empty string for a horizontal rule.
	OR	make type 'heading' with a string for a titled section. */

	$config_vars = array(
		array('heading', &$txt['mods_cat_features']),
			// Big Options... polls, sticky, guest stuff....
			array('select', 'pollMode', array(&$txt['smf34'], &$txt['smf32'], &$txt['smf33'])),
			array('check', 'enableStickyTopics'),
		array('rule'),
			// Basic stuff, user languages, titles, flash, permissions...
			array('check', 'allow_guestAccess'),
			array('check', 'userLanguage'),
			array('check', 'allow_editDisplayName'),
			array('check', 'allow_hideOnline'),
			array('check', 'allow_hideEmail'),
			array('check', 'guest_hideContacts'),
			array('check', 'titlesEnable'),
			array('text', 'default_personalText'),
			array('int', 'max_signatureLength', 5),
			array('check', 'removeNestedQuotes'),
			array('check', 'enableEmbeddedFlash'),
			array('check', 'enablePostHTML'),
			array('text', 'disabledBBC'),
			array('check', 'enableBBC'),
			array('int', 'max_messageLength', 5),
			array('check', 'enableNewReplyWarning'),
		array('rule'),
			// Stats, compression, cookies.... server type stuff.
			array('text', 'time_format'),
			array('select', 'number_format', array('1234.00' => '1234.00', '1,234.00' => '1,234.00', '1.234,00' => '1.234,00', '1 234,00' => '1 234,00', '1234,00' => '1234,00')),
			array('float', 'time_offset'),
			array('int', 'spamWaitTime'),
			array('int', 'edit_wait_time'),
			array('int', 'failed_login_threshold'),
			array('int', 'lastActive'),
			array('check', 'enableSpellChecking'),
			array('check', 'trackStats'),
			array('check', 'hitStats'),
			array('check', 'enableCompressedOutput'),
			array('check', 'databaseSession_enable'),
			array('check', 'databaseSession_loose'),
			array('int', 'databaseSession_lifetime'),
			array('check', 'enableErrorLogging'),
			array('int', 'cookieTime', 5),
			array('check', 'localCookies'),
			array('check', 'globalCookies'),
			array('check', 'redirectMetaRefresh'),
			array('check', 'securityDisable'),
		array('rule'),
			// Email and registration type stuff...
			array('select', 'registration_method', array(&$txt['registration_standard'], &$txt['registration_activate'], &$txt['registration_approval'], &$txt['registration_disabled'])),
			array('check', 'notify_on_new_registration'),
			array('check', 'send_validation_onChange'),
			array('check', 'send_welcomeEmail'),
		array('rule'),
			// Database reapir, optimization, etc.
			array('int', 'autoOptDatabase', 4),
			array('int', 'autoOptMaxOnline', 4),
			array('check', 'autoFixDatabase'),
		array('rule'),
			// Option-ish things... miscellaneous sorta.
			array('check', 'notifyAnncmnts_UserDisable'),
			array('check', 'modlog_enabled'),
			array('check', 'queryless_urls'),
		array('rule'),
			// Width/Height image reduction.
			array('int', 'maxwidth'),
			array('int', 'maxheight'),
		array('rule'),
			// SMTP stuff.
			array('select', 'mail_type', array('smtp' => 'SMTP', 'sendmail' => 'sendmail')),
			array('text', 'smtp_host'),
			array('text', 'smtp_port'),
			array('text', 'smtp_username'),
			array('password', 'smtp_password'),
		array('rule'),
			// XML stuff ;).
			array('check', 'xmlnews_enable'),
			array('int', 'xmlnews_maxlen'),
		array('rule'),
			// Recycle topics?
			array('check', 'recycle_enable'),
			array('select', 'recycle_board', $calBoards),
		array('heading', &$txt['mods_cat_layout']),
			// Compact pages?
			array('check', 'compactTopicPagesEnable'),
			array('int', 'compactTopicPagesContiguous', null, $txt['smf235'] . '<div class="smalltext">' . str_replace(' ', '&nbsp;', '"3" ' . $txt['smf236'] . ': <b>1 ... 4 [5] 6 ... 9</b>') . '<br />' . str_replace(' ', '&nbsp;', '"5" ' . $txt['smf236'] . ': <b>1 ... 3 4 [5] 6 7 ... 9</b>') . '</div>'),
		array('rule'),
			// Stuff that just is everywhere - today, search, online, etc.
			array('select', 'todayMod', array(&$txt['smf290'], &$txt['smf291'], &$txt['smf292'])),
			array('check', 'topbottomEnable'),
			array('check', 'onlineEnable'),
			array('check', 'enableVBStyleLogin'),
			array('check', 'autoLinkUrls'),
			array('int', 'fixLongWords'),
		array('rule'),
			// Pagination stuff.
			array('int', 'defaultMaxTopics'),
			array('int', 'defaultMaxMessages'),
			array('int', 'defaultMaxMembers'),
			array('int', 'topicSummaryPosts'),
			array('int', 'enableAllMessages'),
		array('rule'),
			// Number of posts for a hot topic, participation, etc.?
			array('int', 'hotTopicPosts'),
			array('int', 'hotTopicVeryPosts'),
			array('check', 'enableParticipation'),
			array('check', 'enablePreviousNext'),
		array('rule'),
			// This is like debugging sorta.
			array('check', 'timeLoadPageEnable'),
			array('check', 'disableHostnameLookup'),
		array('rule'),
			// Who's online.
			array('check', 'who_enabled'),
		array('rule'),
			// Thread Bookmarks.
			array('check', 'show_bookmark'),
		array('heading', &$txt['mods_cat_search']),
			// Basic search settings.
			array('check', 'simpleSearch'),
			array('check', 'search_match_complete_words'),
			array('check', 'disableTemporaryTables'),
			// Number of search results displayed/cached.
		array('rule'),
			array('int', 'search_results_per_page'),
			array('int', 'search_cache_size'),
		array('rule'),
			// Search weight settings.
			array('int', 'search_weight_frequency'),
			array('int', 'search_weight_age'),
			array('int', 'search_weight_length'),
			array('int', 'search_weight_subject'),
			array('int', 'search_weight_first_message'),
		array('heading', &$txt['mods_cat_avatars']),
			// Basic avatar settings.
			array('check', 'avatar_allow_server_stored'),
			array('text', 'avatar_directory', 30),
			array('text', 'avatar_url', 30),
		array('rule'),
			// External avatars...
			array('check', 'avatar_allow_external_url'),
			array('int', 'avatar_max_width_external'),
			array('int', 'avatar_max_height_external'),
			array('check', 'avatar_check_size'),
			array('select', 'avatar_action_too_large', array(
				'option_refuse' => &$txt['option_refuse'],
				'option_html_resize' => &$txt['option_html_resize'],
				'option_download_and_resize' => &$txt['option_download_and_resize']
			)),
		array('rule'),
			// Uploaded avatars.
			array('check', 'avatar_allow_upload'),
			array('int', 'avatar_max_width_upload'),
			array('int', 'avatar_max_height_upload'),
			array('check', 'avatar_resize_upload'),
			array('check', 'avatar_download_png'),
		array('heading', &$txt['smf294']),
			// Who can do attachments?
			array('select', 'attachmentEnable', explode('|', $txt['smf111'])),
		array('rule'),
			// Extensions/images...?
			array('check', 'attachmentCheckExtensions'),
			array('text', 'attachmentExtensions'),
			array('check', 'attachmentShowImages'),
			array('check', 'attachmentEncryptFilenames'),
		array('rule'),
			// Directories and sizes/numbers.
			array('text', 'attachmentUploadDir', 30),
			array('int', 'attachmentDirSizeLimit'),
			array('int', 'attachmentPostLimit'),
			array('int', 'attachmentSizeLimit'),
			array('int', 'attachmentNumPerPostLimit'),
		array('heading', &$txt['smf293']),
			// Karma - On or off?
			array('select', 'karmaMode', explode('|', $txt['smf64'])),
		array('rule'),
			// Who can do it.... and who is restricted by time limits?
			array('int', 'karmaMinPosts'),
			array('float', 'karmaWaitTime'),
			array('check', 'karmaTimeRestrictAdmins'),
		array('rule'),
			// What does it look like?  [smite]?
			array('text', 'karmaLabel'),
			array('text', 'karmaApplaudLabel'),
			array('text', 'karmaSmiteLabel'),
		array('heading', &$txt['mods_cat_calendar']),
			// Enable the calendar?
			array('check', 'cal_enabled'),
		array('rule'),
			// How the actual calendar looks...
			array('check', 'cal_daysaslink'),
			array('check', 'cal_showweeknum'),
		array('rule'),
			// Show it on the boardindex?
			array('int', 'cal_days_for_index'),
			array('check', 'cal_showholidaysonindex'),
			array('check', 'cal_showbdaysonindex'),
			array('check', 'cal_showeventsonindex'),
		array('rule'),
			// The default board to post in... kinda alone...
			array('select', 'cal_defaultboard', $calBoards),
		array('rule'),
			// Minimum/Maximum year for events.
			array('int', 'cal_minyear'),
			array('int', 'cal_maxyear'),
		array('rule'),
			// Colors for the calendar display.
			array('text', 'cal_bdaycolor'),
			array('text', 'cal_eventcolor'),
			array('text', 'cal_holidaycolor'),
		array('rule'),
			// One day events or multi-day?
			array('check', 'cal_allowspan'),
			array('int', 'cal_maxspan'),
		array('heading', &$txt['topicsolved_title']),
			array('check', 'topicsolvedLockSolved'),
			array('check', 'topicsolvedAllowReject'),
		array('rule'),
			array('text', 'topicsolvedIconAccept'),
			array('text', 'topicsolvedIconReject'),
			array('text', 'topicsolvedIconStarter'),
			array('text', 'topicsolvedIconOther'),
		array('rule'),
			array('check', 'topicsolvedBackgroundEnabled'),
			array('text', 'topicsolvedColorAccept'),
			array('text', 'topicsolvedColorReject'),
			array('text', 'topicsolvedColorStarter'),
		array('rule'),
	);

	return $config_vars;
}

function ModifyModSettings()
{
	global $txt, $scripturl, $context, $settings, $sc;

	isAllowedTo('admin_forum');

	// Load the administration bar...
	adminIndex('edit_mods_settings');
	loadLanguage('Help');

	$config_vars = defineSettings();

	$context['page_title'] = $txt['modSettings_title'];
	$context['sub_template'] = 'rawdata';

	$context['raw_data'] = '
		<form action="' . $scripturl . '?action=modifyModSettings2" method="post" name="modsForm">
			<table width="100%" border="0" cellspacing="1" cellpadding="0" class="bordercolor" align="center">
				<tr><td>
					<table border="0" cellspacing="0" cellpadding="4" align="center" width="100%">
						<tr class="titlebg">
							<td colspan="2">
								<a href="' . $scripturl . '?action=helpadmin;help=10" onclick="return reqWin(this.href);" class="help"><img src="' . $settings['images_url'] . '/helptopics.gif" alt="' . $txt[119] . '" border="0" align="top" /></a>
								' . $txt['modSettings_title'] . '
							</td>
							<td align="right" class="smalltext">
								<a href="javascript:document.modsForm.expand.value = \'all\'; document.modsForm.submit();">' . $txt['mods_cat_expand_all'] . '</a>
							</td>
						</tr><tr class="windowbg">
							<td colspan="3" class="smalltext" style="padding: 2ex;">' . $txt['smf3'] . '</td>';

	// Show them!
	displaySettings($config_vars);

	$context['raw_data'] .= '
						</tr><tr>
							<td class="windowbg2" colspan="3" align="center" valign="middle"><input type="submit" value="' . $txt[10] . '" /></td>
						</tr>
					</table>
				</td></tr>
			</table>
			<input type="hidden" name="sc" value="' . $sc . '" />
			<input type="hidden" name="expand" value="" />
		</form>';
}

function displaySettings(&$config_vars)
{
	global $txt, $helptxt, $scripturl, $modSettings, $context, $settings;

	// If it's not yet set, default it to just 0.
	if (empty($_SESSION['expand']) || isset($_GET['collapseall']))
		$_SESSION['expand'] = serialize(array(0));
	// Otherwise take off the slashes.
	else
		$_SESSION['expand'] = stripslashes($_SESSION['expand']);

	// Get the actual array.
	$expanded = unserialize($_SESSION['expand']);

	// Expanding/Collapsing?
	if (isset($_GET['expand']))
	{
		if ($_GET['expand'] == 'all')
			$_GET['expandall'] = true;
		// Collapse.
		elseif (in_array($_GET['expand'], $expanded))
		{
			foreach ($expanded as $k => $v)
				if ($v == $_GET['expand'])
					unset($expanded[$k]);
		}
		// Expand.
		else
			$expanded = array_merge($expanded, array((int) $_GET['expand']));

		// Reset the array.
		$_SESSION['expand'] = serialize($expanded);
	}

	// Right now we are showing the boxes, etc.
	$display = true;

	// 0: type, 1: name, 2:desc, 3:helptext, 4:size/values.
	foreach ($config_vars as $k => $config_var)
	{
		if ($display)
			$context['raw_data'] .= '
						</tr>';

		// These two are referenced so much they deserve their own variables.
		$config_type = $config_var[0];
		$config_id = isset($config_var[1]) ? $config_var[1] : '';

		// A rule, no more no less - assuming we're displaying things, go for it.
		if ($config_type == 'rule' && $display)
		{
			$context['raw_data'] .= '
						<tr>
							<td class="windowbg2" colspan="3"><hr size="1" width="100%" class="hrcolor" /></td>';
			continue;
		}
		elseif ($config_type == 'heading')
		{
			// Currently not displaying... close the old cell.
			if (!$display)
				$context['raw_data'] .= '
							</td>
						</tr><tr class="titlebg">';
			else
				$context['raw_data'] .= '
						<tr class="titlebg">';

			// Show the bar with the +/-. [ +          Blah               ]...
			$context['raw_data'] .= '
							<td colspan="3" align="center">
								<a name="sect' . $k . '" />
								<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>
									<td width="16"><a href="javascript:document.modsForm.expand.value = ' . $k . '; document.modsForm.submit();"><img src="' . $settings['images_url'] . '/' . (in_array($k, $expanded) ? 'collapse.gif' : 'expand.gif') . '" alt="*" border="0" /></a></td>
									<td align="center">' . $config_id . '</td>
									<td width="16"></td>
								</tr></table>
							</td>';

			// If it's not in the expanded array, and we're not expanding everything...
			if (!in_array($k, $expanded) && !isset($_GET['expandall']))
			{
				// Turn off display.
				$display = false;

				// Start an empty box. (just so it looks better... err, I think it looks better.)
				$context['raw_data'] .= '
						</tr><tr>
							<td class="windowbg2" colspan="3" align="center">';
			}
			// Expand this and set the session var... done so collapsing after expanding all is logical.
			elseif (isset($_GET['expandall']))
				$expanded = array_merge($expanded, array($k));
			// Turn display on otherwise.
			else
				$display = true;

			continue;
		}

		// Don't do anything more if we're not displaying anything!
		if (!$display)
			continue;

		// This setting isn't set!!  Ignore it and set it to a blank value so mods are easier to write ;).
		if (!isset($modSettings[$config_id]))
			$modSettings[$config_id] = '';

		$context['raw_data'] .= '
						<tr>';

		// Show the little [?] if display is on.
		if (isset($config_var[4]) || isset($helptxt[$config_id]))
			$context['raw_data'] .= '
							<td class="windowbg2" valign="top" width="16"><a href="' . $scripturl . '?action=helpadmin;help=' . (isset($config_var[4]) ? $config_var[4] : $config_id) . '" onclick="return reqWin(this.href);" class="help"><img src="' . $settings['images_url'] . '/helptopics.gif" alt="' . $txt[119] . '" border="0" /></a></td>';
		else
			$context['raw_data'] .= '
							<td class="windowbg2"></td>';

		// Show the description... if a specific string wasn't passed, look for it... makes it easier ;).
		if ($config_id != '')
			$context['raw_data'] .= '
							<td class="windowbg2"><label for="' . $config_var[1] . '">' . (isset($config_var[3]) ? $config_var[3] : $txt[$config_id]) . '</label></td>
							<td class="windowbg2" width="50%">';

		// If it's a textbox.... (text, int, float, etc.)
		if (in_array($config_type, array('text', 'int', 'float')))
			$context['raw_data'] .= '<input type="text" id="' . $config_id . '" name="' . $config_id . '" value="' . htmlspecialchars($modSettings[$config_id]) . '"' . (isset($config_var[2]) ? ' size="' . $config_var[2] . '"' : '') . ' />';
		// If it's a password...
		elseif ($config_type == 'password')
			$context['raw_data'] .= '<input type="password" id="' . $config_id . '" name="' . $config_id . '" value="' . htmlspecialchars($modSettings[$config_id]) . '"' . (isset($config_var[2]) ? ' size="' . $config_var[2] . '"' : '') . ' />';
		// If it's a checkbox, we're gonna have to send a hidden 0 with it...
		elseif ($config_var[0] == 'check')
			$context['raw_data'] .= '<input type="hidden" name="' . $config_id . '" value="0" /><input type="checkbox" id="' . $config_id . '" name="' . $config_id . '" value="1"' . (!empty($modSettings[$config_id]) ? ' checked="checked"' : '') . ' class="check" />';
		// A select box... display on...
		elseif ($config_var[0] == 'select')
		{
			$context['raw_data'] .= '
								<select name="' . $config_id . '" id="' . $config_id . '">';

			/* Display the options.  May be:
				array('option 1', 'value' => 'option2', 'etc')
			OR	array(array('value', 'option'), array('samevalue', 'option'))
			(this way you can have two options with one value, if needed.) */
			foreach ($config_var[2] as $key => $option)
				if (is_array($option))
					$context['raw_data'] .= '
									<option value="' . $option[0] . '"' . ($option[0] == $modSettings[$config_id] ? ' selected="selected"' : '') . '>' . $option[1] . '</option>';
				else
					$context['raw_data'] .= '
									<option value="' . $key . '"' . ($key == $modSettings[$config_id] ? ' selected="selected"' : '') . '>' . $option . '</option>';

			$context['raw_data'] .= '
								</select>
							';
		}

		// End the table cell.
		$context['raw_data'] .= '</td>';
	}

	// Cleanup the expanded array again:
	if (isset($_GET['expandall']))
		$_SESSION['expand'] = serialize($expanded);

	// Gotta close it off if we weren't showing anything ;).
	if (!$display)
		$context['raw_data'] .= '
							</td>';
}

// Save the settings.
function ModifyModSettings2()
{
	global $scripturl, $db_prefix, $settings;

	// Verify the user is allowed to be here!
	isAllowedTo('admin_forum');
	checkSession();

	$setArray = array();

	// All the checkbox values....
	$config_vars = defineSettings();
	foreach ($config_vars as $var)
	{
		if (!isset($var[1]) || !isset($_POST[$var[1]]))
			continue;

		// Checkboxes!
		elseif ($var[0] == 'check')
			$setArray[$var[1]] = !empty($_POST[$var[1]]) ? '1' : '0';
		// Select boxes!
		elseif ($var[0] == 'select' && in_array($_POST[$var[1]], array_keys($var[2])))
			$setArray[$var[1]] = $_POST[$var[1]];
		// Integers!
		elseif ($var[0] == 'int')
			$setArray[$var[1]] = (int) $_POST[$var[1]];
		// Floating point!
		elseif ($var[0] == 'float')
			$setArray[$var[1]] = (float) $_POST[$var[1]];
		// Text and passwords!
		elseif ($var[0] == 'text' || $var[0] == 'password')
			$setArray[$var[1]] = $_POST[$var[1]];
	}

	updateSettings($setArray);

	updateStats('calendar');

	loadUserSettings();
	writeLog();

	// Expand something?
	if (isset($_POST['expand']) && $_POST['expand'] != '')
		redirectexit('action=modifyModSettings;expand=' . $_POST['expand'] . '#sect' . $_POST['expand']);

	// Back to administration.
	redirectexit('action=modifyModSettings');
}

?>