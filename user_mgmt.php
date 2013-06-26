<?php

 //-------------------------------------------------------------------//
 //
 // AGHOST Paranormal Reporting Database (ParaDB)
 // Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>
 //
 // See the file COPYING for terms of use and redistribution.
 //
 // File: user_mgmt.php
 //       Functions to save/edit/add/manage user information.
 //
 //-------------------------------------------------------------------//


 require( 'include/users.inc.php' );


 // Function print_user_mgmt_list()
 // Print user list with reports/cases stats.
 function print_user_mgmt_list()  {

	global $REPORTSDB, $TITLE, $USER_PERMS;
	$title = "User_Stats_";

	$users = get_user_stats();
	ksort( $users );
	print "<form name=\"main\" action=\"" . $REPORTSDB . "\" method=\"post\">\n";
	print "<table cellpadding=\"4\" cellspacing=\"4\" width=\"100%\">\n";
	print "  <tr>\n";
	print "    <td id=\"label2-nowidth\"> <strong>Name</strong> </td>\n";
	print "    <td id=\"label2-nowidth\"> <strong>Username</strong> </td>\n";
	print "    <td id=\"label2-nowidth\"> <strong>Status</strong> </td>\n";
	print "    <td id=\"label2-nowidth\"> <strong>Permissions</strong> </td>\n";

	global $USER_MANAGEMENT;
	if ( $USER_MANAGEMENT == 1 )  {
		print "    <td align=\"center\" id=\"label2-nowidth\"> <strong>Tasks</strong> </td>\n";
	}

	print "  </tr>\n";
	foreach ( array_keys($users) as $key )  {
		print "  <tr>\n";
		print "    <td>\n";
		print $users[$key]['name'] . "\n";
		print "    </td>\n";
		print "    <td>\n";
		print "<a href=\"javascript:void(0);\" onclick=\"window.open('" . $REPORTSDB . "?task=showstats&user_id=" . $users[$key]['id'] . "','" . $title . "','height=300,width=450,toolbar=no,menubar=no,scrollbars=yes,resizable=yes,location=no,directories=no,status=no')\">" . $key . "</a> (" . $users[$key]['reports'] . '/' . $users[$key]['data'] . '/' . $users[$key]['cases'] . ")\n";
		print "    </td>\n";


		// User Status (Enabled/Disabled)
		print "    <td>\n";
		$selected = '';
		$style = '';
		if ( $users[$key]['block'] == 1 )  {
			$style = 'style="background:red;"';
		}
		print "<select name=\"user_block[" . $users[$key]['id'] . "]\" " . $style . ">\n";
		print "  <option value=\"0\">Enabled</option>\n";

		if ( $users[$key]['block'] == 1 )  {  $selected = 'selected';  }
		print "  <option value=\"1\"" . $selected . ">Disabled</option>\n";
		print "</select>\n";
		print "    </td>\n";


		// Permissions.
		print "    <td>\n";
		$selected = '';
		$style = '';
		if ( $users[$key]['reports_db_perm'] == 4 )  {
			$style = 'style="background:red"';
		}
		elseif ( $users[$key]['reports_db_perm'] == 2 )  {
			$style = 'style="background:lime"';
		}

		print "<select name=\"user_perms[" . $users[$key]['id'] . "]\" " . $style . ">\n";

		if ( $users[$key]['reports_db_perm'] == 4 )  {  $selected = 'selected';  }
		print "  <option value=\"4\" " . $selected . ">Administrator</option>\n";
		$selected = '';

		if ( $users[$key]['reports_db_perm'] == 2 )  {  $selected = 'selected';  }
		print "  <option value=\"2\" ". $selected . ">Team Lead</option>\n";
		$selected = '';

		if ( $users[$key]['reports_db_perm'] == 0 )  {  $selected = 'selected';  }
		print "  <option value=\"0\" " . $selected . ">Registered</option>\n";
		$selected = '';

		print "</select>\n";
		print "    </td>\n";

		if ( $USER_MANAGEMENT == 1 )  {
			global $LOGIN_DB_USER_COL;
			print "    <td align=\"center\">\n";
			print "<a href=\"" . $REPORTSDB . "?task=edit_user&" . $LOGIN_DB_USER_COL . "=" . $key . "\">Edit</a> | ";
			print "<a href=\"" . $REPORTSDB . "?task=del_user&" . $LOGIN_DB_USER_COL . "=" . $key . "&confirm_delete=0\">Delete</a>\n";
			print "    </td>\n";
		}

		print "  </tr>\n";
	}
	print "</table>\n\n";

	print "<br /><br />\n";
	if ( $USER_MANAGEMENT == 1 )  {
		print "<div class=\"normal\" style=\"float:left;padding:4px\"> <a href=\"" . $REPORTSDB . "?task=add_user\">Add User</a> </div>\n";
	}
	print "<div style=\"text-align:center;padding:6px;border-top:1px solid #999999\">\n";
	print "<input type=\"hidden\" name=\"task\" value=\"user_mgmt\" />\n";
	print "<input type=\"hidden\" name=\"save_users\" value=\"1\" />\n";
	print "<button type=\"submit\">" . TASK_SAVE_USERS . "</button>\n";
	print "</div>\n";
	print "</form>\n\n";

	print "<script language=\"JavaScript\">\n";
	print "// <!--\n";
	print "  // _parent doesn't work with the forms on the user_stats popup window, so I'm setting this directly.\n";
	print "  window.name='" . $TITLE . "';\n";
	print "// -->\n";
	print "</script>\n";

	return 0;
 }



 // Function: print_add_user_form()
 function print_add_user_form()  {

	global $REPORTSDB, $USER_PERMS;
	global $USER_FIELDS;
	global $LOGIN_DB_NAME_COL;
	global $LOGIN_DB_USER_COL;
	global $LOGIN_DB_EMAIL_COL;
	global $LOGIN_DB_PASS_COL;
	global $LOGIN_DB_PERM_COL;
	global $LOGIN_DB_BLOCK_COL;

	if ( $_REQUEST['task'] == 'edit_user' )  {
		if ( user_exists($_REQUEST[$LOGIN_DB_USER_COL]) )  {
			set_user_fields( $_REQUEST[$LOGIN_DB_USER_COL] );
		}
		else  {
			set_error( ERR_USER_NOEXIST );
			return ERR_UNDEF;
		}
		print "<h2> Edit User </h2>\n";
	}
	elseif ( $_REQUEST['task'] == 'add_user' )  {
		print "<h2> Add User </h2>\n";
	}

	print "<form name=\"main\" action=\"" . $REPORTSDB . "\" method=\"post\">\n";
	print "<table cellpadding=\"6\" cellspacing=\"4\" width=\"100%\">\n";
	print "  <tr>\n";

	print "    <td id=\"label1-nowidth\" style=\"width:23%\"> Full Name: </td>\n";
	print "    <td id=\"value1\">\n";
	print "      <input type=\"text\" id=\"" . $LOGIN_DB_NAME_COL . "\" name=\"" . $LOGIN_DB_NAME_COL . "\" size=\"30\" maxlength=\"" . $USER_FIELDS[$LOGIN_DB_NAME_COL]['maxlength'] . "\" value=\"" . $USER_FIELDS[$LOGIN_DB_NAME_COL]['value'] . "\">\n";
	print "    </td>\n";

	print "  </tr><tr>\n";

	print "    <td id=\"label1-nowidth\"> Username: </td>\n";
	print "    <td id=\"value1\">\n";
	print "      <input type=\"text\" id=\"" . $LOGIN_DB_USER_COL . "\" name=\"" . $LOGIN_DB_USER_COL . "\" size=\"30\" maxlength=\"" . $USER_FIELDS[$LOGIN_DB_USER_COL]['maxlength'] . "\" value=\"" . $USER_FIELDS[$LOGIN_DB_USER_COL]['value'] . "\">\n";
	print "    </td>\n";

	print "  </tr><tr>\n";

	print "    <td id=\"label1-nowidth\"> Email: </td>\n";
	print "    <td id=\"value1\">\n";
	print "      <input type=\"text\" id=\"" . $LOGIN_DB_EMAIL_COL . "\" name=\"" . $LOGIN_DB_EMAIL_COL . "\" size=\"30\" maxlength=\"" . $USER_FIELDS[$LOGIN_DB_EMAIL_COL]['maxlength'] . "\" value=\"" . $USER_FIELDS[$LOGIN_DB_EMAIL_COL]['value'] . "\">\n";
	print "    </td>\n";

	print "  </tr><tr>\n";

	if ( $_REQUEST['task'] == 'edit_user' )  {
		$USER_FIELDS[$LOGIN_DB_PASS_COL]['value'] = '';
	}
	print "    <td id=\"label1-nowidth\"> Password: </td>\n";
	print "    <td id=\"value1\">\n";
	print "      <input type=\"password\" id=\"" . $LOGIN_DB_PASS_COL . "\" name=\"" . $LOGIN_DB_PASS_COL . "\" size=\"30\" maxlength=\"" . $USER_FIELDS[$LOGIN_DB_PASS_COL]['maxlength'] . "\" value=\"" . $USER_FIELDS[$LOGIN_DB_PASS_COL]['value'] . "\">\n";
	print "    </td>\n";

	print "  </tr><tr>\n";

	print "    <td id=\"label1-nowidth\"> Re-type Password: </td>\n";
	print "    <td id=\"value1\">\n";
	print "      <input type=\"password\" id=\"password2\" name=\"password2\" size=\"30\" maxlength=\"" . $USER_FIELDS[$LOGIN_DB_PASS_COL]['maxlength'] . "\" value=\"" . $USER_FIELDS[$LOGIN_DB_PASS_COL]['value'] . "\">\n";
	print "    </td>\n";

	print "  </tr><tr>\n";


	// Permissions.
	print "    <td id=\"label1-nowidth\"> Permissions </td>\n";
	print "    <td id=\"value1\">\n";
	$selected = '';
	$style = '';
	if ( $USER_FIELDS[$LOGIN_DB_PERM_COL]['value'] == 4 )  {
		$style = 'style="background:red"';
	}
	elseif ( $USER_FIELDS[$LOGIN_DB_PERM_COL]['value'] == 2 )  {
		$style = 'style="background:lime"';
	}

	print "<select name=\"" . $LOGIN_DB_PERM_COL . "\" " . $style . ">\n";
	if ( $USER_FIELDS[$LOGIN_DB_PERM_COL]['value'] == 0 )  {  $selected = 'selected';  }
	print "  <option value=\"0\" " . $selected . ">Registered</option>\n";
	$selected = '';

	if ( $USER_FIELDS[$LOGIN_DB_PERM_COL]['value'] == 2 )  {  $selected = 'selected';  }
	print "  <option value=\"2\" ". $selected . ">Team Lead</option>\n";
	$selected = '';

	if ( $USER_FIELDS[$LOGIN_DB_PERM_COL]['value'] == 4 )  {  $selected = 'selected';  }
	print "  <option value=\"4\" " . $selected . ">Administrator</option>\n";
	$selected = '';
	print "</select>\n";
	print "    </td>\n";


	print "  </tr><tr>\n";


	// User Status (Enabled/Disabled).
	print "    <td id=\"label1-nowidth\"> Status: </td>\n";
	print "    <td id=\"value1\">\n";
	$selected = '';
	$style = '';
	if ( $USER_FIELDS[$LOGIN_DB_BLOCK_COL]['value'] == 1 )  {
		$style = 'style="background:red"';
	}
	print "<select name=\"" . $LOGIN_DB_BLOCK_COL . "\" " . $style . ">\n";
	print "  <option value=\"0\">Enabled</option>\n";

	if ( $USER_FIELDS[$LOGIN_DB_BLOCK_COL]['value'] == 1 )  {  $selected = 'selected';  }
	print "  <option value=\"1\"" . $selected . ">Disabled</option>\n";
	print "</select>\n";
	print "    </td>\n";


	print "  </tr>\n";
	print "</table>\n\n";

	print "<br /><br />\n";
	print "<div style=\"text-align:center;padding:6px;border-top:1px solid #999999\">\n";
	print "<input type=\"hidden\" name=\"task\" value=\"" . $_REQUEST['task'] . "\" />\n";
	print "<input type=\"hidden\" name=\"user_save\" value=\"1\" />\n";
	if ( $_REQUEST['task'] == 'edit_user' )  {
		print "<input type=\"hidden\" name=\"cur_username\" value=\"" . $USER_FIELDS[$LOGIN_DB_USER_COL]['value'] . "\" />\n";
	}
	print "<button type=\"button\" onClick=\"Javascript:check_password();\">" . TASK_ADD_USER_SAVE . "</button>\n &nbsp;&nbsp; ";
	print "<button type=\"submit\" onClick=\"Javascript:document.main.task.value=user_mgmt;\">Cancel</button>\n";
	print "</div>\n";
	print "</form>\n\n";

	print "<script language=\"Javascript\">\n";
	print "<!--\n";
	print "	function check_password() {\n";
	print "		if ( document.getElementById('" . $LOGIN_DB_PASS_COL . "').value != document.getElementById('password2').value )  {\n";
	print "			alert (\"Error: Passwords do not match.\");\n";
	print "		}\n";

	if ( $_REQUEST['task'] != 'edit_user' )  {
		print "		else if ( document.getElementById('" . $LOGIN_DB_PASS_COL . "').value == '' )  {\n";
		print "			alert (\"Error: Empty passwords are not permitted.\");\n";
		print "		}\n";
	}

	print "		else  {  document.main.submit();  }\n";
	print "	}\n";
	print "//-->\n";
	print "</script>\n";

	return 0;
 }



 // Function: print_del_user_form()
 // Print the user data for confirming the deletion.
 function print_del_user_form()  {

	global $REPORTSDB, $USER_PERMS;
	global $USER_FIELDS;
	global $LOGIN_DB_NAME_COL;
	global $LOGIN_DB_USER_COL;
	global $LOGIN_DB_EMAIL_COL;
	global $LOGIN_DB_PASS_COL;
	global $LOGIN_DB_PERM_COL;
	global $LOGIN_DB_BLOCK_COL;

	if ( user_exists($_REQUEST[$LOGIN_DB_USER_COL]) )  {
		set_user_fields( $_REQUEST[$LOGIN_DB_USER_COL] );
	}
	else  {
		set_error( ERR_USER_NOEXIST );
		return ERR_UNDEF;
	}

	print "<br /><br />\n";
	print "<table cellpadding=\"6\" cellspacing=\"4\" width=\"50%\" class=\"readonly\">\n";
	print "  <tr>\n";

	print "    <td id=\"label1\" style=\"width:23%\"> Full Name: </td>\n";
	print "    <td id=\"value1\"> " . $USER_FIELDS[$LOGIN_DB_NAME_COL]['value'] . " </td>\n";

	print "  </tr><tr>\n";

	print "    <td id=\"label1\" style=\"width:23%\"> Username: </td>\n";
	print "    <td id=\"value1\"> " . $USER_FIELDS[$LOGIN_DB_USER_COL]['value'] . " </td>\n";

	print "  </tr><tr>\n";

	print "    <td id=\"label1\" style=\"width:23%\"> Email: </td>\n";
	print "    <td id=\"value1\"> " . $USER_FIELDS[$LOGIN_DB_EMAIL_COL]['value'] . " </td>\n";

	print "  </tr><tr>\n";

	print "    <td id=\"label1\" style=\"width:23%\"> Permissions: </td>\n";
	print "    <td id=\"value1\"> ";
	if ( $USER_FIELDS[$LOGIN_DB_PERM_COL]['value'] == 0 )  {
		print "Registered";
	}
	elseif ( $USER_FIELDS[$LOGIN_DB_PERM_COL]['value'] == 2 )  {
		print "Team Lead";
	}
	elseif ( $USER_FIELDS[$LOGIN_DB_PERM_COL]['value'] == 4 )  {
		print "Administrator";
	}
	print " </td>\n";

	print "  </tr><tr>\n";

	print "    <td id=\"label1\" style=\"width:23%\"> Status: </td>\n";
	print "    <td id=\"value1\"> ";
	if ( $USER_FIELDS[$LOGIN_DB_BLOCK_COL]['value'] == 0 )  {
		print "Enabled";
	}
	elseif ( $USER_FIELDS[$LOGIN_DB_BLOCK_COL]['value'] == 1 )  {
		print "Disabled";
	}
	print " </td>\n";
	print "  </tr>\n";
	print "</table>\n";
	print "<br /><br />\n";

	return 0;
 }



 // Function: user_mgmt_save()
 // Save the user list, permissions only right now.
 function user_mgmt_save()  {

	global $LOGIN_DB_HOST;
	global $LOGIN_DB_USER;
	global $LOGIN_DB_PASS;
	global $LOGIN_DB_NAME;
	global $LOGIN_DB_TABLE;
	global $LOGIN_DB_ID_COL;
	global $LOGIN_DB_USER_COL;
	global $LOGIN_DB_PERM_COL;
	global $LOGIN_DB_BLOCK_COL;
	global $TASK_REQ_PERMS;

	// Only administrators may edit users.
	if ( $_SESSION['user_perm'] < $TASK_REQ_PERMS['user_mgmt'] )  {
		set_error( ERR_USER_MGMT_DENIED );
		do_log( "Error: user_mgmt_save(): " . ERR_USER_MGMT_DENIED . " UserID=" . $_SESSION['user_id'] . "." );
		return ERR_PERM;
	}

	$USER_DATA = array();
	$login_db_connection = mysql_init_connect( $LOGIN_DB_HOST, $LOGIN_DB_NAME, $LOGIN_DB_USER, $LOGIN_DB_PASS );
	foreach ( array_keys($_REQUEST['user_perms']) as $key )  {
		$USER_DATA[$LOGIN_DB_PERM_COL]['value'] = $_REQUEST['user_perms'][$key];
		$USER_DATA[$LOGIN_DB_BLOCK_COL]['value'] = $_REQUEST['user_block'][$key];
		if ( mysql_db_update($LOGIN_DB_TABLE, $USER_DATA, $LOGIN_DB_ID_COL . '=' . $key, $login_db_connection) != 0 )  {
			set_error( ERR_USER_UPDATE . ": id=" . $key . "<br />", 'a' );
			do_log( "Error: user_mgmt_save(): " . ERR_USER_UPDATE . ": id=" . $key );
			return ERR_UNDEF;
		}
	}
//	mysql_close( $login_db_connection );

	return 0;
 }



 // Function: update_user()
 // Add a new user or update user information in the database.
 function update_user()  {

	// Only administrators may edit users.
	global $TASK_REQ_PERMS;
	if ( $_SESSION['user_perm'] < $TASK_REQ_PERMS['user_mgmt'] )  {
		set_error( ERR_USER_MGMT_DENIED );
		do_log( "Error: update_user(): " . ERR_USER_MGMT_DENIED . " UserID=" . $_SESSION['user_id'] . "." );
		return ERR_PERM;
	}

	global $LOGIN_DB_HOST;
	global $LOGIN_DB_USER;
	global $LOGIN_DB_PASS;
	global $LOGIN_DB_NAME;
	global $LOGIN_DB_TABLE;
	global $LOGIN_DB_NAME_COL;
	global $LOGIN_DB_USER_COL;
	global $LOGIN_DB_EMAIL_COL;
	global $LOGIN_DB_PASS_COL;
	global $LOGIN_DB_PERM_COL;
	global $LOGIN_DB_BLOCK_COL;
	global $USER_FIELDS;

	foreach ( array_keys($USER_FIELDS) as $key )  {
		if ( !isset($_REQUEST[$key]) && $USER_FIELDS[$key]['required'] == 'yes' )  {
			set_error( USER_ADD_FORM_INCOMPLETE );
			return ERR_UNDEF;
		}
		$_REQUEST[$key] = clean_field( $_REQUEST[$key] );
		if ( $USER_FIELDS[$key]['maxlength'] != NULL && $USER_FIELDS[$key]['maxlength'] != 0 )  {
			$_REQUEST[$key] = check_length( $_REQUEST[$key], $USER_FIELDS[$key]['maxlength'] );
		}
		$USER_FIELDS[$key]['value'] = $_REQUEST[$key];
		if ( $key == $LOGIN_DB_PASS_COL )  {
			if ( !empty($_REQUEST[$key]) )  {
				$USER_FIELDS[$key]['value'] = md5( $_REQUEST[$key] );
			}
		}
	}
	if ( !is_numeric($USER_FIELDS[$LOGIN_DB_BLOCK_COL]['value']) || $USER_FIELDS[$LOGIN_DB_BLOCK_COL]['value'] < 0 )  {
		$USER_FIELDS[$LOGIN_DB_BLOCK_COL]['value'] = 0;
	}
	if ( !is_numeric($USER_FIELDS[$LOGIN_DB_PERM_COL]['value']) || $USER_FIELDS[$LOGIN_DB_PERM_COL]['value'] < 0 )  {
		$USER_FIELDS[$LOGIN_DB_PERM_COL]['value'] = 0;
	}
	if ( empty($USER_FIELDS[$LOGIN_DB_PASS_COL]['value']) )  {
		unset( $USER_FIELDS[$LOGIN_DB_PASS_COL] );
	}

	$login_db_connection = mysql_init_connect( $LOGIN_DB_HOST, $LOGIN_DB_NAME, $LOGIN_DB_USER, $LOGIN_DB_PASS );
	if ( $_REQUEST['task'] == 'add_user' )  {
		if ( user_exists($USER_FIELDS[$LOGIN_DB_USER_COL]['value']) )  {
			set_error( ERR_USER_EXISTS );
			$USER_FIELDS[$LOGIN_DB_PASS_COL]['value'] = $_REQUEST[$LOGIN_DB_PASS_COL];
			return ERR_UNDEF;
		}
		else  {
			if ( mysql_db_insert($LOGIN_DB_TABLE, $USER_FIELDS, $login_db_connection) != 0 )  {
				set_error( ERR_USER_CREATE );
				do_log( "Error: update_user(): " . ERR_USER_CREATE . ": name=" . $USER_FIELDS[$LOGIN_DB_USER_COL]['value'] );
				return ERR_UNDEF;
			}
		}
	}
	elseif ( $_REQUEST['task'] == 'edit_user' )  {
		if ( $_REQUEST['cur_username'] != $USER_FIELDS[$LOGIN_DB_USER_COL]['value'] )  {
			if ( user_exists($USER_FIELDS[$LOGIN_DB_USER_COL]['value']) )  {
				set_error( ERR_USER_EXISTS );
				return ERR_UNDEF;
			}
		}
		if ( !isset($_REQUEST['cur_username']) )  {
			$_REQUEST['cur_username'] = $USER_FIELDS[$LOGIN_DB_USER_COL]['value'];
		}
		if ( mysql_db_update($LOGIN_DB_TABLE, $USER_FIELDS, $LOGIN_DB_USER_COL . '=' . $_REQUEST['cur_username'], $login_db_connection) != 0 )  {
			set_error( ERR_USER_UPDATE );
			do_log( "Error: update_user(): " . ERR_USER_UPDATE . ": name=" . $USER_FIELDS[$LOGIN_DB_USER_COL]['value'] );
			return ERR_UNDEF;
		}
	}
	else  {
		return ERR_UNDEF;
	}
//	mysql_close( $login_db_connection );

	return 0;
 }



 // Function: set_user_fields()
 // Pulls user information from the database and put it in $USER_FIELDS.
 function set_user_fields( $username="" )  {

	if ( empty($username) )  {
		return ERR_UNDEF;
	}

	global $LOGIN_DB_HOST;
	global $LOGIN_DB_USER;
	global $LOGIN_DB_PASS;
	global $LOGIN_DB_NAME;
	global $LOGIN_DB_TABLE;
	global $LOGIN_DB_NAME_COL;
	global $LOGIN_DB_USER_COL;
	global $LOGIN_DB_EMAIL_COL;
	global $LOGIN_DB_PASS_COL;
	global $LOGIN_DB_PERM_COL;
	global $LOGIN_DB_BLOCK_COL;
	global $USER_FIELDS;

	$login_db_connection = mysql_init_connect( $LOGIN_DB_HOST, $LOGIN_DB_NAME, $LOGIN_DB_USER, $LOGIN_DB_PASS );
	$user = mysql_get_rows( $LOGIN_DB_TABLE, '*', $LOGIN_DB_USER_COL . '=' . $username, NULL, NULL, $login_db_connection );

	$USER_FIELDS[$LOGIN_DB_NAME_COL]['value'] = mysql_result( $user, 0, $LOGIN_DB_NAME_COL );
	$USER_FIELDS[$LOGIN_DB_USER_COL]['value'] = mysql_result( $user, 0, $LOGIN_DB_USER_COL );
	$USER_FIELDS[$LOGIN_DB_EMAIL_COL]['value'] = mysql_result( $user, 0, $LOGIN_DB_EMAIL_COL );
	$USER_FIELDS[$LOGIN_DB_PASS_COL]['value'] = mysql_result( $user, 0, $LOGIN_DB_PASS_COL );
	$USER_FIELDS[$LOGIN_DB_PERM_COL]['value'] = mysql_result( $user, 0, $LOGIN_DB_PERM_COL );
	$USER_FIELDS[$LOGIN_DB_BLOCK_COL]['value'] = mysql_result( $user, 0, $LOGIN_DB_BLOCK_COL );

	// mysql_close( $login_db_connection );

	return 0;
 }



 // Function: delete_user()
 // Deletes a user.
 function delete_user( $username="" )  {

	if ( empty($username) )  {
		return ERR_UNDEF;
	}

	// Only administrators may edit users.
	global $TASK_REQ_PERMS;
	if ( $_SESSION['user_perm'] < $TASK_REQ_PERMS['user_mgmt'] )  {
		set_error( ERR_USER_MGMT_DENIED );
		do_log( "Error: update_user(): " . ERR_USER_MGMT_DENIED . " UserID=" . $_SESSION['user_id'] . "." );
		return ERR_PERM;
	}
	if ( !isset($_REQUEST['confirm_delete']) || $_REQUEST['confirm_delete'] == 0 )  {
		return 0;
	}
	if ( !user_exists($username) )  {
		set_error( ERR_USER_NOEXIST );
		return ERR_UNDEF;
	}
	set_user_fields( $username );

	global $LOGIN_DB_HOST;
	global $LOGIN_DB_USER;
	global $LOGIN_DB_PASS;
	global $LOGIN_DB_NAME;
	global $LOGIN_DB_TABLE;
	global $LOGIN_DB_NAME_COL;
	global $LOGIN_DB_USER_COL;
	global $LOGIN_DB_EMAIL_COL;
	global $LOGIN_DB_PASS_COL;
	global $LOGIN_DB_PERM_COL;
	global $LOGIN_DB_BLOCK_COL;
	global $USER_FIELDS;

	$login_db_connection = mysql_init_connect( $LOGIN_DB_HOST, $LOGIN_DB_NAME, $LOGIN_DB_USER, $LOGIN_DB_PASS );
	if ( mysql_db_delete( $LOGIN_DB_TABLE, $LOGIN_DB_USER_COL . "=" . $username, $login_db_connection ) == 0 )  {
		set_error( 'Successfully deleted user ' . $USER_FIELDS[$LOGIN_DB_NAME_COL]['value'] . " (" . $USER_FIELDS[$LOGIN_DB_USER_COL]['value'] . ")." );
		do_log( 'INFO: delete_user(): Successful deletion of user  ' . $username . ' by user ' . $_SESSION['username'] );
	}
	else  {
		return ERR_UNDEF;
	}
//	mysql_close( $login_db_connection );

	return 0;
}




?>
