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
	<div id="sidebar" style="width:300px;padding-top:20px;">';
	
	// Display each of the column headers of the table.
	$sortlist = '';
	foreach ($context['columns'] as $column)
	{
		// We're not able (through the template) to sort the search results right now...
		//if (isset($context['old_search']))
		//	echo $column['label'];
		// This is a selected column, so underline it or some such.
		if(isset($column['selected']) || isset($column['link'])) {
			if ($column['selected'])
			 	$sortlist .= '<li><span class="button" style="width:80%;"><a href="' . $column['href'] . '" rel="nofollow">' . $column['label'] . ' <img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" /></a></span><br><br></li>';
		// This is just some column... show the link and be done with it.
			else
			 	$sortlist .= '<li><span class="button" style="width:80%;">'.$column['link'].'</span><br><br></li>';
		}
	}
	if(strlen($sortlist)) {
		echo '
	 	<div class="cat_bar"><h3>Sort</h3></div>
	 	<div class="blue_container">
		<ol class="centertext" style="list-style:none;">',
		$sortlist,
		'</ol>
		</div>
		<br>';
	}
	echo '
		<form action="', $scripturl, '?action=mlist;sa=search" method="post" accept-charset="', $context['character_set'], '">
		<div id="memberlist">
			<div class="cat_bar">
			<h3>
					', $txt['mlist_search'], '
			</h3>
		</div>';
	echo '
		<div id="memberlist_search" class="clear">
			<div class="blue_container mediumpadding">
				<div id="mlist_search" class="flow_hidden">
					<div id="search_term_input">
						<input type="text" name="search" value="', $context['old_search'], '" size="35" class="input_text" /> <input type="submit" name="submit" value="' . $txt['search'] . '" class="button_submit" />
					</div>
				<span class="floatleft">';

	$count = 0;
	if(isset($context['search_fields'])) {
	foreach ($context['search_fields'] as $id => $title) 
		echo '
			<label for="fields-', $id, '"><input type="checkbox" name="fields[]" id="fields-', $id, '" value="', $id, '" ', in_array($id, $context['search_defaults']) ? 'checked="checked"' : '', ' class="input_check" />', $title, '</label><br />';
	}
		echo '
						</span>
					</div>
				</div><br /><br />
			</div>
		</div>
	</form>
	
	</div>
	<div class="main_section" id="memberlist" style="margin-right:310px;">
		<div class="bigheader">
			<h4>
				<span class="floatleft">', $txt['members_list'], '</span>';
		if (!isset($context['old_search']))
				echo '
				<span class="floatright">', $context['letter_links'], '</span>';
		echo '
			</h4>
		<div style="clear:left;"></div>
		</div>';
		if(isset($context['page_index']))
			echo '
			<div class="pagesection">
			 <div class="pagelinks floatleft">', $txt['pages'], ': ', $context['page_index'], '</div>
			</div>';

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
		<div class="pagesection">';
			if(isset($context['page_index']))
				echo '<div class="pagelinks floatleft">', $txt['pages'], ': ', $context['page_index'], '</div>';

	// If it is displaying the result of a search show a "search again" link to edit their criteria.
	if (isset($context['old_search']) && isset($context['old_search_value']))
		echo '
			<div class="floatright">
				<a href="', $scripturl, '?action=mlist;sa=search;search=', $context['old_search_value'], '">', $txt['mlist_search_again'], '</a>
			</div>';
	echo '
		</div>
	<div class="clear"></div></div>';

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
					<img src="' . $settings['images_url'] . '/buttons/search.gif" alt="" class="icon" />', $txt['mlist_search'], '
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