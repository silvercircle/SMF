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
 * integrate the legacy bbc tags that were removed from parse_bbc() as an
 * optional add-on.
 *
 * This is a core addon and there is no need to install it. It can be enabled/disabled
 * on the BBCode configuration page.
 */

function LegacyBBC_autoloader()
{
	$myplugin_instance = new LegacyBBC();
	return($myplugin_instance);
}

class LegacyBBC extends EoS_Plugin
{
	protected $productShortName = 'myplugin';
	protected $installableHooks = array(
		'parse_bbc' => array('file' => 'main.php', 'callable' => 'LegacyBBC::addtags')
	);

	protected $_product = array(
		'Version' => '0.1',
		'Name' => 'LegacyBBC',
		'Description' => 'Re-adds BBCode tags that were removed from EoS Alpha to allow maximum BBCode compatibility with SMF 2.x'
	);

	public function __construct()
	{
		parent::__construct();
	}

	public static function addtags(&$codes)
	{
		global $context;

		$codes = array_merge($codes, array(
			array(
				'tag' => 'black',
				'before' => '<span style="color: black;" class="bbc_color">',
				'after' => '</span>',
			),
			array(
				'tag' => 'blue',
				'before' => '<span style="color: blue;" class="bbc_color">',
				'after' => '</span>',
			),
			array(
				'tag' => 'ftp',
				'type' => 'unparsed_content',
				'content' => '<a href="$1" class="bbc_ftp new_win" target="_blank">$1</a>',
				'validate' => create_function('&$tag, &$data, $disabled', '
					$data = strtr($data, array(\'<br />\' => \'\'));
					if (strpos($data, \'ftp://\') !== 0 && strpos($data, \'ftps://\') !== 0)
						$data = \'ftp://\' . $data;
				'),
			),
			array(
				'tag' => 'ftp',
				'type' => 'unparsed_equals',
				'before' => '<a href="$1" class="bbc_ftp new_win" target="_blank">',
				'after' => '</a>',
				'validate' => create_function('&$tag, &$data, $disabled', '
					if (strpos($data, \'ftp://\') !== 0 && strpos($data, \'ftps://\') !== 0)
						$data = \'ftp://\' . $data;
				'),
				'disallow_children' => array('email', 'ftp', 'url', 'iurl'),
				'disabled_after' => ' ($1)',
			),
			array(
				'tag' => 'glow',
				'type' => 'unparsed_commas',
				'test' => '[#0-9a-zA-Z\-]{3,12},([012]\d{1,2}|\d{1,2})(,[^]]+)?\]',
				'before' => $context['browser']['is_ie'] ? '<table border="0" cellpadding="0" cellspacing="0" style="display: inline; vertical-align: middle; font: inherit;"><tr><td style="filter: Glow(color=$1, strength=$2); font: inherit;">' : '<span style="text-shadow: $1 1px 1px 1px">',
				'after' => $context['browser']['is_ie'] ? '</td></tr></table> ' : '</span>',
			),
			array(
				'tag' => 'green',
				'before' => '<span style="color: green;" class="bbc_color">',
				'after' => '</span>',
			),
			array(
				'tag' => 'move',
				'before' => '<marquee>',
				'after' => '</marquee>',
				'block_level' => true,
				'disallow_children' => array('move'),
			),
			array(
				'tag' => 'justify',
				'before' => '<div style="text-align:justify;">',
				'after' => '</div>',
				'block_level' => true,
			),
			array(
				'tag' => 'center',
				'before' => '<div style="text-align:center;">',
				'after' => '</div>',
				'block_level' => true,
			),
			array(
				'tag' => 'left',
				'before' => '<div style="text-align: left;">',
				'after' => '</div>',
				'block_level' => true,
			),
			array(
				'tag' => 'right',
				'before' => '<div style="text-align: right;">',
				'after' => '</div>',
				'block_level' => true,
			),
			array(
				'tag' => 'red',
				'before' => '<span style="color: red;" class="bbc_color">',
				'after' => '</span>',
			),
			array(
				'tag' => 'white',
				'before' => '<span style="color: white;" class="bbc_color">',
				'after' => '</span>',
			),
			array(
				'tag' => 'iurl',
				'type' => 'unparsed_content',
				'content' => '<a href="$1" class="bbc_link">$1</a>',
				'validate' => create_function('&$tag, &$data, $disabled', '
						$data = strtr($data, array(\'<br />\' => \'\'));
						if (strpos($data, \'http://\') !== 0 && strpos($data, \'https://\') !== 0)
							$data = \'http://\' . $data;
					'),
			),
			array(
				'tag' => 'iurl',
				'type' => 'unparsed_equals',
				'before' => '<a href="$1" class="bbc_link">',
				'after' => '</a>',
				'validate' => create_function('&$tag, &$data, $disabled', '
						if (substr($data, 0, 1) == \'#\')
							$data = \'#post_\' . substr($data, 1);
						elseif (strpos($data, \'http://\') !== 0 && strpos($data, \'https://\') !== 0)
							$data = \'http://\' . $data;
					'),
				'disallow_children' => array('email', 'ftp', 'url', 'iurl'),
				'disabled_after' => ' ($1)',
			),
		));
	}
}
?>