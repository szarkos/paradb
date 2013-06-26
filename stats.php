<?php

 //-------------------------------------------------------------------//
 //
 // AGHOST Paranormal Reporting Database (ParaDB)
 // Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>
 //
 // See the file COPYING for terms of use and redistribution.
 //
 // File: stats.php
 //	  Generate statistics and user scorecard.
 //
 //-------------------------------------------------------------------//


 require( 'stats_print.php' );


 // Notes:
 // Cases occuring during x-ray activity.
 // Cases occuring during geomagnetic storm.
 // Other ideas:
 //	Average investigation length (time, taken from reports).
 //	Walkthroughs might throw this off.


 // Function: get_user_stats( [user_id] )  {
 // Returns an array with the user's ID, name, username, number of cases associated with the user and
 // number of reports the user has started.
 // Array ( [USERNAME]	=>	Array (	[id]			=>	nn,
 //					[name]			=>	"REAL NAME",
 //					[email]			=>	"EMAIL ADDRESS",
 //					[reports_db_perm]	=>	nn,
 //					[cases]			=>	nn,
 //					[reports]		=>	nn,
 //					[data]			=>	nn )
 function get_user_stats( $user_id="" )  {

	global $db_connection;

	if ( empty($user_id) )  {
		$users = listusers();
	}
	else  {
		$users = get_userinfo( $user_id );
	}

	// Pull case_ids and investigators.
	$cases = array();
	$data_submitted = array();
	$result = mysql_get_rows( 'reportsdb_cases', 'case_id,investigators,data_submitted', NULL, NULL, NULL, $db_connection );
	while ( $row = mysql_fetch_row($result) )  {
		$cases[$row[0]] = explode( ',', $row[1] );
		$data_submitted[$row[0]] = array();
		$data = explode( ',', $row[2] );
		foreach ( $data as $key )  {
			$tmp = explode( '|', $key );
			if ( isset($tmp[1]) && !empty($tmp[1]) )  {
				array_push( $data_submitted[$row[0]], $tmp[0] );
			}
		}
	}

	if ( !empty($user_id) )  {
		// Convert the array listing from the get_userinfo() type to the listusers() type.
		$users = array ( $users['username']	=>	array (	'id'			=>	$users['id'],
									'name'			=>	$users['name'],
									'email'			=>	$users['email'],
									'reports_db_perm'	=>	$users['reports_db_perm'] ) );
	}

	foreach ( array_keys($users) as $user )  {

		// [cases]
		$users[$user]['cases'] = 0;
		$users[$user]['data'] = 0;
		foreach ( array_keys($cases) as $case )  {
			if ( in_array($users[$user]['id'], $cases[$case]) )  {
				// User is on the case...
				$users[$user]['cases']++;
			}

			// [data]
			if ( in_array($users[$user]['id'], $data_submitted[$case]) )  {
				$users[$user]['data']++;
			}
		}

		// [reports]
		$result = mysql_get_rows( 'reportsdb_reports', 'owner_id', 'owner_id=' . $users[$user]['id'], NULL, NULL, $db_connection );
		$users[$user]['reports'] = mysql_num_rows( $result );
	}

	return $users;
 }



 // Function: get_num_users()
 // Return an array with user statistics.
 // $users = array( 'total_users'	=>	nn,
 //		    'team-lead'		=>	nn,
 //		    'administrator'	=>	nn );
 function get_num_users()  {

	global $LOGIN_DB_HOST, $LOGIN_DB_NAME, $LOGIN_DB_USER, $LOGIN_DB_PASS;
	global $LOGIN_DB_TABLE, $LOGIN_DB_ID_COL, $LOGIN_DB_PERM_COL;
	global $USER_PERMS;

	$users = array( 'total_users'	=>	0,
			'team-lead'	=>	0,
			'administrator'	=>	0 );

	$login_db_connection = mysql_init_connect( $LOGIN_DB_HOST, $LOGIN_DB_NAME, $LOGIN_DB_USER, $LOGIN_DB_PASS );
	$result = mysql_get_rows( $LOGIN_DB_TABLE, $LOGIN_DB_ID_COL . ',' . $LOGIN_DB_PERM_COL, NULL, NULL, NULL, $login_db_connection );
	$users['total_users'] = mysql_num_rows( $result );
	while ( $row = mysql_fetch_row($result) )  {
		if ( $row[1] == $USER_PERMS['team-lead'] )  {
			$users['team-lead']++;
		}
		elseif ( $row[1] == $USER_PERMS['administrator'] )  {
			$users['administrator']++;
		}
	}
//	mysql_close( $login_db_connection );

	return $users;
 }



 // Function: get_num_cases()
 // Returns an array with case information.
 // $cases = array ( 'total_cases'	=>	nn,
 //		     'open'		=>	nn,
 //		     'closed'		=>	nn );
 function get_num_cases()  {

	global $db_connection;

	$cases = array ( 'total_cases'	=>	0,
			 'open'		=>	0,
			 'closed'	=>	0 );

	$result = mysql_get_rows( 'reportsdb_cases', 'case_open', NULL, NULL, NULL, $db_connection );
	$cases['total_cases'] = mysql_num_rows( $result );
	while ( $row = mysql_fetch_row($result) )  {
		if ( $row[0] == 'open' )  {
			$cases['open']++;
		}
		elseif ( $row[0] == 'closed' )  {
			$cases['closed']++;
		}
	}

	return $cases;
 }



 // Function: get_num_reports()
 // Returns an array with case information.
 // $reports = array (  'total_reports'	=>	nn,
 //			'published'	=>	nn,
 //			'unpublished'	=>	nn );
 function get_num_reports()  {

	global $db_connection;

	$reports = array ( 'total_reports'	=>	0,
			   'published'		=>	0,
			   'unpublished'	=>	0 );

	$result = mysql_get_rows( 'reportsdb_reports', 'report_state', NULL, NULL, NULL, $db_connection );
	$reports['total_reports'] = mysql_num_rows( $result );
	while ( $row = mysql_fetch_row($result) )  {
		if ( $row[0] == 'published' )  {
			$reports['published']++;
		}
		elseif ( $row[0] == 'unpublished' )  {
			$reports['unpublished']++;
		}
	}

	return $reports;
 }



 // Function: geomag_stats()
 // Output array containing geomagnetic statistics.
 // The status elements are not initialized ahead of time. It's easier this way,
 // if there are no cases then we can just return an empty array.
 function geomag_stats()  {

	global $db_connection;

	$geomag_stats = array(  'Quiet'		=>	0,
				'Unsettled'	=>	0,
				'G1 Storm'	=>	0,
				'G2 Storm'	=>	0,
				'>G2 Storm'	=>	0 );

	$data = mysql_get_rows( 'reportsdb_reports', 'geomag_summary', NULL, NULL, NULL, $db_connection );
	if ( mysql_num_rows($data) == 0 )  {
		return $geomag_stats;
	}

	while ( $row = mysql_fetch_row($data) )  {
		if ( $row[0] == 'Very Quiet' || $row[0] == 'Quiet' )  {
			$geomag_stats['Quiet']++;
		}
		elseif ( $row[0] == 'Semi-Quiet' || $row[0] == 'Unsettled' )  {
			$geomag_stats['Unsettled']++;
		}
		elseif ( $row[0] == 'G1 Storm' )  {
			$geomag_stats['G1 Storm']++;
		}
		elseif ( $row[0] == 'G2 Storm' )  {
			$geomag_stats['G2 Storm']++;
		}
		else  {
			if ( preg_match('/^G[345]\s/', $row[0]) )  {
				$geomag_stats['>G2 Storm']++;
			}
		}
	}

	return $geomag_stats;
 }



 // Function: xray_stats()
 // Output array containing xray statistics.
 // The status elements are not initialized ahead of time. It's easier this way,
 // if there are no cases then we can just return and empty array.
 function xray_stats()  {

	global $db_connection;

	$xray_stats = array(	'>X30 Class (R5)'		=>	0,
				'X20 Class (R5)'		=>	0,
				'X10 Class (R4)'		=>	0,
				'X Class (R3)'			=>	0,
				'M5 Class (R2)'			=>	0,
				'M Class (R1)'			=>	0,
				'C Class (Active)'		=>	0,
				'B Class Flare (Normal)'	=>	0,
				'<B Class (Normal)'		=>	0 );

	$data = mysql_get_rows( 'reportsdb_reports', 'xray_summary', NULL, NULL, NULL, $db_connection );
	if ( mysql_num_rows($data) == 0 )  {
		return $xray_stats;
	}

	while ( $row = mysql_fetch_row($data) )  {
		if ( $row[0] == '>X30 Class (R5)' )  {
			$xray_stats['>X30 Class (R5)']++;
		}
		elseif ( $row[0] == 'X20 Class (R5)' )  {
			$xray_stats['X20 Class (R5)']++;
		}
		elseif ( $row[0] == 'X10 Class (R4)' )  {
			$xray_stats['X10 Class (R4)']++;
		}
		elseif ( $row[0] == 'X Class (R3)' )  {
			$xray_stats['X Class (R3)']++;
		}
		elseif ( $row[0] == 'M5 Class (R2)' )  {
			$xray_stats['M5 Class (R2)']++;
		}
		elseif ( $row[0] == 'M Class (R1)' )  {
			$xray_stats['M Class (R1)']++;
		}
		elseif ( $row[0] == 'C Class (Active)' )  {
			$xray_stats['C Class (Active)']++;
		}
		elseif ( $row[0] == 'B Class Flare (Normal)' )  {
			$xray_stats['B Class Flare (Normal)']++;
		}
		elseif ( $row[0] == '<B Class (Normal)' )  {
			$xray_stats['<B Class (Normal)']++;
		}
	}

	return $xray_stats;
 }





?>
