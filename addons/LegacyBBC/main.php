<?php
/**
 * %%@productname@%%
 * @copyright 2011 Alex Vie silvercircle(AT)gmail(DOT)com
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright:	2011 Simple Machines (http://www.simplemachines.org)
 * license:  	BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version %%@productversion@%%
 *
 * integrate the legacy bbc tags that were removed from parse_bbc()
 */
function legacybbc_addtags(&$codes)
{
	global $context;

	$codes += array(
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
			'tag' => 'red',
			'before' => '<span style="color: red;" class="bbc_color">',
			'after' => '</span>',
		)
	);
}
?>