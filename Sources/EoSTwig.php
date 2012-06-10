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

	public static function init($sourcedir, $themedir, $boarddir)
	{
		@require_once($sourcedir . '/lib/Twig/lib/Twig/Autoloader.php');
		Twig_Autoloader::register();

		self::$_twig_loader_instance = new Twig_Loader_Filesystem($themedir . '/twig');
		self::$_twig_environment = new Twig_Environment(self::$_twig_loader_instance, array('strict_variables' => true, 'cache' => $boarddir . 'template_cache', 'auto_reload' => true, 'autoescape' => false));
	}	

	public static function &loadTemplate($_template_name)
	{
		self::$_the_template = self::$_twig_environment->loadTemplate($_template_name);
	}

	public static function Display()
	{
		global $context, $settings, $modSettings, $options, $txt, $scripturl, $user_info;

		/*$twig_globals = array('c' => &$context, 
							's' => &$settings, 
							'm' => &$modSettings, 
							't' => &$txt, 
							'scripturl' => $scripturl,
							'u' => $user_info
						);
		*/
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

		$context['test_value_twig'] = 'foobar';
		$context['test_value_twig1'] = 'foobar more';
		//self::$_twig_environment->addGlobal('G', $twig_globals);
		self::$_the_template->display(array('pageTitle' => 'Twig output test',
								 		'C' => &$context,
								 		'T' => &$txt,
								 		'S' => &$settings,
								 		'O' => &$options,
								 		'M' => &$modSettings,
								 		'U' => &$user_info,
								 		'SCRIPTURL' => $scripturl
								 	  ));
	}

	// Ends execution.  Takes care of template loading and remembering the previous URL.
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

		// Show the load time?  (only makes sense for the footer.)
		$context['show_load_time'] = !empty($modSettings['timeLoadPageEnable']);
		$context['load_time'] = round(array_sum(explode(' ', microtime())) - array_sum(explode(' ', $time_start)), 3);
		$context['load_queries'] = $db_count;

		if (isset($settings['use_default_images']) && $settings['use_default_images'] == 'defaults' && isset($settings['default_template']))
		{
			$settings['theme_url'] = $settings['actual_theme_url'];
			$settings['images_url'] = $settings['actual_images_url'];
			$settings['theme_dir'] = $settings['actual_theme_dir'];
		}
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

			// Are we debugging the template/html content?
			if (!isset($_REQUEST['xml']) && isset($_GET['debug']) && !$context['browser']['is_ie'] && !WIRELESS)
				header('Content-Type: application/xhtml+xml');
			elseif (!isset($_REQUEST['xml']) && !WIRELESS)
				header('Content-Type: text/html; charset=UTF-8');
		}

		header('Content-Type: text/' . (isset($_REQUEST['xml']) ? 'xml' : 'html') . '; charset=UTF-8');

		$checked_securityFiles = false;
		$showed_banned = false;
		foreach ($context['template_layers'] as $layer)
		{
			// May seem contrived, but this is done in case the body and main layer aren't there...
			if (in_array($layer, array('body', 'main')) && allowedTo('admin_forum') && !$user_info['is_guest'] && !$checked_securityFiles)
			{
				$checked_securityFiles = true;
				$securityFiles = array('install.php', 'webinstall.php', 'upgrade.php', 'convert.php', 'repair_paths.php', 'repair_settings.php', 'Settings.php~', 'Settings_bak.php~');
				foreach ($securityFiles as $i => $securityFile)
				{
					if (!file_exists($boarddir . '/' . $securityFile))
						unset($securityFiles[$i]);
				}

				if (!empty($securityFiles))
				{
					echo '
			<div class="errorbox">
				<p class="alert">!!</p>
				<h3>', $txt['security_risk'], '</h3>
				<p>';

					foreach ($securityFiles as $securityFile)
					{
						echo '
					', $txt['not_removed'], '<strong>', $securityFile, '</strong>!<br />';

						if ($securityFile == 'Settings.php~' || $securityFile == 'Settings_bak.php~')
							echo '
					', sprintf($txt['not_removed_extra'], $securityFile, substr($securityFile, 0, -1)), '<br />';
					}
					echo '
				</p>
			</div>';
				}
			}
			// If the user is banned from posting inform them of it.
			elseif (in_array($layer, array('main', 'body')) && isset($_SESSION['ban']['cannot_post']) && !$showed_banned)
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

		if (isset($settings['use_default_images']) && $settings['use_default_images'] == 'defaults' && isset($settings['default_template']))
		{
			$settings['theme_url'] = $settings['default_theme_url'];
			$settings['images_url'] = $settings['default_images_url'];
			$settings['theme_dir'] = $settings['default_theme_dir'];
		}
	}
}

function TwigTest()
{
	global $context, $sourcedir, $settings, $boarddir;

	EoS_Twig::init($sourcedir, $settings['theme_dir'], $boarddir);

	$_the_template = &EoS_Twig::loadTemplate('twigtest.html');

	$context['twig_template'] = true;
}
?>