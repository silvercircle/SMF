<?php
function template_news_listitems()
{
	global $context;

	foreach($context['news_items'] as $item) {
		echo '
		<li>'
		,$item['body'],'
		</li>';
	}
}
?>
