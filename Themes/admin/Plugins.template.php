<?php
function template_main()
{
	global $context, $txt;

	if(isset($context['plugins'])) {
		echo '
		<ol class="commonlist pluginlist">';
		foreach($context['plugins'] as &$plugin) {
			echo '
			<li class="',($plugin['is_installed'] ? 'installed' : ''),'">
				<h3>',$plugin['name'],'</h3><span class="lowcontrast smalltext">',$plugin['desc'],'</span>
				<span class="floatright tinytext lowcontrast">',$plugin['version'],'</span><br>';
			if($plugin['can_install'])
				echo $plugin['install_link'];
			else
				echo '<span class="alert">',$txt['plugins_cannot_install_reason'], ' <strong>',$plugin['install_error'], '</strong></span>';
			echo '
			</li>';
		}
		echo '
		</ol>';
	}
}
?>