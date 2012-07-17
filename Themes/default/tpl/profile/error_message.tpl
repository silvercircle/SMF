  <div class="orange_container noroundex" id="profile_error">
	<span>
    {(!empty($C.custom_error_title)) ? $C.custom_error_title : $T.profile_errors_occurred}:
  </span>
			<ul class="reset">
		  {foreach $C.post_errors as $error}
        {$index = 'profile_error_'|cat:$error}
				<li>
          {(isset($T.$index)) ? $T.$index : $error}
        </li>
      {/foreach}
			</ul>
	</div>