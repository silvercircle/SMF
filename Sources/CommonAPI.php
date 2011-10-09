<?php
/**
 * %%@productname@%%
 * @copyright 2011 Alex Vie silvercircle(AT)gmail(DOT)com
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright:	2011 Simple Machines (http://www.simplemachines.org)
 * license:  	BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version %%@productversion@%%
 *
 * this is what once was in $smcFunc[], a bit simplified for utf-8 only and entity check
 * always enforced.
 *
 * it also implements the Hook API.
 */
if (!defined('SMF'))
	die('Hacking attempt...');

if (class_exists('memcached', false))
	echo "FOOO";

class commonAPI {

	private static $ent_list = '&(#\d{1,7}|quot|amp|lt|gt|nbsp);';
	private static $space_chars = '\x{A0}\x{AD}\x{2000}-\x{200F}\x{201F}\x{202F}\x{3000}\x{FEFF}';

	private static $mcached_server;

	private static function ent_check($string)
	{
		return(preg_replace('~(&#(\d{1,7}|x[0-9a-fA-F]{1,6});)~e', self::entity_fix($string), $string));
	}

	private static function entity_fix($string)
	{
		$num = substr($string, 0, 1) === 'x' ? hexdec(substr($string, 1)) : (int) $string;
		return $num < 0x20 || $num > 0x10FFFF || ($num >= 0xD800 && $num <= 0xDFFF) || $num === 0x202E || $num === 0x202D ? '' : '&#' . $num . ';';
	}

	public static function ucwords($string)
	{
		$words = preg_split('~([\s\r\n\t]+)~', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
		for ($i = 0, $n = count($words); $i < $n; $i += 2)
			$words[$i] = self::ucfirst($words[$i]);

		return implode('', $words);
	}

	public static function ucfirst($string)
	{
		return self::strtoupper(self::substr($string, 0, 1)) . self::substr($string, 1);
	}

	public static function substr($string, $start, $length = 0)
	{
		$ent_arr = preg_split('~(&#' . ('\d{1,7}') . ';|&quot;|&amp;|&lt;|&gt;|&nbsp;|.)~u', self::ent_check($string), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		return $length == 0 ? implode('', array_slice($ent_arr, $start)) : implode('', array_slice($ent_arr, $start, $length));
	}

	public static function strtolower($string)
	{
		global $sourcedir;

		if(function_exists('mb_strtolower'))
			return mb_strtolower($string, 'UTF-8');

		require_once($sourcedir . '/Subs-Charset.php');
		return utf8_strtolower($string);
	}

	public static function strtoupper($string)
	{
		global $sourcedir;

		if(function_exists('mb_strtoupper'))
			return mb_strtoupper($string, 'UTF-8');

		require_once($sourcedir . '/Subs-Charset.php');
		return utf8_strtoupper($string);
	}

	public static function truncate($string, $length)
	{
		$string = self::ent_check($string);

		preg_match('~^(' . self::$ent_list . '|.){' . self::strlen(substr($string, 0, $length)) . '}~u', $string, $matches);
		$string = $matches[0];
		while (strlen($string) > $length)
			$string = preg_replace('~(?:' . self::$ent_list . '|.)$~u', '', $string);
		return $string;
	}

	public static function strlen($string)
	{
		return strlen(preg_replace('~' . self::$ent_list . '|.~u', '_', self::ent_check($string)));
	}

	public static function htmltrim($string)
	{
		return preg_replace('~^(?:[ \t\n\r\x0B\x00' . self::$space_chars . ']|&nbsp;)+|(?:[ \t\n\r\x0B\x00' . self::$space_chars . ']|&nbsp;)+$~u', '', self::ent_check($string));
	}

	public static function htmlspecialchars($string, $quote_style = ENT_COMPAT, $charset = 'UTF-8')
	{
		return preg_replace(strtr('~(&#(\d{1,7}|x[0-9a-fA-F]{1,6});)~e', array('&' => '&amp;')), self::entity_fix($string), htmlspecialchars($string, $quote_style, 'UTF-8'));
	}

	public static function strpos($haystack, $needle, $offset = 0)
	{
		$haystack_arr = preg_split('~(&#\d{1,7}' . ';|&quot;|&amp;|&lt;|&gt;|&nbsp;|.)~u', self::ent_check($haystack), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		$haystack_size = count($haystack_arr);
		if (strlen($needle) === 1)
		{
			$result = array_search($needle, array_slice($haystack_arr, $offset));
			return is_int($result) ? $result + $offset : false;
		}
		else
		{
			$needle_arr = preg_split('~(&#\d{1,7}' . ';|&quot;|&amp;|&lt;|&gt;|&nbsp;|.)~u',  self::ent_check($needle), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
			$needle_size = count($needle_arr);

			$result = array_search($needle_arr[0], array_slice($haystack_arr, $offset));
			while (is_int($result))
			{
				$offset += $result;
				if (array_slice($haystack_arr, $offset, $needle_size) === $needle_arr)
					return $offset;
				$result = array_search($needle_arr[0], array_slice($haystack_arr, ++$offset));
			}
			return false;
		}
	}

	public static function getMemcachedServer()
	{
		global $modSettings;

		if (is_a(self::$mcached_server, 'Memcached'))
			return self::$mcached_server;

		$servers = explode(',', $modSettings['cache_memcached']);

		self::$mcached_server = new Memcached();
		if (0 == count(self::$mcached_server->getServerList()) )
		{
			$h = array();
			foreach($servers as $server) {
				$server = explode( ':', trim($server));
				$ip     = $server[0];
				$port   = empty($server[1]) ? 11211 : $server[1];
				$h[] = array($ip, $port);
			}
			self::$mcached_server->addServers($h);
		}
		return self::$mcached_server;
	}

}

/**
 * implements the new hook API
 *
 * all hooks are now stored in a single array. each top level array element defines a single
 * hook identified by its name.
 */
class HookAPI {
	private static $hooks = array();

	public static function setHooks(&$the_hooks)
	{
		self::$hooks = @unserialize($the_hooks);
	}

	public static function addHook($hook, $product, $file, $function)
	{
		$ref = array('p' => $product, 'f' => $file, 'c' => $function);

		if(isset(self::$hooks[$hook]) && is_array(self::$hooks[$hook])) {
			foreach(self::$hooks[$hook] as $current_hook) {
				if($current_hook == $ref)
					return;
			}
		}
		self::$hooks[$hook][] = array('p' => $product, 'f' => $file, 'c' => $function);
		$change_array = array('integration_hooks' => serialize(self::$hooks));
		updateSettings($change_array, true);
	}

	// Process functions of an integration hook.
	public static function callHook($hook, $parameters = array())
	{
		global $boarddir;

		$results = array();

		if(isset(self::$hooks[$hook]) && is_array(self::$hooks[$hook])) {
			foreach(self::$hooks[$hook] as $current_hook) {
				@include_once($boarddir . '/addons/' . $current_hook['p'] . '/' . $current_hook['f']);
				$function = trim($current_hook['c']);
				if(is_callable($function))
					$results[$function] = call_user_func_array($function, $parameters);
			}
		}
		return $results;
	}

	public static function removeHook($hook, $product, $file, $function)
	{
		$ref = array('p' => $product, 'f' => $file, 'c' => $function);

		if(isset(self::$hooks[$hook]) && is_array(self::$hooks[$hook])) {
			foreach(self::$hooks[$hook] as $key => $current_hook) {
				if($current_hook == $ref) {
					unset(self::$hooks[$hook][$key]);
					if(0 == count(self::$hooks[$hook]))
						unset(self::$hooks[$hook]);
					$change_array = array('integration_hooks' => serialize(self::$hooks));
					updateSettings($change_array, true);
					return;
				}
			}
		}
	}

	/**
	 * @static
	 * @param $product		string
	 *
	 * remove all hooks related to the product given in $product
	 * product name is CASE SENSITIVE
	 */
	public static function removeAll($product)
	{
		$changed = false;

		foreach(self::$hooks as $k => $hooks) {
			foreach($hooks as $n => $hook) {
				if($hook['p'] == $product) {
					unset(self::$hooks[$k][$n]);
					$changed = true;
				}
			}
			if(0 == count(self::$hooks[$k]))
				unset(self::$hooks[$k]);
		}
		if($changed) {
			$change_array = array('integration_hooks' => serialize(self::$hooks));
			updateSettings($change_array, true);
		}
	}
}
?>
