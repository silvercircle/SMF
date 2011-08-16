<?php
/*
Tagging System
Version 2.0
by:vbgamer45
http://www.smfhacks.com
*/
function template_main()
{
	global $txt, $context, $scripturl;

	echo '
	<div class="orange_container mediumpadding" style="float:right;width:250px;">
		<div class="cat_bar">
			<h3>',$txt['smftags_popular'],'</h3>
		</div>';
  		if (isset($context['poptags']))
  			echo $context['poptags'];
	echo '
	</div>
	<div class="blue_container mediumpadding" style="margin-right:280px;">
  	 <table class="table_grid">
  	 <thead>
	  <tr>
		<th class="red_container">',$txt['smftags_subject'],'</th>
		<th class="red_container" style="width:11%;">',$txt['smftags_topictag'],'</th>
		<th class="red_container" style="width:11%;">',$txt['smftags_startedby'],'</th>
	  	<th class="red_container centertext" style="width:4%;">',$txt['smftags_replies'],'</th>
	  	<th class="red_container centertext" style="width:4%;">', $txt['smftags_views'], '</th>
	  </tr>';
	  foreach ($context['tags_topics'] as $i => $topic)	{
	  	echo '<tr>';
	  	 echo '<td class="windowbg2"><a href="' . $scripturl . '?topic=' . $topic['id_topic'] . '.0">' . $topic['subject'] . '</a></td>';
   		 echo '<td class="windowbg2"><a href="' . $scripturl . '?action=tags;tagid=' . $topic['ID_TAG'] . '">' . $topic['tag'] . '</a></td>';
		 echo '<td class="windowbg2"><a href="' . $scripturl . '?action=profile;u=' . $topic['id_member'] . '">' . $topic['poster_name'] . '</a></td>';
		 echo '<td class="windowbg2 centertext">' . $topic['num_replies'] . '</td>';
		 echo '<td class="windowbg2 centertext">' . $topic['num_views'] . '</td>';
		echo '</tr>';

	  }
	  echo '</table>
	      </div><br class="clear" /><br /><br />';
}

function template_tagging_results()
{
	global $scripturl, $txt, $context;
echo '
	<div class="orange_container mediumpadding" style="width:80%;margin-left:auto;margin-right:auto;">
	<div class="cat_bar">
		<h3>',
		$txt['smftags_resultsfor'] . $context['tag_search'],'
		</h3>
	</div>
	<table class="table_grid mlist">
		<thead>
  		<tr>
			<th class="red_container" style="width:100%;">',$txt['smftags_subject'],'</th>
			<th class="red_container centertext nowrap">',$txt['smftags_startedby'],'</th>
			<th class="red_container centertext">',$txt['smftags_replies'],'</th>
			<th class="red_container centertext">',$txt['smftags_views'], '</th>
		</tr>
		</thead><tbody>';
		foreach ($context['tags_topics'] as $i => $topic)
		{
			echo '<tr>';
			echo '<td class="windowbg2"><a href="' . $scripturl . '?topic=' . $topic['id_topic'] . '.0">' . $topic['subject'] . '</a></td>';
			echo '<td class="windowbg2"><a href="' . $scripturl . '?action=profile;u=' . $topic['id_member'] . '">' . $topic['poster_name'] . '</a></td>';
			echo '<td class="windowbg2 centertext">', $topic['num_replies'], '</td>';
			echo '<td class="windowbg2 centertext">', $topic['num_views'], '</td>';
			echo '</tr>';
		}
echo '
	<tr>
	  <td class="blue_container" colspan="4">' . $txt['smftags_pages'] . $context['page_index'] . '</td>
  	</tr>
  	</tbody></table></div>';
}

function template_addtag()
{
		global $scripturl, $txt, $context;

		echo '<form method="post" action="', $scripturl, '?action=tags;sa=submittag">
    <div class="cat_bar rounded_top centertext"><h3 class="catbg">',$txt['smftags_tagtoadd'], '</h3></div>
    <div class="generic_container largepadding">
    <div class="blue_container">
    	<div class="centertext">
    		<input type="text" name="tag" size="64" maxlength="100" />
    		<input type="submit" value="', $txt['smftags_addtag'], '" name="submit" class="button_submit" />
    	</div>
    </div>
    </div>
    <input type="hidden" name="topic" value="', $context['tags_topic'], '" />
	</form>
';
}

function template_ajax_addtag()
{
	global $scripturl, $txt, $context;

	echo '<div id="tagform" class="blue_container lightshadow mediumpadding" style="padding-bottom:0px;position:absolute;right:50px;">
		<form method="post" action=""><strong>';
		if(isset($context['not_allowed'])) {
			echo $txt['cannot_smftags_add'];
		}
		else {
    		echo $txt['smftags_tagtoadd'],'</strong>&nbsp;&nbsp;<input type="text" name="tag" id="newtags" size="50" maxlength="100" />
				<input type="hidden" name="topic" id="tagtopic" value="', $context['tags_topic'], '" />
    			<input class="button_submit" type="submit" onclick="submitTagForm($(\'#tags\'));return(false);" value="', $txt['go'], '" name="submit" />';
		}
		echo '</form>
		<div style="text-align:center;"><a href="#" onclick="$(\'#tagform\').remove();return(false);">Close</a></div>
		</div>';
}

function template_suggesttag()
{
	global $scripturl, $txt;

	echo '
<form method="POST" action="', $scripturl, '?action=tags;sa=suggest2">
<table border="1" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td width="50%" colspan="2"align="center" class="catbg">
    <b>', $txt['smftags_suggest'], '</b></td>
  </tr>
  <tr>
    <td width="28%" class="windowbg2" align="right"><span class="gen"><b>', $txt['smftags_tagtosuggest'], '</b></span></td>
    <td width="72%"  class="windowbg2"><input type="text" name="tag" size="64" maxlength="100" /></td>
  </tr>

  <tr>
    <td width="28%" colspan="2" align="center" class="windowbg2">
    <input type="submit" value="', $txt['smftags_suggest'], '" name="submit" /></td>
  </tr>
</table>
</form>
';
}
?>