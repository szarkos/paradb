<?php

 //-------------------------------------------------------------------//
 //
 // AGHOST Paranormal Reporting Database (ParaDB)
 // Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>
 //
 // See the file COPYING for terms of use and redistribution.
 //
 // File: moonphase.php
 //	  Functions to obtain and return moon phase data.
 //
 //-------------------------------------------------------------------//


 // Function: populate_moon_phase( [&VALID_FIELDS] )
 // Retrieve moon phase data and copy it to passed array.
 function populate_moon_phase( &$VALID_FIELDS )  {

	global $ENABLE_MOONPHASE, $baseurl;

	if ( $ENABLE_MOONPHASE != 1 )  {
		return 0;
	}
	if ( !is_array($VALID_FIELDS) )  {
		return ERR_UNDEF;
	}

	$img_url = $baseurl . '/images/moon';
	$moonphase_fields = array ( 	'moon_image',
					'moon_phase',
					'moon_illum' );

	// Check the moon-phase related fields to see if they're already set.
	// This is done to keep from querying the server every time the report is saved.
	$count = 0;
	foreach ( $moonphase_fields as $key )  {
		if ( !empty($VALID_FIELDS[$key]['value']) )  {
			$count++;
		}
	}
	if ( $count > 0 )  {
		return 0;
	}

	if ( !empty($VALID_FIELDS['start_time_hour']['value']) )  {
		$time = $VALID_FIELDS['start_time_hour']['value'] . ':';
	}
	else  {
		$time = "00:";
	}
	if ( !empty($VALID_FIELDS['start_time_minute']['value']) )  {
		$time .= $VALID_FIELDS['start_time_minute']['value'] . ':00';
	}
	else  {
		$time .= "00:00";
	}

	$case_info = list_case_info( $VALID_FIELDS['case_id']['value'] );
	$moonphase = get_moon_data( $VALID_FIELDS['date']['value'], $time, $case_info[$VALID_FIELDS['case_id']['value']]['timezone'] );
	if ( empty($moonphase) )  {
		return 0;
	}

	$moonphase = explode( '|', $moonphase );
	$VALID_FIELDS['moon_image']['value'] = $img_url . '/' . $moonphase[0];
	$VALID_FIELDS['moon_phase']['value'] = $moonphase[1];
	$VALID_FIELDS['moon_illum']['value'] = $moonphase[2];

	return 0;
}



// Function: get_moon_data ( [date], [time] )
// Retrieves moon phase information and moon image.
// Date should be in xxx-xx-xx format and time should be in hh:mm:ss.
function get_moon_data( $date="", $time='00:00:00', $tzone="" )  {

	if ( empty($date) )  {
		return ERR_UNDEF;
	}

	include( 'moonphase.inc.php' );

	$moondata = phase( strtotime($date . ' ' . $time . ' ' . $tzone) );
	$MoonPhase = $moondata[0];
	$MoonIllum = $moondata[1];
	$MoonAge = $moondata[2];
	$MoonDist = $moondata[3];
	$MoonAng = $moondata[4];
	$SunDist = $moondata[5];
	$SunAng = $moondata[6];

	// Assign waxing/waning based on age of moon in cycle.
	$phase = 'Waxing';
	if ( $MoonAge > SYNMONTH/2 )  {
		$phase = 'Waning';
	}

	// Make $MoonAge 2 digits (01,02 etc.)
	$MoonAge = round( $MoonAge, 0 );
	if ( $MoonAge < 10 )  {
		$MoonAge = "0" . $MoonAge;
	}

	// Convert $MoonIllum to percent and round to whole percent.
	$MoonIllum = round( $MoonIllum, 2 );
	$MoonIllum *= 100;
	if ( $MoonIllum == 0 )  {
		$phase = "New Moon";
	}
	if ( $MoonIllum == 100 )  {
		$phase = "Full Moon";
	}

	return( "bigmoon" . $MoonAge . ".png|" . $phase . "|" . $MoonIllum . "%" );
}




?>
