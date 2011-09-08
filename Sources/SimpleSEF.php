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

// No Direct Access!
if (!defined('SMF'))
    die('Hacking attempt...');

class SimpleSEF {

    /**
     * @var Tracks the added queries used during execution
     */
    private static $queryCount = 0;
    /**
     * @var array Tracks benchmarking information
     */
    private static $benchMark = array('total' => 0, 'marks' => array());
    /**
     * @var array All actions used in the forum (normally defined in index.php
     * 	but may come from custom action mod too)
     */
    private static $actions = array();
    /**
     * @var array All ignored actions used in the forum
     */
    private static $ignoreactions = array('admin', 'openidreturn');
    /**
     * @var array Actions that have aliases
     */
    private static $aliasactions = array();
    /**
     * @var array Actions that may have a 'u' or 'user' parameter in the URL
     */
    private static $useractions = array();
    /**
     * @var array Words to strip while encoding
     */
    private static $stripWords = array();
    /**
     * @var array Characters to strip while encoding
     */
    private static $stripChars = array();
    /**
     * @var array Stores boards found in the output after a database query
     */
    private static $boardNames = array();
    /**
     * @var array Stores topics found in the output after a database query
     */
    private static $topicNames = array();
    /**
     * @var array Stores usernames found in the output after a database query
     */
    private static $userNames = array();
    /**
     * @var array Tracks the available extensions
     */
    private static $extensions = array();
    /**
     * @var bool Properly track redirects
     */
    private static $redirect = FALSE;

    /**
     * Initialize the mod and it's settings.  We can't use a constructor
     * might change this in the future (either singleton or two classes,
     * one to handle the integration hooks and one that does the dirty work)
     *
     * @global array $modSettings SMF's modSettings variable
     * @staticvar boolean $done Says if this has been done already
     * @param boolean $force Force the init to run again if already done
     * @return void
     */
    public static function init($force = FALSE) {
        global $modSettings;
        static $done = FALSE;

        if ($done && !$force)
            return;
        $done = TRUE;

        self::$actions = !empty($modSettings['simplesef_actions']) ? explode(',', $modSettings['simplesef_actions']) : array();
        self::$ignoreactions = array_merge(self::$ignoreactions, !empty($modSettings['simplesef_ignore_actions']) ? explode(',', $modSettings['simplesef_ignore_actions']) : array());
        self::$aliasactions = !empty($modSettings['simplesef_aliases']) ? unserialize($modSettings['simplesef_aliases']) : array();
        self::$useractions = !empty($modSettings['simplesef_useractions']) ? explode(',', $modSettings['simplesef_useractions']) : array();
        self::$stripWords = !empty($modSettings['simplesef_strip_words']) ? self::explode_csv($modSettings['simplesef_strip_words']) : array();
        self::$stripChars = !empty($modSettings['simplesef_strip_chars']) ? self::explode_csv($modSettings['simplesef_strip_chars']) : array();

        // Do a bit of post processing on the arrays above
        self::$stripWords = array_filter(self::$stripWords, create_function('$value', 'return !empty($value);'));
        array_walk(self::$stripWords, 'trim');
        self::$stripChars = array_filter(self::$stripChars, create_function('$value', 'return !empty($value);'));
        array_walk(self::$stripChars, 'trim');

        self::loadBoardNames($force);
        self::loadExtensions($force);
        self::fixHooks($force);

        self::log('Pre-fix GET:' . var_export($_GET, TRUE));

        // We need to fix our GET array too...
        parse_str(preg_replace('~&(\w+)(?=&|$)~', '&$1=', strtr($_SERVER['QUERY_STRING'], array(';?' => '&', ';' => '&', '%00' => '', "\0" => ''))), $_GET);

        self::log('Post-fix GET:' . var_export($_GET, TRUE), 'Init Complete (forced: ' . ($force ? 'true' : 'false') . ')');
    }

    /**
     * Implements integrate_pre_load
     * Converts the incoming query string 'q=' into a proper querystring and get
     * variable array.  q= comes from the .htaccess rewrite.
     * Will have to figure out how to do some checking of other types of SEF mods
     * and be able to rewrite those as well.  Currently we only rewrite our own urls
     *
     * @global string $boardurl SMF's board url
     * @global array $modSettings
     * @global string $scripturl
     * @global array $smcFunc SMF's smcFunc array of functions
     * @global string $language
     * @global string $sourcedir
     * @return void
     */
    public static function convertQueryString() {
        global $boardurl, $modSettings, $scripturl, $smcFunc, $language, $sourcedir;

        if (empty($modSettings['simplesef_enable']))
            return;

        self::init();

        $scripturl = $boardurl . '/index.php';

        // Make sure we know the URL of the current request.
        if (empty($_SERVER['REQUEST_URI']))
            $_SERVER['REQUEST_URL'] = $scripturl . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
        elseif (preg_match('~^([^/]+//[^/]+)~', $scripturl, $match) == 1)
            $_SERVER['REQUEST_URL'] = $match[1] . $_SERVER['REQUEST_URI'];
        else
            $_SERVER['REQUEST_URL'] = $_SERVER['REQUEST_URI'];

        if (!empty($modSettings['queryless_urls']))
            updateSettings(array('queryless_urls' => '0'));

        if (SMF == 'SSI')
            return;

        // if the URL contains index.php but not our ignored actions, rewrite the URL
        if (strpos($_SERVER['REQUEST_URL'], 'index.php') !== false && !(isset($_GET['xml']) || (!empty($_GET['action']) && in_array($_GET['action'], self::$ignoreactions)))) {
            self::log('Rewriting and redirecting permanently: ' . $_SERVER['REQUEST_URL']);
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . self::create_sef_url($_SERVER['REQUEST_URL']));
            exit();
        }

        // Parse the url
        if (!empty($_GET['q'])) {
            $querystring = self::route($_GET['q']);
            $_GET = $querystring + $_GET;
        }

        // Need to grab any extra query parts from the original url and tack it on here
        $_SERVER['QUERY_STRING'] = http_build_query($_GET, '', ';');

        self::log('Post-convert GET:' . var_export($_GET, true));
    }

    /**
     * Implements integrate_buffer
     * This is the core of the mod.  Rewrites the output buffer to create SEF
     * urls.  It will only rewrite urls for the site at hand, not other urls
     *
     * @global string $scripturl
     * @global array $smcFunc
     * @global string $boardurl
     * @global array $txt
     * @global array $modSettings
     * @global array $context
     * @param string $buffer The output buffer after SMF has output the templates
     * @return string Returns the altered buffer (or unaltered if the mod is disabled)
     */
    public static function ob_simplesef($buffer) {
        global $scripturl, $smcFunc, $boardurl, $txt, $modSettings, $context;

        if (empty($modSettings['simplesef_enable']))
            return $buffer;

        self::benchmark('buffer');

        // Bump up our memory limit a bit
        if (@ini_get('memory_limit') < 128)
            @ini_set('memory_limit', '128M');

        // Grab the topics...
        $matches = array();
        preg_match_all('~\b' . preg_quote($scripturl) . '.*?topic=([0-9]+)~', $buffer, $matches);
        if (!empty($matches[1]))
            self::loadTopicNames(array_unique($matches[1]));

        // We need to find urls that include a user id, so we can grab them all and fetch them ahead of time
        $matches = array();
        preg_match_all('~\b' . preg_quote($scripturl) . '.*?u=([0-9]+)~', $buffer, $matches);
        if (!empty($matches[1]))
            self::loadUserNames(array_unique($matches[1]));

        // Grab all URLs and fix them
        $matches = array();
        $count = 0;
        preg_match_all('~\b(' . preg_quote($scripturl) . '[-a-zA-Z0-9+&@#/%?=\~_|!:,.;\[\]]*[-a-zA-Z0-9+&@#/%=\~_|\[\]]?)([^-a-zA-Z0-9+&@#/%=\~_|])~', $buffer, $matches);
        if (!empty($matches[0])) {
            $replacements = array();
            foreach (array_unique($matches[1]) as $i => $url) {
                $replacement = self::create_sef_url($url);
                if ($url != $replacement)
                    $replacements[$matches[0][$i]] = $replacement . $matches[2][$i];
            }
            $buffer = str_replace(array_keys($replacements), array_values($replacements), $buffer);
            $count = count($replacements);
        }

        // Gotta fix up some javascript laying around in the templates
        $extra_replacements = array(
            '/$d\',' => '_%1$d/\',', // Page index for MessageIndex
            '/rand,' => '/rand=', // Verification Image
            '%1.html$d\',' => '%1$d.html\',', // Page index on MessageIndex for topics
            $boardurl . '/topic/' => $scripturl . '?topic=', // Also for above
            '%1_%1$d/\',' => '%1$d/\',', // Page index on Members listing
            'var smf_scripturl = "' . $boardurl . '/' => 'var smf_scripturl = "' . $scripturl,
        );
        $buffer = str_replace(array_keys($extra_replacements), array_values($extra_replacements), $buffer);

        // Check to see if we need to update the actions lists
        $changeArray = array();
        $possibleChanges = array('actions', 'useractions');
        foreach ($possibleChanges as $change)
            if (empty($modSettings['simplesef_' . $change]) || (substr_count($modSettings['simplesef_' . $change], ',') + 1) != count(self::$$change))
                $changeArray['simplesef_' . $change] = implode(',', self::$$change);

        if (!empty($changeArray)) {
            updateSettings($changeArray);
            self::$queryCount++;
        }

        self::benchmark('buffer');

        if (!empty($context['show_load_time']))
            $buffer = preg_replace('~(' . preg_quote($txt['page_created']) . '.*?' . preg_quote($txt['queries']) . ')~', '$1<br />' . sprintf($txt['simplesef_adds'], $count) . ' ' . round(self::$benchMark['total'], 3) . $txt['seconds_with'] . self::$queryCount . $txt['queries'], $buffer);

        self::log('SimpleSEF rewrote ' . $count . ' urls in ' . self::$benchMark['total'] . ' seconds');

        // I think we're done
        return $buffer;
    }

    /**
     * Implements integrate_redirect
     * When SMF calls redirectexit, we need to rewrite the URL its redirecting to
     * Without this, the convertQueryString would catch it, but would cause an
     * extra page load.  This helps reduce server load and streamlines redirects
     *
     * @global string $scripturl
     * @global array $modSettings
     * @param string $setLocation The original location (passed by reference)
     * @param boolean $refresh Unused, but declares if we are using meta refresh
     * @return <type>
     */
    public static function fixRedirectUrl(&$setLocation, &$refresh) {
        global $scripturl, $modSettings;

        if (empty($modSettings['simplesef_enable']))
            return;

        self::$redirect = true;
        self::log('Fixing redirect location: ' . $setLocation);

        // Only do this if it's an URL for this board
        if (strpos($setLocation, $scripturl) !== false)
            $setLocation = self::create_sef_url($setLocation);
    }

    /**
     * Implements integrate_exit
     * When SMF outputs XML data, the buffer function is never called.  To
     * circumvent this, we use the _exit hook which is called just before SMF
     * exits.  If SMF didn't output a footer, it typically didn't run through
     * our output buffer.  This catches the buffer and runs it through.
     *
     * @global array $modSettings
     * @param boolean $do_footer If we didn't do a footer and we're not wireless
     * @return void
     */
    public static function fixXMLOutput($do_footer) {
        global $modSettings;

        if (empty($modSettings['simplesef_enable']))
            return;

        if (!$do_footer && !self::$redirect) {
            $temp = ob_get_contents();

            ob_end_clean();
            ob_start(!empty($modSettings['enableCompressedOutput']) ? 'ob_gzhandler' : '');
            ob_start(array('SimpleSEF', 'ob_simplesef'));

            echo $temp;

            self::log('Rewriting XML Output');
        }
    }

    /**
     * Implements integrate_outgoing_mail
     * Simply adjusts the subject and message of an email with proper urls
     *
     * @global array $modSettings
     * @param string $subject The subject of the email
     * @param string $message Body of the email
     * @param string $header Header of the email (we don't adjust this)
     * @return boolean Always returns TRUE to prevent SMF from erroring
     */
    public static function fixEmailOutput(&$subject, &$message, &$header) {
        global $modSettings;

        if (empty($modSettings['simplesef_enable']))
            return TRUE;

        // We're just fixing the subject and message
        $subject = self::ob_simplesef($subject);
        $message = self::ob_simplesef($message);

        self::log('Rewriting email output');

        // We must return true, otherwise we fail!
        return TRUE;
    }

    /**
     * Implements integrate_actions
     * @param array $actions SMF's actions array
     */
    public static function actionArray(&$actions) {
        $actions['simplesef-404'] = array('SimpleSEF.php', array('SimpleSEF', 'http404NotFound'));
    }

    /**
     * Outputs a simple 'Not Found' message and the 404 header
     */
    public static function http404NotFound() {
        header('HTTP/1.0 404 Not Found');
        self::log('404 Not Found: ' . $_SERVER['REQUEST_URL']);
        fatal_lang_error('simplesef_404', FALSE);
    }

    /**
     * Implements integrate_menu_buttons
     * Adds some SimpleSEF settings to the main menu under the admin menu
     *
     * @global string $scripturl
     * @global array $txt
     * @global array $modSettings
     * @param array $menu_buttons Array of menu buttons, post processed
     * @return void
     */
    public static function menuButtons(&$menu_buttons) {
        global $scripturl, $txt, $modSettings;

        // If there's no admin menu, don't add our button
        if (empty($txt['simplesef']) || !allowedTo('admin_forum') || isset($menu_buttons['admin']['sub_buttons']['simplesef']))
            return;

        $counter = array_search('featuresettings', array_keys($menu_buttons['admin']['sub_buttons'])) + 1;

        $menu_buttons['admin']['sub_buttons'] = array_merge(
            array_slice($menu_buttons['admin']['sub_buttons'], 0, $counter, TRUE), array('simplesef' => array(
                'title' => $txt['simplesef'],
                'href' => $scripturl . '?action=admin;area=simplesef',
                'sub_buttons' => array(
                    'basic' => array('title' => $txt['simplesef_basic'], 'href' => $scripturl . '?action=admin;area=simplesef;sa=basic'),
                ),
            )), array_slice($menu_buttons['admin']['sub_buttons'], $counter, count($menu_buttons['admin']['sub_buttons']), TRUE)
        );

        if (!empty($modSettings['simplesef_advanced'])) {
            $menu_buttons['admin']['sub_buttons']['simplesef']['sub_buttons']['advanced'] = array('title' => $txt['simplesef_advanced'], 'href' => $scripturl . '?action=admin;area=simplesef;sa=advanced');
            $menu_buttons['admin']['sub_buttons']['simplesef']['sub_buttons']['alias'] = array('title' => $txt['simplesef_alias'], 'href' => $scripturl . '?action=admin;area=simplesef;sa=alias');
        }
    }

    /**
     * Implements integrate_admin_areas
     * Adds SimpleSEF options to the admin panel
     *
     * @global array $txt
     * @global array $modSettings
     * @param array $admin_areas
     */
    public static function adminAreas(&$admin_areas) {
        global $txt, $modSettings;

        // We insert it after Features and Options
        $counter = array_search('featuresettings', array_keys($admin_areas['config']['areas'])) + 1;

        $admin_areas['config']['areas'] = array_merge(
            array_slice($admin_areas['config']['areas'], 0, $counter, TRUE), array('simplesef' => array(
                'label' => $txt['simplesef'],
                'function' => create_function(NULL, 'SimpleSEF::ModifySimpleSEFSettings();'),
                'icon' => 'search.gif',
                'subsections' => array(
                    'basic' => array($txt['simplesef_basic']),
                    'advanced' => array($txt['simplesef_advanced'], 'enabled' => !empty($modSettings['simplesef_advanced'])),
                    'alias' => array($txt['simplesef_alias'], 'enabled' => !empty($modSettings['simplesef_advanced'])),
                ),
            )), array_slice($admin_areas['config']['areas'], $counter, count($admin_areas['config']['areas']), TRUE)
        );
    }

    /**
     * Directs the admin to the proper page of settings for SimpleSEF
     *
     * @global array $txt
     * @global array $context
     * @global string $sourcedir
     */
    public static function ModifySimpleSEFSettings() {
        global $txt, $context, $sourcedir;

        require_once($sourcedir . '/ManageSettings.php');

        $context['page_title'] = $txt['simplesef'];

        $subActions = array(
            'basic' => array('SimpleSEF', 'ModifyBasicSettings'),
            'advanced' => array('SimpleSEF', 'ModifyAdvancedSettings'),
            'alias' => array('SimpleSEF', 'ModifyAliasSettings'),
        );

        loadGeneralSettingParameters($subActions, 'basic');

        // Load up all the tabs...
        $context[$context['admin_menu_name']]['tab_data'] = array(
            'title' => $txt['simplesef'],
            'description' => $txt['simplesef_desc'],
            'tabs' => array(
                'basic' => array(
                ),
                'advanced' => array(
                ),
                'alias' => array(
                    'description' => $txt['simplesef_alias_desc'],
                ),
            ),
        );

        call_user_func($subActions[$_REQUEST['sa']]);
    }

    /**
     * Modifies the basic settings of SimpleSEF.
     *
     * @global string $scripturl
     * @global array $txt
     * @global array $context
     * @global string $boarddir
     * @global array $modSettings
     */
    public static function ModifyBasicSettings() {
        global $scripturl, $txt, $context, $boarddir, $modSettings;

        $config_vars = array(
            array('check', 'simplesef_enable', 'subtext' => $txt['simplesef_enable_desc']),
            array('check', 'simplesef_simple', 'subtext' => $txt['simplesef_simple_desc']),
            array('text', 'simplesef_space', 'size' => 6, 'subtext' => $txt['simplesef_space_desc']),
            array('text', 'simplesef_suffix', 'subtext' => $txt['simplesef_suffix_desc']),
            array('check', 'simplesef_advanced', 'subtext' => $txt['simplesef_advanced_desc']),
        );

        $context['post_url'] = $scripturl . '?action=admin;area=simplesef;sa=basic;save';

        // Saving?
        if (isset($_GET['save'])) {
            checkSession();

            if (trim($_POST['simplesef_suffix']) == '')
                fatal_lang_error('simplesef_suffix_required');

            $_POST['simplesef_suffix'] = trim($_POST['simplesef_suffix'], '.');

            $save_vars = $config_vars;

            // We don't want to break boards, so we'll make sure some stuff exists before actually enabling
            if (!empty($_POST['simplesef_enable']) && empty($modSettings['simplesef_enable'])) {
                if (strpos($_SERVER['SERVER_SOFTWARE'], 'IIS') !== false && file_exists($boarddir . '/web.config'))
                    $_POST['simplesef_enable'] = strpos(implode('', file($boarddir . '/web.config')), '<action type="Rewrite" url="index.php?q={R:1}"') !== false ? 1 : 0;
                elseif (strpos($_SERVER['SERVER_SOFTWARE'], 'IIS') === false && file_exists($boarddir . '/.htaccess'))
                    $_POST['simplesef_enable'] = strpos(implode('', file($boarddir . '/.htaccess')), 'RewriteRule ^(.*)$ index.php') !== false ? 1 : 0;
                elseif (strpos($_SERVER['SERVER_SOFTWARE'], 'lighttpd') !== false)
                    $_POST['simplesef_enable'] = 1;
                elseif (strpos($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false)
                    $_POST['simplesef_enable'] = 1;
                else
                    $_POST['simplesef_enable'] = 0;
            }

            saveDBSettings($save_vars);

            redirectexit('action=admin;area=simplesef;sa=basic');
        }

        prepareDBSettingContext($config_vars);
    }

    /**
     * Modifies the advanced settings for SimpleSEF.  Most setups won't need to
     * touch this (except for maybe other languages)
     *
     * @global string $scripturl
     * @global array $txt
     * @global array $context
     * @global string $boarddir
     * @global array $modSettings
     * @global array $settings
     */
    public static function ModifyAdvancedSettings() {
        global $scripturl, $txt, $context, $boarddir, $modSettings, $settings;

        loadTemplate('SimpleSEF');
        $config_vars = array(
            array('check', 'simplesef_lowercase', 'subtext' => $txt['simplesef_lowercase_desc']),
            array('large_text', 'simplesef_strip_words', 'size' => 6, 'subtext' => $txt['simplesef_strip_words_desc']),
            array('large_text', 'simplesef_strip_chars', 'size' => 6, 'subtext' => $txt['simplesef_strip_chars_desc']),
            array('check', 'simplesef_debug', 'subtext' => $txt['simplesef_debug_desc']),
            '',
            array('callback', 'simplesef_ignore'),
            array('title', 'title', 'label' => $txt['simplesef_action_title']),
            array('desc', 'desc', 'label' => $txt['simplesef_action_desc']),
            array('text', 'simplesef_actions', 'size' => 50, 'disabled' => 'disabled', 'preinput' => '<input type="hidden" name="simplesef_actions" value="' . $modSettings['simplesef_actions'] . '" />'),
            array('text', 'simplesef_useractions', 'size' => 50, 'disabled' => 'disabled', 'preinput' => '<input type="hidden" name="simplesef_useractions" value="' . $modSettings['simplesef_useractions'] . '" />'),
        );

        // Prepare the actions and ignore list
        $context['simplesef_dummy_ignore'] = !empty($modSettings['simplesef_ignore_actions']) ? explode(',', $modSettings['simplesef_ignore_actions']) : array();
        $context['simplesef_dummy_actions'] = array_diff(explode(',', $modSettings['simplesef_actions']), $context['simplesef_dummy_ignore']);
        $context['html_headers'] .= '<script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/SelectSwapper.js?rc5"></script>';

        $context['post_url'] = $scripturl . '?action=admin;area=simplesef;sa=advanced;save';
        $context['settings_post_javascript'] = '
			function editAreas()
			{
				document.getElementById("simplesef_actions").disabled = "";
				document.getElementById("setting_simplesef_actions").nextSibling.nextSibling.style.color = "";
				document.getElementById("simplesef_useractions").disabled = "";
				document.getElementById("setting_simplesef_useractions").nextSibling.nextSibling.style.color = "";
				return false;
			}
			var swapper = new SelectSwapper({
				sFromBoxId			: "dummy_actions",
				sToBoxId			: "dummy_ignore",
				sToBoxHiddenId		: "simplesef_ignore_actions",
				sAddButtonId		: "simplesef_ignore_add",
				sAddAllButtonId		: "simplesef_ignore_add_all",
				sRemoveButtonId		: "simplesef_ignore_remove",
				sRemoveAllButtonId	: "simplesef_ignore_remove_all"
			});';

        // Saving?
        if (isset($_GET['save'])) {
            checkSession();

            $save_vars = $config_vars;

            // Ignoring any actions??
            $save_vars[] = array('text', 'simplesef_ignore_actions');

            saveDBSettings($save_vars);

            redirectexit('action=admin;area=simplesef;sa=advanced');
        }

        prepareDBSettingContext($config_vars);
    }

    /**
     * Modifies the Action Aliasing settings
     *
     * @global string $scripturl
     * @global array $txt
     * @global array $context
     * @global array $modSettings
     */
    public static function ModifyAliasSettings() {
        global $scripturl, $txt, $context, $modSettings;

        loadTemplate('SimpleSEF');
        $context['sub_template'] = 'alias_settings';

        $context['simplesef_aliases'] = !empty($modSettings['simplesef_aliases']) ? unserialize($modSettings['simplesef_aliases']) : array();

        $context['post_url'] = $scripturl . '?action=admin;area=simplesef;sa=alias';

        if (isset($_POST['save'])) {
            checkSession();

            // Start with some fresh arrays
            $alias_original = array();
            $alias_new = array();

            // Clean up the passed in arrays
            if (isset($_POST['original'], $_POST['alias'])) {
                // Make sure we don't allow duplicate actions or aliases
                $_POST['original'] = array_unique(array_filter($_POST['original'], create_function('$x', 'return $x != \'\';')));
                $_POST['alias'] = array_unique(array_filter($_POST['alias'], create_function('$x', 'return $x != \'\';')));
                $alias_original = array_intersect_key($_POST['original'], $_POST['alias']);
                $alias_new = array_intersect_key($_POST['alias'], $_POST['original']);
            }

            $aliases = !empty($alias_original) ? array_combine($alias_original, $alias_new) : array();

            // One last check
            foreach ($aliases as $orig => $alias)
                if ($orig == $alias)
                    unset($aliases[$orig]);

            $updates = array(
                'simplesef_aliases' => serialize($aliases),
            );

            updateSettings($updates);

            redirectexit('action=admin;area=simplesef;sa=alias');
        }
    }

    /**
     * Implements integrate_load_theme
     * Loads up our language files
     */
    public static function loadTheme() {
        loadLanguage('SimpleSEF');
    }

    /**
     * This is a helper function of sorts that actually creates the SEF urls.
     * It compiles the different parts of a normal URL into a SEF style url
     *
     * @global string $sourcedir
     * @global array $modSettings
     * @param string $url URL to SEFize
     * @return string Either the original url if not enabled or ignored, or a new URL
     */
    public static function create_sef_url($url) {
        global $sourcedir, $modSettings;

        if (empty($modSettings['simplesef_enable']))
            return $url;

        // Set our output strings to nothing.
        $sefstring = $sefstring2 = $sefstring3 = '';
        $query_parts = array();

        // Get the query string of the passed URL
        $url_parts = parse_url($url);
        $params = array();
        parse_str(!empty($url_parts['query']) ? preg_replace('~&(\w+)(?=&|$)~', '&$1=', strtr($url_parts['query'], array('&amp;' => '&', ';' => '&'))) : '', $params);

        if (!empty($params['action'])) {
            // If we're ignoring this action, just return the original URL
            if (in_array($params['action'], self::$ignoreactions)) {
                self::log('create_sef_url: Ignoring ' . $params['action']);
                return $url;
            }

            if (!in_array($params['action'], self::$actions))
                self::$actions[] = $params['action'];
            $query_parts['action'] = $params['action'];
            unset($params['action']);

            if (!empty($params['u'])) {
                if (!in_array($query_parts['action'], self::$useractions))
                    self::$useractions[] = $query_parts['action'];
                $query_parts['user'] = self::getUserName($params['u']);
                unset($params['u'], $params['user']);
            }
        }

        if (!empty($query_parts['action']) && !empty(self::$extensions[$query_parts['action']])) {
            require_once($sourcedir . '/SimpleSEF-Ext/' . self::$extensions[$query_parts['action']]);
            $class = ucwords($query_parts['action']);
            $extension = new $class();
            $sefstring2 = $extension->create($params);
        } else {
            if (!empty($params['board'])) {
                $query_parts['board'] = self::getBoardName($params['board']);
                unset($params['board']);
            }
            if (!empty($params['topic'])) {
                $query_parts['topic'] = self::getTopicName($params['topic']);
                unset($params['topic']);
            }

            foreach ($params as $key => $value) {
                if ($value == '')
                    $sefstring3 .= $key . './';
                else {
                    $sefstring2 .= $key;
                    if (is_array($value))
                        $sefstring2 .= '[' . key($value) . '].' . $value[key($value)] . '/';
                    else
                        $sefstring2 .= '.' . $value . '/';
                }
            }
        }

        // Fix the action if it's being aliased
        if (isset($query_parts['action']) && !empty(self::$aliasactions[$query_parts['action']]))
            $query_parts['action'] = self::$aliasactions[$query_parts['action']];

        // Build the URL
        if (isset($query_parts['action']))
            $sefstring .= $query_parts['action'] . '/';
        if (isset($query_parts['user']))
            $sefstring .= $query_parts['user'] . '/';
        if (isset($sefstring2))
            $sefstring .= $sefstring2;
        if (isset($sefstring3))
            $sefstring .= $sefstring3;
        if (isset($query_parts['board']))
            $sefstring .= $query_parts['board'] . '/';
        if (isset($query_parts['topic']))
            $sefstring .= $query_parts['topic'];

        return str_replace('index.php' . (!empty($url_parts['query']) ? '?' . $url_parts['query'] : ''), $sefstring, $url); //$boardurl . '/' . $sefstring . (!empty($url_parts['fragment']) ? '#' . $url_parts['fragment'] : '');
    }

    public static function fixHooks($force = FALSE) {
        global $smcFunc, $modSettings;

        // We only do this once an hour, no need to overload things
        if (!$force && cache_get_data('simplesef_fixhooks', 3600) !== NULL)
            return;

        $request = $smcFunc['db_query']('', '
            SELECT variable, value
            FROM {db_prefix}settings
            WHERE variable LIKE {string:variable}', array(
            'variable' => 'integrate_%',
            )
        );

        $hooks = array();
        while (($row = $smcFunc['db_fetch_assoc']($request)))
            $hooks[$row['variable']] = $row['value'];
        $smcFunc['db_free_result']($request);
        self::$queryCount++;

        $fixups = array();
        if (!empty($hooks['integrate_pre_load']) && strpos($hooks['integrate_pre_load'], 'SimpleSEF') !== 0) {
            $fixups['integrate_pre_load'] = 'SimpleSEF::convertQueryString,' . str_replace(',SimpleSEF::convertQueryString', '', $hooks['integrate_pre_load']);
        }
        if (!empty($hooks['integrate_buffer']) && strpos($hooks['integrate_buffer'], 'SimpleSEF') !== 0) {
            $fixups['integrate_buffer'] = 'SimpleSEF::ob_simplesef,' . str_replace(',SimpleSEF::ob_simplesef', '', $hooks['integrate_buffer']);
        }
        if (!empty($hooks['integrate_exit']) && strpos($hooks['integrate_exit'], 'SimpleSEF') !== 0) {
            $fixups['integrate_exit'] = 'SimpleSEF::fixXMLOutput,' . str_replace(',SimpleSEF::fixXMLOutput', '', $hooks['integrate_exit']);
        }

        if (!empty($fixups))
            updateSettings($fixups);

        // Update modSettings
        foreach ($fixups as $hook => $functions)
            $modSettings[$hook] = str_replace($hooks[$hook], $fixups[$hook], $modSettings[$hook]);

        cache_put_data('simplesef_fixhooks', TRUE, 3600);

        self::log('Fixed up integration hooks: ' . var_export($fixups, TRUE));
    }

    /*     * ******************************************
     * 			Utility Functions				*
     * ****************************************** */

    /**
     * Takes in a board name and tries to determine it's id
     *
     * @global array $modSettings
     * @param string $boardName
     * @return mixed Will return false if it can't find an id or the id if found
     */
    private static function getBoardId($boardName) {
        global $modSettings;

        if (($boardId = array_search($boardName, self::$boardNames)) !== false)
            return $boardId . '.0';

        if (($index = strrpos($boardName, $modSettings['simplesef_space'])) === false)
            return false;

        $page = substr($boardName, $index + 1);
        if (is_numeric($page))
            $boardName = substr($boardName, 0, $index);
        else
            $page = '0';

        if (($boardId = array_search($boardName, self::$boardNames)) !== false)
            return $boardId . '.' . $page;
        else
            return false;
    }

    /**
     * Generates a board name from the ID.  Checks the existing array and reloads
     * it if it's not in there for some reason
     *
     * @global array $modSettings
     * @param int $id Board ID
     * @return string
     */
    private static function getBoardName($id) {
        global $modSettings;

        if (!empty($modSettings['simplesef_simple']))
            $boardName = 'board' . $modSettings['simplesef_space'] . $id;
        else {
            if (stripos($id, '.') !== false) {
                $page = substr($id, stripos($id, '.') + 1);
                $id = substr($id, 0, stripos($id, '.'));
            }

            if (empty(self::$boardNames[$id]))
                self::loadBoardNames(TRUE);
            $boardName = !empty(self::$boardNames[$id]) ? self::$boardNames[$id] : 'board';
            if (isset($page) && ($page > 0))
                $boardName = $boardName . $modSettings['simplesef_space'] . $page;
        }
        return $boardName;
    }

    /**
     * Generates a topic name from it's id.  This is typically called from
     * create_sef_url which is called from ob_simplesef which prepopulates topics.
     * If the topic isn't prepopulated, it attempts to find it.
     *
     * @global array $modSettings
     * @global array $smcFunc
     * @param int $id
     * @return string Topic name with it's associated board name
     */
    private static function getTopicName($id) {
        global $modSettings, $smcFunc;

        @list($value, $start) = explode('.', $id);
        if (!isset($start))
            $start = '0';
        if (!empty($modSettings['simplesef_simple']) || !is_numeric($value))
            return 'topic' . $modSettings['simplesef_space'] . $id . '.' . $modSettings['simplesef_suffix'];

        // If the topic id isn't here (probably from a redirect) we need a query to get it
        if (empty(self::$topicNames[$value]))
            self::loadTopicNames((int) $value);

        // and if it still doesn't exist
        if (empty(self::$topicNames[$value])) {
            $topicName = 'topic';
            $boardName = 'board';
        } else {
            $topicName = self::$topicNames[$value]['subject'];
            $boardName = self::getBoardName(self::$topicNames[$value]['board_id']);
        }

        // Put it all together
        return $boardName . '/' . $topicName . $modSettings['simplesef_space'] . $value . '.' . $start . '.' . $modSettings['simplesef_suffix'];
    }

    /**
     * Generates a username from the ID.  See above comment block for
     * pregeneration information
     *
     * @global array $modSettings
     * @global array $smcFunc
     * @param int $id User ID
     * @return string User name
     */
    private static function getUserName($id) {
        global $modSettings, $smcFunc;

        if (!empty($modSettings['simplesef_simple']) || !is_numeric($id))
            return 'user' . $modSettings['simplesef_space'] . $id;

        if (empty(self::$userNames[$id]))
            self::loadUserNames((int) $id);

        // And if it's still empty...
        if (empty(self::$userNames[$id]))
            return 'user' . $modSettings['simplesef_space'] . $id;
        else
            return self::$userNames[$id] . $modSettings['simplesef_space'] . $id;
    }

    /**
     * Takes the q= part of the query string passed in and tries to find out
     * how to put the URL into terms SMF can understand.  If it can't, it forces
     * the action to SimpleSEF's own 404 action and throws a nice error page.
     *
     * @global string $boardurl
     * @global array $modSettings
     * @global string $sourcedir
     * @param string $query Querystring to deal with
     * @return array Returns an array suitable to be merged with $_GET
     */
    private static function route($query) {
        global $boardurl, $modSettings, $sourcedir;

        $url_parts = explode('/', trim($query, '/'));
        $querystring = array();

        $current_value = reset($url_parts);
        // Do we have an action?
        if ((in_array($current_value, self::$actions) || in_array($current_value, self::$aliasactions)) && !in_array($current_value, self::$ignoreactions) ) {
            $querystring['action'] = array_shift($url_parts);

            // We may need to fix the action
            if (($reverse_alias = array_search($current_value, self::$aliasactions)) !== FALSE)
                $querystring['action'] = $reverse_alias;
            $current_value = reset($url_parts);

            // User
            if (!empty($current_value) && in_array($querystring['action'], self::$useractions) && ($index = strrpos($current_value, $modSettings['simplesef_space'])) !== false) {
                $user = substr(array_shift($url_parts), $index + 1);
                if (is_numeric($user))
                    $querystring['u'] = intval($user);
                else
                    $querystring['user'] = $user;
                $current_value = reset($url_parts);
            }

            if (!empty(self::$extensions[$querystring['action']])) {
                require_once($sourcedir . '/SimpleSEF-Ext/' . self::$extensions[$querystring['action']]);
                $class = ucwords($querystring['action']);
                $extension = new $class();
                $querystring += $extension->route($url_parts);
                self::log('Rerouted "' . $querystring['action'] . '" action with extension');

                // Empty it out so it's not handled by this code
                $url_parts = array();
            }
        }

        if (!empty($url_parts)) {
            $current_value = array_pop($url_parts);
            if (strrpos($current_value, $modSettings['simplesef_suffix'])) {
                // remove the suffix and get the topic id
                $topic = str_replace($modSettings['simplesef_suffix'], '', $current_value);
                $topic = substr($topic, strrpos($topic, $modSettings['simplesef_space']) + 1);
                $querystring['topic'] = $topic;

                // remove the board name too
                if (empty($modSettings['simplesef_simple']))
                    array_pop($url_parts);
            }
            else {
                //check to see if the last one in the url array is a board
                if (preg_match('~^board_(\d+)$~', $current_value, $match))
                    $boardId = $match[1];
                else
                    $boardId = self::getBoardId($current_value);

                if ($boardId !== false)
                    $querystring['board'] = $boardId;
                else
                    array_push($url_parts, $current_value);
            }

            if (!empty($url_parts) && (strpos($url_parts[0], '.') === false && strpos($url_parts[0], ',') === false))
                $querystring['action'] = 'simplesef-404';

            // handle unknown variables
            $temp = array();
            foreach ($url_parts as $part) {
                if (strpos($part, '.') !== false)
                    $part = substr_replace($part, '=', strpos($part, '.'), 1);

                // Backwards compatibility
                elseif (strpos($part, ',') !== false)
                    $part = substr_replace($part, '=', strpos($part, ','), 1);
                parse_str($part, $temp);
                $querystring += $temp;
            }
        }

        self::log('Rerouted "' . $query . '" to ' . var_export($querystring, TRUE));

        return $querystring;
    }

    /**
     * Loads any extensions that other mod authors may have introduced
     *
     * @global string $sourcedir
     */
    private static function loadExtensions($force = FALSE) {
        global $sourcedir;

        if ($force || (self::$extensions = cache_get_data('simplsef_extensions', 3600)) === NULL) {
            $ext_dir = $sourcedir . '/SimpleSEF-Ext';
            self::$extensions = array();
            if (is_readable($ext_dir)) {
                $dh = opendir($ext_dir);
                while ($filename = readdir($dh)) {
                    // Skip these
                    if (in_array($filename, array('.', '..')) || preg_match('~ssef_([a-zA-Z_-]+)\.php~', $filename, $match) == 0)
                        continue;

                    self::$extensions[$match[1]] = $filename;
                }
            }

            cache_put_data('simplesef_extensions', self::$extensions, 3600);
            self::log('Cache hit failed, reloading extensions');
        }
    }

    /**
     * Loads all board names from the forum into a variable and cache (if possible)
     * This helps reduce the number of queries needed for SimpleSEF to run
     *
     * @global array $smcFunc
     * @global string $language
     * @param boolean $force Forces a reload of board names
     */
    private static function loadBoardNames($force = FALSE) {
        global $smcFunc, $language;

        if ($force || (self::$boardNames = cache_get_data('simplesef_board_list', 3600)) == NULL) {
            loadLanguage('index', $language, false);
            $request = $smcFunc['db_query']('', '
				SELECT id_board, name
				FROM {db_prefix}boards', array()
            );
            $boards = array();
            while ($row = $smcFunc['db_fetch_assoc']($request)) {
                // A bit extra overhead to account for duplicate board names
                $temp_name = self::encode($row['name']);
                $i = 0;
                while (!empty($boards[$temp_name . (!empty($i) ? $i + 1 : '')]))
                    $i++;
                $boards[$temp_name . (!empty($i) ? $i + 1 : '')] = $row['id_board'];
            }
            $smcFunc['db_free_result']($request);

            self::$boardNames = array_flip($boards);

            // Add one to the query cound and put the data into the cache
            self::$queryCount++;
            cache_put_data('simplesef_board_list', self::$boardNames, 3600);
            self::log('Cache hit failed, reloading board names');
        }
    }

    /**
     * Takes one or more topic id's, grabs their information from the database
     * and stores it for later use.  Helps keep queries to a minimum.
     *
     * @global array $smcFunc
     * @param mixed $ids Can either be a single id or an array of ids
     */
    private static function loadTopicNames($ids) {
        global $smcFunc;

        $ids = is_array($ids) ? $ids : array($ids);

        // Fill the topic 'cache' in one fell swoop
        $request = $smcFunc['db_query']('', '
			SELECT t.id_topic, m.subject, t.id_board
			FROM {db_prefix}topics AS t
				INNER JOIN {db_prefix}messages AS m ON (m.id_msg = t.id_first_msg)
			WHERE t.id_topic IN ({array_int:topics})', array(
            'topics' => $ids,
            )
        );
        while ($row = $smcFunc['db_fetch_assoc']($request)) {
            self::$topicNames[$row['id_topic']] = array(
                'subject' => self::encode($row['subject']),
                'board_id' => $row['id_board'],
            );
        }
        $smcFunc['db_free_result']($request);
        self::$queryCount++;
    }

    /**
     * Takes one or more user ids and stores the usernames for those users for
     * later user
     *
     * @global array $smcFunc
     * @param mixed $ids can be either a single id or an array of them
     */
    private static function loadUserNames($ids) {
        global $smcFunc;

        $ids = is_array($ids) ? $ids : array($ids);

        $request = $smcFunc['db_query']('', '
			SELECT id_member, real_name
			FROM {db_prefix}members
			WHERE id_member IN ({array_int:members})', array(
            'members' => $ids,
            )
        );
        while ($row = $smcFunc['db_fetch_assoc']($request))
            self::$userNames[$row['id_member']] = self::encode($row['real_name']);
        $smcFunc['db_free_result']($request);
        self::$queryCount++;
    }

	private static function encode_old($text)
	{
		global $modSettings, $txt;

		//	Do you know your ABCs?
		$characterHash = array (
			'a'	=>	array ('a', 'A', 'Ã ', 'Ã€', 'Ã¡', 'Ã', 'Ã¢', 'Ã‚', 'Ã£', 'Ãƒ', 'Ã¤', 'Ã„', 'Ã¥', 'Ã…', 'Âª', 'Ä…', 'Ä„', 'Ð°', 'Ð', 'áº¡', 'áº ', 'áº£', 'áº¢', 'áº¦', 'áº§', 'áº¤', 'áº¥', 'áº¬', 'áº­', 'áº¨', 'áº©', 'áºª', 'áº«', 'Ä‚', 'Äƒ', 'áº®', 'áº¯', 'áº´', 'áºµ', 'áº¶', 'áº·', 'áº°', 'áº±', 'áº²', 'áº³'),
			'ae'	=>	array ('Ã¦', 'Ã†'),
			'b'	=>	array ('b', 'B'),
			'c'	=>	array ('c', 'C', 'Ã§', 'Ã‡', 'Ä‡', 'Ä†', 'Ä', 'ÄŒ'),
			'd'	=>	array ('d', 'D', 'Ã', 'Ä‘', 'Ä', 'Ä', 'ÄŽ'),
			'e'	=>	array ('e', 'E', 'Ã¨', 'Ãˆ', 'Ã©', 'Ã‰', 'Ãª', 'ÃŠ', 'Ã«', 'Ã‹', 'Ä™', 'Ä˜', 'Ðµ', 'Ð•', 'Ñ‘', 'Ð', 'Ñ', 'Ð­', 'áº¸', 'áº¹', 'áºº', 'áº»', 'áº¼', 'áº½', 'á»€', 'á»', 'áº¾', 'áº¿', 'á»†', 'á»‡', 'á»‚', 'á»ƒ', 'á»„', 'á»…', 'Îµ', 'Î•', 'Ä›', 'Äš'),
			'f'	=>	array ('f', 'F'),
			'g'	=>	array ('g', 'G', 'ÄŸ', 'Äž'),
			'h'	=>	array ('h', 'H'),
			'i'	=>	array ('i', 'I', 'Ã¬', 'ÃŒ', 'Ã­', 'Ã', 'Ã®', 'ÃŽ', 'Ã¯', 'Ã', 'Ä±', 'Ä°', 'á»Š', 'á»‹', 'á»ˆ', 'á»‰', 'Ä¨', 'Ä©', 'Î™', 'Î¹'),
			'j'	=>	array ('j', 'J'),
			'k'	=>	array ('k', 'K', 'Ðº', 'Ðš', 'Îº', 'Îš'),
			'l'	=>	array ('l', 'L', 'Å‚', 'Å'),
			'm'	=>	array ('m', 'M', 'Ð¼', 'Ðœ', 'Îœ'),
			'n'	=>	array ('n', 'N', 'Ã±', 'Ã‘', 'Å„', 'Åƒ', 'Åˆ', 'Å‡'),
			'o'	=>	array ('o', 'O', 'Ã²', 'Ã’', 'Ã³', 'Ã“', 'Ã´', 'Ã”', 'Ãµ', 'Ã•', 'Ã¶', 'Ã–', 'Ã¸', 'Ã˜', 'Âº', 'Ð¾', 'Ðž', 'á»Œ', 'á»', 'á»Ž', 'á»', 'á»˜', 'á»™', 'á»', 'á»‘', 'á»–', 'á»—', 'á»’', 'á»“', 'á»”', 'á»•', 'Æ ', 'Æ¡', 'á»œ', 'á»', 'á»š', 'á»›', 'á»¢', 'á»£', 'á»ž', 'á»Ÿ', 'á» ', 'á»¡', 'Î¿', 'ÎŸ'),
			'p'	=>	array ('p', 'P'),
			'q'	=>	array ('q', 'Q'),
			'r'	=>	array ('r', 'R', 'Å™', 'Å˜'),
			's'	=>	array ('s', 'S', 'ÅŸ', 'Åž', 'Å›', 'Åš', 'Å¡', 'Å '),
			'ss'	=>	array ('ÃŸ'),
			't'	=>	array ('t', 'T', 'Ñ‚', 'Ð¢', 'Ï„', 'Î¤', 'Å£', 'Å¢', 'Å¥', 'Å¤'),
			'u'	=>	array ('u', 'U', 'Ã¹', 'Ã™', 'Ãº', 'Ãš', 'Ã»', 'Ã›', 'Ã¼', 'Ãœ', 'á»¤', 'á»¥', 'á»¦', 'á»§', 'Å¨', 'Å©', 'Æ¯', 'Æ°', 'á»ª', 'á»«', 'á»¨', 'á»©', 'á»°', 'á»±', 'á»¬', 'á»­', 'á»®', 'á»¯', 'Å¯', 'Å®'),
			'v'	=>	array ('v', 'V'),
			'w'	=>	array ('w', 'W'),
			'x'	=>	array ('x', 'X', 'Ã—'),
			'y'	=>	array ('y', 'Y', 'Ã½', 'Ã', 'Ã¿', 'á»²', 'á»³', 'á»´', 'á»µ', 'á»¶', 'á»·', 'á»¸', 'á»¹'),
			'z'	=>	array ('z', 'Z', 'Å¼', 'Å»', 'Åº', 'Å¹', 'Å¾', 'Å½', 'Î–'),
			'-'	=>	array ('-', ' ', '.', ','),
			'_'	=>	array ('_'),
			'!'	=>	array ('!'),
			'~'	=>	array ('~'),
			'*'	=>	array ('*'),
			"\x12"	=>	array ("'", '"'),
			'('	=>	array ('(', '{', '['),
			')'	=>	array (')', '}', ']'),
			'$'	=>	array ('$'),
			'0'	=>	array ('0'),
			'1'	=>	array ('1', 'Â¹'),
			'2'	=>	array ('2', 'Â²'),
			'3'	=>	array ('3', 'Â³'),
			'4'	=>	array ('4'),
			'5'	=>	array ('5'),
			'6'	=>	array ('6'),
			'7'	=>	array ('7'),
			'8'	=>	array ('8'),
			'9'	=>	array ('9'),
		);

		//	Get or detect the database encoding, firstly from the settings or language files
		if (isset($modSettings['global_character_set']))
			$encoding = strtoupper($modSettings['global_character_set']);
		else if (isset($txt['lang_character_set']))
			$encoding = strtoupper($txt['lang_character_set']);
		//	or try checking UTF-8 conformance
		else if (preg_match('~.~su', $text))
			$encoding = 'UTF-8';
		//	or sadly... we may have to assume Latin-1
		else
			$encoding = 'ISO-8859-1';

		//	If the database encoding isn't UTF-8 and multibyte string functions are available, try converting the text to UTF-8
		if ($encoding != 'UTF-8' && function_exists('mb_convert_encoding'))
			$text = mb_convert_encoding($text, 'UTF-8', $encoding);
		//	Or maybe we can convert with iconv
		else if ($encoding != 'UTF-8' && function_exists('iconv'))
			$text = iconv($encoding, 'UTF-8', $text);
		//	Fix Turkish
		else if ($encoding == 'ISO-8859-9')
		{
			$text = str_replace(array("\xD0", "\xDD", "\xDE", "\xF0", "\xFD", "\xFE"), array('g', 'i', 's', 'g', 'i', 's'), $text);
			$text = utf8_encode($text);
		}
		//	Latin-1 can be converted easily
		else if ($encoding == 'ISO-8859-1')
			$text = utf8_encode($text);

		//	Change the entities back to normal characters
		$text = str_replace(array('&amp;', '&quot;'), array('&', '"'), $text);
		$prettytext = '';

		//	Split up $text into UTF-8 letters
		preg_match_all("~.~su", $text, $characters);
		foreach ($characters[0] as $aLetter)
		{
			foreach ($characterHash as $replace => $search)
			{
				//	Found a character? Replace it!
				if (in_array($aLetter, $search))
				{
					$prettytext .= $replace;
					break;
				}
			}
		}
		//	Remove unwanted '-'s
		$prettytext = preg_replace(array('~^-+|-+$~', '~-+~'), array('', '-'), $prettytext);
        //$prettytext = urlencode($prettytext);
        $prettytext = str_replace('%2F', '', $prettytext);
        //$prettytext = str_replace(self::$stripChars, '', $prettytext);
		return $prettytext;
	}
    
    /**
     * The encode function is responsible for transforming any string of text
     * in the URL into something that looks good and representable.  For forums
     * not using ASCII or UTF8 character sets, we convert them to utf8 and then
     * transliterate them.
     *
     * @global array $modSettings
     * @global string $sourcedir
     * @global array $txt
     * @staticvar array $utf8_db
     * @param string $string String to encode
     * @return string Returns an encoded string
     */
    private static function encode($string) {
        global $modSettings, $sourcedir, $txt;
        static $utf8_db = array();

        if (empty($string))
            return '';

        // We need to make sure all strings are either ISO-8859-1 or UTF-8 and if not, convert to UTF-8 (if the host has stuff installed right)
        $char_set = empty($modSettings['global_character_set']) ? $txt['lang_character_set'] : $modSettings['global_character_set'];
        if ($char_set != 'ISO-8859-1' && $char_set != 'UTF-8') {
            if (function_exists('iconv'))
                $string = iconv($char_set, 'UTF-8//IGNORE', $string);
            elseif (function_exists('mb_convert_encoding'))
                $string = mb_convert_encoding($string, 'UTF8', $char_set);
            elseif (function_exists('unicode_decode'))
                $string = unicode_decode($string, $char_set);
        }
        else {
        	setlocale(LC_CTYPE, 'en_US.utf8');
    		$string = iconv('UTF-8', "US-ASCII//TRANSLIT", $string); // TRANSLIT does the whole job
        	$string = implode(' ', array_diff(explode(' ', $string), self::$stripWords));
        	$string = str_replace(self::$stripChars, '', $string);
    		$string = preg_replace('~[^\\pL0-9_]+~u', $modSettings['simplesef_space'], $string); // substitutes anything but letters, numbers and '_' with separator 
        	if (!empty($modSettings['simplesef_lowercase']))
            	$string = strtolower($string);
    		return($string);
        }

        // A way to track/store the current character
        
        /*
        $character = 0;
        // Gotta return something...
        $result = '';

        $length = strlen($string);
        $i = 0;

        while ($i < $length) {
            $charInt = ord($string{$i++});
	    
	    $character = $charInt;
            // We have a normal Ascii character

            if (($charInt & 0x80) == 0) {
                $character = $charInt;
            }
            // Two byte unicode character
            elseif (($charInt & 0xE0) == 0xC0) {
                $temp1 = ord($string{$i++});
                if (($temp1 & 0xC0) != 0x80)
                    $character = 63;
                else
                    $character = ($charInt & 0x1F) << 6 | ($temp1 & 0x3F);
            }
            // Three byte unicode character
            elseif (($charInt & 0xF0) == 0xE0) {
                $temp1 = ord($string{$i++});
                $temp2 = ord($string{$i++});
                if (($temp1 & 0xC0) != 0x80 || ($temp2 & 0xC0) != 0x80)
                    $character = 63;
                else
                    $character = ($charInt & 0x0F) << 12 | ($temp1 & 0x3F) << 6 | ($temp2 & 0x3F);
            }
            // Four byte unicode character
            elseif (($charInt & 0xF8) == 0xF0) {
                $temp1 = ord($string{$i++});
                $temp2 = ord($string{$i++});
                $temp3 = ord($string{$i++});
                if (($temp1 & 0xC0) != 0x80 || ($temp2 & 0xC0) != 0x80 || ($temp3 & 0xC0) != 0x80)
                    $character = 63;
                else
                    $character = ($charInt & 0x07) << 18 | ($temp1 & 0x3F) << 12 | ($temp2 & 0x3F) << 6 | ($temp3 & 0x3F);
            }
            // More than four bytes... ? mark
            else
                $character = 63;

            // Need to get the bank this character is in.
            
	    $charBank = $character >> 8;
            if (!isset($utf8_db[$charBank])) {
                // Load up the bank if it's not already in memory
                $dbFile = $sourcedir . 'SimpleSEF-Db/x' . sprintf('%02x', $charBank) . '.php';
                if (!is_readable($dbFile) || !@include_once($dbFile))
                    $utf8_db[$charBank] = array();
            }

            $finalChar = $character & 255;
            $result .= isset($utf8_db[$charBank][$finalChar]) ? $utf8_db[$charBank][$finalChar] : '?';
        }

        // Update the string with our new string
        $string = (string)$result;
        */
        $string = implode(' ', array_diff(explode(' ', $string), self::$stripWords));
        $string = str_replace(self::$stripChars, '', $string);
        $string = trim($string, " $modSettings[simplesef_space]\t\n\r");
        $string = urlencode($string);
        $string = str_replace('%2F', '', $string);
        $string = str_replace($modSettings['simplesef_space'], '+', $string);
        $string = preg_replace('~(\+)+~', $modSettings['simplesef_space'], $string);
        if (!empty($modSettings['simplesef_lowercase']))
            $string = strtolower($string);

        return $string;
    }

    /**
     * Helper function to properly explode a CSV list (Accounts for quotes)
     *
     * @param string $str String to explode
     * @return array Exploded string
     */
    private static function explode_csv($str) {
        return!empty($str) ? preg_replace_callback('/^"(.*)"$/', create_function('$match', 'return trim($match[1]);'), preg_split('/,(?=(?:[^"]*"[^"]*")*(?![^"]*"))/', trim($str))) : array();
    }

    /**
     * Small helper function for benchmarking SimpleSEF.  It's semi smart in the
     * fact that you don't need to specify a 'start' or 'stop'... just pass the
     * 'marker' twice and that starts and stops it automatically and adds to the total
     *
     * @param string $marker
     */
    private static function benchmark($marker) {
        if (!empty(self::$benchMark['marks'][$marker])) {
            self::$benchMark['marks'][$marker]['stop'] = microtime(TRUE);
            self::$benchMark['total'] += self::$benchMark['marks'][$marker]['stop'] - self::$benchMark['marks'][$marker]['start'];
        }
        else
            self::$benchMark['marks'][$marker]['start'] = microtime(TRUE);
    }

    /**
     * Simple function to aide in logging debug statements
     * May pass as many simple variables as arguments as you wish
     *
     * @global array $modSettings
     */
    private static function log() {
        global $modSettings;

        if (!empty($modSettings['simplesef_debug']))
            foreach (func_get_args() as $string)
                log_error($string, 'debug', __FILE__);
    }

}