{$profile_url = $SCRIPTURL|cat:'?action=profile;u='|cat:$C.user.id}
<div id="fatal_error">
  <div class="cat_bar">
    <h3>
      {$T.draft_saved}
    </h3>
  </div>
  <div class="windowbg">
    <div class="padding">{$T.draft_saved_full|sprintf:($profile_url|cat:;area=showposts;sa=drafts):$profile_url}</div>
  </div>
</div>
