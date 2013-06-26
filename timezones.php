<?php

 //-------------------------------------------------------------------//
 //
 // AGHOST Paranormal Reporting Database (ParaDB)
 // Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>
 //
 // See the file COPYING for terms of use and redistribution.
 //
 // File: timezone.php
 //	  Functions to return timezone information.
 //
 //-------------------------------------------------------------------//


 // Function: get_tz( [opt] )
 // Returns the $tz array from include/timezones.inc.php, sorted by timezone name (default)
 // or by UTC offset.
 function get_tz( $opt="" )  {
	include_once( 'include/timezones.inc.php' );
	if ( $opt == 'sort_utc' )  {
		uksort( $tz, 'tz_sort' );
	}
	return $tz;
 }



 // Function: tz_sort( [a], [b] )
 // Callback function for use with get_tz() and uksort().
 function tz_sort( $a="", $b="" )  {

	include( 'include/timezones.inc.php' );

	$tmp_a = preg_replace( '/:\d+/', '', $tz[$a]['utc'] );
	$tmp_a = preg_replace( '/\+/', '', $tmp_a );
	$tmp_b = preg_replace( '/:\d+/', '', $tz[$b]['utc'] );
	$tmp_b = preg_replace( '/\+/', '', $tmp_b );

	return ( $tmp_a > $tmp_b );
 }




?>
