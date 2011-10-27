<?php
/*
 * simple test for a hook...
 */
if (!defined('SMF') && file_exists(dirname(__FILE__) . '/SSI.php'))
    require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF'))
    die('<b>Error:</b> Cannot install - file MUST be run from SMF\'s base directory.');

if (!empty($smcFunc['db_query'])) {
    $_functions = array(
        'integrate_pre_include' => $sourcedir . '/contrib/footnotes.php',
        'integrate_parse_bbc_after' => 'fnotes_parse',
    );
    foreach ($_functions as $hook => $function)
        add_integration_function($hook, $function, TRUE);
}

if (SMF == 'SSI') {
    fatal_error('<b>Installation of hooks completed successfully.!</b><br />');
    @unlink(__FILE__);
}
?>