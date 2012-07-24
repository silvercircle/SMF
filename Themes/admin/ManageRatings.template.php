<?php
function template_manage_ratings()
{
	global $context, $modSettings, $txt, $scripturl;

	echo '
	<div class="cat_bar">
		<h3>', $txt['ratings_settings_title'],'</h3>
	</div>
	<div class="blue_container mediumpadding cleantop">',
	$txt['ratings_help'], '
	<form method="post" accept-charset="UTF-8" action="',$context['post_url'],'">
		<input ',(!empty($context['use_rating_widget']) ? 'checked="checked"' : ''), ' type="checkbox" name="use_widget" value="1" />&nbsp;&nbsp;',$txt['use_rating_widget'],'
		<input type="hidden" name="',$context['session_var'],'" value="',$context['session_id'],'" />
		<br>
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
				$txt['rating_class_format'],'
			</th>
			<th class="glass cleantop nowrap">',
				$txt['rating_class_groups_allowed'],'
			</th>
			<th class="glass cleantop nowrap">',
				$txt['rating_class_boards_allowed'],'
			</th>
			<th class="last_th glass cleantop nowrap">',
				$txt['rating_class_points'],'
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
						<input type="text" size="15" name="rating_desc_', $id, '" value="', $class['desc'], '" />
					</td>
					<td>
						<input type="text" size="15" name="rating_label_', $id, '" value="', $class['label'], '" />
					</td>
					<td style="width:95%;">
						<input type="text" style="width:96%;" name="rating_format_', $id, '" value="', $class['format'], '" />
					</td>
					<td>
						<input type="text" size="15" name="rating_groups_', $id, '" value="', $class['groups'], '" />
					</td>
					<td>
						<input type="text" size="15" name="rating_boards_', $id, '" value="', $class['boards'], '" />
					</td>
					<td>
						<input type="text" size="5" name="rating_points_', $id, '" value="', $class['points'], '" />
					</td>
				</tr>';
			}
			echo '
			</tbody>
		</table>
		<br>
		<div class="righttext">
			<input type="submit" class="button_submit" value="Save" />
		</div>
	</form>
	</div>
	';
}
?>