{*#
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
 * display the software credits - template part
 *}
{extends 'base.tpl'}
{block content}
  <div class="main_section" id="credits">
    <div class="cat_bar2">
      <h3 class="catbg">{$T.credits}</h3>
    </div>
    <div class="blue_container cleantop">
     <div class="content">
      {$C.credits_intro}
     </div>
    </div>
    {foreach $C.credits as $section}
      {if !empty($section.pretext)}
        <br>
        <div class="blue_container">
          <div class="content">
            <p>{$section.pretext}</p>
          </div>
        </div>
      {/if}
      {if !empty($section.title)}
        <br>
        <div class="cat_bar">
          <h3 class="catbg">{$section.title}</h3>
        </div>
      {/if}
      <div class="blue_container cleantop">
        <div class="content">
          <dl>
            {foreach $section.groups as $group}
              {if !empty($group.title)}
                <dt>
                  <strong>{$group.title}</strong>
                </dt>
                <dd>
              {/if}
              {if count($group.members) <= 2}
                {" "|cat:$T.credits_and|cat:" "|implode:$group.members}
              {else}
                {", "|implode:$group.members} {$T.credits_and} {$group.last_peep}
              {/if}
                </dd>
            {/foreach}
          </dl>
          {if !empty($section.posttext)}
            <p class="posttext">{$section.posttext}</p>
          {/if}
        </div>
      </div>
  {/foreach}
  <br>
  <div class="cat_bar2">
    <h3 class="catbg">{$T.credits_copyright}</h3>
  </div>
  <div class="blue_container cleantop">
    <div class="content">
      <dl>
        <dt><strong>{$T.credits_forum}</strong></dt>
        <dd>{$C.copyrights.smf}</dd>
      </dl>
      {if !empty($C.copyrights.mods)}
        <dl>
          <dt><strong>{$T.credits_modifications}</strong></dt>
          <dd>{"</dd><dd>"|implode:$C.copyrights.mods}</dd>
        </dl>
      {/if}
    </div>
  </div>
</div>
{/block}