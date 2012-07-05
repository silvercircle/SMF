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
		'astream_event_added' => array('file' => 'main.php', 'callable' => 'TestPlugin::EventAdded')
	);

	protected $cacheName_Events = 'testplugin-messageindex-events';
	protected $cacheName_Posts = 'testplugin-messageindex-posts';
	
	public function __construct() { parent::__construct(); }	// mandatory

	/*
	 * runs in messageindex.php
	 * 1) fetch our data
	 * 2) allow side bar
	 * 3) specify side bar template to use
	 */
	public static function messageindex()
	{
		global $context, $txt;

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

	public static function PostAdded(&$eventData)
	{
		
	}
}

?>