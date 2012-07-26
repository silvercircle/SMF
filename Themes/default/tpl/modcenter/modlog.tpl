{extends "modcenter/modcenter_base.tpl"}
{block modcenter_content}
{include "generic_list.tpl"}
{call show_list list_id=$C.default_list}
{/block}