<!-- IF not S_ACP_CMS -->
<!-- INCLUDE overall_header.tpl -->
<!-- ENDIF -->

<!-- BEGIN nav_blocks_row -->{nav_blocks_row.CMS_BLOCK}<!-- END nav_blocks_row -->
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td width="180" valign="top"><!-- BEGIN left_blocks_row -->{left_blocks_row.CMS_BLOCK}<!-- END left_blocks_row --></td>
	<td valign="top" style="padding-left: 7px; padding-right: 7px;">
		<!-- BEGIN center_blocks_row -->{center_blocks_row.CMS_BLOCK}<!-- END center_blocks_row -->
		<!-- BEGIN xsnews_blocks_row -->{xsnews_blocks_row.OUTPUT}<!-- END xsnews_blocks_row -->
		<!-- BEGIN centerbottom_blocks_row -->{centerbottom_blocks_row.CMS_BLOCK}<!-- END centerbottom_blocks_row -->
	</td>
	<td width="180" valign="top"><!-- BEGIN right_blocks_row -->{right_blocks_row.CMS_BLOCK}<!-- END right_blocks_row --></td>
</tr>
</table>

<!-- IF not S_ACP_CMS -->
<!-- INCLUDE overall_footer.tpl -->
<!-- ENDIF -->