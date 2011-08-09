<?php

/* * **** BEGIN LICENSE BLOCK *****
 * Version: MPL 1.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is http://code.mattzuba.com code.
 *
 * The Initial Developer of the Original Code is
 * Matt Zuba.
 * Portions created by the Initial Developer are Copyright (C) 2010-2011
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *
 * ***** END LICENSE BLOCK ***** */

function template_alias_settings() {
    global $scripturl, $txt, $context;

    echo '
	<div id="admincenter">
		<form action="', $context['post_url'], '" method="post" accept-charset="', $context['character_set'], '">
			<div class="cat_bar">
				<h3 class="catbg">
					', $txt['simplesef_alias'], '
				</h3>
			</div>
			<div class="windowbg2">
				<span class="topslice"><span></span></span>
				<div class="content">
					<p>', $txt['simplesef_alias_detail'], '</p>';

    foreach ($context['simplesef_aliases'] as $original => $alias)
        echo '
					<div style="margin-top: 1ex;"><input type="text" name="original[]" value="', $original, '" size="20" /> => <input type="text" name="alias[]" value="', $alias, '" size="20" /></div>';

    echo '
					<noscript>
						<div style="margin-top: 1ex;"><input type="text" name="original[]" size="20" class="input_text" /> => <input type="text" name="alias[]" size="20" class="input_text" /></div>
					</noscript>
					<div id="moreAliases"></div><div style="margin-top: 1ex; display: none;" id="moreAliases_link"><a href="#;" onclick="addNewAlias(); return false;">', $txt['simplesef_alias_clickadd'], '</a></div>
					<script type="text/javascript"><!-- // --><![CDATA[
						document.getElementById("moreAliases_link").style.display = "";

						function addNewAlias()
						{
							setOuterHTML(document.getElementById("moreAliases"), \'<div style="margin-top: 1ex;"><input type="text" name="original[]" size="20" class="input_text" /> => <input type="text" name="alias[]" size="20" class="input_text" /><\' + \'/div><div id="moreAliases"><\' + \'/div>\');
						}
					// ]]></script>
					<hr width="100%" size="1" class="hrcolor" />
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
					<input type="submit" name="save" value="', $txt['save'], '" class="button_submit" />
				</div>
				<span class="botslice"><span></span></span>
			</div>
		</form>
	</div>
	<br class="clear" />';
}

function template_callback_simplesef_ignore() {
    global $txt, $modSettings, $context;

    echo '
		<dt>
			<a id="simplesef_ignore"></a>
			<span>
				<label>', $txt['simplesef_ignore'], '</label><br />
				<span class="smalltext">', $txt['simplesef_ignore_desc'], '</span>
			</span>
		</dt>
		<dd>
			<select id="dummy_actions" multiple="multiple" size="9" style="min-width: 100px;">';
    foreach ($context['simplesef_dummy_actions'] as $action)
        echo '
				<option value="', $action, '">', $action, '</option>';
    echo '
			</select>
			<span style="text-align: center; display: inline-block;">
				<input type="button" id="simplesef_ignore_add" value="&raquo;" /><br />
				<input type="button" id="simplesef_ignore_add_all" value="&raquo;&raquo;" /><br />
				<input type="button" id="simplesef_ignore_remove_all" value="&laquo;&laquo;" /><br />
				<input type="button" id="simplesef_ignore_remove" value="&laquo;" /><br /><br />
			</span>
			<select id="dummy_ignore" multiple="multiple" size="9" style="min-width: 100px;">';
    foreach ($context['simplesef_dummy_ignore'] as $action)
        echo '
				<option value="', $action, '">', $action, '</option>';
    echo '
			</select>
			<input type="hidden" id="simplesef_ignore_actions" name="simplesef_ignore_actions" value="', !empty($modSettings['simplesef_ignore_actions']) ? $modSettings['simplesef_ignore_actions'] : '', '" />
		</dd>';
}