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

function gitfeed_autoloader()
{
	return new GitFeed();
}

class GitFeed extends EoS_Plugin
{
	protected $productShortName = 'gitfeed';		// mandatory. should only contain letters and numbers, no special chars. This is the internal plugin identifier.
	
	protected $_product = array(
		'Version' => '0.1',
		'Name' => 'GitFeed',
		'Description' => 'A simple plugin to implement a github feed in the sidebar.'
	);

	protected $installableHooks = array(
		'boardindex' => array('file' => 'main.php', 'callable' => 'GitFeed::boardindex'),
		'menu_buttons' => array('file' => 'main.php', 'callable' => 'GitFeed::menuextend'),
	);

	/*
	 * these should be options
	 * right now, the plugin is in "lazy mode", so no options. sorry.
	 */
	protected static $my_git_url = 'https://github.com/silvercircle/SMF/';				// frontend base url
	protected static $my_git_api_url = 'https://api.github.com/repos/silvercircle/SMF/commits'; // this is where we fetch our json data
	protected $removeableHooks = array();

	public function __construct() { parent::__construct(); }	// mandatory

	public static function menuextend(&$menu_buttons, &$usermenu_buttons)
	{
		$menu_buttons['wiki'] = array(
			'title' => 'Wiki',
			'href' => 'http://eos.miranda.or.at/wiki/',
			'show' => true,
			'is_last' => true,
		);
	}
	/*
	 * runs in board index
	 * 1) fetch our data (we cache this for a while...)
	 * 2) build $context['gitfeed'] array with the last 5 commits
	 */
	public static function boardindex()
	{
		global $context, $txt, $sourcedir, $user_info;
		$data = array();

		if(($data = CacheAPI::getCache('github-feed-index', 1800)) === null) {
			$f = curl_init();
			if($f) {
				curl_setopt_array($f, array(CURLOPT_URL => self::$my_git_api_url,
					CURLOPT_HEADER => false,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_SSL_VERIFYPEER => false
				));
				$json_response = curl_exec($f);
				$data = json_decode($json_response, true);
				CacheAPI::putCache('github-feed-index', $data, 1800);
				curl_close($f);
			}
		}
		$n = 0;
		foreach($data as $commit) {
			$context['gitfeed'][] = array(
				'message_short' => shorten_subject($commit['commit']['message'], 60),
				'message' => nl2br($commit['commit']['message']),		// for the tool tip
				'dateline' => timeformat(strtotime($commit['commit']['committer']['date'])),
				'sha' => $commit['sha'],
				'href' => self::$my_git_url . 'commit/'. $commit['sha']
			);
			if(++$n > 5)
				break;
		}
		if(!empty($data)) {
			/* 
			 * add our plugin directory to the list of directories to search for templates
			 * and register the template hook.
			 * only do this if we actually have something to display
			 */
			EoS_Smarty::addTemplateDir(dirname(__FILE__));
			EoS_Smarty::getConfigInstance()->registerHookTemplate('sidebar_below_userblock', 'gitfeed_sidebar_top');
			$context['gitfeed_global']['see_all']['href'] = self::$my_git_url . 'commits/master';
			$context['gitfeed_global']['see_all']['txt'] = 'Browse all commits';
		}
	}
}
?>