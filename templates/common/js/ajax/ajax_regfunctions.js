//**************************************************************************
//                            ajax_regfunctions.js
//                            -------------------
//   begin                : Sunday, Jul 17, 2005
//   copyright            : (C) 2005 alcaeus
//   email                : mods@alcaeus.org
//
//   $Id$
//
//**************************************************************************

//**************************************************************************
//
//   This program is free software; you can redistribute it and/or modify
//   it under the terms of the GNU General Public License as published by
//   the Free Software Foundation; either version 2 of the License, or
//   (at your option) any later version.
//
//**************************************************************************

//
// Compare passwords entered. Not really AJAX, but not less helpful ;)
//
function ComparePasswords(new_password, password_confirm)
{
	if (!ajax_core_defined)
	{
		return;
	}

	var pass_match = (new_password == password_confirm) || (new_password == '') || (password_confirm == '');

	var password_compare_error_tbl = getElementById('pass_compare_error_tbl');

	if (password_compare_error_tbl == null)
	{
		if (AJAX_DEBUG_HTML_ERRORS)
		{
			alert('ComparePasswords: some HTML elements could not be found');
		}
		return;
	}

	password_compare_error_tbl.style.display = (pass_match != 1) ? '' : 'none';
}

function AJAXCheckEmail(email)
{
	if (!ajax_core_defined)
	{
		return;
	}

	if (email != '')
	{
		error_handler = 'AJAXFinishCheckEmail';
		var url = 'ajax.' + php_ext;
		var params = 'mode=checkemail&email=' + ajax_escape(email);
		if (S_SID != '')
		{
			params += '&sid='+S_SID;
		}
		if (!loadXMLDoc(url, params, 'GET', 'error_req_change'))
		{
			AJAXFinishCheckEmail(AJAX_OP_COMPLETED, '');
		}
	}
	else
	{
		AJAXFinishCheckEmail(AJAX_OP_COMPLETED, '');
	}
}

function AJAXFinishCheckEmail(result_code, error_msg)
{
	if (!ajax_core_defined)
	{
		return;
	}

	var email_tbl = getElementById('email_error_tbl');
	var email_text = getElementById('email_error_text');

	if ((email_tbl == null) || (email_text == null))
	{
		if (AJAX_DEBUG_HTML_ERRORS)
		{
			alert('AJAXFinishCheckEmail: some HTML elements could not be found');
		}
		return;
	}

	email_tbl.style.display = (result_code != AJAX_OP_COMPLETED) ? '' : 'none';
	setInnerText(email_text, error_msg);
}
