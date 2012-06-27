<?php
$myplugin_instance;

function myplugin_autoloader()
{
	$myplugin_instance = new MyPlugin();
	return($myplugin_instance);
}

class MyPlugin extends EoS_Plugin
{
	protected $productShortName = 'myplugin';
	protected $installableHooks = array(
		'smarty_init' => array('file' => 'main.php', 'callable' => 'MyPlugin::registerTemplateHooks')
	);

	public function __construct()
	{
		parent::__construct();
	}

	/*
	 * hooks into smarty_init
	 */
	public static function registerTemplateHooks(&$smarty_instance, &$config_instance)
	{
		//$config_instance->registerHookTemplate('header_area', 'overrides/myplugin_header');
		//$config_instance->registerHookTemplate('postbit_below', 'overrides/myplugin_header');
	}
}

?>