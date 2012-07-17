<script type="text/javascript" src="{$S.default_theme_url}/scripts/profile.js"></script>
{if $C.browser.is_chrome && !$C.useris_owner}
  <script type="text/javascript"><!-- // --><![CDATA[
    disableAutoComplete();
  // ]]>
  </script>
{/if}
{if !empty($C.post_errors)}
  {include 'profile/error_message.tpl'}
{/if}
{if !empty($Cprofile_updated)}
  <div class="windowbg" id="profile_success">
    {$C.profile_updated}
  </div>
{/if}
{$SUPPORT->displayHook('profile_above')}
