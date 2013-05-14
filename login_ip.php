<?php
/**
*
* @package Icy Phoenix
* @version $Id$
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

define('IN_LOGIN', true);
define('IN_ICYPHOENIX', true);
if (!defined('IP_ROOT_PATH')) define('IP_ROOT_PATH', './');
if (!defined('PHP_EXT')) define('PHP_EXT', substr(strrchr(__FILE__, '.'), 1));
include(IP_ROOT_PATH . 'common.' . PHP_EXT);
include(IP_ROOT_PATH . 'includes/auth_db.' . PHP_EXT);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();
// End session management

// If a bot gets redirected here is almost due to an error or a wrong page management... let's output an Error 404 code
if (!empty($user->data['is_bot']))
{
	redirect(append_sid(CMS_PAGE_ERRORS . '?code=404', true));
}

// session id check
$sid = request_var('sid', '');

$redirect = request_var('redirect', '', true);
$redirect_url = (!empty($redirect) ? urldecode(str_replace(array('&amp;', '?', PHP_EXT . '&'), array('&', '&', PHP_EXT . '?'), $redirect)) : CMS_LOGIN_REDIRECT_PAGE);

if (strstr($redirect_url, "\n") || strstr($redirect_url, "\r") || strstr($redirect_url, ';url'))
{
	message_die(GENERAL_ERROR, 'Tried to redirect to potentially insecure url.');
}

if(isset($_POST['login']) || isset($_GET['login']) || isset($_POST['logout']) || isset($_GET['logout']))
{
	if((isset($_POST['login']) || isset($_GET['login'])) && (!$user->data['session_logged_in'] || isset($_POST['admin'])))
	{
		$username = isset($_POST['username']) ? phpbb_clean_username($_POST['username']) : '';
		$password = isset($_POST['password']) ? $_POST['password'] : '';

		$login_result = login_db($username, $password, false, true);

		if ($login_result['status'] === LOGIN_ERROR_ATTEMPTS)
		{
			message_die(GENERAL_MESSAGE, sprintf($lang['LOGIN_ATTEMPTS_EXCEEDED'], $config['max_login_attempts'], $config['login_reset_time']));
		}

		if ($login_result['status'] === LOGIN_SUCCESS)
		{
			if(($login_result['user_row']['user_level'] != ADMIN) && !empty($config['board_disable']))
			{
				redirect(append_sid(CMS_PAGE_FORUM, true));
			}
			else
			{
				$set_admin = (isset($_POST['admin'])) ? 1 : 0;
				$persist_login = (isset($_POST['autologin'])) ? 1 : 0;
				$user->session_create($login_result['user_row']['user_id'], $set_admin, $persist_login, $viewonline);

				if(!empty($user->session_id))
				{
					$redirect_url = empty($redirect_url) ? CMS_LOGIN_REDIRECT_PAGE : $redirect_url;
					redirect(append_sid($redirect_url, true));
				}
				else
				{
					message_die(CRITICAL_ERROR, "Couldn't start session: login", "", __LINE__, __FILE__);
				}
			}
		}
		else
		{
			if (($login_result['status'] === LOGIN_ERROR_USERNAME) || ($login_result['status'] === LOGIN_ERROR_PASSWORD) || ($login_result['status'] === LOGIN_ERROR_ACTIVE))
			{
				$error_message = ($login_result['error_msg'] === 'NO_PASSWORD_SUPPLIED') ? $lang[$login_result['error_msg']] : sprintf($lang[$login_result['error_msg']], '<a href="' . append_sid(CMS_PAGE_CONTACT_US) . '">', '</a>');
				message_die(GENERAL_MESSAGE, $error_message);
			}

			meta_refresh(3, (CMS_PAGE_LOGIN . '?redirect=' . htmlspecialchars($redirect_url)));

			$message = $lang['Error_login'] . '<br /><br />' . sprintf($lang['Click_return_login'], '<a href="' . CMS_PAGE_LOGIN . '?redirect=' . htmlspecialchars($redirect_url) . '">', '</a>') . '<br /><br />' . sprintf($lang['Click_return_index'], '<a href="' . append_sid(CMS_PAGE_FORUM) . '">', '</a>');

			message_die(GENERAL_MESSAGE, $message);
		}
	}
	elseif((isset($_GET['logout']) || isset($_POST['logout'])) && $user->data['session_logged_in'])
	{
		// session id check
		if (empty($sid) || ($sid != $user->data['session_id']))
		{
			//message_die(GENERAL_ERROR, 'INVALID_SESSION');
			trigger_error('INVALID_SESSION');
		}
		if($user->data['session_logged_in'])
		{
			$user->session_kill();
		}

		$redirect_url = empty($redirect_url) ? CMS_LOGIN_REDIRECT_PAGE : $redirect_url;
		redirect(append_sid($redirect_url, true));
	}
	else
	{
		$redirect_url = empty($redirect_url) ? CMS_LOGIN_REDIRECT_PAGE : $redirect_url;
		redirect(append_sid($redirect_url, true));
	}
}
else
{
	// Let's remove $auth->acl_get('a_') until I finish coding permissions properly... and also add/remove 'a_' when users are added/removed from administrators in ACP
	//$is_admin = (($user->data['user_level'] == ADMIN) || $auth->acl_get('a_')) ? true : false;
	$is_admin = ($user->data['user_level'] == ADMIN) ? true : false;
	$is_cms_auth = $auth->acl_get('cms_') ? true : false;
	if (empty($is_admin) && empty($is_cms_auth))
	{
		$cms_mode_array = array('block_settings', 'blocks', 'layouts', 'layouts_special');
		$cms_mode = request_var('mode', '');
		$cms_lid = request_var('l_id', 0);
		$cms_sid = request_var('ls_id', 0);
		$cms_bid = request_var('b_id', 0);
		if (in_array($cms_mode, $cms_mode_array))
		{
			$is_cms_auth = (!empty($cms_lid) && !empty($user->data['user_cms_auth']['cmsl_admin'][$cms_lid])) ? true : $is_cms_auth;
			$is_cms_auth = (!empty($cms_lid) && !empty($user->data['user_cms_auth']['cmss_admin'][$cms_sid])) ? true : $is_cms_auth;
			$is_cms_auth = (!empty($cms_lid) && !empty($user->data['user_cms_auth']['cmsb_admin'][$cms_bid])) ? true : $is_cms_auth;
		}
	}

	if(!$user->data['session_logged_in'] || (isset($_GET['admin']) && $user->data['session_logged_in'] && ($is_admin || $is_cms_auth)))
	{
		$skip_nav_cat = true;
		if(!empty($redirect_url))
		{
			$forward_to = $_SERVER['QUERY_STRING'];
			if(preg_match("/^redirect=([a-z0-9\.#\/\?%&=\+\-_]+)/si", $forward_to, $forward_matches))
			{
				$forward_to = (!empty($forward_matches[3])) ? $forward_matches[3] : $forward_matches[1];
				$forward_match = explode('&', $forward_to);

				if(sizeof($forward_match) > 1)
				{
					$forward_page = '';
					for($i = 1; $i < sizeof($forward_match); $i++)
					{
						if(!ereg("sid=", $forward_match[$i]))
						{
							if($forward_page != '')
							{
								$forward_page .= '&';
							}
							$forward_page .= $forward_match[$i];
						}
					}
					$forward_page = $forward_match[0] . '?' . $forward_page;
				}
				else
				{
					$forward_page = $forward_match[0];
				}
			}
		}

		$username = ($user->data['user_id'] != ANONYMOUS) ? $user->data['username'] : '';

		$s_hidden_fields = '<input type="hidden" name="redirect" value="' . htmlspecialchars($forward_page) . '" />';
		$s_hidden_fields .= (isset($_GET['admin'])) ? '<input type="hidden" name="admin" value="1" />' : '';

		$template->assign_vars(array(
			'USERNAME' => $username,

			'L_ENTER_PASSWORD' => (isset($_GET['admin'])) ? $lang['Admin_reauthenticate'] : $lang['Enter_password'],
			'L_SEND_PASSWORD' => $lang['Forgotten_password'],
			'L_RESEND_ACTIVATION_EMAIL' => $lang['Resend_activation_email'],
			'L_STATUS' => $lang['Login_Status'],
			'L_HIDDEN' => $lang['Login_Hidden'],
			'L_VISIBLE' => $lang['Login_Visible'],
			'L_DEFAULT' => $lang['Login_Default'],

			'U_SEND_PASSWORD' => append_sid(CMS_PAGE_PROFILE . '?mode=sendpassword'),
			'U_RESEND_ACTIVATION_EMAIL' => append_sid(CMS_PAGE_PROFILE . '?mode=resend'),

			'S_HIDDEN_FIELDS' => $s_hidden_fields
			)
		);

		if (!isset($_GET['admin']) && ($config['require_activation'] == USER_ACTIVATION_SELF))
		{
			$template->assign_var('S_SWITCH_RESEND_ACTIVATION_EMAIL', true);
		}

		if (!isset($_GET['admin']))
		{
			$template->assign_block_vars('switch_login_type', array());
		}

		full_page_generation('login_body.tpl', $lang['Login'], '', '');
	}
	else
	{
		redirect(append_sid(CMS_PAGE_FORUM, true));
	}
}

?>