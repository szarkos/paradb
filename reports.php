<?php

 //-------------------------------------------------------------------//
 //
 // AGHOST Paranormal Reporting Database (ParaDB)
 // Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>
 //
 // See the file COPYING for terms of use and redistribution.
 //
 // File: reports.php
 //	  Functions related to working with reports.
 //
 //-------------------------------------------------------------------//


 require( 'reports_print.php' );

 // Function: view_report( [case_id], [owner_id] )
 // Display a report.
 function view_report( $case_id="", $owner_id="" )  {

	global $REPORTS_VALID_FIELDS, $REPORTS_IMPRESSIONS_VALID_FIELDS, $REPORTS_ROOM_DATA_VALID_FIELDS;

	if ( empty($case_id) || empty($owner_id) )  {
		return ERR_UNDEF;
	}
	if ( report_check_perms('r', $case_id, $owner_id) != 0 )  {
		return ERR_PERM;
	}

	if ( report_exists($case_id, $owner_id) != 0 )  {
		$uid = get_userinfo( $owner_id );
		if ( empty($uid) )  {
			$uid = "";
		}
		else  {
			$uid = $uid['name'] . ' (' . $owner_id . ')';
		}
		set_error( ERR_REPORT_NOEXIST );
		set_error( '<br/>Case ID: ' . $case_id . ', Owner ID: ' . $uid, 'a' );
		return ERR_UNDEF;
	}

	// Open existing report.
	set_report_fields( $case_id, $owner_id );
	set_title( ' ('. $case_id . ')', 'a' );
	print_report_view();

	return 0;
 }



 // Function: edit_report( [case_id], [owner_id], [opts] )
 // Edit or create a report.
 function edit_report( $case_id="", $owner_id="", $opts="" )  {

	global $REPORTS_VALID_FIELDS, $REPORTS_IMPRESSIONS_VALID_FIELDS, $REPORTS_ROOM_DATA_VALID_FIELDS;

	if ( empty($case_id) || empty($owner_id) )  {
		return ERR_UNDEF;
	}

	if ( check_case_id($case_id) == 0 )  {
		if ( report_check_perms('w', $case_id, $owner_id) != 0 )  {
			return ERR_PERM;
		}
		if ( $opts != 'NOSET' )  {
			set_report_fields( $case_id, $owner_id );
		}
	}
	else  {
		// New report.
		$owner_id = $_SESSION['user_id'];  // Safety check.
	}
	set_title( ' ('. $case_id . ')', 'a' );
        print_report_form();

        return 0;
 }



 // Function: save_report( [case_id], [owner_id] )
 function save_report( $case_id="", $owner_id="" )  {

	global $REPORTS_VALID_FIELDS, $REPORTS_IMPRESSIONS_VALID_FIELDS, $REPORTS_ROOM_DATA_VALID_FIELDS, $NUM_ROOMS;
	global $TECH_EQUIPMENT, $AUDIO_EQUIPMENT, $VIDEO_EQUIPMENT, $PHOTO_EQUIPMENT, $PSI_EQUIPMENT;
	global $db_connection;

	if ( empty($case_id) || empty($owner_id) )  {
		return ERR_UNDEF;
	}

	// Clean fields and push the values from the form into the various arrays.
	clean_report_fields( $REPORTS_VALID_FIELDS );
	clean_report_fields( $REPORTS_IMPRESSIONS_VALID_FIELDS );
	clean_report_fields( $TECH_EQUIPMENT );
	clean_report_fields( $AUDIO_EQUIPMENT );
	clean_report_fields( $VIDEO_EQUIPMENT );
	clean_report_fields( $PHOTO_EQUIPMENT );
	clean_report_fields( $PSI_EQUIPMENT );
	set_equip_fields( $TECH_EQUIPMENT, 'tech_equip_misc' );
	set_equip_fields( $AUDIO_EQUIPMENT, 'audio_equip_misc' );
	set_equip_fields( $VIDEO_EQUIPMENT, 'video_equip_misc' );
	set_equip_fields( $PHOTO_EQUIPMENT, 'photo_equip_misc' );
	set_equip_fields( $PSI_EQUIPMENT, 'psi_equip_misc' );

	// Set and clean the room data fields.
	for ( $i=1; $i<$NUM_ROOMS+1; $i++ )  {
		global ${'ROOM_DATA_'.$i};
		set_room_fields( ${'ROOM_DATA_'.$i}, 'ROOM_DATA_'.$i );
		anomalies_shift( ${'ROOM_DATA_'.$i} );
	}

	// Shift room data upward.
	rooms_shift();

	// Set the last edited date.
	$REPORTS_VALID_FIELDS['report_edit_date']['value'] = date("Y-n-j H:i:s");

	// Set the report date from the case 'date' field.
	$REPORTS_VALID_FIELDS['date']['value'] = get_case_date( $case_id );

	// Populate the geomagnetic and xray fields.
	include_once( 'geo-xray.php' );
	populate_geo_xray( $REPORTS_VALID_FIELDS );

	// Obtain moon phase data.
	include_once( 'moonphase.php' );
	populate_moon_phase( $REPORTS_VALID_FIELDS );

	// Check to see if all the required fields have been set.
	foreach ( array_keys($REPORTS_VALID_FIELDS) as $key )  {
		if ( empty($REPORTS_VALID_FIELDS[$key]['value']) )  {
			if ( $REPORTS_VALID_FIELDS[$key]['required'] == 'yes' )  {
				set_error( REPORT_FORM_ERROR );
				edit_report( $case_id, $owner_id, 'NOSET' );
				return ERR_UNDEF;
			}
		}
	}

	// Check room data to see if all the required fields have been set.
	for ( $i=1; $i<$NUM_ROOMS+1; $i++ )  {
		$required = 0;
		$not_empty = 0;
		foreach ( array_keys(${'ROOM_DATA_'.$i}) as $key )  {
			if ( $key == 'case_id' || $key == 'owner_id' )  {
				continue;
			}
			if ( !empty(${'ROOM_DATA_'.$i}[$key]['value']) && ${'ROOM_DATA_'.$i}[$key]['value'] != 0 )  {
				if ( !strstr($key, '_units') )  {
					$not_empty = 1;
				}
			}
			elseif ( empty(${'ROOM_DATA_'.$i}[$key]['value']) && ${'ROOM_DATA_'.$i}[$key]['required'] == 'yes' )  {
				$required = 1;
			}
			if ( $required == 1 && $not_empty == 1 )  {
				// Missing required data.
				set_error( REPORT_FORM_ERROR );
				edit_report( $case_id, $owner_id, 'NOSET' );
				return ERR_UNDEF;
			}
		}
	}

	// Update an existing report.
	if ( report_exists($case_id, $owner_id) == 0 )  {
		if ( report_check_perms('w', $case_id, $owner_id, $REPORTS_VALID_FIELDS['owner_id']['value']) != 0 )  {
			return ERR_PERM;
		}
		mysql_db_update( 'reportsdb_reports', $REPORTS_VALID_FIELDS, 'owner_id=' . $owner_id . ' AND case_id=' . $case_id, $db_connection );
		mysql_db_update( 'reportsdb_reports_impressions', $REPORTS_IMPRESSIONS_VALID_FIELDS, 'owner_id=' . $owner_id . ' AND case_id=' . $case_id, $db_connection );

		// Update reportsdb_room_data_# tables.
		for ( $i=1; $i<$NUM_ROOMS+1; $i++ )  {
			if ( mysql_db_update('reportsdb_room_data_'.$i, ${'ROOM_DATA_'.$i}, 'owner_id=' . $owner_id . ' AND case_id=' . $case_id, $db_connection) != 0 )  {
				do_log( 'Error: save_report(): failed to update table reportsdb_room_data_' . $i . '. CaseID=' . $case_id . ' OwnerID=' . $owner_id );
			}
		}

		// Update the $*_EQUIPMENT values.
		mysql_db_update( 'reportsdb_reports_equipment', array_merge($TECH_EQUIPMENT, $AUDIO_EQUIPMENT, $VIDEO_EQUIPMENT, $PHOTO_EQUIPMENT, $PSI_EQUIPMENT), 'owner_id=' . $owner_id . ' AND case_id=' . $case_id, $db_connection );
	}

	// Insert a new report.
	else  {
		if ( report_check_perms('w', $case_id, $owner_id, $REPORTS_VALID_FIELDS['owner_id']['value']) != 0 )  {
			return ERR_PERM;
		}

		mysql_db_insert( 'reportsdb_reports', $REPORTS_VALID_FIELDS, $db_connection );
		mysql_db_insert( 'reportsdb_reports_impressions', $REPORTS_IMPRESSIONS_VALID_FIELDS, $db_connection );

		// Update reportsdb_room_data_# tables.
		for ( $i=1; $i<$NUM_ROOMS+1; $i++ )  {
			global ${'ROOM_DATA_'.$i};
			clean_report_fields( ${'ROOM_DATA_'.$i} );
			if ( mysql_db_insert('reportsdb_room_data_'.$i, ${'ROOM_DATA_'.$i}, $db_connection) != 0 )  {
				do_log( 'Error: save_report(): failed to insert record into table reportsdb_room_data_' . $i . '. CaseID=' . $case_id . ' OwnerID=' . $owner_id . '.' );
			}
		}

		// Insert the $*_EQUIPMENT values.
		mysql_db_insert( 'reportsdb_reports_equipment', array_merge($TECH_EQUIPMENT, $AUDIO_EQUIPMENT, $VIDEO_EQUIPMENT, $PHOTO_EQUIPMENT, $PSI_EQUIPMENT), $db_connection );
	}

	return 0;
 }



 // Function: delete_report( [case_id], [owner_id] )
 function delete_report( $case_id="", $owner_id="" )  {

	global $db_connection, $NUM_ROOMS;

	if ( empty($case_id) || empty($owner_id) )  {
		return ERR_UNDEF;
	}
	if ( report_exists($case_id, $owner_id) == 0 )  {
		if ( case_isopen($case_id) != 0 )  {
			// Cannot delete a report if that case has been closed.
			// FIXME: print an error.
			return ERR_PERM;
		}
		if ( report_check_perms('d', $case_id, $owner_id, $REPORTS_VALID_FIELDS['owner_id']['value']) != 0 )  {
			return ERR_PERM;
		}
		mysql_db_delete( 'reportsdb_reports', 'owner_id=' . $owner_id . ' AND case_id=' . $case_id, $db_connection );
		mysql_db_delete( 'reportsdb_reports_impressions', 'owner_id=' . $owner_id . ' AND case_id=' . $case_id, $db_connection );
		mysql_db_delete( 'reportsdb_reports_equipment', 'owner_id=' . $owner_id . ' AND case_id=' . $case_id, $db_connection );

		// Delete the reportsdb_room_data_# tables.
		for ( $i=1; $i<$NUM_ROOMS+1; $i++ )  {
			global ${'ROOM_DATA_'.$i};
			mysql_db_delete( 'reportsdb_room_data_'.$i, 'owner_id=' . $owner_id . ' AND case_id=' . $case_id, $db_connection );
		}
	}
	else  {
		// FIXME: Error, report does not exist.
		return ERR_UNDEF;
	}

	return 0;
 }



 // Function: clean_report_fields()
 // Clean POST fields related to the $REPORTS_VALID_FIELDS variable and check lengths.
 function clean_report_fields( &$VALID_FIELDS )  {

	foreach ( array_keys($VALID_FIELDS) as $key )  {
		if ( isset($VALID_FIELDS[$key]['ischecked']) )  {
			// This is supposed to be a boolean value (checkbox).
			if ( !isset($_REQUEST[$key]) )  {
				$VALID_FIELDS[$key]['value'] = "0";
				continue;
			}
			if ( $_REQUEST[$key] == "1" || strtolower($_REQUEST[$key]) == "yes" )  {
				$VALID_FIELDS[$key]['ischecked'] = "1";
				$VALID_FIELDS[$key]['value'] = "1";
			}
			else  {
				$VALID_FIELDS[$key]['ischecked'] = "0";
				$VALID_FIELDS[$key]['value'] = "0";
			}
			continue;
		}

		if ( $key == 'date' && isset($_REQUEST['date']) )  {
			$_REQUEST[$key] = $_REQUEST['date_year'] . '-' . $_REQUEST['date_month'] . '-' . $_REQUEST['date_day'];
			if ( check_date($_REQUEST[$key]) === FALSE )  {
				set_error( ERR_DATE );
				$_REQUEST[$key] = "";
				continue;
			}
		}

		// Skip the rest if there is no data from form.
		if ( !isset($_REQUEST[$key]) )  {
			continue;
		}
		if ( is_array($_REQUEST[$key]) )  {
			// The form sent back an array.
			foreach ( array_keys($_REQUEST[$key]) as $value )  {
				$_REQUEST[$key][$value] = clean_field( $_REQUEST[$key][$value] );
				if ( isset($VALID_FIELDS[$value]) )  {
					if ( $VALID_FIELDS[$value]['maxlength'] != NULL && $VALID_FIELDS[$value]['maxlength'] != 0 )  {
						$_REQUEST[$key][$value] = check_length( $_REQUEST[$key], $VALID_FIELDS[$value]['maxlength'] );
					}
					$VALID_FIELDS[$value]['value'] = $_REQUEST[$key][$value];
				}
			}
		}
		else  {
			$_REQUEST[$key] = clean_field( $_REQUEST[$key] );  // Clean field first, as this
									   // may increase the size of the value.
			if ( $VALID_FIELDS[$key]['maxlength'] != NULL && $VALID_FIELDS[$key]['maxlength'] != 0 )  {
				$_REQUEST[$key] = check_length( $_REQUEST[$key], $VALID_FIELDS[$key]['maxlength'] );
			}
			$VALID_FIELDS[$key]['value'] = $_REQUEST[$key];
		}
	}

	// Set owner_id in hash.
	if ( !isset($_REQUEST['owner_id']) )  {
		$VALID_FIELDS['owner_id']['value'] = $_SESSION['user_id'];
	}

	return 0;
 }



 // Function: set_report_fields( [case_id], [owner_id] )
 // Pull data from the reportsdb_reports_* table sand populate $REPORTS_*_VALID_FIELDS.
 function set_report_fields( $case_id="", $owner_id="" )  {

	global $REPORTS_VALID_FIELDS, $REPORTS_IMPRESSIONS_VALID_FIELDS, $REPORTS_ROOM_DATA_VALID_FIELDS, $NUM_ROOMS;
	global $TECH_EQUIPMENT, $AUDIO_EQUIPMENT, $VIDEO_EQUIPMENT, $PHOTO_EQUIPMENT, $PSI_EQUIPMENT;
	global $db_connection;

	if ( empty($case_id) || empty($owner_id)  )  {
		return ERR_UNDEF;
	}

	// $REPORTS_VALID_FIELDS
	$data = mysql_get_rows( 'reportsdb_reports', '*', 'case_id=' . $case_id . ' AND owner_id=' . $owner_id, NULL, NULL, $db_connection );
	$data = mysql_fetch_array( $data, MYSQL_ASSOC );
	foreach ( array_keys($data) as $key )  {
		$REPORTS_VALID_FIELDS[$key]['value'] = $data[$key];
		$REPORTS_VALID_FIELDS[$key]['value'] = stripslashes( $REPORTS_VALID_FIELDS[$key]['value'] );
	}

	// $REPORTS_IMPRESSIONS_VALID_FIELDS
	$data = mysql_get_rows( 'reportsdb_reports_impressions', '*', 'case_id=' . $case_id . ' AND owner_id=' . $owner_id, NULL, NULL, $db_connection );
	$data = mysql_fetch_array( $data, MYSQL_ASSOC );
	foreach ( array_keys($data) as $key )  {
		$REPORTS_IMPRESSIONS_VALID_FIELDS[$key]['value'] = $data[$key];
		$REPORTS_IMPRESSIONS_VALID_FIELDS[$key]['value'] = stripslashes( $REPORTS_IMPRESSIONS_VALID_FIELDS[$key]['value'] );
	}

	// $REPORTS_ROOM_DATA_VALID_FIELDS
	for ( $i=1; $i<$NUM_ROOMS+1; $i++ )  {
		global ${'ROOM_DATA'.'_'.$i};
		$data = mysql_get_rows( 'reportsdb_room_data_'.$i, '*', 'case_id=' . $case_id . ' AND owner_id=' . $owner_id, NULL, NULL, $db_connection );
		$data = mysql_fetch_array( $data, MYSQL_ASSOC );
		foreach ( array_keys($data) as $key )  {
			${'ROOM_DATA'.'_'.$i}[$key]['value'] = $data[$key];
			${'ROOM_DATA'.'_'.$i}[$key]['value'] = stripslashes( ${'ROOM_DATA'.'_'.$i}[$key]['value'] );
		}
	}

	// $*_EQUIPMENT Fields.
	$data = mysql_get_rows( 'reportsdb_reports_equipment', '*', 'case_id=' . $case_id . ' AND owner_id=' . $owner_id, NULL, NULL, $db_connection );
	$equip_arrays = array( 'TECH_EQUIPMENT', 'AUDIO_EQUIPMENT', 'VIDEO_EQUIPMENT', 'PHOTO_EQUIPMENT', 'PSI_EQUIPMENT' );
	foreach ( $equip_arrays as $equip_fields )  {
		foreach ( array_keys(${$equip_fields}) as $key )  {
			${$equip_fields}[$key]['value'] = mysql_result( $data, 0, $key );
			if ( ${$equip_fields}[$key]['value'] === FALSE )  {
				${$equip_fields}[$key]['value'] = "";
			}
			${$equip_fields}[$key]['value'] = stripslashes( ${$equip_fields}[$key]['value'] );
			if ( isset(${$equip_fields}[$key]['ischecked']) )  {
				if ( ${$equip_fields}[$key]['value'] == 1 || ${$equip_fields}[$key]['value'] == "yes" )  {
					${$equip_fields}[$key]['ischecked'] = 1;
				}
				else  {
					${$equip_fields}[$key]['value'] = "1";
					${$equip_fields}[$key]['ischecked'] = 0;
				}
			}
		}
	}

	return 0;
 }



 // Function: set_equip_fields()
 // This is a bit of a hack to take care of the *_equip_misc fields.
 function set_equip_fields( &$VALID_FIELDS, $misc="" )  {

	if ( !is_array($_REQUEST[$misc]) )  {
		return ERR_UNDEF;
	}

	$tmp_array = array();
	foreach ( $_REQUEST[$misc] as $item )  {
		if ( !empty($item) )  {
			$item = preg_replace( '/\|/', '/', $item );
			array_push( $tmp_array, $item );
		}
	}

	$count = count( $_REQUEST[$misc] );
	if ( count($tmp_array) < $count )  {
		do  {
			array_push( $tmp_array, '' );
		} while ( count($tmp_array) < $count );
	}
	$_REQUEST[$misc] = $tmp_array;
	$VALID_FIELDS[$misc]['value'] = implode( '|', $_REQUEST[$misc] );

	return 0;
 }



 // Function: set_room_fields()
 // This is a bit of a hack to take care of the $_REQUEST['ROOM_DATA_#'] array fields passed
 // from the form.
 function set_room_fields( &$VALID_FIELDS, $form_array="" )  {

	global $REPORTS_VALID_FIELDS;

// There may be no array if room is not shown. SAZ, 2007-02-20
//	if ( !is_array($_REQUEST[$form_array]) )  {
//		return ERR_UNDEF;
//	}

	if ( is_array($_REQUEST[$form_array]) )  {
		foreach ( array_keys($_REQUEST[$form_array]) as $key )  {
			if ( isset($VALID_FIELDS[$key]['value']) )  {
				$_REQUEST[$form_array][$key] = clean_field( $_REQUEST[$form_array][$key] );
				if ( $VALID_FIELDS[$key]['maxlength'] != 0 )  {
					$_REQUEST[$form_array][$key] = check_length( $_REQUEST[$form_array][$key], $VALID_FIELDS[$key]['maxlength'] );
				}
				$VALID_FIELDS[$key]['value'] = $_REQUEST[$form_array][$key];
			}
		}
	}

	// Set case_id and owner_id for each room.
	$VALID_FIELDS['case_id']['value'] = $REPORTS_VALID_FIELDS['case_id']['value'];
	$VALID_FIELDS['owner_id']['value'] = $REPORTS_VALID_FIELDS['owner_id']['value'];

	return 0;
 }



 // Function: report_check_perms( [r|w|d], [case_id], [owner_id] )
 // Checks to see if user has desired permission, r=read, w=write (edit), d=delete.
 function report_check_perms( $perm='r', $case_id="", $owner_id="", $real_owner_id="" )  {

	global $USER_PERMS;
	global $db_connection;

	if ( $perm != 'r' && $perm != 'w' && $perm != 'd' )  {
		return ERR_UNDEF;
	}
	if ( empty($case_id) || empty($owner_id) )  {
		return ERR_UNDEF;
	}
	if ( empty($real_owner_id) )  {
		$real_owner_id = $_SESSION['user_id'];
	}

	if ( $perm == 'r')  {
		// Anyone may view the report if it is 'published'.
		if ( report_ispublished( $case_id, $owner_id ) == 0 )  {
			return 0;
		}
	}
	if ( $perm == 'w' || $perm == 'd' )  {
		if ( case_isopen($case_id) != 0 )  {
			// Cannot edit a report if the case has been closed.
			set_error( ERR_PERMISSION_DENIED . '<br />' . ERR_REPORT_CASE_CLOSED, 'a' );
			return ERR_PERM;
		}
		if ( check_case_expired($case_id) )  {
			set_error( ERR_PERMISSION_DENIED . '<br />' . ERR_REPORT_CASE_EXPIRED, 'a' );
			return ERR_PERM;
		}
		if ( check_investigators($case_id, $owner_id) != 0 )  {
			// Check to make sure user is part of the case.
			set_error( ERR_PERMISSION_DENIED . '<br />' . ERR_REPORT_NOT_INVESTIGATOR, 'a' );
			return ERR_UNDEF;
		}
	}

	// Administrator can view/edit/delete.
	if ( $_SESSION['user_perm'] == $USER_PERMS['administrator'] )  {
		return 0;
	}

	// Owner may view/edit/delete.
	if ( $_SESSION['user_id'] == $owner_id )  {
		return 0;
	}

	return ERR_PERM;
 }



 // Function: report_exists( [case_id], [user_id] )
 function report_exists( $case_id="", $owner_id="" )  {

	global $db_connection;

	if ( empty($case_id) || empty($owner_id) )  {
		return ERR_UNDEF;
	}

	$case = mysql_get_rows( 'reportsdb_reports', 'case_id', 'case_id=' . $case_id . ' AND owner_id=' . $owner_id, NULL, NULL, $db_connection );
	if ( mysql_num_rows($case) == 0 )  {
		return ERR_UNDEF;
	}

	return 0;
 }



 // Function: check_investigators( [case_id], [owner_id] )
 // Check to see if $owner_id is actually listed in the 'investigators' column of the 'reportsdb_cases'
 // table.
 function check_investigators( $case_id="", $owner_id="" )  {

	global $db_connection;

	if ( empty($case_id) || empty($owner_id) )  {
		return ERR_UNDEF;
	}

	$investigators = mysql_get_rows( 'reportsdb_cases', 'investigators', 'case_id=' . $case_id, NULL, NULL, $db_connection );
	if ( mysql_num_rows($investigators) == 0 )  {
		return ERR_UNDEF;
	}

	$investigators = mysql_result( $investigators, 0, 'investigators' );
	foreach ( explode(',', $investigators) as $key )  {
		if ( $key == $owner_id )  {
			return 0;
		}
	}

	return ERR_UNDEF;
 }



 // Function: report_ispublished( [case_id], [owner_id] )
 function report_ispublished ( $case_id="", $owner_id="" )  {

	global $db_connection;

	if ( empty($case_id) || empty($owner_id) )  {
		return ERR_UNDEF;
	}
	$report_state = mysql_get_rows( 'reportsdb_reports', 'report_state', 'case_id=' . $case_id . ' AND owner_id=' . $owner_id, NULL, NULL, $db_connection );
	if ( mysql_num_rows($report_state) == 0 )  {
		return ERR_UNDEF;
	}
	if ( strtolower( mysql_result($report_state, 0, 'report_state') ) == 'published' )  {
                return 0;
        }

	return ERR_UNDEF;
 }



 // Function: list_reports( [user_id], [limit] )
 // Returns an associative array that contains general report information for which this user has
 // reports (finished or unfinished).
 // 	$reports[case_id][owner_id]
 // 	$reports[case_id][case_title]
 // 	$reports[case_id][description]
 // 	$reports[case_id][date]
 //	$reports[case_id][report_state]
 //	$reports[case_id][last_edited]
 function list_reports( $user_id="", $limit=NULL )  {

	global $db_connection;

	$reports = array();
	if ( !empty($user_id) )  {
		$result = mysql_get_rows( 'reportsdb_reports', 'case_id,owner_id,case_title,description,date,report_state,report_edit_date', 'owner_id=' . $user_id, 'date', $limit, $db_connection );
	}
	else  {
		$result = mysql_get_rows( 'reportsdb_reports', 'case_id,owner_id,case_title,description,date,report_state,report_edit_date', NULL, 'date', $limit, $db_connection );
	}
	if ( mysql_num_rows($result) == 0 )  {
		return $reports;
	}

	while ( $row = mysql_fetch_row($result) )  {
		$reports[$row[0]]['owner_id'] = $row[1];
		$reports[$row[0]]['case_title'] = $row[2];
		$reports[$row[0]]['description'] = $row[3];
		$reports[$row[0]]['date'] = $row[4];
		$reports[$row[0]]['report_state'] = $row[5];
		$reports[$row[0]]['last_edited'] = preg_replace( '/\s+/', ', ', $row[6] );
	}

	return $reports;
 }



 // Function: list_editable_reports( [user_id] )
 // Returns an array of case_ids for which this user has editable reports (reports already exist).
 // FIXME? Not very efficient.
 function list_editable_reports( $user_id="" )  {

	global $db_connection;

	if ( empty($user_id) )  {
		return ERR_UNDEF;
	}

	$editable_reports = array();
	$reports = list_reports( $user_id );
	if ( empty($reports) )  {
		return $editable_reports;
	}

	$open_cases = mysql_get_rows( 'reportsdb_cases', 'case_id', 'curdate()<`expiration_date` AND case_open=open AND date != 0000-00-00 AND expiration_date != 0000-00-00', NULL, NULL, $db_connection );
	if ( empty($open_cases) )  {
		return $editable_reports;
	}

	foreach ( array_keys($reports) as $report )  {
		foreach ( $open_cases as $case )  {
			if ( $report == $case )  {
				array_push( $editable_reports, $report );
				break;
			}
		}
	}

	return $editable_reports;
 }



 // Function: list_unfinished_reports( [user_id] )
 // Returns an associative array that correspond with reports that have not yet been started, but also
 // have not expired.
 //   $valid_cases[case_id]['date']
 //   $valid_cases[case_id]['investigation_title']
 //   $valid_cases[case_id]['description']
 //   $valid_cases[case_id]['city']
 //   $valid_cases[case_id]['state']
 function list_unfinished_reports( $user_id="" )  {

	global $db_connection;

	if ( empty($user_id) )  {
		return ERR_UNDEF;
	}

	$valid_cases = array();
	$cases = mysql_get_rows( 'reportsdb_cases', 'case_id,investigators,date,investigation_title,description,city,state', 'investigators LIKE %' . $user_id . '% AND curdate()<`expiration_date` AND case_open=open AND `date`!=0000-00-00 AND expiration_date!=0000-00-00', 'date', NULL, $db_connection );
	$numrows = mysql_num_rows( $cases );
	if ( $numrows == 0 )  {
		return $valid_cases;
	}

	$existing_reports = list_reports( $user_id );
	for ( $i=0; $i<$numrows; $i++ )  {
		$investigators = mysql_result( $cases, $i, 'investigators' );
		$investigators = explode( ',', $investigators );
		foreach ( $investigators as $key )  {
			if ( $user_id == trim($key) )  {
				// OK, user is on this case.
				$case = mysql_result( $cases, $i, 'case_id' );
				foreach ( array_keys($existing_reports) as $report )  {
					if ( $report == $case )  {
						// Case already exists.
						break 2;
					}
				}
				$valid_cases[$case]['date'] = mysql_result( $cases, $i, 'date' );
				$valid_cases[$case]['investigation_title'] = mysql_result( $cases, $i, 'investigation_title' );
				$valid_cases[$case]['description'] = mysql_result( $cases, $i, 'description' );
				$valid_cases[$case]['city'] = mysql_result( $cases, $i, 'city' );
				$valid_cases[$case]['state'] = mysql_result( $cases, $i, 'state' );
				break;
			}
		}
	}

	return $valid_cases;
 }


 // Function: get_case_date( [case_id] )
 // Returns the date of a case given the case_id.
 function get_case_date( $case_id="" )  {

	global $db_connection;

	$date = mysql_get_rows( 'reportsdb_cases', 'date', 'case_id=' . $case_id, NULL, NULL, $db_connection );
	if ( mysql_num_rows($date) > 0 )  {
		return mysql_result( $date, 0, 'date' );
	}
	else  {
		return '0000-00-00';
	}

	return ERR_UNDEF;
 }


 // Function: report_publish( [case_id], [owner_id] )
 // Publish an unpublished report.
 function report_publish( $case_id="", $owner_id="" )  {

	global $db_connection, $USER_PERMS;

	if ( empty($case_id) || empty($owner_id) )  {
		return ERR_UNDEF;
	}
	if ( report_ispublished($case_id, $owner_id) == 0 )  {
		return 0;
	}

	// Check permissions if case is not expired or closed.
	if ( case_isopen($case_id) == 0 && !check_case_expired($case_id) )  {
		if ( case_getowner($case_id) != $_SESSION['user_id'] && $_SESSION['user_perm'] != $USER_PERMS['administrator'] )  {
			if ( report_check_perms('w', $case_id, $owner_id) != 0 )  {
				return ERR_PERM;
			}
		}
	}

	$VALID_FIELDS = array ( 'report_state' => array ( 'value' => "published" ) );
	$return = mysql_db_update( 'reportsdb_reports', $VALID_FIELDS, 'owner_id=' . $owner_id . ' AND case_id=' . $case_id, $db_connection );
	if ( $return != 0 )  {
		do_log( 'Error: report_publish(): unable to publish report - case_id=' . $case_id . ', owner_id=' . $owner_id );
	}

	return $return;
 }



 // Function: report_unpublish( [case_id], [owner_id] )
 // Unpublish a published report.
 function report_unpublish( $case_id="", $owner_id="" )  {

	global $db_connection;

	if ( empty($case_id) || empty($owner_id) )  {
		return ERR_UNDEF;
	}
	if ( report_check_perms('w', $case_id, $owner_id) != 0 )  {
		return ERR_PERM;
	}

	$VALID_FIELDS = array ( 'report_state' => array ( 'value' => "unpublished" ) );
	$return = mysql_db_update( 'reportsdb_reports', $VALID_FIELDS, 'owner_id=' . $owner_id . ' AND case_id=' . $case_id, $db_connection );
	if ( $return != 0 )  {
		do_log( 'Error: report_unpublish(): unable to unpublish report - case_id=' . $case_id . ', owner_id=' . $owner_id );
	}

	return $return;
 }



 // Function: room_isempty( [room_num] )
 // Returns true if room fields are empty.
 function room_isempty( $room_num=0 )  {

	global ${'ROOM_DATA_'.$room_num};

	if ( $room_num == 0 )  {
		return ERR_UNDEF;
	}
	foreach ( array_keys(${'ROOM_DATA_'.$room_num}) as $key )  {
		if ( $key == 'case_id' || $key == 'owner_id' )  {
			continue;
		}
		elseif ( preg_match( '/_units$/', $key ) )  {
			continue;
		}
		if ( !empty(${'ROOM_DATA_'.$room_num}[$key]['value']) )  {
			if ( is_numeric(${'ROOM_DATA_'.$room_num}[$key]['value']) && ${'ROOM_DATA_'.$room_num}[$key]['value'] == 0 )  {
				continue;
			}
			return false;
		}
	}

	return true;
 }



 // Function rooms_shift()
 // We need to shift the room info upwards so that we're always using the lowest numbered room.
 // Similar to anomalies_shift.
 function rooms_shift()  {

	global $REPORTS_ROOM_DATA_VALID_FIELDS, $NUM_ROOMS;

	$empty = array();
	for ( $i=1; $i<$NUM_ROOMS+1; $i++ )  {
		global ${'ROOM_DATA_'.$i};
		if ( room_isempty($i) )  {
			array_push( $empty, $i );
		}
	}
	for ( $i=1; $i<$NUM_ROOMS+1; $i++ )  {
		if ( !room_isempty($i) )  {
			foreach ( array_keys($empty) as $roomkey )  {
				if ( $empty[$roomkey] < $i )  {
					${'ROOM_DATA_'.$empty[$roomkey]} = ${'ROOM_DATA_'.$i};
					foreach ( array_keys($REPORTS_ROOM_DATA_VALID_FIELDS) as $key )  {
						if ( $key != 'case_id' && $key != 'owner_id' )  {
							${'ROOM_DATA_'.$i}[$key]['value'] = $REPORTS_ROOM_DATA_VALID_FIELDS[$key]['value'];
						}
					}
					unset( $empty[$roomkey] );
					break;
				}
				else  {
					break;
				}
			}
		}
	}

	return 0;
 }



 // Function: anomalies_shift( [ROOM_DATA] )
 // Anomalies fields can be [data_type]_1 to [data_type]_9.
 // We want to shift these fields down to the lowest unused number.
 // Takes the ROOM_DATA_* field as an argument.
 function anomalies_shift ( &$ROOM_DATA )  {

	global $NUM_ANOMALIES;
	$anomaly_types = array ( 'tech_anomaly_data_',
				 'anomaly_anomaly_data_',
				 'evp_anomaly_data_',
				 'video_anomaly_data_' );

	foreach ( $anomaly_types as $type )  {
		$empty = array();
		for ( $i=1; $i<$NUM_ANOMALIES+1; $i++ )  {
			if ( empty($ROOM_DATA[$type.$i]['value']) )  {
				array_push( $empty, $i );
			}
		}
		if ( count($empty) == 0 )  {
			continue;
		}

		for ( $i=1; $i<$NUM_ANOMALIES+1; $i++ )  {
			if ( !empty($ROOM_DATA[$type.$i]['value']) )  {
				foreach ( array_keys($empty) as $key )  {
					if ( $empty[$key] < $i )  {
						$ROOM_DATA[$type.$empty[$key]]['value'] = $ROOM_DATA[$type.$i]['value'];
						$ROOM_DATA[$type.$i]['value'] = '';
						unset( $empty[$key] );
						break;
					}
					else  {
						break;
					}
				}
			}
		}
	}

	return 0;
 }






?>
