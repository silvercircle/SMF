{include "generics.tpl"}
  <form action="{$SCRIPTURL}?action=pm;sa=search2" method="post" accept-charset="UTF-8" name="searchform" id="searchform">
    <div class="cat_bar2">
      <h3>{$T.pm_search_title}</h3>
    </div>
    <div class="blue_container mediumpadding cleantop">
    {if !empty($C.search_errors)}
      <div class="errorbox">
        {'<br>'|implode:$C.search_errors.messages}
      </div>
    {/if}
    {if $C.simple_search}
      <fieldset id="simple_search">
        <div class="content">
          <div id="search_term_input">
            <strong>{$T.pm_search_text}:</strong>
            <input type="text" name="search"{(!empty($C.search_params.search)) ? (' value="'|cat:$C.search_params.search|cat:'"') : ''} size="40" class="input_text" />
            <input type="submit" name="submit" value="{$T.pm_search_go}" class="default" />
          </div>
          <a href="{$SCRIPTURL}?action=pm;sa=search;advanced" onclick="this.href += ';search=' + escape(document.forms.searchform.search.value);">{$T.pm_search_advanced}</a>
          <input type="hidden" name="advanced" value="0" />
        </div>
      </fieldset>
    {else}
      <fieldset id="advanced_search">
        <div class="content">
          <input type="hidden" name="advanced" value="1" />
          <span>
            <strong>{$T.pm_search_text}:</strong>
            <input type="text" name="search"{(!empty($C.search_params.search)) ? (' value="'|cat:$C.search_params.search|cat:'"') : ''} size="40" class="input_text" />
            <script type="text/javascript"><!-- // --><![CDATA[
              function initSearch()
              {
                if (document.forms.searchform.search.value.indexOf("%u") != -1)
                  document.forms.searchform.search.value = unescape(document.forms.searchform.search.value);
              }
              createEventListener(window);
              window.addEventListener("load", initSearch, false);
            // ]]></script>
            <select name="searchtype">
              <option value="1"{(empty($C.search_params.searchtype)) ? ' selected="selected"' : ''}>{$T.pm_search_match_all}</option>
              <option value="2"{(!empty($C.search_params.searchtype)) ? ' selected="selected"' : ''}>{$T.pm_search_match_any}</option>
            </select>
          </span>
          <dl id="search_options">
            <dt>{$T.pm_search_user}:</dt>
            <dd><input type="text" name="userspec" value="{(empty($C.search_params.userspec)) ? '*' : $C.search_params.userspec}" size="40" class="input_text" /></dd>
            <dt>{$T.pm_search_order}:</dt>
            <dd>
              <select name="sort">
                <option value="relevance|desc">{$T.pm_search_orderby_relevant_first}</option>
                <option value="id_pm|desc">{$T.pm_search_orderby_recent_first}</option>
                <option value="id_pm|asc">{$T.pm_search_orderby_old_first}</option>
              </select>
            </dd>
            <dt class="options">{$T.pm_search_options}:</dt>
            <dd class="options">
              <label class="aligned" for="show_complete_pm"><input type="checkbox" name="show_complete" id="show_complete_pm" value="1"{(!empty($C.search_params.show_complete)) ? ' checked="checked"' : ''} class="input_check aligned" /> {$T.pm_search_show_complete}</label><br>
              <label class="aligned" for="subject_only"><input type="checkbox" name="subject_only" id="subject_only" value="1"{(!empty($C.search_params.subject_only)) ? ' checked="checked"' : ''} class="input_check aligned" /> {$T.pm_search_subject_only}</label>
            </dd>
            <dt class="between">{$T.pm_search_post_age}:</dt>
            <dd>{$T.pm_search_between} <input type="text" name="minage" value="{(empty($C.search_params.minage)) ? '0' : $C.search_params.minage}" size="5" maxlength="5" class="input_text" />&nbsp;{$T.pm_search_between_and}&nbsp;<input type="text" name="maxage" value="{(empty($C.search_params.maxage)) ? '9999' : $C.search_params.maxage}" size="5" maxlength="5" class="input_text" /> {$T.pm_search_between_days}</dd>
          </dl>
          {if !$C.currently_using_labels}
            <input type="submit" name="submit" value="{$T.pm_search_go}" class="default floatright" />
          {/if}
          <br class="clear">
        </div>
      </fieldset>
      {if $C.currently_using_labels}
        {call collapser id='pmsearchlables' title=$T.pm_search_choose_label}
        <fieldset class="labels">
          <ul id="searchLabelsExpand" class="reset">
          {foreach $C.search_labels as $label}
            <li>
              <label for="searchlabel_{$label.id}"><input type="checkbox" id="searchlabel_{$label.id}" name="searchlabel[{$label.id}]" value="{$label.id}" {($label.checked) ? 'checked="checked"' : ''} class="input_check" />
              {$label.name}</label>
            </li>
          {/foreach}
          </ul>
          <p>
            <span class="floatleft"><input type="checkbox" name="all" id="check_all" value="" {($C.check_all) ? 'checked="checked"' : ''} onclick="invertAll(this, this.form, 'searchlabel');" class="input_check" /><em> <label for="check_all">{$T.check_all}</label></em></span>
            <input type="submit" name="submit" value="{$T.pm_search_go}" class="default floatright" />
          </p>
          <br class="clear">
        </fieldset>
        </div>
      {/if}
    {/if}
  </div>
</form>