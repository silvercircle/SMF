<?php

function fnotes_autoloader()
{
	return new FootNotes();
}

class FootNotes extends EoS_Plugin
{
	protected $productShortName = 'fnotes';
	protected $installableHooks = array(
		'parse_bbc_after' => array('file' => 'main.php', 'callable' => 'FootNotes::parse')
	);
	
	public function __construct() { parent::__construct(); }


/*
 * this is only for testing new integration hooks in parse_bbc()
 *
 * It is taken from the footnotes modification by Nao and serves as a sample
 * addon for the new hook system.
 */
	public static function parse(&$message, &$parse_tags, &$smileys)
	{
		if (stripos($message, '[fn]') !== false && (empty($parse_tags) || in_array('nb', $parse_tags)) && (!isset($_REQUEST['action']) || $_REQUEST['action'] != 'jseditor')) {
			preg_match_all('~\s*\[fn]((?>[^[]|\[(?!/?nb])|(?R))+?)\[/fn\]~i', $message, $matches, PREG_SET_ORDER);
	        $feet = 0;
			if (count($matches) > 0) {
				$f = 0;
				global $addnote;
				if (is_null($addnote))
					$addnote = array();
				foreach ($matches as $m) {
					$my_pos = $end_blockquote = strpos($message, $m[0]);
					$message = substr_replace($message, '<a class="fnote_ref" id="footlink' . ++$feet . '" href="#footnote' . $feet . '">[' . ++$f . ']</a>', $my_pos, strlen($m[0]));
					$addnote[$feet] = array($feet, $f, $m[1]);

					while ($end_blockquote !== false) {
						$end_blockquote = strpos($message, '</blockquote>', $my_pos);
						if ($end_blockquote === false)
							continue;

						$start_blockquote = strpos($message, '<blockquote', $my_pos);
						if ($start_blockquote !== false && $start_blockquote < $end_blockquote)
							$my_pos = $end_blockquote + 1;
						else {
							$message = substr_replace($message, '<foot:' . $feet . '>', $end_blockquote, 0);
							break;
						}
					}

					if ($end_blockquote === false)
						$message .= '<foot:' . $feet . '>';
				}

				$message = preg_replace_callback('~(?:<foot:\d+>)+~', create_function('$match', '
					global $addnote;
					$msg = \'<table class="fnotes">\';
					preg_match_all(\'~<foot:(\d+)>~\', $match[0], $mat);
					foreach ($mat[1] as $note)
					{
						$n = &$addnote[$note];
						$msg .= \'<tr><td class="fnote"><a id="footnote\' . $n[0] . \'" href="#footlink\' . $n[0] . \'">&nbsp;\' . $n[1] . \'.&nbsp;</a></td><td class="fnote_content">\'
							 . (stripos($n[2], \'[nb]\', 1) === false ? $n[2] : parse_bbc($n[2])) . \'</td></tr>\';
					}
					return $msg . \'</table>\';'), $message);
			}
		}
	}
}
?>