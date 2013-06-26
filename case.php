<?php

 //-------------------------------------------------------------------//
 //
 // AGHOST Paranormal Reporting Database (ParaDB)
 // Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>
 //
 // See the file COPYING for terms of use and redistribution.
 //
 // File: case.php
 //	  All functions related to case management, saving, editing
 //	  deleting, etc.
 //
 //-------------------------------------------------------------------//


 include( 'cases_print.php' );
 include( 'timezones.php' );

 // Function: view_case( [case_id] )
 // Print out a case.
 function view_case( $case_id="" )  {

	if ( empty($case_id) )  {
		return ERR_UNDEF;
	}
	if ( check_case_id($case_id) != 0 )  {
		// Check to see if there are reports associated with this case.
		global $db_connection;
		$result = mysql_get_rows( 'reportsdb_reports', 'case_id', 'case_id=' . $case_id, NULL, NULL, $db_connection );
		if ( mysql_num_rows($result) > 0 )  {
			set_error( "Error: Case ID <strong>" . $case_id ."</strong> does not exist." . "<br />" . ERR_ORPHANED_REPORTS );
		}
		return ERR_UNDEF;
	}

	// Open existing case.
	set_case_fields( $case_id );
	if ( case_check_perms('r', $case_id) != 0 )  {
		global $CASE_VALID_FIELDS;
		foreach ( array_keys($CASE_VALID_FIELDS) as $key )  {
			if ( $_SESSION['user_perm'] < $CASE_VALID_FIELDS[$key]['minperm'] )  {
				$CASE_VALID_FIELDS[$key]['value'] = "";
			}
		}
	}
	set_title( ' (' . $case_id . ')', 'a' );
	print_case_view();

	return 0;
 }



 // Function: edit_case( [case_id], [opts] )
 // Edit or create a case.  check_case_id() should already have been called.
 // $opts values:
 //	NOPERM = Do not call case_check_perms() to check permissions.
 //	NOSET = Do not call function set_case_fields(). Use this option if
 //		you do not want $CASE_VALID_FIELDS altered.
 function edit_case( $case_id="", $opts="" )  {

	$opts = explode( '|', $opts );
	if ( $case_id != 'new' && !empty($case_id) )  {
		if ( !in_array('NOPERM', $opts) )  {
			if ( case_check_perms('w', $case_id) != 0 )  {
				return ERR_PERM;
			}
		}
		if ( !in_array('NOSET', $opts) )  {
			set_case_fields( $case_id );
		}
	}
	set_title( ' (' . $case_id . ')', 'a' );
	print_case_form();

	return 0;
 }



 // Function: save_case( [case_id] )
 // Save case information.
 function save_case( $case_id="" )  {

	global $CASE_VALID_FIELDS, $db_connection;

	// Fix up the 'investigators_role' and 'data_submitted' fields.
	add_investigators();

	// Clean the form fields and input data to $CASE_VALID_FIELDS.
	clean_case_fields();

	// Check to see if all the required fields have been set.
	foreach ( array_keys($CASE_VALID_FIELDS) as $key )  {
		if ( empty($CASE_VALID_FIELDS[$key]['value']) )  {
			if ( $CASE_VALID_FIELDS[$key]['required'] == 'yes' )  {
				set_error( CASE_FORM_ERROR );
				edit_case( $case_id, 'NOSET' );
				return ERR_UNDEF_EXIT;
			}
		}
	}
	if ( !is_numeric($CASE_VALID_FIELDS['case_id']['value']) )  {
		$CASE_VALID_FIELDS['case_id']['value'] = strtoupper( $CASE_VALID_FIELDS['case_id']['value'] );
		if ( check_case_id($case_id) != 0 )  {
			// Check the format of case_id.
			if ( check_caseid_format($case_id, $CASE_VALID_FIELDS['investigation_type']['value']) != 0 )  {
				set_error( CASE_ERROR_CASEID_FORMAT . "<br />\n");
				edit_case( $case_id, 'NOSET' );
				return ERR_UNDEF_EXIT;
			}
		}
	}

	// Check to make sure investigators on the case have not exceeded their
	// unfinished reports threshold.  Don't error if we're only sending reporting reminders.
	if ( email_reminder($case_id) <= 0 )  {
		if ( !isset($_REQUEST['confirm_save']) || $_REQUEST['confirm_save'] != 1 )  {
			$overdue = check_investigator_stats( $CASE_VALID_FIELDS['investigators']['value'] );
			if ( !empty($overdue) )  {
				$error = "";
				foreach ( array_keys($overdue) as $username )  {
					if ( !empty($error) )  {
						$error .= "<br />";
					}
					$error .= "User <strong>" . $overdue[$username]['name'] . "</strong> is listed on <strong>" . $overdue[$username]['cases'] . "</strong> cases and has filed <strong>" . $overdue[$username]['reports'] . "</strong> reports. (" . $overdue[$username]['overdue_cases'] . " unfinished).\n";
				}
				set_error( CASE_OVERDUE_INVESTIGATORS . "<br />\n" . $error );
				$_REQUEST['confirm_save'] = 0;
				edit_case( $case_id, 'NOSET' );
				return ERR_UNDEF_EXIT;
			}
		}
	}

	// Check to see if case was reassigned.
	$owner_id = case_getowner( $case_id );
	if ( $owner_id != ERR_UNDEF && $owner_id != $_REQUEST['owner_id'] )  {
		reassign_case( $case_id, $owner_id, $_REQUEST['owner_id'] );
	}

	if ( check_case_id($case_id) == 0 )  {  // Existing case, update record.
		if ( case_check_perms('w', $case_id) != 0 )  {
			set_error( CASE_ERROR_CASE_EXISTS . "<br />\n", 'o' );
			edit_case( $case_id, 'NOSET|NOPERM' );
			return ERR_PERM;
		}
		$CASE_VALID_FIELDS['notes']['value'] .= "\n[" . date("D M j G:i:s") . "]: Case saved by " . get_username($_SESSION['user_id']) . ".";

		// Check if we're closing the case, so we can call close_case() directly,
		// which will also unpublish the reports associated with the case.
		if ( $CASE_VALID_FIELDS['case_open']['value'] == 'closed' )  {
			if ( case_isopen($case_id) == 0 )  {
				// Case is currently open, but is being closed.
				// Call close_case() to unpublish reports.
				close_case( $case_id );
			}
		}

		if ( mysql_db_update('reportsdb_cases', $CASE_VALID_FIELDS, 'case_id=' . $case_id, $db_connection) != 0 )  {
			return ERR_UNDEF;
		}
	}
	else  {  // New case_id.
		$CASE_VALID_FIELDS['notes']['value'] .= "\n[" . date("D M j G:i:s") . "]: Case created by " . get_username($_SESSION['user_id']) . ".";
		if ( mysql_db_insert('reportsdb_cases', $CASE_VALID_FIELDS, $db_connection) != 0 )  {
			return ERR_UNDEF;
		}
	}

	return 0;
 }



 // Function: delete_case( [case_id], [owner_id] )
 // Delete a case.
 function delete_case( $case_id="" )  {

	global $db_connection;

	if ( empty($case_id) )  {
		return ERR_UNDEF;
	}
	if ( check_case_id($case_id) != 0 )  {
		return ERR_UNDEF;
	}
	if ( case_check_perms( 'd', $case_id ) != 0 )  {
		return ERR_PERM;
	}
	if ( !isset($_REQUEST['confirm_delete']) || $_REQUEST['confirm_delete'] == 0 )  {
		view_case( $case_id );
		return 0;
	}

	if ( mysql_db_delete( 'reportsdb_cases', 'case_id=' . $case_id, $db_connection ) == 0 )  {
		do_log( 'INFO: delete_case(): Successful deletion of case ID ' . $case_id . ' by user ' . $_SESSION['username'] );
		return 0;
	}

	return ERR_UNDEF;
 }



 // Function: clean_case_fields()
 // Clean POST fields related to the $CASE_VALID_FIELDS variable and check lengths.
 // We're also setting the vars in $CASE_VALID_FIELDS now after the checks.  It should be more efficient.
 function clean_case_fields()  {

	global $CASE_VALID_FIELDS;

	foreach ( array_keys($CASE_VALID_FIELDS) as $key )  {
		if ( $key == 'investigators' )  {
			continue;
		}
		if ( $key == 'date' || $key == 'recap_date')  {
			$_REQUEST[$key] = $_REQUEST[$key.'_year'] . '-' . $_REQUEST[$key.'_month'] . '-' . $_REQUEST[$key.'_day'];
			if ( $_REQUEST[$key] == '--' )  {
				$_REQUEST[$key] = "";
			}
			else  {
				if ( !check_date($_REQUEST[$key]) )  {
					set_error( ERR_DATE );
					$_REQUEST[$key] = "";
				}
			}
		}
		elseif ( $key == 'expiration_date' )  {
			$_REQUEST[$key] = $_REQUEST['expiration_year'] . '-' . $_REQUEST['expiration_month'] . '-' . $_REQUEST['expiration_day'];
			if ( $_REQUEST[$key] == '--' )  {
				$_REQUEST[$key] = "";
			}
			else  {
				if ( !check_date($_REQUEST[$key]) )  {
					set_error( ERR_DATE );
					$_REQUEST[$key] = "";
				}
			}
		}

		if ( isset($_REQUEST[$key]) && !empty($_REQUEST[$key]) )  {
			$_REQUEST[$key] = clean_field( $_REQUEST[$key] ); // Clean field first, as this may
									  // increase the size of the value.
			if ( !empty($CASE_VALID_FIELDS[$key]['maxlength']) && $CASE_VALID_FIELDS[$key]['maxlength'] != 0 )  {
				$_REQUEST[$key] = check_length( $_REQUEST[$key], $CASE_VALID_FIELDS[$key]['maxlength'] );
			}
			$CASE_VALID_FIELDS[$key]['value'] = $_REQUEST[$key];
		}
	}

	// Set owner_id in hash.
	if ( empty($CASE_VALID_FIELDS['owner_id']['value']) )  {
		if ( check_case_id($CASE_VALID_FIELDS['case_id']['value']) == 0 )  {  // Case already exists in database.
			$CASE_VALID_FIELDS['owner_id']['value'] = case_getowner( $CASE_VALID_FIELDS['case_id']['value'] );
		}
		else  {
			$CASE_VALID_FIELDS['owner_id']['value'] = $_SESSION['user_id'];
		}
	}

	return 0;
 }



 // Function: set_case_fields( [case_id] )
 // Pull data from the reportsdb_cases table and populate $CASE_VALID_FIELDS.
 function set_case_fields( $case_id="" )  {

	global $CASE_VALID_FIELDS, $db_connection;

	if ( $case_id == "" )  {
		return ERR_UNDEF;
	}

	$data = mysql_get_rows( 'reportsdb_cases', '*', 'case_id=' . $case_id, NULL, NULL, $db_connection );
	foreach ( array_keys($CASE_VALID_FIELDS) as $key )  {
		$CASE_VALID_FIELDS[$key]['value'] = mysql_result( $data, 0, $key );
		if ( $CASE_VALID_FIELDS[$key]['value'] === FALSE )  {
			$CASE_VALID_FIELDS[$key]['value'] = "";
		}
		$CASE_VALID_FIELDS[$key]['value'] = stripslashes( $CASE_VALID_FIELDS[$key]['value'] );
	}

	return 0;
 }



 // Function: add_investigators()
 // Fix up the 'investigators_role' and 'data_submitted' fields.
 function add_investigators()  {

	global $CASE_VALID_FIELDS;
	global $NUM_INVESTIGATORS;

	$CASE_VALID_FIELDS['investigators']['value'] = "";
	$CASE_VALID_FIELDS['investigators_role']['value'] = "";
	$CASE_VALID_FIELDS['data_submitted']['value'] = "";
	for ( $i=1; $i<$NUM_INVESTIGATORS+1; $i++ )  {
		if ( !empty($_REQUEST['investigator_userid_'.$i]) )  {

			// Investigators column.
			if ( !empty($CASE_VALID_FIELDS['investigators']['value']) )  {
				$CASE_VALID_FIELDS['investigators']['value'] .= ',';
			}
			$CASE_VALID_FIELDS['investigators']['value'] .= $_REQUEST['investigator_userid_'.$i];

			// Investigators Role.
			if ( !empty($CASE_VALID_FIELDS['investigators_role']['value']) )  {
				$CASE_VALID_FIELDS['investigators_role']['value'] .= ',';
			}
			$CASE_VALID_FIELDS['investigators_role']['value'] .= $_REQUEST['investigator_userid_'.$i] . '|';
			$CASE_VALID_FIELDS['investigators_role']['value'] .= $_REQUEST['investigator_pos_'.$i];

			// Data Submitted.
			if ( !empty($CASE_VALID_FIELDS['data_submitted']['value']) )  {
				$CASE_VALID_FIELDS['data_submitted']['value'] .= ',';
			}
			$CASE_VALID_FIELDS['data_submitted']['value'] .= $_REQUEST['investigator_userid_'.$i] . '|';
			if ( !isset($_REQUEST['investigator_data_'.$i]) || empty($_REQUEST['investigator_data_'.$i]) )  {
				$_REQUEST['investigator_data_'.$i] = "";
			}
			$CASE_VALID_FIELDS['data_submitted']['value'] .= $_REQUEST['investigator_data_'.$i];
		}
	}

	return 0;
 }


 // Function: parse_investigators()
 // Parses the 'investigators_role' and 'data_submitted' fields from the database.
 // Returns an array with the investigator's info.
 // $investigators = array ( 'userid' => array ( 'role'   => "",
 //					   	 'data'   => nn ) );
 function parse_investigators()  {

	global $CASE_VALID_FIELDS;
	global $NUM_INVESTIGATORS;

	$investigators = array();
	$roles = explode( ',', $CASE_VALID_FIELDS['investigators_role']['value'] );
	$data = explode( ',', $CASE_VALID_FIELDS['data_submitted']['value'] );

	foreach ( $roles as $key )  {
		$tmp = explode( '|', $key );
		if ( !isset($tmp[1]) )  {
			$tmp[1] = "";
		}
		$investigators[$tmp[0]]['role'] = $tmp[1];
	}
	foreach ( $data as $key )  {
		$tmp = explode( '|', $key );
		if ( !isset($tmp[1]) || empty($tmp[1]) )  {
			$investigators[$tmp[0]]['data'] = "";
		}
		else  {
			$investigators[$tmp[0]]['data'] = $tmp[1];
		}
	}

	return $investigators;
 }



 // Function: case_check_perms( [r|w|d], [case_id] )
 // Checks to see if user has desired permission, r=read, w=write (edit), d=delete.
 function case_check_perms( $perm='r', $case_id="" )  {

	global $USER_PERMS, $db_connection;

	if ( $perm != 'r' && $perm != 'w' && $perm != 'd' )  {
		return ERR_UNDEF;
	}
	if ( empty($case_id) )  {
		return ERR_UNDEF;
	}

	if ( $perm == 'r')  {
		if ( case_isopen($case_id) != 0 )  {
			// Anyone may view if the case is 'closed'.
			return 0;
		}
		elseif ( $_SESSION['user_perm'] == $USER_PERMS['administrator'] )  {
			// Administrator can always view.
			return 0;
		}
		elseif ( case_getowner($case_id) == $_SESSION['user_id'] )  {
			// Owner may always view.
			return 0;
		}
		else  {
			return ERR_PERM;
//			set_error( ERR_PERM_CASEVIEW );
		}
	}
	if ( $perm == 'w' || $perm == 'd' )  {
		$isopen = case_isopen( $case_id );
		if ( case_getowner($case_id) == $_SESSION['user_id'] )  {
			// Case owner can edit/delete, even if the case is closed.
			return 0;
		}
		elseif ( $_SESSION['user_perm'] == $USER_PERMS['administrator'] )  {
			// Administrator can view/edit/delete.
			return 0;
		}
		elseif ( $isopen != 0 && $isopen != ERR_DB_NODATA )  {
			// No-one may edit if the case is 'closed'.
			set_error( ERR_PERM_CASECLOSED );
			return ERR_PERM;
		}
		else  {
			if ( $perm == 'w' )  {
				set_error( ERR_PERM_CASEEDIT );
			}
			else  {
				set_error( ERR_PERM_CASEDELETE );
			}
			return ERR_PERM;
		}
	}

	return ERR_PERM;
 }



 // Function: check_case_id( [case_id] )
 // Return 0 if a case ID exists.
 function check_case_id( $case_id="" )  {

	global $db_connection;

	if ( empty($case_id) )  {
		return ERR_UNDEF;
	}

	$case = mysql_get_rows( 'reportsdb_cases', 'case_id', 'case_id=' . $case_id, NULL, NULL, $db_connection );
	if ( mysql_num_rows($case) == 0 )  {
		return ERR_UNDEF;
	}

	return 0;
 }



 // Function: case_isopen( [case_id] )
 // Returns 0 if case is open, ERR_UNDEF otherwise..
 function case_isopen( $case_id="" )  {

	global $db_connection;

	if ( empty($case_id) )  {
		return ERR_UNDEF;
	}
	$isopen = mysql_get_rows( 'reportsdb_cases', 'case_open', 'case_id=' . $case_id, NULL, NULL, $db_connection );
	if ( mysql_num_rows($isopen) > 0 )  {
		if (strtolower(mysql_result($isopen, 0, 'case_open')) == 'open' )  {
			return 0;
		}
	}
	else  {
		return ERR_DB_NODATA;
	}

	return ERR_UNDEF;
 }



 // Function: case_getowner( [case_id] )
 // Returns the owner_id for a particular case.
 function case_getowner( $case_id="" )  {

	global $db_connection;

	if ( empty($case_id) )  {
		return ERR_UNDEF;
	}
	$owner_id = mysql_get_rows( 'reportsdb_cases', 'owner_id', 'case_id=' . $case_id, NULL, NULL, $db_connection );
	if ( mysql_num_rows($owner_id) > 0 )  {
		return mysql_result( $owner_id, 0, 'owner_id' );
	}

	return ERR_UNDEF;
 }



 // Function: close_case( [case_id] )
 // Close a case.
 function close_case( $case_id="" )  {

	global $db_connection;

	if ( empty($case_id) )  {
		return ERR_UNDEF;
	}
	if ( case_check_perms( 'w', $case_id ) != 0 )  {
		return ERR_PERM;
	}

	// First locate all associated reports and publish them.
	$reports = mysql_get_rows( 'reportsdb_reports', 'owner_id', 'case_id=' . $case_id, NULL, NULL, $db_connection );
	$numrows = mysql_num_rows( $reports );
	if ( $numrows != 0 )  {
		for ( $i=0; $i<$numrows; $i++ )  {
			report_publish( $case_id, mysql_result($reports, $i, 'owner_id') );
		}
	}

	// Now close the case.
	$query = "UPDATE reportsdb_cases SET case_open='closed' WHERE case_id='" . $case_id . "'";
	if ( !mysql_query($query, $db_connection) )  {
		return ERR_UNDEF;
	}

	return 0;
 }



 // Function: open_case( [case_id] )
 // Open a closed case.
 function open_case( $case_id="" )  {

	global $db_connection;

	if ( empty($case_id) )  {
		return ERR_UNDEF;
	}
	if ( case_check_perms( 'w', $case_id ) != 0 )  {
		return ERR_PERM;
	}

	$query = "UPDATE reportsdb_cases SET case_open='open' WHERE case_id='" . $case_id . "'";
	if ( mysql_query($query, $db_connection) === FALSE )  {
		return ERR_UNDEF;
	}

	return 0;
 }



 // Function: check_case_expired()
 // Checks the expiration date of a case and returns true of case is expired.
 function check_case_expired( $case_id="" )  {

	global $db_connection;

	if ( empty($case_id) )  {
		return ERR_UNDEF;
	}

	$expiration = mysql_get_rows( 'reportsdb_cases', 'expiration_date', 'case_id=' . $case_id, NULL, NULL, $db_connection );
	if ( !$expiration )  {
		do_log( 'Error: check_case_expired(): Error in SQL query, '. mysql_error() );
		return ERR_UNDEF;
	}
	$expiration = mysql_result( $expiration, 0, 'expiration_date' );
	if ( empty($expiration) || $expiration == '0000-00-00' )  {
		return false;
	}
	if ( strtotime($expiration) <= time() )  {
		// Case expired.
		return true;
	}

	return false;
 }



 // Function: close_exp_cases()
 // Checks the expiration date of all cases and closes expired cases.  This just calls
 //   close_case() which also publishes reports.
 // This should really just be run from a cron job.
 function close_exp_cases()  {

	global $db_connection;

	$result = mysql_get_rows( 'reportsdb_cases', 'case_id', 'curdate()>=`expiration_date` AND case_open=open AND `date`!=0000-00-00 AND expiration_date!=0000-00-00', NULL, NULL, $db_connection );
	$num_rows = mysql_num_rows( $result );
	if ( $num_rows < 1 )  {
		return 0;
	}
	else  {
		do_log( 'INFO: close_exp_cases(): ' . $num_rows . ' expired cases found.' );
	}
	while ( $row = mysql_fetch_row($result) )  {
		close_case( $row[0] );
	}

	return 0;
 }



 // Function: list_cases( [user_id] )
 // Returns an associative array with case information for all the cases (open and closed) the $user_id
 // has been on.
 //   $cases[case_id]['date']
 //   $cases[case_id]['investigation_title']
 //   $cases[case_id]['description']
 //   $cases[case_id]['data']
 function list_cases( $user_id="" )  {

	global $db_connection;

	if ( empty($user_id)  )  {
		return ERR_UNDEF;
	}

	$cases = array();
 	$result = mysql_get_rows( 'reportsdb_cases', 'case_id,investigation_title,date,description,investigators,data_submitted', 'investigators LIKE %' . $user_id . '%', 'date', NULL, $db_connection );
	if ( mysql_num_rows($result) < 1 )  {
		return $cases;
	}
	while ( $row = mysql_fetch_row($result) )  {
		$investigators = explode( ',', $row[4] );
		foreach ( $investigators as $key )  {
			if ( $user_id == trim($key) )  {
				// User is on this case.
				$cases[$row[0]]['investigation_title'] = $row[1];
				$cases[$row[0]]['date'] = $row[2];
				$cases[$row[0]]['description'] = $row[3];

				// Check to see if data was turned in.
				$cases[$row[0]]['data'] = "";
				$data = explode( ',', $row[5] );
				foreach ( $data as $key2 )  {
					$tmp = explode( '|', $key2 );
					if ( $user_id == $tmp[0] )  {
						if ( isset($tmp[1]) && $tmp[1] == 1 )  {
							$cases[$row[0]]['data'] = 1;
						}
					}
				}
				break;
			}
		}
	}

	return $cases;
 }



 // Function: list_mycases( [user_id] )
 // Returns an associative array with case information for all the cases the user *owns*.
 // Different than list_cases(), which prints out only those cases the user has been on.
 //   $cases[case_id]['date']
 //   $cases[case_id]['investigation_title']
 //   $cases[case_id]['description']
 function list_mycases( $user_id="" )  {

	global $db_connection;

	if ( empty($user_id)  )  {
		return ERR_UNDEF;
	}

	$cases = array();
	$result = mysql_get_rows( 'reportsdb_cases', 'case_id,investigation_title,date,description', 'owner_id=' . $user_id, 'date', NULL, $db_connection );
	if ( mysql_num_rows($result) < 1 )  {
		return $cases;
	}
	while ( $row = mysql_fetch_row($result) )  {
		$cases[$row[0]]['investigation_title'] = $row[1];
		$cases[$row[0]]['date'] = $row[2];
		$cases[$row[0]]['description'] = $row[3];
	}

	return $cases;
 }



 // Function: list_cases_all()
 // Returns an associative array with case information for all the cases.
 // Different than list_cases(), which prints out only those cases the user has been on.
 //	$cases[$row[0]]['owner_id']
 //	$cases[$row[0]]['investigation_title']
 //	$cases[$row[0]]['date']
 //	$cases[$row[0]]['description']
 //	$cases[$row[0]]['city']
 //	$cases[$row[0]]['state']
 //	$cases[$row[0]]['loc_type']
 //	$cases[$row[0]]['investigators']
 //	$cases[$row[0]]['investigators_role']
 function list_cases_all()  {

	global $db_connection;

	$cases = array();
	$result = mysql_get_rows( 'reportsdb_cases', 'case_id,owner_id,investigation_title,date,description,city,state,loc_type,investigators,investigators_role', NULL, 'date', NULL, $db_connection );
	if ( mysql_num_rows($result) < 1 )  {
		return $cases;
	}
	while ( $row = mysql_fetch_row($result) )  {
		$cases[$row[0]]['owner_id'] = $row[1];
		$cases[$row[0]]['investigation_title'] = $row[2];
		$cases[$row[0]]['date'] = $row[3];
		$cases[$row[0]]['description'] = $row[4];
		$cases[$row[0]]['city'] = $row[5];
		$cases[$row[0]]['state'] = $row[6];
		$cases[$row[0]]['loc_type'] = $row[7];
		$cases[$row[0]]['investigators'] = $row[8];
		$cases[$row[0]]['investigators_role'] = $row[9];
	}

	return $cases;
 }



 // Function: list_case_info( [case_id] )
 // Returns an associative array with information about [case_id].
 //	$cases[case_id]['owner_id']
 //	$cases[case_id]['investigation_title']
 //	$cases[case_id]['date']
 //	$cases[case_id]['time']
 //	$cases[case_id]['timezone'];
 //	$cases[case_id]['expiration_date']
 //	$cases[case_id]['city']
 //	$cases[case_id]['state']
 //	$cases[case_id]['loc_type']
 //	$cases[case_id]['case_open']
 //	$cases[case_id]['investigation_type']
 //	$cases[case_id]['recap_date']
 //	$cases[case_id]['recap_time']
 //	$cases[case_id]['recap_location']
 //	$cases[case_id]['description']
 //	$cases[case_id]['investigators']
 //	$cases[case_id]['investigators_role']
 function list_case_info( $case_id="" )  {

	global $db_connection;

	if ( empty($case_id) || check_case_id($case_id) != 0 )  {
		return ERR_UNDEF;
	}

	$cases = array();
	$result = mysql_get_rows( 'reportsdb_cases', 'owner_id,investigation_title,date,time_hour,time_minute,timezone,expiration_date,city,state,loc_type,case_open,investigation_type,recap_date,recap_time_hour,recap_time_minute,recap_time_timezone,recap_location,description,investigators,investigators_role', 'case_id=' . $case_id, NULL, NULL, $db_connection );
	if ( mysql_num_rows($result) == 0 )  {
		return $cases;
	}
	while ( $row = mysql_fetch_row($result) )  {
		$cases[$case_id]['owner_id'] = $row[0];
		$cases[$case_id]['investigation_title'] = $row[1];
		$cases[$case_id]['date'] = $row[2];
		if ( !empty($row[3]) && !empty($row[4]) )  {
			$cases[$case_id]['time'] = $row[3] . ':' . $row[4] . ' ' . strtoupper($row[5]);
		}
		else  {
			$cases[$case_id]['time'] = "";
		}
		$cases[$case_id]['timezone'] = strtoupper($row[5]);
		$cases[$case_id]['expiration_date'] = $row[6];
		$cases[$case_id]['city'] = $row[7];
		$cases[$case_id]['state'] = $row[8];
		$cases[$case_id]['loc_type'] = $row[9];
		$cases[$case_id]['case_open'] = $row[10];
		$cases[$case_id]['investigation_type'] = $row[11];
		$cases[$case_id]['recap_date'] = $row[12];
		if ( !empty($row[13]) && !empty($row[14]) )  {
			$cases[$case_id]['recap_time'] = $row[13] . ':' . $row[14] . ' ' . strtoupper($row[15]);
		}
		else  {
			$cases[$case_id]['recap_time'] = "";
		}
		$cases[$case_id]['recap_location'] = $row[16];
		$cases[$case_id]['description'] = $row[17];
		$cases[$case_id]['investigators'] = $row[18];
		$cases[$case_id]['investigators_role'] = $row[19];
	}

	return $cases;
 }



 // Function: generate_caseid()
 // Returns an unused case_id.
 function generate_caseid()  {

	global $db_connection;

	// Case IDs (ie. I6010) have the following components:
		// I|W, investigation or walkthrough.
		// Last two digits of year, reversed (ie. 60)
		// Incremented two-digit number.

	$count = 0;
	$case_id = 'I' . strrev( date("y") );
	$caseids = mysql_get_rows( 'reportsdb_cases', 'case_id', NULL, NULL, NULL, $db_connection );
	while ( $row = mysql_fetch_row($caseids) )  {
		if ( strstr($row[0], $case_id) )  {
			$row[0] = substr( $row[0], 3 );
			if ( !is_numeric($row[0]) )  {
				continue;
			}
			if ( $row[0] > $count )  {
				$count = $row[0];
			}
		}
	}
	$count++;
	if ( $count < 10 )  {
		$count = 0 . $count;
	}

	return $case_id . $count;
 }



 // Function: check_caseid_format()
 // Check to make sure case_id conforms to the I/W/E (investigation, walkthrough,
 // expedition) standards.
 function check_caseid_format( $case_id="", $inv_type="" )  {

	if ( empty($case_id) || empty($inv_type) )  {
		return ERR_UNDEF;
	}
	$inv_type = strtolower( $inv_type );

	if ( $inv_type == 'investigation' )  {
		if ( !preg_match('/^[Ii]/', $case_id) )  {
			// Case_ID does not start with an 'i'.
			return ERR_UNDEF;
		}
	}
	elseif ( $inv_type == 'walkthrough' )  {
		if ( !preg_match('/^[Ww]/', $case_id) )  {
			// Case_ID does not start with an 'w'.
			return ERR_UNDEF;
		}
	}
	elseif ( $inv_type == 'expedition' )  {
		if ( !preg_match('/^[Ee]/', $case_id) )  {
			// Case_ID does not start with an 'e'.
			return ERR_UNDEF;
		}
	}

	return 0;
 }



 // Function: check_investigator_stats( $investigators )
 // Checks investigators and returns array of all users that have exceeded the threshold set
 // by $USER_ALLOWED_CASE_THRESHOLD.
 function check_investigator_stats( $investigators="" )  {

	global $USER_ALLOWED_CASE_THRESHOLD;

	$bad_investigators = array();
	if ( empty($investigators) )  {
		return $bad_investigators;
	}

	$investigators = explode( ',', $investigators );
	foreach ( $investigators as $user )  {
		$user_stats = get_user_stats( $user );
		foreach ( array_keys($user_stats) as $username )  {
			if ( ($user_stats[$username]['cases'] - $user_stats[$username]['reports']) > $USER_ALLOWED_CASE_THRESHOLD )  {
				// User has exceeded threshold.
				$bad_investigators[$username]['user_id'] = $user;
				$bad_investigators[$username]['name'] = $user_stats[$username]['name'];
				$bad_investigators[$username]['reports'] = $user_stats[$username]['reports'];
				$bad_investigators[$username]['cases'] = $user_stats[$username]['cases'];
				$bad_investigators[$username]['overdue_cases'] = $user_stats[$username]['cases'] - $user_stats[$username]['reports'];
				break;
			}
		}
	}

	return $bad_investigators;
 }



 // Function: reassign_case( [case_id], [olduid], [newuid] )
 // Send email notification about case_reassignment.
 function reassign_case( $case_id="", $olduid="", $newuid="" )  {

	if ( empty($case_id) || empty($olduid) || empty($newuid) )  {
		return ERR_UNDEF;
	}

	$olduser_info = get_userinfo( $olduid );
	$newuser_info = get_userinfo( $newuid );

	$subject = REPORTS_DB_NAME . ": " . CASE_REASSIGN_SUBJECT;
	$message = CASE_REASSIGN_BODY;
	$message = preg_replace( '/%case_id%/', $case_id, $message );
	$message = preg_replace( '/%olduser%/', $olduser_info['name'], $message );

	// Set message and change task to view_case.
	$mesg = CASE_REASSIGN_SUCCESS;
	$mesg = preg_replace( '/%case_id%/', $case_id, $mesg );
	$mesg = preg_replace( '/%newuser%/', $newuser_info['name'], $mesg );
	set_error( $mesg );

	// Send email to the new owner.
	if ( send_email($message, $subject, $newuser_info['email']) != 0 )  {
		return ERR_UNDEF;
	}

	return 0;
 }



 // Function: email_reminder( [case_id] )
 // Send an email to remind the user to complete his/her report.
 // Returns either the number of emails sent, or ERR_UNDEF on error.
 function email_reminder( $case_id="" )  {

	global $NUM_INVESTIGATORS;

	if ( empty($case_id) )  {
		return ERR_UNDEF;
	}

	$count = 0;
	for ( $i=1; $i<$NUM_INVESTIGATORS+1; $i++ )  {
		if ( !empty($_REQUEST['investigator_userid_'.$i]) )  {
			if ( isset($_REQUEST['investigator_reminder_'.$i]) && $_REQUEST['investigator_reminder_'.$i] == 1 )  {
				$user_id = $_REQUEST['investigator_userid_'.$i];
				$user_info = get_userinfo( $user_id );
				$subject = REPORTS_DB_NAME . ": " . CASE_SEND_REMINDER_SUBJECT;
			        $message = CASE_SEND_REMINDER_BODY;
				$message = preg_replace( '/%case_id%/', $case_id , $message);
				$message = preg_replace( '/%owner_id%/', $user_id , $message);
				if ( send_email($message, $subject, $user_info['email']) == 0 )  {
					set_error( CASE_SEND_REMINDER_SUCCESS );
					$count++;
				}
				else  {
					set_error( CASE_SEND_REMINDER_UNSUCCESS );
					return ERR_UNDEF;
				}
			}
		}
	}

	return $count;
 }



 // Function: print_caseinfo( [case_id] )
 function print_caseinfo( $case_id='' )  {

	$delim = "\n";
	if ( isset($_REQUEST['format']) )  {
		if ( $_REQUEST['format'] == 'html' )  {
			$delim = "<br/>\n";
		}
		else  {
			header( "content-type: text/plain" );
		}
	}
	else  {
		header( "content-type: text/plain" );
	}

	$users = array();
	if ( empty($case_id) || check_case_id($case_id) != 0 )  {
		$cases = list_cases_all();
	}
	else  {
		$cases = list_case_info( $case_id );
	}
	foreach ( array_keys($cases) as $case_id )  {
		print "<item>" . $delim;
		print "<case_id>" . $case_id . "</case_id>" . $delim;
		foreach ( array_keys($cases[$case_id]) as $case_info )  {
		print "<" . $case_info . ">";
			if ( $case_info == 'investigators' )  {
				$users = explode( ',', $cases[$case_id][$case_info] );
				$count = count( $users ) - 1;
				foreach ( $users as $uid )  {
					$user_info = get_userinfo( trim($uid) );
					print preg_replace('/,/', '', $user_info['name']) . '|' . $uid;
					if ( $count > 0 )  {
						print ",";
					}
					$count--;
				}
			}
			elseif ( $case_info == 'investigators_role' )  {
				$users = explode( ',', $cases[$case_id][$case_info] );
				$count = count( $users ) - 1;
				foreach ( $users as $uid )  {
					$id_pos = explode( '|', $uid );
					$user_info = get_userinfo(trim($id_pos[0]));
					print preg_replace('/,/', '', $user_info['name']) . '|' . $id_pos[1];
					if ( $count > 0 )  {
						print ",";
					}
					$count--;
				}
			}
			else  {
				print preg_replace( '/[\r\n]/', '<br/>', $cases[$case_id][$case_info] );
			}
			print "</" . $case_info . ">" . $delim;
		}
		print "</item>" . $delim . "\n";
	}

	return 0;
 }




?>
