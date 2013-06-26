<?php

 //-------------------------------------------------------------------//
 //
 // AGHOST Paranormal Reporting Database (ParaDB)
 // Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>
 //
 // See the file COPYING for terms of use and redistribution.
 //
 // File: misc_functions.php
 //       As the name implies, this is where I put functions that I
 //	  did not have another place for.
 //
 //-------------------------------------------------------------------//


 // Function: check_length( [string], [maxlength] )
 // Check the length of the data from the form.  $MaxLength is passed here, but is usually
 // taken from 'maxlength' field in the $VALID_FIELDS hash.
 function check_length ( $string="", $maxlength=0 )  {

	if ( empty($string) || $maxlength == 0 || !is_numeric($maxlength) )  {
		return $string;
	}

	if ( strlen($string) > $maxlength )  {
		$string = substr( $string, 0, $maxlength );
	}

	return $string;
 }



 // Function: set_error( [error], [opt] )
 // Sets the global variable $ERR_MSG.
 function set_error( $error="", $opt="o" )  {
	global $ERR_MSG;

	if ( empty($error) )  {
		return 0;
	}

	if ( strtolower($opt) == 'o' )  {  // Overwrite.
		$ERR_MSG = $error;
	}
	elseif ( strtolower($opt) == 'a' )  {  // Append.
		$ERR_MSG .= $error;
	}

	return 0;
 }



 // Function: set_title( [title], [opt] )
 function set_title( $title="", $opt="o" )  {
	global $TITLE;

	if ( strtolower($opt) == 'o' )  {  // Overwrite title.
		$TITLE = $title;
	}
	elseif ( strtolower($opt) == 'a' )  {  // Append to title.
		$TITLE .= $title;
	}

	return 0;
 }



 // Function: check_date( [date] )
 // Check the format of a date.
 function check_date( $date='' )  {

	if ( empty($date) )  {
		return FALSE;
	}
	if ( $date == '0000-00-00' )  {
		return TRUE;
	}

	$date = explode('-', $date);
	foreach ( $date as $key )  {
		if ( !is_numeric($key) )  {
			return FALSE;
		}
	}
	if ( checkdate($date[1], $date[2], $date[0]) )  {
		return TRUE;
	}

	return FALSE;
 }



 // Function: clean_field( [value] )
 // Strip HTML and javascript from fields.
 function clean_field ( $value="" )  {

	if ( empty($value) )  {
		return $value;
	}

	// Strip out some javascript and HTML, and replace some HTML with their ASCII equivalents.
	// At the moment, we do not strip out whitespace at the moment.
	$search = array ('/<script[^>]*?>.*?<\/script>/si',	// Strip out javascript.
			 '/<[\/\!]*?[^<>]*?>/si',		// Strip out HTML tags.
			 '/&(quot|#34);/i',			// Replace HTML stuff.
			 '/&(amp|#38);/i',
			 '/&(lt|#60);/i',
			 '/&(gt|#62);/i',
			 '/&(nbsp|#160);/i',
			 '/&(iexcl|#161);/i',
			 '/&(cent|#162);/i',
			 '/&(pound|#163);/i',
			 '/&(copy|#169);/i' );

	$replace = array ('',
			  '',
			  '"',
			  '&',
			  '<',
			  '>',
			  ' ',
			  chr(161),
			  chr(162),
			  chr(163),
			  chr(169) );

	$value = preg_replace($search, $replace, $value);

	return $value;
 }



 // Function is_browserie()
 // Return true of browser is IE.
 function is_browserie()  {
	if ( preg_match("/MSIE ([0-9]{1,2})/i", $_SERVER['HTTP_USER_AGENT']) && stristr($_SERVER['HTTP_USER_AGENT'], "Mozilla/4.0") )  {
		if ( stristr($_SERVER['HTTP_USER_AGENT'], "Opera") )  {
			return false;
		}
		return true;
	}

	return false;
 }



 // Function: send_email( [message], [subject], [email] )
 // Send an email notification.
 function send_email( $message="", $subject="", $email="" )  {

        global $MAIL_FROM;

	if ( empty($message) || empty($subject) || empty($email) )  {
		return ERR_UNDEF;
	}

        $to = $email;
        $headers = "To: " . $email . "\r\n";
        $headers = "From: \"" . $MAIL_FROM . "\" <" . $MAIL_FROM . ">\r\n";
        $headers .= "Reply-To: \"" . $MAIL_FROM . "\" <" . $MAIL_FROM . ">\r\n";
        $headers .= "X-Priority: 1\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "Importance: High\r\n";
        if ( !mail($to, $subject, $message, $headers) )  {
                do_log( 'Error: send_email(): Error sending email.' );
                return ERR_UNDEF;
        }

        return 0;
 }



 // Function: set_paradb_include_path()
 // Set our include path.
 function set_paradb_include_path()  {

	global $server_path;

	$include_path = $server_path . '/PEAR:' . get_include_path();
	if ( ini_set('include_path', $include_path) === FALSE )  {
		if ( set_include_path($include_path) ===  FALSE )  {
			do_log( 'Warning: set_paradb_include_path(): Unable to set include_path.' );
			return ERR_UNDEF;
		}
	}
	return 0;
 }



 // Function: do_log()
 // Use this function to make a log entry in $LOGFILE.
 function do_log ( $log_msg )  {

	global $LOGFILE, $PRINT_ERRS;

	if ( $PRINT_ERRS == 1 )  {
		print $log_msg . "\n";
	}

	if ( !is_writable($LOGFILE) )  {
		return ERR_UNDEF;
	}
	if ( $FH = fopen($LOGFILE, 'a+') )  {
		$locked = flock( $FH,LOCK_EX );
		$log_msg = "[" . date("D M d G:i:s Y") . "] " . $log_msg . "\n";
		fwrite( $FH, $log_msg );
		fclose( $FH );
	}
	else  {
		return ERR_UNDEF;
	}

	return 0;
 }



 // Close mysql connections and clean up.
 function cleanup ()  {

	global $db_connection;
	global $login_db_connection;

	if ( is_resource($db_connection) )  {
		mysql_close( $db_connection );
	}
	if ( is_resource($login_db_connection) )  {
		mysql_close( $login_db_connection );
	}

	return 0;
 }




?>
