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

if (!defined('IN_INSTALL'))
{
	define('IN_INSTALL', true);
}

/**
* Some Icy Phoenix Functions
*/
class ip_functions
{
	/*
	* Remove variables created by register_globals from the global scope
	* Thanks to Matt Kavanagh
	*/
	function deregister_globals()
	{
		$not_unset = array(
			'GLOBALS' => true,
			'_GET' => true,
			'_POST' => true,
			'_COOKIE' => true,
			'_REQUEST' => true,
			'_SERVER' => true,
			'_SESSION' => true,
			'_ENV' => true,
			'_FILES' => true,
		);

		// Not only will array_merge and array_keys give a warning if
		// a parameter is not an array, array_merge will actually fail.
		// So we check if _SESSION has been initialised.
		if (!isset($_SESSION) || !is_array($_SESSION))
		{
			$_SESSION = array();
		}

		// Merge all into one extremely huge array; unset this later
		$input = array_merge(
			array_keys($_GET),
			array_keys($_POST),
			array_keys($_COOKIE),
			array_keys($_SERVER),
			array_keys($_SESSION),
			array_keys($_ENV),
			array_keys($_FILES)
		);

		foreach ($input as $varname)
		{
			if (isset($not_unset[$varname]))
			{
				// Hacking attempt. No point in continuing unless it's a COOKIE
				if ($varname !== 'GLOBALS' || isset($_GET['GLOBALS']) || isset($_POST['GLOBALS']) || isset($_SERVER['GLOBALS']) || isset($_SESSION['GLOBALS']) || isset($_ENV['GLOBALS']) || isset($_FILES['GLOBALS']))
				{
					exit;
				}
				else
				{
					$cookie = &$_COOKIE;
					while (isset($cookie['GLOBALS']))
					{
						foreach ($cookie['GLOBALS'] as $registered_var => $value)
						{
							if (!isset($not_unset[$registered_var]))
							{
								unset($GLOBALS[$registered_var]);
							}
						}
						$cookie = &$cookie['GLOBALS'];
					}
				}
			}

			unset($GLOBALS[$varname]);
		}

		unset($input);
	}

	/**
	* Read file content
	*/
	function file_read($filename)
	{
		$handle = @fopen($filename, 'r');
		$content = @fread($handle, filesize($filename));
		@fclose($handle);
		return $content;
	}

	/**
	* Write content to file
	*/
	function file_write($filename, $content)
	{
		$handle = @fopen($filename, 'w');
		$result = @fwrite($handle, $content, strlen($content));
		@fclose($handle);
		return $result;
	}

	/**
	* Testing File Creation
	*/
	function file_creation($path)
	{
		$test_file = $path . 'icy_phoenix_testing_write_access_permissions.test';

		// Check if the test file already exists...
		if (file_exists($test_file))
		{
			if (!@unlink($test_file))
			{
				// It seems we haven't deleted it... try to change permissions
				if (!@chmod($test_file, 0666))
				{
					return false;
				}
				else
				{
					if (!@unlink($test_file))
					{
						return false;
					}
				}
			}
		}

		// Attempt to create a new file...
		if (!@touch($test_file))
		{
			return false;
		}
		else
		{
			if (!@chmod($test_file, 0666))
			{
				if (!@unlink($test_file))
				{
					return false;
				}
				else
				{
					return true;
				}
			}
			else
			{
				// We really want to make sure...
				if (file_exists($test_file))
				{
					if (!@unlink($test_file))
					{
						return false;
					}
					else
					{
						return true;
					}
				}
				else
				{
					return false;
				}
			}
		}
		return true;
	}

	/**
	* Check MEM Limit
	*/
	function check_mem_limit()
	{
		$mem_limit = @ini_get('memory_limit');
		if (!empty($mem_limit))
		{
			$unit = strtolower(substr($mem_limit, -1, 1));
			$mem_limit = (int) $mem_limit;

			if ($unit == 'k')
			{
				$mem_limit = floor($mem_limit / 1024);
			}
			elseif ($unit == 'g')
			{
				$mem_limit *= 1024;
			}
			elseif (is_numeric($unit))
			{
				$mem_limit = floor((int) ($mem_limit . $unit) / 1048576);
			}
			$mem_limit = max(128, $mem_limit) . 'M';
		}
		else
		{
			$mem_limit = '128M';
		}
		return $mem_limit;
	}

	function create_server_url()
	{
		// usage: $server_url = create_server_url();
		global $config;

		$server_protocol = ($config['cookie_secure']) ? 'https://' : 'http://';
		$server_name = preg_replace('#^\/?(.*?)\/?$#', '\1', trim($config['server_name']));
		$server_port = ($config['server_port'] <> 80) ? ':' . trim($config['server_port']) : '';
		$script_name = preg_replace('/^\/?(.*?)\/?$/', '\1', trim($config['script_path']));
		$script_name = ($script_name == '') ? '' : '/' . $script_name;
		$server_url = $server_protocol . $server_name . $server_port . $script_name;
		while(substr($server_url, -1, 1) == '/')
		{
			$server_url = substr($server_url, 0, -1);
		}
		$server_url = $server_url . '/';

		//$server_url = 'icyphoenix.com/';

		return $server_url;
	}

	function ip_realpath($path)
	{
		return (!@function_exists('realpath') || !@realpath(IP_ROOT_PATH . 'includes/functions.' . PHP_EXT)) ? $path : @realpath($path);
	}

	/**
	* Set variable, used by {@link request_var the request_var function}
	* function backported from phpBB3 - Olympus
	* @access private
	*/
	function set_var(&$result, $var, $type, $multibyte = false)
	{
		settype($var, $type);
		$result = $var;

		if ($type == 'string')
		{
			$result = trim(htmlspecialchars(str_replace(array("\r\n", "\r"), array("\n", "\n"), $result), ENT_COMPAT, 'UTF-8'));

			if (!empty($result))
			{
				// Make sure multibyte characters are wellformed
				if ($multibyte)
				{
					if (!preg_match('/^./u', $result))
					{
						$result = '';
					}
				}
				else
				{
					// no multibyte, allow only ASCII (0-127)
					$result = preg_replace('/[\x80-\xFF]/', '?', $result);
				}
			}

			$result = (defined('STRIP') && STRIP) ? stripslashes($result) : $result;
		}
	}

	/**
	* Used to get passed variable
	* function backported from phpBB3 - Olympus
	*/
	function request_var($var_name, $default, $multibyte = false, $cookie = false)
	{
		if (!$cookie && isset($_COOKIE[$var_name]))
		{
			if (!isset($_GET[$var_name]) && !isset($_POST[$var_name]))
			{
				return (is_array($default)) ? array() : $default;
			}
			$_REQUEST[$var_name] = isset($_POST[$var_name]) ? $_POST[$var_name] : $_GET[$var_name];
		}

		if (!isset($_REQUEST[$var_name]) || (is_array($_REQUEST[$var_name]) && !is_array($default)) || (is_array($default) && !is_array($_REQUEST[$var_name])))
		{
			return (is_array($default)) ? array() : $default;
		}

		$var = $_REQUEST[$var_name];
		if (!is_array($default))
		{
			$type = gettype($default);
		}
		else
		{
			list($key_type, $type) = each($default);
			$type = gettype($type);
			$key_type = gettype($key_type);
			if ($type == 'array')
			{
				reset($default);
				$default = current($default);
				list($sub_key_type, $sub_type) = each($default);
				$sub_type = gettype($sub_type);
				$sub_type = ($sub_type == 'array') ? 'NULL' : $sub_type;
				$sub_key_type = gettype($sub_key_type);
			}
		}

		if (is_array($var))
		{
			$_var = $var;
			$var = array();

			foreach ($_var as $k => $v)
			{
				$this->set_var($k, $k, $key_type);
				if ($type == 'array' && is_array($v))
				{
					foreach ($v as $_k => $_v)
					{
						if (is_array($_v))
						{
							$_v = null;
						}
						$this->set_var($_k, $_k, $sub_key_type);
						$this->set_var($var[$k][$_k], $_v, $sub_type, $multibyte);
					}
				}
				else
				{
					if ($type == 'array' || is_array($v))
					{
						$v = null;
					}
					$this->set_var($var[$k], $v, $type, $multibyte);
				}
			}
		}
		else
		{
			$this->set_var($var, $var, $type, $multibyte);
		}

		return $var;
	}

	//
	// Append $SID to a url. Borrowed from phplib and modified. This is an
	// extra routine utilised by the session code above and acts as a wrapper
	// around every single URL and form action. If you replace the session
	// code you must include this routine, even if it's empty.
	//
	function append_sid($url, $non_html_amp = false, $char_conversion = false)
	{
		global $SID;

		if (!empty($SID) && !preg_match('#sid=#', $url))
		{
			if ($char_conversion == true)
			{
				$url .= ((strpos($url, '?') !== false) ? '%26' : '?') . $SID;
			}
			else
			{
				$url .= ((strpos($url, '?') !== false) ? (($non_html_amp) ? '&' : '&amp;') : '?') . $SID;
			}
		}

		return $url;
	}

	// Guess an initial language ... borrowed from phpBB 2.2 it's not perfect,
	// really it should do a straight match first pass and then try a "fuzzy"
	// match on a second pass instead of a straight "fuzzy" match.
	function guess_lang()
	{

		// The order here _is_ important, at least for major_minor matches.
		// Don't go moving these around without checking with me first - psoTFX
		$match_lang = array(
			'arabic'											=> 'ar([_-][a-z]+)?',
			'bulgarian'										=> 'bg',
			'catalan'											=> 'ca',
			'czech'												=> 'cs',
			'danish'											=> 'da',
			'german'											=> 'de([_-][a-z]+)?',
			'english'											=> 'en([_-][a-z]+)?',
			'estonian'										=> 'et',
			'finnish'											=> 'fi',
			'french'											=> 'fr([_-][a-z]+)?',
			'greek'												=> 'el',
			'spanish_argentina'						=> 'es[_-]ar',
			'spanish'											=> 'es([_-][a-z]+)?',
			'gaelic'											=> 'gd',
			'galego'											=> 'gl',
			'gujarati'										=> 'gu',
			'hebrew'											=> 'he',
			'hindi'												=> 'hi',
			'croatian'										=> 'hr',
			'hungarian'										=> 'hu',
			'icelandic'										=> 'is',
			'indonesian'									=> 'id([_-][a-z]+)?',
			'italian'											=> 'it([_-][a-z]+)?',
			'japanese'										=> 'ja([_-][a-z]+)?',
			'korean'											=> 'ko([_-][a-z]+)?',
			'latvian'											=> 'lv',
			'lithuanian'									=> 'lt',
			'macedonian'									=> 'mk',
			'dutch'												=> 'nl([_-][a-z]+)?',
			'norwegian'										=> 'no',
			'punjabi'											=> 'pa',
			'polish'											=> 'pl',
			'portuguese_brazil'						=> 'pt[_-]br',
			'portuguese'									=> 'pt([_-][a-z]+)?',
			'romanian'										=> 'ro([_-][a-z]+)?',
			'russian'											=> 'ru([_-][a-z]+)?',
			'slovenian'										=> 'sl([_-][a-z]+)?',
			'albanian'										=> 'sq',
			'serbian'											=> 'sr([_-][a-z]+)?',
			'slovak'											=> 'sv([_-][a-z]+)?',
			'swedish'											=> 'sv([_-][a-z]+)?',
			'thai'												=> 'th([_-][a-z]+)?',
			'turkish'											=> 'tr([_-][a-z]+)?',
			'ukranian'										=> 'uk([_-][a-z]+)?',
			'urdu'												=> 'ur',
			'viatnamese'									=> 'vi',
			'chinese_traditional_taiwan'	=> 'zh[_-]tw',
			'chinese_simplified'					=> 'zh',
		);

		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			$accept_lang_ary = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			for ($i = 0; $i < sizeof($accept_lang_ary); $i++)
			{
				@reset($match_lang);
				while (list($lang, $match) = each($match_lang))
				{
					if (preg_match('#' . $match . '#i', trim($accept_lang_ary[$i])))
					{
						if (@file_exists(@$this->ip_realpath('language/lang_' . $lang)))
						{
							return $lang;
						}
					}
				}
			}
		}
		return 'english';
	}
}

/**
* Some functions used to process text
*/
class mg_functions
{
	function html_text_cleaning($text)
	{
		$look_up_array = array(
			"&agrave;",
			"&egrave;",
			"&igrave;",
			"&ograve;",
			"&ugrave;",
			"&eacute;",
			"&nbsp;",
		);

		$replacement_array = array(
			"à",
			"è",
			"ì",
			"ò",
			"ù",
			"é",
			" ",
		);

		$text = str_replace($look_up_array, $replacement_array, $text);

		return $text;
	}

	function html_text_format($text)
	{
		$look_up_array = array(
			"à",
			"è",
			"ì",
			"ò",
			"ù",
			"é",
			" ",
		);

		$replacement_array = array(
			"&agrave;",
			"&egrave;",
			"&igrave;",
			"&ograve;",
			"&ugrave;",
			"&eacute;",
			"&nbsp;",
		);

		$text = str_replace($look_up_array, $replacement_array, $text);

		return $text;
	}

	/*
	* Creates a short url to be used in replace, without server_protocol
	*/
	function create_short_server_url()
	{
		// usage: $server_url = create_short_server_url();
		global $config;

		if (!empty($config['short_site_url']))
		{
			return $config['short_site_url'];
		}

		$server_protocol = ($config['cookie_secure']) ? 'https://' : 'http://';
		$server_name = preg_replace('#^\/?(.*?)\/?$#', '\1', trim($config['server_name']));
		$server_port = ($config['server_port'] <> 80) ? ':' . trim($config['server_port']) : '';
		$script_name = preg_replace('/^\/?(.*?)\/?$/', '\1', trim($config['script_path']));
		$script_name = ($script_name == '') ? '' : '/' . $script_name;
		//$server_url = $server_protocol . $server_name . $server_port . $script_name;
		$server_url = $server_name . $server_port . $script_name;
		while(substr($server_url, -1, 1) == '/')
		{
			$server_url = substr($server_url, 0, -1);
		}
		$server_url = $server_url . '/';

		//$server_url = 'icyphoenix.com/';

		$config['short_site_url'] = $server_url;
		return $server_url;
	}

}

/*
* Some SQL functions
* Borrowed from phpBB package
*/
class ip_sql
{
	/**
	* Execute SQL and store errors
	*/
	function _sql($sql, &$errored, &$error_ary, $echo_dot = true)
	{
		global $db;
		if (!($result = $db->sql_query($sql)))
		{
			$errored = true;
			$error_ary['sql'][] = (is_array($sql)) ? $sql[$i] : $sql;
			$error_ary['error_code'][] = $db->sql_error();
		}
		if ($echo_dot)
		{
			echo ". \n";
			flush();
		}
		return $result;
	}

	/**
	* Get config values from DB
	*/
	function get_config_value($config_name)
	{
		global $db, $table_prefix;
		$sql = "SELECT config_value
						FROM " . CONFIG_TABLE . "
						WHERE config_name = '" . $config_name . "'
						LIMIT 1";
		if (!($result = $db->sql_query($sql)))
		{
			$config_value = false;
		}
		else
		{
			$row = $db->sql_fetchrow($result);
			$config_value = !empty($row['config_value']) ? $row['config_value'] : '';
		}
		return $config_value;
	}

	//
	// remove_comments will strip the sql comment lines out of an uploaded sql file
	// specifically for mssql and postgres type files in the install....
	//
	function remove_comments(&$output)
	{
		$lines = explode("\n", $output);
		$output = "";

		// try to keep mem. use down
		$linecount = sizeof($lines);

		$in_comment = false;
		for($i = 0; $i < $linecount; $i++)
		{
			if( preg_match("/^\/\*/", preg_quote($lines[$i])) )
			{
				$in_comment = true;
			}

			if( !$in_comment )
			{
				$output .= $lines[$i] . "\n";
			}

			if( preg_match("/\*\/$/", preg_quote($lines[$i])) )
			{
				$in_comment = false;
			}
		}

		unset($lines);
		return $output;
	}

	//
	// remove_remarks will strip the sql comment lines out of an uploaded sql file
	//
	function remove_remarks($sql)
	{
		$lines = explode("\n", $sql);

		// try to keep mem. use down
		$sql = "";

		$linecount = sizeof($lines);
		$output = "";

		for ($i = 0; $i < $linecount; $i++)
		{
			if (($i != ($linecount - 1)) || (strlen($lines[$i]) > 0))
			{
				if ($lines[$i][0] != "#")
				{
					$output .= $lines[$i] . "\n";
				}
				else
				{
					$output .= "\n";
				}
				// Trading a bit of speed for lower mem. use here.
				$lines[$i] = "";
			}
		}

		return $output;

	}

	//
	// split_sql_file will split an uploaded sql file into single sql statements.
	// Note: expects trim() to have already been run on $sql.
	//
	function split_sql_file($sql, $delimiter)
	{
		// Split up our string into "possible" SQL statements.
		$tokens = explode($delimiter, $sql);

		// try to save mem.
		$sql = "";
		$output = array();

		// we don't actually care about the matches preg gives us.
		$matches = array();

		// this is faster than calling sizeof($oktens) every time thru the loop.
		$token_count = sizeof($tokens);
		for ($i = 0; $i < $token_count; $i++)
		{
			// Don't wanna add an empty string as the last thing in the array.
			if (($i != ($token_count - 1)) || (strlen($tokens[$i] > 0)))
			{
				// This is the total number of single quotes in the token.
				$total_quotes = preg_match_all("/'/", $tokens[$i], $matches);
				// Counts single quotes that are preceded by an odd number of backslashes,
				// which means they're escaped quotes.
				$escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$i], $matches);

				$unescaped_quotes = $total_quotes - $escaped_quotes;

				// If the number of unescaped quotes is even, then the delimiter did NOT occur inside a string literal.
				if (($unescaped_quotes % 2) == 0)
				{
					// It's a complete sql statement.
					$output[] = $tokens[$i];
					// save memory.
					$tokens[$i] = "";
				}
				else
				{
					// incomplete sql statement. keep adding tokens until we have a complete one.
					// $temp will hold what we have so far.
					$temp = $tokens[$i] . $delimiter;
					// save memory..
					$tokens[$i] = "";

					// Do we have a complete statement yet?
					$complete_stmt = false;

					for ($j = $i + 1; (!$complete_stmt && ($j < $token_count)); $j++)
					{
						// This is the total number of single quotes in the token.
						$total_quotes = preg_match_all("/'/", $tokens[$j], $matches);
						// Counts single quotes that are preceded by an odd number of backslashes,
						// which means they're escaped quotes.
						$escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$j], $matches);

						$unescaped_quotes = $total_quotes - $escaped_quotes;

						if (($unescaped_quotes % 2) == 1)
						{
							// odd number of unescaped quotes. In combination with the previous incomplete
							// statement(s), we now have a complete statement. (2 odds always make an even)
							$output[] = $temp . $tokens[$j];

							// save memory.
							$tokens[$j] = "";
							$temp = "";

							// exit the loop.
							$complete_stmt = true;
							// make sure the outer loop continues at the right point.
							$i = $j;
						}
						else
						{
							// even number of unescaped quotes. We still don't have a complete statement.
							// (1 odd and 1 even always make an odd)
							$temp .= $tokens[$j] . $delimiter;
							// save memory.
							$tokens[$j] = "";
						}

					} // for
				} // else
			}
		}

		return $output;
	}

	/**
	* Converts the DB to UTF-8
	*/
	function convert_utf8($echo_results = false)
	{
		global $db, $dbname, $table_prefix;

		$db->sql_return_on_error(true);

		$sql = "ALTER DATABASE {$db->sql_escape($dbname)}
			CHARACTER SET utf8
			DEFAULT CHARACTER SET utf8
			COLLATE utf8_bin
			DEFAULT COLLATE utf8_bin";
		$db->sql_query($sql);

		$sql = "SHOW TABLES";
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			// This assignment doesn't work...
			//$table = $row[0];

			$current_item = each($row);
			$table = $current_item['value'];
			reset($row);

			$sql = "ALTER TABLE {$db->sql_escape($table)}
				DEFAULT CHARACTER SET utf8
				COLLATE utf8_bin";
			$db->sql_query($sql);

			if (!empty($echo_results))
			{
				echo("&bull;&nbsp;Table&nbsp;<b style=\"color: #dd2222;\">$table</b> converted to UTF-8<br />\n");
			}

			$sql = "SHOW FIELDS FROM {$db->sql_escape($table)}";
			$result_fields = $db->sql_query($sql);

			while ($row_fields = $db->sql_fetchrow($result_fields))
			{
				// These assignments don't work...
				/*
				$field_name = $row_fields[0];
				$field_type = $row_fields[1];
				$field_null = $row_fields[2];
				$field_key = $row_fields[3];
				$field_default = $row_fields[4];
				$field_extra = $row_fields[5];
				*/

				$field_name = $row_fields['Field'];
				$field_type = $row_fields['Type'];
				$field_null = $row_fields['Null'];
				$field_key = $row_fields['Key'];
				$field_default = $row_fields['Default'];
				$field_extra = $row_fields['Extra'];

				// Let's remove BLOB and BINARY for now...
				//if ((strpos(strtolower($field_type), 'char') !== false) || (strpos(strtolower($field_type), 'text') !== false) || (strpos(strtolower($field_type), 'blob') !== false) || (strpos(strtolower($field_type), 'binary') !== false))
				if ((strpos(strtolower($field_type), 'char') !== false) || (strpos(strtolower($field_type), 'text') !== false))
				{
					//$sql_fields = "ALTER TABLE {$db->sql_escape($table)} CHANGE " . $db->sql_escape($field_name) . " " . $db->sql_escape($field_name) . " " . $db->sql_escape($field_type) . " CHARACTER SET utf8 COLLATE utf8_bin";

					$sql_fields = "ALTER TABLE {$db->sql_escape($table)} CHANGE " . $db->sql_escape($field_name) . " " . $db->sql_escape($field_name) . " " . $db->sql_escape($field_type) . " CHARACTER SET utf8 COLLATE utf8_bin " . (($field_null != 'YES') ? "NOT " : "") . "NULL DEFAULT " . (($field_default != 'None') ? ((!empty($field_default) || !is_null($field_default)) ? (is_string($field_default) ? ("'" . $db->sql_escape($field_default) . "'") : $field_default) : (($field_null != 'YES') ? "''" : "NULL")) : "''");
					$db->sql_query($sql_fields);

					if (!empty($echo_results))
					{
						echo("\t&nbsp;&nbsp;&raquo;&nbsp;Field&nbsp;<b style=\"color: #4488aa;\">$field_name</b> (in table <b style=\"color: #009900;\">$table</b>) converted to UTF-8<br />\n");
					}
				}
			}

			if (!empty($echo_results))
			{
				echo("<br />\n");
				flush();
			}
		}

		$db->sql_return_on_error(false);
		return true;
	}

}


/**
* Icy Phoenix Template
*/
class ip_page
{

	var $tbl_h_l = '<table class="roundedtop" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td width="27" align="right" valign="bottom"><img class="topcorners" src="style/tbl/tbl_h_l.gif" width="27" height="29" alt="" /></td>';
	var $tbl_h_c = '<td class="roundedhc" width="100%" align="center">';
	var $tbl_h_r = '</td><td width="27" align="left" valign="bottom"><img class="topcorners" src="style/tbl/tbl_h_r.gif" width="27" height="29" alt="" /></td></tr></table>';

	var $tbl_f_l = '<table class="roundedbottom" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td width="4" align="right" valign="top"><img src="style/tbl/tbl_f_l.gif" width="4" height="3" alt="" /></td>';
	var $tbl_f_c = '<td class="roundedfc" width="100%" align="center">';
	var $tbl_f_r = '</td><td width="4" align="left" valign="top"><img src="style/tbl/tbl_f_r.gif" width="4" height="3" alt="" /></td></tr></table>';

	var $img_ok = '<img src="style/b_ok.png" alt="" title="" />';
	var $img_error = '<img src="style/b_cancel.png" alt="" title="" />';

	var $color_ok = '#228822';
	var $color_error = '#dd3333';

	var $color_blue = '#224488';
	var $color_green = '#228822';
	var $color_orange = '#ff5500';
	var $color_purple = '#880088';
	var $color_red = '#dd2222';

	function page_header($page_title, $content, $form_action = false, $write_form = true, $extra_css = false, $extra_js = false, $meta_refresh = '')
	{
		global $lang;
		$encoding_charset = !empty($lang['ENCODING']) ? $lang['ENCODING'] : 'UTF-8';

		echo('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n");
		echo('<html>' . "\n");
		echo('<head>' . "\n");
		echo('	<meta http-equiv="Content-Type" content="text/html; charset=' . $encoding_charset . '" />' . "\n");
		echo('	<meta http-equiv="content-style-type" content="text/css" />' . "\n");
		echo('	<meta name="author" content="Icy Phoenix Team" />' . "\n");
		if ($meta_refresh != '')
		{
			echo('	' . $meta_refresh . "\n");
		}
		echo('	<title>' . $page_title . ' :: Icy Phoenix</title>' . "\n");
		echo('	<link rel="stylesheet" href="style/style.css" type="text/css" />' . "\n");
		if (!empty($extra_css))
		{
			for ($i = 0; $i < sizeof($extra_css); $i++)
			{
				echo('	<link rel="stylesheet" href="' . $extra_css[$i] . '" type="text/css" />' . "\n");
			}
		}
		echo('	<link rel="shortcut icon" href="style/favicon.ico" />' . "\n");
		echo('	<script type="text/javascript" src="style/ip_scripts.js"></script>' . "\n");
		if (!empty($extra_js))
		{
			for ($i = 0; $i < sizeof($extra_js); $i++)
			{
				echo('	<script type="text/javascript" src="' . $extra_js[$i] . '"></script>' . "\n");
			}
		}
		echo('	<!--[if lt IE 7]>' . "\n");
		echo('	<script type="text/javascript" src="style/pngfix.js"></script>' . "\n");
		echo('	<![endif]-->' . "\n");
		echo('</head>' . "\n");
		echo('<body>' . "\n");
		echo('<span><a name="top" id="top"></a></span>' . "\n");
		echo('<div id="global-wrapper"><div id="wrapper"><div id="wrapper1"><div id="wrapper2"><div id="wrapper3"><div id="wrapper4"><div id="wrapper5"><div id="wrapper6"><div id="wrapper7"><div id="wrapper-inner">' . "\n");

		if ($write_form == true)
		{
			$form_action = $form_action ? $form_action : 'install.' . PHP_EXT;
			echo('<form action="' . $form_action . '" name="install" method="post">' . "\n");
		}

		echo('<table id="forumtable" cellspacing="0" cellpadding="0">' . "\n");

		echo('<tr>' . "\n");
		echo('	<td width="100%" colspan="3" valign="top">' . "\n");
		echo('		<div id="top_logo"><div style="margin-top:20px;margin-left:10px;"><a href="http://www.icyphoenix.com" title="Icy Phoenix"><img src="style/sitelogo_small.png" alt="Icy Phoenix" title="Icy Phoenix" /></a></div></div>' . "\n");
		echo('	</td>' . "\n");
		echo('</tr>' . "\n");

		echo('<tr>' . "\n");
		echo('	<td width="100%" class="forum-buttons" colspan="3">' . "\n");
		echo('	&nbsp;<img src="style/menu_sep.gif" alt="" />&nbsp;<a href="http://www.icyphoenix.com/">Icy Phoenix</a>&nbsp;<img src="style/menu_sep.gif" alt="" />&nbsp;' . "\n");
		echo('	</td>' . "\n");
		echo('</tr>' . "\n");

		echo('<tr>' . "\n");
		echo('	<td width="100%" colspan="3" style="padding-left:10px;padding-right:10px;">' . "\n");

		if (!empty($content))
		{
			echo('	<table class="forumline" width="100%" cellspacing="0" cellpadding="0">' . "\n");
			echo('		<tr><td class="row-header" colspan="2"><span>' . $page_title . '</span></td></tr>' . "\n");
			echo('		<tr><td class="row-post" colspan="2"><br /><div class="post-text">' . $content . '</div><br /></td></tr>' . "\n");
			echo('	</table>' . "\n");
		}
	}

	function page_footer($write_form = true)
	{
		echo('	</td>' . "\n");
		echo('</tr>' . "\n");
		echo('<tr>' . "\n");
		echo('	<td colspan="3">' . "\n");
		echo('	<div id="bottom_logo_ext">' . "\n");
		echo('	<div id="bottom_logo">' . "\n");
		echo('		<table class="empty-table" width="100%" cellspacing="0" cellpadding="0" border="0">' . "\n");
		echo('			<tr>' . "\n");
		echo('				<td nowrap="nowrap" class="min250" align="left"><span class="copyright">&nbsp;Powered by <a href="http://www.icyphoenix.com/" target="_blank">Icy Phoenix</a> based on <a href="http://www.phpbb.com/" target="_blank">phpBB</a></span></td>' . "\n");
		echo('				<td nowrap="nowrap" align="center">&nbsp;<br />&nbsp;</td>' . "\n");
		echo('				<td nowrap="nowrap" class="min250" align="right"><span class="copyright">Design by <a href="http://www.mightygorgon.com" target="_blank">Mighty Gorgon</a>&nbsp;</span></td>' . "\n");
		echo('			</tr>' . "\n");
		echo('		</table>' . "\n");
		echo('	</div>' . "\n");
		echo('	</div>' . "\n");
		echo('	</td>' . "\n");
		echo('</tr>' . "\n");
		echo('</table>' . "\n");
		if ($write_form == true)
		{
			echo('</form>' . "\n");
		}
		echo('</div></div></div></div></div></div></div></div></div></div>' . "\n");
		echo('<span><a name="bottom" id="bottom"></a></span>' . "\n");
		echo('</body>' . "\n");
		echo('</html>');
	}

	function box($bg_color, $color, $content)
	{
		$colors_array = array('blue', 'gray', 'green', 'orange', 'red', 'yellow');
		$bg_color = (in_array($bg_color, $colors_array) ? $bg_color : 'red');
		$color = (in_array($color, $colors_array) ? $color : 'red');
		echo('<div class="text_cont_center" style="width:400px;"><div class="text_' . $bg_color . '_cont"><span class="text_' . $color . '">' . $content . '</span></div></div>' . "\n");
	}

	function spoiler($spoiler_id, $content, $direct_output = true)
	{
		global $lang;

		$html = '';
		$html .= '<div class="spoiler">' . "\n";
		$html .= '<div class="code-header" id="spoilerhdr_' . $spoiler_id . '" style="position: relative;">' . $lang['Spoiler'] . ': [ <a href="javascript:void(0)" onclick="ShowHide(\'spoiler_' . $spoiler_id . '\', \'spoiler_h_' . $spoiler_id . '\', \'\'); ShowHide(\'spoilerhdr_' . $spoiler_id . '\', \'spoilerhdr_h_' . $spoiler_id . '\', \'\')">' . $lang['Show'] . '</a> ]</div>' . "\n";
		$html .= '<div class="code-header" id="spoilerhdr_h_' . $spoiler_id . '" style="position: relative; display: none;">' . $lang['Spoiler'] . ': [ <a href="javascript:void(0)" onclick="ShowHide(\'spoiler_' . $spoiler_id . '\', \'spoiler_h_' . $spoiler_id . '\', \'\'); ShowHide(\'spoilerhdr_' . $spoiler_id . '\', \'spoilerhdr_h_' . $spoiler_id . '\', \'\')">' . $lang['Hide'] . '</a> ]</div>' . "\n";
		$html .= '<div class="spoiler-content" id="spoiler_h_' . $spoiler_id . '" style="position: relative; display: none;">' . $content . '</div>' . "\n";
		$html .= '</div>' . "\n";
		if ($direct_output)
		{
			echo($html);
		}
		return($html);
	}

	function table_begin($header, $td_class = '', $colspan = 0, $spoiler_id = false)
	{
		$img_maximise = '';
		$img_minimise = '';
		if (!empty($spoiler_id))
		{
			$img_maximise = '<div class="max-min-right"><img src="style/switch_maximise.gif" onclick="ShowHide(\'' . $spoiler_id . '\',\'' . $spoiler_id . '_h\',\'' . $spoiler_id . '\');" alt="" />&nbsp;</div>';
			$img_minimise = '<div class="max-min-right"><img src="style/switch_minimise.gif" onclick="ShowHide(\'' . $spoiler_id . '\',\'' . $spoiler_id . '_h\',\'' . $spoiler_id . '\');" alt="" />&nbsp;</div>';
			echo('<div id="' . $spoiler_id . '_h" style="display: none;"><table class="forumline" width="100%" cellspacing="0" cellpadding="0"><tr><td class="row-header">' . $img_maximise . '<span>' . $header . '</span></td></tr></table></div>' . "\n");
			echo('<div id="' . $spoiler_id . '">' . "\n");
		}
		echo('<table class="forumline" width="100%" cellspacing="0" cellpadding="0">' . "\n");
		echo('<tr><td class="row-header"' . (($colspan > 0) ? (' colspan="' . $colspan . '"') : '') . '>' . $img_minimise . '<span>' . $header . '</span></td></tr>' . "\n");
		echo('<tr>' . "\n");
		echo('	<td' . (!empty($td_class) ? (' class="' . $td_class . '"') : '') . '>' . "\n");
	}

	function table_end($spoiler_id = false)
	{
		echo('	</td>' . "\n");
		echo('</tr>' . "\n");
		echo('</table>' . "\n");
		if (!empty($spoiler_id))
		{
			echo('</div>' . "\n");
			echo('<script type="text/javascript">' . "\n");
			echo('<!--' . "\n");
			echo('tmp = \'' . $spoiler_id . '\';' . "\n");
			echo('if(GetCookie(tmp) == \'2\')' . "\n");
			echo('{' . "\n");
			echo('	ShowHide(\'' . $spoiler_id . '\',\'' . $spoiler_id . '_h\',\'' . $spoiler_id . '\');' . "\n");
			echo('}' . "\n");
			echo('//-->' . "\n");
			echo('</script>' . "\n");
		}
	}

	function table_r_begin($header)
	{
		echo($this->tbl_h_l . $this->tbl_h_c . '<span class="forumlink">' . $header . '</span>' . $this->tbl_h_r . '<table class="forumlinenb" width="100%" cellspacing="0" cellpadding="0">' . "\n");
	}

	function table_r_end()
	{
		echo('</table>' . $this->tbl_f_l . $this->tbl_f_c . $this->tbl_f_r . "\n");
	}

	function common_form($hidden, $submit)
	{
		echo('<tr><td class="cat" align="center" colspan="2" style="border-width: 0px;">' . $hidden . '<input class="mainoption" type="submit" value="' . $submit . '" /></td></tr>' . "\n");
	}

	function stats_box($current_ip_version, $current_phpbb_version)
	{
		global $lang;

		$current_ip_version_full = (empty($current_ip_version) ? $lang['NotInstalled'] : $current_ip_version);

		if ($current_phpbb_version === false)
		{
			$this->table_begin($lang['Information'], 'row-post');
			echo('<div class="post-text"><br /><br /><br />' . $lang['phpBB_NotDetected'] . '<br /><br /><br /></div>' . "\n");
			$this->table_end();
			exit;
		}

		switch ($current_phpbb_version)
		{
			case '':
				$current_phpbb_version_full = '&lt; RC-3';
				break;
			case 'RC-3':
				$current_phpbb_version_full = 'RC-3';
				break;
			case 'RC-4':
				$current_phpbb_version_full = '&lt; RC-4';
				break;
			default:
				$current_phpbb_version_full = '2' . $current_phpbb_version;
				break;
		}

		echo('<br clear="all" />' . "\n");
		$this->table_begin($lang['Information'], $td_class = 'row-post', 0, 'info');
		echo('<div class="post-text">' . "\n");
		$this->info_box($current_ip_version_full, $current_phpbb_version_full);
		echo('</div>' . "\n");
		$this->table_end('info');
	}

	function info_box($ip_version_full, $phpbb_version_full)
	{
		global $lang, $ip_version, $phpbb_version;

		$phpbb_color = ($phpbb_version_full == ('2' . $phpbb_version)) ? $this->color_ok : $this->color_error;
		$ip_color = ($ip_version_full == $ip_version) ? $this->color_ok : $this->color_error;

		$file_creation_test = ip_functions::file_creation(IP_ROOT_PATH);
		$file_creation_text = ($file_creation_test ? $lang['FileCreation_OK'] : $lang['FileCreation_ERROR']);
		$file_creation_color = ($file_creation_test ? $this->color_ok : $this->color_error);

		echo('<h2>' . $lang['VersionInformation'] . '</h2><br />' . "\n");
		echo('<div class="genmed"><ul type="circle">' . "\n");
		echo('<li><b>' . $lang['Current_phpBB_Version'] . '</b> :: <b style="color:' . $phpbb_color . ';">' . $phpbb_version_full . '</b> &raquo; ' . $lang['Latest_Release'] . ' :: <b style="color: ' . $this->color_ok . ';">2' . $phpbb_version . '</b></li>' . "\n");
		echo('<li><b>' . $lang['Current_IP_Version'] . '</b> :: <b style="color:' . $ip_color . ';">' . $ip_version_full . '</b> &raquo; ' . $lang['Latest_Release'] . ' :: <b style="color: ' . $this->color_ok . ';">' . $ip_version . '</b></li>' . "\n");
		echo('<li><b>' . $lang['dbms'] . '</b> :: <b>' . SQL_LAYER . '</b></li>' . "\n");
		echo('<li><b>' . $lang['FileWriting'] . '</b> :: <b style="color:' . $file_creation_color . ';">' . $file_creation_text . '</b></li>' . "\n");
		if ($ip_version_full != $lang['NotInstalled'])
		{
			echo('<li><b>' . $lang['CHMOD_Files'] . ':</b><br />' . $this->spoiler('chmod', $this->read_chmod(), false) . '</li>' . "\n");
		}
		echo('</ul></div><br /><br />' . "\n");
	}

	function ftp_type()
	{
		global $lang;

		echo('	<table class="forumline" width="100%" cellspacing="0" cellpadding="0">' . "\n");
		echo('	<tr><th colspan="2">' . $lang['ftp_choose'] . '</th></tr>' . "\n");
		echo('	<tr>' . "\n");
		echo('		<td class="row1" align="right" width="50%"><span class="gen">' . $lang['Attempt_ftp'] . '</span></td>' . "\n");
		echo('		<td class="row2"><input type="radio" name="send_file" value="2" /></td>' . "\n");
		echo('	</tr>' . "\n");
		echo('	<tr>' . "\n");
		echo('		<td class="row1" align="right" width="50%"><span class="gen">' . $lang['Send_file'] . '</span></td>' . "\n");
		echo('		<td class="row2"><input type="radio" name="send_file" value="1" /></td>' . "\n");
		echo('	</tr>' . "\n");
	}

	function error($error_title, $error)
	{
		echo('<tr><th>' . $error_title . '</th></tr>' . "\n");
		echo('<tr><td class="row1" align="center"><span class="gen">' . $error . '</span></td></tr>' . "\n");
	}

	function ftp($s_hidden_fields)
	{
		global $lang;

		echo('	<table class="forumline" width="100%" cellspacing="0" cellpadding="0">' . "\n");
		echo('	<tr><th colspan="2">' . $lang['ftp_info'] . '</th></tr>' . "\n");
		echo('	<tr>' . "\n");
		echo('		<td class="row1" align="right"><span class="gen">' . $lang['ftp_path'] . '</span></td>' . "\n");
		echo('		<td class="row2"><input class="post" type="text" name="ftp_dir"></td>' . "\n");
		echo('	</tr>' . "\n");
		echo('	<tr>' . "\n");
		echo('		<td class="row1" align="right"><span class="gen">' . $lang['ftp_username'] . '</span></td>' . "\n");
		echo('		<td class="row2"><input type="text" name="ftp_user"></td>' . "\n");
		echo('	</tr>' . "\n");
		echo('	<tr>' . "\n");
		echo('		<td class="row1" align="right"><span class="gen">' . $lang['ftp_password'] . '</span></td>' . "\n");
		echo('		<td class="row2"><input type="password" name="ftp_pass"></td>' . "\n");
		echo('	</tr>' . "\n");
		$this->common_form($s_hidden_fields, $lang['Transfer_config']);
		echo('	</td>' . "\n");
		echo('	</tr>' . "\n");
		echo('	</table>' . "\n");
	}

	function setup_form($error, $lang_select, $dbms_select, $upgrade_option, $dbhost, $dbname, $dbuser, $dbpasswd, $table_prefix, $board_email, $server_name, $server_port, $script_path, $admin_name, $admin_pass1, $admin_pass2, $language, $hidden_fields)
	{
		global $lang;

		$rowspan_admin = 8;
		$rowspan_admin = empty($upgrade_option) ? $rowspan_admin : $rowspan_admin++;

		echo('	<table class="forumline" width="100%" cellspacing="0" cellpadding="0">' . "\n");
		echo('	<tr><td class="row-header" colspan="3"><span>' . $lang['Initial_config'] . '</span></td></tr>' . "\n");

		echo('	<tr><th colspan="3">' . $lang['Admin_config'] . '</th></tr>' . "\n");
		if ($error)
		{
			echo('	<tr><td class="row1" colspan="3" align="center"><span class="gen" style="color: ' . $this->color_error . '">' . $error . '</span></td></tr>' . "\n");
		}
		echo('	<tr><td class="row1 row-center" rowspan="' . $rowspan_admin . '" width="90"><img src="style/server_install.png" alt="' . $lang['Initial_config'] . '" title="' . $lang['Initial_config'] . '" /></td></tr>' . "\n");
		echo('	<!--' . "\n");
		echo('	<tr>' . "\n");
		echo('		<td class="row1" align="right"><span class="gen">' . $lang['Default_lang'] . ': </span></td>' . "\n");
		echo('		<td class="row2">' . $lang_select . '</td>' . "\n");
		echo('	</tr>' . "\n");
		echo('	-->' . "\n");
		if (!empty($upgrade_option))
		{
			echo('	<tr>' . "\n");
			echo('		<td class="row1" align="right"><span class="gen">' . $lang['Install_Method'] . ':</span></td>' . "\n");
			echo('		<td class="row2">' . $upgrade_option . '</td>' . "\n");
			echo('	</tr>' . "\n");
		}
		echo('	<tr>' . "\n");
		echo('		<td class="row1" align="right"><span class="gen">' . $lang['Admin_Username'] . ': </span></td>' . "\n");
		echo('		<td class="row2"><input type="text" class="post" name="admin_name" value="' . (($admin_name != '') ? $admin_name : '') . '" /></td>' . "\n");
		echo('	</tr>' . "\n");
		echo('	<tr>' . "\n");
		echo('		<td class="row1" align="right"><span class="gen">' . $lang['Admin_Password'] . ': </span></td>' . "\n");
		echo('		<td class="row2"><input type="password" class="post" name="admin_pass1" value="' . (($admin_pass1 != '') ? $admin_pass1 : '') . '" /></td>' . "\n");
		echo('	</tr>' . "\n");
		echo('	<tr>' . "\n");
		echo('		<td class="row1" align="right"><span class="gen">' . $lang['Admin_Password_confirm'] . ': </span></td>' . "\n");
		echo('		<td class="row2"><input type="password" class="post" name="admin_pass2" value="' . (($admin_pass2 != '') ? $admin_pass2 : '') . '" /></td>' . "\n");
		echo('	</tr>' . "\n");
		echo('	<tr>' . "\n");
		echo('		<td class="row1" align="right"><span class="gen">' . $lang['Admin_email'] . ': </span></td>' . "\n");
		echo('		<td class="row2"><input type="text" class="post" name="board_email" value="' . (($board_email != '') ? $board_email : '') . '" /></td>' . "\n");
		echo('	</tr>' . "\n");
		echo('	<tr>' . "\n");
		echo('		<td class="row1" align="right"><span class="gen">' . $lang['Server_name'] . ': </span></td>' . "\n");
		echo('		<td class="row2"><input type="text" class="post" name="server_name" value="' . $server_name . '" /></td>' . "\n");
		echo('	</tr>' . "\n");
		echo('	<tr>' . "\n");
		echo('		<td class="row1" align="right"><span class="gen">' . $lang['Server_port'] . ': </span></td>' . "\n");
		echo('		<td class="row2"><input type="text" class="post" name="server_port" value="' . $server_port . '" /></td>' . "\n");
		echo('	</tr>' . "\n");
		echo('	<tr>' . "\n");
		echo('		<td class="row1" align="right"><span class="gen">' . $lang['Script_path'] . ': </span></td>' . "\n");
		echo('		<td class="row2"><input type="text" class="post" name="script_path" value="' . $script_path . '" /></td>' . "\n");
		echo('	</tr>' . "\n");

		echo('	<tr><th colspan="3">' . $lang['DB_config'] . '</th></tr>' . "\n");
		echo('	<tr><td class="row1 row-center" rowspan="7" width="90"><img src="style/db_status.png" alt="' . $lang['DB_config'] . '" title="' . $lang['DB_config'] . '" /></td></tr>' . "\n");
		echo('	<tr>' . "\n");
		echo('		<td class="row1" align="right"><span class="gen">' . $lang['dbms'] . ': </span></td>' . "\n");
		echo('		<td class="row2">' . $dbms_select . '</td>' . "\n");
		echo('	</tr>' . "\n");
		echo('	<tr>' . "\n");
		echo('		<td class="row1" align="right"><span class="gen">' . $lang['DB_Host'] . ': </span></td>' . "\n");
		echo('		<td class="row2"><input type="text" class="post" name="dbhost" value="' . (($dbhost != '') ? $dbhost : '') . '" /></td>' . "\n");
		echo('	</tr>' . "\n");
		echo('	<tr>' . "\n");
		echo('		<td class="row1" align="right"><span class="gen">' . $lang['DB_Name'] . ': </span></td>' . "\n");
		echo('		<td class="row2"><input type="text" class="post" name="dbname" value="' . (($dbname != '') ? $dbname : '') . '" /></td>' . "\n");
		echo('	</tr>' . "\n");
		echo('	<tr>' . "\n");
		echo('		<td class="row1" align="right"><span class="gen">' . $lang['DB_Username'] . ': </span></td>' . "\n");
		echo('		<td class="row2"><input type="text" class="post" name="dbuser" value="' . (($dbuser != '') ? $dbuser : '') . '" /></td>' . "\n");
		echo('	</tr>' . "\n");
		echo('	<tr>' . "\n");
		echo('		<td class="row1" align="right"><span class="gen">' . $lang['DB_Password'] . ': </span></td>' . "\n");
		echo('		<td class="row2"><input type="password" class="post" name="dbpasswd" value="' . (($dbpasswd != '') ? $dbpasswd : '') . '" /></td>' . "\n");
		echo('	</tr>' . "\n");
		echo('	<tr>' . "\n");
		echo('		<td class="row1" align="right"><span class="gen">' . $lang['Table_Prefix'] . ': </span></td>' . "\n");
		echo('		<td class="row2"><input type="text" class="post" name="prefix" value="' . ((!empty($table_prefix)) ? $table_prefix : 'ip_') . '" /></td>' . "\n");
		echo('	</tr>' . "\n");

		echo('	<tr>' . "\n");
		echo('		<td class="cat" colspan="3" align="center" style="border-width: 0px;">' . "\n");
		echo('			' . $hidden_fields);
		echo('			<input type="hidden" name="install_step" value="2" />' . "\n");
		echo('			<input type="hidden" name="cur_lang" value="' . $language . '" />' . "\n");
		echo('			<input class="mainoption" type="submit" value="' . $lang['Continue_Install'] . '" />' . "\n");
		echo('		</td>' . "\n");
		echo('	</tr>' . "\n");

		echo('	</table>' . "\n");
	}

	function finish_install($hidden_fields)
	{
		global $db, $ip_sql, $lang;

		echo('	<table class="forumline" width="100%" cellspacing="0" cellpadding="0">' . "\n");
		echo('	<tr><td class="row-header" colspan="2"><span>' . $lang['Finish_Install'] . '</span></td></tr>' . "\n");
		echo('' . "\n");
		echo('	<tr><th colspan="2">' . $lang['Finish_Install'] . '</th></tr>' . "\n");
		echo('	<tr><td class="row1 row-center" rowspan="2" width="90"><img src="style/setup.png" alt="' . $lang['Finish_Install'] . '" title="' . $lang['Finish_Install'] . '" /></td></tr>' . "\n");
		echo('	<tr><td class="row1" align="left"><span class="gen">' . $lang['Inst_Step_2'] . '</span></td></tr>' . "\n");
		echo('	<tr><td class="cat" colspan="2" align="center" style="border-width: 0px;"><?php $hidden_fields; ?><input class="mainoption" type="submit" value="' . $lang['Finish_Install'] . '" /></td></tr>' . "\n");
		echo('' . "\n");
		echo('	</table>' . "\n");

	}

	function read_chmod()
	{
		// chmod files defined in schemas/versions.php
		global $chmod_777, $chmod_666;
		global $lang, $language;

		$img_ok = str_replace('alt="" title=""', 'alt="' . $lang['CHMOD_OK'] . '" title="' . $lang['CHMOD_File_Exists'] . '"', $this->img_ok);
		$img_error = str_replace('alt="" title=""', 'alt="' . $lang['CHMOD_Error'] . '" title="' . $lang['CHMOD_Error'] . '"', $this->img_error);

		$report_string_append = '';

		$chmod_items_array = array($chmod_777, $chmod_666);
		$chmod_values_array = array(0777, 0666);
		$chmod_langs_array = array($lang['CHMOD_777'], $lang['CHMOD_666']);

		$table_output = '';

		for ($i = 0; $i < sizeof($chmod_items_array); $i++)
		{
			if (!empty($chmod_items_array[$i]))
			{
				$table_output .= '<b>' . $chmod_langs_array[$i] . '</b><br />' . '<ul>';
				for ($j = 0; $j < sizeof($chmod_items_array[$i]); $j++ )
				{
					$report_string = '';
					$errored = false;
					if (!file_exists($chmod_items_array[$i][$j]))
					{
						$errored = true;
						$report_string = $lang['CHMOD_File_NotExists'];
					}
					else
					{
						if (!is_writable($chmod_items_array[$i][$j]))
						{
							$errored = true;
							$report_string = $lang['CHMOD_File_Exists_Read_Only'];
						}
						else
						{
							$report_string = $lang['CHMOD_File_Exists'];
						}
					}

					if ($errored)
					{
						$report_string_append = '&nbsp;<span class="genmed" style="color: ' . $this->color_error . ';"><b>' . $report_string . '</b></span>';
						$table_output .= '<li><span class="gensmall" style="color: ' . $this->color_error . ';"><b>' . $chmod_items_array[$i][$j] . '</b></span>&nbsp;' . $img_error . '</li>' . "\n";
					}
					else
					{
						$report_string_append = '&nbsp;<span class="genmed" style="color: ' . $this->color_ok . ';">' . $report_string . '</span>';
						$table_output .= '<li><span class="gensmall" style="color: ' . $this->color_ok . ';"><b>' . $chmod_items_array[$i][$j] . '</b></span>&nbsp;' . $img_ok . '</li>' . "\n";
					}
				}
				$table_output .= '</ul>' . '<br />';
			}
		}

		return $table_output;
	}

	function apply_chmod($install_mode = true)
	{
		// chmod files defined in schemas/versions.php
		global $chmod_777, $chmod_666;
		global $lang, $language;

		$img_ok = str_replace('alt="" title=""', 'alt="' . $lang['CHMOD_OK'] . '" title="' . $lang['CHMOD_OK'] . '"', $this->img_ok);
		$img_error = str_replace('alt="" title=""', 'alt="' . $lang['CHMOD_Error'] . '" title="' . $lang['CHMOD_Error'] . '"', $this->img_error);

		$chmod_errors = false;
		$file_exists_errors = false;
		$read_only_errors = false;
		$report_string_append = '';

		$chmod_items_array = array($chmod_777, $chmod_666);
		$chmod_values_array = array(0777, 0666);
		$chmod_langs_array = array($lang['CHMOD_777'], $lang['CHMOD_666']);
		$chmod_images_array = array('./style/folder_blue.png', './style/folder_red.png');

		$table_output = '';
		if ($install_mode)
		{
			$this->output_lang_select(THIS_FILE);
			$table_output .= '<form action="install.' . PHP_EXT . '" name="lang" method="post">' . "\n";
		}
		$table_output .= '<table class="forumline" width="100%" cellspacing="0" cellpadding="0">' . "\n";
		$table_output .= '<tr><td class="row-header" colspan="3"><span>' . $lang['CHMOD_Files'] . '</span></td></tr>' . "\n";

		for ($i = 0; $i < sizeof($chmod_items_array); $i++)
		{
			if (!empty($chmod_items_array[$i]))
			{
				$table_output .= '<tr><th colspan="3"><span class="gen"><b>' . $chmod_langs_array[$i] . '</b></span></th></tr>' . "\n";
				$table_output .= '<tr><td class="row1 row-center" rowspan="' . (sizeof($chmod_items_array[$i]) + 1) . '" width="90"><img src="' . $chmod_images_array[$i] . '" alt="' . $chmod_langs_array[$i] . '" title="' . $chmod_langs_array[$i] . '" /></td></tr>' . "\n";
				for ($j = 0; $j < sizeof($chmod_items_array[$i]); $j++ )
				{
					$report_string = '';
					$errored = false;
					if (!file_exists($chmod_items_array[$i][$j]))
					{
						$errored = true;
						$report_string = $lang['CHMOD_File_NotExists'];
					}
					else
					{
						@chmod($chmod_items_array[$i][$j], $chmod_values_array[$i]);
						if (!is_writable($chmod_items_array[$i][$j]))
						{
							$errored = true;
							$report_string = $lang['CHMOD_File_Exists_Read_Only'];
						}
						else
						{
							$report_string = $lang['CHMOD_File_Exists'];
						}
					}

					if ($errored)
					{
						$report_string_append = '&nbsp;<span class="genmed" style="color: ' . $this->color_error . ';"><b>' . $report_string . '</b></span>';
						$table_output .= '<tr><td class="row1" width="40%"><span class="gen" style="color: ' . $this->color_error . ';"><b>' . $chmod_items_array[$i][$j] . '</b></span></td><td class="row2">' . $img_error . $report_string_append . '</td></tr>' . "\n";
						$chmod_errors = true;
					}
					else
					{
						$report_string_append = '&nbsp;<span class="genmed" style="color: ' . $this->color_ok . ';">' . $report_string . '</span>';
						$table_output .= '<tr><td class="row1" width="40%"><span class="gen" style="color: ' . $this->color_ok . ';"><b>' . $chmod_items_array[$i][$j] . '</b></span></td><td class="row2">' . $img_ok . $report_string_append . '</td></tr>' . "\n";
					}
				}
			}
		}

		if ($install_mode)
		{
			$table_output .= '<tr><th colspan="3"><span class="gen"><b>&nbsp;</b></span></th></tr>' . "\n";
			$s_hidden_fields = '';
			$s_hidden_fields .= '<input type="hidden" name="install_step" value="1" />' . "\n";
			$s_hidden_fields .= '<input type="hidden" name="lang" value="' . $language . '" />' . "\n";
			$table_output .= '<tr><td class="row2 row-center" colspan="3"><span style="color:' . ($chmod_errors ? $this->color_error : $this->color_ok) . ';"><b>' . ($chmod_errors ? ($lang['CHMOD_Files_Explain_Error'] . ' ' . $lang['Confirm_Install_anyway']) : ($lang['CHMOD_Files_Explain_Ok'] . ' ' . $lang['Can_Install'])) . '</b></span></td></tr>' . "\n";
			$table_output .= '<tr><td class="cat" colspan="3" align="center" style="border-width: 0px;">' . $s_hidden_fields . '<input class="mainoption" type="submit" value="' . ($chmod_errors ? $lang['Start_Install_Anyway'] : $lang['Start_Install']) . '" /></td></tr>' . "\n";

			$table_output .= '</table>' . "\n";
			$table_output .= '</form>' . "\n";
		}
		else
		{
			$table_output .= '<tr><td class="cat" colspan="3"><span class="gen"><b>&nbsp;</b></span></td></tr>' . "\n";
			$table_output .= '</table>' . "\n";
		}

		echo($table_output);
		return $chmod_errors;
	}

	/*
	* Displays a box with upgrade info
	*/
	function box_upgrade_info()
	{
		global $lang, $language;
		global $current_phpbb_version, $phpbb_version;
		global $current_ip_version, $ip_version;

		echo('<br />' . "\n");
		$phpbb_update = '';
		if ($current_phpbb_version == $phpbb_version)
		{
			//$box_message = $lang['phpBB_Version_UpToDate'];
			//$page_framework->box('green', 'green', $box_message);
		}
		else
		{
			$phpbb_update = '&amp;phpbb_update=true';
			// Comment "Force phpBB update" if you want to make all db updates all at once
			// Force phpBB update - BEGIN
			$box_message = $lang['phpBB_Version_NotUpToDate'] . '<br /><br />' . sprintf($lang['ClickUpdate'], '<a href="' . ip_functions::append_sid(THIS_FILE . '?mode=update_phpbb') . '">', '</a>');
			$this->box('yellow', 'red', $box_message);
			$this->page_footer(false);
			exit;
			// Force phpBB update - END
		}
		//echo('<br /><br />');

		if (($current_ip_version == $ip_version) && ($phpbb_update == ''))
		{
			$needs_update = false;
		}
		else
		{
			$needs_update = true;
			$phpbb_string = '';
			if ($phpbb_update != '')
			{
				$phpbb_string = $lang['phpBB_Version_NotUpToDate'] . '<br /><br />';
			}

			$ip_string = $lang['IcyPhoenix_Version_NotUpToDate'] . '<br /><br />';
			if ($current_ip_version == $lang['NotInstalled'])
			{
				$ip_string = $lang['IcyPhoenix_Version_NotInstalled'] . '<br /><br />';
			}
		}

		if ($needs_update == false)
		{
			$box_message = $lang['IcyPhoenix_Version_UpToDate'];
			$this->box('green', 'green', $box_message);
		}
		elseif (($needs_update == true) && version_compare($current_ip_version, '1.2.9.36', '<') && !defined('IP_DB_UPDATE'))
		{
			$this->box_upgrade_steps();
		}
		else
		{
			$box_message = $phpbb_string . $ip_string . sprintf($lang['ClickUpdate'], '<a href="' . ip_functions::append_sid(THIS_FILE . '?mode=update' . $phpbb_update) . '">', '</a>');
			$this->box('yellow', 'red', $box_message);
		}
		echo('<br clear="all" />' . "\n");
		echo('<br /><br />' . "\n");
	}

	/*
	* Displays a box with upgrade steps
	*/
	function box_upgrade_steps()
	{
		global $lang, $language;

		$lang_append = '&amp;lang=' . $language;
		$img_ok = str_replace('alt="" title=""', 'alt="' . $lang['Done'] . '" title="' . $lang['Done'] . '"', $this->img_ok);
		$img_error = str_replace('alt="" title=""', 'alt="' . $lang['NotDone'] . '" title="' . $lang['NotDone'] . '"', $this->img_error);

		$table_update_options = '';
		$table_update_options .= '<div class="post-text">' . "\n";

		$table_update_options .= '<div class="genmed"><br /><ol type="1">' . "\n";
		$table_update_options .= '<li><span class="text_red"><strong>' . $lang['MakeFullBackup'] . '</strong></span><br /><br /></li>' . "\n";

		//$table_update_options .= '<li><a href="' . ip_functions::append_sid(THIS_FILE . '?mode=update' . $lang_append) . '"><span class="text_gray">' . $lang['Update_phpBB'] . '</span></a><br /><br /></li>' . "\n";

		$table_update_options .= '<li><span class="text_red">' . $lang['Upload_NewFiles'] . '</span><br /><br /></li>' . "\n";
		$table_update_options .= '<li><a href="' . ip_functions::append_sid(THIS_FILE . '?mode=fix' . $lang_append) . '"><span class="text_orange">' . $lang['FixPosts_IP2'] . '</span></a><br /><br /></li>' . "\n";
		$table_update_options .= '</ol></div>' . "\n";

		$table_update_options .= '<br />' . "\n";

		$table_update_options .= '<div class="forumline" style="width: 700px; padding: 5px;">' . "\n";
		$table_update_options .= '<strong>' . $lang['ColorsLegend'] . '</strong><br />' . "\n";
		$table_update_options .= '<div class="gensmall"><ul>' . "\n";
		$table_update_options .= '<li><span class="text_red">' . $lang['ColorsLegendRed'] . '</span></li>' . "\n";
		$table_update_options .= '<li><span class="text_orange">' . $lang['ColorsLegendOrange'] . '</span></li>' . "\n";
		$table_update_options .= '<li><span class="text_gray">' . $lang['ColorsLegendGray'] . '</span></li>' . "\n";
		$table_update_options .= '<li><span class="text_blue">' . $lang['ColorsLegendBlue'] . '</span></li>' . "\n";
		$table_update_options .= '<li><span class="text_green">' . $lang['ColorsLegendGreen'] . '</span></li>' . "\n";
		$table_update_options .= '</ul></div>' . "\n";
		$table_update_options .= '</div>' . "\n";

		$table_update_options .= '<br />' . "\n";

		$table_update_options .= '</div>' . "\n";

		$this->table_begin($lang['Upgrade_Steps'], '', 0, 'upgrade_steps');
		echo($table_update_options);
		$this->table_end('upgrade_steps');
	}

	function box_ip_tools()
	{
		global $lang, $language;

		$lang_append = '&amp;lang=' . $language;

		//$this->output_lang_select(THIS_FILE, true);

		//$this->box('yellow', 'red', 'Icy Phoenix');

		$table_update_options = '';
		$table_update_options .= '<div class="post-text">' . "\n";

		// Update Options
		$table_update_options .= '<br /><br />' . "\n";
		$table_update_options .= '<span style="color:#880088;font-weight:bold">' . $lang['Upgrade_Options'] . '</span><br />' . "\n";

		// Only update options to be written inside the spoiler
		$update_options = '';
		$update_options .= '<div class="genmed"><br /><ul type="circle">' . "\n";
		$update_options .= '<li><a href="' . ip_functions::append_sid(THIS_FILE . '?mode=update' . $lang_append) . '"><span class="text_red">' . $lang['Upgrade_From'] . ' ' . $lang['Upgrade_From_phpBB'] . '</span></a><br /><br /></li>' . "\n";
		$update_options .= '<li><a href="' . ip_functions::append_sid(THIS_FILE . '?mode=update_100' . $lang_append) . '"><span class="text_gray">' . $lang['Upgrade_From'] . ' ' . $lang['Upgrade_From_Version'] . ' 2.0.0.84RC1 (' . $lang['Upgrade_Higher'] . ')</span></a><br /><br /></li>' . "\n";
		$update_options .= '</ul></div>' . "\n";

		// Output the spoiler
		$table_update_options .= $this->spoiler('update_options', $update_options, false);

		// CHMOD
		$table_update_options .= '<br /><br />' . "\n";
		$table_update_options .= '<span style="color: #008888; font-weight: bold;">' . $lang['CHMOD_Files'] . '</span><br />' . "\n";
		$table_update_options .= '<div class="genmed"><br /><ul type="circle">' . "\n";
		$table_update_options .= '<li><a href="' . ip_functions::append_sid(THIS_FILE . '?mode=chmod' . $lang_append) . '"><span class="text_red">' . $lang['CHMOD_Apply'] . '</span></a><br /><span class="gensmall">' . $lang['CHMOD_Apply_Warn'] . '</span><br /><br /></li>' . "\n";
		$table_update_options .= '</ul></div>' . "\n";

		$table_update_options .= '</div>' . "\n";

		$this->table_begin($lang['IP_Utilities'], '', 0, 'ip_tools');
		echo($table_update_options);
		$this->table_end('ip_tools');
	}

	function output_lang_select($form_action = THIS_FILE, $single_line = false)
	{
		global $lang, $language;

		$lang_select = $this->build_lang_select($language);

		$table_lang_select = '<form action="' . $form_action . '" name="install" method="post"><table class="forumline" width="100%" cellspacing="0" cellpadding="0">';
		if ($single_line == true)
		{
			$table_lang_select .= '<tr><td class="row-header" colspan="2"><span><b style="background-image:none;float:right;display:inline;padding-right:5px;">' . $lang_select . '</b>' . $lang['Select_lang'] . '</span></td></tr>';
		}
		else
		{
			$table_lang_select .= '<tr><td class="row-header" colspan="2"><span>' . $lang['Default_lang'] . '</span></td></tr>';
			$table_lang_select .= '<tr><td class="row1" align="right"><span class="gen">' . $lang['Default_lang'] .':</span></td><td class="row2">' . $lang_select . '</td></tr>';
		}
		$table_lang_select .= '</table></form>';
		echo($table_lang_select);
	}

	function build_lang_select($language = 'english')
	{
		$language = ($language == '') ? 'english' : $language;
		$dirname = 'language';
		$dir = opendir($dirname);

		$lang_options = array();
		while ($file = readdir($dir))
		{
			if (preg_match('#^lang_#i', $file) && !is_file(@ip_functions::ip_realpath($dirname . '/' . $file)) && !is_link(@ip_functions::ip_realpath($dirname . '/' . $file)))
			{
				$filename = trim(str_replace('lang_', '', $file));
				$displayname = preg_replace('/^(.*?)_(.*)$/', '\1 [ \2 ]', $filename);
				$displayname = preg_replace('/\[(.*?)_(.*)\]/', '[ \1 - \2 ]', $displayname);
				$lang_options[$displayname] = $filename;
			}
		}

		closedir($dir);

		@asort($lang_options);
		@reset($lang_options);

		$lang_select = '<select name="lang" onchange="this.form.submit()">';
		while (list($displayname, $filename) = @each($lang_options))
		{
			$selected = ($language == $filename) ? ' selected="selected"' : '';
			$lang_select .= '<option value="' . $filename . '"' . $selected . '>' . ucwords($displayname) . '</option>';
		}
		$lang_select .= '</select>';
		return $lang_select;
	}
}

?>