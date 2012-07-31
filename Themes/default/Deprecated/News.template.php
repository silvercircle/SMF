<?php
function template_news_listitems()
{
	echo <<<EOT

	<script>
	// <![CDATA[
	function dismissNews(id) {
		sendRequest('action=dismissnews;id=' + id, null);
	}
	// ]]>
	</script>
EOT;

	echo '
	<div class="blue_container gradient_darken_down" id="newsitem_container">
		<div class="content smallpadding inset_shadow">
			<ol class="commonlist noshadow news" id="newsitem_list">';
	template_news_listitem();
	echo '
			</ol>
		</div>
	</div>
	<div class="cContainer_end"></div>';
}
function template_news_listitem()
{
	global $context, $scripturl;

	foreach($context['news_items'] as &$item) {
		echo '
		<li class="visible" id="newsitem_',$item['id'],'">';
		if($item['can_dismiss'] && $context['can_dismiss_news'])
			echo '
		<div class="floatright">
		<a onclick="dismissNews(',$item['id'],');return(false);" href="',$scripturl, '?action=dismissnews;id=' . $item['id'], '">X</a>
		</div>';
		echo
		 $item['body'],'
		</li>';
	}
}
?>