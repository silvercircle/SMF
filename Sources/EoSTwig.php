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
 * Twig template engine playground (very, very experimental)
 */

class EoS_Twig {
	private static $_twig_environment;
	private static $_twig_loader_instance;
	private static $_the_template;
	private static $_template_name = '';
	private static $_template_blocks = array();

	public static function init()
	{
		global $sourcedir, $settings, $boarddir;	

		@require_once($sourcedir . '/lib/Twig/lib/Twig/Autoloader.php');
		Twig_Autoloader::register();

		self::$_twig_loader_instance = new Twig_Loader_Filesystem($settings['theme_dir'] . '/twig');
		self::$_twig_environment = new Twig_Environment(self::$_twig_loader_instance, 
			array('strict_variables' => true, 
				  'cache' => $boarddir . 'template_cache', 'auto_reload' => true, 'autoescape' => false));
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
		self::$_template_name = $_template_name . '.twig';
	}

	/**
	 * @static
	 * @param $_blocks array() of block names to display.
	 *
	 * Smaller templates may be combined in form of blocks into a single (larger)
	 * template files. This function allows to specify a list of blocks that should
	 * be rendered instead of the entire template.
	 */
	public static function setBlocks($_blocks)
	{
		self::$_template_blocks = !is_array($_blocks) ? array($_blocks) : $_blocks;
	}
	/**
	 * output all enqued footer scripts.
	 * used as custom template function
	 */
	public static function footer_scripts()
	{
		global $context, $settings;

		if(!empty($context['theme_scripts'])) {
			foreach($context['theme_scripts'] as $type => $script) {
				if($script['footer'])
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
		if(isset($context['footer_script_fragments'])) {
			foreach($context['footer_script_fragments'] as $this_script)
				echo $this_script;
		}
		echo '
		// ]]>
		</script>
		';
	}

	/**
	 * does absolutely nothing
	 * used as dummy for custom callback functions
	 */
	public static function dummy() {}

	/**
	 * @static
	 * @param array $button_strip
	 * @param string $direction
	 * @param array $strip_options
	 * @return mixed
	 *
	 * Render a button strip. TODO: this should be converted into a template.
	 */
	public static function button_strip($button_strip, $direction = 'top', $strip_options = array())
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
	 * @static
	 * set up the template context, load and display the template
	 * this does everything needed to get our ouput
	 */
	public static function Display()
	{
		global $context, $settings, $modSettings, $options, $txt, $scripturl, $user_info, $cookiename;
		global $forum_copyright, $forum_version, $time_start, $db_count;

		$functions = array(
				'output_footer_scripts' => 'EoS_Twig::footer_scripts',
				'url_action' => 'URL::action',
				'url_user' => 'URL::user',
				'url_parse' => 'URL::parse',
				'array_search' => 'array_search',
				'sprintf' => 'sprintf',
				'implode' => 'implode',
				'explode' => 'explode',
				'button_strip' => 'EoS_Twig::button_strip',
				'comma_format' => 'comma_format',
				'timeformat' => 'timeformat'
			);

		$settings['theme_variants'] = array('default', 'lightweight');
		$settings['clip_image_src'] = array(
			'_default' => 'clipsrc.png',
		    '_lightweight' => 'clipsrc_l.png',
			'_dark' => 'clipsrc_dark.png'
		);
		$settings['sprite_image_src'] = array(
			'_default' => 'theme/sprite.png',
			'_lightweight' => 'theme/sprite.png',
			'_dark' => 'theme/sprite.png'
		);

  		$context['template_time_now'] = forum_time(false);
  		$context['template_timezone'] = date_default_timezone_get();
		$context['template_time_now_formatted'] = strftime($modSettings['time_format'], $context['template_time_now']);
		$context['template_allow_rss'] = (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']));
		$context['template_copyright'] = sprintf($forum_copyright, $forum_version);
  		$context['inline_footer_script'] .= $txt['jquery_timeago_loc'];
		$context['show_load_time'] = !empty($modSettings['timeLoadPageEnable']);
		$context['load_time'] = round(array_sum(explode(' ', microtime())) - array_sum(explode(' ', $time_start)), 3);
		$context['load_queries'] = $db_count;

		if (isset($settings['use_default_images']) && $settings['use_default_images'] == 'defaults' && isset($settings['default_template']))
		{
			$settings['theme_url'] = $settings['actual_theme_url'];
			$settings['images_url'] = $settings['actual_images_url'];
			$settings['theme_dir'] = $settings['actual_theme_dir'];
		}

  		if(isset($modSettings['embed_GA']) && $modSettings['embed_GA'] && ($context['user']['is_guest'] || (empty($options['disable_analytics']) ? 1 : !$options['disable_analytics'])))
  			$context['want_GA_embedded'] = true;

  		/*
  		 * set up functions
  		 */
  		foreach($functions as $fn => $name)
  			self::$_twig_environment->addFunction($fn, new Twig_Function_Function($name));

		$twig_context = array('C' => &$context, 'T' => &$txt, 'S' => &$settings, 'O' => &$options,
								 		'M' => &$modSettings, 'U' => &$user_info, 'SCRIPTURL' => $scripturl, 'COOKIENAME' => $cookiename,
								 		'_COOKIE' => &$_COOKIE);

		self::$_the_template = self::$_twig_environment->loadTemplate(self::$_template_name);
		if(!empty(self::$_template_blocks)) {
			foreach(self::$_template_blocks as $block)
				self::$_the_template->displayBlock($block, $twig_context);
		}
		else
			self::$_the_template->display($twig_context, self::$_template_blocks);
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

			// Anything special to put out?
			if (!empty($context['insert_after_template']) && !isset($_REQUEST['xml']))
				echo $context['insert_after_template'];

			EoS_Twig::Display();
			// Just so we don't get caught in an endless loop of errors from the footer...
			if (!$footer_done)
			{
				$footer_done = true;
				self::template_footer();

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

	public static function template_footer()
	{
		global $context, $settings, $modSettings, $time_start, $db_count;
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

		if (self::$_template_name == 'boardindex.twig' && allowedTo('admin_forum') && !$user_info['is_guest'] && !$checked_securityFiles)
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
		elseif (self::$_template_name == 'boardindex.twig' && isset($_SESSION['ban']['cannot_post']) && !$showed_banned)
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

		if (isset($settings['use_default_images']) && $settings['use_default_images'] == 'defaults' && isset($settings['default_template']))
		{
			$settings['theme_url'] = $settings['default_theme_url'];
			$settings['images_url'] = $settings['default_images_url'];
			$settings['theme_dir'] = $settings['default_theme_dir'];
		}
	}
}
?>