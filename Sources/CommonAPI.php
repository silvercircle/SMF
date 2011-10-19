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
 *
 * this is what once was in $smcFunc[], a bit simplified for utf-8 only and entity check
 * always enforced.
 *
 * it also implements the Hook API.
 */
if (!defined('SMF'))
	die('No access');

class commonAPI {

	private static $ent_list = '&(#\d{1,7}|quot|amp|lt|gt|nbsp);';
	private static $space_chars = '\x{A0}\x{AD}\x{2000}-\x{200F}\x{201F}\x{202F}\x{3000}\x{FEFF}';

	private static function ent_check($string)
	{
		return(preg_replace('~(&#(\d{1,7}|x[0-9a-fA-F]{1,6});)~e', self::entity_fix($string), $string));
	}

	public static function entity_fix($string)
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
}

/**
 * implements the new hook API
 *
 * all hooks are now stored in a single array. each top level array element defines a single
 * hook identified by its name.
 */
class HookAPI {
	private static $hooks = array();

	/**
	 * @param $the_hooks	string - serialized array of hooks
	 *
	 * initialize the hooks
	 * this must be called immediately after loading modSettings[] from the database
	 */
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

	public static function integrateOB()
	{
		global $boarddir;

		if(isset(self::$hooks['integrate_buffer'])) {
			foreach(self::$hooks['integrate_buffer'] as $current_hook) {
				@include_once($boarddir . '/addons/' . $current_hook['p'] . '/' . $current_hook['f']);
				$function = trim($current_hook['c']);
				if(is_callable($function))
					ob_start($function);
			}
		}
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

class cacheAPI {

	private static $API = -1;
	private static $mcached_server;
	private static $basekey = '';
	private static $memcached = 0;

	private static $cache_hits = array();
	private static $cache_count = 0;

	private static $memcached_hosts = '';
	/**
	 * support for PECL new memcacheD
	 */
	private static function getMemcachedServer()
	{
		if (is_a(self::$mcached_server, 'Memcached'))
			return self::$mcached_server;

		$servers = explode(',', self::$memcached_hosts);
		self::$mcached_server = new Memcached();
		if (0 == count(self::$mcached_server->getServerList()) ) {
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

	/**
	 * @param int $level	caching level
	 *
	 * get server for the OLD memcache implementation
	 */
	private static function getMemcacheServer($level = 3)
	{
		global $modSettings, $db_persist;

		$servers = explode(',', $modSettings['cache_memcached']);
		$server = explode(':', trim($servers[array_rand($servers)]));

		// Don't try more times than we have servers!
		$level = min(count($servers), $level);

		// Don't wait too long: yes, we want the server, but we might be able to run the query faster!
		if (empty($db_persist))
			self::$memcached = memcache_connect($server[0], empty($server[1]) ? 11211 : $server[1]);
		else
			self::$memcached = memcache_pconnect($server[0], empty($server[1]) ? 11211 : $server[1]);

		if (!self::$memcached && $level > 0)
			self::getMemcacheServer($level - 1);
	}

	public static function cacheInit($desired, $basekey, $memcached_hosts)
	{
		self::$basekey = $basekey;
		self::$memcached_hosts = $memcached_hosts;

		if($desired == 'apc' && function_exists('apc_store'))
		    self::$API = 1;
		elseif($desired == 'xcache' && function_exists('xcache_get') && ini_get('xcache.var_size') > 0)
			self::$API = 2;
		elseif($desired == 'zend' && function_exists('output_cache_get'))
			self::$API = 3;
		elseif($desired == 'memcache' && function_exists('memcache_get'))
			self::$API = 4;
		elseif($desired == 'new_memcache' && class_exists('Memcached'))
			self::$API = 5;
		elseif($desired == 'file')
			self::$API = 0;

		// check for possible cache configuration errors
		//if(((self::$API == 4 || self::$API == 5) && empty(self::$memcached_hosts)) || self::$API == -1)
		//	log_error(sprintf('cacheInit: desired caching system unsupported or not available (desired = %s, memcached hosts = %s', $desired, self::$memcached_hosts));
	}

	public static function disable()
	{
		self::$API = -1;
	}

	public static function getEngine()
	{
		$engines = array('Filesystem cache', 'APC', 'Xcache', 'Zend', 'Memcached', 'New PECL Memcached');

		if(-1 == self::$API)
			return('Caching is disabled or not available');
		else
			return($engines[(int)self::$API]);
	}

	public static function getCache($key, $ttl = 120)
	{
		global $db_show_debug, $cachedir;

		if(-1 == self::$API)
			return(null);

		self::$cache_count++;
		if (isset($db_show_debug) && $db_show_debug === true) {
			self::$cache_hits[self::$cache_count] = array('k' => $key, 'd' => 'get');
			$st = microtime();
		}

		$key = self::$basekey . strtr($key, ':', '-');

		switch(self::$API) {
			case 5:
				$key = str_replace(' ', '_', $key);

				$instance = self::getMemcachedServer();
				$value = $instance->get($key);
				break;

			case 4:
				if (empty(self::$memcached))
					self::getMemcacheServer();
				if (!self::$memcached)
					return;

				$value = memcache_get(self::$memcached, $key);
				break;

			case 1:
				$value = apc_fetch($key . 'smf');
				break;

			case 3:
				$value = output_cache_get($key, $ttl);
				break;

			case 2:
				$value = xcache_get($key);
				break;

			case 0:
				if (file_exists($cachedir . '/data_' . $key . '.php') && filesize($cachedir . '/data_' . $key . '.php') > 10) {
					require($cachedir . '/data_' . $key . '.php');
					if (!empty($expired) && isset($value))
					{
						@unlink($cachedir . '/data_' . $key . '.php');
						unset($value);
					}
				}
				break;
		}

		if (isset($db_show_debug) && $db_show_debug === true) {
			self::$cache_hits[self::$cache_count]['t'] = array_sum(explode(' ', microtime())) - array_sum(explode(' ', $st));
			self::$cache_hits[self::$cache_count]['s'] = isset($value) ? strlen($value) : 0;
		}

		if (empty($value))
			return null;
		else
			return @unserialize($value);
	}

	public static function putCache($key, $value, $ttl = 120)
	{
		global $db_show_debug, $cachedir;

		if(-1 == self::$API)
			return;

		self::$cache_count++;
		if (isset($db_show_debug) && $db_show_debug === true) {
			self::$cache_hits[self::$cache_count] = array('k' => $key, 'd' => 'put', 's' => $value === null ? 0 : strlen(serialize($value)));
			$st = microtime();
		}

		$key = self::$basekey . strtr($key, ':', '-');
		$value = $value === null ? null : serialize($value);

		switch(self::$API) {
			case 5:
				$key = str_replace(' ', '_', $key);
				$instance = self::getMemcachedServer();
				$instance->set($key, $value, $ttl);
				break;

			case 4:
				if (empty(self::$memcached))
					self::getMemcacheServer();
				if (!self::$memcached)
					return;

				memcache_set(self::$memcached, $key, $value, 0, $ttl);
				break;

			case 1:
				// An extended key is needed to counteract a bug in APC.
				if ($value === null)
					apc_delete($key . 'smf');
				else
					apc_store($key . 'smf', $value, $ttl);
				break;

			case 3:
				output_cache_put($key, $value);
				break;

			case 2:
				if ($value === null)
					xcache_unset($key);
				else
					xcache_set($key, $value, $ttl);
				break;

			case 0:
				if ($value === null)
					@unlink($cachedir . '/data_' . $key . '.php');
				else {
					$cache_data = '<' . '?' . 'php if (!defined(\'SMF\')) die; if (' . (time() + $ttl) . ' < time()) $expired = true; else{$expired = false; $value = \'' . addcslashes($value, '\\\'') . '\';}' . '?' . '>';
					$fh = @fopen($cachedir . '/data_' . $key . '.php', 'w');
					if ($fh)
					{
						// Write the file.
						set_file_buffer($fh, 0);
						flock($fh, LOCK_EX);
						$cache_bytes = fwrite($fh, $cache_data);
						flock($fh, LOCK_UN);
						fclose($fh);

						// Check that the cache write was successful; all the data should be written
						// If it fails due to low diskspace, remove the cache file
						if ($cache_bytes != strlen($cache_data))
							@unlink($cachedir . '/data_' . $key . '.php');
					}
				}
				break;
		}
		if (isset($db_show_debug) && $db_show_debug === true)
			self::$cache_hits[self::$cache_count]['t'] = array_sum(explode(' ', microtime())) - array_sum(explode(' ', $st));
	}
}
?>
