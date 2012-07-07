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

/**
 * test plugin for EoS alpha
 * demonstrates how to add a side bar to the message index
 *
 * plugin adds a side bar to the message index, showing:
 * 1) 5 most recent posts in the board
 * 2) 5 most recent events in the board (posts, ratings, new topics etc.)
 */
function testplugin_autoloader()
{
	return new TestPlugin();
}

class TestPlugin extends EoS_Plugin
{
	protected $productShortName = 'testplugin';
	protected $installableHooks = array(
		'messageindex' => array('file' => 'main.php', 'callable' => 'TestPlugin::messageindex'),
		'astream_event_added' => array('file' => 'main.php', 'callable' => 'TestPlugin::EventAdded'),
		'create_topic' => array('file' => 'main.phhp', 'callable' => 'TestPlugin::PostAdded'),
		'update_topic' => array('file' => 'main.phhp', 'callable' => 'TestPlugin::PostAdded')
	);

	protected $cacheName_Events = 'testplugin-messageindex-events';
	protected $cacheName_Posts = 'testplugin-messageindex-posts';
	
	protected static $mydata = array();

	public function __construct() { parent::__construct(); }	// mandatory

	/*
	 * runs in messageindex.php
	 * 1) fetch our data
	 * 2) allow side bar
	 * 3) specify side bar template to use
	 */
	public static function messageindex(&$board_info)
	{
		global $context, $txt, $sourcedir, $user_info;
		loadLanguage('Activities');

		// add our plugin directory to the list of directories to search for templates.
		EoS_Smarty::addTemplateDir(dirname(__FILE__));
		// register two hook templates for the side bar top and bottom areas
		EoS_Smarty::getConfigInstance()->registerHookTemplate('sidebar_top', 'testplugin_sidebar_top');
		EoS_Smarty::getConfigInstance()->registerHookTemplate('sidebar_bottom', 'testplugin_sidebar_bottom');
		// register some global variable (that's optional though, it would be totally ok to use $context)
		// You should always assignByRef(), because it's faster and doesn't create a copy
		// of the variable
		EoS_Smarty::getSmartyInstance()->assignByRef('MYDATA', self::$mydata);
		// enable side bar in the message index display
		if($user_info['is_admin'] && $board_info['allow_topics']) {
			$context['show_sidebar'] = true;
			$context['sidebar_template'] = 'sidebars/sidebar_on_messageindex.tpl';
			$context['sidebar_class'] = 'messageindex';
			GetSidebarVisibility('messageindex');
		}
		else
			$context['show_sidebar'] = false;

		$ignoreusers = !empty($user_info['ignoreusers']) ? $user_info['ignoreusers'] : array(0);
		// .. and set the name of the template
		self::$mydata['testvalue'] = 'Foo';
		@require_once($sourcedir . '/lib/Subs-Activities.php');
		$context['act_global'] = false;
		$request = smf_db_query('SELECT a.*, t.*, b.name AS board_name FROM {db_prefix}log_activities AS a
				LEFT JOIN {db_prefix}activity_types AS t ON (t.id_type = a.id_type)
				LEFT JOIN {db_prefix}boards AS b ON (b.id_board = a.id_board)
				WHERE a.id_board = {int:id_board} AND a.id_member NOT IN({array_int:ignoredusers}) LIMIT 5',
				array('id_board' => $context['current_board'], 'ignoredusers' => $ignoreusers)
		);
		aStreamOutput($request, false, true);
		if(isset($context['activities'])) {
			uasort($context['activities'], function($a, $b) {
	    		if ($a['updated'] == $b['updated'])
	        		return 0;
	    		return ($a['updated'] < $b['updated']) ? -1 : 1;
			});
		}
	}

	/*
	 * runs when a new activity is added to the stream. Simply clear out our
	 * cached copy
	 */
	public static function EventAdded(&$eventData)
	{
		if($eventData['id_board'])				// the event references a board event
			CacheAPI::putCache(self::$cacheName_Events, null, 0);
	}

	/*
	 * runs when a topic is created or updated (i.e. posts were added)
	 */
	public static function PostAdded(&$msgOptions, &$topicOptions, &$posterOptions)
	{
		CacheAPI::putCache(self::$cacheName_Posts, null, 0);
	}
}

?>