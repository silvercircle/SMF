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

$my_theme_context = array();

/*
 * this is called to initialize your object
 * it's therefore mandatory
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

function theme_support_autoload($smarty_instance)
{
	return new MyCustomTheme($smarty_instance);
}

/**
 * your class MUST inherit from _EoS_Smarty_Template_Support
 */
class MyCustomTheme extends EoS_Smarty_Template_Support
{
	public function __construct($smarty_instance) 
	{
		global $my_theme_context, $settings;

		parent::__construct($smarty_instance);		// this is a MUST

		/*
		 * add a custom theme variable and make it available in templates
		 * we always assign by reference!

		$smarty_instance->assignByRef('MYCONTEXT', $my_theme_context);
		$my_theme_context['testvalue'] = 'FOO';

		*/
	}

	/*
	 * demonstrate how to add own functions to your theme that can be accessed
	 * in smarty templates
	 *
	 * the function can then be used in any of your smarty templates as
	 * {$SUPPORT->testfunction('foo')}
	 */

	public function testfunction($param)
	{
		global $context, $txt;
		echo "We are in test function and this is our param: ", $param;

		/*
		 * display a custom theme template from overrides/
		 */
		$this->_smarty_instance->display('overrides/foo.tpl');
	}
}
?>