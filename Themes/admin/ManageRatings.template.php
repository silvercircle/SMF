<?php
function template_manage_ratings()
{
	global $context, $modSettings, $txt, $scripturl;

	echo '
	<div class="cat_bar">
		<h3>', $txt['ratings_settings_title'],'</h3>
	</div>
	<div class="blue_container mediumpadding cleantop">',
	$txt['ratings_help_intro'], '
	<div id="rhelp" class="smalltext" style="display:none;">',
	$txt['ratings_help'],'
	</div>
	<form method="post" accept-charset="UTF-8" action="',$context['post_url'],'">
		<input type="hidden" name="',$context['session_var'],'" value="',$context['session_id'],'" />
		<br>
		<h1 class="bigheader secondary">',$txt['rating_classes'],'</h1>
		<table class="table_grid" style="width:100%;">
			<thead>
			<tr>
			<th class="first_th glass cleantop" style="width:20px;">',
				$txt['rating_class_ID'],'
			</th>
			<th class="glass cleantop nowrap">',
				$txt['rating_class_desc'],'
			</th>
			<th class="glass cleantop nowrap">',
				$txt['rating_class_label'],'
			</th>
			<th class="glass cleantop nowrap">',
				$txt['rating_class_localized'],'
			</th>
			<th class="glass cleantop nowrap">',
				$txt['rating_class_format'],'
			</th>
			<th class="glass cleantop nowrap">',
				$txt['rating_class_groups_allowed'],'
			</th>
			<th class="glass cleantop nowrap">',
				$txt['rating_class_boards_allowed'],'
			</th>
			<th class="glass cleantop nowrap">',
				$txt['rating_class_points'],'
			</th>
			<th class="glass cleantop nowrap">',
				$txt['rating_class_unique'],'
			</th>
			<th class="last_th glass cleantop nowrap">',
				$txt['rating_class_anon'],'
			</th>
			</tr>
			</thead>
			<tbody>';
			foreach($context['rating_classes'] as $class) {
				$id = $class['id'];
				echo '
				<tr>
					<td>
						<input type="text" size="3" name="rating_id_', $id, '" value="', $id, '" />
					</td>
					<td>
						<input type="text" size="8" name="rating_desc_', $id, '" value="', $class['desc'], '" />
					</td>
					<td>
						<input type="text" size="10" name="rating_label_', $id, '" value="', $class['label'], '" />
					</td>
					<td>
						<input type="text" size="10" name="rating_localized_', $id, '" value="', $class['localized'], '" />
					</td>
					<td style="width:95%;">
						<input type="text" style="width:96%;" name="rating_format_', $id, '" value="', $class['format'], '" />
					</td>
					<td class="nowrap">
						<span style="color:green;">&bull;</span><input style=margin-bottom:4px;" type="text" size="15" name="rating_groups_', $id, '" value="', $class['groups'], '" /><br>
						<span style="color:red;">&bull;</span><input type="text" size="15" name="rating_groups_denied_', $id, '" value="', $class['groups_denied'], '" /><br>
					</td>
					<td class="nowrap">
						<span style="color:green;">&bull;</span><input style=margin-bottom:4px;" type="text" size="15" name="rating_boards_', $id, '" value="', $class['boards'], '" /><br>
						<span style="color:red;">&bull;</span><input type="text" size="15" name="rating_boards_denied_', $id, '" value="', $class['boards_denied'], '" />
					</td>
					<td class="nowrap">
						<input type="text" size="5" name="rating_points_', $id, '" value="', $class['points'], '" /><br>
						<input type="text" size="5" name="rating_cost_', $id, '" value="', $class['cost'], '" />
					</td>
					<td>
						<input type="checkbox" ',($class['unique'] ? 'checked="checked"' : ''),' name="rating_unique_', $id, '" value="1" />
					</td>
					<td>
						<input type="checkbox" ',($class['anon'] ? 'checked="checked"' : ''),' name="rating_anon_', $id, '" value="1" />
					</td>
				</tr>';
			}
			echo '
			</tbody>
		</table>
		<br>
		<h1 class="bigheader secondary">',$txt['rating_other'],'</h1>
		<input ',(!empty($context['rating_show_repair']) ? 'checked="checked"' : ''), ' class="aligned" type="checkbox" id="rating_show_repair" name="rating_show_repair" value="1" />&nbsp;&nbsp;<label for="rating_show_repair" class="aligned">',$txt['rating_show_repair'],'</label><br>
		<input ',(!empty($context['rating_allow_comments']) ? 'checked="checked"' : ''), ' class="aligned" type="checkbox" id="rating_allow_comments" name="rating_allow_comments" value="1" />&nbsp;&nbsp;<label for="rating_allow_comments" class="aligned">',$txt['rating_allow_comments'],'</label>
		<div class="righttext">
			<input type="submit" class="button_submit" value="Save" />
		</div>
	</form>
	</div>
	';
}
?>