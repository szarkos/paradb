<?php

 //-------------------------------------------------------------------//
 //
 // AGHOST Paranormal Reporting Database (ParaDB)
 // Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>
 //
 // See the file COPYING for terms of use and redistribution.
 //
 // File: stats_plot.php
 //	  Generate plots and stuff.
 //
 //-------------------------------------------------------------------//



 // Function: print_geomag_plot()
 // Prints geomagnetic data plot.
 function print_geomag_plot()  {

	global $server_path;

	if ( set_paradb_include_path() != 0 )  {
		return ERR_UNDEF;
	}
	if ( !@(include_once('PEAR/Image/Graph.php')) )  {
		if ( !@(include_once($server_path . 'PEAR/Image/Graph.php')) )  {
			do_log( 'Error: print_geomag_plot(): Image/Graph.php not available. You may need to install the PEAR Image_Graph module: http://pear.php.net/package/Image_Graph' );
			return ERR_UNDEF;
		}
	}


	// Check if our output image is writable.
	$output_dir = $server_path . '/images/stats_images/';
	$output_image = $output_dir . 'geomag_stats.png';
	if ( file_exists($output_image) )  {
		if ( !is_writable($output_image) )  {
			do_log( 'Error: print_geomag_plot(): File $output_image is not writeable.' );
			return ERR_FWRITE;
		}
	}
	else  {
		if ( !is_writable($output_dir) )  {
			do_log( 'Error: print_geomag_plot(): Directory $output_dir is not writeable.' );
			return ERR_FWRITE;
		}
	}


	// Call geomag_stats() to get statistics from database.
	$geomag_stats = geomag_stats();	
	$cnt = 0;
	foreach ( array_keys($geomag_stats) as $status )  {
		// Let's see if any of the elements are >0.
		if ( $geomag_stats[$status] > 0 )  {
			$cnt = 1;
			break;
		}
	}
	if ( $cnt == 0 )  {
		print "<p>Sorry, no geomagnetic information available.\n</p>";
		if ( file_exists($output_image) )  {
			unlink( $output_image );
		}
		return 0;
	}

	return create_pie_chart( $geomag_stats, 'Geomagnetic Activity' );
 }



 // Function: print_xray_plot()
 // Prints xray data plot.
 function print_xray_plot()  {

	global $server_path;

	if ( set_paradb_include_path() != 0 )  {
		return ERR_UNDEF;
	}
	if ( !@(include_once('PEAR/Image/Graph.php')) )  {
		if ( !@(include_once($server_path . 'PEAR/Image/Graph.php')) )  {
			do_log( 'Error: print_geomag_plot(): Image/Graph.php not available. You may need to install the PEAR Image_Graph module: http://pear.php.net/package/Image_Graph' );
			return ERR_UNDEF;
		}
	}


	// Check if our output image is writable.
	$output_dir = $server_path . '/images/stats_images/';
	$output_image = $output_dir . 'xray_stats.png';
	if ( file_exists($output_image) )  {
		if ( !is_writable($output_image) )  {
			do_log( 'Error: print_xray_plot(): File $output_image is not writeable.' );
			return ERR_FWRITE;
		}
	}
	else  {
		if ( !is_writable($output_dir) )  {
			do_log( 'Error: print_xray_plot(): Directory $output_dir is not writeable.' );
			return ERR_FWRITE;
		}
	}


	// Call xray_stats() to get statistics from database.
	$xray_stats = xray_stats();	
	$cnt = 0;
	foreach ( array_keys($xray_stats) as $status )  {
		// Let's see if any of the elements are >0.
		if ( $xray_stats[$status] > 0 )  {
			$cnt = 1;
			break;
		}
	}
	if ( $cnt == 0 )  {
		print "<p>Sorry, no xray information available.</p>\n";
		if ( file_exists($output_image) )  {
			unlink( $output_image );
		}
		return 0;
	}

	return create_pie_chart( $xray_stats, 'Xray Activity' );
 }



 // Function: create_pie_chart()
 function create_pie_chart( $data=array(), $title='' )  {

	if ( !is_array($data) || empty($data) )  {
		return 0;
	}

	// Create the graph.
	$Graph =& Image_Graph::factory( 'graph', array(400, 300) );

	$Font =& $Graph->addNew( 'font', 'LiberationMono-Regular' );
	$Font->setSize( 8 );

	$Graph->setFont( $Font );

	// create the plotarea
	$Graph->add(
		Image_Graph::vertical(
			Image_Graph::factory('title', array($title, 12)),
			Image_Graph::horizontal(
				$Plotarea = Image_Graph::factory('plotarea'),
				$Legend = Image_Graph::factory('legend'),
				70
			),
			5
		)
	);

	$Legend->setPlotarea( $Plotarea );

	// Create the 1st dataset
	$Dataset =& Image_Graph::factory( 'dataset' );

	foreach ( array_keys($data) as $key )  {
		if ( $data[$key] != 0 )  {
			$Dataset->addPoint( $key, $data[$key] );
		}
	}

	// Create the 1st plot as smoothed area chart using the 1st dataset
	$Plot =& $Plotarea->addNew( 'pie', array(&$Dataset) );
	$Plotarea->hideAxis();

	// Create a Y data value marker
	$Marker =& $Plot->addNew( 'Image_Graph_Marker_Value', IMAGE_GRAPH_PCT_Y_TOTAL );

	// Create a pin-point marker type
	$PointingMarker =& $Plot->addNew( 'Image_Graph_Marker_Pointing_Angular', array(20, &$Marker) );

	// and use the marker on the 1st plot
	$Plot->setMarker( $PointingMarker );

	// Format value marker labels as percentage values
	$Marker->setDataPreprocessor( Image_Graph::factory('Image_Graph_DataPreprocessor_Formatted', '%0.1f%%') );

	$Plot->Radius = 3;

	$FillArray =& Image_Graph::factory( 'Image_Graph_Fill_Array' );
	$Plot->setFillStyle( $FillArray );

	$FillArray->addColor( 'green@0.8' );
	$FillArray->addColor( 'blue@0.8' );
	$FillArray->addColor( 'yellow@0.8' );
	$FillArray->addColor( 'red@0.8' );
	$FillArray->addColor( 'orange@0.8' );
	$FillArray->addColor( 'brown@0.8' );

	$Plot->explode( 5 );
	$Plot->setStartingAngle( 90 );

	// Output the Graph.
	$Graph->done( array('filename' => $output_image) );

	return 0;
 }




?>
