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

define('IN_CRON', true);
define('CTRACKER_DISABLED', true);
define('IN_ICYPHOENIX', true);
// Should we use this? Is absolute path always working fine?
//if (!defined('IP_ROOT_PATH')) dirname(__FILE__) . DIRECTORY_SEPARATOR);
if (!defined('IP_ROOT_PATH')) define('IP_ROOT_PATH', './');
if (!defined('PHP_EXT')) define('PHP_EXT', substr(strrchr(__FILE__, '.'), 1));
include(IP_ROOT_PATH . 'common.' . PHP_EXT);
include_once(IP_ROOT_PATH . 'includes/functions_admin.' . PHP_EXT);
include_once(IP_ROOT_PATH . 'includes/functions_cron.' . PHP_EXT);

// Do not update users last page entry
// Start session management
$user->session_begin(false);
$auth->acl($user->data);
$user->setup();
// End session management

// Set this to true if you want to skip gif output and make some debugs on cron
define('CRON_DEBUG', false);

@set_time_limit(0);
$mem_limit = check_mem_limit();
@ini_set('memory_limit', $mem_limit);

if (CRON_DEBUG == false)
{
	// Output transparent gif
	header('Cache-Control: no-cache');
	header('Content-type: image/gif');
	header('Content-length: 43');

	echo base64_decode('R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==');

	// Flush here to prevent browser from showing the page as loading while running cron.
	flush();
}

// We query cron_* config values to make sure we don't get cached values!
$sql = "SELECT * FROM " . CONFIG_TABLE . " WHERE config_name LIKE 'cron_%'";
$result = $db->sql_query($sql);
$cron_config = $db->sql_fetchrowset($result);
$db->sql_freeresult($result);

foreach ($cron_config as $row)
{
	$config[$row['config_name']] = $row['config_value'];
}

// CRON QUICK DEBUG - BEGIN
/*
$config['cron_lock'] = 0;
$config['cron_lock_hour'] = 0;
$config['cron_digests_last_run'] = 0;
*/
// CRON QUICK DEBUG - END

/*
// Shall we add queue as well?
$cron_types = array('queue');
$cron_functions = array('queue');
*/
$cron_types = array('digests', 'birthdays', 'files', 'database', 'cache', 'sql', 'users', 'topics', 'sessions');
$cron_functions = array('process_digests', 'process_birthdays', 'process_files', 'tidy_database', 'tidy_cache', 'tidy_sql', 'tidy_users', 'tidy_topics', 'tidy_sessions');
$cron_queue = array();
$cron_queue_functions = array();

$cron_time = time();

for ($i = 0; $i < sizeof($cron_types); $i++)
{
	$cron_queue_vars[$cron_types[$i]] = request_var($cron_types[$i], 0);
	if (!empty($cron_queue_vars[$cron_types[$i]]))
	{
		$skip_this = false;
		$force_this = false;
		// Cron specific cases
		switch ($cron_types[$i])
		{
			case 'queue':
				if (!file_exists(MAIN_CACHE_FOLDER . 'queue.' . PHP_EXT))
				{
					$skip_this = true;
				}
			break;

			case 'digests':
				if (empty($config['cron_digests_interval']) || ($config['cron_digests_interval'] == -1))
				{
					$skip_this = true;
				}
				else
				{
					$force_this = true;
				}
			break;

			case 'birthdays':
				if (empty($config['cron_birthdays_interval']))
				{
					$skip_this = true;
				}
				else
				{
					$force_this = true;
				}
			break;
		}

		if ($skip_this)
		{
			continue;
		}

		$cron_trigger = $cron_time - $config['cron_' . $cron_types[$i] . '_interval'];
		if ($force_this || (($config['cron_' . $cron_types[$i] . '_interval'] > 0) && ($cron_trigger > $config['cron_' . $cron_types[$i] . '_last_run'])))
		{
			$cron_queue[] = $cron_types[$i];
			$cron_queue_functions[] = $cron_functions[$i];
		}
	}
}

$use_shutdown_function = (@function_exists('register_shutdown_function')) ? true : false;
$use_shutdown_function = false;

if ($use_shutdown_function)
{
	//define('CRON_REAL_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
	define('CRON_REAL_PATH', @phpbb_realpath(IP_ROOT_PATH) . '/');
}
else
{
	define('CRON_REAL_PATH', IP_ROOT_PATH);
}

if (!isset($config['cron_lock']))
{
	set_config('cron_lock', '0');
}

// Make sure cron doesn't run multiple times in parallel
if (!empty($config['cron_lock']))
{
	// If the other process is running more than CRON_REFRESH already we have to assume it aborted without cleaning the lock
	$time = explode(' ', $config['cron_lock']);
	$time = $time[0];

	if ((($time + CRON_REFRESH) >= time()) && (CRON_DEBUG == false))
	{
		exit;
	}
}

define('CRON_ID', time() . ' ' . unique_id());

$sql = "UPDATE " . CONFIG_TABLE . "
	SET config_value = '" . $db->sql_escape(CRON_ID) . "'
	WHERE config_name = 'cron_lock'
		AND config_value = '" . $db->sql_escape($config['cron_lock']) . "'";
$db->sql_query($sql);

$cache->destroy('config');

// another cron process altered the table between script start and UPDATE query so exit
if (($db->sql_affectedrows() != 1) && (CRON_DEBUG == false))
{
	exit;
}

/**
* Run cron-like action
*/
for ($i = 0; $i < sizeof($cron_queue); $i++)
{
	$skip_this = false;
	// Cron specific cases
	switch ($cron_queue[$i])
	{
		case 'queue':
			if (!file_exists(MAIN_CACHE_FOLDER . 'queue.' . PHP_EXT))
			{
				$skip_this = true;
			}
		break;

		case 'digests':
			if (empty($config['cron_digests_interval']) || ($config['cron_digests_interval'] == -1))
			{
				$skip_this = true;
			}
		break;

		case 'birthdays':
			if (empty($config['cron_birthdays_interval']))
			{
				$skip_this = true;
			}
		break;
	}

	if ($skip_this)
	{
		continue;
	}

	if ($use_shutdown_function)
	{
		register_shutdown_function($cron_queue_functions[$i]);
	}
	else
	{
		call_user_func($cron_queue_functions[$i]);
	}
}

// Unloading cache and closing db after having done the dirty work.
if ($use_shutdown_function)
{
	register_shutdown_function('unlock_cron');
}
else
{
	unlock_cron();
}

exit;

?>