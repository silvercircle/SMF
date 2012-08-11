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
 * skeleton for the moderation center
 *}
{extends 'base.tpl'}
{block content}
{include 'generics/menu_above.tpl'}
{include 'pm/above.tpl'}
{if isset($C._template_include)}
	{include $C._template_include|cat:'.tpl'}
{/if}
{block 'pm_content'}
{/block}
{include 'pm/below.tpl'}
{include 'generics/menu_below.tpl'}
{/block}