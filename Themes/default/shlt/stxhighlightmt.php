<?php
/*
    Copyright (C) 2009  Megatome Technologies

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
/*
Plugin Name: Syntax Highlighter MT
Plugin URI: http://www.megatome.com/syntaxhighlighter
Description: Provides a simple way to use the Syntax Highlighter tool from <a href="http://alexgorbatchev.com/wiki/SyntaxHighlighter">http://alexgorbatchev.com/wiki/SyntaxHighlighter</a>
Version: 2.0
Author: Chad Johnston
Author URI: http://www.megatome.com
*/

function mtsh_write_head() {
    $x = WP_PLUGIN_URL.'/'.str_replace("/" . basename( __FILE__),"",plugin_basename(__FILE__));
    echo "<script type='text/javascript' src='$x/scripts/shCore.js'></script>\n";
    echo "<script type='text/javascript' src='$x/scripts/shAutoloader.js'></script>\n";
    echo "";
	echo "<link type='text/css' rel='stylesheet' href='$x/styles/shCore.css'/>\n";
	echo "<link type='text/css' rel='stylesheet' href='$x/styles/shThemeDefault.css'/>\n";
}
add_action('wp_head', 'mtsh_write_head');

function mtsh_write_footer() {
    $x = WP_PLUGIN_URL.'/'.str_replace("/" . basename( __FILE__),"",plugin_basename(__FILE__));
	echo "<script type='text/javascript'>\n";
    echo "  SyntaxHighlighter.autoloader(
      'applescript            $x/scripts/shBrushAppleScript.js',
      'actionscript3 as3      $x/scripts/shBrushAS3.js',
      'bash shell             $x/scripts/shBrushBash.js',
      'coldfusion cf          $x/scripts/shBrushColdFusion.js',
      'cpp c                  $x/scripts/shBrushCpp.js',
      'c# c-sharp csharp      $x/scripts/shBrushCSharp.js',
      'css                    $x/scripts/shBrushCss.js',
      'delphi pascal          $x/scripts/shBrushDelphi.js',
      'diff patch pas         $x/scripts/shBrushDiff.js',
      'erl erlang             $x/scripts/shBrushErlang.js',
      'groovy                 $x/scripts/shBrushGroovy.js',
      'java                   $x/scripts/shBrushJava.js',
      'jfx javafx             $x/scripts/shBrushJavaFX.js',
      'js jscript javascript  $x/scripts/shBrushJScript.js',
      'perl pl                $x/scripts/shBrushPerl.js',
      'php                    $x/scripts/shBrushPhp.js',
      'text plain             $x/scripts/shBrushPlain.js',
      'py python              $x/scripts/shBrushPython.js',
      'ruby rails ror rb      $x/scripts/shBrushRuby.js',
      'sass scss              $x/scripts/shBrushSass.js',
      'scala                  $x/scripts/shBrushScala.js',
      'sql                    $x/scripts/shBrushSql.js',
      'vb vbnet               $x/scripts/shBrushVb.js',
      'xml xhtml xslt html    $x/scripts/shBrushXml.js'
       );\n";
	echo "	SyntaxHighlighter.all();\n";
	echo "</script>\n";
}
add_action('wp_footer', 'mtsh_write_footer');

?>