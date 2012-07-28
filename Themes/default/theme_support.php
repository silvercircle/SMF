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

$my_theme_context = array();

/*
 * this is called to initialize your object
 * it's therefore mandatory
 */

global $settings;

$settings['theme_variants'] = array('default', 'lightweight');
$settings['clip_image_src'] = array(
	'_default' => 'clipsrc.png',
    '_lightweight' => 'clipsrc_l.png',
	'_dark' => 'clipsrc_dark.png'
);
$settings['sprite_image_src'] = array(
	'_default' => 'theme/sprite.png',
	'_lightweight' => 'theme/sprite.png',
	'_dark' => 'theme/sprite.png'
);

function theme_support_autoload($smarty_instance)
{
	return new MyCustomTheme($smarty_instance);
}

/**
 * your class MUST inherit from _EoS_Smarty_Template_Support
 */
class MyCustomTheme extends EoS_Smarty_Template_Support
{
	public function __construct($smarty_instance) 
	{
		global $my_theme_context, $settings;

		parent::__construct($smarty_instance);		// this is a MUST

		/*
		 * add a custom theme variable and make it available in templates
		 * we always assign by reference!

		$smarty_instance->assignByRef('MYCONTEXT', $my_theme_context);
		$my_theme_context['testvalue'] = 'FOO';

		*/
	}

	/*
	 * demonstrate how to add own functions to your theme that can be accessed
	 * in smarty templates
	 *
	 * the function can then be used in any of your smarty templates as
	 * {$SUPPORT->testfunction('foo')}
	 */

	public function testfunction($param)
	{
		global $context, $txt;
		echo "We are in test function and this is our param: ", $param;

		/*
		 * display a custom theme template from overrides/
		 */
		$this->_smarty_instance->display('overrides/foo.tpl');
	}

	/**
	 * used in the modcente/watched_users template as a list callback
	 * function
	 */
	public function user_watch_post_callback($post)
	{
		global $scripturl, $context, $txt, $delete_button;

		// We'll have a delete please bob.
		if (empty($context['delete_button']))
			$context['delete_button'] = create_button('remove_message', 'remove');

		$output_html = '
						<div>
							<div class="floatleft">
								<strong><a href="' . $scripturl . '?topic=' . $post['id_topic'] . '.' . $post['id'] . '#msg' . $post['id'] . '">' . $post['subject'] . '</a></strong> ' . $txt['mc_reportedp_by'] . ' <strong>' . $post['author_link'] . '</strong>
							</div>
							<div class="floatright">';

		if ($post['can_delete'])
			$output_html .= '
								<a href="' . $scripturl . '?action=moderate;area=userwatch;sa=post;delete=' . $post['id'] . ';start=' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '" onclick="return confirm(\'' . $txt['mc_watched_users_delete_post'] . '\');">' . $delete_button . '</a>
								<input type="checkbox" name="delete[]" value="' . $post['id'] . '" class="input_check" />';

		$output_html .= '
							</div>
						</div><br />
						<div class="smalltext">
							&#171; ' . $txt['mc_watched_users_posted'] . ': ' . $post['poster_time'] . ' &#187;
						</div>
						<hr />
						' . $post['body'];

		return $output_html;
	}

	// What's this, verification?!
	public function template_control_verification($verify_id, $display_type = 'all', $reset = false)
	{
		global $context, $settings, $options, $txt, $modSettings;

		$verify_context = &$context['controls']['verification'][$verify_id];

		// Keep track of where we are.
		if (empty($verify_context['tracking']) || $reset)
			$verify_context['tracking'] = 0;

		// How many items are there to display in total.
		$total_items = count($verify_context['questions']) + ($verify_context['show_visual'] ? 1 : 0);

		// If we've gone too far, stop.
		if ($verify_context['tracking'] > $total_items)
			return false;

		// Loop through each item to show them.
		for ($i = 0; $i < $total_items; $i++)
		{
			// If we're after a single item only show it if we're in the right place.
			if ($display_type == 'single' && $verify_context['tracking'] != $i)
				continue;

			if ($display_type != 'single')
				echo '
				<div id="verification_control_', $i, '" class="verification_control">';

			// Do the actual stuff - image first?
			if ($i == 0 && $verify_context['show_visual'])
			{
				if ($context['use_graphic_library'])
					echo '
					<img src="', $verify_context['image_href'], '" alt="', $txt['visual_verification_description'], '" id="verification_image_', $verify_id, '" />';
				else
					echo '
					<img src="', $verify_context['image_href'], ';letter=1" alt="', $txt['visual_verification_description'], '" id="verification_image_', $verify_id, '_1" />
					<img src="', $verify_context['image_href'], ';letter=2" alt="', $txt['visual_verification_description'], '" id="verification_image_', $verify_id, '_2" />
					<img src="', $verify_context['image_href'], ';letter=3" alt="', $txt['visual_verification_description'], '" id="verification_image_', $verify_id, '_3" />
					<img src="', $verify_context['image_href'], ';letter=4" alt="', $txt['visual_verification_description'], '" id="verification_image_', $verify_id, '_4" />
					<img src="', $verify_context['image_href'], ';letter=5" alt="', $txt['visual_verification_description'], '" id="verification_image_', $verify_id, '_5" />
					<img src="', $verify_context['image_href'], ';letter=6" alt="', $txt['visual_verification_description'], '" id="verification_image_', $verify_id, '_6" />';

				echo '
					<div class="smalltext" style="margin: 4px 0 8px 0;">
						<a href="', $verify_context['image_href'], ';sound" id="visual_verification_', $verify_id, '_sound" rel="nofollow">', $txt['visual_verification_sound'], '</a> / <a href="#!" id="visual_verification_', $verify_id, '_refresh">', $txt['visual_verification_request_new'], '</a>', $display_type != 'quick_reply' ? '<br />' : '', '<br />
						', $txt['visual_verification_description'], ':', $display_type != 'quick_reply' ? '<br />' : '', '
						<input type="text" name="', $verify_id, '_vv[code]" value="', !empty($verify_context['text_value']) ? $verify_context['text_value'] : '', '" size="30" tabindex="', $context['tabindex']++, '" class="input_text" />
					</div>';
			}
			else
			{
				// Where in the question array is this question?
				$qIndex = $verify_context['show_visual'] ? $i - 1 : $i;

				echo '
					<div class="smalltext">
						', $verify_context['questions'][$qIndex]['q'], ':<br />
						<input type="text" name="', $verify_id, '_vv[q][', $verify_context['questions'][$qIndex]['id'], ']" size="30" value="', $verify_context['questions'][$qIndex]['a'], '" ', $verify_context['questions'][$qIndex]['is_error'] ? 'style="border: 1px red solid;"' : '', ' tabindex="', $context['tabindex']++, '" class="input_text" />
					</div>';
			}

			if ($display_type != 'single')
				echo '
				</div>';

			// If we were displaying just one and we did it, break.
			if ($display_type == 'single' && $verify_context['tracking'] == $i)
				break;
		}

		// Assume we found something, always,
		$verify_context['tracking']++;

		// Tell something displaying piecemeal to keep going.
		if ($display_type == 'single')
			return true;
	}
}
?>