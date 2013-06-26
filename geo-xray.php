<?php

 //-------------------------------------------------------------------//
 //
 // AGHOST Paranormal Reporting Database (ParaDB)
 // Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>
 //
 // See the file COPYING for terms of use and redistribution.
 //
 // File: geo-xray.php
 //       Functions related to obtaining geomagnetic and xray
 //	  information.
 //
 //-------------------------------------------------------------------//


 // AGHOSTOnline.org Space Weather Archive.
 DEFINE( "SEC_ARCHIVE", "http://www.aghostonline.org/weather/archive/sec_history.php" );


 // Function: populate_geo-xray( [array] )
 // Retrieve geomagnetic and xray data and copy it to the passed array.
 function populate_geo_xray( &$VALID_FIELDS )  {

	global $ENABLE_GEO_XRAY;
	if ( $ENABLE_GEO_XRAY != 1 )  {
		return 0;
	}

	if ( !is_array($VALID_FIELDS) )  {
		return ERR_UNDEF;
	}
	$geoxray_fields = array ( 'xray_long',
				  'xray_short',
				  'xray_high',
				  'xray_low',
				  'xray_peak',
				  'xray_summary',
				  'xray_list',
				  'xray_plot',
				  'geomag_kp',
				  'geomag_ap',
				  'geomag_summary',
				  'geomag_list',
				  'geomag_plot' );

	// Check to see if case_date is today or in the future. If so, don't
	// try to populate the fields.
	$casedate = strtotime( get_case_date($VALID_FIELDS['case_id']['value']) );
	$curdate = strtotime( date("Y-m-d") );
	if ( $casedate >= $curdate )  {
		return 0;
	}

	// Check the geomagnetic and xray related fields to see if they're already set.
	// This is done to keep from querying the server every time the report is saved.
	$count = 0;
	foreach ( $geoxray_fields as $key )  {
		if ( !empty($VALID_FIELDS[$key]['value']) && $VALID_FIELDS[$key]['value'] != 0 )  {
			$count++;
		}
	}
	if ( $count > 0 )  {
		return 0;
	}

	$xray = get_xray_data( $VALID_FIELDS['date']['value'] );
	if ( !is_array($xray) )  {
		$xray = array();
	}
	$geomag = get_geomag_data( $VALID_FIELDS['date']['value'] );
	if ( !is_array($geomag) )  {
		$geomag = array();
	}
	foreach ( array_merge($xray, $geomag) as $key=>$value )  {
		if ( isset($VALID_FIELDS[$key]['value']) )  {
			$value = preg_replace( '/</', '&lt;', $value );
			$value = preg_replace( '/>/', '&gt;', $value );
			$VALID_FIELDS[$key]['value'] = $value;
		}
	}

	return 0;
 }



 // Function get_xray_data ( [date] )
 // Retrieves an array containing x-ray data for the specified date.  Date should be in the format xxxx-xx-xx.
 // The returned array has the following values:
 // $xraydata = ( 'xray_long',
 //		  'xray_short',
 //		  'xray_high',
 //		  'xray_low',
 //		  'xray_peak',
 //		  'xray_summary',
 //		  'xray_list',
 //		  'xray_plot' );
 function get_xray_data ( $date="" )  {

	if ( empty($date) )  {
		return ERR_UNDEF;
	}

	$xraydata = array();
	$date = explode( '-', $date );
	$date[1] = preg_replace( '/^0+/', '', $date[1] );
	$date[2] = preg_replace( '/^0+/', '', $date[2] );

	$url = SEC_ARCHIVE . '?format=0&year=' . $date[0] . '&month=' . $date[1] . '&day=' . $date[2];
	$FH = fopen( $url, "r" );
	if ( !$FH )  {
		do_log( 'Error: get_xray_data(): ' . ERR_XRAY_FILE_RETR );
		return ERR_FRETR;
	}
	while ( !feof($FH) )  {
		$line = fgets( $FH, 1024 );
		if ( preg_match( '/^Xray \(1.0-8.0 Ang\):/', $line) )  {
			$xraydata['xray_long'] = preg_replace( '/^Xray \(1.0-8.0 Ang\):[\s\t]+/', '', $line );
			$xraydata['xray_long'] = trim( $xraydata['xray_long'] );
		}
		elseif ( preg_match( '/^Xray \(0.5-3.0 Ang\):[\s\t]+/', $line) )  {
			$xraydata['xray_short'] = preg_replace( '/^Xray \(0.5-3.0 Ang\):[\s\t]+/', '', $line );
			$xraydata['xray_short'] = trim( $xraydata['xray_short'] );
		}
		elseif ( preg_match( '/^Xray High:[\s\t]+/', $line) )  {
			$xraydata['xray_high'] = preg_replace( '/^Xray High:[\s\t]+/', '', $line );
			$xraydata['xray_high'] = trim( $xraydata['xray_high'] );
		}
		elseif ( preg_match( '/^Xray Low:[\s\t]+/', $line) )  {
			$xraydata['xray_low'] = preg_replace( '/^Xray Low:[\s\t]+/', '', $line );
			$xraydata['xray_low'] = trim( $xraydata['xray_low'] );
		}
		elseif ( preg_match( '/^Xray Peak:[\s\t]+/', $line) )  {
			$xraydata['xray_peak'] = preg_replace( '/^Xray Peak:[\s\t]+/', '', $line );
			$xraydata['xray_peak'] = trim( $xraydata['xray_peak'] );
		}
		elseif ( preg_match( '/^Xray Summary:[\s\t]+/', $line) )  {
			$xraydata['xray_summary'] = preg_replace( '/^Xray Summary:[\s\t]+/', '', $line );
			$xraydata['xray_summary'] = trim( $xraydata['xray_summary'] );
		}
		elseif ( preg_match( '/^Xray List:[\s\t]+/', $line) )  {
			$xraydata['xray_list'] = preg_replace( '/^Xray List:[\s\t]+/', '', $line );
			$xraydata['xray_list'] = trim( $xraydata['xray_list'] );
		}
		elseif ( preg_match( '/^Xray Plot:[\s\t]+/', $line) )  {
			$xraydata['xray_plot'] = preg_replace( '/^Xray Plot:[\s\t]+/', '', $line );
			$xraydata['xray_plot'] = trim( $xraydata['xray_plot'] );
		}
	}
	fclose( $FH );

	return $xraydata;
 }



 // Function get_geomag_data ( [date] )
 // Retrieves an array containing geomagnetic data for the specified date.  Date should be in the
 // format xxxx-xx-xx.
 // The returned array has the following values:
 // $geodata = (  'geomag_kp',
 //		  'geomag_ap',
 //		  'geomag_summary',
 //		  'geomag_list',
 //		  'geomag_plot' );
 function get_geomag_data ( $date="" )  {

	if ( empty($date) )  {
		return ERR_UNDEF;
	}

	$geodata = array();
	$date = explode( '-', $date );
	$date[1] = preg_replace( '/^0+/', '', $date[1] );
	$date[2] = preg_replace( '/^0+/', '', $date[2] );

	$url = SEC_ARCHIVE . '?format=0&year=' . $date[0] . '&month=' . $date[1] . '&day=' . $date[2];
	$FH = fopen( $url, "r" );
	if ( !$FH )  {
		do_log( 'Error: get_geomag_data(): ' . ERR_GEOM_FILE_RETR );
		return ERR_FRETR;
	}
	while ( !feof($FH) )  {
		$line = fgets( $FH, 1024 );
		if ( preg_match( '/^Geomagnetic Kp:/', $line) )  {
			$geodata['geomag_kp'] = preg_replace( '/^Geomagnetic Kp:[\s\t]+/', '', $line );
			$geodata['geomag_kp'] = trim( $geodata['geomag_kp'] );
		}
		elseif ( preg_match( '/^Geomagnetic Ap:/', $line) )  {
			$geodata['geomag_ap'] = preg_replace( '/^Geomagnetic Ap:[\s\t]+/', '', $line );
			$geodata['geomag_ap'] = trim( $geodata['geomag_ap'] );
		}
		elseif ( preg_match( '/^Geomagnetic Summary:/', $line) )  {
			$geodata['geomag_summary'] = preg_replace( '/^Geomagnetic Summary:[\s\t]+/', '', $line );
			$geodata['geomag_summary'] = trim( $geodata['geomag_summary'] );
		}
		elseif ( preg_match( '/^Geomagnetic List:/', $line) )  {
			$geodata['geomag_list'] = preg_replace( '/^Geomagnetic List:[\s\t]+/', '', $line );
			$geodata['geomag_list'] = trim( $geodata['geomag_list'] );
		}
		elseif ( preg_match( '/^Geomagnetic Plot:/', $line) )  {
			$geodata['geomag_plot'] = preg_replace( '/^Geomagnetic Plot:[\s\t]+/', '', $line );
			$geodata['geomag_plot'] = trim( $geodata['geomag_plot'] );
		}
	}
	fclose( $FH );

	return $geodata;
 }


 



?>
