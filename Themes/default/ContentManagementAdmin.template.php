<?php
// Version: 2.0 RC2; ContentManagement

function template_main()
{
	global $context, $scripturl, $boardurl, $txt;

	echo '
	<span class="clear upperframe"><span></span></span>
	<div class="roundframe">
		<div class="innerframe">
			<h3 class="catbg"><span class="left"></span>', $context['page_title'], '</h3>
			<div>
				', $txt['cms_admin_welcome'], '
				<br /><br />
				<form action="', $scripturl, '?action=cmsadmin" method="post">
					<select name="page" onchange="if (this.selectedIndex > 0 &amp;&amp; this.options[this.selectedIndex].value) document.location = smf_scripturl + \'?action=cmsadmin;page=\' + this.options[this.selectedIndex].value;">
						<option value="">', $txt['cms_pick_edit'], '</option>';

	foreach ($context['cms_pages'] as $page)
	{
		echo '
						<option value="', $page, '">', $page, '</option>';
	}

	echo '
					</select>
					<noscript><input type="submit" value="', $txt['go'], '" /></noscript>
				</form>
				<br /><br />
				<a href="', $scripturl, '?action=cmsadmin;add">', $txt['cms_add_new'], '</a><br />
				<a href="', $scripturl, '?action=cmsadmin;file=homepage">', $txt['cms_edit_homepage'], '</a><br />
				<a href="', $scripturl, '?action=cmsadmin;file=', MENU_CACHE_FILE, '">', $txt['cms_edit_menu'], '</a><br />
				<br /><br />
				', sprintf($txt['cms_upload_explain'], $boardurl . '/' . CMS_DIR . '/' . UPLOAD_DIR), '<br />
				<form action="', $scripturl, '?action=cmsadmin" method="post" enctype="multipart/form-data">
					<table id="uploads" width="10%">
						<tr>
							<td><input type="file" name="uploads[]" size="40" /></td>
						</tr>
						<tr>
							<td><a href="javascript:void(0);" onclick="table = document.getElementById(\'uploads\'); newRow = table.insertRow(table.rows.length - 1); newCell = newRow.insertCell(0); newCell.innerHTML = \'&lt;input type=\\\'file\\\' name=\\\'uploads[]\\\' size=\\\'40\\\' /&gt;\';">', $txt['cms_add_another'], '</a><input type="submit" name="submit" value="', $txt['cms_upload'], '" style="float: right;" /></td>
						</tr>
					</table>
				</form>';

	if (!empty($context['cms_uploads']))
	{
		echo '
				<form action="', $scripturl, '?action=cmsadmin" method="post">
					', $txt['cms_delete_uploaded'], '&nbsp;
					<select name="delete">';

		foreach ($context['cms_uploads'] as $upload)
		{
			echo '
						<option value="', $upload, '">', $upload, '</option>';
		}

		echo '
					</select>
					<input type="submit" value="', $txt['cms_delete'], '" />
				</form>';
	}

	echo '
			</div>
		</div>
	</div>
	<span class="lowerframe"><span></span></span>';
}

function template_cms_edit()
{
	global $context, $scripturl, $txt;

	echo '
	<span class="clear upperframe"><span></span></span>
	<div class="roundframe">
		<div class="innerframe">
			<h3 class="catbg"><span class="left"></span>', !isset($_GET['add']) ? sprintf($txt['cms_editing2'], $context['cms_edit_title']) : $txt['cms_adding_new'], '</h3>
			<div>
				<form action="', $scripturl, '?action=cmsadmin;', !isset($_GET['add']) ? ($context['cms_edit_is_page'] ? 'page' : 'file') . '=' . $context['cms_edit_file'] : 'add' , '" method="post">';

	if ($context['cms_edit_is_page'])
	{
		echo '
					<table>
						<tr>
							<td><b>', $txt['cms_title'], ':</b></td>
							<td><input type="text" name="title" value="', isset($context['cms_edit_title']) ? $context['cms_edit_title'] : '', '" size="50" /></td>
						</tr>
						<tr>
							<td><b>', $txt['cms_filename'], ':</b></td>
							<td><input type="text" name="filename" value="', isset($context['cms_edit_file']) ? $context['cms_edit_file'] : '', '" size="50" /></td>
						</tr>
						<tr>
							<td><b>', $txt['cms_type'], ':</b></td>
							<td>
								<label for="type_raw">', $txt['cms_type_php'], '</label> <input type="radio" name="type" id="type_raw" value="raw"', isset($context['cms_edit_is_bbc']) && !$context['cms_edit_is_bbc'] ? ' checked="checked"' : '', ' />
								<label for="type_bbc">', $txt['cms_type_bbc'], '</label> <input type="radio" name="type" id="type_bbc" value="bbc"', isset($context['cms_edit_is_bbc']) && $context['cms_edit_is_bbc'] ? ' checked="checked"' : '', ' />
							</td>
						</tr>
					</table><br />';
	}

	echo '
					<b>', $txt['cms_contents'], '</b><br />
					<textarea name="contents" rows="30" cols="50" style="width: 99%;">', $context['cms_edit_contents'], '</textarea><br />
					<div style="float: right;">';

	if (!isset($_GET['add']) && $context['cms_edit_is_page'])
	{
		echo '
						<input name="delete" type="submit" value="', $txt['cms_delete'], '" style="margin-right: 4px; font-weight: bold;" />';
	}

	echo '
						<input name="submit" type="submit" value="', $txt['save'], '" style="margin-right: 8px; font-size: large; font-weight: bold;" />
					</div>
					<br /><br />
				</form>
			</div>
		</div>
	</div>
	<span class="lowerframe"><span></span></span>';
}

?>