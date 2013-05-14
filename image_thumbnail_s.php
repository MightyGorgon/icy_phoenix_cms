<?php
/**
*
* @package Icy Phoenix
* @version $Id$
* @copyright (c) 2008 Icy Phoenix
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

define('IMG_THUMB', true);
define('CT_SECLEVEL', 'MEDIUM');
$ct_ignoregvar = array('');
define('IN_ICYPHOENIX', true);
if (!defined('IP_ROOT_PATH')) define('IP_ROOT_PATH', './');
if (!defined('PHP_EXT')) define('PHP_EXT', substr(strrchr(__FILE__, '.'), 1));
include(IP_ROOT_PATH . 'common.' . PHP_EXT);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();
// End session management

// Get general album information
include(ALBUM_MOD_PATH . 'album_common.' . PHP_EXT);
require(IP_ROOT_PATH . 'includes/class_image.' . PHP_EXT);

// ------------------------------------
// Check the request
// ------------------------------------
$pic_id = request_var('pic_id', '');
if (empty($pic_id))
{
	image_no_thumbnail('no_thumb.jpg');
	exit;
	//die($lang['NO_PICS_SPECIFIED']);
	//message_die(GENERAL_MESSAGE, $lang['NO_PICS_SPECIFIED']);
}
$pic_id = urldecode($pic_id);
$tmp_split = explode('/', $pic_id);
$pic_user_id = intval((int) $tmp_split[0]);
if ($pic_user_id < 0)
{
	image_no_thumbnail('no_thumb.jpg');
	exit;
	//message_die(GENERAL_MESSAGE, $lang['NO_PICS_SPECIFIED']);
}

$pic_filename = $tmp_split[1];
$pic_fullpath = POSTED_IMAGES_PATH . $pic_user_id . '/' . $pic_filename;
$pic_thumbnail = 'thumb_' . $pic_filename;
$pic_thumbnail_fullpath = POSTED_IMAGES_THUMBS_S_PATH . $pic_thumbnail;
$file_part = explode('.', strtolower($pic_filename));
$pic_filetype = $file_part[sizeof($file_part) - 1];
$pic_title = substr($pic_filename, 0, strlen($pic_filename) - strlen($pic_filetype) - 1);
$pic_title_reg = preg_replace('/[^A-Za-z0-9]+/', '_', $pic_title);

if (USERS_SUBFOLDERS_IMG == true)
{
	$pic_thumbnail_path = POSTED_IMAGES_THUMBS_S_PATH . $pic_user_id . '/';
	if (is_dir($pic_thumbnail_path))
	{
		$pic_thumbnail = $pic_filename;
		$pic_thumbnail_fullpath = $pic_thumbnail_path . '/' . $pic_thumbnail;
	}
	else
	{
		$dir_creation = @mkdir($pic_thumbnail_path, 0777);
		if ($dir_creation == true)
		{
			$pic_thumbnail = $pic_filename;
			$pic_thumbnail_fullpath = $pic_thumbnail_path . '/' . $pic_thumbnail;
		}
	}
}

if (!in_array($pic_filetype, array('gif', 'jpg', 'jpeg', 'png')))
{
	image_no_thumbnail('thumb_' . $pic_title_reg . '.' . $pic_filetype);
	exit;
}

// --------------------------------
// Check thumbnail cache. If cache is available we will SEND & EXIT
// --------------------------------

if(($album_config['thumbnail_cache'] == 1) && file_exists($pic_thumbnail_fullpath))
{
	image_output($pic_thumbnail_fullpath, $pic_title_reg, $pic_filetype, 'thumb_');
	exit;
}

if(!@file_exists($pic_fullpath))
{
	image_no_thumbnail('no_thumb.jpg');
	exit;
	//message_die(GENERAL_MESSAGE, $lang['Pic_not_exist']);
}

$pic_size = @getimagesize($pic_fullpath);
$pic_width = $pic_size[0];
$pic_height = $pic_size[1];

if(($pic_width < $album_config['thumbnail_size']) && ($pic_height < $album_config['thumbnail_size']))
{
	$copy_success = @copy($pic_fullpath, $pic_thumbnail_fullpath);
	@chmod($pic_thumbnail_fullpath, 0777);
	image_output($pic_fullpath, $pic_title_reg, $pic_filetype, 'thumb_');
	exit;
}
else
{
	// --------------------------------
	// Cache is empty. Try to re-generate!
	// --------------------------------
	if ($pic_width > $pic_height)
	{
		$thumbnail_width = $album_config['thumbnail_size'];
		$thumbnail_height = $album_config['thumbnail_size'] * ($pic_height / $pic_width);
	}
	else
	{
		$thumbnail_height = $album_config['thumbnail_size'];
		$thumbnail_width = $album_config['thumbnail_size'] * ($pic_width / $pic_height);
	}

	// Old Thumbnails - BEGIN
	// Old thumbnail generation functions, for GD1 and some strange servers...
	if (($album_config['gd_version'] == 1) || ($album_config['use_old_pics_gen'] == 1))
	{
		switch ($pic_filetype)
		{
			case 'gif':
				image_no_thumbnail('thumb_' . $pic_title_reg . '.' . $pic_filetype);
				exit;
				break;
		}
		if($album_config['show_pic_size_on_thumb'] == 1)
		{
			$thumbnail = ($album_config['gd_version'] == 1) ? @imagecreate($thumbnail_width, $thumbnail_height + 16) : @imagecreatetruecolor($thumbnail_width, $thumbnail_height + 16);
		}
		else
		{
			$thumbnail = ($album_config['gd_version'] == 1) ? @imagecreate($thumbnail_width, $thumbnail_height) : @imagecreatetruecolor($thumbnail_width, $thumbnail_height);
		}

		$resize_function = ($album_config['gd_version'] == 1) ? 'imagecopyresized' : 'imagecopyresampled';

		@$resize_function($thumbnail, $pic_fullpath, 0, 0, 0, 0, $thumbnail_width, $thumbnail_height, $pic_width, $pic_height);

		if($album_config['show_pic_size_on_thumb'] == 1)
		{
			$dimension_font = 1;
			$dimension_filesize = filesize($pic_fullpath);
			$dimension_string = intval($pic_width) . 'x' . intval($pic_height) . '(' . intval($dimension_filesize / 1024) . 'KB)';
			$dimension_colour = ImageColorAllocate($thumbnail, 255, 255, 255);
			$dimension_height = imagefontheight($dimension_font);
			$dimension_width = imagefontwidth($dimension_font) * strlen($dimension_string);
			$dimension_x = ($thumbnail_width - $dimension_width) / 2;
			$dimension_y = $thumbnail_height + ((16 - $dimension_height) / 2);
			imagestring($thumbnail, 1, $dimension_x, $dimension_y, $dimension_string, $dimension_colour);
		}

		if ($album_config['thumbnail_cache'] == 1)
		{
			// ------------------------
			// Re-generate successfully. Write it to disk!
			// ------------------------
			switch ($pic_filetype)
			{
				case 'jpg':
					@imagejpeg($thumbnail, $pic_thumbnail_fullpath, $album_config['thumbnail_quality']);
					break;
				case 'png':
					@imagepng($thumbnail, $pic_thumbnail_fullpath);
					break;
			}
			@chmod($pic_thumbnail_fullpath, 0777);
		}

		// ----------------------------
		// After write to disk, donot forget to send to browser also
		// ----------------------------
		switch ($pic_filetype)
		{
			case 'jpg':
				@imagejpeg($thumbnail, '', $album_config['thumbnail_quality']);
				break;
			case 'png':
				@imagepng($thumbnail);
				break;
			default:
				image_no_thumbnail('thumb_' . $pic_title_reg . '.' . $pic_filetype);
				exit;
		}
		exit;
	}
	// Old Thumbnails - END

	$Image = new ImgObj();

	if ($pic_filetype == 'jpg')
	{
		$Image->ReadSourceFileJPG($pic_fullpath);
	}
	else
	{
		$Image->ReadSourceFile($pic_fullpath);
	}

	$Image->Resize($thumbnail_width, $thumbnail_height);

	if($album_config['show_pic_size_on_thumb'] == 1)
	{
		$dimension_string = intval($pic_width) . 'x' . intval($pic_height) . '(' . intval(filesize($pic_fullpath) / 1024) . 'KB)';
		$Image->Text($dimension_string);
	}

	if ($album_config['thumbnail_cache'] == 1)
	{
		if ($pic_filetype == 'jpg')
		{
			$Image->SendToFileJPG($pic_thumbnail_fullpath, $album_config['thumbnail_quality']);
		}
		else
		{
			$Image->SendToFile($pic_thumbnail_fullpath, $album_config['thumbnail_quality']);
		}
		//$Image->SendToFile($pic_thumbnail_fullpath, $album_config['thumbnail_quality']);
		//@chmod($pic_thumbnail_fullpath, 0777);
	}

	if ($pic_filetype == 'jpg')
	{
		$Image->SendToBrowserJPG($pic_title_reg, $pic_filetype, 'thumb_', '', $album_config['thumbnail_quality']);
	}
	else
	{
		$Image->SendToBrowser($pic_title_reg, $pic_filetype, 'thumb_', '', $album_config['thumbnail_quality']);
	}

	if ($Image == true)
	{
		$Image->Destroy();
		exit;
	}
	else
	{
		$Image->Destroy();
		image_no_thumbnail('thumb_' . $pic_title_reg . '.' . $pic_filetype);
		exit;
	}
}

?>