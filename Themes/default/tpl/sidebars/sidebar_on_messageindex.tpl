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
 * side bar template for the message index. There is no default content,
 * because the side bar is not enabled for the message index by default, but there are
 * 2 hookable template areas (sidebar_top and sidebar_bottom) that can be
 * populated by plugins.
 *}
<h1 class="bigheader">&nbsp;</h1>
<br>
{$C.template_hooks.global.sidebar_top}
{$SUPPORT->displayHook('sidebar_top')}
<script>
// <![CDATA[
  sidebar_content_loaded = 1;
// ]]>
</script>
{$C.template_hooks.global.sidebar_bottom}
{$SUPPORT->displayHook('sidebar_bottom')}
