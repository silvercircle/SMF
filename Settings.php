<?php
/**
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
 * Settings.php template
 */

########## Maintenance ##########
# Note: If $maintenance is set to 2, the forum will be unusable!  Change it to 0 to fix it.
$maintenance = 0;		# Set to 1 to enable Maintenance Mode, 2 to make the forum untouchable. (you'll have to make it 0 again manually!)
$mtitle = 'Maintenance Mode';		# Title for the Maintenance Mode message.
$mmessage = 'Offline for a short maintainance period.';		# Description of why the forum is in maintenance mode.

########## Forum Info ##########
$mbname = 'My Community';  # The name of your forum.
$language = 'english';  # The default language file set for the forum.
$boardurl = 'http://127.0.0.1/smf';  # URL to your forum's folder. (without the trailing /!)
$webmaster_email = 'noreply@myserver.com';  # Email address to send emails from. (like noreply@yourdomain.com.)
$cookiename = 'SMFCookie20';  # Name of the cookie to set for authentication.
########## Database Info ##########
$db_type = 'mysql';
$db_server = 'localhost';
$db_name = 'smf';
$db_user = 'root';
$db_passwd = '';
$ssi_db_user = '';
$ssi_db_passwd = '';
$db_prefix = 'smf_';
$db_persist = 0;
$db_error_send = 1;

########## Directories/Files ##########
# Note: These directories do not have to be changed unless you move things.
$boarddir = dirname(__FILE__);  # The absolute path to the forum's folder. (not just '.'!)
$sourcedir = dirname(__FILE__) . '/Sources';  # Path to the Sources directory.
$cachedir = dirname(__FILE__) . '/cache';  # Path to the cache directory.
########## Error-Catching ##########
# Note: You shouldn't touch these settings.
$backend_subdir = 'backend';
$db_last_error = 0;

$ssi_db_user = '';
$db_character_set = 'utf8';
$db_show_debug = false;
$theme_show_debug = false;
$g_disable_all_hooks = false;
$db_cache_api = 'file';
$db_cache_memcached = 'localhost:11211';
?>