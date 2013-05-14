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
* @Extra credits for this file
* difus (admin@digi-sky.net)
*
*/

define('IN_ICYPHOENIX', true);
if (!defined('IP_ROOT_PATH')) define('IP_ROOT_PATH', './');
if (!defined('PHP_EXT')) define('PHP_EXT', substr(strrchr(__FILE__, '.'), 1));
include(IP_ROOT_PATH . 'common.' . PHP_EXT);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();
// End session management

include(IP_ROOT_PATH . 'includes/class_images.' . PHP_EXT);
$class_images = new class_images();

// This page is not in layout special...
$cms_page['page_id'] = 'pic_upload';
$cms_page['page_nav'] = false;
$cms_page['global_blocks'] = false;
$cms_auth_level = (isset($config['auth_view_pic_upload']) ? $config['auth_view_pic_upload'] : AUTH_ALL);
check_page_auth($cms_page['page_id'], $cms_auth_level);

$upload_dir = POSTED_IMAGES_PATH;
$filetypes = 'jpg,jpeg,gif,png';
$maxsize = (1024 * 1024);

// We need to keep it here... so also error messages will initialize it correctly!
$gen_simple_header = true;

if(isset($_FILES['userfile']))
{
	$filename = strtolower($_FILES['userfile']['name']);
	$types = explode(',', $filetypes);
	$file = explode('.', $filename);
	$extension = $file[sizeof($file) - 1];
	$filename = substr($filename, 0, strlen($filename) - strlen($extension) - 1);

	if(!in_array($extension, $types))
	{
		message_die(GENERAL_MESSAGE, $lang['Upload_File_Type_Allowed'] . ': ' . str_replace(',', ', ', $filetypes) . '.');
	}

	$template_to_parse = 'uploaded_image_bbc_popup.tpl';

	$server_path = create_server_url();

	if ($user->data['user_id'] < 0)
	{
		$filename = 'guest_' . preg_replace('/[^a-z0-9]+/', '_', $filename);
	}
	else
	{
		$filename = preg_replace('/[^a-z0-9]+/', '_', $filename);
		if (USERS_SUBFOLDERS_IMG == true)
		{
			if (is_dir($upload_dir . $user->data['user_id']))
			{
				$upload_dir = $upload_dir . $user->data['user_id'] . '/';
			}
			else
			{
				$dir_creation = @mkdir($upload_dir . $user->data['user_id'], 0777);
				if ($dir_creation == true)
				{
					$upload_dir = $upload_dir . $user->data['user_id'] . '/';
				}
				else
				{
					$filename = 'user_' . $user->data['user_id'] . '_' . $filename;
				}
			}
		}
		else
		{
			$filename = 'user_' . $user->data['user_id'] . '_' . $filename;
		}
	}
	while(file_exists($upload_dir . $filename . '.' . $extension))
	{
		$filename = $filename . '_' . time() . '_' . mt_rand(100000, 999999);
	}
	$filename_tmp = $_FILES['userfile']['tmp_name'];
	$file_size = $_FILES['userfile']['size'];

	if(empty($filename))
	{
		message_die(GENERAL_MESSAGE, $lang['Upload_Image_Empty']);
	}

	if($file_size > $maxsize)
	{
		message_die(GENERAL_MESSAGE, $lang['Upload_File_Too_Big'] . ' ' . ($maxsize / 1000) . 'KB');
	}

	if(is_uploaded_file($filename_tmp))
	{
		@move_uploaded_file($filename_tmp, $upload_dir . $filename . '.' . $extension);
		@chmod($upload_dir . $filename . '.' . $extension, 0777);
	}

	$pic_size = @getimagesize($upload_dir . $filename . '.' . $extension);
	if($pic_size == false)
	{
		@unlink($upload_dir . $filename . '.' . $extension);
		message_die(GENERAL_MESSAGE, $lang['Upload_File_Type_Allowed'] . ': ' . str_replace(',', ', ', $filetypes) . '.');
	}

	$image_data = array(
		'pic_filename' => $filename . '.' . $extension,
		'pic_size' => filesize($upload_dir . $filename . '.' . $extension),
		'pic_title' => $filename . '.' . $extension,
		'pic_desc' => $filename . '.' . $extension,
		'pic_user_id' => $user->data['user_id'],
		'pic_user_ip' => $user->ip,
		'pic_time' => time(),
	);
	$image_submit = $class_images->submit_image($image_data, 'insert');

	$template->assign_vars(array(
		'S_ACTION' => append_sid('upload.' . PHP_EXT),
		'L_UPLOAD_IMAGE' => $lang['Upload_Image_Local'],
		'L_BBCODE' => $lang['BBCode'],
		'L_BBCODE_DES' => $lang['Uploaded_Image_BBC'],
		'L_UPLOAD_SUCCESS' => $lang['Uploaded_Image_Success'],
		'L_INSERT_BBC' => $lang['Upload_Insert_Image'],
		'L_CLOSE_WINDOW' => $lang['Upload_Close'],
		'IMG_BBCODE' => '[img]' . $server_path . substr($upload_dir, strlen(IP_ROOT_PATH)) . $filename . '.' . $extension . '[/img]',
		)
	);

}
else
{
	$template_to_parse = 'upload_image_popup.tpl';

	$template->assign_vars(array(
		'S_ACTION' => append_sid('upload.' . PHP_EXT),
		'L_UPLOAD_IMAGE' => $lang['Upload_Image_Local'],
		'L_UPLOAD_IMAGE_EXPLAIN' => $lang['Upload_Image_Local_Explain'],
		'L_ALLOWED_EXT' => $lang['Upload_File_Type_Allowed'] . ': ' . str_replace(',', ', ', $filetypes) . '.<br />' . $lang['Upload_File_Max_Size'] . ' ' . floor($maxsize / 1024) . $lang['KB'] . '.',
		'L_SUBMIT' => $lang['Submit'],
		)
	);

}

full_page_generation($template_to_parse, $lang['Upload_Image_Local'], '', '');

?>