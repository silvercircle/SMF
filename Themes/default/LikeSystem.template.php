<?php
function template_getlikes()
{
	global $context, $txt;

	echo '
	<div class="title_bar">
		<h1>',$txt['members_who_liked'],'</h1>
	</div>
	<div class="mediummargin">
	<ol class="category">';

	foreach($context['likes'] as $like) {
		echo '
		<li>
		<div class="floatright smalltext">', $like['dateline'], '</div>';
		template_userbit_compact($like['member']);
		echo '
		</li>';
	}
	echo '
	</ol>
	</div>';
}
?>
