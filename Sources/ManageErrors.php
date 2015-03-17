<?php
/**
 * @name      EosAlpha BBS
 * @copyright 2015 Alex Vie silvercircle(AT)gmail(DOT)com
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright:	2011 Simple Machines (http://www.simplemachines.org)
 * license:  	BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version 1.0pre
 */
if (!defined('SMF'))
	die('Hacking attempt...');

/* Show a list of all errors that were logged on the forum.

	void ViewErrorLog()
		- sets all the context up to show the error log for maintenance.
		- uses the Errors template and error_log sub template.
		- requires the maintain_forum permission.
		- uses the 'view_errors' administration area.
		- accessed from ?action=admin;area=logs;sa=errorlog.

	void deleteErrors()
		- deletes all or some of the errors in the error log.
		- applies any necessary filters to deletion.
		- should only be called by ViewErrorLog().
		- attempts to TRUNCATE the table to reset the auto_increment.
		- redirects back to the error log when done.

	void ViewFile()
		- will do php highlighting on the file specified in $_REQUEST['file']
		- file must be readable
		- full file path must be base64 encoded
		- user must have admin_forum permission
		- the line number number is specified by $_REQUEST['line']
		- Will try to get the 20 lines before and after the specified line
*/

// View the forum's error log.
function ViewErrorLog()
{
	global $scripturl, $txt, $context, $modSettings, $user_profile, $filter, $boarddir, $sourcedir, $themedir, $smcFunc;

	// Viewing contents of a file?
	if (isset($_GET['file']))
		return ViewFile();

	// Check for the administrative permission to do this.
	isAllowedTo('admin_forum');

	// Templates, etc...
	loadLanguage('ManageMaintenance');
	loadAdminTemplate('Errors');

	// You can filter by any of the following columns:
	$filters = array(
		'id_member' => $txt['username'],
		'ip' => $txt['ip_address'],
		'session' => $txt['session'],
		'url' => $txt['error_url'],
		'message' => $txt['error_message'],
		'error_type' => $txt['error_type'],
		'file' => $txt['file'],
		'line' => $txt['line'],
	);

	// Set up the filtering...
	if (isset($_GET['value'], $_GET['filter']) && isset($filters[$_GET['filter']]))
		$filter = array(
			'variable' => $_GET['filter'],
			'value' => array(
				'sql' => in_array($_GET['filter'], array('message', 'url', 'file')) ? base64_decode(strtr($_GET['value'], array(' ' => '+'))) : smf_db_escape_wildcard_string($_GET['value']),
			),
			'href' => ';filter=' . $_GET['filter'] . ';value=' . $_GET['value'],
			'entity' => $filters[$_GET['filter']]
		);

	// Deleting, are we?
	if (isset($_POST['delall']) || isset($_POST['delete']))
		deleteErrors();

	// Just how many errors are there?
	$result = smf_db_query( '
		SELECT COUNT(*)
		FROM {db_prefix}log_errors' . (isset($filter) ? '
		WHERE ' . $filter['variable'] . ' LIKE {string:filter}' : ''),
		array(
			'filter' => isset($filter) ? $filter['value']['sql'] : '',
		)
	);
	list ($num_errors) = mysql_fetch_row($result);
	mysql_free_result($result);

	// If this filter is empty...
	if ($num_errors == 0 && isset($filter))
		redirectexit('action=admin;area=logs;sa=errorlog' . (isset($_REQUEST['desc']) ? ';desc' : ''));

	// Clean up start.
	if (!isset($_GET['start']) || $_GET['start'] < 0)
		$_GET['start'] = 0;

	// Do we want to reverse error listing?
	$context['sort_direction'] = isset($_REQUEST['desc']) ? 'down' : 'up';

	// Set the page listing up.
	$context['page_index'] = constructPageIndex($scripturl . '?action=admin;area=logs;sa=errorlog' . ($context['sort_direction'] == 'down' ? ';desc' : '') . (isset($filter) ? $filter['href'] : ''), $_GET['start'], $num_errors, $modSettings['defaultMaxMessages']);
	$context['start'] = $_GET['start'];

	// Find and sort out the errors.
	$request = smf_db_query( '
		SELECT id_error, id_member, ip, url, log_time, message, session, error_type, file, line
		FROM {db_prefix}log_errors' . (isset($filter) ? '
		WHERE ' . $filter['variable'] . ' LIKE {string:filter}' : '') . '
		ORDER BY id_error ' . ($context['sort_direction'] == 'down' ? 'DESC' : '') . '
		LIMIT ' . $_GET['start'] . ', ' . $modSettings['defaultMaxMessages'],
		array(
			'filter' => isset($filter) ? $filter['value']['sql'] : '',
		)
	);
	$context['errors'] = array();
	$members = array();

	for ($i = 0; $row = mysql_fetch_assoc($request); $i ++)
	{
		$search_message = preg_replace('~&lt;span class=&quot;remove&quot;&gt;(.+?)&lt;/span&gt;~', '%', smf_db_escape_wildcard_string($row['message']));
		if ($search_message == $filter['value']['sql'])
			$search_message = smf_db_escape_wildcard_string($row['message']);
		$show_message = strtr(strtr(preg_replace('~&lt;span class=&quot;remove&quot;&gt;(.+?)&lt;/span&gt;~', '$1', $row['message']), array("\r" => '', '<br />' => "\n", '<' => '&lt;', '>' => '&gt;', '"' => '&quot;')), array("\n" => '<br />'));

		$context['errors'][$row['id_error']] = array(
			'alternate' => $i %2 == 0,
			'member' => array(
				'id' => $row['id_member'],
				'ip' => $row['ip'],
				'session' => $row['session']
			),
			'time' => timeformat($row['log_time']),
			'timestamp' => $row['log_time'],
			'url' => array(
				'html' => htmlspecialchars((substr($row['url'], 0, 1) == '?' ? $scripturl : '') . $row['url']),
				'href' => base64_encode(smf_db_escape_wildcard_string($row['url']))
			),
			'message' => array(
				'html' => $show_message,
				'href' => base64_encode($search_message)
			),
			'id' => $row['id_error'],
			'error_type' => array(
				'type' => $row['error_type'],
				'name' => isset($txt['errortype_'.$row['error_type']]) ? $txt['errortype_'.$row['error_type']] : $row['error_type'],
			),
			'file' => array(),
		);
		if (!empty($row['file']) && !empty($row['line']))
		{
			// Eval'd files rarely point to the right location and cause havoc for linking, so don't link them.
			$linkfile = strpos($row['file'], 'eval') === false || strpos($row['file'], '?') === false; // De Morgan's Law.  Want this true unless both are present.

			$matches = array();
			preg_match('~(.*\.php).*~', $row['file'], $matches);
			$context['errors'][$row['id_error']]['file'] = array(
				'file' => $row['file'],
				'line' => $row['line'],
				'href' => $scripturl . '?action=admin;area=logs;sa=errorlog;file=' . base64_encode($matches[1]) . ';line=' . $row['line'],
				'link' => $linkfile ? '<a href="' . $scripturl . '?action=admin;area=logs;sa=errorlog;file=' . base64_encode($matches[1]) . ';line=' . $row['line'] . '" onclick="return showFile(\''.base64_encode($matches[1]).'\', '.$row['line'].');return(false);">' . $row['file'] . '</a>' : $row['file'],
				'search' => base64_encode($row['file']),
			);
		}

		// Make a list of members to load later.
		$members[$row['id_member']] = $row['id_member'];
	}
	mysql_free_result($request);

	// Load the member data.
	if (!empty($members))
	{
		// Get some additional member info...
		$request = smf_db_query( '
			SELECT id_member, member_name, real_name
			FROM {db_prefix}members
			WHERE id_member IN ({array_int:member_list})
			LIMIT ' . count($members),
			array(
				'member_list' => $members,
			)
		);
		while ($row = mysql_fetch_assoc($request))
			$members[$row['id_member']] = $row;
		mysql_free_result($request);

		// This is a guest...
		$members[0] = array(
			'id_member' => 0,
			'member_name' => '',
			'real_name' => $txt['guest_title']
		);

		// Go through each error and tack the data on.
		foreach ($context['errors'] as $id => $dummy)
		{
			$memID = $context['errors'][$id]['member']['id'];
			$context['errors'][$id]['member']['username'] = $members[$memID]['member_name'];
			$context['errors'][$id]['member']['name'] = $members[$memID]['real_name'];
			$context['errors'][$id]['member']['href'] = empty($memID) ? '' : $scripturl . '?action=profile;u=' . $memID;
			$context['errors'][$id]['member']['link'] = empty($memID) ? $txt['guest_title'] : '<a href="' . $scripturl . '?action=profile;u=' . $memID . '">' . $context['errors'][$id]['member']['name'] . '</a>';
		}
	}

	// Filtering anything?
	if (isset($filter))
	{
		$context['filter'] = &$filter;

		// Set the filtering context.
		if ($filter['variable'] == 'id_member')
		{
			$id = $filter['value']['sql'];
			loadMemberData($id, false, 'minimal');
			$context['filter']['value']['html'] = '<a href="' . $scripturl . '?action=profile;u=' . $id . '">' . $user_profile[$id]['real_name'] . '</a>';
		}
		elseif ($filter['variable'] == 'url')
			$context['filter']['value']['html'] = '\'' . strtr(htmlspecialchars((substr($filter['value']['sql'], 0, 1) == '?' ? $scripturl : '') . $filter['value']['sql']), array('\_' => '_')) . '\'';
		elseif ($filter['variable'] == 'message')
		{
			$context['filter']['value']['html'] = '\'' . strtr(htmlspecialchars($filter['value']['sql']), array("\n" => '<br />', '&lt;br /&gt;' => '<br />', "\t" => '&nbsp;&nbsp;&nbsp;', '\_' => '_', '\\%' => '%', '\\\\' => '\\')) . '\'';
			$context['filter']['value']['html'] = preg_replace('~&amp;lt;span class=&amp;quot;remove&amp;quot;&amp;gt;(.+?)&amp;lt;/span&amp;gt;~', '$1', $context['filter']['value']['html']);
		}
		elseif ($filter['variable'] == 'error_type')
		{
			$context['filter']['value']['html'] = '\'' . strtr(htmlspecialchars($filter['value']['sql']), array("\n" => '<br />', '&lt;br /&gt;' => '<br />', "\t" => '&nbsp;&nbsp;&nbsp;', '\_' => '_', '\\%' => '%', '\\\\' => '\\')) . '\'';
		}
		else
			$context['filter']['value']['html'] = &$filter['value']['sql'];
	}

	$context['error_types'] = array();

	$context['error_types']['all'] = array(
		'label' => $txt['errortype_all'],
		'description' => isset($txt['errortype_all_desc']) ? $txt['errortype_all_desc'] : '',
		'url' => $scripturl . '?action=admin;area=logs;sa=errorlog' . ($context['sort_direction'] == 'down' ? ';desc' : ''),
		'is_selected' => empty($filter),
	);

	$sum = 0;
	// What type of errors do we have and how many do we have?
	$request = smf_db_query( '
		SELECT error_type, COUNT(*) AS num_errors
		FROM {db_prefix}log_errors
		GROUP BY error_type
		ORDER BY error_type = {string:critical_type} DESC, error_type ASC',
		array(
			'critical_type' => 'critical',
		)
	);
	while ($row = mysql_fetch_assoc($request))
	{
		// Total errors so far?
		$sum += $row['num_errors'];

		$context['error_types'][$sum] = array(
			'label' => (isset($txt['errortype_' . $row['error_type']]) ? $txt['errortype_' . $row['error_type']] : $row['error_type']) . ' (' . $row['num_errors'] . ')',
			'description' => isset($txt['errortype_' . $row['error_type'] . '_desc']) ? $txt['errortype_' . $row['error_type'] . '_desc'] : '',
			'url' => $scripturl . '?action=admin;area=logs;sa=errorlog' . ($context['sort_direction'] == 'down' ? ';desc' : '') . ';filter=error_type;value=' . $row['error_type'],
			'is_selected' => isset($filter) && $filter['value']['sql'] == smf_db_escape_wildcard_string($row['error_type']),
		);
	}
	mysql_free_result($request);

	// Update the all errors tab with the total number of errors
	$context['error_types']['all']['label'] .= ' (' . $sum . ')';

	// Finally, work out what is the last tab!
	if (isset($context['error_types'][$sum]))
		$context['error_types'][$sum]['is_last'] = true;
	else
		$context['error_types']['all']['is_last'] = true;

	// And this is pretty basic ;).
	$context['page_title'] = $txt['errlog'];
	$context['has_filter'] = isset($filter);
	$context['sub_template'] = 'error_log';
}

// Delete errors from the database.
function deleteErrors()
{
	global $filter, $smcFunc;

	// Make sure the session exists and is correct; otherwise, might be a hacker.
	checkSession();

	// Delete all or just some?
	if (isset($_POST['delall']) && !isset($filter))
		smf_db_query('
			TRUNCATE {db_prefix}log_errors',
			array(
			)
		);
	// Deleting all with a filter?
	elseif (isset($_POST['delall']) && isset($filter))
		smf_db_query( '
			DELETE FROM {db_prefix}log_errors
			WHERE ' . $filter['variable'] . ' LIKE {string:filter}',
			array(
				'filter' => $filter['value']['sql'],
			)
		);
	// Just specific errors?
	elseif (!empty($_POST['delete']))
	{
		smf_db_query( '
			DELETE FROM {db_prefix}log_errors
			WHERE id_error IN ({array_int:error_list})',
			array(
				'error_list' => array_unique($_POST['delete']),
			)
		);

		// Go back to where we were.
		redirectexit('action=admin;area=logs;sa=errorlog' . (isset($_REQUEST['desc']) ? ';desc' : '') . ';start=' . $_GET['start'] . (isset($filter) ? ';filter=' . $_GET['filter'] . ';value=' . $_GET['value'] : ''));
	}

	// Back to the error log!
	redirectexit('action=admin;area=logs;sa=errorlog' . (isset($_REQUEST['desc']) ? ';desc' : ''));
}

/**
 * Highlight any code.
 *
 * Uses PHP's highlight_string() to highlight PHP syntax
 * does special handling to keep the tabs in the code available.
 * used to parse PHP code from inside [code] and [php] tags.
 *
 * @param string $code
 * @return string the code with highlighted HTML.
 */
function highlight_php_code($code)
{
	global $context;

	// Remove special characters.
	$code = un_htmlspecialchars(strtr($code, array('<br />' => "\n", "\t" => 'SMF_TAB();', '&#91;' => '[')));

	$oldlevel = error_reporting(0);

	$buffer = str_replace(array("\n", "\r"), '', @highlight_string($code, true));

	error_reporting($oldlevel);

	// Yes, I know this is kludging it, but this is the best way to preserve tabs from PHP :P.
	$buffer = preg_replace('~SMF_TAB(?:</(?:font|span)><(?:font color|span style)="[^"]*?">)?\\(\\);~', '<pre style="display: inline;">' . "\t" . '</pre>', $buffer);

	return strtr($buffer, array('\'' => '&#039;', '<code>' => '', '</code>' => ''));
}

function ViewFile()
{
	global $context, $txt, $boarddir, $sourcedir;
	// Check for the administrative permission to do this.
	isAllowedTo('admin_forum');

	$context['need_synhlt'] = true;

	// decode the file and get the line
	$file = base64_decode($_REQUEST['file']);
	$line = isset($_REQUEST['line']) ? (int) $_REQUEST['line'] : 0;

	// Make sure the file we are looking for is one they are allowed to look at
	if (!is_readable($file) || (strpos($file, '../') !== false && ( strpos($file, $boarddir) === false || strpos($file, $sourcedir) === false)))
		fatal_lang_error('error_bad_file', true, array(htmlspecialchars($file)));

	// get the min and max lines
	$min = $line - 20 <= 0 ? 1 : $line - 20;
	$max = $line + 21; // One additional line to make everything work out correctly

	if ($max <= 0 || $min >= $max)
		fatal_lang_error('error_bad_line');

	$file_data = explode('<br />', highlight_php_code(htmlspecialchars(implode('', file($file)))));

	// We don't want to slice off too many so lets make sure we stop at the last one
	$max = min($max, max(array_keys($file_data)));

	$file_data = array_slice($file_data, $min-1, $max - $min);

	$context['file_data'] = array(
		'contents' => $file_data,
		'min' => $min,
		'target' => $line,
		'file' => strtr($file, array('"' => '\\"')),
	);
	Eos_Smarty::loadTemplate('admin/errors_show_file');
	//loadAdminTemplate('Errors');
}
