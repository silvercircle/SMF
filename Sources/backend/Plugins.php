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

if (!defined('EOSA'))
	die('No access');

class EoS_Plugin_Loader
{
	public static function install($product)
	{
		global $modSettings;

		$installedPlugins = @unserialize($modSettings['plugins']);

		$pluginMain = HookAPI::getAddonsDir() . $product . '/main.php';
		@require_once($pluginMain);
		$autoloader = $product . '_autoloader';

		if(is_callable($autoloader)) {
			$pluginInstance = $autoloader();
			$pluginInstance->installHooks();
			$installedPlugins[$product] = array(
				'name' => $pluginInstance->Name,
				'version' => $pluginInstance->Version
			);
			updateSettings(array('plugins' => @serialize($installedPlugins)));
			return true;
		}
		else {
			// todo: error handling
		}
	}

	public static function uninstall($product)
	{
		global $modSettings;

		$pluginMain = HookAPI::getAddonsDir() . $product . '/main.php';
		require_once($pluginMain);
		$autoloader = $product . '_autoloader';

		$pluginInstance = $autoloader();
		if(is_callable($autoloader)) {
			$installedPlugins = @unserialize($modSettings['plugins']);
			$pluginInstance->removeHooks();
			unset($installedPlugins[$product]);
			updateSettings(array('plugins' => @serialize($installedPlugins)));
			return true;
		}
		else {
			// todo error handling
		}
	}

	public static function installProducts()
	{
		global $context;

		isAllowedTo('admin_forum');
		checkSession('get');

		$action = isset($_REQUEST['sa']) ? $_REQUEST['sa'] : '';
		$pluginlist = isset($_REQUEST['p']) ? explode(',', $_REQUEST['p']) : array();

		if(!empty($pluginlist) && ($action === 'install' || $action === 'uninstall')) {
			foreach($pluginlist as $plugin) {
				if($action === 'install')
					self::install($plugin);
				else
					self::uninstall($plugin);
			}
		}
		redirectexit('action=admin;area=plugins;' . $context['session_var'] . '=' . $context['session_id']);
	}

	/**
	 * @static
	 * implements main admin UI for plugins
	 */
	public static function main()
	{
		global $context, $txt, $modSettings;

		isAllowedTo('admin_forum');
		loadAdminTemplate('Plugins');
		loadLanguage('Plugins');

		/*
		if (isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]))
			$context['sub_action'] = $_REQUEST['sa'];
		else
			$context['sub_action'] = 'browse';
		*/
		if(isset($_REQUEST['sa']) && $_REQUEST['sa'] === 'hooks')
			return self::hooks();
		elseif(isset($_REQUEST['sa']) && ($_REQUEST['sa'] === 'install' || $_REQUEST['sa'] === 'uninstall'))
			self::installProducts();

		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $txt['plugin_manager'],
			// !!! 'help' => 'registrations',
			'description' => $txt['plugin_manager_desc'],
			'tabs' => array(
				'browse' => array(
				),
				'hooks' => array(
					'description' => $txt['browse_hooks_desc'],
				),
			),
		);
		$context['page_title'] = $txt['plugin_manager'];

		$userdata = array(
			'addonsdir' => HookAPI::getAddonsDir(),
			'installedPlugins' => @unserialize($modSettings['plugins'])
		);

		if(($_f = scandir($userdata['addonsdir'])) != false) {
			array_walk($_f, function(&$file, $key, &$data) {
				global $context, $txt, $scripturl;

				if($file === '.' || $file === '..')
					return;
				$fullname = $data['addonsdir'] . $file;
				if(file_exists($fullname) && file_exists($fullname . '/main.php')) {
					$context['plugins'][$file]['found'] = true;
					@require_once($fullname . '/main.php');
					$autoloader = $file . '_autoloader';
					if(is_callable($autoloader)) {
						$pluginInstance = $autoloader();
						$context['plugins'][$file]['name'] = $pluginInstance->Name;
						$context['plugins'][$file]['version'] = sprintf($txt['plugin_version'], $pluginInstance->Version);
						$context['plugins'][$file]['desc'] = $pluginInstance->Description;
						$context['plugins'][$file]['is_installed'] = isset($data['installedPlugins'][$file]) ? true : false;
						$context['plugins'][$file]['install_link'] = isset($data['installedPlugins'][$file]) ? ('<a href="' . $scripturl . '?action=admin;area=plugins;sa=uninstall;p=' . $file . ';' . $context['session_var'] . '=' . $context['session_id'] . '">' . $txt['uninstall_plugin'] . '</a>') : ('<a href="' . $scripturl . '?action=admin;area=plugins;sa=install;p=' . $file . ';'. $context['session_var'] . '=' .$context['session_id'] . '">' . $txt['install_plugin'] . '</a>');
					}
				}
			}, $userdata);
		}
	}

	/**
	 * @static
	 * implements hooks UI in admin panel
	 */
	public static function hooks()
	{
		$the_hooks = &HookAPI::getHooks();
	}
}
function PluginsMain()
{
	EoS_Plugin_Loader::main();
}
?>