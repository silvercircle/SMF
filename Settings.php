<?php
/******************************************************************************
* Settings.php                                                                *
*******************************************************************************
* SMF: Simple Machines Forum                                                  *
* Open-Source Project Inspired by Zef Hemel (zef@zefhemel.com)                *
* =========================================================================== *
* Software Version:           SMF 1.0 RC2                                     *
* Software by:                Simple Machines (http://www.simplemachines.org) *
* Copyright 2001-2004 by:     Lewis Media (http://www.lewismedia.com)         *
* Support, News, Updates at:  http://www.simplemachines.org                   *
*******************************************************************************
* This program is free software; you may redistribute it and/or modify it     *
* under the terms of the provided license as published by Lewis Media.        *
*                                                                             *
* This program is distributed in the hope that it is and will be useful,      *
* but WITHOUT ANY WARRANTIES; without even any implied warranty of            *
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                        *
*                                                                             *
* See the "license.txt" file for details of the Simple Machines license.      *
* The latest version can always be found at http://www.simplemachines.org.    *
******************************************************************************/

########## Maintenance ##########
# Note: If $maintenance is set to 2, the forum will be unusable!  Change it to 0 to fix it.
$maintenance = 0;		# Set to 1 to enable Maintenance Mode, 2 to make the forum untouchable. (you'll have to make it 0 again manually!)
$mtitle = 'Maintenance Mode';		# Title for the Maintenance Mode message.
$mmessage = 'Offline for a short maintainance period.';		# Description of why the forum is in maintenance mode.

########## Forum Info ##########
$mbname = 'TabSRMM';		# The name of your forum.
$language = 'english';		# The default language file set for the forum.
$boardurl = 'http://forum.miranda.or.at';		# URL to your forum's folder. (without the trailing /!)
$webmaster_email = '';		# Email address to send emails from. (like noreply@yourdomain.com.)
$cookiename = 'SMFCookie20';		# Name of the cookie to set for authentication.

########## Database Info ##########
$db_server = 'localhost';
$db_name = '';
$db_user = '';
$db_passwd = '';
$db_prefix = 'smf_';
$db_persist = 0;
$db_error_send = 0;

########## Directories/Files ##########
# Note: These directories do not have to be changed unless you move things.
$boarddir = '/home/alex/www/forum/';		# The absolute path to the forum's folder. (not just '.'!)
$sourcedir = '/home/alex/www/forum/Sources/';		# Path to the Sources directory.

########## Error-Catching ##########
# Note: You shouldn't touch these settings.

# Make sure the paths are correct... at least try to fix them.
if (!file_exists($boarddir) && file_exists(dirname(__FILE__) . '/agreement.txt'))
$boarddir = '';
if (!file_exists($sourcedir) && file_exists($boarddir . '/Sources'))
$sourcedir = '';

$ssi_db_user = '';
$cachedir = '';
$db_character_set = 'utf8';
$db_show_debug = false;
$g_disable_all_hooks = false;
//$db_no_admin_security = 0;
?>