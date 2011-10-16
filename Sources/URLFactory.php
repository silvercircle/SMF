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
	class URL {

		private static $boardurl = '';
		private static $scripturl = '';
		private static $sep = '-';

		public static function init($b, $s)
		{
			self::$boardurl = $b;
			self::$scripturl = $s;
		}

		public static function topic($id, $name, $start = 0, $boardname)
		{
			return(self::$boardurl . '/' . SimpleSEF::encode($name) . self::$sep . $id . '.' . $start . '.html');
		}

		public static function user($id, $name)
		{
			return(self::$boardurl . '/profile/' . SimpleSEF::encode($name) . self::$sep . $id);
		}

		public static function board($id, $name, $start = 0, $force_start = false)
		{
			return(self::$boardurl . '/' . SimpleSEF::encode($name) . ($start > 0 || $force_start ? ('.' . (int)$start) : ''));
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

		public static function topic($id, $name, $start = 0, $force_start = false, $boardname = '')
		{
			return(self::$scripturl . '?topic=' . (int)$id . '.' . $start);
		}

		public static function user($id, $name)
		{
			return(self::$scripturl . '?action=profile;u=' . (int)$id);
		}

		public static function board($id, $name, $start = 0, $force_start = false)
		{
			return(self::$scripturl . '?board=' . (int)$id . '.' . (int)$start);
		}
	}
}
?>