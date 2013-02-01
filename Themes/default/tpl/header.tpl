{**
 * @name      EosAlpha BBS
 * @copyright 2011 Alex Vie silvercircle(AT)gmail(DOT)com
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright:	2011 Simple Machines (http://www.simplemachines.org)
 * license:  	BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version 1.0pre
 *
 * the header area (the topmost part, above the menu bar)
 *}
  <div id="upper_section" class="smalltext">
    <div class="floatleft" style="overflow:hidden;max-height:90px;"><img src="{$S.images_url}/logo.png" alt="logo" /></div>
    {* don't remove this, plugins may use this hook*}
    {$SUPPORT->displayHook('header_area')}
    <div class="clear"></div>
  </div>
