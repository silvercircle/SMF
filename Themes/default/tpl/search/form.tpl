{extends "base.tpl"}
{block content}
{include "generics/boardlisting.tpl"}
<form action="{$SCRIPTURL}?action=search2" method="post" accept-charset="UTF-8" name="searchform" id="searchform">
  <div class="bigheader">
    {if isset($M.search_index) and $M.search_index == 'sphinx'}
      <div class="floatright">
        Powered by: <a href="http://sphinxsearch.com"><img src="{$S.images_url}/theme/sphinx.jpg" alt="sphinxlogo" style="vertical-align:middle;" /></a>
      </div>
    {/if}
    <span class="ie6_header floatleft">{$T.set_parameters}</span>
    <div class="clear"></div>
  </div>
  <div class="blue_container">
  {if !empty($C.search_errors)}
    <p id="search_error" class="error">{'<br />'|implode:$C.search_errors.messages}</p>
  {/if}
  {if isset($C.simple_search) and !empty($C.simple_search)}
    <fieldset id="simple_search">
      <div>
        <div id="search_term_input">
          <strong>{$T.search_for}:</strong>
          <input type="text" name="search"{(!empty($C.search_params.search)) ? (' value="'|cat:$C.search_params.search|cat:'"') : ''} maxlength="{$C.search_string_limit}" size="40" class="input_text" />
            {(isset($C.require_verification) and !empty($C.require_verification)) ? '' : ('&nbsp;<input type="submit" name="submit" value="'|cat:$T.search|cat:'" class="button_submit" />')}
        </div>
        {if empty($M.search_simple_fulltext)}
          <p class="smalltext">{$T.search_example}</p>
        {/if}
        {if $C.require_verification}
        <div class="verification">
          <strong>{$T.search_visual_verification_label}:</strong>
          <br>
          {$SUPPORT->template_control_verification($C.visual_verification_id, 'all')}
          <br>
          <input id="submit" type="submit" name="submit" value="{$T.search}" class="button_submit" />
        </div>
        {/if}
        <a href="{$SCRIPTURL}?action=search;advanced" onclick="this.href += ';search=' + escape(document.forms.searchform.search.value);">{$T.search_advanced}</a>
        <input type="hidden" name="advanced" value="0" />
      </div>
      <span class="lowerframe"><span></span></span>
    </fieldset>
  {else}
    <fieldset id="advanced_search">
      <div>
        <input type="hidden" name="advanced" value="1" />
        <span class="enhanced">
          <strong>{$T.search_for}:</strong>
          <input type="text" name="search"{(!empty($C.search_params.search)) ? (' value="'|cat:$C.search_params.search|cat:'"') : ''} maxlength="{$C.search_string_limit}" size="40" class="input_text" />
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
            <option value="1"{(empty($C.search_params.searchtype)) ? ' selected="selected"' : ''}>{$T.all_words}</option>
            <option value="2"{(!empty($C.search_params.searchtype)) ? ' selected="selected"' : ''}>{$T.any_words}</option>
          </select>
        </span>
        {if empty($M.search_simple_fulltext)}
          <em class="smalltext">{$T.search_example}</em>
        {/if}
        <dl id="search_options">
          <dt>{$T.by_user}:</dt>
          <dd><input id="userspec" type="text" name="userspec" value="{(empty($C.search_params.userspec)) ? '*' : $C.search_params.userspec}" size="40" class="input_text" /></dd>
          <dt>{$T.search_order}:</dt>
          <dd>
            <select id="sort" name="sort">
              <option value="relevance|desc">{$T.search_orderby_relevant_first}</option>
              <option value="num_replies|desc">{$T.search_orderby_large_first}</option>
              <option value="num_replies|asc">{$T.search_orderby_small_first}</option>
              <option value="id_msg|desc">{$T.search_orderby_recent_first}</option>
              <option value="id_msg|asc">{$T.search_orderby_old_first}</option>
            </select>
          </dd>
          <dt class="options">{$T.search_options}:</dt>
          <dd class="options">
            <label class="aligned" for="show_complete1"><input type="checkbox" name="show_complete" id="show_complete1" value="1"{(!empty($C.search_params.show_complete)) ? ' checked="checked"' : ''} class="input_check aligned" /> {$T.search_show_complete_messages}</label><br />
            <label class="aligned" for="subject_only1"><input type="checkbox" name="subject_only" id="subject_only1" value="1"{(!empty($C.search_params.subject_only)) ? ' checked="checked"' : ''} class="input_check aligned" /> {$T.search_subject_only}</label>
          </dd>
          <dt class="between">{$T.search_post_age}: </dt>
          <dd>{$T.search_between} <input type="text" name="minage" value="{(empty($C.search_params.minage)) ? '0' : $C.search_params.minage}" size="5" maxlength="4" class="input_text" />&nbsp;{$T.search_and}&nbsp;<input type="text" name="maxage" value="{(empty($C.search_params.maxage)) ? '9999' : $C.search_params.maxage}" size="5" maxlength="4" class="input_text" /> {$T.days_word}</dd>
        </dl>
        {if $C.require_verification}
          <p>
            <strong>{$T.verification}:</strong>
            {$SUPPORT->template_control_verification($C.visual_verification_id, 'all')}
          </p>
        {/if}
        {if !empty($C.search_params.topic)}
          <p>{$T.search_specific_topic} &quot;{$C.search_topic.link}&quot;.</p>
          <input type="hidden" name="topic" value="{$C.search_topic.id}" />
        {/if}
        </div>
      </fieldset>
      {if empty($C.search_params.topic)}
          {call collapser id='search_boards' title=$T.choose_board bodyclass='flat_container'}
          <fieldset class="flow_hidden">
            <div>
              <div class="flow_auto" id="searchBoardsExpand">
                {call boardlisting prefix='brd'}
              </div>
              <br class="clear" />
              <div class="padding">
                <input type="checkbox" name="all" id="check_all" value=""{($C.boards_check_all) ? ' checked="checked"' : ''} onclick="invertAll(this, this.form, 'brd');" class="input_check floatleft" />
                <label for="check_all" class="floatleft">{$T.check_all}</label>
              </div>
              <br class="clear" />
            </div>
          </fieldset>
        </div>
      {/if}
  {/if}
  <input style="margin-top:5px;" type="submit" name="submit" value="{$T.search}" class="button_submit floatright" />
  <div class="clear"></div>
  </div>
</form>
<script type="text/javascript">
<!-- // --><![CDATA[
  function selectBoards(ids)
  {
    var toggle = true;

    for (i = 0; i < ids.length; i++)
      toggle = toggle & document.forms.searchform["brd" + ids[i]].checked;

    for (i = 0; i < ids.length; i++)
      document.forms.searchform["brd" + ids[i]].checked = !toggle;
  }

  function expandCollapseBoards()
  {
    $("#searchBoardsExpand").toggle();
    $("#expandBoardsIcon").attr("src", smf_images_url + ($("#searchBoardsExpand").is(":visible") ? "/collapse.gif" : "/expand.gif"));
  }
// ]]>
</script>
{/block}