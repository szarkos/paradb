<?php

 //-------------------------------------------------------------------//
 //
 // AGHOST Paranormal Reporting Database (ParaDB)
 // Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>
 //
 // See the file COPYING for terms of use and redistribution.
 //
 // File: init_login.php
 //       Functions related to obtaining checking login information
 //	  and initializing session information.  Functions related
 //	  to retreiving user information are also located here.
 //
 //-------------------------------------------------------------------//


 // Function: init_login()
 // Check login information and initialize session.
 function init_login()  {

	global $LOGIN_DB_HOST;
	global $LOGIN_DB_USER;
	global $LOGIN_DB_PASS;
	global $LOGIN_DB_NAME;
	global $LOGIN_DB_TABLE;
	global $LOGIN_DB_USER_COL;
        global $login_db_connection;
	global $cookie_timeout;
	global $ADMIN_EMAIL;

	ini_set( "session.use_only_cookies", true );
	ini_set( "session.hash_function", 1 );
	ini_set( "session.name", "reports_db" );
	session_set_cookie_params( $cookie_timeout );
	session_start();

	if ( isset($_GET['logout']) )  {
		do_log( 'Info: init_login(): Logout: ' . $_SESSION['username'] . ' (' . $_SERVER['REMOTE_ADDR'] . ').' );
		display_logout();
		return ERR_PERM;
	}

	// Connect to DB.
	$login_db_connection = mysql_init_connect( $LOGIN_DB_HOST, $LOGIN_DB_NAME, $LOGIN_DB_USER, $LOGIN_DB_PASS );

	if ( !isset($_SESSION['username']) )  {

		// Check for "Remember Me" cookie data.
		if ( isset($_COOKIE['reportsdbname']) && isset($_COOKIE['reportsdbpass']) )  {
			if ( !empty($_COOKIE['reportsdbname']) )  {
				$_POST['username'] = $_COOKIE['reportsdbname'];
			}
			if ( !empty($_COOKIE['reportsdbpass']) )  {
				$_POST['password'] = $_COOKIE['reportsdbpass'];
			}
		}
		if ( isset($_POST['username']) && isset($_POST['password']) )  {
			$_POST['username'] = strtolower($_POST['username']);
			if ( check_login($login_db_connection) == 0 )  {
				if ( set_user_attribs( $_POST['username'], $login_db_connection ) != 0 )  {
					kill_session();
					display_login( 'ERROR: Invalid user attribute.' );
					return ERR_PERM;
				}
				session_write_close();
				if ( isset($_SERVER['HTTP_USER_AGENT']) )  {
					if ( preg_match('/MSIE\s6.0/', $_SERVER['HTTP_USER_AGENT']) )  {
						header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
					}
				}
				do_log( 'Info: init_login(): Successful Login: ' . $_SESSION['username'] . ' (' . $_SERVER['REMOTE_ADDR'] . ').' );
				return 0;
			}
			else  {
				kill_session();
				do_log( 'Info: init_login(): Failed Login: ' . $_SESSION['username'] . ' (' . $_SERVER['REMOTE_ADDR'] . ').' );
				display_login( 'Invalid username or password, please try again.' );
				return ERR_PERM;
			}
		}
		else  {
			kill_session();
			display_login();
			return ERR_PERM;
		}
	}
	else  {
		// Check username against the db to make sure it is valid.
		$user = mysql_get_rows( $LOGIN_DB_TABLE, $LOGIN_DB_USER_COL, $LOGIN_DB_USER_COL . "=" . $_SESSION['username'], NULL, NULL, $login_db_connection );
		$user = mysql_result( $user, 0, $LOGIN_DB_USER_COL );
		$user = strtolower( $user );
		if ( strcasecmp($user, $_SESSION['username']) == 0 )  {
			if ( set_user_attribs( $user, $login_db_connection ) != 0 )  {
				kill_session();
				display_login( 'ERROR: Invalid user attribute.' );
				return ERR_PERM;
			}
			session_write_close();
			if ( isset($_SERVER['HTTP_USER_AGENT']) )  {
				if ( preg_match('/MSIE\s6.0/', $_SERVER['HTTP_USER_AGENT']) )  {
					header( 'P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"' );
				}
			}
			return 0;
		}
		else  {
			do_log( 'Error: init_login(): Invalid session variable for user ' . $_SESSION['username'] . '.' );
			display_login( 'Error: Invalid session, please contact the administrator: ' . $ADMIN_EMAIL );
			kill_session();
			return ERR_PERM;
		}
	}
 }



 // Function: set_rememberme()
 // Set the cookies for the "Remember Me" functionality.
 function set_rememberme ()  {
	if ( isset($_POST['rememberme']) && $_POST['rememberme'] == 1 )  {
		setcookie( "reportsdbname", $_SESSION['username'], time()+60*60*24*100, "/" );
		setcookie( "reportsdbpass", $_SESSION['password'], time()+60*60*24*100, "/" );
	}
	return 0;
 }



 // Function: unset_rememberme()
 // Delete the cookies for the "Remember Me" functionality.
 function unset_rememberme()  {
	if ( isset($_COOKIE['reportsdbname']) )  {
		setcookie("reportsdbname", "", time()-60*60*24*100, "/");
	}
	if ( isset($_COOKIE['reportsdbpass']) )  {
		setcookie("reportsdbpass", "", time()-60*60*24*100, "/");
	}
	return 0;
 }



 function display_logout () {
	unset_rememberme();
	kill_session();
	display_login();
	return 0;
 }



 function display_login ( $msg="" )  {
//	print_header();
	include( 'template/login.inc.php' );
	print_footer();
	return 0;
 }



 function check_login ( $login_db_connection )  {

	global $LOGIN_DB_TABLE;
	global $LOGIN_DB_USER_COL;
	global $LOGIN_DB_PASS_COL;
	global $LOGIN_DB_BLOCK_COL;


	$user_info = mysql_get_rows( $LOGIN_DB_TABLE, $LOGIN_DB_USER_COL . ',' . $LOGIN_DB_PASS_COL . ',' . $LOGIN_DB_BLOCK_COL, $LOGIN_DB_USER_COL . "=" . $_POST['username'], NULL, NULL, $login_db_connection );
	if ( mysql_num_rows($user_info) < 1 )  {
		return ERR_PERM;
	}

	// Check if account has been disabled.
	if ( mysql_result($user_info, 0, $LOGIN_DB_BLOCK_COL) == 1 )  {
		return ERR_PERM;
	}


	// Check password.
	$user = mysql_result( $user_info, 0, $LOGIN_DB_USER_COL );
	if ( strcasecmp($user, $_POST['username']) == 0 )  {
		$salt = '';
		$pass = mysql_result( $user_info, 0, $LOGIN_DB_PASS_COL );
		if ( strstr($pass, ':') )  {
			// Password is salted.
			list( $pass, $salt ) = explode( ':', $pass );
		}
		if ( isset($_COOKIE['reportsdbpass']) && !empty($_COOKIE['reportsdbpass']) )  {
			if ( strcasecmp($pass, $_COOKIE['reportsdbpass']) == 0 )  {
				return 0;
			}
		}
		else  {
			if ( strcasecmp($pass, md5($_POST['password'] . $salt)) == 0 )  {
				return 0;
			}
		}
	}

	return ERR_PERM;
 }



 // Set the user's $_SESSION variables.
 function set_user_attribs ( $username="", $login_db_connection="" )  {

	global $LOGIN_DB_TABLE;
	global $LOGIN_DB_ID_COL;
	global $LOGIN_DB_USER_COL;
	global $LOGIN_DB_NAME_COL;
	global $LOGIN_DB_PASS_COL;
	global $LOGIN_DB_PERM_COL;
	global $USER_PERMS;

	if ( empty($username) || empty($login_db_connection) )  {
		return ERR_PERM;
	}

 	$all_data = mysql_get_rows( $LOGIN_DB_TABLE, $LOGIN_DB_ID_COL . ',' . $LOGIN_DB_USER_COL . ',' . $LOGIN_DB_NAME_COL . ',' . $LOGIN_DB_PASS_COL . ',' . $LOGIN_DB_PERM_COL, $LOGIN_DB_USER_COL . "=" . $username, NULL, NULL, $login_db_connection );

	// User ID
	$user_data = mysql_result( $all_data, 0, $LOGIN_DB_ID_COL );
	if ( $user_data === FALSE || empty($user_data) )  {
 		do_log( 'ERROR: set_user_attribs(): Invalid user attribute \'LOGIN_DB_ID_COL\', user_data=' . $user_data );
		return ERR_UNDEF;
	}
	else  {
		$_SESSION['user_id'] = $user_data;
	}

	// Username
	$user_data = mysql_result( $all_data, 0, $LOGIN_DB_USER_COL );
	if ( $user_data === FALSE || empty($user_data) )  {
		do_log( 'ERROR: set_user_attribs(): Invalid user attribute \'LOGIN_DB_USER_COL\', user_data=' . $user_data );
		return ERR_UNDEF;
	}
	else  {
		$_SESSION['username'] = $user_data;
	}

	// Full Name
	$user_data = mysql_result( $all_data, 0, $LOGIN_DB_NAME_COL );
	if ( $user_data === FALSE || empty($user_data) )  {
		do_log( 'ERROR: set_user_attribs(): Invalid user attribute \'LOGIN_DB_NAME_COL\', user_data=' . $user_data );
		return ERR_UNDEF;
	}
	else  {
		$_SESSION['name'] = $user_data;
	}

	// Set cookies for "Remember Me" functionality.
	if ( isset($_POST['rememberme']) && $_POST['rememberme'] == 1 )  {
		$user_data = mysql_result( $all_data, 0, $LOGIN_DB_PASS_COL );
		if ( $user_data === FALSE || empty($user_data) )  {
			do_log( 'ERROR: set_user_attribs(): Invalid user attribute \'LOGIN_DB_PASS_COL\', user_data=' . $user_data );
			return ERR_UNDEF;
		}
		if ( strstr($user_data, ':') )  {
			// Salted password, don't put salt in cookie.
			list( $hash, $salt ) = explode( ':', $user_data );
			$_SESSION['password'] = $hash;
		}
		else  {
			$_SESSION['password'] = $user_data;
		}
		set_rememberme();
	}

	// User Perm
	$user_data = mysql_result( $all_data, 0, $LOGIN_DB_PERM_COL );
	if ( $user_data === FALSE || empty($user_data) )  {
		$_SESSION['user_perm'] = 0;
		return 0;
	}
	else  {
		foreach ( array_keys($USER_PERMS) as $key )  {
			if ( $user_data == $USER_PERMS[$key] )  {
				$_SESSION['user_perm'] = $user_data;
				return 0;
			}
		}
	}

	$_SESSION['user_perm'] = 0;
	do_log( 'ERROR: set_user_attribs(): Invalid user attribute \'LOGIN_DB_PERM_COL\'.' );
	return ERR_PERM;
 }



 // End the session, delete the cookie(s) and unset the $_SESSION data.
 function kill_session ()  {
	$_SESSION = array();
	unset_rememberme();
	if ( isset($_COOKIE[session_name()]) )  {
		setcookie(session_name(), '', time()-42000, '/');
	}
	session_destroy();
	return 0;
 }



 // Function: listusers()
 // Returns an array of user's username, real name and user ID.
 //	$users[$row['username']]['id']
 //	$users[$row['username']]['name']
 //	$users[$row['username']]['reports_db_perm']
 //	$users[$row['username']]['disabled']
 //	$users[$row['username']]['email']
 function listusers()  {

	global $LOGIN_DB_HOST;
	global $LOGIN_DB_USER;
	global $LOGIN_DB_PASS;
	global $LOGIN_DB_NAME;
	global $LOGIN_DB_TABLE;
	global $LOGIN_DB_ID_COL;
	global $LOGIN_DB_EMAIL_COL;
	global $LOGIN_DB_USER_COL;
	global $LOGIN_DB_NAME_COL;
	global $LOGIN_DB_PERM_COL;
	global $LOGIN_DB_BLOCK_COL;

	$login_db_connection = mysql_init_connect( $LOGIN_DB_HOST, $LOGIN_DB_NAME, $LOGIN_DB_USER, $LOGIN_DB_PASS );
	$data = mysql_get_rows( $LOGIN_DB_TABLE, $LOGIN_DB_ID_COL . ',' . $LOGIN_DB_USER_COL . ',' . $LOGIN_DB_NAME_COL . ',' . $LOGIN_DB_PERM_COL . ',' . $LOGIN_DB_EMAIL_COL . ',' . $LOGIN_DB_BLOCK_COL, NULL, NULL, NULL, $login_db_connection );
//	mysql_close( $login_db_connection );

	$users = array();
	while ( $row = mysql_fetch_array($data, MYSQL_ASSOC) )  {
//		if ( strtolower($row[$LOGIN_DB_USER_COL]) == 'admin' )  {
//			continue;
//		}
		$users[$row['username']]['id'] = $row[$LOGIN_DB_ID_COL];
		$users[$row['username']]['name'] = $row[$LOGIN_DB_NAME_COL];
		$users[$row['username']]['reports_db_perm'] = $row[$LOGIN_DB_PERM_COL];
		$users[$row['username']]['email'] = $row[$LOGIN_DB_EMAIL_COL];
		$users[$row['username']]['block'] = $row[$LOGIN_DB_BLOCK_COL];
	}

	return $users;
 }



 // Function: get_userinfo( [user_id] )
 // Returns array of information about a particular user given their user_id.
 // 	$user_info['id']
 //	$user_info['username']
 //	$user_info['name']
 //	$user_info['reports_db_perm']
 //	$user_info['email']
 function get_userinfo( $user_id="" )  {

	global $LOGIN_DB_HOST;
	global $LOGIN_DB_USER;
	global $LOGIN_DB_PASS;
	global $LOGIN_DB_NAME;
	global $LOGIN_DB_TABLE;
	global $LOGIN_DB_ID_COL;
	global $LOGIN_DB_USER_COL;
	global $LOGIN_DB_NAME_COL;
	global $LOGIN_DB_EMAIL_COL;
	global $LOGIN_DB_PERM_COL;
	global $LOGIN_DB_BLOCK_COL;

	if ( empty($user_id) )  {
		return ERR_UNDEF;
	}

	$user_info = array();
	$login_db_connection = mysql_init_connect( $LOGIN_DB_HOST, $LOGIN_DB_NAME, $LOGIN_DB_USER, $LOGIN_DB_PASS );
	$data = mysql_get_rows( $LOGIN_DB_TABLE, $LOGIN_DB_USER_COL . ',' . $LOGIN_DB_NAME_COL . ',' . $LOGIN_DB_PERM_COL . ',' . $LOGIN_DB_EMAIL_COL . ',' . $LOGIN_DB_BLOCK_COL, $LOGIN_DB_ID_COL . '=' . $user_id, NULL, NULL, $login_db_connection );
//	mysql_close( $login_db_connection );
	if ( mysql_num_rows($data) == 0 )  {
		return $user_info;
	}

	while ( $row = mysql_fetch_row($data) )  {
		$user_info['id'] = $user_id;
		$user_info['username'] = $row[0];
		$user_info['name'] = $row[1];
		$user_info['reports_db_perm'] = $row[2];
		$user_info['email'] = $row[3];
		$user_info['block'] = $row[4];
	}

	return $user_info;
 }



 // Function: get_username( [userid] )
 // Returns the username given the userid.
 function get_username( $userid="" )  {

	if ( empty($userid) )  {
		return "";
	}

	global $LOGIN_DB_HOST;
	global $LOGIN_DB_NAME;
	global $LOGIN_DB_USER;
	global $LOGIN_DB_PASS;
	global $LOGIN_DB_TABLE;
	global $LOGIN_DB_ID_COL;
	global $LOGIN_DB_USER_COL;

	$login_db_connection = mysql_init_connect( $LOGIN_DB_HOST, $LOGIN_DB_NAME, $LOGIN_DB_USER, $LOGIN_DB_PASS );
	$username = mysql_get_rows( $LOGIN_DB_TABLE, $LOGIN_DB_USER_COL, $LOGIN_DB_ID_COL . '=' . $userid, NULL, NULL, $login_db_connection );
//	mysql_close( $login_db_connection );

	if ( mysql_num_rows($username) > 0 )  {
		return mysql_result( $username, 0, $LOGIN_DB_USER_COL );
	}

	return "";
 }



 // Function: user_exists( [username] )
 // Returns true if the username exists.
 function user_exists( $username="" )  {

	if ( empty($username) )  {
		return false;
	}

	global $LOGIN_DB_HOST;
	global $LOGIN_DB_NAME;
	global $LOGIN_DB_USER;
	global $LOGIN_DB_PASS;
	global $LOGIN_DB_TABLE;
	global $LOGIN_DB_USER_COL;

	$login_db_connection = mysql_init_connect( $LOGIN_DB_HOST, $LOGIN_DB_NAME, $LOGIN_DB_USER, $LOGIN_DB_PASS );
	$username = mysql_get_rows( $LOGIN_DB_TABLE, $LOGIN_DB_USER_COL, $LOGIN_DB_USER_COL . '=' . $username, NULL, NULL, $login_db_connection );
//	mysql_close( $login_db_connection );

	if ( mysql_num_rows($username) > 0 )  {
		return true;
	}

	return false;
 }




?>
