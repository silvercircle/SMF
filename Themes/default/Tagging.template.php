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
	<table border="0" cellpadding="0" cellspacing="0" align="center" width="95%">
  <tr>
  	<td align="center"  class="catbg">',$txt['smftags_popular'], '

  	</td>
  	</tr>
  <tr>
  	<td align="center" class="windowbg2">';
  	if (isset($context['poptags']))
  		echo $context['poptags'];
 echo '
  	</td>
  	</tr>
  	</table>
  	<br />
  	<table border="0" cellpadding="0" cellspacing="0" align="center" width="95%">
  <tr>
  	<td align="center"  class="catbg">',$txt['smftags_latest'], '

  	</td>
  	</tr>
  <tr>
  	<td align="center" class="windowbg2">
  	<table border="0" width="100%" cellspacing="1" cellpadding="4" class="bordercolor">
					<tr>
						<td class="catbg3">',$txt['smftags_subject'],'</td>
						<td class="catbg3" width="11%">',$txt['smftags_topictag'],'</td>
						<td class="catbg3" width="11%">',$txt['smftags_startedby'],'</td>
						<td class="catbg3" width="4%" align="center">',$txt['smftags_replies'],'</td>
						<td class="catbg3" width="4%" align="center">', $txt['smftags_views'], '</td>
					</tr>';
		foreach ($context['tags_topics'] as $i => $topic)
		{
				echo '<tr>';
					echo '<td class="windowbg2"><a href="' . $scripturl . '?topic=' . $topic['id_topic'] . '.0">' . $topic['subject'] . '</a></td>';
					echo '<td class="windowbg2"><a href="' . $scripturl . '?action=tags;tagid=' . $topic['ID_TAG'] . '">' . $topic['tag'] . '</a></td>';
					echo '<td class="windowbg"><a href="' . $scripturl . '?action=profile;u=' . $topic['id_member'] . '">' . $topic['poster_name'] . '</a></td>';
					echo '<td class="windowbg2">' . $topic['num_replies'] . '</td>';
					echo '<td class="windowbg2">' . $topic['num_views'] . '</td>';
				echo '</tr>';

		}
echo '

  	</tr>
  	</table>
  	</td></tr></table>

  	<br />
  	';
}

function template_tagging_results()
{
	global $scripturl, $txt, $context;
echo '
	<div style="width:80%;margin-left:auto;margin-right:auto;">
	<div class="cat_bar">
		<h3 class="catbg">',
		$txt['smftags_resultsfor'] . $context['tag_search'],'
		</h3>
	</div>
	<div class="generic_container mediumpadding">
	<table class="blue_container">
  		<tr class="orange_container">
			<td class="catbg3" style="width:100%;">',$txt['smftags_subject'],'</td>
			<td class="catbg3" style="white-space:nowrap;">',$txt['smftags_startedby'],'</td>
			<td class="catbg3" style="text-align:center;">',$txt['smftags_replies'],'</td>
			<td class="catbg3" style="text-align:center;">',$txt['smftags_views'], '</td>
		</tr>';
		foreach ($context['tags_topics'] as $i => $topic)
		{
			echo '<tr>';
			echo '<td class="windowbg2"><a href="' . $scripturl . '?topic=' . $topic['id_topic'] . '.0">' . $topic['subject'] . '</a></td>';
			echo '<td class="windowbg"><a href="' . $scripturl . '?action=profile;u=' . $topic['id_member'] . '">' . $topic['poster_name'] . '</a></td>';
			echo '<td class="windowbg2">', $topic['num_replies'], '</td>';
			echo '<td class="windowbg2">', $topic['num_views'], '</td>';
			echo '</tr>';
		}
echo '
	<tr>
	<td colspan="4">' . $txt['smftags_pages'] . $context['page_index'] . '</td>
  	</tr>
  	</table></div></div>';
}

function template_addtag()
{
		global $scripturl, $txt, $context;

		echo '<form method="post" action="', $scripturl, '?action=tags;sa=submittag">
			<table center" width="100%">
  <tr>
    <td width="50%" colspan="2"  align="center" class="catbg">
    <b>', $txt['smftags_addtag2'], '</b></td>
  </tr>
  <tr>
    <td width="28%"  class="windowbg2" align="right"><span class="gen"><b>', $txt['smftags_tagtoadd'], '</b></span></td>
    <td width="72%" class="windowbg2"><input type="text" name="tag" size="64" maxlength="100" /></td>
  </tr>

  <tr>
    <td width="28%" colspan="2" align="center" class="windowbg2">
    <input type="hidden" name="topic" value="', $context['tags_topic'], '" />
    <input type="submit" value="', $txt['smftags_addtag2'], '" name="submit" /></td>

  </tr>
</table>
</form>
';
}

function template_ajax_addtag()
{
	global $scripturl, $txt, $context;

	echo '<div id="tagform" class="blue_container lightshadow mediumpadding" style="padding-bottom:0px;position:absolute;right:50px;">
		<form method="post" action=""><strong>',
    	$txt['smftags_tagtoadd'],'</strong>&nbsp;&nbsp;<input type="text" name="tag" id="newtags" size="50" maxlength="100" />
		<input type="hidden" name="topic" id="tagtopic" value="', $context['tags_topic'], '" />
    	<input class="button_submit" type="submit" onclick="submitTagForm($(\'#tags\'));return(false);" value="', $txt['go'], '" name="submit" />
		</form>
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