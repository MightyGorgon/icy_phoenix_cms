<?php
/**
*
* @package Icy Phoenix
* @version $Id$
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*
* @Icy Phoenix is based on phpBB
* @copyright (c) 2008 phpBB Group
*
*/

if (!defined('IN_ICYPHOENIX'))
{
	die('Hacking attempt');
}

/*
* Language select box
*/
function language_select($select_name = 'language', $default = 'english', $dirname = 'language', $return_array = false)
{

	$dir = opendir(IP_ROOT_PATH . $dirname);

	$lang = array();
	while ($file = readdir($dir))
	{
		if (preg_match('#^lang_#i', $file) && !is_file(@phpbb_realpath(IP_ROOT_PATH . $dirname . '/' . $file)) && !is_link(@phpbb_realpath(IP_ROOT_PATH . $dirname . '/' . $file)))
		{
			$filename = trim(str_replace("lang_", "", $file));
			$displayname = preg_replace("/^(.*?)_(.*)$/", "\\1 [ \\2 ]", $filename);
			$displayname = preg_replace("/\[(.*?)_(.*)\]/", "[ \\1 - \\2 ]", $displayname);
			$lang[$displayname] = $filename;
		}
	}

	closedir($dir);

	@asort($lang);
	@reset($lang);

	if ($return_array)
	{
		$result = $lang;
	}
	else
	{
		$lang_select = '<select name="' . $select_name . '">';
		while (list($displayname, $filename) = @each($lang))
		{
			$selected = (strtolower($default) == strtolower($filename)) ? ' selected="selected"' : '';
			$lang_select .= '<option value="' . $filename . '"' . $selected . '>' . ucwords($displayname) . '</option>';
		}
		$lang_select .= '</select>';
		$result = $lang_select;
	}
	return $result;
}

/*
* Styles select box
*/
function style_select($select_name = 'style', $default_style = '', $js_append = '')
{
	global $db, $cache;

	$style_select = '<select name="' . $select_name . '"' . $js_append . '>';
	if (empty($cache) || !class_exists('ip_cache'))
	{
		@include_once(IP_ROOT_PATH . 'includes/class_cache.' . PHP_EXT);
		@include_once(IP_ROOT_PATH . 'includes/class_cache_extends.' . PHP_EXT);
		$cache = new ip_cache();
	}
	$styles = $cache->obtain_styles(true);
	foreach ($styles as $k => $v)
	{
		$selected = (!empty($default_style) && ($k == $default_style)) ? ' selected="selected"' : '';
		$style_select .= '<option value="' . $k . '"' . $selected . '>' . htmlspecialchars($v) . '</option>';
	}
	$style_select .= '</select>';

	return $style_select;
}

/*
* TimeZone select box
*/
function tz_select($select_name = 'timezone', $default = '')
{
	global $sys_timezone, $lang;

	$default == empty($default) ? $sys_timezone : $default;

	$tz_select = '<select name="' . $select_name . '">';

	while(list($offset, $zone) = @each($lang['tz_zones']))
	{
		$selected = ($offset == $default) ? ' selected="selected"' : '';
		$tz_select .= '<option value="' . $offset . '"' . $selected . '>' . str_replace('GMT', 'UTC', $zone) . '</option>';
	}
	$tz_select .= '</select>';

	return $tz_select;
}

/*
* Date/Time format select box
*/
function date_select($select_name = 'dateformat', $default_format = '')
{
	global $lang, $config;

	//---------------------------------------------------
	$date_format_list[] = array('Y/m/d - H:i');
	$date_format_list[] = array('Y.m.d - H:i');
	$date_format_list[] = array('d/m/Y - H:i');
	$date_format_list[] = array('d.m.Y - H:i');
	//---------------------------------------------------
	$date_format_list[] = array('F d Y, H:i');
	$date_format_list[] = array('F d Y, G:i');
	$date_format_list[] = array('F d Y, h:i A');
	$date_format_list[] = array('F d Y');
	//---------------------------------------------------
	$date_format_list[] = array('d F Y');
	$date_format_list[] = array('d F Y, H:i');
	$date_format_list[] = array('d F Y, G:i');
	$date_format_list[] = array('d F Y, h:i A');
	//---------------------------------------------------
	$date_format_list[] = array('l, d F Y');
	$date_format_list[] = array('l, d F Y, H:i');
	$date_format_list[] = array('l, d F Y, G:i');
	$date_format_list[] = array('l, d F Y, h:i A');
	//---------------------------------------------------
	$date_format_list[] = array('D, M d Y');
	$date_format_list[] = array('D, M d Y, H:i');
	$date_format_list[] = array('D, M d Y, G:i');
	$date_format_list[] = array('D, M d Y, h:i A');
	//---------------------------------------------------
	$date_format_list[] = array('D d M');
	$date_format_list[] = array('D d M, Y H:i');
	$date_format_list[] = array('D d M, Y G:i');
	$date_format_list[] = array('D d M, Y h:i A');
	//---------------------------------------------------
	$date_format_list[] = array('d/m/Y');
	$date_format_list[] = array('d/m/Y H:i');
	$date_format_list[] = array('d/m/Y G:i');
	$date_format_list[] = array('d/m/Y h:i A');
	//---------------------------------------------------
	$date_format_list[] = array('m/d/Y');
	$date_format_list[] = array('m/d/Y H:i');
	$date_format_list[] = array('m/d/Y G:i');
	$date_format_list[] = array('m/d/Y h:i A');
	//---------------------------------------------------
	$date_format_list[] = array('m.d.Y');
	$date_format_list[] = array('m.d.Y H:i');
	$date_format_list[] = array('m.d.Y G:i');
	$date_format_list[] = array('m.d.Y h:i A');
	//---------------------------------------------------
	$date_format_list[] = array('d.m.Y');
	$date_format_list[] = array('d.m.Y H:i');
	$date_format_list[] = array('d.m.Y G:i');
	$date_format_list[] = array('d.m.Y h:i A');
	//---------------------------------------------------


	//---------------------------------------------------
	// Set a default value.
	//---------------------------------------------------
	if (empty($default_format))
	{
		$default_format = $date_format_list[11][0];
	}


	$date_select = '<select name="' . $select_name . '">' . "\n";
	for($i = 0; $i < sizeof($date_format_list); $i++)
	{
		$date_format = $date_format_list[$i][0];
		$date_desc = create_date($date_format_list[$i][0], time(), $config['board_timezone']);

		$selected = ($date_format == $default_format) ? ' selected="selected"' : '';
		$date_select .= '<option value="' . $date_format . '"' . $selected . '>' . $date_desc . '</option>' . "\n";

		$counter = 0;
	}
	$date_select .= '</select>' . "\n";

	return $date_select;
}

/*
* Auth select box
*/
function auth_select($select_name, $default)
{
	global $lang;

	$auth_array = array(AUTH_ALL, AUTH_REG, AUTH_MOD, AUTH_ADMIN);
	$auth_array_lang = array($lang['AUTH_ALL'], $lang['AUTH_REG'], $lang['AUTH_MOD'], $lang['AUTH_ADMIN']);

	$auth_select = '<select name="' . $select_name . '">';

	for($j = 0; $j < sizeof($auth_array); $j++)
	{
		$selected = ($auth_array[$j] == $default) ? ' selected="selected"' : '';
		$auth_select .= '<option value="' . $auth_array[$j] . '"' . $selected . '>' . $auth_array_lang[$j] . '</option>';
	}
	$auth_select .= '</select>';

	return $auth_select;
}

/*
* Groups select box
*/
function groups_select($select_name, $default, $allow_empty = true)
{
	global $db, $cache, $lang;

	$sql = "SELECT group_id, group_name, group_color
		FROM " . GROUPS_TABLE . "
		WHERE group_single_user <> 1
		ORDER BY group_name ASC";
	$result = $db->sql_query($sql, 0, 'groups_', USERS_CACHE_FOLDER);
	$groups = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);

	$groups_select = '<select name="' . $select_name . '">';
	$groups_select .= (!empty($allow_empty) ? '<option value="0">' . $lang['None'] . '</option>' : '');
	for($j = 0; $j < sizeof($groups); $j++)
	{
		$group_color = check_valid_color($groups[$j]['group_color']);
		$group_color = (!empty($group_color) ? ' style="color: ' . $group_color . '; font-weight: bold;"' : '');
		$selected = ($groups[$j]['group_id'] == $default) ? ' selected="selected"' : '';
		$groups_select .= '<option value="' . $groups[$j]['group_id'] . '"' . $selected . $group_color . '>' . htmlspecialchars($groups[$j]['group_name']) . '</option>';
	}
	$groups_select .= '</select>';

	return $groups_select;
}

/*
* Creates selectbox for Gravatar Ratings
*/
function select_gravatar_rating($default = '')
{
	global $lang;

	$symbols = array('G', 'PG', 'R', 'X');

	$select_box = '<select name="gravatar_rating"><option value="">' . $lang['None'] . '</option>';
	foreach($symbols as $rating)
	{
		$selected = ($rating == $default) ? ' selected="selected"' : '';
		$select_box .= '<option value="' . $rating . '"' . $selected . '>' . $rating . '</option>';
	}
	$select_box .= '</select>';

	return $select_box;
}

// Settings wrappers functions to be used in settings modules - BEGIN

/*
* Creates selectbox for...
*/
/*
function settings_XXX_select($name, $default = '')
{
	$select_box = XXX_select($default, $name, $templates_folder);
	return $select_box;
}
*/

?>