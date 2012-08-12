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
 * acts as the skeleton for profile pages. the actual profile page must
 * implement the block profile_content and extend profile/profile_base.tpl
 *}
{extends 'base.tpl'}
{block content}
{function savebutton}
<hr width="100%" size="1" class="hrcolor clear" />
{if $C.require_password}
  <dl>
  <dt>
    <strong{(isset($C.modify_error.bad_password) or isset($C.modify_error.no_password)) ? ' class="error"' : ''}>{$T.current_password}: </strong><br>
    <span class="smalltext">{$T.required_security_reasons}</span>
  </dt>
  <dd>
    <input type="password" name="oldpasswrd" size="20" style="margin-right: 4ex;" class="input_password" />
  </dd>
  </dl>
{/if}
<div class="righttext">
  <input type="submit" value="{$T.change_profile}" class="default" />
  {$C.hidden_sid_input}
  <input type="hidden" name="u" value="{$C.id_member}" />
  <input type="hidden" name="sa" value="{$C.menu_item_selected}" />
</div>
{/function}
{include 'generics/menu_above.tpl'}
{include 'profile/above.tpl'}
{$SUPPORT->displayHook('profile_content_area')}
{include 'profile/below.tpl'}
{include 'generics/menu_below.tpl'}
{/block}