<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines
 * @copyright 2011 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0
 */

// Displays a sortable listing of all members registered on the forum.
function template_main()
{
	global $context, $settings, $options, $scripturl, $txt;

	// Build the memberlist button array.
	$memberlist_buttons = array(
			'view_all_members' => array('text' => 'view_all_members', 'image' => 'mlist.gif', 'lang' => true, 'url' => $scripturl . '?action=mlist' . ';sa=all', 'active'=> true),
			'mlist_search' => array('text' => 'mlist_search', 'image' => 'mlist.gif', 'lang' => true, 'url' => $scripturl . '?action=mlist' . ';sa=search'),
		);

	echo '
	<div class="main_section" id="memberlist">
		<div class="bigheader">
			<h4>
				<span class="floatleft">', $txt['members_list'], '</span>';
		if (!isset($context['old_search']))
				echo '
				<span class="floatright">', $context['letter_links'], '</span>';
		echo '
			</h4>
		<div class="clear"></div>
		</div>
		<div class="pagesection">
			', template_button_strip($memberlist_buttons, 'right'), '
			<div class="pagelinks floatleft">', $txt['pages'], ': ', $context['page_index'], '</div>
		</div>';

	echo '<div class="orange_container">Sort by:  ';
	// Display each of the column headers of the table.
	foreach ($context['columns'] as $column)
	{
		// We're not able (through the template) to sort the search results right now...
		echo '<span style="margin-left:10px;"><strong>';
		if (isset($context['old_search']))
			echo $column['label'];
		// This is a selected column, so underline it or some such.
		elseif ($column['selected'])
			echo '
					<a href="' . $column['href'] . '" rel="nofollow">' . $column['label'] . ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" /></a>';
		// This is just some column... show the link and be done with it.
		else
			echo $column['link'];
		echo '</strong></span>';
	}
	echo '</div>';
	// Assuming there are members loop through each one displaying their data.
	if (!empty($context['members'])) {
		echo '<div><ol class="tiles" id="membertiles">';
		foreach ($context['members'] as $member) {
			$loc = array();
			echo '
				<li>
				<div class="blue_container" style="margin:2px;overflow:hidden;">
				<div style="width:67px;float:left;">';
				if(!empty($member['avatar']['image']))
					echo $member['avatar']['image'];
				else
					echo '<img class="avatar" src="',$settings['images_url'], '/unknown.png" alt="avatar" />';
				
				echo '
					</div>
					<span style="font-size:15px;"><strong>',$member['link'],'</strong></span><br>
					<span class="smalltext">';
				if(!empty($member['gender']['name']))
					$loc[0] = $member['gender']['image'].$member['gender']['name'];
			
				if(isset($member['birth_date']) && !empty($member['birth_date'])) {
					$l = idate('Y', time()) - intval($member['birth_date']);
					if($l < 100)
					$loc[1] = $l;
				}
			
				if(!empty($member['location']))
					$loc[2] = 'from '.$member['location'];
			
				if(!empty($loc)) 
					echo implode(', ', $loc), '<br />';
				
				echo '</span>
				</div></li>';
		}
		echo '</ol><br class="clear" /></div>
			<script>
				$(document).ready(function() {
					var _w = $("#membertiles").width();
					if(_w < 900)
						$("html > head").append("<style>ol.tiles li { width: 50%; } ol.tiles {padding-left: 0;}</style>");
				});
			</script>
			';
	}
	// No members?
	else
		echo '
			<div class="blue_container">', $txt['search_no_results'], '</div>';

	// Show the page numbers again. (makes 'em easier to find!)
	//echo '
		//</div>';

	echo '
		<div class="pagesection">
			<div class="pagelinks floatleft">', $txt['pages'], ': ', $context['page_index'], '</div>';

	// If it is displaying the result of a search show a "search again" link to edit their criteria.
	if (isset($context['old_search']))
		echo '
			<div class="floatright">
				<a href="', $scripturl, '?action=mlist;sa=search;search=', $context['old_search_value'], '">', $txt['mlist_search_again'], '</a>
			</div>';
	echo '
		</div>
	</div>';

}

// A page allowing people to search the member list.
function template_search()
{
	global $context, $settings, $options, $scripturl, $txt;

	// Build the memberlist button array.
	$memberlist_buttons = array(
			'view_all_members' => array('text' => 'view_all_members', 'image' => 'mlist.gif', 'lang' => true, 'url' => $scripturl . '?action=mlist' . ';sa=all'),
			'mlist_search' => array('text' => 'mlist_search', 'image' => 'mlist.gif', 'lang' => true, 'url' => $scripturl . '?action=mlist' . ';sa=search', 'active' => true),
		);

	// Start the submission form for the search!
	echo '
	<form action="', $scripturl, '?action=mlist;sa=search" method="post" accept-charset="', $context['character_set'], '">
		<div id="memberlist">
			<h1 class="bigheader">
					', !empty($settings['use_buttons']) ? '<img src="' . $settings['images_url'] . '/buttons/search.gif" alt="" class="icon" />' : '', $txt['mlist_search'], '
			</h3>
			<div class="pagesection">
				', template_button_strip($memberlist_buttons, 'right'), '
			</div>';
	// Display the input boxes for the form.
	echo '	<div id="memberlist_search" class="clear">
				<div class="blue_container">
					<div id="mlist_search" class="flow_hidden">
						<div id="search_term_input"><br />
							<strong>', $txt['search_for'], ':</strong>
							<input type="text" name="search" value="', $context['old_search'], '" size="35" class="input_text" /> <input type="submit" name="submit" value="' . $txt['search'] . '" class="button_submit" />
						</div>
						<span class="floatleft">';

	$count = 0;
	foreach ($context['search_fields'] as $id => $title)
	{
		echo '
							<label for="fields-', $id, '"><input type="checkbox" name="fields[]" id="fields-', $id, '" value="', $id, '" ', in_array($id, $context['search_defaults']) ? 'checked="checked"' : '', ' class="input_check" />', $title, '</label><br />';
	// Half way through?
		if (round(count($context['search_fields']) / 2) == ++$count)
			echo '
						</span>
						<span class="floatleft">';
	}
		echo '
						</span>
					</div>
				</div><br /><br />
			</div>
		</div>
	</form>';
}

?>