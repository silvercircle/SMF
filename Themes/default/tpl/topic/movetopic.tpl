{**
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
 *
 * template for moving a topic
 *}
{extends 'base.tpl'}
{block content}
<div id="move_topic" class="lower_padding">
	<form action="{$SCRIPTURL}?action=movetopic2;topic={$C.current_topic}'.0" method="post" accept-charset="UTF-8" onsubmit="submitonce(this);">
		<div class="cat_bar">
			<h3 class="catbg">{$T.move_topic}</h3>
		</div>
		<div class="blue_container cleantop centertext">
			<div class="content">
				<div class="move_topic mediumpadding">
					<div class="orange_container">
						<div class="content">
							<div style="padding-left:40px;">
								<strong>{$T.move_to}:</strong>
								<select name="toboard">
								{foreach $C.categories as $category}
									<optgroup label="{$category.name}">
									{foreach $category.boards as $board}
										<option value="{$board.id}" {($board.selected) ? ' selected="selected"' : ''} {($board.id == $C.current_board) ? ' disabled="disabled"' : ''}>{($board.child_level > 0) ? ('=='|str_repeat:($board.child_level -1)|cat:'=&gt;') : ''}{$board.name}</option>
									{/foreach}
									</optgroup>
								{/foreach}
								</select>
								<br>
								<label for="reset_subject"><input type="checkbox" name="reset_subject" id="reset_subject" onclick="document.getElementById('subjectArea').style.display = this.checked ? 'block' : 'none';" class="input_check" />{$T.moveTopic2}</label>
								<br>
								<fieldset id="subjectArea" style="display: none;">
								<dl class="settings">
									<dt><strong>{$T.moveTopic3}:</strong></dt>
									<dd><input type="text" name="custom_subject" size="30" value="{$C.subject}" class="input_text" /></dd>
								</dl>
								<label for="enforce_subject"><input type="checkbox" name="enforce_subject" id="enforce_subject" class="input_check" />{$T.moveTopic4}</label>
								</fieldset>
								<label for="postRedirect"><input type="checkbox" name="postRedirect" id="postRedirect" {($C.is_approved) ? 'checked="checked"' : ''} onclick="{($C.is_approved) ? '' : ('if (this.checked && !confirm(\''|cat:{$T.move_topic_unapproved_js}|cat:'\')) return false; document.getElementById(\'reasonArea\').style.display = this.checked ? \'block\' : \'none\';')}" class="input_check" />{$T.moveTopic1}</label>
							</div>
							<fieldset id="reasonArea" style="margin-top: 1ex;{($C.is_approved) ? '' : 'display: none;'}">
							<dl class="settings">
							<dt>
								{$T.moved_why}
							</dt>
							<dd>
								<textarea name="reason" rows="3" cols="40">{$T.movetopic_default}</textarea>
							</dd>
							</dl>
							</fieldset>
						</div>
					</div>
					<br>
					<div class="righttext">
						<input type="submit" value="{$T.move_topic}" onclick="return submitThisOnce(this);" accesskey="s" class="button_submit" />
					</div>
				</div>
			</div>
		</div>
		{if $C.back_to_topic}
			<input type="hidden" name="goback" value="1" />
		{/if}
		<input type="hidden" name="{$C.session_var}" value="{$C.session_id}" />
		<input type="hidden" name="seqnum" value="{$C.form_sequence_number}" />
	</form>
</div>
{/block}