<h1>{L_HEADLINE}</h1>
<p>{L_SUBHEADLINE}</p>

<br />
<!-- BEGIN infobox -->
<div align="center">
<table class="forumline" width="80%" cellspacing="0" cellpadding="0" border="0">
<tr><td align="center" style="background-color:#DBFFCF;"><b>{L_MESSAGE_TEXT}</b></td></tr>
</table>
</div>

<br /><br />
<!-- END infobox -->

<div align="center">
<form action="{S_FORM_ACTION}" method="post">
<table class="forumline" width="80%" cellspacing="0" cellpadding="0" border="0">
<tr><th colspan="2">{L_SELECT_FOOTER}</th></tr>
<!-- BEGIN footer_output -->
<tr> 
	<td class="{footer_output.ROW_CLASS} row-center"><br /><br />{footer_output.IMG_FOOTER}<br /><br /></td>
	<td class="{footer_output.ROW_CLASS} row-center" style="vertical_align:center;"><input type="radio" name="ctracker_footer_layout" value="{footer_output.S_SELECT}"{footer_output.S_SELECTED}></td>
</tr>
<!-- END footer_output -->
<tr><td class="cat" colspan="2" align="center"><input type="submit" name="submit" value="{L_SUBMIT_BUTTON}" class="mainoption"></td></tr>
</table>
</form>
</div>