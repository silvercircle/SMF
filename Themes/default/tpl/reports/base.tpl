{extends "base.tpl"}
{block content}
{include "generics.tpl"}
{include 'generics/menu_above.tpl'}
{$SUPPORT->displayHook('reports_content_area')}
{include 'generics/menu_below.tpl'}
{/block}