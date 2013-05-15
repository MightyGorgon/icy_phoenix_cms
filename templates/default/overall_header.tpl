{DOCTYPE_HTML}
<head>
<!-- INCLUDE overall_inc_header.tpl -->
{EXTRA_CSS_JS}

</head>
<body>

<div id="global-wrapper">
<span><a name="top" id="top"></a></span>
{TOP_HTML_BLOCK}
<!-- IF GH_BLOCK --><!-- BEGIN gheader_blocks_row -->{gheader_blocks_row.CMS_BLOCK}<!-- END gheader_blocks_row --><!-- ENDIF -->

{PAGE_BEGIN}
<table id="forumtable" cellspacing="0" cellpadding="0">
<!-- IF GT_BLOCK -->
<tr><td width="100%" colspan="3"><!-- BEGIN ghtop_blocks_row -->{ghtop_blocks_row.CMS_BLOCK}<!-- END ghtop_blocks_row --></td></tr>
<!-- ENDIF -->
<tr>
	<td width="100%" colspan="3" valign="top">
		<div id="top_logo">
		<table class="" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
		<td align="left" height="100%" valign="middle">
		<!-- IF GL_BLOCK -->
		<!-- BEGIN ghleft_blocks_row -->{ghleft_blocks_row.OUTPUT}<!-- END ghleft_blocks_row -->
		<!-- ELSE -->
		<div id="logo-img"><a href="{FULL_SITE_PATH}{U_PORTAL}" title="{L_HOME}"><img src="{FULL_SITE_PATH}{SITELOGO}" alt="{L_HOME}" title="{L_HOME}" /></a></div>
		<!-- ENDIF -->
		</td>
		<td align="center" valign="middle"><!-- IF S_HEADER_BANNER --><center><br />{HEADER_BANNER_CODE}</center><!-- ELSE -->&nbsp;<!-- ENDIF --></td>
		<td align="right" valign="middle">
		<!-- <div class="sitedes"><h1>{SITENAME}</h1><h2>{SITE_DESCRIPTION}</h2></div> -->
		<!-- IF GR_BLOCK -->
		<!-- BEGIN ghright_blocks_row -->{ghright_blocks_row.OUTPUT}<!-- END ghright_blocks_row -->
		<!-- ELSE -->
		<!-- IF S_LOGGED_IN -->&nbsp;<!-- ELSE -->&nbsp;<!-- ENDIF -->
		<!-- ENDIF -->
		</td>
		</tr>
		</table>
		</div>
	</td>
</tr>

<!-- IF GB_BLOCK -->
<tr><td width="100%" colspan="3"><!-- BEGIN ghbottom_blocks_row -->{ghbottom_blocks_row.CMS_BLOCK}<!-- END ghbottom_blocks_row --></td></tr>
<!-- ELSE -->
<tr>
	<td width="100%" class="forum-buttons" colspan="3">
		<a href="{FULL_SITE_PATH}{U_PORTAL}">{L_HOME}</a>&nbsp;&nbsp;<img src="{FULL_SITE_PATH}{IMG_MENU_SEP}" alt="" />&nbsp;
		<!-- IF S_LOGGED_IN -->
		<a href="{FULL_SITE_PATH}{U_PROFILE}">{L_PROFILE}</a>&nbsp;&nbsp;<img src="{FULL_SITE_PATH}{IMG_MENU_SEP}" alt="" />&nbsp;
		<!-- ENDIF -->
		<a href="{FULL_SITE_PATH}{U_FAQ}">{L_FAQ}</a>&nbsp;&nbsp;<img src="{FULL_SITE_PATH}{IMG_MENU_SEP}" alt="" />&nbsp;
		<!-- IF not S_LOGGED_IN -->
		<a href="{FULL_SITE_PATH}{U_REGISTER}">{L_REGISTER}</a>&nbsp;&nbsp;<img src="{FULL_SITE_PATH}{IMG_MENU_SEP}" alt="" />&nbsp;
		<!-- ENDIF -->
		<a href="{FULL_SITE_PATH}{U_LOGIN_LOGOUT}">{L_LOGIN_LOGOUT2}</a>
	</td>
</tr>
<!-- ENDIF -->

<!-- IF S_PAGE_NAV --><tr><td width="100%" colspan="3"><div style="margin-left: 7px; margin-right: 7px;"><!-- INCLUDE breadcrumbs_main.tpl --></div></td></tr><!-- ENDIF -->

<!-- INCLUDE overall_inc_body.tpl -->