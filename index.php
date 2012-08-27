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

/*	This, as you have probably guessed, is the crux on which SMF functions.
	Everything should start here, so all the setup and security is done
	properly.  The most interesting part of this file is the action array in
	the smf_main() function.  It is formatted as so:

		'action-in-url' => array('Source-File.php', 'FunctionToCall'),

	Then, you can access the FunctionToCall() function from Source-File.php
	with the URL index.php?action=action-in-url.  Relatively simple, no?
*/

$forum_version = 'EosAlpha 1.0pre';

// Get everything started up...
define('SMF', 1);
define('EOSA', 1);
define('__APICOMPAT__', 0);			// if set to 1, smcFunc[] will be populated like in SMF 2

error_reporting(defined('E_STRICT') ? E_ALL | E_STRICT : E_ALL);
$time_start = microtime();

// This makes it so headers can be sent!
ob_start();

// Load the settings...
require_once(dirname(__FILE__) . '/Settings.php');

if(!isset($db_cache_api))
	$db_cache_api = 'file';

// And important includes.
require_once($sourcedir . '/CommonAPI.php');
require_once($sourcedir . '/QueryString.php');
require_once($sourcedir . '/lib/Subs.php');
require_once($sourcedir . '/Errors.php');
require_once($sourcedir . '/Load.php');
require_once($sourcedir . '/Security.php');
require_once($sourcedir . '/EosSmarty.php');

// If $maintenance is set specifically to 2, then we're upgrading or something.
if (!empty($maintenance) && $maintenance == 2)
	db_fatal_error();

// Create a variable to store some SMF specific functions in. (unused in EoS, but kept for compatibility)
$smcFunc = array();

// Initate the database connection and define some database functions to use.
loadDatabase();

// Load the settings from the settings table, and perform operations like optimizing.
reloadSettings();
// Clean the request variables, add slashes, etc.
cleanRequest();
$context = array();
$context['template_hooks']['global'] = array(
	'above' => '',
	'sidebar_top' => '',
	'sidebar_bottom' => '',
	'footer' => '',
	'header' => ''
);
$context['is_https'] = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on';
$context['jsver'] = '?v=1586';
$context['jquery_version'] = '1.7.2';
$context['multiquote_cookiename'] = 'mquote';
$context['time_now'] = time();
$context['additional_admin_errors'] = '';
$context['query_string'] = $boardurl . $_SERVER['REQUEST_URI'];
$context['sidebar_class'] = '';

// Seed the random generator.
if (empty($modSettings['rand_seed']) || mt_rand(1, 250) == 69)
	smf_seed_generator();

// Before we get carried away, are we doing a scheduled task? If so save CPU cycles by jumping out!
if (isset($_GET['scheduled']))
{
	require_once($sourcedir . '/ScheduledTasks.php');
	AutoTask();
}

// Check if compressed output is enabled, supported, and not already being done.
if (!empty($modSettings['enableCompressedOutput']) && !headers_sent())
{
	// If zlib is being used, turn off output compression.
	if (@ini_get('zlib.output_compression') == '1' || @ini_get('output_handler') == 'ob_gzhandler')
		$modSettings['enableCompressedOutput'] = '0';
	else
	{
		ob_end_clean();
		ob_start('ob_gzhandler');
	}
}

// Register an error handler.
set_error_handler('error_handler');

// Start the session. (assuming it hasn't already been.)
loadSession();

define('WIRELESS', false);
// Restore post data if we are revalidating OpenID.
if (isset($_GET['openid_restore_post']) && !empty($_SESSION['openid']['saved_data'][$_GET['openid_restore_post']]['post']) && empty($_POST))
{
	$_POST = $_SESSION['openid']['saved_data'][$_GET['openid_restore_post']]['post'];
	unset($_SESSION['openid']['saved_data'][$_GET['openid_restore_post']]);
}

call_user_func(smf_main());

// Call obExit specially; we're coming from the main area ;).
obExit(null, null, true);

// The main controlling function.
function smf_main()
{
	global $context, $modSettings, $settings, $user_info, $board, $topic, $board_info, $maintenance, $sourcedir, $backend_subdir, $db_show_debug;

	// Special case: session keep-alive, output a transparent pixel.
	if (isset($_GET['action']) && $_GET['action'] == 'keepalive')
	{
		header('Content-Type: image/gif');
		die("\x47\x49\x46\x38\x39\x61\x01\x00\x01\x00\x80\x00\x00\x00\x00\x00\x00\x00\x00\x21\xF9\x04\x01\x00\x00\x00\x00\x2C\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02\x44\x01\x00\x3B");
	}
	// Load the user's cookie (or set as guest) and load their settings.
	loadUserSettings();

	// Load the current board's information.
	loadBoard();

	// Load the current user's permissions.
	loadPermissions();
	$context['can_search'] = allowedTo('search_posts');
	$context['additional_admin_errors'] .= CacheAPI::verifyFileCache();

	// Attachments don't require the entire theme to be loaded.
	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'dlattach' && (!empty($modSettings['allow_guestAccess']) && $user_info['is_guest'])) {
		detectBrowser();
		$context['forum_name_html_safe'] = '';
  	}
	// Load the current theme.  (note that ?theme=1 will also work, may be used for guest theming.)
	else {
		loadTheme();
		EoS_Smarty::init($db_show_debug);
	}
	$user_info['notify_count'] += (!empty($context['open_mod_reports']) ? 1 : 0);
	URL::setSID();
	array_unshift($context['linktree'], array(
		'url' => URL::home(),
		'name' => $context['forum_name_html_safe']
	));
	// Check if the user should be disallowed access.
	is_not_banned();

	$context['can_see_hidden_level1'] = allowedTo('see_hidden1');
	$context['can_see_hidden_level2'] = allowedTo('see_hidden2');
	$context['can_see_hidden_level2'] = allowedTo('see_hidden2');

	// If we are in a topic and don't have permission to approve it then duck out now.
	if (!empty($topic) && empty($board_info['cur_topic_approved']) && !allowedTo('approve_posts') && ($user_info['id'] != $board_info['cur_topic_starter'] || $user_info['is_guest']))
		fatal_lang_error('not_a_topic', false);

	// Do some logging, unless this is an attachment, avatar, toggle of editor buttons, theme option, XML feed etc.
	if (empty($_REQUEST['action']) || !in_array($_REQUEST['action'], array('dlattach', 'findmember', 'jseditor', 'jsoption', 'requestmembers', 'smstats', '.xml', 'xmlhttp', 'verificationcode', 'viewquery', 'viewsmfile')))
	{
		// Log this user as online.
		writeLog();

		// Track forum statistics and hits...?
		if (!empty($modSettings['hitStats']))
			trackStats(array('hits' => '+'));
	}

	// Is the forum in maintenance mode? (doesn't apply to administrators.)
	if (!empty($maintenance) && !allowedTo('admin_forum'))
	{
		// You can only login.... otherwise, you're getting the "maintenance mode" display.
		if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'login2' || $_REQUEST['action'] == 'logout'))
		{
			require_once($sourcedir . '/LogInOut.php');
			return $_REQUEST['action'] == 'login2' ? 'Login2' : 'Logout';
		}
		// Don't even try it, sonny.
		else
		{
			require_once($sourcedir . '/lib/Subs-Auth.php');
			return 'InMaintenance';
		}
	}
	// If guest access is off, a guest can only do one of the very few following actions.
	elseif (empty($modSettings['allow_guestAccess']) && $user_info['is_guest'] && (!isset($_REQUEST['action']) || !in_array($_REQUEST['action'], array('coppa', 'login', 'login2', 'register', 'register2', 'reminder', 'activate', 'help', 'smstats', 'mailq', 'verificationcode', 'openidreturn'))))
	{
		require_once($sourcedir . '/lib/Subs-Auth.php');
		return 'KickGuest';
	}
	elseif (empty($_REQUEST['action']))
	{
		// Action and board are both empty... BoardIndex!
		if (empty($board) && empty($topic))
		{
			require_once($sourcedir . '/BoardIndex.php');
			return 'BoardIndex';
		}
		// Topic is empty, and action is empty.... MessageIndex!
		elseif (empty($topic))
		{
			require_once($sourcedir . '/MessageIndex.php');
			return 'MessageIndex';
		}
		// Board is not empty... topic is not empty... action is empty.. Display!
		else
		{
			require_once($sourcedir . '/Display.php');
			return 'Display';
		}
	}

	// Here's the monstrous $_REQUEST['action'] array - $_REQUEST['action'] => array($file, $function).
	$actionArray = array(
		'activate' => array('Register.php', 'Activate'),
		'admin' => array($backend_subdir .  '/Admin.php', 'AdminMain'),
		'announce' => array('Post.php', 'AnnounceTopic'),
		'attachapprove' => array('lib/Subs-ManageAttachments.php', 'ApproveAttach'),
		'buddy' => array('lib/Subs-Members.php', 'BuddyListToggle'),
		'calendar' => array('Calendar.php', 'CalendarMain'),
		'clock' => array('Calendar.php', 'clock'),
		'collapse' => array('BoardIndex.php', 'CollapseCategory'),
		'coppa' => array('Register.php', 'CoppaForm'),
		'credits' => array('Who.php', 'Credits'),
		'deletemsg' => array('RemoveTopic.php', 'DeleteMessage'),
		'display' => array('Display.php', 'Display'),
		'dlattach' => array('Display.php', 'Download'),
		'editpoll' => array('Poll.php', 'EditPoll'),
		'editpoll2' => array('Poll.php', 'EditPoll2'),
		'emailuser' => array('SendTopic.php', 'EmailUser'),
		'findmember' => array('lib/Subs-Auth.php', 'JSMembers'),
		'groups' => array('Groups.php', 'Groups'),
		'help' => array('Help.php', 'ShowHelp'),
		'helpadmin' => array('Help.php', 'ShowAdminHelp'),
		'im' => array('PersonalMessage.php', 'MessageMain'),
		'jseditor' => array('lib/Subs-Editor.php', 'EditorMain'),
		'jsmodify' => array('Post.php', 'JavaScriptModify'),
		'jsoption' => array($backend_subdir .  '/Themes.php', 'SetJavaScript'),
		'lock' => array('LockTopic.php', 'LockTopic'),
		'lockvoting' => array('Poll.php', 'LockVoting'),
		'login' => array('LogInOut.php', 'Login'),
		'login2' => array('LogInOut.php', 'Login2'),
		'logout' => array('LogInOut.php', 'Logout'),
		'markasread' => array('lib/Subs-Boards.php', 'MarkRead'),
		'mergetopics' => array('SplitTopics.php', 'MergeTopics'),
		'mlist' => array('Memberlist.php', 'Memberlist'),
		'moderate' => array('ModerationCenter.php', 'ModerationMain'),
		'modifycat' => array('ManageBoards.php', 'ModifyCat'),
		'movetopic' => array('MoveTopic.php', 'MoveTopic'),
		'movetopic2' => array('MoveTopic.php', 'MoveTopic2'),
		'notify' => array('Notify.php', 'Notify'),
		'notifyboard' => array('Notify.php', 'BoardNotify'),
		'openidreturn' => array('lib/Subs-OpenID.php', 'smf_openID_return'),
		'pm' => array('PersonalMessage.php', 'MessageMain'),
		'post' => array('Post.php', 'Post'),
		'post2' => array('Post.php', 'Post2'),
		'printpage' => array('Printpage.php', 'PrintTopic'),
		'profile' => array('Profile.php', 'ModifyProfile'),
		'quotefast' => array('Post.php', 'QuoteFast'),
		'quickmod' => array('MessageIndex.php', 'QuickModeration'),
		'quickmod2' => array('Display.php', 'QuickInTopicModeration'),
		'recent' => array('Recent.php', 'RecentPosts'),
		'register' => array('Register.php', 'Register'),
		'register2' => array('Register.php', 'Register2'),		
		'reminder' => array('Reminder.php', 'RemindMe'),
		'removepoll' => array('Poll.php', 'RemovePoll'),
		'removetopic2' => array('RemoveTopic.php', 'RemoveTopic2'),
		'reporttm' => array('SendTopic.php', 'ReportToModerator'),
		'requestmembers' => array('lib/Subs-Auth.php', 'RequestMembers'),
		'restoretopic' => array('RemoveTopic.php', 'RestoreTopic'),
		'search' => array('Search.php', 'PlushSearch1'),
		'search2' => array('Search.php', 'PlushSearch2'),
		'sendtopic' => array('SendTopic.php', 'EmailUser'),
		'smstats' => array('Stats.php', 'SMStats'),
		'suggest' => array('lib/Subs-Editor.php', 'AutoSuggestHandler'),
		'splittopics' => array('SplitTopics.php', 'SplitTopics'),
		'stats' => array('Stats.php', 'DisplayStats'),
		'sticky' => array('LockTopic.php', 'Sticky'),
		'theme' => array($backend_subdir . '/Themes.php', 'ThemesMain'),
		'trackip' => array('Profile-View.php', 'trackIP'),
		'about:unknown' => array('Karma.php', 'BookOfUnknown'),
		'unread' => array('Recent.php', 'UnreadTopics'),
		'unreadreplies' => array('Recent.php', 'UnreadTopics'),
		'verificationcode' => array('Register.php', 'VerificationCode'),
		'viewprofile' => array('Profile.php', 'ModifyProfile'),
		'vote' => array('Poll.php', 'Vote'),
		'viewquery' => array('ViewQuery.php', 'ViewQuery'),
		//'viewsmfile' => array($backend_subdir . '/Admin.php', 'DisplayAdminFile'),
		'who' => array('Who.php', 'Who'),
		'.xml' => array('News.php', 'ShowXmlFeed'),
		'xmlhttp' => array('Xml.php', 'XMLhttpMain'),
		'like' => array('Ratings.php', 'LikeDispatch'),
		'tags' => array('Tagging.php', 'TagsMain'),
		'astream' => array('Activities.php', 'aStreamDispatch'),
		'dismissnews' => array('Profile-Actions.php', 'DismissNews'),
		'whatsnew' => array('Recent.php', 'WhatsNew'),
	);
	// Allow modifying $actionArray easily.
	HookAPI::callHook('integrate_actions', array(&$actionArray));

	// Get the function and file to include - if it's not there, do the board index.
	if (!isset($_REQUEST['action']) || !isset($actionArray[$_REQUEST['action']]))
	{
		// Catch the action with the theme?
		if (!empty($settings['catch_action']))
		{
			require_once($sourcedir . '/' . $backend_subdir . '/Themes.php');
			return 'WrapAction';
		}

		// Fall through to the board index then...
		require_once($sourcedir . '/BoardIndex.php');
		return 'BoardIndex';
	}
	// Otherwise, it was set - so let's go to that action.
	require_once($sourcedir . '/' . $actionArray[$_REQUEST['action']][0]);
	return $actionArray[$_REQUEST['action']][1];
}
