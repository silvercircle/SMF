<?php
function template_main()
{
	global $context, $settings, $user_info;
	
	echo '
	<div style="max-width:50%;width:300;height:300;position:absolute;display:none;" class="tcard" id="tpeekresult">
	<div onclick="$(\'#tpeekresult\').remove();return(false);" id="tcard_close">X</div>
	<div class="tcard_inner">';
	echo $context['preview']['first_body'];
	echo '</div></div>';
}
?>
