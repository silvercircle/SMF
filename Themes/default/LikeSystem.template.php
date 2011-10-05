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
 */
function template_getlikes()
{
	global $context, $txt;

	echo '
	<div class="title_bar">
		<h1>',$txt['members_who_liked'],'</h1>
	</div>
	<div class="mediummargin">
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
