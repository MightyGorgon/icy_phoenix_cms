<!-- INCLUDE overall_header.tpl -->

<script type="text/javascript">
// <![CDATA[

function checkForm(form) {

	errors = false;

	if (form.message.value.length < 2)
	{
		errors = "{L_EMPTY_MESSAGE_EMAIL}";
	}
	else if (form.subject.value.length < 2)
	{
		errors = "{L_EMPTY_SUBJECT_EMAIL}";
	}
	else if (form.sender.value.length < 2)
	{
		errors = "{L_EMPTY_SENDER_EMAIL}";
	}

	if (errors)
	{
		alert(errors);
		return false;
	}
}
// ]]>
</script>

<form action="{S_POST_ACTION}" method="post" name="post" onsubmit="return checkForm(this)">

<!-- BEGIN delete_account -->
{IMG_TBL}<table class="forumline" width="100%" cellspacing="0" cellpadding="0">
<tr><td class="row1"><span class="topic_glo"><span class="genmed">{L_DELETE_ACCOUNT_EXPLAIN}</span></span></td></tr>
</table>{IMG_TBR}
<!-- END delete_account -->

{ERROR_BOX}

{IMG_THL}{IMG_THC}<span class="forumlink">{L_SEND_EMAIL_MSG}</span>{IMG_THR}<table class="forumlinenb" width="100%" cellspacing="0" cellpadding="0">
<tr>
	<td class="row1" width="22%"><span class="gen"><b>{L_SENDER}</b></span></td>
	<td class="row2" width="78%"><span class="gen"><input type="text" name="sender" size="45" maxlength="100" style="width:450px" tabindex="2" class="post" value="{SENDER}" /></span></td>
</tr>
<tr>
	<td class="row1"><span class="gen"><b>{L_SUBJECT}</b></span></td>
	<td class="row2"><span class="gen"><input type="text" class="post" name="subject" size="45" maxlength="120" style="width:450px" tabindex="3" value="{SUBJECT}" /></span></td>
</tr>
<!-- IF S_TICKETS and SELECT_TICKET -->
<tr>
	<td class="row1"><span class="gen"><b>{L_TICKET_CAT}</b></span></td>
	<td class="row2">&nbsp;{SELECT_TICKET}</td>
</tr>
<!-- ENDIF -->
<tr>
	<td class="row1" valign="top"><span class="gen"><b>{L_MESSAGE_BODY}</b></span><br /><span class="gensmall">{L_MESSAGE_BODY_DESC}</span></td>
	<td class="row2"><span class="gen"><textarea name="message" rows="25" cols="40" style="width: 450px;" tabindex="4">{MESSAGE}</textarea></span></td>
</tr>
<tr>
	<td class="row1" valign="top"><span class="gen"><b>{L_OPTIONS}</b></span></td>
	<td class="row2"><input type="checkbox" name="cc_email" value="1" checked="checked" />&nbsp;<span class="gen">{L_CC_EMAIL}</span></td>
</tr>
<!-- BEGIN switch_confirm -->
<tr><td class="row1 row-center" colspan="2"><span class="gensmall">{L_CONFIRM_CODE_IMPAIRED}</span><br /><br />{CONFIRM_IMG}<br /><br /></td></tr>
<tr>
	<td class="row1">
		<span class="gen">{L_CONFIRM_CODE}:&nbsp;*</span><br />
		<span class="gensmall">{L_CONFIRM_CODE_EXPLAIN}</span>
	</td>
	<td class="row2"><input type="text" class="post" style="width: 200px" name="confirm_code" size="6" maxlength="6" value="" /></td>
</tr>
<!-- END switch_confirm -->
<tr><td class="cat" colspan="2">{S_HIDDEN_FIELDS}<input type="submit" tabindex="6" name="submit" class="mainoption" value="{L_SEND_EMAIL}" /></td></tr>
</table>{IMG_TFL}{IMG_TFC}{IMG_TFR}
</form>

<!-- INCLUDE overall_footer.tpl -->