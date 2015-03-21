<?php
/**
 * @name      EosAlpha BBS
 * @copyright 2015 Alex Vie silvercircle(AT)gmail(DOT)com
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright:	2011 Simple Machines (http://www.simplemachines.org)
 * license:  	BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version 1.0pre
 */
if (!defined('SMF'))
	die('Hacking attempt...');

/*
 * Karma is basically dead in EoS Alpha. It has been replaced by the post rating system
 * This file is here for historical reasons only, see below why.
 */

/**
 * This is here for historical reasons and won't go away :)
 */
function BookOfUnknown()
{
	global $context;

	if (strpos($_GET['action'], 'mozilla') !== false && !$context['browser']['is_gecko'])
		redirectexit('http://www.getfirefox.com/');
	elseif (strpos($_GET['action'], 'mozilla') !== false)
		redirectexit('about:mozilla');

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '>
	<head>
		<title>The Book of Unknown, ', @$_GET['verse'] == '2:18' ? '2:18' : '4:16', '</title>
		<style type="text/css">
			em
			{
				font-size: 1.3em;
				line-height: 0;
			}
		</style>
	</head>
	<body style="background-color: #444455; color: white; font-style: italic; font-family: serif;">
		<div style="margin-top: 12%; font-size: 1.1em; line-height: 1.4; text-align: center;">';
	if (@$_GET['verse'] == '2:18')
		echo '
			Woe, it was that his name wasn\'t <em>known</em>, that he came in mystery, and was recognized by none.&nbsp;And it became to be in those days <em>something</em>.&nbsp; Something not yet <em id="unknown" name="[Unknown]">unknown</em> to mankind.&nbsp; And thus what was to be known the <em>secret project</em> began into its existence.&nbsp; Henceforth the opposition was only <em>weary</em> and <em>fearful</em>, for now their match was at arms against them.';
	else
		echo '
			And it came to pass that the <em>unbelievers</em> dwindled in number and saw rise of many <em>proselytizers</em>, and the opposition found fear in the face of the <em>x</em> and the <em>j</em> while those who stood with the <em>something</em> grew stronger and came together.&nbsp; Still, this was only the <em>beginning</em>, and what lay in the future was <em id="unknown" name="[Unknown]">unknown</em> to all, even those on the right side.';
	echo '
		</div>
		<div style="margin-top: 2ex; font-size: 2em; text-align: right;">';
	if (@$_GET['verse'] == '2:18')
		echo '
			from <span style="font-family: Georgia, serif;"><strong><a href="http://www.unknownbrackets.com/about:unknown" style="color: white; text-decoration: none; cursor: text;">The Book of Unknown</a></strong>, 2:18</span>';
	else
		echo '
			from <span style="font-family: Georgia, serif;"><strong><a href="http://www.unknownbrackets.com/about:unknown" style="color: white; text-decoration: none; cursor: text;">The Book of Unknown</a></strong>, 4:16</span>';
	echo '
		</div>
	</body>
</html>';

	obExit(false);
}
