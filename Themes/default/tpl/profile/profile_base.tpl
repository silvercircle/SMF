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
{include 'generic_menu_above.tpl'}
{include 'profile/above.tpl'}
{block 'profile_content'}
{/block}
{include 'profile/below.tpl'}
{include 'generic_menu_below.tpl'}
{/block}