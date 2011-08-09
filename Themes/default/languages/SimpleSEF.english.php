<?php

/* * **** BEGIN LICENSE BLOCK *****
 * Version: MPL 1.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is http://code.mattzuba.com code.
 *
 * The Initial Developer of the Original Code is
 * Matt Zuba.
 * Portions created by the Initial Developer are Copyright (C) 2010-2011
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *
 * ***** END LICENSE BLOCK ***** */

$txt['simplesef'] = 'SimpleSEF';
$txt['simplesef_desc'] = 'This section allows you to edit the options for SimpleSEF.<br /><br />
<strong>Note: If you enable this and start receiving 404 errors on your board, it is likely because .htaccess or web.config was not created, or your host does not have the mod_rewrite or Microsoft Url Rewrite module installed on the web server and you will not be able to use this mod.</strong> [<a href="#" onclick="showSimpleSEFHelp(); return false;">Help</a>]
<span style="display:block;" id="simplesef_help">If you have an Apache webserver, or one that uses .htaccess and has mod_rewrite functionality, you need a .htaccess file in your main SMF directory with the following:
<span style="display:block;" class="codeheader">Code:</span>
<code>RewriteEngine On<br /># Uncomment the following line if its not working right<br /># RewriteBase /<br />RewriteCond %{REQUEST_FILENAME} !-f<br />RewriteCond %{REQUEST_FILENAME} !-d<br />RewriteRule ^(.*)$ index.php?q=$1 [L,QSA]</code>
<br />
If you have a IIS7 webserver, you need a web.config file in your main SMF directory with the following:
<span style="display:block;" class="codeheader">Code:</span>
<code>&lt;?xml version=&quot;1.0&quot; encoding=&quot;UTF-8&quot;?&gt;<br />&lt;configuration&gt;<br />    &lt;system.webServer&gt;<br />        &lt;rewrite&gt;<br />            &lt;rules&gt;<br />                &lt;rule name=&quot;SimpleSEF&quot; stopProcessing=&quot;true&quot;&gt;<br />                    &lt;match url=&quot;^(.*)$&quot; ignoreCase=&quot;false&quot; /&gt;<br />                    &lt;conditions logicalGrouping=&quot;MatchAll&quot;&gt;<br />                        &lt;add input=&quot;{REQUEST_FILENAME}&quot; matchType=&quot;IsFile&quot; negate=&quot;true&quot; pattern=&quot;&quot; ignoreCase=&quot;false&quot; /&gt;<br />                        &lt;add input=&quot;{REQUEST_FILENAME}&quot; matchType=&quot;IsDirectory&quot; negate=&quot;true&quot; pattern=&quot;&quot; ignoreCase=&quot;false&quot; /&gt;<br />                    &lt;/conditions&gt;<br />                    &lt;action type=&quot;Rewrite&quot; url=&quot;index.php?q={R:1}&quot; appendQueryString=&quot;true&quot; /&gt;<br />                &lt;/rule&gt;<br />            &lt;/rules&gt;<br />        &lt;/rewrite&gt;<br />    &lt;/system.webServer&gt;<br />&lt;/configuration&gt;</code>
<br />
If you have Lighttpd v1.4.23 or less, you will need the following in your Lighttpd config file, normally at /etc/lighttpd/lighttpd.conf (thanks to <a href="http://www.simplemachines.org/community/index.php?action=profile;u=9547">Daniel15</a>).
<code>$HTTP[&quot;host&quot;] =~ &quot;(www.)?example.com&quot; {<br />   url.rewrite-final += (<br />      # Allow all normal files<br />      &quot;^/forum/.*\.(js|ico|gif|jpg|png|swf|css|htm|php)(\?.*)?$&quot; =&gt; &quot;$0&quot;,<br />      # Rewrite everything else<br />      &quot;^/([^.?]*)$&quot; =&gt; &quot;/index.php?q=$1&quot;<br />   )<br />}</code>
<br />
You can also make this work with Nginx with the following code added to your Nginx configuration file.
<code>if (!-e $request_filename) {<br />    rewrite ^/(.*)$ /index.php?q=$1 last;<br />}</code>
</span>
<script type="text/javascript"><!-- // --><!' . '[CDATA[
document.getElementById("simplesef_help").style.display = "none";
function showSimpleSEFHelp()
{
	document.getElementById("simplesef_help").style.display = "block";
}
// ]]' . '></script>';
$txt['simplesef_basic'] = 'Basic Options';
$txt['simplesef_enable'] = 'Enable SimpleSEF';
$txt['simplesef_enable_desc'] = 'Requires mod_rewrite support or Url Rewrite/web.config (IIS7) support.';
$txt['simplesef_simple'] = 'Create Simple URLs';
$txt['simplesef_simple_desc'] = 'Urls will look like /forum/board-1/, or /forum/topic-3.html instead of content filled.';
$txt['simplesef_space'] = 'Space';
$txt['simplesef_space_desc'] = 'Character to be used for spaces in the url.  Typically _ or -.';
$txt['simplesef_suffix'] = 'Topic Extension';
$txt['simplesef_suffix_desc'] = 'URL extension to use on topics (ie: html, php).';
$txt['simplesef_suffix_required'] = 'An extension is required';
$txt['simplesef_strip_words'] = 'Words to strip';
$txt['simplesef_strip_words_desc'] = 'These are words that will be stripped out of urls.  This creates shorter, yet still readable urls.  The words you wish to strip should be seperated by a comma (no spaces).';
$txt['simplesef_strip_chars'] = 'Characters to strip';
$txt['simplesef_strip_chars_desc'] = 'These are characters that will be stripped out of urls.  This creates shorter, yet still readable urls.  The characters you wish to strip should be seperated by a comma (no spaces).';
$txt['simplesef_lowercase'] = 'Lowercase URLs';
$txt['simplesef_lowercase_desc'] = 'Use this option to convert all urls to lower case letters.';
$txt['simplesef_action_title'] = 'Actions and User Actions';
$txt['simplesef_action_desc'] = 'These are all of the actions of the board.  You normally do not need to edit this list.  Infact, if you do edit these lists, it can cause parts of your board not to function temporarily.  These are only provided to be edited if you are directed to do so.  [<a href="#" onclick="return editAreas();">Edit</a>]';
$txt['simplesef_actions'] = 'Actions';
$txt['simplesef_useractions'] = 'User Actions';
$txt['simplesef_adds'] = '<a href="http://code.mattzuba.com/simplesef">SimpleSEF</a> added';
$txt['simplesef_404'] = 'The page you requested could not be found.  Please contact the site administrator if you believe you have reached this page in error.';
$txt['simplesef_advanced'] = 'Advanced Options';
$txt['simplesef_advanced_desc'] = 'Enable action aliasing, action ignoring and other advanced options.';
$txt['simplesef_alias'] = 'Action Aliasing';
$txt['simplesef_alias_desc'] = 'Action Aliasing your actions allows you to change the name of an action without adversly affecting your board.  For example, a Spanish language community may have a portal installed and will want to change the \'forum\' action to \'foro\'.  This section makes it possible.  Each action can only have <strong>one</strong> alias and if you create an alias that is already an action, the action will take precedence, not the alias.';
$txt['simplesef_alias_clickadd'] = 'Add another alias';
$txt['simplesef_alias_detail'] = 'Enter the original action on the left, and what to change it to on the right';
$txt['simplesef_ignore'] = 'Ignore Actions';
$txt['simplesef_ignore_desc'] = 'Move actions you want to ignore to the box on the right';
$txt['simplesef_debug'] = 'Enable debug mode';
$txt['simplesef_debug_desc'] = 'This option will enable debugging on your board.  This does not output any specific information to the screen, but will rapidly fill up your error log with debugging statements from SimpleSEF.  This should really only be used if the author requests it when seeking support';