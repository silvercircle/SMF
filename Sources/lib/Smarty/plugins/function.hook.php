<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.vbchecker.php
 * Type:     function
 * Name:     vbchecker
 * Purpose:  check string length and outputs a response
 * -------------------------------------------------------------
 */

/**
 * @param $params array
 * @param $smarty Smarty
 * @return string
 */
function smarty_function_hook($params, &$smarty)
{
	EoS_Smarty::getConfigInstance()->displayHook($params['name']);
}
