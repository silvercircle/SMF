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
 * this implements smarty template handling.
 */
if (!defined('SMF'))
	die('No access.');

class EoS_Smarty {
	private static $_template_names = array();
	private static $_additional_template_dirs = array();
	private static $_smartyInstance;
	private static $_configInstance;
	private static $_is_BoardIndex = false;
	private static $_is_Active = false;
	private static $_is_Debug = false;

	/**
	 * init smarty engine and custom theme support object
	 *
	 * this is called once from index.php when ALL other initializations
	 * are complete.
	 */
	public static function init($debug = false)
	{
		global $sourcedir, $settings, $boarddir, $context;

		self::$_is_Debug = $debug;
		@require_once($sourcedir . '/lib/Smarty/Smarty.class.php');
		self::$_smartyInstance = new Smarty();
		self::$_smartyInstance->caching = 0;		// this is *STATIC* caching of generated pages, we don't want (or even need) this for a forum...
		if(is_callable('theme_support_autoload'))	// theme_support.php (if present) was previously loaded in loadTheme().
			self::$_configInstance = theme_support_autoload(self::$_smartyInstance);
		else
			self::$_configInstance = new EoS_Smarty_Template_Support(self::$_smartyInstance);

		$firstdir = 0;
		if(MOBILE) {
			self::$_smartyInstance->setTemplateDir($settings['default_theme_dir'] . '/m');
			$firstdir++;
		}
		if($settings['allow_template_overrides']) {			// allow overrides (mod setting)
			self::$_smartyInstance->setTemplateDir($settings['default_theme_dir'] . '/tpl/overrides');
			$firstdir++;
		}
		foreach($settings['template_dirs'] as $dir) {
			if(!$firstdir)
				self::$_smartyInstance->setTemplateDir($dir . '/tpl');
			else
				self::$_smartyInstance->addTemplateDir($dir . '/tpl');
			$firstdir++;
		}
		self::$_smartyInstance->setCompileDir(rtrim($boarddir, '/') . '/template_cache');		// TODO: make this customizable

		$context['clip_image_src'] = $settings['images_url'] . '/' . $settings['clip_image_src'][$context['theme_variant']];
		$context['sprite_image_src'] = $settings['images_url'] . '/' . $settings['sprite_image_src'][$context['theme_variant']];

		/*
		 * this hook could be used to re-configure smarty (for example, add additional template dir(s)),
		 * or register hook template fragments via $_configInstance->registerTemplateHook()
		 */
		HookAPI::callHook('smarty_init', array(&self::$_smartyInstance, &self::$_configInstance));
	}	

	/**
	 * @static
	 * @param string - $_template_name the desired template name that should be loaded
	 * 		  without file name extension and path information.
	 *
	 * The template is not immediately loaded, this functionm merely remembers the template
	 * name. The template must be loaded *after* the full context has been setup and this
	 * happens in Display().
	 */
	public static function loadTemplate($_template_name)
	{
		self::$_template_names[] = $_template_name . '.tpl';
		if($_template_name === 'boardindex')
			self::$_is_BoardIndex = true;

		self::$_is_Active = true;			// set us active, so we can rule in obExit()
	}

	/**
	 * clear all templates that have been loaded
	 * this is needed in a few places (e.g. setup_fatal_error_context) when we have
	 * to discard all previously loaded templates.
	 */
	public static function resetTemplates()
	{
		self::$_template_names = array();
	}

	/**
	 * @static
	 * @return array of template names
	 */
	public static function &getTemplates()
	{
		return self::$_template_names;
	}

	/**
	 * @static
	 * @param string - $_dir. The template directory to add
	 *
	 * add a new template directory. this will typically be used by plugins to add
	 * additional template directories.

	 * todo: add some debugging support to assist plugin authors (e.g. check whether
	 * the directory does actually exist).
	 */
	public static function addTemplateDir($_dir)
	{
		/*
		 * since this function could - in theory - been called before the init()
		 * method, but additional (= plugin-hosted) template dirs *must* exist at the end 
		 * of the list, we just remember them here. it will also make things faster, because
		 * we can add all template directories with a single call to Smarty::addTemplateDir()
		 */
		self::$_additional_template_dirs[] = $_dir;
	}
	/**
	 * does absolutely nothing
	 * used as dummy for custom callback functions
	 */
	public static function dummy() {}

	public static function isActive() { return self::$_is_Active; }

	public static function setActive() { self::$_is_Active = true; }
	/**
	 * output all enqued footer scripts.
	 * used as custom template function
	 */
	public static function footer_scripts()
	{
		self::$_configInstance->footer_scripts();
	}

	public static function &getConfigInstance()
	{
		return self::$_configInstance;
	}
	public static function &getSmartyInstance()
	{
		return self::$_smartyInstance;
	}
	/**
	 * @static
	 * set up the template context, load and display the template
	 * this does everything needed to get our ouput
	 */
	public static function Display()
	{
		global $context;
	
		if(!empty(self::$_additional_template_dirs))
			self::$_smartyInstance->addTemplateDir(self::$_additional_template_dirs);

  		$context['template_benchmark'] = microtime();
  		self::$_configInstance->setupContext();
  		foreach(self::$_template_names as $the_template)
			self::$_smartyInstance->display($the_template);
	}

	// Ends execution.  Takes care of template loading and remembering the previous URL.
	// this is for twig templates ONLY
	public static function obExit($header = null, $do_footer = null, $from_index = false, $from_fatal_error = false)
	{
		global $context, $modSettings;
		static $header_done = false, $footer_done = false, $level = 0, $has_fatal_error = false;

		// Attempt to prevent a recursive loop.
		++$level;
		if ($level > 1 && !$from_fatal_error && !$has_fatal_error)
			exit;
		if ($from_fatal_error)
			$has_fatal_error = true;

		// Clear out the stat cache.
		trackStats();

		// If we have mail to send, send it.
		if (!empty($context['flush_mail']))
			AddMailQueue(true);

		$do_header = $header === null ? !$header_done : $header;
		if ($do_footer === null)
			$do_footer = $do_header;

		// Has the template/header been done yet?
		if ($do_header)
		{
			// Was the page title set last minute? Also update the HTML safe one.
			if (!empty($context['page_title']) && empty($context['page_title_html_safe']))
				$context['page_title_html_safe'] = $context['forum_name_html_safe'] . ' - ' . commonAPI::htmlspecialchars(un_htmlspecialchars($context['page_title']));

			// Start up the session URL fixer.
			ob_start('ob_sessrewrite');

			HookAPI::integrateOB();

			//if(!empty($modSettings['simplesef_enable']))
			//	ob_start('SimpleSEF::ob_simplesef');

			// Display the screen in the logical order.
			self::template_header();
			$header_done = true;
		}
		if ($do_footer)
		{
			if (WIRELESS && !isset($context['sub_template']))
				fatal_lang_error('wireless_error_notyet', false);

			self::Display();
			// Just so we don't get caught in an endless loop of errors from the footer...
			if (!$footer_done)
			{
				$footer_done = true;

				// (since this is just debugging... it's okay that it's after </html>.)
				if (!isset($_REQUEST['xml']))
					db_debug_junk();
			}
		}

		// Remember this URL in case someone doesn't like sending HTTP_REFERER.
		if (strpos($_SERVER['REQUEST_URL'], 'action=dlattach') === false && strpos($_SERVER['REQUEST_URL'], 'action=viewsmfile') === false)
			$_SESSION['old_url'] = $_SERVER['REQUEST_URL'];

		// For session check verfication.... don't switch browsers...
		$_SESSION['USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];

		// Hand off the output to the portal, etc. we're integrated with.
		HookAPI::callHook('integrate_exit', array($do_footer));

		if(!empty($modSettings['simplesef_enable']))
			SimpleSEF::fixXMLOutput($do_footer);

		// Don't exit if we're coming from index.php; that will pass through normally.
		if (!$from_index)
			exit;
	}

	public static function template_header()
	{
		global $txt, $modSettings, $context, $settings, $user_info, $boarddir, $cachedir;

		setupThemeContext();

		// Print stuff to prevent caching of pages (except on attachment errors, etc.)
		if (empty($context['no_last_modified']))
		{
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			if(!isset($_REQUEST['xml']) && !WIRELESS)
				header('Content-Type: text/html; charset=UTF-8');
		}

		header('Content-Type: text/' . (isset($_REQUEST['xml']) ? 'xml' : 'html') . '; charset=UTF-8');

		$checked_securityFiles = false;
		$showed_banned = false;

		if (self::$_is_BoardIndex && allowedTo('admin_forum') && !$user_info['is_guest'] && !$checked_securityFiles)
		{
			$checked_securityFiles = true;
			$securityFiles = array('install.php', 'upgrade.php', 'repair_settings.php', 'Settings.php~', 'Settings_bak.php~');
			foreach ($securityFiles as $i => $securityFile)
			{
				if (!file_exists($boarddir . '/' . $securityFile))
					unset($securityFiles[$i]);
			}

			if (!empty($securityFiles))
			{
				$context['additional_admin_errors'] .= '
		<div class="errorbox">
			<p class="alert">!!</p>
			<h3>' . $txt['security_risk'] . '</h3>
			<p>';

				foreach ($securityFiles as $securityFile)
				{
					$context['additional_admin_errors'] .=  '
				'. $txt['not_removed']. '<strong>'. $securityFile. '</strong>!<br />';

					if ($securityFile == 'Settings.php~' || $securityFile == 'Settings_bak.php~')
						$context['additional_admin_errors'] .= '
				'. sprintf($txt['not_removed_extra']. $securityFile. substr($securityFile, 0, -1)). '<br />';
				}
				$context['additional_admin_errors'] .= '
			</p>
		</div>';
			}
		}
		// If the user is banned from posting inform them of it.
		elseif (self::$_is_BoardIndex && isset($_SESSION['ban']['cannot_post']) && !$showed_banned)
		{
			$showed_banned = true;
			echo '
				<div class="windowbg alert" style="margin: 2ex; padding: 2ex; border: 2px dashed red;">
					', sprintf($txt['you_are_post_banned'], $user_info['is_guest'] ? $txt['guest_title'] : $user_info['name']);

			if (!empty($_SESSION['ban']['cannot_post']['reason']))
				echo '
					<div style="padding-left: 4ex; padding-top: 1ex;">', $_SESSION['ban']['cannot_post']['reason'], '</div>';

			if (!empty($_SESSION['ban']['expire_time']))
				echo '
					<div>', sprintf($txt['your_ban_expires'], timeformat($_SESSION['ban']['expire_time'], false)), '</div>';
			else
				echo '
					<div>', $txt['your_ban_expires_never'], '</div>';

			echo '
				</div>';
		}
	}
}

/**
 * this class is the base for the smarty template support class.
 * Theme authors can inherit from it to provide their own 
 *
 * particularly, it allows to:
 *
 * #) customize postbit behavior by modifiying the default _postbitClasses
 *    array
 * #) implement and/or override theme functions (e.g. button_strip()). All
 *    public functions of this class are available in smarty templates through
 *    the $SUPPORT object.
 * #) implement smarty plugins through $this->_smarty_instance
 * #) extend setupContext() to implement own theme extensions.
 *
 * for template developers, this object is exposed to the template
 * engine via the $SUPPORT variable. 
 */
class EoS_Smarty_Template_Support {
	
	protected $_template_overrides = array();
	protected $_subtemplates = array();
	protected $_smarty_instance;
	protected $_postbitClasses = array();
	protected $_hook_templates = array();

	public function __construct(Smarty $smarty_instance) 
	{
		$this->_smarty_instance = $smarty_instance;
  		$this->_smarty_instance->assignByRef('SUPPORT', $this);

  		$this->_postbitClasses = array(
  			'normal' => 'n',
  			'commentstyle' => 'c',
  			'article' => 'a',
  			'lean' => 'l'
  			);
	}
	/**
	 * @param array $button_strip
	 * @param string $direction
	 * @param array $strip_options
	 * @return mixed
	 *
	 * Render a button strip. TODO: this should be converted into a template.
	 */
	public function button_strip(array $button_strip, $direction = 'top', $strip_options = array())
	{
		global $context, $txt;

		if (!is_array($strip_options))
			$strip_options = array();

		// List the buttons in reverse order for RTL languages.
		if ($context['right_to_left'])
			$button_strip = array_reverse($button_strip, true);

		// Create the buttons...
		$buttons = array();
		foreach ($button_strip as $key => $value)
		{
			if (!isset($value['test']) || !empty($context[$value['test']]))
				$buttons[] = '
					<li><a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_strip_' . $key . (isset($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a></li>';
		}

		// No buttons? No button strip either.
		if (empty($buttons))
			return;

		// Make the last one, as easy as possible.
		$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

		if(!isset($strip_options['class']))
			$strip_options['class'] = 'buttonlist';

		echo '
			<div class="',$strip_options['class'], !empty($direction) ? ' float' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
				<ul class="',$strip_options['class'],'">',
					implode('', $buttons), '
				</ul>
			</div>';
	}

	/**
	 * output all enqued footer scripts.
	 * used as custom template function
	 */
	public function footer_scripts()
	{
		global $context, $settings;

		if(!empty($context['theme_scripts'])) {
			foreach($context['theme_scripts'] as $type => $script) {
				echo '
		<script type="text/javascript" src="',($script['default'] ? $settings['default_theme_url'] : $settings['theme_url']) . '/' . $script['name'] . $context['jsver'], '"></script>';
			}
		}
		if(!empty($context['inline_footer_script']))
			echo '
		<script type="text/javascript">
		<!-- // --><![CDATA[
		',$context['inline_footer_script'],'

		';
		echo '
		// ]]>
		</script>
		';
	}
	/*
	 * some common functions that should be available in templates
	 */
	
	/*
	 * used in topic display as template function. fetch and prepare the next message from the database result
	 */
	public function getMessage()
	{
		prepareDisplayContext();
	}
	/*
	 * make the url generation api available to templates
	 */
	public function url_user($id, $name)
	{
		return URL::user($id, $name);
	}
	public function url_parse($uri)
	{
		return URL::parse($uri);
	}
	public function url_action($action)
	{
		return URL::action($action);
	}
	public function JavaScriptEscape($string)
	{
		return JavaScriptEscape($string);
	}
	public function getPostbitClasses()
	{
		return $this->_postbitClasses;
	}
	public function setupContext()
	{
		global $context, $settings, $modSettings, $options, $txt;
		global $forum_copyright, $forum_version;

  		$context['template_time_now'] = forum_time(false);
  		$context['template_timezone'] = date_default_timezone_get();
		$context['template_time_now_formatted'] = strftime($modSettings['time_format'], $context['template_time_now']);
		$context['template_allow_rss'] = (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']));
		$context['template_copyright'] = sprintf($forum_copyright, $forum_version);
  		$context['inline_footer_script'] .= $txt['jquery_timeago_loc'];
		$context['show_load_time'] = !empty($modSettings['timeLoadPageEnable']);

		if (isset($settings['use_default_images']) && $settings['use_default_images'] == 'defaults' && isset($settings['default_template']))
		{
			$settings['theme_url'] = $settings['actual_theme_url'];
			$settings['images_url'] = $settings['actual_images_url'];
			$settings['theme_dir'] = $settings['actual_theme_dir'];
		}

		$this->assignGlobals();
  		$context['collapsed_containers'] = isset($_COOKIE['SF_collapsed']) ? explode(',', $_COOKIE['SF_collapsed']) : array(0);
  		/*
  		 * hook to extend theme context initialization.
  		 */
  		HookAPI::callHook('smarty_init_context', array(&$this));
	}

	/**
	 * @
	 * globals that must be available to all templates by default
	 */
	public function assignGlobals()
	{
		global $context, $settings, $modSettings, $options, $txt, $scripturl, $user_info, $cookiename;

  		$this->_smarty_instance->assignByRef('C', $context);
  		$this->_smarty_instance->assignByRef('T', $txt);
  		$this->_smarty_instance->assignByRef('M', $modSettings);
  		$this->_smarty_instance->assignByRef('S', $settings);
  		$this->_smarty_instance->assignByRef('O', $options);
  		$this->_smarty_instance->assignByRef('U', $user_info);
  		$this->_smarty_instance->assignByRef('SCRIPTURL', $scripturl);
  		$this->_smarty_instance->assignByRef('COOKIENAME', $cookiename);
  		$this->_smarty_instance->assignByRef('_COOKIE', $_COOKIE);
  		$this->_smarty_instance->assign('SID', SID != '' ? '&' . SID : '');
	}

	/**
	 * @static
	 * @param $position - string. indicates where the template fragment 
	 *        should go.
	 * @param $template_name a template file name. must be relative to the
	 *        one of the configured template directories. can be either
	 *		  a string (single template) or an array of template fragments
	 */
	public function registerHookTemplate($position, $template_name)
	{
		if(!is_array($template_name))
			$this->_hook_templates[$position][] = $template_name . '.tpl';
		else {
			foreach($template_name as $name)
				$this->_hook_templates[$position][] = $name . '.tpl';
		}
	}

	/**
	 * @static
	 * 
	 * return formatted list of registered hooks. Used for debugging purposes
	 * only
	 *
	 * todo: does this need translation?
	 */
	public function getHookDebugInfo()
	{
		$parts = array();
		foreach($this->_hook_templates as $position => $hook)
			$parts[] = '<strong>' . $position . ':</strong> ' . implode(', ', $hook);

		return !empty($parts) ? ('<span style="font-size:1.1em;color:red;"><strong>Hook templates:</strong></span><br>' . implode('<br>', $parts)) : '';
	}
	/**
	 * @static
	 * @param $position - string. 
	 * 
	 * output all chained template fragments for the given hook position.
	 * example (in a template file): {$SUPPORT->displayHook('above_index')}
	 */
	public function displayHook($position)
	{
		if(isset($this->_hook_templates[$position])) {
			foreach($this->_hook_templates[$position] as $the_template)
				$this->_smarty_instance->display($the_template);
		}
	}
}
?>