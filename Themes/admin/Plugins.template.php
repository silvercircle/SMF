<?php
function template_main()
{
	global $context;

	if(isset($context['plugins'])) {
		echo '
		<ol class="commonlist pluginlist">';
		foreach($context['plugins'] as &$plugin) {
			echo '
			<li class="',($plugin['is_installed'] ? 'installed' : ''),'">
				<h3>',$plugin['name'],'</h3><span class="lowcontrast smalltext">',$plugin['desc'],'</span>
				<span class="floatright tinytext lowcontrast">',$plugin['version'],'</span><br>
				', $plugin['install_link'],'
			</li>';
		}
		echo '
		</ol>';
	}
}
?>