<?php

 //-------------------------------------------------------------------//
 //
 // AGHOST Paranormal Reporting Database (ParaDB)
 // Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>
 //
 // See the file COPYING for terms of use and redistribution.
 //
 // File: users.inc.php
 //       This file declares variables and arrays needed for user
 //       management.
 //
 //-------------------------------------------------------------------//


	global $LOGIN_DB_USER_COL;
	global $LOGIN_DB_NAME_COL;
	global $LOGIN_DB_EMAIL_COL;
	global $LOGIN_DB_PASS_COL;
	global $LOGIN_DB_PERM_COL;
	global $LOGIN_DB_BLOCK_COL;


	// User information.
	$USER_FIELDS = array (	$LOGIN_DB_USER_COL	=>	array (	'value'		=>	'',
									'maxlength'	=>	20,
									'required'	=>	'yes' ),

				$LOGIN_DB_NAME_COL	=>	array (	'value'		=>	'',
									'maxlength'	=>	30,
									'required'	=>	'yes' ),

				$LOGIN_DB_EMAIL_COL	=>	array (	'value'		=>	'',
									'maxlength'	=>	35,
									'required'	=>	'yes' ),

				$LOGIN_DB_PASS_COL	=>	array (	'value'		=>	'',
									'maxlength'	=>	20,
									'required'	=>	'yes' ),

				$LOGIN_DB_PERM_COL	=>	array (	'value'		=>	0,
									'maxlength'	=>	3,
									'required'	=>	'no' ),

				$LOGIN_DB_BLOCK_COL	=>	array (	'value'		=>	0,
									'maxlength'	=>	1,
									'required'	=>	'no' )
	);



?>
