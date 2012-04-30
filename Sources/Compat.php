<?php

global $smcFunc, $modSettings;

// Set a list of common functions.
$ent_list = empty($modSettings['disableEntityCheck']) ? '&(#\d{1,7}|quot|amp|lt|gt|nbsp);' : '&(#021|quot|amp|lt|gt|nbsp);';
$ent_check = empty($modSettings['disableEntityCheck']) ? array('preg_replace(\'~(&#(\d{1,7}|x[0-9a-fA-F]{1,6});)~e\', \'$smcFunc[\\\'entity_fix\\\'](\\\'\\2\\\')\', ', ')') : array('', '');

// Preg_replace can handle complex characters only for higher PHP versions.
//$space_chars = $utf8 ? (@version_compare(PHP_VERSION, '4.3.3') != -1 ? '\x{A0}\x{AD}\x{2000}-\x{200F}\x{201F}\x{202F}\x{3000}\x{FEFF}' : "\xC2\xA0\xC2\xAD\xE2\x80\x80-\xE2\x80\x8F\xE2\x80\x9F\xE2\x80\xAF\xE2\x80\x9F\xE3\x80\x80\xEF\xBB\xBF") : '\x00-\x08\x0B\x0C\x0E-\x19\xA0';
$space_chars = '\x{A0}\x{AD}\x{2000}-\x{200F}\x{201F}\x{202F}\x{3000}\x{FEFF}';

$smcFunc += array(
	'entity_fix' => create_function('$string', '
		$num = substr($string, 0, 1) === \'x\' ? hexdec(substr($string, 1)) : (int) $string;
		return $num < 0x20 || $num > 0x10FFFF || ($num >= 0xD800 && $num <= 0xDFFF) || $num === 0x202E || $num === 0x202D ? \'\' : \'&#\' . $num . \';\';'),
	'htmlspecialchars' => create_function('$string, $quote_style = ENT_COMPAT, $charset = \'ISO-8859-1\'', '
		global $smcFunc;
		return ' . strtr($ent_check[0], array('&' => '&amp;')) . 'htmlspecialchars($string, $quote_style, ' . '\'UTF-8\'' . ')' . $ent_check[1] . ';'),
	'htmltrim' => create_function('$string', '
		global $smcFunc;
		return preg_replace(\'~^(?:[ \t\n\r\x0B\x00' . $space_chars . ']|&nbsp;)+|(?:[ \t\n\r\x0B\x00' . $space_chars . ']|&nbsp;)+$~' . 'u' . '\', \'\', ' . implode('$string', $ent_check) . ');'),
	'strlen' => create_function('$string', '
		global $smcFunc;
		return strlen(preg_replace(\'~' . $ent_list . '|.~u' . '\', \'_\', ' . implode('$string', $ent_check) . '));'),
	'strpos' => create_function('$haystack, $needle, $offset = 0', '
		global $smcFunc;
		$haystack_arr = preg_split(\'~(&#' . (empty($modSettings['disableEntityCheck']) ? '\d{1,7}' : '021') . ';|&quot;|&amp;|&lt;|&gt;|&nbsp;|.)~' . 'u' . '\', ' . implode('$haystack', $ent_check) . ', -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		$haystack_size = count($haystack_arr);
		if (strlen($needle) === 1)
		{
			$result = array_search($needle, array_slice($haystack_arr, $offset));
			return is_int($result) ? $result + $offset : false;
		}
		else
		{
			$needle_arr = preg_split(\'~(&#' . (empty($modSettings['disableEntityCheck']) ? '\d{1,7}' : '021') . ';|&quot;|&amp;|&lt;|&gt;|&nbsp;|.)~' . 'u' . '\',  ' . implode('$needle', $ent_check) . ', -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
			$needle_size = count($needle_arr);

			$result = array_search($needle_arr[0], array_slice($haystack_arr, $offset));
			while (is_int($result))
			{
				$offset += $result;
				if (array_slice($haystack_arr, $offset, $needle_size) === $needle_arr)
					return $offset;
				$result = array_search($needle_arr[0], array_slice($haystack_arr, ++$offset));
			}
			return false;
		}'),
	'substr' => create_function('$string, $start, $length = null', '
		global $smcFunc;
		$ent_arr = preg_split(\'~(&#' . (empty($modSettings['disableEntityCheck']) ? '\d{1,7}' : '021') . ';|&quot;|&amp;|&lt;|&gt;|&nbsp;|.)~' . 'u' . '\', ' . implode('$string', $ent_check) . ', -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		var_dump($ent_arr);
		return $length === null ? implode(\'\', array_slice($ent_arr, $start)) : implode(\'\', array_slice($ent_arr, $start, $length));'),
	'strtolower' => (function_exists('mb_strtolower') ? create_function('$string', '
		return mb_strtolower($string, \'UTF-8\');') : create_function('$string', '
		global $sourcedir;
		require_once($sourcedir . \'/lib/Subs-Charset.php\');
		return utf8_strtolower($string);')),
	'strtoupper' => (function_exists('mb_strtoupper') ? create_function('$string', '
		return mb_strtoupper($string, \'UTF-8\');') : create_function('$string', '
		global $sourcedir;
		require_once($sourcedir . \'/lib/Subs-Charset.php\');
		return utf8_strtoupper($string);')),
	'truncate' => create_function('$string, $length', (empty($modSettings['disableEntityCheck']) ? '
		global $smcFunc;
		$string = ' . implode('$string', $ent_check) . ';' : '') . '
		preg_match(\'~^(' . $ent_list . '|.){\' . $smcFunc[\'strlen\'](substr($string, 0, $length)) . \'}~'.  'u' . '\', $string, $matches);
		$string = $matches[0];
		while (strlen($string) > $length)
			$string = preg_replace(\'~(?:' . $ent_list . '|.)$~'.  'u' . '\', \'\', $string);
		return $string;'),
	'ucfirst' => create_function('$string', '
		global $smcFunc;
		return $smcFunc[\'strtoupper\']($smcFunc[\'substr\']($string, 0, 1)) . $smcFunc[\'substr\']($string, 1);'),
	'ucwords' => create_function('$string', '
		global $smcFunc;
		$words = preg_split(\'~([\s\r\n\t]+)~\', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
		for ($i = 0, $n = count($words); $i < $n; $i += 2)
			$words[$i] = $smcFunc[\'ucfirst\']($words[$i]);
		return implode(\'\', $words);'),
);

// Map some database specific functions, only do this once.
if (!isset($smcFunc['db_fetch_assoc']) || $smcFunc['db_fetch_assoc'] != 'mysql_fetch_assoc')
	$smcFunc += array(
		'db_query' => 'smf_db_query_compat',
		'db_quote' => 'smf_db_quote',
		'db_fetch_assoc' => 'mysql_fetch_assoc',
		'db_fetch_row' => 'mysql_fetch_row',
		'db_free_result' => 'mysql_free_result',
		'db_insert' => 'smf_db_insert',
		'db_insert_id' => 'smf_db_insert_id',
		'db_num_rows' => 'mysql_num_rows',
		'db_data_seek' => 'mysql_data_seek',
		'db_num_fields' => 'mysql_num_fields',
		'db_server_info' => 'mysql_get_server_info',
		'db_affected_rows' => 'smf_db_affected_rows',
		'db_transaction' => 'smf_db_transaction',
		'db_select_db' => 'mysql_select_db',
		'db_title' => 'MySQL',
		'db_case_sensitive' => false,
	);

if (!isset($smcFunc['db_backup_table']) || $smcFunc['db_backup_table'] != 'smf_db_backup_table')
	$smcFunc += array(
		'db_backup_table' => 'smf_db_backup_table',
		'db_optimize_table' => 'smf_db_optimize_table',
		'db_insert_sql' => 'smf_db_insert_sql',
		'db_table_sql' => 'smf_db_table_sql',
		'db_list_tables' => 'smf_db_list_tables',
		'db_get_version' => 'smf_db_get_version',
	);

if (!isset($smcFunc['db_create_table']) || $smcFunc['db_create_table'] != 'smf_db_create_table')
	$smcFunc += array(
		'db_add_column' => 'smf_db_add_column',
		'db_add_index' => 'smf_db_add_index',
		'db_calculate_type' => 'smf_db_calculate_type',
		'db_change_column' => 'smf_db_change_column',
		'db_create_table' => 'smf_db_create_table',
		'db_drop_table' => 'smf_db_drop_table',
		'db_table_structure' => 'smf_db_table_structure',
		'db_list_columns' => 'smf_db_list_columns',
		'db_list_indexes' => 'smf_db_list_indexes',
		'db_remove_column' => 'smf_db_remove_column',
		'db_remove_index' => 'smf_db_remove_index',
	);

if (!isset($smcFunc['db_search_query']) || $smcFunc['db_search_query'] != 'smf_db_query')
	$smcFunc += array(
		'db_search_query' => 'smf_db_query',
		'db_search_support' => 'smf_db_search_support',
		'db_create_word_search' => 'smf_db_create_word_search',
		'db_support_ignore' => true,
	);
?>
