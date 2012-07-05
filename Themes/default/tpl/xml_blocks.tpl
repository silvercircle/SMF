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
  function _h_dismiss_news_item(content)
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
  function _h_ratings_return(content)
  {
    result = $.parseJSON(content);
    if(result['mid'] > 0) {
      $('span#likers_msg_' + result['mid']).html(result['output']);
      if(result['likebar'].length > 0)
        $('span[data-likebarid="' + result['mid'] + '"]').html(result['likebar']);
    }
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
        <strong>{$like.memberlink}</strong>
        <br>
        Ratings: +0/0/-0
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

{foreach from=$C.template_functions item=fn}
{call name=$fn}
{/foreach}