{extends file="base.tpl"}
{block name='content'}
<div>
  {($C.smarty_template) ? FOO : BAR}
  {$C.smarty_template|cat:foo|cat:' what'}
  <link rel="stylesheet" type="text/css" href="{$S.primary_css}" />

  {include file=$C.footpl}
  {* variables test *}

  {if $C.testvar}
    {$C.testvar.foo}
  {/if}
</div>
{(isset($C.current_board)) ? 'is defined' : 'is not defined'}
{if !empty($C.current_board)}
{$astream_link = '<a data-board="'|cat:$C.current_board|cat:'" href="'|cat:$SCRIPTURL|cat:'?action=astream;sa=get;b='|cat:$C.current_board|cat:'">Recent activity</a>'}
{$astream_link}
{/if}
{$link = "foobar and "blubb" {$C.testvar.foo} the link"}
{$link}
{/block}