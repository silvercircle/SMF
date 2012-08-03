{*foo*}

<?xml version="1.0" encoding="UTF-8" ?>
<document>
 <response open="default_overlay" width="50%" />
 <content>
 <![CDATA[ <!-- > -->
	{if $C.preview}
		<div class="title_bar">
	 		<h1>{$C.preview.first_subject}</h1>
		</div>
		<div class="smallpadding" id="mcard_content">
	 		<div class="orange_container" style="margin-bottom:3px;"><strong>{$T.started_by}: {$C.member_started.name}, {$C.preview.first_time}</strong></div>
	 		<div class="blue_container smallpadding" style="margin-bottom:5px;">{$C.preview.first_body}</div>
			{if $C.member_lastpost}
	 			<div class="orange_container" style="margin-bottom:3px;"><strong>{$T.last_post} {$T.by}: {$C.member_lastpost.name}, {$C.preview.last_time}</strong></div>
	 			<div class="blue_container smallpadding" style="margin-bottom:5px;">{$C.preview.last_body}</div>
	 		{/if}
		</div>
		<div class="cat_bar">
	  	<div style="position:absolute;bottom:3px;right:8px;">
	   		<a href="{$SCRIPTURL}?topic={$C.preview.id_topic}">{$T.read_topic}</a>
				&nbsp;|&nbsp;<a href="{$SCRIPTURL}?topic={$C.preview.id_topic}.msg{$C.preview.new_from}#new">{$T.visit_new}</a>
	  	</div>
			<div class="clear">
		</div>
		</div>
	{/if}
 ]]>
 </content>
</document>