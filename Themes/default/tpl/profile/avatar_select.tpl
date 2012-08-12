<dt>
  <strong id="personal_picture">{$T.personal_picture}</strong>
  <input type="radio" onclick="swap_avatar(this); return true;" name="avatar_choice" id="avatar_choice_none" value="none" {($C.member.avatar.choice == 'none') ? ' checked="checked"' : ''} class="input_radio" /><label for="avatar_choice_none"{(isset($C.modify_error.bad_avatar)) ? ' class="error"' : ''}>{$T.no_avatar}</label><br>
  {if !empty($C.member.avatar.allow_server_stored)}
    <input type="radio" onclick="swap_avatar(this); return true;" name="avatar_choice" id="avatar_choice_server_stored" value="server_stored"{($C.member.avatar.choice == 'server_stored') ? ' checked="checked"' : ''} class="input_radio" /><label for="avatar_choice_server_stored" {(isset($C.modify_error.bad_avatar)) ? ' class="error"' : ''}>{$T.choose_avatar_gallery}</label><br>
  {/if}
  {if !empty($C.member.avatar.allow_external)}
    <input type="radio" onclick="swap_avatar(this); return true;" name="avatar_choice" id="avatar_choice_external" value="external"{($C.member.avatar.choice == 'external') ? ' checked="checked"' : ''} class="input_radio" /><label for="avatar_choice_external"{(isset($C.modify_error.bad_avatar)) ? ' class="error"' : ''}>{$T.my_own_pic}</label><br>
  {/if}
  {if !empty($C.member.avatar.allow_upload)}
    <input type="radio" onclick="swap_avatar(this); return true;" name="avatar_choice" id="avatar_choice_upload" value="upload"{($C.member.avatar.choice == 'upload') ? ' checked="checked"' : ''} class="input_radio" /><label for="avatar_choice_upload"{(isset($C.modify_error.bad_avatar)) ? ' class="error"' : ''}>{$T.avatar_will_upload}</label><br>
  {/if}
  {if !empty($C.member.avatar.allow_gravatar)}
    <input type="radio" onclick="swap_avatar(this); return true;" name="avatar_choice" id="avatar_choice_gravatar" value="gravatar"{($C.member.avatar.choice == 'gravatar') ? ' checked="checked"' : ''} class="input_radio" /><label for="avatar_choice_gravatar"{(isset($C.modify_error.bad_avatar)) ? ' class="error"' : ''}>{$T.avatar_gravatar}</label><br>
  {/if}
</dt>
<dd>
{if !empty($C.member.avatar.allow_server_stored)}
  <div id="avatar_server_stored">
    <div>
      <select name="cat" id="cat" onchange="changeSel('');" onfocus="selectRadioByName(document.forms.creator.avatar_choice, 'server_stored');">
      {foreach $C.avatars as $avatar}
        <option value="{$avatar.filename}{($avatar.is_dir) ? '/' : ''}"{($avatar.checked) ? ' selected="selected"' : ''}>{$avatar.name}</option>
      {/foreach}
      </select>
    </div>
    <div>
      <select name="file" id="file" size="10" style="display: none;" onchange="showAvatar()" onfocus="selectRadioByName(document.forms.creator.avatar_choice, 'server_stored');" disabled="disabled"><option></option></select>
    </div>
    <div><img id="avatar" src="{(!empty($C.member.avatar.allow_external) and $C.member.avatar.choice == 'external') ? $C.member.avatar.external : ($M.avatar_url|cat:'/blank.gif')}" alt="Do Nothing" /></div>
    <script type="text/javascript"><!-- // --><![CDATA[
      var files = ["{'\", \"'|implode:$C.avatar_list}"];
      var avatar = document.getElementById("avatar");
      var cat = document.getElementById("cat");
      var selavatar = "{$C.avatar_selected}";
      var avatardir = "{$M.avatar_url}/";
      var size = avatar.alt.substr(3, 2) + " " + avatar.alt.substr(0, 2) + String.fromCharCode(117, 98, 116);
      var file = document.getElementById("file");

      if (avatar.src.indexOf("blank.gif") > -1)
        changeSel(selavatar);
      else
        previewExternalAvatar(avatar.src)

      function changeSel(selected)
      {
        if (cat.selectedIndex == -1)
          return;

        if (cat.options[cat.selectedIndex].value.indexOf("/") > 0)
        {
          var i;
          var count = 0;

          file.style.display = "inline";
          file.disabled = false;

          for (i = file.length; i >= 0; i = i - 1)
            file.options[i] = null;

          for (i = 0; i < files.length; i++)
            if (files[i].indexOf(cat.options[cat.selectedIndex].value) == 0)
            {
              var filename = files[i].substr(files[i].indexOf("/") + 1);
              var showFilename = filename.substr(0, filename.lastIndexOf("."));
              showFilename = showFilename.replace(/[_]/g, " ");

              file.options[count] = new Option(showFilename, files[i]);

              if (filename == selected)
              {
                if (file.options.defaultSelected)
                  file.options[count].defaultSelected = true;
                else
                  file.options[count].selected = true;
              }

              count++;
            }

          if (file.selectedIndex == -1 && file.options[0])
            file.options[0].selected = true;

          showAvatar();
        }
        else
        {
          file.style.display = "none";
          file.disabled = true;
          document.getElementById("avatar").src = avatardir + cat.options[cat.selectedIndex].value;
          document.getElementById("avatar").style.width = "";
          document.getElementById("avatar").style.height = "";
        }
      }

      function showAvatar()
      {
        if (file.selectedIndex == -1)
          return;

        document.getElementById("avatar").src = avatardir + file.options[file.selectedIndex].value;
        document.getElementById("avatar").alt = file.options[file.selectedIndex].text;
        document.getElementById("avatar").alt += file.options[file.selectedIndex].text == size ? "!" : "";
        document.getElementById("avatar").style.width = "";
        document.getElementById("avatar").style.height = "";
      }

      function previewExternalAvatar(src)
      {
        if (!document.getElementById("avatar"))
          return;

        var maxHeight = {(!empty($M.avatar_max_height_external)) ? $M.avatar_max_height_external : 0};
        var maxWidth = {(!empty($M.avatar_max_width_external)) ? $M.avatar_max_width_external : 0};
        var tempImage = new Image();

        tempImage.src = src;
        if (maxWidth != 0 && tempImage.width > maxWidth)
        {
          document.getElementById("avatar").style.height = parseInt((maxWidth * tempImage.height) / tempImage.width) + "px";
          document.getElementById("avatar").style.width = maxWidth + "px";
        }
        else if (maxHeight != 0 && tempImage.height > maxHeight)
        {
          document.getElementById("avatar").style.width = parseInt((maxHeight * tempImage.width) / tempImage.height) + "px";
          document.getElementById("avatar").style.height = maxHeight + "px";
        }
        document.getElementById("avatar").src = src;
      }
      // ]]>
    </script>
  </div>
{/if}
{if !empty($C.member.avatar.allow_external)}
  <div id="avatar_external">
    <div class="smalltext">{$T.avatar_by_url}</div>
    <input type="text" name="userpicpersonal" size="45" value="{$C.member.avatar.external}" onfocus="selectRadioByName(document.forms.creator.avatar_choice, 'external');" onchange="if (typeof(previewExternalAvatar) != 'undefined') previewExternalAvatar(this.value);" class="input_text" />
  </div>
{/if}
{if !empty($C.member.avatar.allow_upload)}
  <div id="avatar_upload">
    <input type="file" name="attachment" onfocus="selectRadioByName(document.forms.creator.avatar_choice, 'upload');" class="input_file" />
    {($C.member.avatar.id_attach > 0) ? ('<br><br><img src="'|cat:$C.member.avatar.href|cat:((strpos($C.member.avatar.href, '?') === false) ? '?' : '&amp;')|cat:'time='|cat:$C.time_now|cat:'" alt="" /><input type="hidden" name="id_attach" value="'|cat:$C.member.avatar.id_attach|cat:'" />') : ''}
  </div>
{/if}
<script type="text/javascript"><!-- // --><![CDATA[
  {if !empty($C.member.avatar.allow_server_stored)}
    document.getElementById("avatar_server_stored").style.display = "{($C.member.avatar.choice == 'server_stored') ? '' : 'none'}";
  {/if}
  {if !empty($C.member.avatar.allow_external)}
    document.getElementById("avatar_external").style.display = "{($C.member.avatar.choice == 'external') ? '' : 'none'}";
  {/if}
  {if !empty($C.member.avatar.allow_upload)}
    document.getElementById("avatar_upload").style.display = "{($C.member.avatar.choice == 'upload') ? '' : 'none'}";
  {/if}

  function swap_avatar(type)
  {
    switch(type.id)
    {
      case "avatar_choice_server_stored":
        {(!empty($C.member.avatar.allow_server_stored)) ? 'document.getElementById("avatar_server_stored").style.display = "";' : ''}
        {(!empty($C.member.avatar.allow_external)) ? 'document.getElementById("avatar_external").style.display = "none";' : ''}
        {(!empty($C.member.avatar.allow_upload)) ? 'document.getElementById("avatar_upload").style.display = "none";' : ''}
        break;
      case "avatar_choice_external":
        {(!empty($C.member.avatar.allow_server_stored)) ? 'document.getElementById("avatar_server_stored").style.display = "none";' : ''}
        {(!empty($C.member.avatar.allow_external)) ? 'document.getElementById("avatar_external").style.display = "";' : ''}
        {(!empty($C.member.avatar.allow_upload)) ? 'document.getElementById("avatar_upload").style.display = "none";' : ''}
        break;
      case "avatar_choice_upload":
        {(!empty($C.member.avatar.allow_server_stored)) ? 'document.getElementById("avatar_server_stored").style.display = "none";' : ''}
        {(!empty($C.member.avatar.allow_external)) ? 'document.getElementById("avatar_external").style.display = "none";' : ''}
        {(!empty($C.member.avatar.allow_upload)) ? 'document.getElementById("avatar_upload").style.display = "";' : ''}
        break;
      case "avatar_choice_none":
      case "avatar_choice_gravatar":
        {(!empty($C.member.avatar.allow_server_stored)) ? 'document.getElementById("avatar_server_stored").style.display = "none";' : ''}
        {(!empty($C.member.avatar.allow_external)) ? 'document.getElementById("avatar_external").style.display = "none";' : ''}
        {(!empty($C.member.avatar.allow_upload)) ? 'document.getElementById("avatar_upload").style.display = "none";' : ''}
        break;
    }
  }
// ]]>
</script>
</dd>
