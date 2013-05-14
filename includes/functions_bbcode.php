<?php
/**
*
* @package Icy Phoenix
* @version $Id$
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('IN_ICYPHOENIX'))
{
	die('Hacking attempt');
}

/**
* For display of custom parsed text on user-facing pages
* Expects $text to be the value directly from the database (stored value)
*/
function generate_text_for_display($text, $only_smileys = false, $censor = true, $acro_autolinks = false, $forum_id = '999999')
{
	global $bbcode, $config, $user;

	if (empty($text))
	{
		return '';
	}

	if (defined('IS_ICYPHOENIX') && $censor)
	{
		$text = censor_text($text);
	}

	if (!class_exists('bbcode') || empty($bbcode))
	{
		include_once(IP_ROOT_PATH . 'includes/bbcode.' . PHP_EXT);
	}

	if (empty($bbcode))
	{
		$bbcode = new bbcode();
		if (!$user->data['session_logged_in'])
		{
			$user->data['user_allowhtml'] = $config['allow_html'] ? true : false;
			$user->data['user_allowbbcode'] = $config['allow_bbcode'] ? true : false;
			$user->data['user_allowsmile'] = $config['allow_smilies'] ? true : false;
		}
		$bbcode->allow_html = ($user->data['user_allowhtml'] && $config['allow_html']) ? true : false;
		$bbcode->allow_bbcode = ($user->data['user_allowbbcode'] && $config['allow_bbcode']) ? true : false;
		$bbcode->allow_smilies = ($user->data['user_allowsmile'] && $config['allow_smilies']) ? true : false;
	}

	if ($only_smileys)
	{
		$text = $bbcode->parse_only_smilies($text);
	}
	else
	{
		$text = $bbcode->parse($text);
		if ($acro_autolinks)
		{
			$text = $bbcode->acronym_pass($text);
			$text = $bbcode->autolink_text($text, $forum_id);
		}
	}

	return $text;
}

/*
* Generate a single row of smileys
* Moved here from functions_post to optimize viewtopic and remove the full include of functions_post
*/
function generate_smilies_row()
{
	global $db, $cache, $config, $template;

	$max_smilies = (!empty($config['smilie_single_row']) ? intval($config['smilie_single_row']) : 20);

	$sql = "SELECT emoticon, code, smile_url FROM " . SMILIES_TABLE . " GROUP BY smile_url ORDER BY smilies_order LIMIT " . $max_smilies;
	$result = $db->sql_query($sql, 0, 'smileys_');

	$host = extract_current_hostname();

	$orig = array();
	$repl = array();
	while ($row = $db->sql_fetchrow($result))
	{
		$template->assign_block_vars('smilies', array(
			'CODE' => $row['code'],
			'URL' => 'http://' . $host . $config['script_path'] . $config['smilies_path'] . '/' . $row['smile_url'],
			'DESC' => htmlspecialchars($row['emoticon'])
			)
		);
	}
	$db->sql_freeresult($result);
}

/*
* This function turns HTML into text... strips tags, comments spanning multiple lines including CDATA, and anything else that gets in it's way.
*/
function html2txt($document)
{
	$search = array(
						'@<script[^>]*?>.*?</script>@si',	// Strip out javascript
						'@<[\/\!]*?[^<>]*?>@si',					// Strip out HTML tags
						'@<style[^>]*?>.*?</style>@siU',	// Strip style tags properly
						'@<![\s\S]*?--[ \t\n\r]*>@'				// Strip multi-line comments including CDATA
					);
	$text = preg_replace($search, '', $document);
	return $text;
}

/*
* Convert newline to paragraph
*/
function nl2any($text, $tag = 'p', $feed = '')
{
	// making tags
	$start_tag = "<$tag" . ($feed ? ' ' . $feed : '') . '>';
	$end_tag = "</$tag>";

	// exploding string to lines
	$lines = preg_split('`[\n\r]+`', trim($text));

	// making new string
	$string = '';
	foreach($lines as $line)
	$string .= "$start_tag$line$end_tag\n";

	return $string;
}

/*
* Convert paragraphs to newline
*/
function any2nl($text, $tag = 'p')
{
	//exploding
	preg_match_all("`<" . $tag . "[^>]*>(.*)</" . $tag . ">`Ui", $text, $results);
	// reimploding without tags
	return implode("\n", array_filter($results[1]));
}

/*
* Convert BR to newline
*/
function br2nl($text, $remove_linebreaks = false)
{
	if ($remove_linebreaks)
	{
		$text = preg_replace("/(\r\n|\n|\r)/", "", $text);
	}
	return preg_replace("=<br */?>=i", "\n", $text);
}

/*
* Convert newline to BR
*/
function nl2br_mg($text)
{
	/*
	$text = preg_replace("/\r\n/", "\n", $text);
	$text = str_replace('<br />', "\n", $text);
	*/
	$text = preg_replace(array("/<br \/>\r\n/", "/<br>\r\n/", "/(\r\n|\n|\r)/"), array("\r\n", "\r\n", "<br />\r\n"), $text);
	return $text;
}

?>