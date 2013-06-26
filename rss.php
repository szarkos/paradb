<?php

 //-------------------------------------------------------------------//
 //
 // AGHOST Paranormal Reporting Database (ParaDB)
 // Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>
 //
 // See the file COPYING for terms of use and redistribution.
 //
 // File: rss.php
 //       Functions related to producing and outputing an RSS feeds.
 //-------------------------------------------------------------------//


 // Function print_rss_feed()
 // Print the RSS feed to the browser.
 function print_rss_feed()  {

	header("Content-type: text/plain");

	$rss_feed = build_rss_feed();
	if ( empty($rss_feed) )  {
		return 0;
	}

	print $rss_feed;

	return 0;
 }



 // Function: write_rss_feed( [$data] )
 // Write RSS feed to $RSS_FILE.
 function write_rss_feed()  {

	$rss_feed = build_rss_feed();

	if ( empty($rss_feed) )  {
		return 0;
	}

	global $RSS_FILE;
	if ( !is_writable($RSS_FILE) )  {
		do_log( "Error: write_rss_feed(): File " . $RSS_FILE . " is not writable." );
		return ERR_FOPEN;
	}
	if ( !$FH = fopen("$RSS_FILE", "w") )  {
		do_log( "Error: write_rss_feed(): Cannot open file " . $RSS_FILE . "." );
		return ERR_FOPEN;
	}
	fwrite( $FH, $rss_feed );
	fclose( $FH );

	return 0;
 }



 // Function: build_rss_feed( [$limit] )
 // Query the database and create the RSS feed.
 // Returns a string containing the RSS feed.
 function build_rss_feed( $limit=10 )  {

	global $ADMIN_EMAIL, $TITLE, $REPORTSDB;

	$pubdate = date("D, j M Y G:i:s T");

	$rss_feed = "<rss version=\"2.0\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">\n";
	$rss_feed .= "<channel>\n";
	$rss_feed .= "<title>" . $TITLE . "</title>\n";
	$rss_feed .= "<link>" . $REPORTSDB . "</link>\n";
	$rss_feed .= "<description></description>\n";
	$rss_feed .= "<language>en-us</language>\n";
	$rss_feed .= "<copyright></copyright>\n";
	$rss_feed .= "<pubDate>" . $pubdate . "</pubDate>\n";
	$rss_feed .= "<lastBuildDate>" . $pubdate ."</lastBuildDate>\n";
	$rss_feed .= "<generator>" . $TITLE . "</generator>\n";

	global $db_connection;
	$reports = mysql_get_rows( 'reportsdb_reports', 'case_id,owner_id,case_title,description,report_edit_date', 'report_state=published AND report_edit_date!=0000-00-00', 'report_edit_date DESC', $limit, $db_connection );
	if ( mysql_num_rows($reports) == 0 )  {
		return 0;
	}

	while ( $row = mysql_fetch_row($reports) )  {
		$case_id = $row[0];
		$owner_id = $row[1];
		$case_title = $row[2];
		$description = $row[3];
		$last_edited_ts = strtotime( $row[4] );

		if ( report_check_perms( 'r', $case_id, $owner_id ) != 0 )  {
			continue;
		}

		// Case title
		if ( !empty($case_title) )  {
			if ( strlen($case_title) > 45 )  {
				$case_title = substr( $case_title, 0, 55 ) . '...';
			}
			$description = preg_replace( '/&/', '&amp;', $description );
			$description = preg_replace( '/</', '&lt;', $description );
			$description = preg_replace( '/>/', '&gt;', $description );
		}

		// Published by
		global $LOGIN_DB_NAME_COL;
		$userinfo = get_userinfo( $owner_id );
		$owner_name = $userinfo[$LOGIN_DB_NAME_COL];

		// Description
		if ( !empty($description) )  {
			if ( strlen($description) > 120 )  {
				$description = substr( $description, 0, 120 );
				$description .= '...';
			}
			$description = preg_replace( '/&/', '&amp;', $description );
			$description = preg_replace( '/</', '&lt;', $description );
			$description = preg_replace( '/>/', '&gt;', $description );
		}

		// Last edited, W3CDTF format for <dc:date>
//		$last_edited = date( "D, j M Y G:i:s T", $last_edited_ts );	// RFC_822
		$last_edited = date( "Y-m-d", $last_edited_ts );
		$last_edited .= 'T';
		$last_edited .= date( "G:i:s", $last_edited_ts );

		// Doing this because date("P") wasn't added until 5.1.3
		$last_edited .= substr( date("O", $last_edited_ts), 0, 3 ) . ':';
		$last_edited .= substr( date("O", $last_edited_ts), 3 );

		$rss_feed .= "\n";
		$rss_feed .= "<item>\n";
		$rss_feed .= "<title>" . $case_id . ": " . $case_title . "</title>\n";
		$rss_feed .= "<link>" . $REPORTSDB . "?task=view_case&amp;case_id=" . $case_id . "</link>\n";
//		$rss_feed .= "<author></author>\n";
//		$rss_feed .= "<pubDate>" . $last_edited . "</pubDate>\n";
		$rss_feed .= "<dc:creator>" . $owner_name . "</dc:creator>\n";
		$rss_feed .= "<dc:date>" . $last_edited . "</dc:date>\n";
		$rss_feed .= "<description>" . $description . "</description>\n";
		$rss_feed .= "</item>\n";
	}

	$rss_feed .= "\n</channel>\n";
	$rss_feed .= "</rss>\n";

	return $rss_feed;
 }




?>
