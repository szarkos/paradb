<?php

 //-------------------------------------------------------------------//
 //
 // AGHOST Paranormal Reporting Database (ParaDB)
 // Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>
 //
 // See the file COPYING for terms of use and redistribution.
 //
 // File: search.php
 //	  Functions to search cases and reports.
 //
 //-------------------------------------------------------------------//


 // Function: print_search_results()
 // Collect the search data and print it.
 function print_search_results()  {

	global $SEARCH;
	global $SEARCH_LIMIT;
	global $SEARCH_RESULTS;
	global $REPORTSDB;

	if ( empty($SEARCH_RESULTS) )  {
		print "<div align=\"center\"><h3> " . SEARCH_NO_RESULTS . " </h3></div>\n";
		return 0;
	}

	print "<h3> " . SEARCH_RESULTS;
	if ( $SEARCH['last_search']['value'] == 0 )  {
		print " 1-" . $SEARCH['next_search']['value'];
	}
	else  {
		print $SEARCH['last_search']['value'] . "-" . $SEARCH['next_search']['value'];
	}
	print " of " . $SEARCH['num_results']['value'] . " </h3>\n";

	foreach ( array_keys($SEARCH_RESULTS) as $key )  {
		print "<div class=\"search_results\">\n";
		if ( $SEARCH_RESULTS[$key]['owner_id'] == "" )  {
			print "<strong> " . SEARCH_CASE . ": </strong> <a href=\"" . $REPORTSDB . "?task=view_case&case_id=" . $SEARCH_RESULTS[$key]['case_id'] . "\">" . $SEARCH_RESULTS[$key]['case_id'] . "</a> <br />\n";

			if ( $SEARCH_RESULTS[$key]['city'] != "" && $SEARCH_RESULTS[$key]['state'] != "" )  {
				print $SEARCH_RESULTS[$key]['city'] . ", " . $SEARCH_RESULTS[$key]['state'];
			}
			elseif ( $SEARCH_RESULTS[$key]['city'] != "" )  {
				print $SEARCH_RESULTS[$key]['city'];
			}
			elseif ( $SEARCH_RESULTS[$key]['state'] != "" )  {
				print $SEARCH_RESULTS[$key]['state'];
			}
			if ( $SEARCH_RESULTS[$key]['country'] != "" )  {
				print " (" . $SEARCH_RESULTS[$key]['country'] . ") <br />\n";
			}
		}
		else  {
			print "<strong> " . SEARCH_REPORT . ": </strong> <a href=\"" . $REPORTSDB . "?task=view_report&case_id=" . $SEARCH_RESULTS[$key]['case_id'] . "&owner_id=" . $SEARCH_RESULTS[$key]['owner_id'] . "\">" . $SEARCH_RESULTS[$key]['case_title'] . "</a> ";
			print " (<a href=\"" . $REPORTSDB . "?task=view_case&case_id=" . $SEARCH_RESULTS[$key]['case_id'] . "\">" . $SEARCH_RESULTS[$key]['case_id'] . "</a>) <br />\n";
		}
		if ( strlen($SEARCH_RESULTS[$key]['description']) > 250 )  {
			$SEARCH_RESULTS[$key]['description'] = substr( $SEARCH_RESULTS[$key]['description'], 0, 250 );
			$SEARCH_RESULTS[$key]['description'] .= '...';
		}
		print $SEARCH_RESULTS[$key]['description'] . "\n";
		print "</div>\n";
	}

	return 0;
 }



 // Function: search_all( [query] )
 // Calls other search functions and organizes the results.
 function search_all( $query="" )  {

	global $db_connection;
	global $SEARCH_LIMIT;
	global $SEARCH;
	
	if ( $query == "" )  {
		return array();
	}

	$results = array();
	$ret_results = array();
	$cases = search_cases( $query );
	$reports = search_reports( $query );

	if ( is_array($cases) )  {
		$results = array_merge( $results, $cases );
	}
	if ( is_array($reports) )  {
		$results = array_merge( $results, $reports );
	}
	sort( $results );

	$SEARCH['num_results']['value'] = $count = count( $results );
	$i = $SEARCH['last_search']['value'];
	if ( $count > $SEARCH_LIMIT )  {
		if ( ($SEARCH['last_search']['value'] + $SEARCH_LIMIT) < $count )  {
			$SEARCH['next_search']['value'] = $count = $SEARCH['last_search']['value'] + $SEARCH_LIMIT;
		}
		else  {
			$SEARCH['next_search']['value'] = $SEARCH['num_results']['value'];
		}
	}
	else  {
		$SEARCH['next_search']['value'] = $SEARCH['num_results']['value'];
	}

	for ( $i; $i<$count; $i++ )  {
		$tmp = explode( '|', $results[$i] );
		$ret_results[$i]['date'] = $tmp[0];
		$ret_results[$i]['case_id'] = $tmp[1];
		if ( $tmp[2] == "" )  {
			// This is a case, not a report.
			$ret_results[$i]['owner_id'] = "";
			$data = mysql_get_rows( 'reportsdb_cases', 'city,state,country,description', 'case_id=' . $tmp[1], NULL, NULL, $db_connection );
			$ret_results[$i]['city'] = mysql_result( $data, 0, 'city' );
			$ret_results[$i]['state'] = mysql_result( $data, 0, 'state' );
			$ret_results[$i]['country'] = mysql_result( $data, 0, 'country' );
			$ret_results[$i]['description'] = mysql_result( $data, 0, 'description' );
		}
		else  {
			$ret_results[$i]['owner_id'] = $tmp[2];
			$data = mysql_get_rows( 'reportsdb_reports', 'case_title,description', 'case_id=' . $tmp[1] . ' AND owner_id=' . $tmp[2], NULL, NULL, $db_connection );
			$ret_results[$i]['case_title'] = mysql_result( $data, 0, 'case_title' );
			$ret_results[$i]['description'] = mysql_result( $data, 0, 'description' );
		}
	}

	return $ret_results;
 }



 // Function: search_cases( [query], [limit] )
 // Search through the case-related fields and return a list of cases dates and case_ids.
 function search_cases( $query="", $limit=NULL )  {

	global $db_connection;

	if ( $query == "" )  {
		return array();
	}
	else  {
		$query = mysql_safe_string( trim($query), $db_connection );
	}

	$case_ids = array();
	$reportsdb_cases = array ( 'case_id', 'address', 'city', 'state', 'country', 'description' );

	// reportsdb_cases table.
	$where = '';
	foreach ( $reportsdb_cases as $key )  {
		if ( $where != "" )  {
			$where .= ' OR ';
		}
		$where .= $key . ' LIKE \'%' . $query . '%\'';
	}
	$result = mysql_get_rows_safe( 'reportsdb_cases', 'date,case_id', $where, 'date', $limit, $db_connection );
	if ( $result )  {
		while ( $row = mysql_fetch_row($result) )  {
			array_push( $case_ids, trim($row[0]) . '|' . trim($row[1]) . '|' );
		}
	}

	return $case_ids;
 }



 // Function search_reports( [query], [limit] )
 // Search through the report-related fields and return a list of reports dates, case_ids and owner_ids.
 function search_reports( $query="", $limit=NULL )  {

	global $db_connection;
	global $NUM_ROOMS;

	if ( $query == "" )  {
		return array();
	}
	else  {
		$query = mysql_safe_string( trim($query), $db_connection );
	}

	$case_ids = array();
	$reportsdb_reports = array ( 'case_id', 'case_title', 'description' );
	$reportsdb_reports_impressions = array ( 'case_id', 'outside_impr', 'walkin_impr', 'closing_impr' );
	$reportsdb_room_data = array ( 'case_id', 'room_id', 'notes' );

	// reportsdb_reports table.
	$where = '';
	foreach ( $reportsdb_reports as $key )  {
		if ( $where != "" )  {
			$where .= ' OR ';
		}
		$where .= $key . ' LIKE \'%' . $query . '%\'';
	}
	$result = mysql_get_rows_safe( 'reportsdb_reports', 'date,case_id,owner_id', $where, 'date', $limit, $db_connection );
	if ( $result )  {
		while ( $row = mysql_fetch_row($result) )  {
			array_push( $case_ids, trim($row[0]) . '|' . trim($row[1]) . '|' . trim($row[2]) );
		}
	}

	// reportsdb_reports_impressions table.
	$where = '';
	foreach ( $reportsdb_reports_impressions as $key )  {
		if ( $where != "" )  {
			$where .= ' OR ';
		}
		$where .= $key . ' LIKE \'%' . $query . '%\'';
	}
	$result = mysql_get_rows_safe( 'reportsdb_reports_impressions', 'case_id,owner_id', $where, NULL, NULL, $db_connection );
	if ( $result > 0 )  {
		while ( $row = mysql_fetch_row($result) )  {
			$date = get_report_date( $row[0], $row[1] );
			if ( $date == ERR_UNDEF )  {
				$date = "";
			}
			array_push( $case_ids, $date . '|' . trim($row[0]) . '|' . trim($row[1]) );
		}
	}


	// reportsdb_room_data_# tables.
	$where = '';
	foreach ( $reportsdb_room_data as $key )  {
		if ( $where != "" )  {
			$where .= ' OR ';
		}
		$where .= $key . ' LIKE \'%' . $query . '%\'';
	}
	for ( $i=1; $i<$NUM_ROOMS+1; $i++ )  {
		$result = mysql_get_rows_safe( 'reportsdb_room_data_'.$i, 'case_id,owner_id', $where, NULL, NULL, $db_connection );
	}
	if ( $result )  {
		while ( $row = mysql_fetch_row($result) )  {
			$date = get_report_date( $row[0], $row[1] );
			if ( $date == ERR_UNDEF )  {
				$date = "";
			}
			array_push( $case_ids, $date . '|' . trim($row[0]) . '|' . trim($row[1]) );
		}
	}

	return array_unique( $case_ids );
 }



 // Function: get_report_date( [case_id], [owner_id] )
 // Returns the date of the report.
 function get_report_date( $case_id="", $owner_id="" )  {

	global $db_connection;

	if ( $case_id == "" || $owner_id == "" )  {
		return ERR_UNDEF;
	}
	$date = mysql_get_rows( 'reportsdb_reports', 'date', 'case_id=' . $case_id . ' AND owner_id=' . $owner_id, NULL, NULL, $db_connection );
	if ( !$date )  {
		return ERR_UNDEF;
	}

	return mysql_result( $date, 0, 'date' );
 }




?>
