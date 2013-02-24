<?php
/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.hook.php
 * Type:     function
 * Name:     hook
 * Purpose:  display a template hook (a set of sub-templates registered under the given hook identifier).
 * -------------------------------------------------------------
 */

/**
 * @param $params 		array  the parameters passed to the function.
 * 						{hook name="foo"} would place "foo" into $params['name'].
 * @param $smarty 		Smarty (our smarty object)
 */
function smarty_function_hook($params, &$smarty)
{
	EoS_Smarty::getConfigInstance()->displayHook($params['name']);
}
