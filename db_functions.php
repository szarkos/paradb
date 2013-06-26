<?php

 //-------------------------------------------------------------------//
 //
 // AGHOST Paranormal Reporting Database (ParaDB)
 // Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>
 //
 // See the file COPYING for terms of use and redistribution.
 //
 // File: db_functions.php
 //	  Functions related to database interaction (connect, select,
 //	  insert, update, delete, etc.
 //-------------------------------------------------------------------//


 // Function mysql_init_connect()
 // Connect to a MySQL server and return a resource handle.
 function mysql_init_connect( $db_host, $db_name, $db_user, $db_pass )  {

	// Connect to MySQL server.
	if (! $db_connection = mysql_connect($db_host, $db_user, $db_pass) )  {
		do_log( 'Error: mysql_init_connect(): Could not connect to MySQL: ' . mysql_error() );
		print_error( 'Error: mysql_init_connect(): Could not connect to MySQL: ' . mysql_error() );
		return ERR_UNDEF;
	}

	// Connect to Database.
	if (! mysql_select_db($db_name, $db_connection) )  {
		do_log( 'Error: mysql_init_connect(): Could not select database: ' . mysql_error() );
		print_error( 'Error: mysql_init_connect(): Could not select database: ' . mysql_error() );
		return ERR_UNDEF;
	}

	return $db_connection;
 }



 function mysql_safe_string ( $string="", $db_conn="" )  {

	if ( empty($string) )  {
		return $string;
	}

	if ( get_magic_quotes_gpc() )  {
		$string = stripslashes( $string );
	}

	if ( version_compare(phpversion(),"4.3.0") == "-1" )  {
		$string = mysql_escape_string( $string );
	}
	else  {
		if ( $db_conn == "" )  {
			global $db_connection;
			$db_conn = $db_connection;
		}
		$string = mysql_real_escape_string( $string, $db_conn );
	}

	// Escape some wildcards missed by mysql_real_escape_string().
//	$string = preg_replace( '/\\\\*%/', '\%', $string );
//	$string = preg_replace( '/\\\\\*_/', '\_', $string );

	return $string;
 }



 function mysql_get_rows( $table, $colname, $where, $order, $limit, $db_conn )  {

	$colname = ( $colname != NULL ) ? $colname : "*";
	$where = ( ($where != NULL) && ($where != "") ) ? $where : "";
	$order = ( $order != NULL ) ? $order : "";
	$limit = ( $limit != NULL ) ? $limit : "";

	if ( !empty($where) )  {
		$operator = array();
		$args = array();

		if ( preg_match('/[\s\t]+AND[\s\t]+/i', $where) )  {
			$args = preg_split( '/[\s\t]+AND[\s\t]+/i', $where );
		}
		else  {
			array_push( $args, $where );
		}

		$where = ' WHERE ';
		foreach ( $args as $key )  {
			if ( $where != ' WHERE ' )  {
				$where .= ' AND ';
			}
			if ( preg_match('/!?=?[<=>]=?/', $key, $operator) )  {
				$tmpargs = preg_split( '/' . $operator[0] .'/', $key, 2 );
				if ( preg_match('/^`[\w\-_]+`$/', trim($tmpargs[1])) )  {
					$where .= trim($tmpargs[0]) . $operator[0] . mysql_safe_string( trim($tmpargs[1]), $db_conn );
				}
				else  {
					$where .= trim($tmpargs[0]) . $operator[0] . "'" . mysql_safe_string( trim($tmpargs[1]), $db_conn ) . "'";
				}
			}
			elseif ( preg_match('/like/i', $key, $operator) )  {
				$tmpargs = preg_split( '/' . $operator[0] .'/', $key, 2 );
				if ( preg_match('/^`[\w\-_]+`$/', trim($tmpargs[1])) )  {
					$where .= trim($tmpargs[0]) . ' ' . $operator[0] . " " . mysql_safe_string( trim($tmpargs[1]), $db_conn );
				}
				else  {
					$where .= trim($tmpargs[0]) . ' ' . $operator[0] . " '" . mysql_safe_string( trim($tmpargs[1]), $db_conn ) . "'";
				}
			}
			else  {
				continue;
			}
		}
	}

	if ( !empty($order) )  {
		$order = " ORDER BY " . mysql_safe_string( $order, $db_conn );
	}
	if ( !empty($limit) )  {
		$limit = " LIMIT " . mysql_safe_string( $limit, $db_conn );
	}

	$query = "select " . $colname . " from " . $table . $where . $order . $limit . ";";
	$result = mysql_query( $query, $db_conn );
	if ( DEBUG == 1 )  {
		do_log( "DEBUG: mysql_get_rows(): " . $query );
	}
	if ( !$result )  {
		do_log( 'ERROR: mysql_get_rows(): Undefined output from mysql_query(). ' . mysql_error() );
		print_error( 'Error: mysql_get_rows(): Undefined output from mysql_query(). <br>' . mysql_error() );
		return ERR_UNDEF;
	}

	return $result;
 }


 // Function: mysql_get_rows_safe()
 // Just like mysql_get_rows except without all the checks on the WHERE statements.
 // Allows one to do more complexe WHERE statements provides any external input is already
 // run through mysql_safe_string() before passing it to this function.
 function mysql_get_rows_safe( $table, $colname, $where, $order, $limit, $db_conn )  {

	$colname = ( $colname != NULL ) ? $colname : "*";
	$where = ( ($where != NULL) && ($where != "") ) ? $where : "";
	$order = ( $order != NULL ) ? $order : "";
	$limit = ( $limit != NULL ) ? $limit : "";

	if ( !empty($where) )  {
		$where = ' WHERE ' . $where;
	}

	if ( !empty($order) )  {
		$order = " ORDER BY " . mysql_safe_string( $order, $db_conn );
	}
	if ( !empty($limit) )  {
		$limit = " LIMIT " . mysql_safe_string( $limit, $db_conn );
	}

	$query = "select " . $colname . " from " . $table . $where . $order . $limit . ";";
	$result = mysql_query( $query, $db_conn );
	if ( DEBUG == 1 )  {
		do_log( "DEBUG: mysql_get_rows(): " . $query );
	}
	if ( !$result )  {
		do_log( 'ERROR: mysql_get_rows(): Undefined output from mysql_query(). ' . mysql_error() );
		print_error( 'Error: mysql_get_rows(): Undefined output from mysql_query(). <br>' . mysql_error() );
		return ERR_UNDEF;
	}

	return $result;
 }



 function mysql_db_insert ( $table="", &$VALID_FIELDS, $db_conn=0 )  {

	if ( empty($table) || $db_conn == 0 || !isset($VALID_FIELDS) )  {
		return ERR_UNDEF;
	}

	$query = "INSERT INTO " . $table . " (";

	$tmp = 0;
	foreach ( array_keys($VALID_FIELDS) as $key )  {
		if ( $tmp == 1 )  {
			$query .= ", ";
		}
		$query .= $key;
		$tmp = 1;
	}
	$query .= ") VALUES (trim('";

	$tmp = 0;
	foreach ( array_keys($VALID_FIELDS) as $key )  {
		if ($tmp == 1)  {
			$query .= ", trim('";
		}
		$query .= mysql_safe_string( $VALID_FIELDS[$key]['value'], $db_conn ) . "')";
		$tmp = 1;
	}
	$query .= ");";
	if ( DEBUG == 1 )  {
		do_log( "DEBUG: mysql_db_insert(): " . $query );
	}

	if ( $result = mysql_query($query, $db_conn) )  {
		do_log( 'Info: mysql_db_insert(): Successful insert into database by user ' . $_SESSION['username'] . '.' );
		return 0;
	}
	else  {
		do_log( 'Error: mysql_db_insert(): Undefined output from mysql_query(). ' . mysql_error() );
		print_error( 'Error: mysql_db_insert(): Undefined output from mysql_query(). <br>' . mysql_error() );
		return ERR_UNDEF;
	}

	return 0;
 }



 function mysql_db_update ( $table="", &$VALID_FIELDS, $where="", $db_conn="" )  {

	if ( empty($table) || empty($db_conn) || !is_array($VALID_FIELDS) )  {
		return ERR_UNDEF;
	}

	$query = "UPDATE " . $table . " SET ";

	$tmp = 0;
	foreach ( array_keys($VALID_FIELDS) as $key )  {
		if ( $tmp == 1 )  {
			$query .= ", ";
		}
		$query .= $key . " = trim('" . mysql_safe_string($VALID_FIELDS[$key]['value'], $db_conn) . "')";
		$tmp = 1;
	}

	if ( !empty($where) )  {
		$args = array();
		if ( preg_match('/[\s\t]+AND[\s\t]+/i', $where) )  {
			$args = preg_split( '/[\s\t]+AND[\s\t]+/i', $where );
		}
		else  {
			array_push( $args, $where );
		}
		$where = ' WHERE ';
		foreach ( $args as $key )  {
			if ( $where != ' WHERE ' )  {
				$where .= ' AND ';
			}
			$tmpargs = preg_split( '/=/', $key, 2 );
			$where .= $tmpargs[0] . "='" . mysql_safe_string($tmpargs[1], $db_conn) . "'";
		}
	}

	$query .= $where . ";";
	if ( DEBUG == 1 )  {
		do_log( "DEBUG: mysql_db_update(): " . $query );
	}
	if ( $result = mysql_query($query, $db_conn) )  {
		do_log( 'Info: mysql_db_update(): Successful update of table ' . $table . ' by user ' . $_SESSION['username'] . ', case_id: ' . $VALID_FIELDS['case_id']['value'] . '.' );
		return 0;
	}
	else  {
		do_log( 'Error: mysql_db_update(): Undefined output from mysql_query(). ' . mysql_error() );
		print_error( 'Error: mysql_db_update(): Undefined output from mysql_query(). <br>' . mysql_error() );
		return ERR_UNDEF;
	}

	return 0;
 }



 function mysql_db_delete ( $table="", $where="", $db_conn=0 )  {

	if ( empty($table) || empty($db_conn) )  {
		return ERR_UNDEF;
	}

	$query = "DELETE FROM " . $table;

	if ( !empty($where) )  {
		$args = array();
		if ( preg_match('/[\s\t]+AND[\s\t]+/i', $where) )  {
			$args = preg_split( '/[\s\t]+AND[\s\t]+/i', $where );
		}
		else  {
			array_push( $args, $where );
		}
		$where = ' WHERE ';
		foreach ( $args as $key )  {
			if ( $where != ' WHERE ' )  {
				$where .= ' AND ';
			}
			$tmpargs = preg_split( '/=/', $key, 2 );
			$where .= $tmpargs[0] . "='" . mysql_safe_string($tmpargs[1], $db_conn) . "'";
		}
	}

	$query .= $where . ";";
	if ( DEBUG == 1 )  {
		do_log( "DEBUG: mysql_db_update(): " . $query );
	}

	if ( mysql_query($query, $db_conn) )  {
		do_log( 'Info: mysql_db_delete(): Successful deletion of record by user ' . $_SESSION['username'] . '.' );
//		send_email( 'Info: mysql_db_delete(): Successful deletion of record by user ' . $_SESSION['username'] . '.', NULL );
		return 0;
	}
	else  {
		do_log( 'Error: mysql_db_delete(): Undefined output from mysql_query(). ' . mysql_error() );
		set_error ( 'Error: mysql_db_delete(): Undefined output from mysql_query(). <br>' . mysql_error() );
		return ERR_UNDEF;
	}

	return 0;
 }



 function mysql_db_search ( $table, $column, $return, $search, $order, $db_conn )  {

	$order = ( $order != NULL ) ? $order : "";

	$search = mysql_safe_string( $search, $db_conn );
	$query = "SELECT ";

	$tmp = 0;
	foreach ( $return as $ret )  {
		if ( $tmp != 0 )  {
			$query = $query . ",";
		}
		$query = $query . $ret;
		$tmp = 1;
	}

	$query = $query . " FROM " . $table . " WHERE ";

	$tmp = 0;
	foreach ( $column as $col )  {
		if ( $tmp != 0 )  {
			$query = $query . " OR ";
		}
		$query = $query . $col . " LIKE '%" . $search . "%' ";
		$tmp = 1;
	}

	if ( !empty($order) )  {
		$query = $query . " ORDER BY " . mysql_safe_string( $order, $db_conn );
	}

	$result = mysql_query( $query, $db_conn );
	if ( !$result )  {
		do_log( 'Error: mysql_db_search(): Undefined output from mysql_query(). ' . mysql_error() );
		print_error( 'mysql_db_search(): error: Undefined output from mysql_query(). <br>' . mysql_error() );
		return ERR_UNDEF;
	}

	return $result;
 }





?>
