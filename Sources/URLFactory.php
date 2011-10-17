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
global $modSettings;
if(!empty($modSettings['simplesef_enable'])) {
//if(0) {
	class URL {

		private static $boardurl = '';
		private static $scripturl = '';
		private static $sep = '-';

		private static $boardnames = array();
		private static $topicnames = array();
		private static $usernames = array();

		public static function init($b, $s)
		{
			self::$boardurl = $b;
			self::$scripturl = $s;
		}

		public static function topic($topicid, $topicname, $start = 0, $boardname = '', $boardid = 0, $force_start = true, $msgfragment = '', $a = '')
		{
			$_id = (int)$topicid;
			if(!isset(self::$topicnames[$_id]))
				self::$topicnames[$_id] = SimpleSEF::encode($topicname);

			if(!isset(self::$boardnames[$boardid]))
				self::$boardnames[$boardid] = SimpleSEF::encode($boardname);

			return(self::$boardurl . '/' . self::$boardnames[$boardid] . '.'.$boardid.'/' . self::$topicnames[$_id] . self::$sep . $_id . ($start > 0 || $force_start ? ('.' . $start) : '') . $msgfragment . '.html' . $a);
		}

		public static function user($id, $name)
		{
			$_id = (int)$id;
			if(!isset(self::$usernames[$_id]))
				self::$usernames[$_id] = SimpleSEF::encode($name);
			return(self::$boardurl . '/profile/' . self::$usernames[$_id] . self::$sep . $_id);
		}

		public static function board($id, $boardname, $start = 0, $force_start = false)
		{
			$_id = (int)$id;
			if(!isset(self::$boardnames[$_id]))
				self::$boardnames[$_id] = SimpleSEF::encode($boardname);
			return(self::$boardurl . '/' . self::$boardnames[$_id] . '.' . trim($id) . ($start > 0 || $force_start ? ('-' . $start) : ''));
		}

		public static function msg($topicid, $topicname, $msgid, $boardname = '', $boardid = 0, $start, $force_start = false)
		{
			$_id = (int)$topicid;
			//if(!isset(self::$topicnames[$_id]))
			//	self::$topicnames[$_id] = SimpleSEF::encode($topicname);

			if(!isset(self::$boardnames[$boardid]))
				self::$boardnames[$boardid] = SimpleSEF::encode($boardname);

			return(self::$boardurl . '/' . self::$boardnames[$boardid] . '/' .SimpleSEF::encode($topicname) . self::$sep . $_id . ($start > 0 || $force_start ? ('.' . $start) : '')  . '.msg' . $msgid . '.html');
		}
		public static function home()
		{
			return(self::$boardurl . '/');
		}
	}
}
else {
	class URL {

		private static $boardurl = '';
		private static $scripturl = '';

		public static function init($b, $s)
		{
			self::$boardurl = $b;
			self::$scripturl = $s;
		}

		public static function topic($topicid, $topicname, $start = 0, $boardname = '', $boardid = 0, $force_start = true, $msgfragment = '', $a = '')
		{
			return(self::$scripturl . '?topic=' . (int)$topicid . ($start > 0 || $force_start ? ('.' . $start) : '') . $msgfragment . $a);
		}

		public static function user($id, $name)
		{
			return(self::$scripturl . '?action=profile;u=' . (int)$id);
		}

		public static function board($id, $name, $start = 0, $force_start = false)
		{
			return(self::$scripturl . '?board=' . (int)$id . '.' . (int)$start);
		}

		public static function msg($topicid, $topicname, $msgid, $boardname = '', $boardid = 0)
		{
			return(self::$scripturl . '?topic=' . (int)$topicid . '.msg' . (int)$msgid);
		}

		public static function home()
		{
			return(self::$scripturl);
		}
	}
}
?>