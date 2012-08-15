<dd></dd>
  </dl>
    <ul id="theme_settings">
      <!-- todo drafts -> plugin <li>
        <input type="hidden" name="default_options[use_drafts]" value="0" />
        <label for="use_drafts"><input type="checkbox" name="default_options[use_drafts]" id="use_drafts" value="1"', !empty($context['member']['options']['use_drafts']) ? ' checked="checked"' : '', ' class="input_check" /> ', $txt['use_drafts'], '</label>
      </li>-->
      <li>
        <input type="hidden" name="default_options[show_board_desc]" value="0" />
        <label for="show_board_desc"><input type="checkbox" name="default_options[show_board_desc]" id="show_board_desc" value="1"{(!empty($C.member.options.show_board_desc)) ? ' checked="checked"' : ''} class="input_check" /> {$T.board_desc_inside}</label>
      </li>
      <li>
        <input type="hidden" name="default_options[show_children]" value="0" />
        <label for="show_children"><input type="checkbox" name="default_options[show_children]" id="show_children" value="1"{(!empty($C.member.options.show_children)) ? ' checked="checked"' : ''} class="input_check" /> {$T.show_children}</label>
      </li>
      <li>
        <input type="hidden" name="default_options[post_icons_index]" value="0" />
        <label for="post_icons_index"><input type="checkbox" name="default_options[post_icons_index]" id="post_icons_index" value="1"{(!empty($C.member.options.post_icons_index)) ? ' checked="checked"' : ''} class="input_check" /> {$T.post_icons_index}</label>
      </li>
      <li>
        <input type="hidden" name="default_options[show_no_avatars]" value="0" />
        <label for="show_no_avatars"><input type="checkbox" name="default_options[show_no_avatars]" id="show_no_avatars" value="1"{(!empty($C.member.options.show_no_avatars)) ? ' checked="checked"' : ''} class="input_check" /> {$T.show_no_avatars}</label>
      </li>
      <li>
        <input type="hidden" name="default_options[show_no_signatures]" value="0" />
        <label for="show_no_signatures"><input type="checkbox" name="default_options[show_no_signatures]" id="show_no_signatures" value="1"{(!empty($C.member.options.show_no_signatures)) ? ' checked="checked"' : ''} class="input_check" /> {$T.show_no_signatures}</label>
      </li>
      <li>
        <input type="hidden" name="default_options[use_share_bar]" value="0" />
        <label for="use_share_bar"><input type="checkbox" name="default_options[use_share_bar]" id="use_share_bar" value="1"{(!empty($C.member.options.use_share_bar)) ? ' checked="checked"' : ''} class="input_check" /> {$T.disable_share_bar}</label>
      </li>   
      <li>
        <input type="hidden" name="default_options[disable_analytics]" value="0" />
        <label for="disable_analytics"><input type="checkbox" name="default_options[disable_analytics]" id="disable_analytics" value="1"{(!empty($C.member.options.disable_analytics)) ? ' checked="checked"' : ''} class="input_check" /> {$T.disable_analytics}</label>
      </li>
      {if $S.allow_no_censored}
        <li>
          <input type="hidden" name="default_options[show_no_censored]" value="0" />
          <label for="show_no_censored"><input type="checkbox" name="default_options[show_no_censored]" id="show_no_censored" value="1"{(!empty($C.member.options.show_no_censored)) ? ' checked="checked"' : ''} class="input_check" /> {$T.show_no_censored}</label>
        </li>
      {/if}
      <li>
        <input type="hidden" name="default_options[return_to_post]" value="0" />
        <label for="return_to_post"><input type="checkbox" name="default_options[return_to_post]" id="return_to_post" value="1"{(!empty($C.member.options.return_to_post)) ? ' checked="checked"' : ''} class="input_check" /> {$T.return_to_post}</label>
      </li>
      <li>
        <input type="hidden" name="default_options[no_new_reply_warning]" value="0" />
        <label for="no_new_reply_warning"><input type="checkbox" name="default_options[no_new_reply_warning]" id="no_new_reply_warning" value="1"{(!empty($C.member.options.no_new_reply_warning)) ? ' checked="checked"' : ''} class="input_check" /> {$T.no_new_reply_warning}</label>
      </li>
      {if !empty($M.enable_buddylist)}
        <li>
          <input type="hidden" name="default_options[posts_apply_ignore_list]" value="0" />
          <label for="posts_apply_ignore_list"><input type="checkbox" name="default_options[posts_apply_ignore_list]" id="posts_apply_ignore_list" value="1"{(!empty($C.member.options.posts_apply_ignore_list)) ? ' checked="checked"' : ''} class="input_check" /> {$T.posts_apply_ignore_list}</label>
        </li>
      {/if}
      <li>
        <input type="hidden" name="default_options[view_newest_first]" value="0" />
        <label for="view_newest_first"><input type="checkbox" name="default_options[view_newest_first]" id="view_newest_first" value="1"{(!empty($C.member.options.view_newest_first)) ? ' checked="checked"' : ''} class="input_check" /> {$T.recent_posts_at_top}</label>
      </li>
      {if empty($M.disable_wysiwyg)}
        <li>
          <input type="hidden" name="default_options[wysiwyg_default]" value="0" />
          <label for="wysiwyg_default"><input type="checkbox" name="default_options[wysiwyg_default]" id="wysiwyg_default" value="1"{(!empty($C.member.options.wysiwyg_default)) ? ' checked="checked"' : ''} class="input_check" /> {$T.wysiwyg_default}</label>
        </li>
      {/if}
      {if empty($M.disableCustomPerPage)}
        <li>
          <label for="topics_per_page">{$T.topics_per_page}</label>
          <select name="default_options[topics_per_page]" id="topics_per_page">
            <option value="0"{(empty($C.member.options.topics_per_page)) ? ' selected="selected"' : ''}>{$T.per_page_default} ({$M.defaultMaxTopics})</option>
            <option value="5"{(!empty($C.member.options.topics_per_page) and $C.member.options.topics_per_page == 5 ) ? ' selected="selected"' : ''}>5</option>
            <option value="10"{(!empty($C.member.options.topics_per_page) and $C.member.options.topics_per_page == 10) ? ' selected="selected"' : ''}>10</option>
            <option value="25"{(!empty($C.member.options.topics_per_page) and $C.member.options.topics_per_page == 25) ? ' selected="selected"' : ''}>25</option>
            <option value="50"{(!empty($C.member.options.topics_per_page) and $C.member.options.topics_per_page == 50) ? ' selected="selected"' : ''}>50</option>
          </select>
        </li>
        <li>
          <label for="messages_per_page">{$T.messages_per_page}</label>
          <select name="default_options[messages_per_page]" id="messages_per_page">
            <option value="0"{(empty($C.member.options.messages_per_page)) ? ' selected="selected"' : ''}>{$T.per_page_default} ({$M.defaultMaxMessages})</option>
            <option value="5"{(!empty($C.member.options.messages_per_page) and $C.member.options.messages_per_page == 5) ? ' selected="selected"' : ''}>5</option>
            <option value="10"{(!empty($C.member.options.messages_per_page) and $C.member.options.messages_per_page == 10) ? ' selected="selected"' : ''}>10</option>
            <option value="25"{(!empty($C.member.options.messages_per_page) and $C.member.options.messages_per_page == 25) ? ' selected="selected"' : ''}>25</option>
            <option value="50"{(!empty($C.member.options.messages_per_page) and $C.member.options.messages_per_page == 50) ? ' selected="selected"' : ''}>50</option>
          </select>
        </li>
      {/if}
      {if !empty($M.cal_enabled)}
        <li>
          <label for="calendar_start_day">{$T.calendar_start_day}:</label>
          <select name="default_options[calendar_start_day]" id="calendar_start_day">
            <option value="0"{(empty($C.member.options.calendar_start_day)) ? ' selected="selected"' : ''}>{$T.days.0}</option>
            <option value="1"{(!empty($C.member.options.calendar_start_day) and $C.member.options.calendar_start_day == 1) ? ' selected="selected"' : ''}>{$T.days.1}</option>
            <option value="6"{(!empty($C.member.options.calendar_start_day) and $C.member.options.calendar_start_day == 6) ? ' selected="selected"' : ''}>{$T.days.6}</option>
          </select>
        </li>
      {/if}
      <li>
        <label for="display_quick_reply">{$T.display_quick_reply}</label>
        <select name="default_options[display_quick_reply]" id="display_quick_reply">
          <option value="0"{(empty($C.member.options.display_quick_reply)) ? ' selected="selected"' : ''}>{$T.display_quick_reply1}</option>
          <option value="1"{(!empty($C.member.options.display_quick_reply)) ? ' selected="selected"' : ''}>{$T.display_quick_reply2}</option>
        </select>
      </li>
      <li>
        <label for="display_quick_mod">{$T.display_quick_mod}</label>
        <select name="default_options[display_quick_mod]" id="display_quick_mod">
          <option value="0"{(empty($C.member.options.display_quick_mod)) ? ' selected="selected"' : ''}>{$T.display_quick_mod_none}</option>
          <option value="1"{(!empty($C.member.options.display_quick_mod) and $C.member.options.display_quick_mod == 1) ? ' selected="selected"' : ''}>{$T.display_quick_mod_check}</option>
        </select>
      </li>
      <li>
        <label for="editor_height">{$T.editor_height}</label>
        <input size="5" type="text" name="default_options[editor_height]" id="editor_height" value="{(empty($C.member.options.editor_height)) ? '250' : $C.member.options.editor_height}" />
      </li>
      <li>
        <label for="content_width">{$T.content_width}</label>
        <input type="text" name="default_options[content_width]" id="content_width" value="{(empty($C.member.options.content_width)) ? '95%' : $C.member.options.content_width}" />
      </li>
    </ul>
    <dl>
<dd></dd>

