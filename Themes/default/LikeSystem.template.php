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
function template_getlikes_xml()
{
	global $context, $txt;
	header('Content-Type: text/xml; charset=UTF-8');
	echo '<', '?xml version="1.0" encoding="UTF-8" ?', '>';
	echo '
<document>
 <response open="default" width="400px">
  <content>
  <![CDATA[
	<div class="title_bar">
		<h1>',$txt['members_who_liked'],'</h1>
	</div>
	<div class="mediummargin content">
	<ol class="commonlist">';

	foreach($context['likes'] as $like) {
		echo '
		<li>
		<div class="floatright smalltext">', $like['dateline'], '</div>';
		template_userbit_compact($like['member']);
		echo '
		<div class="clear"></div>
		</li>';
	}
	echo '
	</ol>
	</div>
  ]]>
  </content>
 </response>
</document>';
}
function template_getlikes()
{
	global $context, $txt;
	echo '
	<div class="title_bar">
		<h1>',$txt['members_who_liked'],'</h1>
	</div>
	<div class="mediummargin content">
	<ol class="commonlist">';

	foreach($context['likes'] as $like) {
		echo '
		<li>
		<div class="floatright smalltext">', $like['dateline'], '</div>';
		template_userbit_compact($like['member']);
		echo '
		<div class="clear"></div>
		</li>';
	}
	echo '
	</ol>
	</div>';
}
?>
