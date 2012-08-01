<?xml version="1.0" encoding="UTF-8" ?>
{*
 * @name      EosAlpha BBS
 * @copyright 2011 Alex Vie silvercircle(AT)gmail(DOT)com
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright: 2011 Simple Machines (http://www.simplemachines.org)
 * license:   BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version 1.0pre
 *
 * small template blocks for ajax response handlers
 *}
{function help_popup}
  <div class="blue_container norounded content smalltext mediumpadding">
    {$C.help_text}<br>
  </div>
{/function}
{* output the "who posted" information box *}
{function who_posted_xml}
<document>
 <response open="default_overlay" width="auto" offset="-150" />
 <content>
 <![CDATA[ <!-- > -->
  <div class="title_bar">
    <h1>{$T.who_posted}</h1>
  </div>
  <div class="blue_container mediumpadding mediummargin" style="width:200px;">
    <dl class="common">
      <dt class="red_container lefttext"><strong>&nbsp;&nbsp;{$T.who_member}&nbsp;&nbsp;</strong></dt>
      <dd class="red_container righttext"><strong>&nbsp;&nbsp;{$T.posts}&nbsp;&nbsp;</strong></dd>
      {foreach from=$C.posters item=poster}
        <dt class="lefttext">{$poster.real_name}</dt>
        <dd class="righttext">{$poster.count}</dd>
      {/foreach}
    </dl>
  </div>
  ]]>
 </content>
</document>
{/function}

{function dismiss_newsitem}
{* xml response for action=dismissnews 
 # content will have the item id to remove, <handler> is the script that does
 # DOM manipulation *}
<document>
  <response open="private_handler" fn="_h_dismiss_news_item" />
  <content>
    <![CDATA[
      {$C.item_to_dismiss}
    ]]>
  </content>
  <handler>
  <![CDATA[
  function _h_dismiss_news_item(content, data)
  {
    var result = $.parseJSON(content);
    var id = parseInt(result['id']) || 0;
    if(id) {
      if($('li#newsitem_' + id).length) {
        $('li#newsitem_' + id).fadeOut();
        $('li#newsitem_' + id).removeClass('visible');
      }
      if($('#newsitem_list').children('li.visible').length == 0)
        $('#newsitem_container').fadeOut();
    }
  }
  ]]>
  </handler>
</document>
{/function}
{function rating_response}
<document>
  <response open="private_handler" fn="_h_ratings_return" />
  <content>
    <![CDATA[
    {$C.postratings}
    ]]>
  </content>
  <handler>
  <![CDATA[
  function _h_ratings_return(content, data)
  {
    result = $.parseJSON(content);
    if(result['mid'] > 0) {
      $('span#likers_msg_' + result['mid']).html(result['output']);
      if(result['likebar'].length > 0)
        $('span[data-likebarid="' + result['mid'] + '"]').html(result['likebar']);
    }
    if($('#ratingwidget').length > 0)
      $('#ratingwidget').remove();

    // refresh event handlers for changed content
    $('.givelike').click(function() {
      giveLike($(this));
      return(false);
    });
    $('span.ratings span.number').click(function() {
      sendRequest('action=like;sa=getlikes;m=' + parseInt($(this).parent().attr('data-mid'))  + ';r=' + parseInt($(this).attr('data-rtype')), null);
      return(false);
    });
  }
  ]]>
  </handler>
</document>
{/function}

{function getlikes_by_type}
<document>
  <response open="default_overlay" width="350" offset="-150" />
  <content>
    <![CDATA[ <!-- > -->
    <div class="flat_container">
    {if !empty($C.likes)}
    <div class="glass centertext" style="font-size:1.1em;">
      {$C.rating_title}
    </div>
    <ol class="commonlist">
      {foreach $C.likes as $like}
      <li>
        <span class="floatright">{$like.dateline}</span>
        <strong>{$like.memberlink}</strong> <span class="tinytext lowcontrast">(-0/0/+0)</span>
        <br>
        {if !empty($like.comment)}
          <span class="tinytext"><span class="lowcontrast">Comment:</span> {$like.comment}</span>
        {/if}
      </li>
      {/foreach}
    </ol>
    {else}
    {/if}
    </div>
    ]]>
  </content>
</document>
{/function}
{function ratingwidget}
<document>
  <response open="private_handler" fn="_create_rating_widget" />
  <content>
    <![CDATA[ <!-- > -->
      <div class="blue_container light_shadow blue_topbar" id="ratingwidget" data-id="{$C.content_id}" style="position:absolute;right:0;float:right;z-index:9999;min-width:170px;">
        <h1 class="bigheader secondary">{$T.rate_this_post}</h1>
        <div class="tinypadding smalltext">
        {if $C.result_count}
          <ol class="commonlist" style="margin-bottom:0;">
          {foreach $C.ratings as $rating}
            <li>
              {if $rating.unique}
                <input class="rw_option aligned" name="RW" id="rtype_{$rating.rtype}" value="{$rating.rtype}" type="radio" /><label for="rtype_{$rating.rtype}" class="aligned">{$rating.label}</label>
              {else}
                <input class="rw_option aligned" name="RW" id="rtype_{$rating.rtype}" value="{$rating.rtype}" type="checkbox" /><label for="rtype_{$rating.rtype}" class="aligned">{$rating.label}</label>
              {/if}
            </li>
          {/foreach}
          </ol>
        {else}
          <div class="red_container norounded">
            No rating types available
          </div>
        {/if}
        {if $C.result_count > 0 and !empty($M.rating_allow_comments)}
          <hr>
          <div class="centertext tinytext" style="line-height:110%;">
            <div class="lefttext" style="margin-bottom:4px;">{$T.rating_attach_comment}</div>
            <input type="text" size="30" name="ratingcomment" id="ratingcomment" />
          </div>
        {/if}
        <div class="centertext smallpadding">
          {if $C.result_count}
            <span class="button default centered" onclick="ratingwidget_submit();return(false);">Submit</span>&nbsp;&nbsp;&nbsp;
          {/if}
          <span class="button centered" onclick="$('#ratingwidget').remove();return(false);">{$T.find_close}</span>
          <div class="clear"></div>
        </div>
      </div>
      </div>
    ]]>
  </content>
  <data>
    {$C.json_data}
  </data>
  <handler>
    <![CDATA[ <!-- 
    function _create_rating_widget(content, data)
    {
      var _el = $(content);
      window._data = data;
      if($('#ratingwidget').length > 0)
        $('#ratingwidget').remove();
      $('span[data-likebarid=' + data['id'] + ']').after(_el);
      $('#ratingwidget').on('mouseleave', function(event) {
        $('#ratingwidget').remove();
      });
      return(false);
    }
    function ratingwidget_submit()
    {
      var done = false;
      var rtypes = new Array();
      var i = 0;
      $('#ratingwidget input.rw_option').each(function() {
        if($(this).is(':checked')) {
          rtypes[i++] = parseInt($(this).val());
          done = true;
        }
      });
      if(done) {
        var comment = $('#ratingcomment').val().length > 0 ? ';comment=' + encodeURIComponent($('#ratingcomment').val()) : '';
        var uri = 'action=xmlhttp;sa=givelike;r=' + rtypes.join(',') + ';m=' + parseInt($('#ratingwidget').attr('data-id')) + comment;
        sendRequest(uri, null);
        $('#ratingwidget').remove();
        return(false);
      }
      Eos_Alert('Error', window._data['error_text']);
    }
    ]]>
  </handler>
</document>
{/function}
{foreach from=$C.template_functions item=fn}
{call name=$fn}
{/foreach}