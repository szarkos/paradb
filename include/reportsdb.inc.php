<?php

 //-------------------------------------------------------------------//
 //
 // AGHOST Paranormal Reporting Database (ParaDB)
 // Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>
 //
 // See the file COPYING for terms of use and redistribution.
 //
 // File: reportsdb.inc.php
 //
 //-------------------------------------------------------------------//


 // ParaDB Database Information.
 $DB_HOST		= "localhost";	// Database hostname.
 $DB_NAME		= "paradb";	// Database name.
 $DB_USER		= "paradb";	// Database username - should have read/write access to the table
					//  defined in $DB_TABLE.
 $DB_PASS		= "a";	// Database password.


 // Login Database Information.
 $LOGIN_DB_HOST		= $DB_HOST;		// Login database hostname.
 $LOGIN_DB_NAME		= $DB_NAME;		// Login database name.
 $LOGIN_DB_USER		= $DB_USER;		// Login database username - only needs read-only access here.
 $LOGIN_DB_PASS		= $DB_PASS;		// Login database password.
 $LOGIN_DB_TABLE	= "reportsdb_users";	// Login table name.
 $LOGIN_DB_ID_COL	= "id";			// Name of the column where the user ID is stored.
 $LOGIN_DB_NAME_COL	= "name";		// Name of the column where the user's full name is stored.
 $LOGIN_DB_USER_COL	= "username";		// Name of the column where the username is stored.
 $LOGIN_DB_EMAIL_COL	= "email";		// Name of the column where the user's email is stored.
 $LOGIN_DB_PASS_COL	= "password";		// Name of the column where the password is stored.
 $LOGIN_DB_PERM_COL	= "reports_db_perm";	// Name of the column where the user permissions are kept.
 $LOGIN_DB_BLOCK_COL	= "block";		// Name of the column that tells us if user should be blocked.


 // ReportsDB log file. If you want a log file, then this file must be writable by
 // the user the HTTP daemon runs as.  This is not required, but can be useful for debugging.
 $LOGFILE		= 'reportsdb.log';
 $PRINT_ERR		= 0;			// Set to '1' to also print errors to the screen.


 // Email Configuration
 $ADMIN_EMAIL		= 'root@localhost';
 $MAIL_FROM		= 'root@localhost';

 // Page Title
 $TITLE			= "ParaDB - Paranormal Reporting Database";

 // Base URLs and Paths
 $baseurl = "http://localhost/paradb";		// Required: The external URL used to access the reportsdb directory.
 $reportsdb = "reportsdb.php";			// Required: You should not need to ever change this.
 $server_path = getcwd();			// Optional: getcwd() should work, but you may also hardcode this.
 $REPORTSDB = $baseurl . "/" . $reportsdb;


 // Case Variables
 $USER_ALLOWED_CASE_THRESHOLD = 3;	// Case vs. Reports threshold.  If (num_cases - num_reports) is greater
					// than this number, then the case manager will receive a warning before
					// they are allowed to add the user to any case.

 // Modules
 $ENABLE_MOONPHASE = 1;		// Set this to 1 to enable the calculation of moon phase information.
 $ENABLE_GEO_XRAY = 1;		// Set this to 1 to enable the retrieval of Geomagnetic and X-Ray information.
				// This function relies on the Space Weather Archive on AGHOSTOnline.org.
 $ENABLE_UPLOAD = 1;            //Uploads enabled or not.  Not working right now though, its always on.
 // RSS Options
 $RSS_FILE = $server_path . '/rss.xml';

 // Other Variables
 $HOME_LIST_LIMIT = 10;		// Number of reports/cases to display on the home page columns.
 $SEARCH_LIMIT = 15;		// Number of search results to display per page.

 // System-wide alert message.  Use a null string for no alerts.  HTML ok here.  Maybe even Javascript.  Haven't tested it though
 $SYS_ALERTS = "";

 // Misc.
 $cookie_timeout = 0;	// Session cookie timeout in seconds (Default: 0).



///////////////////////////////////////////////////////////////////////////////////////////////
// Mostly Internal Variables - You should probably not edit these.
///////////////////////////////////////////////////////////////////////////////////////////////

 // Misc. Global Variables.
 // Arbitrarily editing these values will break things. Changing values will often
 // require a change to the database as well.
 $db_connection			= "";
 $login_db_connection		= "";
 $ERR_MSG			= "";
 $NUM_ROOMS			= 30;	// Also edit in addRoominput(), template/js/report.js
 $NUM_INVESTIGATORS		= 25;	// Maximum number of investigators that can be assigned to a case.
 $NUM_ANOMALIES			= 9;	// Maximum number of anomalies per room.
 $PRINTABLE_WIDTH		= 800;	// Width for the printable reports.
 $CASE_VIEW_NOTES_IN_PRINTPAGE	= 0;
 $USER_MANAGEMENT		= 1;

 // User permissions.
 $USER_PERMS = array (	'administrator'		=>	4,
			'team-lead'		=>	2,
			'registered'		=>	0
 );

 // This hash is used to check permissions required for certain tasks.
 $TASK_REQ_PERMS = array ( 'edit_case'		=>	$USER_PERMS['team-lead'],
			   'delete_case'	=>	$USER_PERMS['team-lead'],
			   'save_case'		=>	$USER_PERMS['team-lead'],
			   'edit_report'	=>	$USER_PERMS['registered'],
			   'delete_report'	=>	$USER_PERMS['registered'],
			   'save_report'	=>	$USER_PERMS['registered'],
			   'user_mgmt'		=>	$USER_PERMS['administrator']
 );

 $SEARCH_RESULTS = array();
 $SEARCH = array (	'search_query'		=>	array ( 'value'		=>	"",
								'maxlength'	=>	25 ),

			'last_search'		=>	array ( 'value'		=>	0,
								'maxlength'	=>	4 ),

			'next_search'		=>	array ( 'value'		=>	0,
								'maxlength'	=>	4 ),

			'num_results'		=>	array ( 'value'		=>	0,
								'maxlength'	=>	4 ),
 );


 // Constants
 DEFINE( "REPORTS_DB_NAME", "ParaDB -- Paranormal Reporting Database" );
 DEFINE( "REPORTS_DB_NAME_LINK", "<a href=\"http://www.paradb.org/\" style=\"text-decoration:none\">ParaDB -- Paranormal Reporting Database</a>" );
 DEFINE( "REPORTS_DB_VERSION", "0.2.0" );
 DEFINE( "DEV_NAME", "Stephen A. Zarkos" );
 DEFINE( "DEV_EMAIL", "obsid@sentry.net" );
 DEFINE( "ERR_UNDEF", -1 );
 DEFINE( "ERR_PERM", -2 );
 DEFINE( "ERR_FRETR", -4 );
 DEFINE( "ERR_FOPEN", -8 );
 DEFINE( "ERR_FWRITE", -16 );
 DEFINE( "ERR_DB_NODATA", -32 );
 DEFINE( "ERR_UNDEF_EXIT", -64 );
 DEFINE( "DEBUG", 0 );


?>
