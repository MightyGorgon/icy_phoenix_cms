SET storage_engine=MYISAM;

## --------------------------------------------------------

## `phpbb_ads`

CREATE TABLE `phpbb_ads` (
	`ad_id` MEDIUMINT(8) unsigned NOT NULL auto_increment,
	`ad_title` VARCHAR(255) NOT NULL,
	`ad_text` TEXT NOT NULL,
	`ad_position` VARCHAR(255) NOT NULL,
	`ad_auth` TINYINT(1) NOT NULL default '0',
	`ad_format` TINYINT(1) NOT NULL default '0',
	`ad_active` TINYINT(1) NOT NULL default '0',
	PRIMARY KEY (`ad_id`)
);

## `phpbb_ads`


## --------------------------------------------------------

## `phpbb_banlist`

CREATE TABLE `phpbb_banlist` (
	`ban_id` MEDIUMINT(8) unsigned NOT NULL auto_increment,
	`ban_userid` MEDIUMINT(8) NOT NULL DEFAULT '0',
	`ban_ip` VARCHAR(40) NOT NULL DEFAULT '',
	`ban_email` VARCHAR(255) DEFAULT NULL,
	`ban_start` INT(11) DEFAULT NULL,
	`ban_end` INT(11) DEFAULT NULL,
	`ban_by_userid` MEDIUMINT(8) DEFAULT NULL,
	`ban_priv_reason` TEXT NOT NULL,
	`ban_pub_reason_mode` TINYINT(1) DEFAULT NULL,
	`ban_pub_reason` TEXT NOT NULL,
	PRIMARY KEY (`ban_id`),
	KEY `ban_ip_user_id` (`ban_ip`,`ban_userid`)
);

## `phpbb_banlist`


## --------------------------------------------------------

## `phpbb_bbcodes`

CREATE TABLE `phpbb_bbcodes` (
	bbcode_id MEDIUMINT(8) UNSIGNED NOT NULL auto_increment,
	bbcode_tag VARCHAR(16) DEFAULT '' NOT NULL,
	bbcode_helpline VARCHAR(255) DEFAULT '' NOT NULL,
	display_on_posting TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
	bbcode_match TEXT NOT NULL,
	bbcode_tpl MEDIUMTEXT NOT NULL,
	first_pass_match MEDIUMTEXT NOT NULL,
	first_pass_replace MEDIUMTEXT NOT NULL,
	second_pass_match MEDIUMTEXT NOT NULL,
	second_pass_replace MEDIUMTEXT NOT NULL,
	PRIMARY KEY (bbcode_id),
	KEY display_on_post (display_on_posting)
);

## `phpbb_bbcodes`


## --------------------------------------------------------

## `phpbb_bots`

CREATE TABLE phpbb_bots (
	bot_id MEDIUMINT(8) UNSIGNED NOT NULL auto_increment,
	bot_active TINYINT(1) UNSIGNED DEFAULT '1' NOT NULL,
	bot_name VARCHAR(255) DEFAULT '' NOT NULL,
	bot_color VARCHAR(255) DEFAULT '' NOT NULL,
	bot_agent VARCHAR(255) DEFAULT '' NOT NULL,
	bot_ip VARCHAR(255) DEFAULT '' NOT NULL,
	bot_last_visit VARCHAR(11) DEFAULT '0' NOT NULL,
	bot_visit_counter MEDIUMINT(8) DEFAULT '0' NOT NULL,
	PRIMARY KEY (bot_id),
	KEY bot_name (bot_name),
	KEY bot_active (bot_active)
);

## `phpbb_bots`


## --------------------------------------------------------

## `phpbb_config`

CREATE TABLE `phpbb_config` (
	`config_name` VARCHAR(255) NOT NULL DEFAULT '',
	`config_value` TEXT NOT NULL,
	PRIMARY KEY (`config_name`)
);

## `phpbb_config`


## --------------------------------------------------------

## `phpbb_confirm`

CREATE TABLE `phpbb_confirm` (
	confirm_id CHAR(32) DEFAULT '' NOT NULL,
	session_id CHAR(32) DEFAULT '' NOT NULL,
	confirm_type TINYINT(3) DEFAULT '0' NOT NULL,
	code VARCHAR(8) DEFAULT '' NOT NULL,
	seed INT(10) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (session_id, confirm_id),
	KEY confirm_type (confirm_type)
);

## `phpbb_confirm`


## --------------------------------------------------------

## `phpbb_flags`

CREATE TABLE `phpbb_flags` (
	`flag_id` INT(10) NOT NULL auto_increment,
	`flag_name` VARCHAR(30) DEFAULT NULL,
	`flag_image` VARCHAR(30) DEFAULT NULL,
	PRIMARY KEY (`flag_id`)
);

## `phpbb_flags`


## --------------------------------------------------------

## `phpbb_groups`

CREATE TABLE `phpbb_groups` (
	`group_id` MEDIUMINT(8) NOT NULL auto_increment,
	`group_type` TINYINT(4) NOT NULL DEFAULT '1',
	`group_founder_manage` TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
	`group_name` VARCHAR(255) DEFAULT '' NOT NULL,
	`group_description` TEXT NOT NULL,
	`group_display` TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
	`group_moderator` MEDIUMINT(8) NOT NULL DEFAULT '0',
	`group_single_user` TINYINT(1) NOT NULL DEFAULT '1',
	`group_rank` MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
	`group_color` VARCHAR(16) DEFAULT '' NOT NULL,
	`group_legend` TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
	`group_legend_order` MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
	`group_sig_chars` MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
	`group_receive_pm` TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
	`group_message_limit` MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
	`group_max_recipients` MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
	`group_skip_auth` TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
	`group_count` INT(4) unsigned DEFAULT '99999999',
	`group_count_max` INT(4) unsigned DEFAULT '99999999',
	`group_count_enable` SMALLINT(2) unsigned DEFAULT '0',
	`upi2db_on` TINYINT(1) NOT NULL DEFAULT '1',
	`upi2db_min_posts` MEDIUMINT(4) NOT NULL DEFAULT '0',
	`upi2db_min_regdays` MEDIUMINT(4) NOT NULL DEFAULT '0',
	PRIMARY KEY (`group_id`),
	KEY `group_legend_name` (`group_legend`, `group_name`),
	KEY `group_single_user` (`group_single_user`)
);

## `phpbb_groups`


## --------------------------------------------------------

## `phpbb_logins`

CREATE TABLE `phpbb_logins` (
	`login_id` MEDIUMINT(8) unsigned NOT NULL auto_increment,
	`login_userid` MEDIUMINT(8) NOT NULL DEFAULT '0',
	`login_ip` VARCHAR(40) NOT NULL DEFAULT '0',
	`login_user_agent` VARCHAR(255) NOT NULL DEFAULT 'n/a',
	`login_time` INT(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`login_id`)
);

## `phpbb_logins`


## --------------------------------------------------------

## `phpbb_megamail`

CREATE TABLE `phpbb_megamail` (
	`mail_id` smallint unsigned NOT NULL auto_increment,
	`mailsession_id` VARCHAR(32) NOT NULL,
	`mass_pm` TINYINT(1) NOT NULL default '0',
	`user_id` MEDIUMINT(8) NOT NULL,
	`group_id` MEDIUMINT(8) NOT NULL,
	`email_subject` VARCHAR(255) NOT NULL,
	`email_body` TEXT NOT NULL,
	`email_format` TINYINT(1) NOT NULL default '0',
	`batch_start` MEDIUMINT(8) NOT NULL,
	`batch_size` smallint UNSIGNED NOT NULL,
	`batch_wait` smallint NOT NULL,
	`status` smallint NOT NULL,
	PRIMARY KEY (`mail_id`)
);

## `phpbb_megamail`


## --------------------------------------------------------

## `phpbb_modules`

CREATE TABLE `phpbb_modules` (
	`module_id` MEDIUMINT(8) UNSIGNED NOT NULL auto_increment,
	`module_enabled` TINYINT(1) UNSIGNED DEFAULT '1' NOT NULL,
	`module_display` TINYINT(1) UNSIGNED DEFAULT '1' NOT NULL,
	`module_basename` VARCHAR(255) DEFAULT '' NOT NULL,
	`module_class` VARCHAR(10) DEFAULT '' NOT NULL,
	`parent_id` MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
	`left_id` MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
	`right_id` MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
	`module_langname` VARCHAR(255) DEFAULT '' NOT NULL,
	`module_mode` VARCHAR(255) DEFAULT '' NOT NULL,
	`module_auth` VARCHAR(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (`module_id`),
	KEY `left_right_id` (`left_id`, `right_id`),
	KEY `module_enabled` (`module_enabled`),
	KEY `class_left_id` (`module_class`, `left_id`)
);

## `phpbb_modules`


## --------------------------------------------------------

## `phpbb_profile_fields`

CREATE TABLE `phpbb_profile_fields` (
	`field_id` MEDIUMINT(8) unsigned NOT NULL auto_increment,
	`field_name` VARCHAR(255) NOT NULL DEFAULT '',
	`field_description` VARCHAR(255) DEFAULT NULL,
	`field_type` TINYINT(4) unsigned NOT NULL DEFAULT '0',
	`text_field_default` VARCHAR(255) DEFAULT NULL,
	`text_field_maxlen` INT(255) unsigned NOT NULL DEFAULT '255',
	`text_area_default` TEXT NOT NULL,
	`text_area_maxlen` INT(255) unsigned NOT NULL DEFAULT '1024',
	`radio_button_default` VARCHAR(255) DEFAULT NULL,
	`radio_button_values` TEXT NOT NULL,
	`checkbox_default` TEXT NOT NULL,
	`checkbox_values` TEXT NOT NULL,
	`is_required` TINYINT(2) unsigned NOT NULL DEFAULT '0',
	`users_can_view` TINYINT(2) unsigned NOT NULL DEFAULT '1',
	`view_in_profile` TINYINT(2) unsigned NOT NULL DEFAULT '1',
	`profile_location` TINYINT(2) unsigned NOT NULL DEFAULT '2',
	`view_in_memberlist` TINYINT(2) unsigned NOT NULL DEFAULT '0',
	`view_in_topic` TINYINT(2) unsigned NOT NULL DEFAULT '0',
	`topic_location` TINYINT(2) unsigned NOT NULL DEFAULT '1',
	PRIMARY KEY (`field_id`),
	UNIQUE KEY `field_name` (`field_name`),
	KEY `field_type` (`field_type`)
);

## `phpbb_profile_fields`


## --------------------------------------------------------

## `phpbb_referers`

CREATE TABLE `phpbb_referers` (
	`id` INT(11) NOT NULL auto_increment,
	`host` VARCHAR(255) NOT NULL DEFAULT '',
	`url` VARCHAR(255) NOT NULL DEFAULT '',
	`t_url` VARCHAR(255) NOT NULL DEFAULT '',
	`ip` VARCHAR(40) NOT NULL DEFAULT '',
	`hits` INT(11) NOT NULL DEFAULT '1',
	`firstvisit` INT(11) NOT NULL DEFAULT '0',
	`lastvisit` INT(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
);

## `phpbb_referers`


## --------------------------------------------------------

## `phpbb_search_results`

CREATE TABLE `phpbb_search_results` (
	`search_id` INT(11) unsigned NOT NULL DEFAULT '0',
	`session_id` VARCHAR(32) NOT NULL DEFAULT '',
	`search_array` MEDIUMTEXT NOT NULL,
	`search_time` INT(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`search_id`),
	KEY `session_id` (`session_id`)
);

## `phpbb_search_results`


## --------------------------------------------------------

## `phpbb_search_wordlist`

CREATE TABLE `phpbb_search_wordlist` (
	`word_text` VARCHAR(50) binary NOT NULL DEFAULT '',
	`word_id` MEDIUMINT(8) unsigned NOT NULL auto_increment,
	`word_common` TINYINT(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`word_text`),
	KEY `word_id` (`word_id`)
);

## `phpbb_search_wordlist`


## --------------------------------------------------------

## `phpbb_search_wordmatch`

CREATE TABLE `phpbb_search_wordmatch` (
	`post_id` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	`word_id` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	`title_match` TINYINT(1) NOT NULL DEFAULT '0',
	KEY `post_id` (`post_id`),
	KEY `word_id` (`word_id`)
);

## `phpbb_search_wordmatch`


## --------------------------------------------------------

## `phpbb_sessions`

CREATE TABLE `phpbb_sessions` (
	`session_id` VARCHAR(32) NOT NULL DEFAULT '',
	`session_user_id` MEDIUMINT(8) NOT NULL DEFAULT '0',
	`session_start` INT(11) NOT NULL DEFAULT '0',
	`session_time` INT(11) NOT NULL DEFAULT '0',
	`session_ip` VARCHAR(40) NOT NULL DEFAULT '0',
	`session_browser` VARCHAR(255) DEFAULT '' NOT NULL,
	`session_page` VARCHAR(255) NOT NULL DEFAULT '',
	`session_logged_in` TINYINT(1) NOT NULL DEFAULT '0',
	`session_forum_id` MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
	`session_topic_id` MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
	`session_last_visit` INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	`session_forwarded_for` VARCHAR(255) DEFAULT '' NOT NULL,
	`session_viewonline` TINYINT(1) UNSIGNED DEFAULT '1' NOT NULL,
	`session_autologin` TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
	`session_admin` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`session_id`),
	KEY `session_user_id` (`session_user_id`),
	KEY `session_fid` (`session_forum_id`)
);

## `phpbb_sessions`


## --------------------------------------------------------

## `phpbb_sessions_keys`

CREATE TABLE `phpbb_sessions_keys` (
	`key_id` VARCHAR(32) NOT NULL DEFAULT '0',
	`user_id` MEDIUMINT(8) NOT NULL DEFAULT '0',
	`last_ip` VARCHAR(40) NOT NULL DEFAULT '',
	`last_login` INT(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`key_id`,`user_id`),
	KEY `last_login` (`last_login`)
);

## `phpbb_sessions_keys`


## --------------------------------------------------------

## `phpbb_smilies`

CREATE TABLE `phpbb_smilies` (
	`smilies_id` SMALLINT(5) unsigned NOT NULL auto_increment,
	`code` VARCHAR(50) DEFAULT NULL,
	`smile_url` VARCHAR(100) DEFAULT NULL,
	`emoticon` VARCHAR(75) DEFAULT NULL,
	`smilies_order` INT(5) NOT NULL DEFAULT '0',
	PRIMARY KEY (`smilies_id`)
);

## `phpbb_smilies`


## --------------------------------------------------------

## `phpbb_themes`

CREATE TABLE `phpbb_themes` (
	`themes_id` MEDIUMINT(8) unsigned NOT NULL auto_increment,
	`template_name` VARCHAR(30) NOT NULL DEFAULT '',
	`style_name` VARCHAR(30) NOT NULL DEFAULT '',
	`head_stylesheet` VARCHAR(100) DEFAULT NULL,
	`body_background` VARCHAR(100) DEFAULT NULL,
	`body_bgcolor` VARCHAR(6) DEFAULT NULL,
	`tr_class1` VARCHAR(25) DEFAULT NULL,
	`tr_class2` VARCHAR(25) DEFAULT NULL,
	`tr_class3` VARCHAR(25) DEFAULT NULL,
	`td_class1` VARCHAR(25) DEFAULT NULL,
	`td_class2` VARCHAR(25) DEFAULT NULL,
	`td_class3` VARCHAR(25) DEFAULT NULL,
	PRIMARY KEY (`themes_id`)
);

## `phpbb_themes`


## --------------------------------------------------------

## `phpbb_topics_tags_list`

CREATE TABLE `phpbb_topics_tags_list` (
	`tag_text` VARCHAR(50) binary NOT NULL DEFAULT '',
	`tag_id` MEDIUMINT(8) unsigned NOT NULL auto_increment,
	`tag_count` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`tag_text`),
	KEY `tag_id` (`tag_id`)
);

## `phpbb_topics_tags_list`


## --------------------------------------------------------

## `phpbb_topics_tags_match`

CREATE TABLE `phpbb_topics_tags_match` (
	`tag_id` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	`topic_id` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	`forum_id` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	KEY `tag_id` (`tag_id`),
	KEY `topic_id` (`topic_id`)
);

## `phpbb_topics_tags_match`


## --------------------------------------------------------

## `phpbb_user_group`

CREATE TABLE `phpbb_user_group` (
	`group_id` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	`user_id` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	`group_leader` TINYINT(1) unsigned DEFAULT '0' NOT NULL,
	`user_pending` TINYINT(1) DEFAULT '1' NOT NULL,
	KEY `group_id` (`group_id`),
	KEY `user_id` (`user_id`),
	KEY `group_leader` (`group_leader`)
);

## `phpbb_user_group`


## --------------------------------------------------------

## `phpbb_users`

CREATE TABLE `phpbb_users` (
	`user_id` MEDIUMINT(8) NOT NULL DEFAULT '0',
	`user_active` TINYINT(1) DEFAULT '1',
	`user_mask` TINYINT(1) DEFAULT '0',
	`user_cms_auth` TEXT NOT NULL,
	`user_permissions` MEDIUMTEXT NOT NULL,
	`user_perm_from` MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
	`username` VARCHAR(36) NOT NULL DEFAULT '',
	`username_clean` VARCHAR(255) NOT NULL DEFAULT '',
	`user_email` VARCHAR(255) DEFAULT NULL,
	`user_email_hash` bigint(20) DEFAULT '0' NOT NULL,
	`user_first_name` VARCHAR(255) NOT NULL DEFAULT '',
	`user_last_name` VARCHAR(255) NOT NULL DEFAULT '',
	`user_password` VARCHAR(40) NOT NULL DEFAULT '',
	`user_newpasswd` VARCHAR(40) NOT NULL DEFAULT '',
	`user_passchg` INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	`user_pass_convert` TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
	`user_phone` VARCHAR(255) DEFAULT NULL,
	`user_selfdes` TEXT NOT NULL,
	`user_form_salt` VARCHAR(32) DEFAULT '' NOT NULL,
	`user_session_time` INT(11) NOT NULL DEFAULT '0',
	`user_session_page` VARCHAR(255) NOT NULL DEFAULT '',
	`user_browser` VARCHAR(255) NOT NULL DEFAULT '',
	`user_lastvisit` INT(11) NOT NULL DEFAULT '0',
	`user_regdate` INT(11) NOT NULL DEFAULT '0',
	`user_type` TINYINT(2) DEFAULT '0' NOT NULL,
	`user_level` TINYINT(4) DEFAULT '0',
	`user_posts` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	`user_timezone` DECIMAL(5,2) NOT NULL DEFAULT '0.00',
	`user_style` MEDIUMINT(8) DEFAULT NULL,
	`user_lang` VARCHAR(255) DEFAULT NULL,
	`user_dateformat` VARCHAR(14) NOT NULL DEFAULT 'd M Y H:i',
	`user_private_chat_alert` VARCHAR(255) NOT NULL DEFAULT '0',
	`user_emailtime` INT(11) DEFAULT NULL,
	`user_options` INT(11) UNSIGNED DEFAULT '895' NOT NULL,
	`user_allow_mass_email` TINYINT(1) NOT NULL DEFAULT '1',
	`user_notify` TINYINT(1) NOT NULL DEFAULT '1',
	`user_rank` INT(11) DEFAULT '0',
	`user_avatar` VARCHAR(100) DEFAULT NULL,
	`user_avatar_type` TINYINT(4) NOT NULL DEFAULT '0',
	`user_website` VARCHAR(100) DEFAULT NULL,
	`user_from` VARCHAR(100) DEFAULT NULL,
	`user_sig` TEXT NOT NULL,
	`user_aim` VARCHAR(255) DEFAULT '' NOT NULL,
	`user_facebook` VARCHAR(255) DEFAULT '' NOT NULL,
	`user_flickr` VARCHAR(255) DEFAULT '' NOT NULL,
	`user_googleplus` VARCHAR(255) DEFAULT '' NOT NULL,
	`user_icq` VARCHAR(15) DEFAULT '' NOT NULL,
	`user_jabber` VARCHAR(255) DEFAULT '' NOT NULL,
	`user_linkedin` VARCHAR(255) DEFAULT '' NOT NULL,
	`user_msnm` VARCHAR(255) DEFAULT '' NOT NULL,
	`user_twitter` VARCHAR(255) DEFAULT '' NOT NULL,
	`user_skype` VARCHAR(255) DEFAULT '' NOT NULL,
	`user_yim` VARCHAR(255) DEFAULT '' NOT NULL,
	`user_youtube` VARCHAR(255) DEFAULT '' NOT NULL,
	`user_occ` VARCHAR(255) DEFAULT '' NOT NULL,
	`user_interests` VARCHAR(255) DEFAULT '' NOT NULL,
	`user_actkey` VARCHAR(32) DEFAULT NULL,
	`user_birthday_y` VARCHAR(4) NOT NULL DEFAULT '',
	`user_birthday_m` VARCHAR(2) NOT NULL DEFAULT '',
	`user_birthday_d` VARCHAR(2) NOT NULL DEFAULT '',
	`user_color_group` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	`user_color` VARCHAR(16) NOT NULL DEFAULT '',
	`user_gender` TINYINT(4) NOT NULL DEFAULT '0',
	`user_totaltime` INT(11) DEFAULT '0',
	`user_totallogon` INT(11) DEFAULT '0',
	`user_totalpages` INT(11) DEFAULT '0',
	`user_time_mode` TINYINT(4) NOT NULL DEFAULT '5',
	`user_dst_time_lag` TINYINT(4) NOT NULL DEFAULT '60',
	`user_registered_ip` VARCHAR(40) DEFAULT NULL,
	`user_registered_hostname` VARCHAR(255) DEFAULT NULL,
	`user_topics_per_page` VARCHAR(5) DEFAULT NULL,
	`user_posts_per_page` VARCHAR(5) DEFAULT NULL,
	`user_login_attempts` TINYINT(4) DEFAULT '0' NOT NULL,
	`user_last_login_attempt` INT(11) NOT NULL DEFAULT '0',
	`user_from_flag` VARCHAR(30) DEFAULT NULL,
	`user_personal_pics_count` INT(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`user_id`),
	KEY `user_session_time` (`user_session_time`)
);

## `phpbb_users`


## ICY PHOENIX LOGS - BEGIN

CREATE TABLE `phpbb_log` (
	`log_id` MEDIUMINT(8) UNSIGNED NOT NULL auto_increment,
	`log_type` TINYINT(4) DEFAULT '0' NOT NULL,
	`user_id` MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
	`forum_id` MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
	`topic_id` MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
	`reportee_id` MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
	`log_ip` VARCHAR(40) DEFAULT '' NOT NULL,
	`log_time` INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	`log_operation` TEXT NOT NULL,
	`log_data` MEDIUMTEXT NOT NULL,
	PRIMARY KEY (`log_id`),
	KEY log_type (`log_type`),
	KEY forum_id (`forum_id`),
	KEY topic_id (`topic_id`),
	KEY reportee_id (`reportee_id`),
	KEY user_id (`user_id`)
);

CREATE TABLE `phpbb_logs` (
	`log_id` INT(11) unsigned NOT NULL auto_increment,
	`log_time` VARCHAR(11) NOT NULL,
	`log_page` VARCHAR(255) NOT NULL DEFAULT '',
	`log_user_id` INT(10) NOT NULL,
	`log_action` VARCHAR(60) NOT NULL DEFAULT '',
	`log_desc` MEDIUMTEXT NOT NULL,
	`log_target` INT(10) NOT NULL DEFAULT '0',
	PRIMARY KEY (`log_id`)
);

## ICY PHOENIX LOGS - END


## Icy Phoenix CMS - BEGIN

CREATE TABLE `phpbb_cms_block_position` (
	`bpid` INT(10) NOT NULL auto_increment,
	`layout` INT(10) NOT NULL DEFAULT '1',
	`pkey` VARCHAR(30) NOT NULL DEFAULT '',
	`bposition` CHAR(2) NOT NULL DEFAULT '',
	PRIMARY KEY (`bpid`)
);

CREATE TABLE `phpbb_cms_block_settings` (
	`bs_id` INT(10) NOT NULL AUTO_INCREMENT,
	`user_id` INT(10) NOT NULL,
	`name` VARCHAR(255) NOT NULL default '',
	`content` TEXT NOT NULL ,
	`blockfile` VARCHAR(255) NOT NULL default '',
	`view` TINYINT(1) NOT NULL default 0,
	`type` TINYINT(1) NOT NULL default 1,
	`groups` tinytext NOT NULL,
	`locked` TINYINT(1) NOT NULL DEFAULT 1,
	PRIMARY KEY (`bs_id`)
);

CREATE TABLE `phpbb_cms_block_variable` (
	`bvid` INT(10) NOT NULL auto_increment,
	`bid` INT(10) NOT NULL DEFAULT '0',
	`label` VARCHAR(30) NOT NULL DEFAULT '',
	`sub_label` VARCHAR(255) DEFAULT NULL,
	`config_name` VARCHAR(30) NOT NULL DEFAULT '',
	`field_options` VARCHAR(255) DEFAULT NULL,
	`field_values` VARCHAR(255) DEFAULT NULL,
	`type` TINYINT(1) NOT NULL DEFAULT '0',
	`block` VARCHAR(255) DEFAULT NULL,
	PRIMARY KEY (`bvid`)
);

CREATE TABLE `phpbb_cms_blocks` (
	`bid` INT(10) NOT NULL auto_increment,
	`bs_id` INT(10) UNSIGNED NOT NULL,
	`block_cms_id` INT(10) UNSIGNED NOT NULL,
	`layout` INT(10) NOT NULL DEFAULT '0',
	`layout_special` INT(10) NOT NULL DEFAULT '0',
	`title` VARCHAR(60) NOT NULL DEFAULT '',
	`bposition` CHAR(2) NOT NULL DEFAULT '',
	`weight` INT(10) NOT NULL DEFAULT '1',
	`active` TINYINT(1) NOT NULL DEFAULT '1',
	`border` TINYINT(1) NOT NULL DEFAULT '1',
	`titlebar` TINYINT(1) NOT NULL DEFAULT '1',
	`background` TINYINT(1) NOT NULL DEFAULT '1',
	`local` TINYINT(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`bid`)
);

CREATE TABLE `phpbb_cms_config` (
	`id` INT(10) unsigned NOT NULL auto_increment,
	`bid` INT(10) NOT NULL DEFAULT '0',
	`config_name` VARCHAR(255) NOT NULL DEFAULT '',
	`config_value` VARCHAR(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
);

CREATE TABLE `phpbb_cms_layout` (
	`lid` INT(10) unsigned NOT NULL auto_increment,
	`name` VARCHAR(100) NOT NULL DEFAULT '',
	`filename` VARCHAR(100) NOT NULL DEFAULT '',
	`template` VARCHAR(100) NOT NULL DEFAULT '',
	`layout_cms_id` INT(10) UNSIGNED NOT NULL,
	`global_blocks` TINYINT(1) NOT NULL DEFAULT '0',
	`page_nav` TINYINT(1) NOT NULL DEFAULT '1',
	`config_vars` TEXT NOT NULL,
	`view` TINYINT(1) NOT NULL DEFAULT '0',
	`groups` TINYTEXT NOT NULL,
	PRIMARY KEY (`lid`)
);

CREATE TABLE `phpbb_cms_layout_special` (
	`lsid` INT(10) unsigned NOT NULL auto_increment,
	`page_id` VARCHAR(100) NOT NULL DEFAULT '',
	`locked` TINYINT(1) NOT NULL DEFAULT '1',
	`name` VARCHAR(100) NOT NULL DEFAULT '',
	`filename` VARCHAR(100) NOT NULL DEFAULT '',
	`template` VARCHAR(100) NOT NULL DEFAULT '',
	`global_blocks` TINYINT(1) NOT NULL DEFAULT '0',
	`page_nav` TINYINT(1) NOT NULL DEFAULT '1',
	`config_vars` TEXT NOT NULL,
	`view` TINYINT(1) NOT NULL DEFAULT '0',
	`groups` TINYTEXT NOT NULL,
	PRIMARY KEY (`lsid`),
	UNIQUE KEY `page_id` (`page_id`)
);

CREATE TABLE `phpbb_cms_nav_menu` (
	`menu_item_id` MEDIUMINT(8) unsigned NOT NULL auto_increment,
	`menu_id` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	`menu_parent_id` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	`cat_id` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	`cat_parent_id` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	`menu_default` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	`menu_status` TINYINT(1) NOT NULL DEFAULT '0',
	`menu_order` SMALLINT(5) NOT NULL DEFAULT '0',
	`menu_icon` VARCHAR(255) DEFAULT NULL,
	`menu_name_lang` VARCHAR(150) DEFAULT NULL,
	`menu_name` VARCHAR(150) DEFAULT NULL,
	`menu_desc` TEXT NOT NULL,
	`menu_link` VARCHAR(255) DEFAULT NULL,
	`menu_link_external` TINYINT(1) NOT NULL DEFAULT '0',
	`auth_view` TINYINT(2) NOT NULL DEFAULT '0',
	`auth_view_group` SMALLINT(5) NOT NULL DEFAULT '0',
	PRIMARY KEY (`menu_item_id`),
	KEY `cat_id` (`cat_id`)
);

## Icy Phoenix CMS - END


## AJAX Shoutbox - BEGIN

CREATE TABLE phpbb_ajax_shoutbox (
	shout_id MEDIUMINT(9) UNSIGNED NOT NULL AUTO_INCREMENT,
	user_id MEDIUMINT(8) NOT NULL,
	shouter_name VARCHAR(30) NOT NULL DEFAULT 'guest',
	shout_text TEXT NOT NULL,
	shouter_ip VARCHAR(40) NOT NULL DEFAULT '',
	shout_time INT(11) NOT NULL,
	shout_room VARCHAR(255) NOT NULL DEFAULT '',
	PRIMARY KEY ( shout_id )
);


CREATE TABLE `phpbb_ajax_shoutbox_sessions` (
	`session_id` INT(10) NOT NULL,
	`session_user_id` MEDIUMINT(8) NOT NULL DEFAULT '0',
	`session_username` VARCHAR(25) NOT NULL DEFAULT '',
	`session_ip` VARCHAR(40) NOT NULL DEFAULT '0',
	`session_start` INT(11) NOT NULL DEFAULT '0',
	`session_time` INT(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`session_id`)
);

## AJAX Shoutbox - END

## TICKETS - BEGIN
CREATE TABLE phpbb_tickets_cat (
	ticket_cat_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	ticket_cat_title VARCHAR(255) NOT NULL DEFAULT '',
	ticket_cat_des TEXT NOT NULL,
	ticket_cat_emails TEXT NOT NULL,
	PRIMARY KEY (ticket_cat_id)
);
## TICKETS - END

## AUTH SYSTEM - BEGIN
CREATE TABLE `phpbb_acl_groups` (
	`group_id` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	`forum_id` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	`auth_option_id` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	`auth_role_id` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	`auth_setting` TINYINT(2) NOT NULL DEFAULT '0',
	KEY `group_id` (`group_id`),
	KEY `auth_opt_id` (`auth_option_id`),
	KEY `auth_role_id` (`auth_role_id`)
);

CREATE TABLE `phpbb_acl_options` (
	`auth_option_id` MEDIUMINT(8) unsigned NOT NULL AUTO_INCREMENT,
	`auth_option` VARCHAR(50) COLLATE utf8_bin NOT NULL DEFAULT '',
	`is_global` TINYINT(1) unsigned NOT NULL DEFAULT '0',
	`is_local` TINYINT(1) unsigned NOT NULL DEFAULT '0',
	`founder_only` TINYINT(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`auth_option_id`),
	UNIQUE KEY `auth_option` (`auth_option`)
);

CREATE TABLE `phpbb_acl_roles` (
	`role_id` MEDIUMINT(8) unsigned NOT NULL AUTO_INCREMENT,
	`role_name` VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT '',
	`role_description` text COLLATE utf8_bin NOT NULL,
	`role_type` VARCHAR(10) COLLATE utf8_bin NOT NULL DEFAULT '',
	`role_order` SMALLINT(4) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`role_id`),
	KEY `role_type` (`role_type`),
	KEY `role_order` (`role_order`)
);

CREATE TABLE `phpbb_acl_roles_data` (
	`role_id` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	`auth_option_id` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	`auth_setting` TINYINT(2) NOT NULL DEFAULT '0',
	PRIMARY KEY (`role_id`,`auth_option_id`),
	KEY `ath_op_id` (`auth_option_id`)
);

CREATE TABLE `phpbb_acl_users` (
	`user_id` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	`forum_id` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	`auth_option_id` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	`auth_role_id` MEDIUMINT(8) unsigned NOT NULL DEFAULT '0',
	`auth_setting` TINYINT(2) NOT NULL DEFAULT '0',
	KEY `user_id` (`user_id`),
	KEY `auth_option_id` (`auth_option_id`),
	KEY `auth_role_id` (`auth_role_id`)
);

## AUTH SYSTEM - END

## IMAGES - END

CREATE TABLE `phpbb_images` (
	`pic_id` INT(11) unsigned NOT NULL auto_increment,
	`pic_filename` VARCHAR(255) NOT NULL DEFAULT '',
	`pic_size` INT(15) unsigned NOT NULL DEFAULT '0',
	`pic_title` VARCHAR(255) NOT NULL DEFAULT '',
	`pic_desc` TEXT NOT NULL,
	`pic_user_id` MEDIUMINT(8) NOT NULL DEFAULT '0',
	`pic_user_ip` VARCHAR(40) NOT NULL DEFAULT '0',
	`pic_time` INT(11) unsigned NOT NULL DEFAULT '0',
	`pic_approval` TINYINT(3) NOT NULL DEFAULT '1',
	PRIMARY KEY (`pic_id`),
	KEY `pic_user_id` (`pic_user_id`),
	KEY `pic_time` (`pic_time`)
);

## IMAGES - END
