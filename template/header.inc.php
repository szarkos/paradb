<?php global $REPORTSDB, $TITLE; ?>

<html>
<head>
  <title> <?php print $TITLE; ?> </title>
  <link rel="stylesheet" href="template/template.css" type="text/css" />
  <script type="text/javascript" src="template/js/addEvent.js"></script>
  <script type="text/javascript" src="template/js/sweetTitles.js"></script>
  <script type="text/javascript" src="template/js/misc.js"></script>
  <meta name="software" content="<?php print REPORTS_DB_NAME; ?>" />
  <meta name="version" content="<?php print REPORTS_DB_VERSION; ?>" />
  <meta name="copyright" content="<?php print DEV_NAME . " <" . DEV_EMAIL . ">"; ?>" />
  <meta name="license" content="http://www.gnu.org/licenses/gpl.txt" />
</head>

<!--
	AGHOST Paranormal Reporting Database (ParaDB)
	Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>
	All Rights Reserved.

	http://sourceforge.net/projects/paradb
	http://www.paradb.org/
-->

<body>
<div id="headerdiv">

<div>
  <?php

	if ( !isset($_REQUEST['view']) || $_REQUEST['view'] != 'printable' )  {
		if ( !empty($_SESSION) )  {
			print WELCOME . ', ' . $_SESSION['name'] . '!';
			$stats = get_user_stats( $_SESSION['user_id'] );
			print '<abbr title="' . HELP_USER_STATS . '">' . "\n";
			print ' (' . $stats[$_SESSION['username']]['reports'] . '/' . $stats[$_SESSION['username']]['data'] . '/' . $stats[$_SESSION['username']]['cases'] . ')';
                 	if ( !empty($SYS_ALERTS) ) {
			print $SYS_ALERTS;
			print "</abbr>\n"; }
		}
	}
  ?>
</div>

