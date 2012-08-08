{extends "base.tpl"}
{block content}
<div id="announcement">
  <form action="{$SCRIPTURL}?action=announce;sa=send" method="post" accept-charset="UTF-8">
    <div class="cat_bar">
      <h3>{$T.announce_title}</h3>
    </div>
    <div class="orange_container cleantop mediumpadding">
      {$T.announce_desc}
    </div>
    <br>
    <div class="blue_container">
      <div class="content">
        <h1 class="bigheader secondary">
          {$T.announce_this_topic} <a href="{$SUPPORT->url_parse($SCRIPTURL|cat:'?topic='|cat:$C.current_topic|cat:'.0')}">{$C.topic_subject}</a>
        </h1>
        <ul class="reset">
        {foreach $C.groups as $group}
          <li>
            <label for="who_{$group.id}"><input type="checkbox" name="who[{$group.id}]" id="who_{$group.id}" value="{$group.id}" checked="checked" class="input_check" /> {$group.name}</label> <em>({$group.member_count})</em>
          </li>
        {/foreach}
        <li>
          <label for="checkall"><input type="checkbox" id="checkall" class="input_check" onclick="invertAll(this, this.form);" checked="checked" /> <em>{$T.check_all}</em></label>
        </li>
        </ul>
        <div id="confirm_buttons">
            <input type="submit" value="{$T.post}" class="default" />
            {$C.hidden_sid_input}
            <input type="hidden" name="topic" value="{$C.current_topic}" />
            <input type="hidden" name="move" value="{$C.move}" />
            <input type="hidden" name="goback" value="{$C.go_back}" />
          </div>
        </div>
      </div>
    </form>
  </div>
  <br>
{/block}