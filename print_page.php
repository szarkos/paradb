<?php

 //-------------------------------------------------------------------//
 //
 // AGHOST Paranormal Reporting Database (ParaDB)
 // Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>
 //
 // See the file COPYING for terms of use and redistribution.
 //
 // File: print_page.php
 //	  Functions related to printing various pages and templates.
 //
 //-------------------------------------------------------------------//


 // Print the page header.
 function print_header ()  {
	global $VALID_FIELDS;
	global $TITLE;
	include('template/header.inc.php');
	return 0;
 }



 // Print the page footer.
 function print_footer ()  {
	include('template/footer.inc.php');
	return 0;
 }



 // Print the main portion of the page.
 function print_home ()  {
	include('template/home.inc.php');
	return 0;
 }



 // Print the error header.
 function print_error ($err_msg)  {
        if ( empty($err_msg) )  {
                return 1;
        }
//        include('template/error.inc.php');
	print $err_msg . "<br /><br />\n";
        return 0;
 }



 // Function: print_case_view()
 // Print case data.
 function print_case_view()  {
	print_header();
	include('template/case_view.inc.php');
	print_footer();
	return 0;
 }



 // Function: print_case_form()
 // Print the case form, for creating a new case or editing an existing one.
 function print_case_form()  {
	print_header();
	include('template/case_form.inc.php');
	print_footer();
	return 0;
 }



 // Function: print_report_form()
 // Print the report form, for creating a new report or editing an existing one.
 function print_report_form()  {
	print_header();
	include('template/report_form.inc.php');
	print_footer();
	return 0;
 }



 // Function: print_report_view()
 // View a report.
 function print_report_view()  {
	print_header();
	include('template/report_view.inc.php');
	print_footer();
	return 0;
 }


 function print_search_page()  {
	print_header();
	include( 'template/search.inc.php' );
	print_footer();
	return 0;
 }



 function print_stats_page()  {
	print_header();
	include( 'template/stats.inc.php' );
	print_footer();
	return 0;
 }



 function print_user_stats_page()  {
	print_header();
	include( 'template/user_stats.inc.php' );
	print_footer();
	return 0;
 }



 // Function: print_user_mgmt()
 // Print the user management page.
 function print_user_mgmt()  {
	print_header();
	include( 'template/user_mgmt.inc.php' );
	print_footer();
	return 0;
 }



 // Function: set_required_fields()
 // Returns CSS for required fields.
 function set_required_fields( &$VALID_FIELDS, $suffix="" )  {

	$return = "<style type=\"text/css\">\n";
	foreach ( array_keys($VALID_FIELDS) as $key )  {
		if ( !isset($VALID_FIELDS[$key]['value']) || empty($VALID_FIELDS[$key]['value']) )  {
			if ( isset($VALID_FIELDS[$key]['required']) && $VALID_FIELDS[$key]['required'] == 'yes' )  {
				$return .= '#' . $key . $suffix . " { background: #ff9999; }\n";
			}
		}
	}

	$return .= "</style>\n";
	return $return;
 }



 // Function: set_required_room_fields()
 // Returns CSS for required ROOM_DATA_# fields.
 function set_required_room_fields()  {

	global $NUM_ROOMS;

	$return = "<style type=\"text/css\">\n";

	$missing = array();
	for ( $i=1; $i<$NUM_ROOMS+1; $i++ )  {
		global ${'ROOM_DATA_'.$i};
		$not_empty = 0;
		$required = 0;
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
			if ( $not_empty == 1 && $required == 1 )  {
				if ( $not_empty == 1 )  {
					array_push( $missing, $i );
					break;
				}
			}
		}
	}
	foreach ( $missing as $num )  {
		foreach ( array_keys(${'ROOM_DATA_'.$num}) as $key )  {
			if ( $key == 'case_id' || $key == 'owner_id' )  {
				continue;
			}
			if ( empty(${'ROOM_DATA_'.$num}[$key]['value']) && ${'ROOM_DATA_'.$num}[$key]['required'] == 'yes' )  {
				// Missing required data.
				$return .= '#' . $key . '_' . $num . " { background: #ff9999; }\n";
			}
		}
	}

	$return .= "</style>\n";
	return $return;
 }



 // Function: is_printpage()
 // Returns true if page should be displayed in printable view.
 function is_printpage()  {
	if ( isset($_REQUEST['view']) && $_REQUEST['view'] == 'printable' )  {
		return true;
	}
	return false;
 }




?>
