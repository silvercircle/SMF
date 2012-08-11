<form action="{$SCRIPTURL}?action=pm;sa=prune" method="post" accept-charset="UTF-8" onsubmit="return confirm('{$T.pm_prune_warning}');">
	<div class="cat_bar2">
		<h3>{$T.pm_prune}</h3>
	</div>
	<div class="blue_container cleantop">
		<div class="content">
			<p>{$T.pm_prune_desc1} <input type="text" name="age" size="3" value="14" class="input_text" /> {$T.pm_prune_desc2}</p>
			<div class="righttext">
				<input type="submit" value="{$T.delete}" class="default" />
			</div>
		</div>
	</div>
	{$C.hiiden_sid_input}
</form>
