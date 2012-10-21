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
 */

// this should always be there
if (!defined('EOSA'))
	die('No access');

function geoip_autoloader()
{
	return new EoS_GeoIP();
}

class EoS_GeoIP extends EoS_Plugin
{
	protected $productShortName = 'geoip';		// mandatory. should only contain letters and numbers, no special chars. This is the internal plugin identifier.
	
	protected $_product = array(
		'Version' => '0.1',
		'Name' => 'GeoIP',
		'Description' => 'A simple plugin to implement GeoIP lookup.'
	);

	protected $installableHooks = array(
		'load_userdata' => array('file' => 'main.php', 'callable' => 'EoS_GeoIP::load_data'),
		'track_ip' => array('file' => 'main.php', 'callable' => 'EoS_GeoIP::track_ip'),
	);

	/*
	 * these should be options
	 * right now, the plugin is in "lazy mode", so no options. sorry.
	 */
	protected $removeableHooks = array();

	public function __construct() { parent::__construct(); }	// mandatory

	public function canInstall(&$message = null)
	{
		if(is_callable('geoip_db_avail')) {
			if(geoip_db_avail(GEOIP_COUNTRY_EDITION) || geoip_db_avail(GEOIP_REGION_EDITION_REV0) || geoip_db_avail(GEOIP_CITY_EDITION_REV0) || geoip_db_avail(GEOIP_CITY_EDITION_REV1) || geoip_db_avail(GEOIP_REGION_EDITION_REV1))
				return true;
			else {
				$this->installError = 'GeoIP Database missing';
				return false;
			}
		}
		else {
			$this->installError = 'PECL GeoIP extension not avaialble';
			return false;
		}
	}
	/*
	 * runs in LoadUserSettings()
	 */
	public static function load_data(&$user_info, &$user_settings)
	{
		if(is_callable('geoip_db_avail') && !$user_info['is_admin']) {
			$region = geoip_record_by_name($user_info['ip']);
			//if($region['country_code'] === 'AT')
			//	die('Unspecified error');
		}
	}

	public static function track_ip()
	{
		global $context, $txt;

		if(is_callable('geoip_db_avail')) {
			$context['region_info'] = geoip_record_by_name($context['ip']);
			EoS_Smarty::addTemplateDir(dirname(__FILE__));
			EoS_Smarty::getConfigInstance()->registerHookTemplate('track_ip_top', 'geoip_trackip_top');
			$txt['geoip_header'] = 'Location information';
		}
	}
}
?>