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
 * This specific module is based on SimpleSEF for SMF 2.0, (C) by Matt Zuba.
 * See license note below.
 */
/* * **** BEGIN LICENSE BLOCK *****
 * Version: MPL 1.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is http://code.mattzuba.com code.
 *
 * The Initial Developer of the Original Code is
 * Matt Zuba.
 * Portions created by the Initial Developer are Copyright (C) 2010-2011
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *
 * ***** END LICENSE BLOCK ***** */
// No Direct Access!
if (!defined('SMF'))
    die('No access');

/**
 * generate "pretty" (or so-called SEO-friendly) URLs for topics, boards and other things
 * only active when SimpleSEF is enabled.
 */

class URLFactory {
	private $boardurl = '';
	private $scripturl = '';
	private $sep = '-';
	private $boardnames = array();
	private $topicnames = array();
	private $usernames = array();
	private $topics_fragment;
	private $suffix;
	private $topics_base;
	private $profile_base;

	public function __construct($b)
	{
		global $modSettings;

		$this->boardurl = rtrim($b, '/');
		$this->scripturl = 	$this->boardurl . '/index.php';

		$this->suffix = '.' . (!empty($modSettings['simplesef_suffix']) ? $modSettings['simplesef_suffix'] : 'html');
		$this->topics_fragment = !empty($modSettings['simplesef_topicsbase']) ? '/'.$modSettings['simplesef_topicsbase'] : '/topics/';
		$this->topics_base = $this->boardurl . $this->topics_fragment;
		$this->profile_base = $this->boardurl . '/profile/';
	}

	/**
	 * @static
	 * @param $topicid				our topic id
	 * @param $topicname			topic name (usually subject of the first post)
	 * @param int $start			topic start
	 * @param bool $force_start		if true, always ouput the .start even when it is 0
	 * @param string $msgfragment	what comes after the topic id (e.g. .msg2187).
	 * @param string $a				the target anchor id (e.g. #msg2187)
	 * @return string				a SEF URL for the topic.
	 */
	public function topic($topicid, $topicname, $start = 0, $force_start = true, $msgfragment = '', $a = '')
	{
		$_id = (int)$topicid;
		if(!isset($this->topicnames[$_id]))
			$this->topicnames[$_id] = SimpleSEF::encode($topicname);		// cache encoded topic names (encode() is quite heavy stuff).

		return($this->topics_base . $this->topicnames[$_id] . $this->sep . $_id . ((int)$start > 0 || $force_start ? ('.' . $start) : '') . $msgfragment . $this->suffix . $a);
	}

	/**
	 * @static
	 * @param $id			user id
	 * @param $name			user's name as it should appear in the URL
	 * @return string		a SEF URL for the user's profile
	 */
	public function user($id, $name)
	{
		$_id = (int)$id;
		if(!isset($this->usernames[$_id]))
			$this->usernames[$_id] = SimpleSEF::encode($name);
		return($this->profile_base . $this->usernames[$_id] . $this->sep . $_id);
	}

	/**
	 * @param type $id				int: board id
	 * @param type $boardname		string: boardname
	 * @param type $start			start with (page nr.)
	 * @param type $force_start		force the start value in the url, even if it is 0
	 * @return type 
	 */
	public function board($id, $boardname, $start = 0, $force_start = false)
	{
		$_id = (int)$id;
		if(!isset($this->boardnames[$_id]))
			$this->boardnames[$_id] = SimpleSEF::encode($boardname);
		return($this->boardurl . '/' . $this->boardnames[$_id] . '.' . trim($id) . ($start > 0 || $force_start ? ('-' . $start) : ''));
	}

	public function home()
	{
		return($this->boardurl . '/');
	}

	public function action($a)
	{
		if(stripos($a, '=admin') !== false)
			return($a);
		return(preg_replace('~\b' . preg_quote($this->scripturl) . '\?action=([a-zA-Z0-9]+)(.*)~', $this->boardurl . '/$1$2', $a));
	}

	public function addParam($url, $params)
	{
		$newparam = '';
		$_p = explode(';', trim($params, ';'));
		foreach($_p as $p) {
			$_c = explode('=', $p);
			$newparam .= ('/' . $_c[0] . '.' . (isset($_c[1]) && !empty($_c[1]) ? $_c[1] : ''));
		}
		if(!empty($newparam))
			return str_replace($this->boardurl, $this->boardurl . $newparam, $url);

		return($url);
	}

	/**
	 * @static
	 * @param $_r			a conventional querystring (i.e. ?action=mlist;sa=search) or URL
	 * 						including $scripturl.
	 * @return string		a prettyfied URL
	 *
	 * create a SEF URL for all instances for which we don't have a faster URL crafting method.
	 * this is inherently slower than topic() or user(), so don't use this if you can generate
	 * the URL with one of the more specific and faster methods.
	 */
	public function parse($_r)
	{
		$matches = array();
		$url = stripos($_r, $this->scripturl) === false ? ($this->scripturl . $_r . ' ') : ($_r . ' ');

		preg_match_all('~(' . preg_quote($this->scripturl) . '[-a-zA-Z0-9+&@#/%?=\~_|!:,.;\[\]]*[-a-zA-Z0-9+&@#/%=\~_|\[\]]?)([^-a-zA-Z0-9+&@#/%=\~_|])~', $url, $matches);
		if (!empty($matches[0])) {
			$replacements = array();
			foreach (array_unique($matches[1]) as $i => $_url) {
				$replacement = SimpleSEF::create_sef_url($_url);
				if ($_url != $replacement)
					$replacements[$matches[0][$i]] = $replacement . $matches[2][$i];
			}
			$url = str_replace(array_keys($replacements), array_values($replacements), $url);
		}
		return(trim($url));
	}
}

class URL {
	
	private static $impl = 0;
	private static $is_sef = false;
	private static $boardurl = '';
	private static $scripturl = '';
	
	public static function init($b)
	{
		global $modSettings;
		
		if(!empty($modSettings['simplesef_enable'])) {
			self::$impl = new URLFactory($b);
			self::$is_sef = true;
		}
		else
			self::$is_sef = false;

		self::$boardurl = rtrim($b, '/');
		self::$scripturl = 	self::$boardurl . '/index.php';
	}
	
	public static function topic($topicid, $topicname, $start = 0, $force_start = true, $msgfragment = '', $a = '')
	{
		if(self::$is_sef)
			return(self::$impl->topic($topicid, $topicname, $start, $force_start, $msgfragment, $a));
		
		return(self::$scripturl . '?topic=' . (int)$topicid . ($start > 0 || $force_start ? ('.' . $start) : '') . $msgfragment . $a);
	}
	
	public static function user($id, $name)
	{
		if(self::$is_sef)
			return(self::$impl->user($id, $name));
		
		return(self::$scripturl . '?action=profile;u=' . (int)$id);
	}
	
	public static function board($id, $boardname, $start = 0, $force_start = false)
	{
		if(self::$is_sef)
			return(self::$impl->board($id, $boardname, $start, $force_start));
		
		return(self::$scripturl . '?board=' . (int)$id . '.' . (int)$start);
	}
	
	public static function home()
	{
		if(self::$is_sef)
			return(self::$impl->home());
		
		return(self::$scripturl);
	}

	public static function action($a)
	{
		if(self::$is_sef)
			return(self::$impl->action($a));
		
		return($a);
	}

	public static function addParam($url, $params)
	{
		if(self::$is_sef)
			return(self::$impl->addParam($url, $params));
		
		list($base, $fragment) = explode('#', $url);
		$newparam = ';' . ltrim($params, ';');
		if(!empty($newparam))
			return $base . $newparam . (!empty($fragment) ? ('#' . $fragment) : '');
		return($url);
	}
	
	public static function parse($_r)
	{
		if(self::$is_sef)
			return(self::$impl->parse($_r));
		
		return(stripos($_r, self::$scripturl) === false ? (self::$scripturl . $_r) : $_r);
	}

}
class SimpleSEF {

    /**
     * @var Tracks the added queries used during execution
     */
    private static $queryCount = 0;
    /**
     * @var array Tracks benchmarking information
     */
    private static $benchMark = array('total' => 0, 'marks' => array());
    /**
     * @var array All actions used in the forum (normally defined in index.php
     * 	but may come from custom action mod too)
     */
    private static $actions = array();
    /**
     * @var array All ignored actions used in the forum
     */
    private static $ignoreactions = array('admin', 'openidreturn');
    /**
     * @var array Actions that have aliases
     */
    private static $aliasactions = array();
    /**
     * @var array Actions that may have a 'u' or 'user' parameter in the URL
     */
    private static $useractions = array();
    /**
     * @var array Words to strip while encoding
     */
    private static $stripWords = array();
    /**
     * @var array Characters to strip while encoding
     */
    private static $stripChars = array();
    /**
     * @var array Stores boards found in the output after a database query
     */
    private static $boardNames = array();
    /**
     * @var array Stores topics found in the output after a database query
     */
    private static $topicNames = array();
    /**
     * @var array Stores usernames found in the output after a database query
     */
    private static $userNames = array();
    /**
     * @var array Tracks the available extensions
     */
    private static $extensions = array();
    /**
     * @var bool Properly track redirects
     */
    private static $redirect = FALSE;

	private static $topics_base = '';

    /**
     * Initialize the mod and it's settings.  We can't use a constructor
     * might change this in the future (either singleton or two classes,
     * one to handle the integration hooks and one that does the dirty work)
     *
     * @global array $modSettings SMF's modSettings variable
     * @staticvar boolean $done Says if this has been done already
     * @param boolean $force Force the init to run again if already done
     * @return void
     */
    public static function init($force = FALSE) {
        global $modSettings;
        static $done = FALSE;

        if ($done && !$force)
            return;
        $done = TRUE;

        self::$actions = !empty($modSettings['simplesef_actions']) ? explode(',', $modSettings['simplesef_actions']) : array();
        self::$ignoreactions = array_merge(self::$ignoreactions, !empty($modSettings['simplesef_ignore_actions']) ? explode(',', $modSettings['simplesef_ignore_actions']) : array());
        self::$aliasactions = !empty($modSettings['simplesef_aliases']) ? unserialize($modSettings['simplesef_aliases']) : array();
        self::$useractions = !empty($modSettings['simplesef_useractions']) ? explode(',', $modSettings['simplesef_useractions']) : array();
        self::$stripWords = !empty($modSettings['simplesef_strip_words']) ? self::explode_csv($modSettings['simplesef_strip_words']) : array();
        self::$stripChars = !empty($modSettings['simplesef_strip_chars']) ? self::explode_csv($modSettings['simplesef_strip_chars']) : array();
		self::$topics_base = !empty($modSettings['simplesef_topicsbase']) ? $modSettings['simplesef_topicsbase'] : 'topics/';

        // Do a bit of post processing on the arrays above
        self::$stripWords = array_filter(self::$stripWords, create_function('$value', 'return !empty($value);'));
        array_walk(self::$stripWords, 'trim');
        self::$stripChars = array_filter(self::$stripChars, create_function('$value', 'return !empty($value);'));
        array_walk(self::$stripChars, 'trim');

        self::loadBoardNames($force);
        self::loadExtensions($force);

        //self::log('Pre-fix GET:' . var_export($_GET, TRUE));

        // We need to fix our GET array too...
        parse_str(preg_replace('~&(\w+)(?=&|$)~', '&$1=', strtr($_SERVER['QUERY_STRING'], array(';?' => '&', ';' => '&', '%00' => '', "\0" => ''))), $_GET);

        //self::log('Post-fix GET:' . var_export($_GET, TRUE), 'Init Complete (forced: ' . ($force ? 'true' : 'false') . ')');
    }

    /**
     * Implements integrate_pre_load
     * Converts the incoming query string 'q=' into a proper querystring and get
     * variable array.  q= comes from the .htaccess rewrite.
     * Will have to figure out how to do some checking of other types of SEF mods
     * and be able to rewrite those as well.  Currently we only rewrite our own urls
     *
     * @global string $boardurl SMF's board url
     * @global array $modSettings
     * @global string $scripturl
     * @global array $smcFunc SMF's smcFunc array of functions
     * @global string $language
     * @global string $sourcedir
     * @return void
     */
    public static function convertQueryString() {
        global $boardurl, $modSettings, $scripturl;

        if (empty($modSettings['simplesef_enable']))
            return;

        self::init();

        $scripturl = $boardurl . '/index.php';

        // Make sure we know the URL of the current request.
        if (empty($_SERVER['REQUEST_URI']))
            $_SERVER['REQUEST_URL'] = $scripturl . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
        elseif (preg_match('~^([^/]+//[^/]+)~', $scripturl, $match) == 1)
            $_SERVER['REQUEST_URL'] = $match[1] . $_SERVER['REQUEST_URI'];
        else
            $_SERVER['REQUEST_URL'] = $_SERVER['REQUEST_URI'];

        if (SMF == 'SSI')
            return;

        // if the URL contains index.php but not our ignored actions, rewrite the URL

		// todo
		if (!empty($modSettings['simplesef_redirect']) && strpos($_SERVER['REQUEST_URL'], 'index.php') !== false && !(isset($_GET['xml']) || (!empty($_GET['action']) && in_array($_GET['action'], self::$ignoreactions)))) {
            //self::log('Rewriting and redirecting permanently: ' . $_SERVER['REQUEST_URL']);
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . self::create_sef_url($_SERVER['REQUEST_URL']));
            exit();
        }

        // Parse the url
        if (!empty($_GET['q'])) {
            $querystring = self::route($_GET['q']);
            $_GET = $querystring + $_GET;
			unset($_GET['q']);
        }
        // Need to grab any extra query parts from the original url and tack it on here
        $_SERVER['QUERY_STRING'] = http_build_query($_GET, '', ';');
        //self::log('Post-convert GET:' . var_export($_GET, true));
    }

	/**
	 * @static
	 * @param $buffer		string: output buffer
	 * @return mixed		string: the rewritten buffer
	 *
	 * lightweight buffer rewrite. Only looks at topic URLs that couldn't be
	 * created dynamically (e.g. quotes, other BBCodes).
	 * Most other URLs are dynamically created through URL:: methods now
	 */
	public static function ob_simplesef_light($buffer)
	{
		global $scripturl, $txt, $context;

		self::benchmark('buffer');

		$matches = array();
  		$count = 0;
		preg_match_all('~\b' . preg_quote($scripturl) . '.*?topic=([0-9]+)\b~', $buffer, $matches);
		if (!empty($matches[1])) {
    		self::loadTopicNames(array_unique($matches[1]));
			$matches = array();
			preg_match_all('~\b(' . preg_quote($scripturl) . '\?topic=[-a-zA-Z0-9+&@#/%?=\~_|!:,.;\[\]]*[-a-zA-Z0-9+&@#/%=\~_|\[\]]?)([^-a-zA-Z0-9+&@#/%=\~_|])~', $buffer, $matches);
			if (!empty($matches[0])) {
				$replacements = array();
				foreach (array_unique($matches[1]) as $i => $url) {
					$replacement = self::create_sef_url($url);
					if ($url != $replacement)
						$replacements[$matches[0][$i]] = $replacement . $matches[2][$i];
				}
				$buffer = str_replace(array_keys($replacements), array_values($replacements), $buffer);
				$count = count($replacements);
			}
		}

		self::benchmark('buffer');

  		if (!empty($context['show_load_time']))
      		$buffer = preg_replace('~(.*[s]\sCPU,\s.*queries\.)~', '$1' . sprintf(' OB_rewrite_fast: %d', $count) . ' (' . round(self::$benchMark['total'], 3) . $txt['seconds_with'] . self::$queryCount . $txt['queries'].')', $buffer);

  		//self::log('SimpleSEF rewrote ' . $count . ' urls in ' . self::$benchMark['total'] . ' seconds');

  		return $buffer;
	}
    /**
     * Implements integrate_buffer
     * This is the core of the mod.  Rewrites the output buffer to create SEF
     * urls.  It will only rewrite urls for the site at hand, not other urls
     *
     * @global string $scripturl
     * @global array $smcFunc
     * @global string $boardurl
     * @global array $txt
     * @global array $modSettings
     * @global array $context
     * @param string $buffer The output buffer after SMF has output the templates
     * @return string Returns the altered buffer (or unaltered if the mod is disabled)
     */
    public static function ob_simplesef($buffer) {
        global $scripturl, $boardurl, $txt, $modSettings, $context;

        if (empty($modSettings['simplesef_enable']))
            return $buffer;

        self::benchmark('buffer');
        // Bump up our memory limit a bit
        if (@ini_get('memory_limit') < 128)
            @ini_set('memory_limit', '128M');

        // Grab the topics...
        $matches = array();
        preg_match_all('~\b' . preg_quote($scripturl) . '.*?topic=([0-9]+)~', $buffer, $matches);
        if (!empty($matches[1]))
            self::loadTopicNames(array_unique($matches[1]));

        // We need to find urls that include a user id, so we can grab them all and fetch them ahead of time
        $matches = array();
        preg_match_all('~\b' . preg_quote($scripturl) . '.*?u=([0-9]+)~', $buffer, $matches);
        if (!empty($matches[1]))
            self::loadUserNames(array_unique($matches[1]));

        // Grab all URLs and fix them
        $matches = array();
        $count = 0;
        preg_match_all('~\b(' . preg_quote($scripturl) . '[-a-zA-Z0-9+&@#/%?=\~_|!:,.;\[\]]*[-a-zA-Z0-9+&@#/%=\~_|\[\]]?)([^-a-zA-Z0-9+&@#/%=\~_|])~', $buffer, $matches);
        if (!empty($matches[0])) {
            $replacements = array();
            foreach (array_unique($matches[1]) as $i => $url) {
                $replacement = self::create_sef_url($url);
                if ($url != $replacement)
                    $replacements[$matches[0][$i]] = $replacement . $matches[2][$i];
            }
            $buffer = str_replace(array_keys($replacements), array_values($replacements), $buffer);
            $count = count($replacements);
        }

        // Gotta fix up some javascript laying around in the templates
        $extra_replacements = array(
            '/$d\',' => '_%1$d/\',', // Page index for MessageIndex
            '/rand,' => '/rand=', // Verification Image
            '%1.html$d\',' => '%1$d.html\',', // Page index on MessageIndex for topics
            $boardurl . '/topic/' => $scripturl . '?topic=', // Also for above
            '%1_%1$d/\',' => '%1$d/\',', // Page index on Members listing
            'var smf_scripturl = "' . $boardurl . '/' => 'var smf_scripturl = "' . $scripturl,
        );
        $buffer = str_replace(array_keys($extra_replacements), array_values($extra_replacements), $buffer);

        // Check to see if we need to update the actions lists
        $changeArray = array();
        $possibleChanges = array('actions', 'useractions');
        foreach ($possibleChanges as $change)
            if (empty($modSettings['simplesef_' . $change]) || (substr_count($modSettings['simplesef_' . $change], ',') + 1) != count(self::$$change))
                $changeArray['simplesef_' . $change] = implode(',', self::$$change);

        if (!empty($changeArray)) {
            updateSettings($changeArray);
            self::$queryCount++;
        }

        self::benchmark('buffer');

        if (!empty($context['show_load_time']))
            $buffer = preg_replace('~(.*[s]\sCPU,\s.*queries\.)~', '$1' . sprintf('SimpleSEF: %d replacements', $count) . ' ' . round(self::$benchMark['total'], 3) . $txt['seconds_with'] . self::$queryCount . $txt['queries'], $buffer);
			//$buffer = preg_replace('~(.*[s]\sCPU,\s.*queries\.)~', '$1 foo', $buffer);

        //self::log('SimpleSEF rewrote ' . $count . ' urls in ' . self::$benchMark['total'] . ' seconds');

        // I think we're done
        return $buffer;
    }

    /**
     * Implements integrate_redirect
     * When SMF calls redirectexit, we need to rewrite the URL its redirecting to
     * Without this, the convertQueryString would catch it, but would cause an
     * extra page load.  This helps reduce server load and streamlines redirects
     *
     * @global string $scripturl
     * @global array $modSettings
     * @param string $setLocation The original location (passed by reference)
     * @param boolean $refresh Unused, but declares if we are using meta refresh
     * @return <type>
     */
    public static function fixRedirectUrl(&$setLocation, &$refresh) {
        global $scripturl, $modSettings;

        if (empty($modSettings['simplesef_enable']))
            return;

        self::$redirect = true;
        //self::log('Fixing redirect location: ' . $setLocation);

        // Only do this if it's an URL for this board
        if (strpos($setLocation, $scripturl) !== false)
            $setLocation = self::create_sef_url($setLocation);
    }

    /**
     * Implements integrate_exit
     * When SMF outputs XML data, the buffer function is never called.  To
     * circumvent this, we use the _exit hook which is called just before SMF
     * exits.  If SMF didn't output a footer, it typically didn't run through
     * our output buffer.  This catches the buffer and runs it through.
     *
     * @global array $modSettings
     * @param boolean $do_footer If we didn't do a footer and we're not wireless
     * @return void
     */
    public static function fixXMLOutput($do_footer) {
        global $modSettings;

        if (empty($modSettings['simplesef_enable']))
            return;

        if (!$do_footer && !self::$redirect) {
            $temp = ob_get_contents();

            ob_end_clean();
            ob_start(!empty($modSettings['enableCompressedOutput']) ? 'ob_gzhandler' : '');
            ob_start(array('SimpleSEF', 'ob_simplesef'));

            echo $temp;

            //self::log('Rewriting XML Output');
        }
    }

    /**
     * Implements integrate_outgoing_mail
     * Simply adjusts the subject and message of an email with proper urls
     *
     * @global array $modSettings
     * @param string $subject The subject of the email
     * @param string $message Body of the email
     * @param string $header Header of the email (we don't adjust this)
     * @return boolean Always returns TRUE to prevent SMF from erroring
     */
    public static function fixEmailOutput(&$subject, &$message, &$header) {
        global $modSettings;

        if (empty($modSettings['simplesef_enable']))
            return TRUE;

        // We're just fixing the subject and message
        $subject = self::ob_simplesef($subject);
        $message = self::ob_simplesef($message);

        //self::log('Rewriting email output');

        // We must return true, otherwise we fail!
        return TRUE;
    }

    /**
     * Implements integrate_actions
     * @param array $actions SMF's actions array
     */
    public static function actionArray(&$actions) {
        $actions['simplesef-404'] = array('SimpleSEF.php', array('SimpleSEF', 'http404NotFound'));
    }

    /**
     * Outputs a simple 'Not Found' message and the 404 header
     */
    public static function http404NotFound() {
        header('HTTP/1.0 404 Not Found');
        //self::log('404 Not Found: ' . $_SERVER['REQUEST_URL']);
        fatal_lang_error('simplesef_404', FALSE);
    }

    /**
     * Implements integrate_admin_areas
     * Adds SimpleSEF options to the admin panel
     *
     * @global array $txt
     * @global array $modSettings
     * @param array $admin_areas
     */
    public static function adminAreas(&$admin_areas) {
        global $txt, $modSettings;

        if (empty($modSettings['simplesef_enable']))
			return;

		// We insert it after Features and Options
        $counter = array_search('featuresettings', array_keys($admin_areas['config']['areas'])) + 1;

        $admin_areas['config']['areas'] = array_merge(
            array_slice($admin_areas['config']['areas'], 0, $counter, TRUE), array('simplesef' => array(
                'label' => $txt['simplesef_admin_label'],
                'function' => create_function(NULL, 'SimpleSEF::ModifySimpleSEFSettings();'),
                'icon' => 'search.gif',
                'subsections' => array(
                    'basic' => array($txt['simplesef_basic']),
                    'advanced' => array($txt['simplesef_advanced'], 'enabled' => !empty($modSettings['simplesef_advanced'])),
                    'alias' => array($txt['simplesef_alias'], 'enabled' => !empty($modSettings['simplesef_advanced'])),
                ),
            )), array_slice($admin_areas['config']['areas'], $counter, count($admin_areas['config']['areas']), TRUE)
        );
    }

    /**
     * Directs the admin to the proper page of settings for SimpleSEF
     *
     * @global array $txt
     * @global array $context
     * @global string $sourcedir
     */
    public static function ModifySimpleSEFSettings() {
        global $txt, $context, $sourcedir;

        require_once($sourcedir . '/ManageSettings.php');

        $context['page_title'] = $txt['simplesef'];

        $subActions = array(
            'basic' => array('SimpleSEF', 'ModifyBasicSettings'),
            'advanced' => array('SimpleSEF', 'ModifyAdvancedSettings'),
            'alias' => array('SimpleSEF', 'ModifyAliasSettings'),
        );

        loadGeneralSettingParameters($subActions, 'basic');

        // Load up all the tabs...
        $context[$context['admin_menu_name']]['tab_data'] = array(
            'title' => $txt['simplesef'],
            'description' => $txt['simplesef_desc'],
            'tabs' => array(
                'basic' => array(
                ),
                'advanced' => array(
                ),
                'alias' => array(
                    'description' => $txt['simplesef_alias_desc'],
                ),
            ),
        );

        call_user_func($subActions[$_REQUEST['sa']]);
    }

    /**
     * Modifies the basic settings of SimpleSEF.
     *
     * @global string $scripturl
     * @global array $txt
     * @global array $context
     * @global string $boarddir
     * @global array $modSettings
     */
    public static function ModifyBasicSettings() {
        global $scripturl, $txt, $context, $boarddir, $modSettings;

        $config_vars = array(
			array('text', 'simplesef_topicsbase', 'size' => 20, 'subtext' => $txt['simplesef_topicsbase_desc']),
			array('check', 'simplesef_redirect', 'subtext' => $txt['simplesef_redirect_desc']),
            array('text', 'simplesef_space', 'size' => 6, 'subtext' => $txt['simplesef_space_desc']),
            array('text', 'simplesef_suffix', 'subtext' => $txt['simplesef_suffix_desc']),
            array('check', 'simplesef_advanced', 'subtext' => $txt['simplesef_advanced_desc']),
        );

        $context['post_url'] = $scripturl . '?action=admin;area=simplesef;sa=basic;save';

        // Saving?
        if (isset($_GET['save'])) {
            checkSession();

            if (trim($_POST['simplesef_suffix']) == '')
                fatal_lang_error('simplesef_suffix_required');

            $_POST['simplesef_suffix'] = trim($_POST['simplesef_suffix'], '.');

			if (trim($_POST['simplesef_topicsbase']) == '')
				$_POST['simplesef_topicsbase'] = 'topics/';

			$_POST['simplesef_topicsbase'] = preg_replace('~[^A-Za-z0-9]~', "", $_POST['simplesef_topicsbase']) . '/';
            $save_vars = $config_vars;

            // We don't want to break boards, so we'll make sure some stuff exists before actually enabling
            if (!empty($_POST['simplesef_enable']) && empty($modSettings['simplesef_enable'])) {
                if (strpos($_SERVER['SERVER_SOFTWARE'], 'IIS') !== false && file_exists($boarddir . '/web.config'))
                    $_POST['simplesef_enable'] = strpos(implode('', file($boarddir . '/web.config')), '<action type="Rewrite" url="index.php?q={R:1}"') !== false ? 1 : 0;
                elseif (strpos($_SERVER['SERVER_SOFTWARE'], 'IIS') === false && file_exists($boarddir . '/.htaccess'))
                    $_POST['simplesef_enable'] = strpos(implode('', file($boarddir . '/.htaccess')), 'RewriteRule ^(.*)$ index.php') !== false ? 1 : 0;
                elseif (strpos($_SERVER['SERVER_SOFTWARE'], 'lighttpd') !== false)
                    $_POST['simplesef_enable'] = 1;
                elseif (strpos($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false)
                    $_POST['simplesef_enable'] = 1;
                else
                    $_POST['simplesef_enable'] = 0;
            }

            saveDBSettings($save_vars);

            redirectexit('action=admin;area=simplesef;sa=basic');
        }

        prepareDBSettingContext($config_vars);
    }

    /**
     * Modifies the advanced settings for SimpleSEF.  Most setups won't need to
     * touch this (except for maybe other languages)
     *
     * @global string $scripturl
     * @global array $txt
     * @global array $context
     * @global array $modSettings
     * @global array $settings
     */
    public static function ModifyAdvancedSettings() {
        global $scripturl, $txt, $context, $modSettings, $settings;

        loadTemplate('SimpleSEF');
        $config_vars = array(
            array('check', 'simplesef_lowercase', 'subtext' => $txt['simplesef_lowercase_desc']),
            array('large_text', 'simplesef_strip_words', 'size' => 6, 'subtext' => $txt['simplesef_strip_words_desc']),
            array('large_text', 'simplesef_strip_chars', 'size' => 6, 'subtext' => $txt['simplesef_strip_chars_desc']),
            array('check', 'simplesef_debug', 'subtext' => $txt['simplesef_debug_desc']),
            '',
            array('callback', 'simplesef_ignore'),
            array('title', 'title', 'label' => $txt['simplesef_action_title']),
            array('desc', 'desc', 'label' => $txt['simplesef_action_desc']),
            array('text', 'simplesef_actions', 'size' => 50, 'disabled' => 'disabled', 'preinput' => '<input type="hidden" name="simplesef_actions" value="' . $modSettings['simplesef_actions'] . '" />'),
            array('text', 'simplesef_useractions', 'size' => 50, 'disabled' => 'disabled', 'preinput' => '<input type="hidden" name="simplesef_useractions" value="' . $modSettings['simplesef_useractions'] . '" />'),
        );

        // Prepare the actions and ignore list
        $context['simplesef_dummy_ignore'] = !empty($modSettings['simplesef_ignore_actions']) ? explode(',', $modSettings['simplesef_ignore_actions']) : array();
        $context['simplesef_dummy_actions'] = array_diff(explode(',', $modSettings['simplesef_actions']), $context['simplesef_dummy_ignore']);
        $context['html_headers'] .= '<script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/SelectSwapper.js?rc5"></script>';

        $context['post_url'] = $scripturl . '?action=admin;area=simplesef;sa=advanced;save';
        $context['settings_post_javascript'] = '
			function editAreas()
			{
				document.getElementById("simplesef_actions").disabled = "";
				document.getElementById("setting_simplesef_actions").nextSibling.nextSibling.style.color = "";
				document.getElementById("simplesef_useractions").disabled = "";
				document.getElementById("setting_simplesef_useractions").nextSibling.nextSibling.style.color = "";
				return false;
			}
			var swapper = new SelectSwapper({
				sFromBoxId			: "dummy_actions",
				sToBoxId			: "dummy_ignore",
				sToBoxHiddenId		: "simplesef_ignore_actions",
				sAddButtonId		: "simplesef_ignore_add",
				sAddAllButtonId		: "simplesef_ignore_add_all",
				sRemoveButtonId		: "simplesef_ignore_remove",
				sRemoveAllButtonId	: "simplesef_ignore_remove_all"
			});';

        // Saving?
        if (isset($_GET['save'])) {
            checkSession();

            $save_vars = $config_vars;

            // Ignoring any actions??
            $save_vars[] = array('text', 'simplesef_ignore_actions');

            saveDBSettings($save_vars);

            redirectexit('action=admin;area=simplesef;sa=advanced');
        }

        prepareDBSettingContext($config_vars);
    }

    /**
     * Modifies the Action Aliasing settings
     *
     * @global string $scripturl
     * @global array $txt
     * @global array $context
     * @global array $modSettings
     */
    public static function ModifyAliasSettings() {
        global $scripturl, $context, $modSettings;

        loadTemplate('SimpleSEF');
        $context['sub_template'] = 'alias_settings';

        $context['simplesef_aliases'] = !empty($modSettings['simplesef_aliases']) ? unserialize($modSettings['simplesef_aliases']) : array();

        $context['post_url'] = $scripturl . '?action=admin;area=simplesef;sa=alias';

        if (isset($_POST['save'])) {
            checkSession();

            // Start with some fresh arrays
            $alias_original = array();
            $alias_new = array();

            // Clean up the passed in arrays
            if (isset($_POST['original'], $_POST['alias'])) {
                // Make sure we don't allow duplicate actions or aliases
                $_POST['original'] = array_unique(array_filter($_POST['original'], create_function('$x', 'return $x != \'\';')));
                $_POST['alias'] = array_unique(array_filter($_POST['alias'], create_function('$x', 'return $x != \'\';')));
                $alias_original = array_intersect_key($_POST['original'], $_POST['alias']);
                $alias_new = array_intersect_key($_POST['alias'], $_POST['original']);
            }

            $aliases = !empty($alias_original) ? array_combine($alias_original, $alias_new) : array();

            // One last check
            foreach ($aliases as $orig => $alias)
                if ($orig == $alias)
                    unset($aliases[$orig]);

            $updates = array(
                'simplesef_aliases' => serialize($aliases),
            );

            updateSettings($updates);

            redirectexit('action=admin;area=simplesef;sa=alias');
        }
    }

    /**
     * Implements integrate_load_theme
     * Loads up our language files
     */
    public static function loadTheme() {
        loadLanguage('SimpleSEF');
    }

    /**
     * This is a helper function of sorts that actually creates the SEF urls.
     * It compiles the different parts of a normal URL into a SEF style url
     *
     * @global string $sourcedir
     * @global array $modSettings
     * @param string $url URL to SEFize
     * @return string Either the original url if not enabled or ignored, or a new URL
     */
    public static function create_sef_url($url) {
        global $sourcedir, $modSettings;

        if (empty($modSettings['simplesef_enable']))
            return $url;

        // Set our output strings to nothing.
        $sefstring = $sefstring2 = $sefstring3 = '';
        $query_parts = array();

        // Get the query string of the passed URL
        $url_parts = parse_url($url);
        $params = array();
        parse_str(!empty($url_parts['query']) ? preg_replace('~&(\w+)(?=&|$)~', '&$1=', strtr($url_parts['query'], array('&amp;' => '&', ';' => '&'))) : '', $params);

        if (!empty($params['action'])) {
            // If we're ignoring this action, just return the original URL
            if (in_array($params['action'], self::$ignoreactions)) {
                //self::log('create_sef_url: Ignoring ' . $params['action']);
                return $url;
            }

            if (!in_array($params['action'], self::$actions))
                self::$actions[] = $params['action'];
            $query_parts['action'] = $params['action'];
            unset($params['action']);

            if (!empty($params['u'])) {
                if (!in_array($query_parts['action'], self::$useractions))
                    self::$useractions[] = $query_parts['action'];
                $query_parts['user'] = self::getUserName($params['u']);
                unset($params['u'], $params['user']);
            }
        }

        if (!empty($query_parts['action']) && !empty(self::$extensions[$query_parts['action']])) {
            require_once($sourcedir . '/SimpleSEF-Ext/' . self::$extensions[$query_parts['action']]);
            $class = ucwords($query_parts['action']);
            $extension = new $class();
            $sefstring2 = $extension->create($params);
        } else {
            if (!empty($params['board'])) {
                $query_parts['board'] = empty($params['topic']) ? self::getBoardName($params['board']) : 'topics';
                unset($params['board']);
            }
            if (!empty($params['topic'])) {
                $query_parts['topic'] = self::getTopicName($params['topic']);
                unset($params['topic']);
            }

            foreach ($params as $key => $value) {
                if ($value == '')
                    $sefstring3 .= $key . './';
                else {
                    $sefstring2 .= $key;
                    if (is_array($value))
                        $sefstring2 .= '[' . key($value) . '].' . $value[key($value)] . '/';
                    else
                        $sefstring2 .= '.' . $value . '/';
                }
            }
        }

        // Fix the action if it's being aliased
        if (isset($query_parts['action']) && !empty(self::$aliasactions[$query_parts['action']]))
            $query_parts['action'] = self::$aliasactions[$query_parts['action']];

        // Build the URL
        if (isset($query_parts['action']))
            $sefstring .= $query_parts['action'] . '/';
        if (isset($query_parts['user']))
            $sefstring .= $query_parts['user'] . '/';
        if (isset($sefstring2))
            $sefstring .= $sefstring2;
        if (isset($sefstring3))
            $sefstring .= $sefstring3;
        if (isset($query_parts['board']))
            $sefstring .= $query_parts['board'] . '/';
        if (isset($query_parts['topic']))
            $sefstring .= $query_parts['topic'];

        return str_replace('index.php' . (!empty($url_parts['query']) ? '?' . $url_parts['query'] : ''), $sefstring, $url); //$boardurl . '/' . $sefstring . (!empty($url_parts['fragment']) ? '#' . $url_parts['fragment'] : '');
    }

    /*     * ******************************************
     * 			Utility Functions				*
     * ****************************************** */

    /**
     * Takes in a board name and tries to determine it's id
     *
     * @global array $modSettings
     * @param string $boardName
     * @return mixed Will return false if it can't find an id or the id if found
     */
    private static function getBoardId($boardName) {
        global $modSettings;

        if (($boardId = array_search($boardName, self::$boardNames)) !== false)
            return $boardId . '.0';

        if (($index = strrpos($boardName, $modSettings['simplesef_space'])) === false)
            return false;

        $page = substr($boardName, $index + 1);
        if (is_numeric($page))
            $boardName = substr($boardName, 0, $index);
        else
            $page = '0';

        if (($boardId = array_search($boardName, self::$boardNames)) !== false)
            return $boardId . '.' . $page;
        else
            return false;
    }

    /**
     * Generates a board name from the ID.  Checks the existing array and reloads
     * it if it's not in there for some reason
     *
     * @global array $modSettings
     * @param int $id Board ID
     * @return string
     */
    private static function getBoardName($id) {
        global $modSettings;

		if (stripos($id, '.') !== false) {
			$page = substr($id, stripos($id, '.') + 1);
			$id = substr($id, 0, stripos($id, '.'));
		}

		if (empty(self::$boardNames[$id]))
			self::loadBoardNames(TRUE);
		$boardName = !empty(self::$boardNames[$id]) ? self::$boardNames[$id] : 'board';
		if (isset($page) && ($page > 0))
			$boardName = $boardName . $modSettings['simplesef_space'] . $page;
        return $boardName;
    }

    /**
     * Generates a topic name from it's id.  This is typically called from
     * create_sef_url which is called from ob_simplesef which prepopulates topics.
     * If the topic isn't prepopulated, it attempts to find it.
     *
     * @global array $modSettings
     * @global array $smcFunc
     * @param int $id
     * @return string Topic name with it's associated board name
     */
    private static function getTopicName($id) {
        global $modSettings;

        @list($value, $start) = explode('.', $id);
        if (!isset($start))
            $start = '0';
        if (!is_numeric($value))
            return 'topic' . $modSettings['simplesef_space'] . $id . '.' . $modSettings['simplesef_suffix'];

        // If the topic id isn't here (probably from a redirect) we need a query to get it
        if (empty(self::$topicNames[$value]))
            self::loadTopicNames((int) $value);

        // and if it still doesn't exist
        if (empty(self::$topicNames[$value])) {
            $topicName = 'topic';
            //$boardName = 'board';
        } else {
            $topicName = self::$topicNames[$value]['subject'];
            //$boardName = self::getBoardName(self::$topicNames[$value]['board_id']);
        }
		return self::$topics_base . $topicName . $modSettings['simplesef_space'] . $value . '.' . $start . '.' . $modSettings['simplesef_suffix'];
    }

    /**
     * Generates a username from the ID.  See above comment block for
     * pregeneration information
     *
     * @global array $modSettings
     * @global array $smcFunc
     * @param int $id User ID
     * @return string User name
     */
    private static function getUserName($id) {
        global $modSettings;

        if (!is_numeric($id))
            return 'user' . $modSettings['simplesef_space'] . $id;

        if (empty(self::$userNames[$id]))
            self::loadUserNames((int) $id);

        // And if it's still empty...
        if (empty(self::$userNames[$id]))
            return 'user' . $modSettings['simplesef_space'] . $id;
        else
            return self::$userNames[$id] . $modSettings['simplesef_space'] . $id;
    }

    /**
     * Takes the q= part of the query string passed in and tries to find out
     * how to put the URL into terms SMF can understand.  If it can't, it forces
     * the action to SimpleSEF's own 404 action and throws a nice error page.
     *
     * @global array $modSettings
     * @global string $sourcedir
     * @param string $query Querystring to deal with
     * @return array Returns an array suitable to be merged with $_GET
     */
    private static function route($query) {
        global $modSettings, $sourcedir;

        $url_parts = explode('/', trim($query, '/'));
        $querystring = array();

        $current_value = reset($url_parts);
        // Do we have an action?
        if ((in_array($current_value, self::$actions) || in_array($current_value, self::$aliasactions)) && !in_array($current_value, self::$ignoreactions) ) {
            $querystring['action'] = array_shift($url_parts);

            // We may need to fix the action
            if (($reverse_alias = array_search($current_value, self::$aliasactions)) !== FALSE)
                $querystring['action'] = $reverse_alias;
            $current_value = reset($url_parts);

            // User
            if (!empty($current_value) && in_array($querystring['action'], self::$useractions) && ($index = strrpos($current_value, $modSettings['simplesef_space'])) !== false) {
                $user = substr(array_shift($url_parts), $index + 1);
                if (is_numeric($user))
                    $querystring['u'] = intval($user);
                else
                    $querystring['user'] = $user;
                $current_value = reset($url_parts);
            }

            if (!empty(self::$extensions[$querystring['action']])) {
                require_once($sourcedir . '/SimpleSEF-Ext/' . self::$extensions[$querystring['action']]);
                $class = ucwords($querystring['action']);
                $extension = new $class();
                $querystring += $extension->route($url_parts);
                //self::log('Rerouted "' . $querystring['action'] . '" action with extension');

                // Empty it out so it's not handled by this code
                $url_parts = array();
            }
        }

        if (!empty($url_parts)) {
            $current_value = array_pop($url_parts);
            if (strrpos($current_value, $modSettings['simplesef_suffix'])) {
                // remove the suffix and get the topic id
                $topic = str_replace($modSettings['simplesef_suffix'], '', $current_value);
                $topic = substr($topic, strrpos($topic, $modSettings['simplesef_space']) + 1);
				$querystring['topic'] = $topic;
				array_pop($url_parts);
            }
            else {
                //check to see if the last one in the url array is a board
                if (preg_match('~^board_(\d+)$~', $current_value, $match))
                    $boardId = $match[1];
                else
                    $boardId = self::getBoardId($current_value);

                if ($boardId !== false)
                    $querystring['board'] = $boardId;
                else
                    array_push($url_parts, $current_value);
            }

            if (!empty($url_parts) && (strpos($url_parts[0], '.') === false && strpos($url_parts[0], ',') === false))
                $querystring['action'] = 'simplesef-404';

            // handle unknown variables
            $temp = array();
            foreach ($url_parts as $part) {
                if (strpos($part, '.') !== false)
                    $part = substr_replace($part, '=', strpos($part, '.'), 1);

                // Backwards compatibility
                elseif (strpos($part, ',') !== false)
                    $part = substr_replace($part, '=', strpos($part, ','), 1);
                parse_str($part, $temp);
                $querystring += $temp;
            }
        }

        //self::log('Rerouted "' . $query . '" to ' . var_export($querystring, TRUE));

        return $querystring;
    }

    /**
     * Loads any extensions that other mod authors may have introduced
     *
     * @global string $sourcedir
     */
    private static function loadExtensions($force = FALSE) {
        global $sourcedir;

        if ($force || (self::$extensions = CacheAPI::getCache('simplsef_extensions', 3600)) === NULL) {
            $ext_dir = $sourcedir . '/SimpleSEF-Ext';
            self::$extensions = array();
            if (is_readable($ext_dir)) {
                $dh = opendir($ext_dir);
                while ($filename = readdir($dh)) {
                    // Skip these
                    if (in_array($filename, array('.', '..')) || preg_match('~ssef_([a-zA-Z_-]+)\.php~', $filename, $match) == 0)
                        continue;

                    self::$extensions[$match[1]] = $filename;
                }
            }

            CacheAPI::putCache('simplesef_extensions', self::$extensions, 3600);
            //self::log('Cache hit failed, reloading extensions');
        }
    }

    /**
     * Loads all board names from the forum into a variable and cache (if possible)
     * This helps reduce the number of queries needed for SimpleSEF to run
     *
     * @global array $smcFunc
     * @global string $language
     * @param boolean $force Forces a reload of board names
     */
    private static function loadBoardNames($force = FALSE) {
        global $language;

		if ($force || (self::$boardNames = CacheAPI::getCache('simplesef_board_list', 3600)) == NULL) {
            loadLanguage('index', $language, false);
            $request = smf_db_query( '
				SELECT id_board, name
				FROM {db_prefix}boards', array()
            );
            $boards = array();
            while ($row = mysql_fetch_assoc($request)) {
                // A bit extra overhead to account for duplicate board names
                $temp_name = self::encode($row['name']);
                $i = 0;
                while (!empty($boards[$temp_name . (!empty($i) ? $i + 1 : '')]))
                    $i++;
                //$boards[$temp_name . (!empty($i) ? $i + 1 : '')] = $row['id_board'];
				$boards[$temp_name . '.'.trim($row['id_board'])] = $row['id_board'];
            }
            mysql_free_result($request);

            self::$boardNames = array_flip($boards);

            // Add one to the query cound and put the data into the cache
            self::$queryCount++;
            CacheAPI::putCache('simplesef_board_list', self::$boardNames, 3600);
            //self::log('Cache hit failed, reloading board names');
        }
    }

    /**
     * Takes one or more topic id's, grabs their information from the database
     * and stores it for later use.  Helps keep queries to a minimum.
     *
     * @global array $smcFunc
     * @param mixed $ids Can either be a single id or an array of ids
     */
    private static function loadTopicNames($ids) {

        $ids = is_array($ids) ? $ids : array($ids);

        // Fill the topic 'cache' in one fell swoop
        $request = smf_db_query( '
			SELECT t.id_topic, m.subject, t.id_board
			FROM {db_prefix}topics AS t
				INNER JOIN {db_prefix}messages AS m ON (m.id_msg = t.id_first_msg)
			WHERE t.id_topic IN ({array_int:topics})', array(
            'topics' => $ids,
            )
        );
        while ($row = mysql_fetch_assoc($request)) {
            self::$topicNames[$row['id_topic']] = array(
                'subject' => self::encode($row['subject']),
                'board_id' => $row['id_board'],
            );
        }
        mysql_free_result($request);
        self::$queryCount++;
    }

    /**
     * Takes one or more user ids and stores the usernames for those users for
     * later user
     *
     * @global array $smcFunc
     * @param mixed $ids can be either a single id or an array of them
     */
    private static function loadUserNames($ids) {

        $ids = is_array($ids) ? $ids : array($ids);

        $request = smf_db_query( '
			SELECT id_member, real_name
			FROM {db_prefix}members
			WHERE id_member IN ({array_int:members})', array(
            'members' => $ids,
            )
        );
        while ($row = mysql_fetch_assoc($request))
            self::$userNames[$row['id_member']] = self::encode($row['real_name']);
        mysql_free_result($request);
        self::$queryCount++;
    }

    /**
     * The encode function is responsible for transforming any string of text
     * in the URL into something that looks good and representable.  For forums
     * not using ASCII or UTF8 character sets, we convert them to utf8 and then
     * transliterate them.
     *
     * @global array $modSettings
     * @global string $sourcedir
     * @global array $txt
     * @staticvar array $utf8_db
     * @param string $string String to encode
     * @return string Returns an encoded string
     */
    public static function encode($string) {
        global $modSettings;

        if (empty($string))
            return '';

		setlocale(LC_CTYPE, 'en_US.UTF8');
   		$string = iconv('UTF-8', "UTF-8//TRANSLIT", $string); // TRANSLIT does the whole job
       	$string = implode(' ', array_diff(explode(' ', $string), self::$stripWords));
       	$string = str_replace(self::$stripChars, '', $string);
   		$string = preg_replace('~[^\\pL0-9_]+~u', $modSettings['simplesef_space'], $string); // substitutes anything but letters, numbers and '_' with separator
       	if (!empty($modSettings['simplesef_lowercase']))
           	$string = strtolower($string);

		return($string);
    }

	public static function encodeTest($string)
	{
		global $modSettings, $sourcedir;
  		global $utf8_db;
		$utf8_db = array();

        $character = 0;
        // Gotta return something...
        $result = '';

        $length = strlen($string);
        $i = 0;

        while ($i < $length) {
            $charInt = ord($string[$i++]);
	    	$character = $charInt;

            if (($charInt & 0x80) == 0) {
                $character = $charInt;
            }
			/*
            // Two byte unicode character
            elseif (($charInt & 0xE0) == 0xC0) {
                $temp1 = ord($string[$i++]);
                if (($temp1 & 0xC0) != 0x80)
                    $character = 63;
                else
                    $character = ($charInt & 0x1F) << 6 | ($temp1 & 0x3F);
            }
            // Three byte unicode character
            elseif (($charInt & 0xF0) == 0xE0) {
                $temp1 = ord($string[$i++]);
                $temp2 = ord($string[$i++]);
                if (($temp1 & 0xC0) != 0x80 || ($temp2 & 0xC0) != 0x80)
                    $character = 63;
                else
                    $character = ($charInt & 0x0F) << 12 | ($temp1 & 0x3F) << 6 | ($temp2 & 0x3F);
            }
            // Four byte unicode character
            elseif (($charInt & 0xF8) == 0xF0) {
                $temp1 = ord($string[$i++]);
                $temp2 = ord($string[$i++]);
                $temp3 = ord($string[$i++]);
                if (($temp1 & 0xC0) != 0x80 || ($temp2 & 0xC0) != 0x80 || ($temp3 & 0xC0) != 0x80)
                    $character = 63;
                else
                    $character = ($charInt & 0x07) << 18 | ($temp1 & 0x3F) << 12 | ($temp2 & 0x3F) << 6 | ($temp3 & 0x3F);
            }
            // More than four bytes... ? mark
            else
                $character = 63;
            */
            // Need to get the bank this character is in.

	    	$charBank = $character >> 8;
            if (!isset($utf8_db[$charBank])) {
                // Load up the bank if it's not already in memory
                $dbFile = $sourcedir . 'SimpleSEF-Db/x' . sprintf('%02x', $charBank) . '.php';
				@include_once($dbFile);
                //if (!is_readable($dbFile) || !@include_once($dbFile))
                //    $utf8_db[$charBank] = array();
            }
            $finalChar = $character & 255;
            $result .= isset($utf8_db[$charBank][$finalChar]) ? $utf8_db[$charBank][$finalChar] : '?';
        }
		return;
        // Update the string with our new string
        $string = (string)$result;

        $string = implode(' ', array_diff(explode(' ', $string), self::$stripWords));
        $string = str_replace(self::$stripChars, '', $string);
        $string = trim($string, " $modSettings[simplesef_space]\t\n\r");
        $string = urlencode($string);
        $string = str_replace('%2F', '', $string);
        $string = str_replace($modSettings['simplesef_space'], '+', $string);
        $string = preg_replace('~(\+)+~', $modSettings['simplesef_space'], $string);
        if (!empty($modSettings['simplesef_lowercase']))
            $string = strtolower($string);
        return $string;
	}

    /**
     * Helper function to properly explode a CSV list (Accounts for quotes)
     *
     * @param string $str String to explode
     * @return array Exploded string
     */
    private static function explode_csv($str) {
        return!empty($str) ? preg_replace_callback('/^"(.*)"$/', create_function('$match', 'return trim($match[1]);'), preg_split('/,(?=(?:[^"]*"[^"]*")*(?![^"]*"))/', trim($str))) : array();
    }

    /**
     * Small helper function for benchmarking SimpleSEF.  It's semi smart in the
     * fact that you don't need to specify a 'start' or 'stop'... just pass the
     * 'marker' twice and that starts and stops it automatically and adds to the total
     *
     * @param string $marker
     */
    private static function benchmark($marker) {
        if (!empty(self::$benchMark['marks'][$marker])) {
            self::$benchMark['marks'][$marker]['stop'] = microtime(TRUE);
            self::$benchMark['total'] += self::$benchMark['marks'][$marker]['stop'] - self::$benchMark['marks'][$marker]['start'];
        }
        else
            self::$benchMark['marks'][$marker]['start'] = microtime(TRUE);
    }

    /**
     * Simple function to aide in logging debug statements
     * May pass as many simple variables as arguments as you wish
     *
     * @global array $modSettings
     */
    private static function log() {
        global $modSettings;

        if (!empty($modSettings['simplesef_debug']))
            foreach (func_get_args() as $string)
                log_error($string, 'debug', __FILE__);
    }

}
