<?php

 //-------------------------------------------------------------------//
 //
 // AGHOST Paranormal Reporting Database (ParaDB)
 // Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>
 //
 // See the file COPYING for terms of use and redistribution.
 //
 // File: reportsdb.php
 //	  This serves as the mainline for the ParaDB Paranormal
 //	  Reporting Database.
 //
 //-------------------------------------------------------------------//


 require('include/reportsdb.inc.php');
 require('login.php');
 require('print_page.php');
 require('db_functions.php');
 require('misc_functions.php');
 require('case.php');
 require('reports.php');
 require('search.php');
 require('stats.php');
 require('stats_plot.php');
 require('template/lang.php');


 // Connect to MySQL server and database.
 $db_connection = mysql_init_connect( $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS );


 // Start session and check if user is logged in.
 if ( init_login() != 0 )  {
	cleanup();
	exit;
 }


 // Strip out slashes.
 foreach ( array_keys($_REQUEST) as $key )  {
	if ( ! get_magic_quotes_gpc() )  {
		if ( $key == 'investigators' )  {
			continue;
		}
		if ( !is_array($_REQUEST[$key]) )  {
			$_REQUEST[$key] = stripslashes( $_REQUEST[$key] );
		}
	}
 }


 // Run the user-specified task.
 if ( isset($_REQUEST['task']) )  {

	if ( $_REQUEST['task'] == 'view_case' || $_REQUEST['task'] == 'edit_case' ||
	     $_REQUEST['task'] == 'save_case' || $_REQUEST['task'] == 'delete_case' )  {
		include('include/case.inc.php');
	}

	if ( $_REQUEST['task'] == 'view_report' || $_REQUEST['task'] == 'edit_report' ||
	     $_REQUEST['task'] == 'save_report' || $_REQUEST['task'] == 'delete_report' )  {
		include('include/report.inc.php');
	}

	if ( $_REQUEST['task'] == 'user_mgmt' || $_REQUEST['task'] == 'add_user' ||
	     $_REQUEST['task'] == 'edit_user' || $_REQUEST['task'] == 'del_user' )  {
		include( 'user_mgmt.php' );
	}

	if ( post_task() != 0 )  {
		print_header();
		print_home();
		print_footer();
		cleanup();
		exit;
	}
 }
 else  {
	// Check permissions and clean output.
	// check_output();

	// Print page.
	print_header();
	print_home();
	print_footer();
 }

 // Clean up.
 cleanup();

 exit;


// End Main
// ------------------------------------------------------------------------//



// Function post_task()
// This function reads the $_POST['task'] variable and implements the
// required task.
function post_task()  {

	global $TASK_REQ_PERMS;

	if ( isset($_REQUEST['task']) )  {


		// View case properties.
		if ( $_REQUEST['task'] == 'view_case' )  {
			if ( view_case($_REQUEST['case_id']) == 0 )  {
				return 0;
			}
			else  {
				return ERR_UNDEF;
			}
		}


		// Create or edit a case.
		elseif ( $_REQUEST['task'] == 'edit_case' )  {
			if ( $_SESSION['user_perm'] < $TASK_REQ_PERMS['edit_case'] )  {
				return ERR_PERM;
			}
			if ( !isset($_REQUEST['case_id']) || $_REQUEST['case_id'] == "" )  {
				set_title( ' -- Create Case', 'a' );
				edit_case('new');  // Create new case.
				return 0;
			}
			else  {  // Existing case.
				if ( check_case_id($_REQUEST['case_id']) != 0 )  {
					return ERR_UNDEF;
				}
				set_title( ' -- Edit Case', 'a' );
				edit_case( $_REQUEST['case_id'] );
			}
			return 0;
		}


		// Save case properties.
		elseif ( $_REQUEST['task'] == 'save_case' )  {
			// Don't allow this function to be done via a GET request.
			if ( !isset($_POST['task']) )  {
				do_log( 'Error: post_task(): Attempted to save case via GET, case_id=' . $_REQUEST['case_id'] );
				return ERR_PERM;
			}
			$status = save_case( $_REQUEST['case_id'] );
			if ( $status == 0 )  {
				if ( case_isopen($_REQUEST['case_id']) == 0 )  {
					if ( case_getowner($_REQUEST['case_id']) == $_SESSION['user_id'] )  {
						edit_case( $_REQUEST['case_id'] );
					}
					else  {
						// Case probably reassigned.
						view_case( $_REQUEST['case_id'] );
					}
				}
				else  {
					view_case( $_REQUEST['case_id'] );
				}
			}
			elseif ( $status == ERR_UNDEF )  {
				view_case( $_REQUEST['case_id'] );
			}
			return 0;
		}


		// Delete a case.
		elseif ( $_REQUEST['task'] == 'delete_case' )  {
			if ( !isset($_REQUEST['case_id']) )  {
				return ERR_UNDEF;
			}

			if ( !isset($_REQUEST['confirm_delete']) || $_REQUEST['confirm_delete'] == 0 )  {
				set_title( ' -- Delete Case', 'a' );
				view_case( $_REQUEST['case_id'] );
				return 0;
			}
			if ( delete_case($_REQUEST['case_id']) != 0 )  {
				set_title( ' -- View Case', 'a' );
				unset( $_REQUEST['confirm_delete'] );
				view_case( $_REQUEST['case_id'] );
				return ERR_UNDEF;
			}
			if ( isset($_REQUEST['confirm_delete']) && $_REQUEST['confirm_delete'] == 1 )  {
				if ( check_case_id($_REQUEST['case_id']) != 0 )  {
					// Case has been deleted, return to home page.
					set_error( CASE_DELETE_SUCCESS );
					print_header();
					print_home();
					print_footer();
				}
				else  {
					set_error( CASE_DELETE_UNSUCCESS );
					return ERR_UNDEF;
				}
			}

			return 0;
		}


		// Close a case.
		elseif ( $_REQUEST['task'] == 'close_case' )  {
			if ( empty($_REQUEST['case_id']) )  {
				set_error('Error: A case_id is required.');
				return ERR_UNDEF;
			}
			if ( close_case($_REQUEST['case_id']) != 0 )  {
				set_error( CASE_CLOSE_UNSUCCESS );
				return ERR_UNDEF;
			}
			set_error( CASE_CLOSE_SUCCESS );
			print_header();
			print_home();
			print_footer();
			return 0;
		}


		// Re-open a case.
		elseif ( $_REQUEST['task'] == 'open_case' )  {
			if ( empty($_REQUEST['case_id']) )  {
				set_error('Error: A case_id is required.');
				return ERR_UNDEF;
			}
			if ( open_case($_REQUEST['case_id']) != 0 )  {
				set_error( CASE_OPEN_UNSUCCESS );
				return ERR_UNDEF;
			}
			set_error( CASE_OPEN_SUCCESS );
			print_header();
			print_home();
			print_footer();
			return 0;
		}


		// View a report.
		elseif ( $_REQUEST['task'] == 'view_report'  )  {
			if ( empty($_REQUEST['case_id']) || empty($_REQUEST['owner_id']) )  {
				return ERR_UNDEF;
			}

			set_title( ' -- View Report', 'a' );
			if ( view_report($_REQUEST['case_id'], $_REQUEST['owner_id']) != 0 )  {
				// ERROR
				return ERR_UNDEF;
			}
			return 0;
		}


		// Create or edit a report.
		elseif ( $_REQUEST['task'] == 'edit_report'  )  {
			if ( !isset($_REQUEST['owner_id']) || empty($_REQUEST['owner_id']) )  {
				$_REQUEST['owner_id'] = $_SESSION['user_id'];
			}

			if ( report_exists($_REQUEST['case_id'], $_REQUEST['owner_id']) == 0 )  {
				// Existing report.
				set_title( ' -- Edit Report', 'a' );
				if ( edit_report($_REQUEST['case_id'], $_REQUEST['owner_id']) != 0 )  {
					return ERR_UNDEF;
				}
			}
			else  {
				// Possibly a new report.
				set_title( ' -- Create Report', 'a' );
				if ( isset($_REQUEST['case_id']) && !empty($_REQUEST['case_id']) )  {
					global $REPORTS_VALID_FIELDS;
					$REPORTS_VALID_FIELDS['case_id']['value'] = $_REQUEST['case_id'];
				}
//				$_REQUEST['case_id'] = 'new';
				if ( edit_report( 'new', $_SESSION['user_id']) != 0 )  {
					return ERR_UNDEF;
				}
			}

			return 0;
		}


		// Save a report.
		elseif ( $_REQUEST['task'] == 'save_report' )  {
			if ( empty($_REQUEST['case_id']) )  {
				return ERR_UNDEF;
			}

			// Don't allow this function to be done via a GET request.
			if ( !isset($_POST['task']) )  {
				do_log( 'Error: post_task(): Attempted to save report via GET, case_id=' . $_REQUEST['case_id'] );
				return ERR_PERM;
			}
			if ( !isset($_REQUEST['owner_id']) || empty($_REQUEST['owner_id']) )  {
				// Possibly a new report, set owner_id.
				$_REQUEST['owner_id'] = $_SESSION['user_id'];
			}
			if ( save_report($_REQUEST['case_id'], $_REQUEST['owner_id']) == 0 )  {
				edit_report( $_REQUEST['case_id'], $_REQUEST['owner_id'] );
			}

			return 0;
		}


		// Delete a report.
		elseif ( $_REQUEST['task'] == 'delete_report' )  {
			if ( empty($_REQUEST['case_id']) || empty($_REQUEST['owner_id']) )  {
				return ERR_UNDEF;
			}

			set_title( ' -- Delete Report', 'a' );
			if ( !isset($_REQUEST['confirm_delete']) || $_REQUEST['confirm_delete'] == 0 )  {
				$_REQUEST['confirm_delete'] = 0;
				view_report( $_REQUEST['case_id'], $_REQUEST['owner_id'] );
				return 0;
			}
			if ( delete_report($_REQUEST['case_id'], $_REQUEST['owner_id']) != 0 )  {
				unset( $_REQUEST['confirm_delete'] );
				view_report( $_REQUEST['case_id'], $_REQUEST['owner_id'] );
				return ERR_UNDEF;
			}
			if ( isset($_REQUEST['confirm_delete']) && $_REQUEST['confirm_delete'] == 1 )  {
				if ( report_exists($_REQUEST['case_id'], $_REQUEST['owner_id']) != 0 )  {
					// Report has been deleted, return to home page.
					set_error( REPORT_DELETE_SUCCESS );
					print_header();
					print_home();
					print_footer();
				}
				else  {
					set_error( REPORT_DELETE_UNSUCCESS );
					return ERR_UNDEF;
				}
			}

			return 0;
		}


		// Search.
		elseif ( $_REQUEST['task'] == 'search' )  {
			global $SEARCH, $SEARCH_RESULTS;
			foreach ( array_keys($SEARCH) as $key )  {
				if ( isset($_REQUEST[$key]) )  {
					$_REQUEST[$key] = clean_field( $_REQUEST[$key] );
					if ( $SEARCH[$key]['maxlength'] != NULL && $SEARCH[$key]['maxlength'] != 0 )  {
						$_REQUEST[$key] = check_length( $_REQUEST[$key], $SEARCH[$key]['maxlength'] );
					}
					$SEARCH[$key]['value'] = $_REQUEST[$key];
				}
                        }
			if ( !is_numeric($SEARCH['last_search']['value']) )  {
				$SEARCH['last_search']['value'] = 0;
			}

			$SEARCH_RESULTS = array();
			$SEARCH_RESULTS = search_all( $SEARCH['search_query']['value'] );
			print_search_page();

			return 0;
		}


		// Show user stats.
		elseif ( $_REQUEST['task'] == 'showstats' )  {
			if ( isset($_REQUEST['user_id']) )  {
				print_user_stats( $_REQUEST['user_id'] );
			}
			else  {
				print_stats_page();
			}
		}


		// Close all expired cases.  This can be called periodically to check for and close expired cases.
		elseif ( $_REQUEST['task'] == 'close_expired' )  {
			close_exp_cases();
			return 1;  // Print page and clean up.
		}


		// Publish a report.
		elseif ( $_REQUEST['task'] == 'report_publish' )  {
			if ( report_publish($_REQUEST['case_id'], $_SESSION['user_id']) != 0 )  {
				set_error( REPORT_PUBLISH_UNSUCCESS );
				return ERR_UNDEF;
			}
			set_error( REPORT_PUBLISH_SUCCESS );
			print_header();
			print_home();
			print_footer();
			return 0;
		}


		// Un-publish a report.
		elseif ( $_REQUEST['task'] == 'report_unpublish' )  {
			if ( report_unpublish($_REQUEST['case_id'], $_SESSION['user_id']) != 0 )  {
				set_error( REPORT_UNPUBLISH_UNSUCCESS );
				return ERR_UNDEF;
			}
			set_error( REPORT_UNPUBLISH_SUCCESS );
			print_header();
			print_home();
			print_footer();
			return 0;
		}


		// Listing reports and cases.
		elseif ( $_REQUEST['task'] == 'reports_view_all' ||	// View all reports owned by owner_id.
			 $_REQUEST['task'] == 'cases_view' ||		// View all cases owner_id has been on.
			 $_REQUEST['task'] == 'cases_view_all' ||	// View all cases.
			 $_REQUEST['task'] == 'mycases_view' )  {	// View all cases owned by owner_id.

			if ( empty($_REQUEST['owner_id']) && $_REQUEST['task'] != 'cases_view_all' )  {
				return ERR_UNDEF;
			}
			print_user_stats_page();
			return 0;
		}


		// Manage users.
		elseif ( $_REQUEST['task'] == 'user_mgmt' )  {
			set_title( ' -- Manage Users', 'a' );
			if ( isset($_REQUEST['save_users']) && $_REQUEST['save_users'] == 1 )  {
				user_mgmt_save();
			}
			print_user_mgmt();
			return 0;
		}


		// Add or edit user information.
		elseif ( $_REQUEST['task'] == 'add_user' || $_REQUEST['task'] == 'edit_user' )  {
			if ( $_REQUEST['task'] == 'add_user' )  {
				set_title( ' -- Add User', 'a' );
			}
			elseif ( $_REQUEST['task'] == 'edit_user' )  {
				set_title( ' -- Edit User', 'a' );
			}
			if ( isset($_REQUEST['user_save']) && $_REQUEST['user_save'] == 1 )  {
				if ( update_user() == 0 )  {
					global $USER_FIELDS, $LOGIN_DB_NAME_COL, $LOGIN_DB_USER_COL;
					if ( $_REQUEST['task'] == 'add_user' )  {
						set_error( 'Successfully added user ' . $USER_FIELDS[$LOGIN_DB_NAME_COL]['value'] . " (" . $USER_FIELDS[$LOGIN_DB_USER_COL]['value'] . ")." );
					}
					elseif ( $_REQUEST['task'] == 'edit_user' )  {
						set_error( 'Successfully saved user ' . $USER_FIELDS[$LOGIN_DB_NAME_COL]['value'] . " (" . $USER_FIELDS[$LOGIN_DB_USER_COL]['value'] . ")." );
					}
					$_REQUEST['task'] = 'user_mgmt';
				}
			}
			print_user_mgmt();
			return 0;
		}


		// Delete a user.
		elseif ( $_REQUEST['task'] == 'del_user' )  {
			global $LOGIN_DB_USER_COL;
			if ( isset($_REQUEST[$LOGIN_DB_USER_COL]) && !empty($_REQUEST[$LOGIN_DB_USER_COL]) )  {
				if ( delete_user($_REQUEST[$LOGIN_DB_USER_COL]) == 0 )  {
					if ( isset($_REQUEST['confirm_delete']) && $_REQUEST['confirm_delete'] == 1 )  {
						$_REQUEST['task'] = 'user_mgmt';
					}
				}
				else  {
					$_REQUEST['task'] = 'user_mgmt';
				}
			}
			print_user_mgmt();
		}

		// Print the current RSS feed.
		elseif ( $_REQUEST['task'] == 'print_rss' )  {
			include( 'rss.php' );
			print_rss_feed();
			return 0;
		}

		// Print all the case info.
		elseif ( $_REQUEST['task'] == 'view_caseinfo' )  {
			if ( isset($_REQUEST['case_id']) && !empty($_REQUEST['case_id']) )  {
				print_caseinfo( $_REQUEST['case_id'] );
			}
			else  {
				print_caseinfo();
			}
			return 0;
		}

		else  {
			return ERR_UNDEF;
		}
	}

	return 0;
}




?>
