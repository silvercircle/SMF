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
function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	echo '
	<form action="', $scripturl, '?action=search2" method="post" accept-charset="UTF-8" name="searchform" id="searchform">
		<div class="bigheader">';
			if(isset($modSettings['search_index']) && $modSettings['search_index'] == 'sphinx')
				echo '<div class="floatright">
					Powered by: <a href="http://sphinxsearch.com"><img src="',$settings['images_url'],'/theme/sphinx.jpg" alt="sphinxlogo" style="vertical-align:middle;" /></a>
					</div>';

			echo '<span class="ie6_header floatleft">', $txt['set_parameters'], '</span>
			<div class="clear"></div>
		</div>
		<div class="blue_container">';

		
	if (!empty($context['search_errors']))
		echo '
		<p id="search_error" class="error">', implode('<br />', $context['search_errors']['messages']), '</p>';

	// Simple Search?
	if ($context['simple_search'])
	{
		echo '
		<fieldset id="simple_search">
			<div>
				<div id="search_term_input">
					<strong>', $txt['search_for'], ':</strong>
					<input type="text" name="search"', !empty($context['search_params']['search']) ? ' value="' . $context['search_params']['search'] . '"' : '', ' maxlength="', $context['search_string_limit'], '" size="40" class="input_text" />
					', $context['require_verification'] ? '' : '&nbsp;<input type="submit" name="submit" value="' . $txt['search'] . '" class="button_submit" />
				</div>';

		if (empty($modSettings['search_simple_fulltext']))
			echo '
				<p class="smalltext">', $txt['search_example'], '</p>';

		if ($context['require_verification'])
			echo '
				<div class="verification>
					<strong>', $txt['search_visual_verification_label'], ':</strong>
					<br />', template_control_verification($context['visual_verification_id'], 'all'), '<br />
					<input id="submit" type="submit" name="submit" value="' . $txt['search'] . '" class="button_submit" />
				</div>';

		echo '
				<a href="', $scripturl, '?action=search;advanced" onclick="this.href += \';search=\' + escape(document.forms.searchform.search.value);">', $txt['search_advanced'], '</a>
				<input type="hidden" name="advanced" value="0" />
			</div>
			<span class="lowerframe"><span></span></span>
		</fieldset>';
	}

	// Advanced search!
	else
	{
		echo '
		<fieldset id="advanced_search">
			<div>
				<input type="hidden" name="advanced" value="1" />
				<span class="enhanced">
					<strong>', $txt['search_for'], ':</strong>
					<input type="text" name="search"', !empty($context['search_params']['search']) ? ' value="' . $context['search_params']['search'] . '"' : '', ' maxlength="', $context['search_string_limit'], '" size="40" class="input_text" />
					<script type="text/javascript"><!-- // --><![CDATA[
						function initSearch()
						{
							if (document.forms.searchform.search.value.indexOf("%u") != -1)
								document.forms.searchform.search.value = unescape(document.forms.searchform.search.value);
						}
						createEventListener(window);
						window.addEventListener("load", initSearch, false);
					// ]]></script>
					<select name="searchtype">
						<option value="1"', empty($context['search_params']['searchtype']) ? ' selected="selected"' : '', '>', $txt['all_words'], '</option>
						<option value="2"', !empty($context['search_params']['searchtype']) ? ' selected="selected"' : '', '>', $txt['any_words'], '</option>
					</select>
				</span>';

		if (empty($modSettings['search_simple_fulltext']))
			echo '
				<em class="smalltext">', $txt['search_example'], '</em>';

		echo '
				<dl id="search_options">
					<dt>', $txt['by_user'], ':</dt>
					<dd><input id="userspec" type="text" name="userspec" value="', empty($context['search_params']['userspec']) ? '*' : $context['search_params']['userspec'], '" size="40" class="input_text" /></dd>
					<dt>', $txt['search_order'], ':</dt>
					<dd>
						<select id="sort" name="sort">
							<option value="relevance|desc">', $txt['search_orderby_relevant_first'], '</option>
							<option value="num_replies|desc">', $txt['search_orderby_large_first'], '</option>
							<option value="num_replies|asc">', $txt['search_orderby_small_first'], '</option>
							<option value="id_msg|desc">', $txt['search_orderby_recent_first'], '</option>
							<option value="id_msg|asc">', $txt['search_orderby_old_first'], '</option>
						</select>
					</dd>
					<dt class="options">', $txt['search_options'], ':</dt>
					<dd class="options">
						<label for="show_complete"><input type="checkbox" name="show_complete" id="show_complete" value="1"', !empty($context['search_params']['show_complete']) ? ' checked="checked"' : '', ' class="input_check" /> ', $txt['search_show_complete_messages'], '</label><br />
						<label for="subject_only"><input type="checkbox" name="subject_only" id="subject_only" value="1"', !empty($context['search_params']['subject_only']) ? ' checked="checked"' : '', ' class="input_check" /> ', $txt['search_subject_only'], '</label>
					</dd>
					<dt class="between">', $txt['search_post_age'], ': </dt>
					<dd>', $txt['search_between'], ' <input type="text" name="minage" value="', empty($context['search_params']['minage']) ? '0' : $context['search_params']['minage'], '" size="5" maxlength="4" class="input_text" />&nbsp;', $txt['search_and'], '&nbsp;<input type="text" name="maxage" value="', empty($context['search_params']['maxage']) ? '9999' : $context['search_params']['maxage'], '" size="5" maxlength="4" class="input_text" /> ', $txt['days_word'], '</dd>
				</dl>';

		// Require an image to be typed to save spamming?
		if ($context['require_verification'])
		{
			echo '
				<p>
					<strong>', $txt['verification'], ':</strong>
					', template_control_verification($context['visual_verification_id'], 'all'), '
				</p>';
		}

		// If $context['search_params']['topic'] is set, that means we're searching just one topic.
		if (!empty($context['search_params']['topic']))
			echo '
				<p>', $txt['search_specific_topic'], ' &quot;', $context['search_topic']['link'], '&quot;.</p>
				<input type="hidden" name="topic" value="', $context['search_topic']['id'], '" />';

		echo '
			</div>
		</fieldset>';

		if (empty($context['search_params']['topic']))
		{
			$collapser = array('id' => 'search_boards', 'title' => $txt['choose_board'], 'bodyclass' => 'flat_container');
			template_create_collapsible_container($collapser);
			echo '
		<fieldset class="flow_hidden">
			<div>
				<div class="flow_auto" id="searchBoardsExpand">
					<ul class="ignoreboards floatleft">';

	$i = 0;
	$limit = ceil($context['num_boards'] / 2);
	foreach ($context['categories'] as $category)
	{
		echo '
			<li class="category">
			<a href="javascript:void(0);" onclick="selectBoards([', implode(', ', $category['child_ids']), ']); return false;">', $category['name'], '</a>
			<ul>';

		foreach ($category['boards'] as $board)
		{
			if ($i == $limit)
				echo '
				</ul>
			 	 </li>
				</ul>
				<ul class="ignoreboards floatright">
				 <li class="category">
				<ul>';
			echo '
				 <li class="board" style="margin-', $context['right_to_left'] ? 'right' : 'left', ': ', $board['child_level'], 'em;">
				  <label for="brd', $board['id'], '"><input type="checkbox" id="brd', $board['id'], '" name="brd[', $board['id'], ']" value="', $board['id'], '"', $board['selected'] ? ' checked="checked"' : '', ' class="input_check" /> ', $board['name'], '</label>
				 </li>';
			$i ++;
		}
		echo '
				</ul>
				</li>';
	}
	echo '
		</ul>
		</div>
		<br class="clear" />';

	echo '
		<div class="padding">
			<input type="checkbox" name="all" id="check_all" value=""', $context['boards_check_all'] ? ' checked="checked"' : '', ' onclick="invertAll(this, this.form, \'brd\');" class="input_check floatleft" />
			<label for="check_all" class="floatleft">', $txt['check_all'], '</label>
		</div>
		<br class="clear" />
		</div>
		</fieldset>';
	echo '
		</div>';
		}
	}

	echo '
	<input style="margin-top:5px;" type="submit" name="submit" value="', $txt['search'], '" class="button_submit floatright" />
	<div class="clear"></div>
	</div>
	</form>

	<script type="text/javascript"><!-- // --><![CDATA[
		function selectBoards(ids)
		{
			var toggle = true;

			for (i = 0; i < ids.length; i++)
				toggle = toggle & document.forms.searchform["brd" + ids[i]].checked;

			for (i = 0; i < ids.length; i++)
				document.forms.searchform["brd" + ids[i]].checked = !toggle;
		}

		function expandCollapseBoards()
		{
			$("#searchBoardsExpand").toggle();
			$("#expandBoardsIcon").attr("src", smf_images_url + ($("#searchBoardsExpand").is(":visible") ? "/collapse.gif" : "/expand.gif"));
		}';

	echo '
	// ]]></script>';
}

function template_results()
{
	global $context, $settings, $options, $txt, $scripturl, $message;

	if (isset($context['did_you_mean']) || empty($context['topics']))
	{
		echo '
	<div id="search_results">
		<div class="cat_bar">
			<h3>
				', $txt['search_adjust_query'], '
			</h3>
		</div>
		<div class="blue_container">';

		// Did they make any typos or mistakes, perhaps?
		if (isset($context['did_you_mean']))
			echo '
			<p>', $txt['search_did_you_mean'], ' <a href="', $scripturl, '?action=search2;params=', $context['did_you_mean_params'], '">', $context['did_you_mean'], '</a>.</p>';

		echo '
			<form action="', $scripturl, '?action=search2" method="post" accept-charset="UTF-8">
				<strong>', $txt['search_for'], ':</strong>
				<input type="text" name="search"', !empty($context['search_params']['search']) ? ' value="' . $context['search_params']['search'] . '"' : '', ' maxlength="', $context['search_string_limit'], '" size="40" class="input_text" />
				<input type="submit" name="submit" value="', $txt['search_adjust_submit'], '" class="button_submit" />
				<input type="hidden" name="searchtype" value="', !empty($context['search_params']['searchtype']) ? $context['search_params']['searchtype'] : 0, '" />
				<input type="hidden" name="userspec" value="', !empty($context['search_params']['userspec']) ? $context['search_params']['userspec'] : '', '" />
				<input type="hidden" name="show_complete" value="', !empty($context['search_params']['show_complete']) ? 1 : 0, '" />
				<input type="hidden" name="subject_only" value="', !empty($context['search_params']['subject_only']) ? 1 : 0, '" />
				<input type="hidden" name="minage" value="', !empty($context['search_params']['minage']) ? $context['search_params']['minage'] : '0', '" />
				<input type="hidden" name="maxage" value="', !empty($context['search_params']['maxage']) ? $context['search_params']['maxage'] : '9999', '" />
				<input type="hidden" name="sort" value="', !empty($context['search_params']['sort']) ? $context['search_params']['sort'] : 'relevance', '" />';

		if (!empty($context['search_params']['brd']))
			foreach ($context['search_params']['brd'] as $board_id)
				echo '
				<input type="hidden" name="brd[', $board_id, ']" value="', $board_id, '" />';

		echo '
			</form>
		</div>
	</div><br />';
	}

	if ($context['compact'])
	{
		// Quick moderation set to checkboxes? Oh, how fun :/.
		if (!empty($options['display_quick_mod']))
			echo '
	<form action="', $scripturl, '?action=quickmod" method="post" accept-charset="UTF-8" name="topicForm">';

	echo '
		<h1 class="bigheader">
			<span class="floatright">';
				if (!empty($options['display_quick_mod']))
				echo '
						<input type="checkbox" onclick="invertAll(this, this.form, \'topics[]\');" class="input_check" />';
			echo '
			</span>
			', $txt['mlist_search_results'],':&nbsp;',$context['search_params']['search'],'
		</h1>
		<div class="pagesection">
			<span>', $txt['pages'], ': ', $context['page_index'], '</span>
		</div>';

		while ($topic = $context['get_topics']())
		{
			$color_class = '';
			if ($topic['is_sticky'])
				$color_class = 'stickybg';
			if ($topic['is_locked'])
				$color_class .= 'lockedbg';

			echo '
			<div class="blue_container smallpadding" style="margin-bottom:10px;">
			<div class="core_posts">
				<div class="flow_auto">';

			foreach ($topic['matches'] as $message)
			{
				if(!empty($message['member']['avatar']['image']))
					echo '
					<div class="user floatleft"><div class="avatar" style="margin-right:10px;">',$message['member']['avatar']['image'],'</div></div>';
				else
					echo '
					<div class="user floatleft"><div class="avatar" style="margin:0 10px 0 0;"><a href="', $scripturl, '?action=profile;u=', $message['member']['id'], '">
					<img src="',$settings['images_url'],'/unknown.png" alt="avatar" /></a></div></div>';

				echo '<div style="margin-left:90px;">';
				if (!empty($options['display_quick_mod']))
				{
					echo '
						<div class="floatright">
						<input type="checkbox" name="topics[]" value="', $topic['id'], '" class="input_check" />
						</div>';
				}
				echo '<strong>',$message['counter'], '. ','<a href="', $scripturl, '?topic=', $topic['id'], '.msg', $message['id'], '#msg', $message['id'], '">', $message['subject_highlighted'], '</a> ',$txt['in'], ': ',$topic['board']['link'],'</strong><br />
				<div class="smalltext">',$txt['topic'], ' ',$txt['by'],'&nbsp;<strong>', $message['member']['link'], '</strong>,&nbsp;<em>', $message['time'], '</em>&nbsp;</div>';

				if ($message['body_highlighted'] != '')
					echo '<hr style="margin:2px 0;">
					<div class="smalltext">', $message['body_highlighted'], '</div>';
			}

			echo '</div>
				</div>
			</div>
		</div>';

		}
		if (!empty($context['topics']))
		echo '
		<div class="pagesection">
			<span>', $txt['pages'], ': ', $context['page_index'], '</span>
		</div>';

		if (!empty($options['display_quick_mod']) && !empty($context['topics']))
		{
			echo '
			<div class="smalltext blue_container" style="padding: 4px;">
				<div class="floatright">
					<select name="qaction"', $context['can_move'] ? ' onchange="this.form.moveItTo.disabled = (this.options[this.selectedIndex].value != \'move\');"' : '', '>
						<option value="">--------</option>', $context['can_remove'] ? '
						<option value="remove">' . $txt['quick_mod_remove'] . '</option>' : '', $context['can_lock'] ? '
						<option value="lock">' . $txt['quick_mod_lock'] . '</option>' : '', $context['can_sticky'] ? '
						<option value="sticky">' . $txt['quick_mod_sticky'] . '</option>' : '',	$context['can_move'] ? '
						<option value="move">' . $txt['quick_mod_move'] . ': </option>' : '', $context['can_merge'] ? '
						<option value="merge">' . $txt['quick_mod_merge'] . '</option>' : '', '
						<option value="markread">', $txt['quick_mod_markread'], '</option>
					</select>';

			if ($context['can_move'])
			{
					echo '
					<select id="moveItTo" name="move_to" disabled="disabled">';

					foreach ($context['move_to_boards'] as $category)
					{
						echo '
						<optgroup label="', $category['name'], '">';
						foreach ($category['boards'] as $board)
								echo '
						<option value="', $board['id'], '"', $board['selected'] ? ' selected="selected"' : '', '>', $board['child_level'] > 0 ? str_repeat('==', $board['child_level'] - 1) . '=&gt;' : '', ' ', $board['name'], '</option>';
						echo '
						</optgroup>';
					}
					echo '
					</select>';
			}

			echo '
					<input type="hidden" name="redirect_url" value="', $scripturl . '?action=search2;params=' . $context['params'], '" />
					<input type="submit" style="font-size: 0.8em;" value="', $txt['quick_mod_go'], '" onclick="return this.form.qaction.value != \'\' &amp;&amp; confirm(\'', $txt['quickmod_confirm'], '\');" class="button_submit" />
				</div>
				<br class="clear" />
			</div>';
		}


		if (!empty($options['display_quick_mod']) && !empty($context['topics']))
			echo '
			<input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '" />
		</form>';

	}
	else
	{
		echo '
		<div class="cat_bar">
			<h3>
				', $txt['mlist_search_results'],':&nbsp;',$context['search_params']['search'],'
			</h3>
		</div>
		<div class="pagesection">
			<span>', $txt['pages'], ': ', $context['page_index'], '</span>
		</div>';

		if (empty($context['topics']))
			echo '
		<div class="blue_container">(', $txt['search_no_results'], ')</div>';

		while ($topic = $context['get_topics']()) {
			foreach ($topic['matches'] as $message)
				template_postbit_compact($message, 0);
		}

		echo '
		<div class="pagesection">
			<span>', $txt['pages'], ': ', $context['page_index'], '</span>
		</div>';
	}

	// Show a jump to box for easy navigation.
	echo '
		<br class="clear" />
		<div class="smalltext righttext" id="search_jump_to">&nbsp;</div>
		<script type="text/javascript"><!-- // --><![CDATA[
			if (typeof(window.XMLHttpRequest) != "undefined")
				aJumpTo[aJumpTo.length] = new JumpTo({
					sContainerId: "search_jump_to",
					sJumpToTemplate: "<label class=\"smalltext\" for=\"%select_id%\">', $context['jump_to']['label'], ':<" + "/label> %dropdown_list%",
					iCurBoardId: 0,
					iCurBoardChildLevel: 0,
					sCurBoardName: "', $context['jump_to']['board_name'], '",
					sBoardChildLevelIndicator: "==",
					sBoardPrefix: "=> ",
					sCatSeparator: "-----------------------------",
					sCatPrefix: "",
					sGoButtonLabel: "', $txt['quick_mod_go'], '"
				});
		// ]]></script>';

}

?>
