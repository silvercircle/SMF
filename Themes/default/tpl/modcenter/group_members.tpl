{extends "modcenter/modcenter_base.tpl"}
{block modcenter_content}
{include "generics/list.tpl"}
{include "admin/group_list_members.tpl"}
{call show_list list_id=$C.default_list}
{/block}