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
 * it also implements the Hook and Cache APIs.
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

		require_once($sourcedir . '/lib/Subs-Charset.php');
		return utf8_strtolower($string);
	}

	public static function strtoupper($string)
	{
		global $sourcedir;

		if(function_exists('mb_strtoupper'))
			return mb_strtoupper($string, 'UTF-8');

		require_once($sourcedir . '/lib/Subs-Charset.php');
		return utf8_strtoupper($string);
	}

	public static function truncate($string, $length)
	{
		$string = self::ent_check($string);
		$matches = array();
		
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

	public static function htmlspecialchars($string, $quote_style = ENT_COMPAT)
	{
		return preg_replace(strtr('~(&#(\d{1,7}|x[0-9a-fA-F]{1,6});)~e', array('&' => '&amp;')), self::entity_fix($string), htmlspecialchars($string, $quote_style, 'UTF-8'));
	}

	public static function strpos($haystack, $needle, $offset = 0)
	{
		$haystack_arr = preg_split('~(&#\d{1,7}' . ';|&quot;|&amp;|&lt;|&gt;|&nbsp;|.)~u', self::ent_check($haystack), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		//$haystack_size = count($haystack_arr);
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

	public static function getMessagesPerPage()
	{
		global $modSettings, $options;

		return(empty($modSettings['disableCustomPerPage']) && !empty($options['messages_per_page']) ? $options['messages_per_page'] : $modSettings['defaultMaxMessages']);
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
	private static $addonsdir;

	/**
	 * @param $the_hooks	string - serialized array of hooks
	 *
	 * initialize the hooks
	 * this must be called immediately after loading modSettings[] from the database
	 * 
	 * also: sets the addons base directory. It must exist, be writeable and be a directory.
	 * If any check fails, it falls back to the default hardcoded sub-folder ($boarddir/addons).
	 */
	public static function setHooks(&$the_hooks)
	{
		global $boarddir;
		
		self::$hooks = @unserialize($the_hooks);
		if(isset($GLOBALS['addonsdir']) && !empty($GLOBALS['addonsdir']) && file_exists($GLOBALS['addonsdir']) && is_dir($GLOBALS['addonsdir']))
			self::$addonsdir = rtrim($GLOBALS['addonsdir'], '/\\ ') . '/';
		else
			self::$addonsdir = $boarddir . 'addons/';

	}

	/**
	 *
	 * @param type $hook     string - the name of the hook
	 * @param type $product  string - a product name. This also defines the sub-folder in which the files of the addons must be
	 * @param string $file   string - the file to include
	 * @param type $function string - a function name to call
	 * @return type			 bool   - true if all ok, false if the file or function could not be found.
	 * 
	 * this function is typicalle called from the install procedure of an addon. It adds one file/function
	 * to a named hook.
	 */
	public static function addHook($hook, $product, $file, $function)
	{
		$ref = array('p' => $product, 'f' => $file, 'c' => $function);

		if(isset(self::$hooks[$hook]) && is_array(self::$hooks[$hook])) {
			foreach(self::$hooks[$hook] as $current_hook) {
				if($current_hook == $ref) {
					log_error(sprintf('HookAPI: duplicate hook installation detected in hook %s (product: %s, function: %s, file: %s', $hook, $ref['p'], $ref['c'], $ref['f']));
					return;
				}
			}
		}
		// check the hook for validity
		$file = self::$addonsdir . $ref['p'] . '/' . $ref['f'];
		if(!file_exists($file)) {
			log_error(sprintf('HookAPI: missing hook file while installing into hook %s (product: %s, function: %s, file: %s', $hook, $ref['p'], $ref['c'], $ref['f']));
			return(false);
		}
		@include_once($file);
		if(!is_callable($ref['c'])) {
			log_error(sprintf('HookAPI: missing function while installing into hook %s (product: %s, function: %s, file: %s', $hook, $ref['p'], $ref['c'], $ref['f']));
			return(false);
		}
		self::$hooks[$hook][] = array('p' => $product, 'f' => $file, 'c' => trim($function));
		$change_array = array('integration_hooks' => serialize(self::$hooks));
		updateSettings($change_array, true);
		return(true);
	}

	// Process functions of an integration hook.
	public static function callHook($hook, $parameters = array())
	{
		$results = array();

		if(isset(self::$hooks[$hook]) && is_array(self::$hooks[$hook])) {
			foreach(self::$hooks[$hook] as $current_hook) {
				@include_once(self::$addonsdir . $current_hook['p'] . '/' . $current_hook['f']);
				if(is_callable($current_hook['c']))
					$results[$current_hook['c']] = call_user_func_array($current_hook['c'], $parameters);
			}
		}
		return $results;
	}

	/*
	 * special case - hooks that work on the output buffer - they
	 * must be called via ob_start() and therefore need their own method.
	 * 
	 * all functions registered under the integrate_buffer hook will run here
	 */
	public static function integrateOB()
	{
		if(isset(self::$hooks['integrate_buffer'])) {
			foreach(self::$hooks['integrate_buffer'] as $current_hook) {
				@include_once(self::$addonsdir . $current_hook['p'] . '/' . $current_hook['f']);
				if(is_callable($current_hook['c']))
					ob_start($current_hook['c']);
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
	private static $cachedir = '';

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

	public static function init($desired, $basekey, $memcached_hosts, $cachedir)
	{
		self::$basekey = $basekey;
		self::$memcached_hosts = $memcached_hosts;
		self::$cachedir = $cachedir;

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

	public static function verifyFileCache()
	{
		global $user_info, $txt;

		$msg = '';
		// if we have file caching, make absolutely sure the folder exists and is writeable
		// if not, throw a warning (for admins only)
		if(0 == self::$API && (empty(self::$cachedir) || !file_exists(self::$cachedir) || !is_writable(self::$cachedir) )) {
			self::$API = -1;
			if($user_info['is_admin']) {
				loadLanguage('Errors');
				$msg = '
				<div class="errorbox">
					'.$txt['file_cache_config_error'].'<br><br>
					'.sprintf($txt['file_cache_config_path'], empty(self::$cachedir) ? 'Empty value' : self::$cachedir).'
				</div>
				';
			}
		}
		return($msg);
	}

	public static function disable()
	{
		self::$API = -1;
	}

	public static function getEngine()
	{
		global $txt;
		$engines = array('Filesystem cache', 'APC', 'Xcache', 'Zend', 'Memcached', 'New PECL Memcached');

		if(-1 == self::$API)
			return($txt['caching_disabled']);
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

/**
 * Mobile Detect
 * @usage      require_once 'Mobile_Detect.php';
 *             $detect = new Mobile_Detect();
 *             $detect->isMobile() or $detect->isTablet()
 *
 *             For more specific usage see the documentation inside the class.
 *             $detect->isAndroidOS() or $detect->isiPhone() ...
 *
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 */

class Mobile_Detect {

	protected $detectionRules;
	protected $userAgent = null;
	protected $accept = null;
	// Assume the visitor has a desktop environment.
	protected $isMobile = false;
	protected $isTablet = false;
	protected $phoneDeviceName = null;
	protected $tabletDevicename = null;
	protected $operatingSystemName = null;
	protected $userAgentName = null;
	// List of mobile devices (phones)
	protected $phoneDevices = array(
		'iPhone' => '(iPhone.*Mobile|iPod|iTunes)',
		'BlackBerry' => 'BlackBerry|rim[0-9]+',
		'HTC' => 'HTC|Desire',
		'Nexus' => 'Nexus One|Nexus S',
		'DellStreak' => 'Dell Streak',
		'Motorola' => '\bDroid\b.*Build|HRI39|MOT\-',
		'Samsung' => 'Samsung|GT\-P1000|SGH\-T959D|GT\-I9100|GT\-I9000',
		'Sony' => 'E10i',
		'Asus' => 'Asus.*Galaxy',
		'Palm' => 'PalmSource|Palm', // avantgo|blazer|elaine|hiptop|plucker|xiino
		'GenericPhone' => '(mmp|pocket|psp|symbian|Smartphone|smartfon|treo|up.browser|up.link|vodafone|wap|nokia|Series40|Series60|S60|SonyEricsson|N900|\bPPC\b|MAUI.*WAP.*Browser|LG\-P500)'
	);
	// List of tablet devices.
	protected $tabletDevices = array(
		'BlackBerryTablet' => 'PlayBook|RIM Tablet',
		'iPad' => 'iPad.*Mobile',
		'Kindle' => 'Kindle|Silk.*Accelerated',
		'SamsungTablet' => 'SCH\-I800|GT\-P1000|Galaxy.*Tab',
		'MotorolaTablet' => 'xoom|sholest',
		'AsusTablet' => 'Transformer|TF101',
		'GenericTablet' => 'Tablet|ViewPad7|LG\-V909|MID7015|BNTV250A|LogicPD Zoom2|\bA7EB\b|CatNova8|A1_07|CT704|CT1002|\bM721\b',
	);
	// List of mobile Operating Systems.
	protected $operatingSystems = array(
		'AndroidOS' => '(android.*mobile|android(?!.*mobile))',
		'BlackBerryOS' => '(blackberry|rim tablet os)',
		'PalmOS' => '(avantgo|blazer|elaine|hiptop|palm|plucker|xiino)',
		'SymbianOS' => 'Symbian|SymbOS|Series60|Series40|\bS60\b',
		'WindowsMobileOS' => 'IEMobile|Windows Phone|Windows CE.*(PPC|Smartphone)|MSIEMobile|Window Mobile|XBLWP7',
		'iOS' => '(iphone|ipod|ipad)',
		'FlashLiteOS' => '',
		'JavaOS' => '',
		'NokiaOS' => '',
		'webOS' => '',
		'badaOS' => '\bBada\b',
		'BREWOS' => '',
	);
	// List of mobile User Agents.
	protected $userAgents = array(
		'Chrome' => '\bCrMo\b',
		'Dolfin' => '\bDolfin\b',
		'Opera' => '(Opera.*Mini|Opera.*Mobi)',
		'Skyfire' => 'skyfire',
		'IE' => 'ie*mobile',
		'Firefox' => 'fennec|firefox.*maemo',
		'Bolt' => 'bolt',
		'TeaShark' => 'teashark',
		'Blazer' => 'Blazer',
		'Safari' => 'Mobile*Safari',
		'Midori' => 'midori',
		'GenericBrowser' => 'NokiaBrowser|OviBrowser'
	);

	function __construct(){

		// Merge all rules together.
		$this->detectionRules = array_merge(
			$this->phoneDevices,
			$this->tabletDevices,
			$this->operatingSystems,
			$this->userAgents
		);
		$this->userAgent = $_SERVER['HTTP_USER_AGENT'];
		$this->accept = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';

		if (
			isset($_SERVER['HTTP_X_WAP_PROFILE']) ||
			isset($_SERVER['HTTP_X_WAP_CLIENTID']) ||
			isset($_SERVER['HTTP_WAP_CONNECTION']) ||
			isset($_SERVER['HTTP_PROFILE']) ||
			isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']) || // Reported by Nokia devices (eg. C3)
			isset($_SERVER['HTTP_X_NOKIA_IPADDRESS']) ||
			isset($_SERVER['HTTP_X_NOKIA_GATEWAY_ID']) ||
			isset($_SERVER['HTTP_X_ORANGE_ID']) ||
			isset($_SERVER['HTTP_X_VODAFONE_3GPDPCONTEXT']) ||
			isset($_SERVER['HTTP_X_HUAWEI_USERID']) ||
			isset($_SERVER['HTTP_UA_OS']) || // Reported by Windows Smartphones
			(isset($_SERVER['HTTP_UA_CPU']) && $_SERVER['HTTP_UA_CPU'] == 'ARM') // Seen this on a HTC
		) {
			$this->isMobile = true;
		} elseif (!empty($this->accept) && (strpos($this->accept, 'text/vnd.wap.wml') !== false || strpos($this->accept, 'application/vnd.wap.xhtml+xml') !== false)) {
			$this->isMobile = true;
		} else {
			$this->_detect();
		}

	}

	public function getRules()
	{
		return $this->detectionRules;
	}

	public function __call($name, $arguments)
	{

		$key = substr($name, 2);
		return $this->_detect($key);

	}

	private function _detect($key='')
	{

		if(empty($key)){

			// Begin general search.
			foreach($this->detectionRules as $_key => $_regex){
				if(empty($_regex)){ continue; }
				if(preg_match('/'.$_regex.'/is', $this->userAgent)){
					$this->isMobile = true;
					return true;
				}
			}
			return false;

		} else {

			// Search for a certain key.
			// Make the keys lowecase so we can match: isIphone(), isiPhone(), isiphone(), etc.
			$key = strtolower($key);
			$_rules = array_change_key_case($this->detectionRules);

			if(array_key_exists($key, $_rules)){
				if(empty($_rules[$key])){ return null; }
				if(preg_match('/'.$_rules[$key].'/is', $this->userAgent)){
					$this->isMobile = true;
					return true;
				} else {
					return false;
				}
			} else {
				trigger_error("Method $key is not defined", E_USER_WARNING);
			}

			return false;

		}

	}

	/**
	 * Returns true if any type of mobile device detected, including special ones
	 * @return bool
	 */
	public function isMobile()
	{
		return $this->isMobile;
	}

	/**
	 * Return true if any type of tablet device is detected.
	 * @return boolean
	 */
	public function isTablet()
	{

		foreach($this->tabletDevices as $_key => $_regex){
			if(preg_match('/'.$_regex.'/is', $this->userAgent)){
				$this->isTablet = true;
				return true;
			}
		}

		return false;

	}


}

class Topiclist {

	private $topiclist = array();
	private $users_to_load = array();
	private $topic_ids = array();

	function __construct($request, $total_items) {

		global $context, $txt, $user_info, $scripturl, $options, $memberContext, $modSettings;

		while ($row = mysql_fetch_assoc($request))
		{
			censorText($row['subject']);

			$this->topic_ids[] = $row['id_topic'];

			$f_post_mem_href = !empty($row['id_member']) ? URL::user($row['id_member'], $row['first_member_name']) : '';
			$t_href = URL::topic($row['id_topic'], $row['subject'], 0);

			$l_post_mem_href = !empty($row['id_member_updated']) ? URL::user($row['id_member_updated'], $row['last_real_name'] ) : '';
			$l_post_msg_href = URL::topic($row['id_topic'], $row['last_subject'], $user_info['is_guest'] ? (!empty($options['view_newest_first']) ? 0 : ((int) (($row['num_replies']) / $context['pageindex_multiplier'])) * $context['pageindex_multiplier']) : 0, $user_info['is_guest'] ? true : false, $user_info['is_guest'] ? '' : ('.msg' . $row['id_last_msg']), $user_info['is_guest'] ? ('#msg' . $row['id_last_msg']) : '#new');

			$this->topiclist[$row['id_topic']] = array(
				'id' => $row['id_topic'],
				'id_member_started' => empty($row['id_member']) ? 0 : $row['id_member'],
				'first_post' => array(
					'id' => $row['id_first_msg'],
					'member' => array(
						'username' => $row['first_member_name'],
						'name' => $row['first_member_name'],
						'id' => empty($row['id_member']) ? 0 : $row['id_member'],
						'href' => $f_post_mem_href,
						'link' => !empty($row['id_member']) ? '<a onclick="getMcard('.$row['id_member'].', $(this));return(false);" href="' . $f_post_mem_href . '" title="' . $txt['profile_of'] . ' ' . $row['first_member_name'] . '">' . $row['first_member_name'] . '</a>' : $row['first_member_name'],
					),
					'time' => timeformat($row['first_poster_time']),
					'timestamp' => forum_time(true, $row['first_poster_time']),
					'subject' => $row['subject'],
					'icon' => $row['first_icon'],
					'icon_url' => getPostIcon($row['first_icon']),
					'href' => $t_href,
					'link' => '<a href="' . $t_href .'">' . $row['subject'] . '</a>'
				),
				'last_post' => array(
					'id' => $row['id_last_msg'],
					'member' => array(
						'username' => $row['last_real_name'],
						'name' => $row['last_real_name'],
						'id' => $row['id_member_updated'],
						'href' => $l_post_mem_href,
						'link' => !empty($row['id_member_updated']) ? '<a onclick="getMcard('.$row['id_member_updated'].', $(this));return(false);" href="' . $l_post_mem_href . '">' . $row['last_real_name'] . '</a>' : $row['last_real_name']
					),
					'time' => timeformat($row['last_post_time']),
					'timestamp' => forum_time(true, $row['last_post_time']),
					'subject' => $row['last_subject'],
					'href' => $l_post_msg_href,
					'link' => '<a href="' . $l_post_msg_href . ($row['num_replies'] == 0 ? '' : ' rel="nofollow"') . '>' . $row['last_subject'] . '</a>'
				),
				'subject' => $row['subject'],
				'new' => $row['new_from'] <= $row['id_msg_modified'],
				'new_from' => $row['new_from'],
				'newtime' => $row['new_from'],
				'updated' => timeformat($row['poster_time']),
				'new_href' => $scripturl . '?topic=' . $row['id_topic'] . '.msg' . $row['new_from'] . '#new',
				'new_link' => '<a href="' . $scripturl . '?topic=' . $row['id_topic'] . '.msg' . $row['new_from'] . '#new">' . $row['subject'] . '</a>',
				'replies' => comma_format($row['num_replies']),
				'views' => comma_format($row['num_views']),
				'approved' => $row['approved'],
				'unapproved_posts' => $row['unapproved_posts'],
				'is_old' => !empty($modSettings['oldTopicDays']) ? (($context['time_now'] - $row['last_post_time']) > ($modSettings['oldTopicDays'] * 86400)) : false,
				'is_posted_in' => false,
				'prefix' => '',
				'pages' => '',
				'is_sticky' => !empty($modSettings['enableStickyTopics']) && !empty($row['is_sticky']),
				'is_locked' => !empty($row['locked']),
				'is_poll' => false,
				'is_hot' => $row['num_replies'] >= $modSettings['hotTopicPosts'],
				'is_very_hot' => $row['num_replies'] >= $modSettings['hotTopicVeryPosts'],
				'board' => isset($row['id_board']) && !empty($row['id_board']) ? array(
					'name' => $row['board_name'],
					'id' => $row['id_board'],
					'href' => URL::board($row['id_board'], $row['board_name'])
				) : array(
					'name' => '',
					'id' => 0,
					'href' => ''
				)
			);
			//determineTopicClass($this->topiclist[$row['id_topic']]);
			if(!empty($row['id_member']) && $row['id_member'] != $user_info['id'])
				$this->users_to_load[$row['id_member']] = $row['id_member'];
		}
		loadMemberData($this->users_to_load);
		foreach($this->topiclist as &$topic) {
			if(!isset($memberContext[$topic['id_member_started']]))
				loadMemberContext($topic['id_member_started']);
			$topic['first_post']['member']['avatar'] = &$memberContext[$topic['id_member_started']]['avatar']['image'];
		}

		// figure out whether we have posted in a topic (but only if we are not the topic starter)
		if (!empty($modSettings['enableParticipation']) && !$user_info['is_guest'] && !empty($this->topic_ids))
		{
			$result = smf_db_query( '
				SELECT id_topic
				FROM {db_prefix}messages
				WHERE id_topic IN ({array_int:topic_list})
					AND id_member = {int:current_member}
				GROUP BY id_topic
				LIMIT ' . count($this->topic_ids),
				array(
					'current_member' => $user_info['id'],
					'topic_list' => $this->topic_ids,
				)
			);
			while ($row = mysql_fetch_assoc($result)) {
				if($this->topiclist[$row['id_topic']]['first_post']['member']['id'] != $user_info['id'])
					$this->topiclist[$row['id_topic']]['is_posted_in'] = true;
			}
			mysql_free_result($result);
		}
	}

	public function &getResult() {
		return $this->topiclist;
	}
}
?>
