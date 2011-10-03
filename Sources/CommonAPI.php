<?php
/**
 * %%@productname@%%
 * @copyright 2011 Alex Vie silvercircle(AT)gmail(DOT)com
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright:	2011 Simple Machines (http://www.simplemachines.org)
 * license:  	BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version %%@productversion@%%
 *
 * this is what once was in $smcFunc[], a bit simplified for utf-8 only and entity check
 * always enforced.
 */
class commonAPI {

	private static $ent_list = '&(#\d{1,7}|quot|amp|lt|gt|nbsp);';
	private static $space_chars = '\x{A0}\x{AD}\x{2000}-\x{200F}\x{201F}\x{202F}\x{3000}\x{FEFF}';

	private static function ent_check($string)
	{
		return(preg_replace('~(&#(\d{1,7}|x[0-9a-fA-F]{1,6});)~e', self::entity_fix($string), $string));
	}

	private static function entity_fix($string)
	{
		$num = substr($string, 0, 1) === 'x' ? hexdec(substr($string, 1)) : (int) $string;
		return $num < 0x20 || $num > 0x10FFFF || ($num >= 0xD800 && $num <= 0xDFFF) || $num === 0x202E || $num === 0x202D ? '' : '&#' . $num . ';';
	}

	public static function ucwords($string)
	{
		$words = preg_split('~([\s\r\n\t]+)~', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
		for ($i = 0, $n = count($words); $i < $n; $i += 2)
			$words[$i] = self::ucfirst($words[$i]);

		return implode('', $words);
	}

	public static function ucfirst($string)
	{
		return self::strtoupper(self::substr($string, 0, 1)) . self::substr($string, 1);
	}

	public static function substr($string, $start, $length = 0)
	{
		$ent_arr = preg_split('~(&#' . ('\d{1,7}') . ';|&quot;|&amp;|&lt;|&gt;|&nbsp;|.)~u', self::ent_check($string), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		return $length === null ? implode('', array_slice($ent_arr, $start)) : implode('', array_slice($ent_arr, $start, $length));
	}

	public static function strtolower($string)
	{
		global $sourcedir;

		if(function_exists('mb_strtolower'))
			return mb_strtolower($string, 'UTF-8');

		require_once($sourcedir . '/Subs-Charset.php');
		return utf8_strtolower($string);
	}

	public static function strtoupper($string)
	{
		global $sourcedir;

		if(function_exists('mb_strtoupper'))
			return mb_strtoupper($string, 'UTF-8');

		require_once($sourcedir . '/Subs-Charset.php');
		return utf8_strtoupper($string);
	}

	public static function truncate($string, $length)
	{
		$string = self::ent_check($string);

		preg_match('~^(' . self::$ent_list . '|.){' . self::strlen(substr($string, 0, $length)) . '}~u', $string, $matches);
		$string = $matches[0];
		while (strlen($string) > $length)
			$string = preg_replace('~(?:' . self::$ent_list . '|.)$~u', '', $string);
		return $string;
	}

	public static function strlen($string)
	{
		return strlen(preg_replace('~' . self::$ent_list . '|.~u', '_', self::ent_check($string)));
	}

	public static function htmltrim($string)
	{
		return preg_replace('~^(?:[ \t\n\r\x0B\x00' . self::$space_chars . ']|&nbsp;)+|(?:[ \t\n\r\x0B\x00' . self::$space_chars . ']|&nbsp;)+$~u', '', self::ent_check($string));
	}

	public static function htmlspecialchars($string, $quote_style = ENT_COMPAT, $charset = 'UTF-8')
	{
		return preg_replace(strtr('~(&#(\d{1,7}|x[0-9a-fA-F]{1,6});)~e', array('&' => '&amp;')), self::entity_fix($string), htmlspecialchars($string, $quote_style, 'UTF-8'));
	}

	public static function strpos($haystack, $needle, $offset = 0)
	{
		$haystack_arr = preg_split('~(&#\d{1,7}' . ';|&quot;|&amp;|&lt;|&gt;|&nbsp;|.)~u', self::ent_check($haystack), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		$haystack_size = count($haystack_arr);
		if (strlen($needle) === 1)
		{
			$result = array_search($needle, array_slice($haystack_arr, $offset));
			return is_int($result) ? $result + $offset : false;
		}
		else
		{
			$needle_arr = preg_split('~(&#\d{1,7}' . ';|&quot;|&amp;|&lt;|&gt;|&nbsp;|.)~u',  self::ent_check($needle), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
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
		}
	}
}
?>
