<?php

 //-------------------------------------------------------------------//
 //
 // AGHOST Paranormal Reporting Database (ParaDB)
 // Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>
 //
 // See the file COPYING for terms of use and redistribution.
 //
 // File: case.inc.php
 //	  This file declares variables and arrays needed for case
 //	  management.
 //
 //-------------------------------------------------------------------//


 ///////////////////////////////////////////////////////////////////////////////////////////////
 // Internal Variables - Do Not Edit.
 ///////////////////////////////////////////////////////////////////////////////////////////////

 // $CASE_VALID_FIELDS
 // First array key is the name of a column in the database.
 // 'value' is propogated with data from the form or from the database.
 // 'maxlength' is the maximum allowed length of the data in 'value'.
 $CASE_VALID_FIELDS = array (	'case_id'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"20",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"yes" ),

				'owner_id'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"10",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"yes" ),

				'investigation_title'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"50",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"no" ),

				'expiration_date'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"12",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"yes" ),

				'date'			=>	array ( 'value'		=>	"",
									'maxlength'	=>	"12",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"yes" ),

				'timezone'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"10",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"yes" ),

				'time_hour'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"2",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"yes" ),

				'time_minute'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"2",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"yes" ),

				'address'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"50",
									'minperm'	=>	$USER_PERMS['team-lead'],
									'required'	=>	"no" ),

				'city'			=>	array ( 'value'		=>	"",
									'maxlength'	=>	"25",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"yes" ),

				'state'			=>	array ( 'value'		=>	"",
									'maxlength'	=>	"25",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"yes" ),

				'zip'			=>	array ( 'value'		=>	"",
									'maxlength'	=>	"10",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"no" ),

				'country'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"25",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"no" ),

				'loc_type'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"25",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"no" ),

				'description'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"500",
									'minperm'	=>	$USER_PERMS['team-lead'],
									'required'	=>	"no" ),

				'contact_name'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"50",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"no" ),

				'contact_primary_phone'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"25",
									'minperm'	=>	$USER_PERMS['team-lead'],
									'required'	=>	"no" ),

				'contact_office_phone'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"25",
									'minperm'	=>	$USER_PERMS['team-lead'],
									'required'	=>	"no" ),

				'contact_mobile_phone'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"25",
									'minperm'	=>	$USER_PERMS['team-lead'],
									'required'	=>	"no" ),

				'contact_email'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"50",
									'minperm'	=>	$USER_PERMS['team-lead'],
									'required'	=>	"no" ),

				'investigators'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"50",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"no" ),

				'investigation_type'	=>	array ( 'value'		=>	"investigation",
									'maxlength'	=>	"25",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"yes" ),

				'investigators_role'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"150",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"no" ),

				'data_submitted'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"50",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"no" ),

				'case_open'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"10",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"yes" ),

				'recap_date'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"12",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"no" ),

				'recap_time_hour'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"2",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"no" ),

				'recap_time_minute'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"2",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"no" ),

				'recap_time_timezone'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"12",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"no" ),

				'recap_location'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"50",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"no" ),

				'data_submitted'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"50",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"no" ),

				'notes'			=>	array ( 'value'		=>	"",
									'maxlength'	=>	"5000",
									'minperm'	=>	$USER_PERMS['registered'],
									'required'	=>	"no" ),
 );



?>
