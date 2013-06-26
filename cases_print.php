<?php

 //-------------------------------------------------------------------//
 //
 // AGHOST Paranormal Reporting Database (ParaDB)
 // Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>
 //
 // See the file COPYING for terms of use and redistribution.
 //
 // File: cases_print.php
 //	  This file contains several case-related data output functions.
 //
 //-------------------------------------------------------------------//


 // Function: print_cases( [user_id] )
 // Print list of all cases that this user has been on.
 function print_cases( $user_id="", $limit=0 )  {

	global $REPORTSDB;

	if ( empty($user_id) )  {
		return ERR_UNDEF;
	}
	if ( empty($limit) || $limit == 0 )  {
		$limit = 1000;
	}

	$count = 1;
	$cases = list_cases( $user_id );
	foreach ( array_keys($cases) as $key )  {
		$report_exists = 0;
		if ( report_exists($key, $user_id) == 0 )  {
			$report_exists = 1;
		}

		// Case ID.
		print "<a href=\"" . $REPORTSDB . "?task=view_case&case_id=" . $key . "\">" . $key . "</a>: \n";

		// Investigation Title.
		if ( !empty($cases[$key]['investigation_title']) )  {
			if ( strlen($cases[$key]['investigation_title']) > 35 )  {
				$cases[$key]['investigation_title'] = substr($cases[$key]['investigation_title'], 0, 35);
				$cases[$key]['investigation_title'] .= '...';
			}
			print "<strong>" . $cases[$key]['investigation_title'] . "</strong> <br />\n";
		}
		else  {
			print "<strong> &lt;No Title&gt; </strong> <br />\n";
		}

		// Date.
		if ( !empty($cases[$key]['date']) )  {
			print $cases[$key]['date'] . " <br />\n";
		}

		// Description.
		if ( !empty($cases[$key]['description']) )  {
			if ( strlen($cases[$key]['description']) > 120 )  {
				$cases[$key]['description'] = substr($cases[$key]['description'], 0, 120);
				$cases[$key]['description'] .= '...';
			}
			print "<div style=\"padding-left:10px;\">\n";
			print $cases[$key]['description'] . "\n";
			print "</div>\n";
		}

		// Report Submitted.
		print STATS_REPORT_SUBMITTED . " ";
		if ( $report_exists == 1 )  {
			print "<span style=\"color:green\">YES</span>\n";
		}
		else  {
			print "<span style=\"color:#ff0000\">NO</span>\n";
		}
		print " <br />\n";

		// Data Submitted.
		print STATS_DATA_SUBMITTED . " ";
		if ( $cases[$key]['data'] == 1 )  {
			print "<span style=\"color:green\">YES</span>\n";
		}
		else  {
			print "<span style=\"color:#ff0000\">NO</span>\n";
		}

		print "<p>\n";

		// Case Functions.
		print "<div align=\"center\">\n";
		print "<a href=\"" . $REPORTSDB . "?task=view_case&case_id=" . $key . "\">" . TASK_VIEW_CASE . "</a>";
		if ( case_check_perms('w', $key) == 0 )  {
			print " | <a href=\"" . $REPORTSDB . "?task=edit_case&case_id=" . $key . "\">" . TASK_EDIT_CASE . "</a>";

			if ( case_isopen( $key ) == 0 )  {
				print " | ";
				print "<a href=\"" . $REPORTSDB . "?task=close_case&case_id=" . $key . "\">" . TASK_CLOSE_CASE . "</a>";
			}
			else  {
				print " | ";
				print "<a href=\"" . $REPORTSDB . "?task=open_case&case_id=" . $key . "\">" . TASK_OPEN_CASE . "</a>";
			}
		}
		if ( case_check_perms('d', $key) == 0 )  {
			print " | <a href=\"" . $REPORTSDB . "?task=delete_case&case_id=" . $key . "&confirm_delete=0\">" . TASK_DELETE_CASE . "</a> <br />\n";
		}
		print "</div>\n";

		// Report Functions.
		if ( $report_exists == 1 )  {
			print "<div align=\"center\">\n";
			if ( report_check_perms('r', $key, $user_id) == 0 )  {
				print "<a href=\"" . $REPORTSDB . "?task=view_report&case_id=" . $key . "&owner_id=" . $user_id . "\">" . TASK_VIEW_REPORT_LONG . "</a>";
			}

			if ( report_check_perms('w', $key, $user_id) == 0 )  {
				print " | <a href=\"" . $REPORTSDB . "?task=edit_report&case_id=" . $key . "&owner_id=" . $user_id . "\">" . TASK_EDIT_REPORT_LONG . "</a>";

				if ( report_ispublished( $key, $user_id ) == 0 )  {
					print " | ";
					print "<a href=\"" . $REPORTSDB . "?task=report_unpublish&case_id=" . $key . "&owner_id=" . $user_id . "\">" . TASK_UNPUBLISH_REPORT_LONG . "</a>";
				}
				else  {
					print " | ";
					print "<a href=\"" . $REPORTSDB . "?task=report_publish&case_id=" . $key . "&owner_id=" . $user_id . "\">" . TASK_PUBLISH_REPORT_LONG . "</a>";
				}
			}
			if ( report_check_perms('d', $key, $user_id) == 0 )  {
				print " | <a href=\"" . $REPORTSDB . "?task=delete_report&case_id=" . $key . "&owner_id=" . $user_id . "&confirm_delete=0\">" . TASK_DELETE_REPORT_LONG . "</a> <br />\n";
			}
			print "</div>\n";
		}

		print "<div class=\"separator\"></div>\n";

		if ( ++$count >= $limit )  {
			break;
		}
	}

	return 0;
 }



 // Function: print_cases_all()
 // Print list of all cases.
 function print_cases_all( $limit=0 )  {

	global $REPORTSDB;

	if ( empty($limit) || $limit == 0 )  {
		$limit = 1000;
	}

	$count = 1;
	$cases = list_cases_all();
	foreach ( array_keys($cases) as $key )  {

		// Case ID.
		print "<a href=\"" . $REPORTSDB . "?task=view_case&case_id=" . $key . "\">" . $key . "</a>: \n";

		// Investigation Title.
		if ( !empty($cases[$key]['investigation_title']) )  {
			if ( strlen($cases[$key]['investigation_title']) > 35 )  {
				$cases[$key]['investigation_title'] = substr( $cases[$key]['investigation_title'], 0, 35 );
				$cases[$key]['investigation_title'] .= '...';
			}
			print "<strong>" . $cases[$key]['investigation_title'] . "</strong> <br />\n";
		}
		else  {
			print "<strong> &lt;No Title&gt; </strong> <br />\n";
		}

		// Description.
		if ( !empty($cases[$key]['description']) )  {
			if ( strlen($cases[$key]['description']) > 120 )  {
				$cases[$key]['description'] = substr( $cases[$key]['description'], 0, 120 );
				$cases[$key]['description'] .= '...';
			}
			print $cases[$key]['description'] . " <br />\n";
		}
		else  {
			print "<br />\n";
		}

		// Date.
		if ( !empty($cases[$key]['date']) )  {
			print $cases[$key]['date'] . " <br />\n";
		}

		print "<p>\n";

		// Case Functions.
		print "<div align=\"center\">\n";
		print "<a href=\"" . $REPORTSDB . "?task=view_case&case_id=" . $key . "\">" . TASK_VIEW_CASE . "</a>";
		if ( case_check_perms('w', $key) == 0 )  {
			print " | <a href=\"" . $REPORTSDB . "?task=edit_case&case_id=" . $key . "\">" . TASK_EDIT_CASE . "</a>";

			if ( case_isopen( $key ) == 0 )  {
				print " | ";
				print "<a href=\"" . $REPORTSDB . "?task=close_case&case_id=" . $key . "\">" . TASK_CLOSE_CASE . "</a>";
			}
			else  {
				print " | ";
				print "<a href=\"" . $REPORTSDB . "?task=open_case&case_id=" . $key . "\">" . TASK_OPEN_CASE . "</a>";
			}
		}
		if ( case_check_perms('d', $key) == 0 )  {
			print " | <a href=\"" . $REPORTSDB . "?task=delete_case&case_id=" . $key . "&confirm_delete=0\">" . TASK_DELETE_CASE . "</a> <br />\n";
		}
		print "</div>\n";
		print "<div class=\"separator\"></div>\n";

		if ( ++$count >= $limit )  {
			break;
		}
	}

	return 0;
 }



 // Function: print_mycases( [owner_id] )
 // Output list of cases owned by $owner_id.  This only includes cases which the
 // $owner_id owns, not necessarily all the cases he/she has been on as an
 // investigator.
 function print_mycases( $owner_id="", $limit=0 )  {

	global $REPORTSDB;

	if ( empty($owner_id) )  {
		return ERR_UNDEF;
	}
	if ( empty($limit) || $limit == 0 )  {
		$limit = 1000;
	}

	$count = 1;
	$cases = list_mycases( $owner_id );
	foreach ( array_keys($cases) as $key )  {

		// Case ID.
		print "<a href=\"" . $REPORTSDB . "?task=view_case&case_id=" . $key . "\">" . $key . "</a>: \n";

		// Investigation Title.
		if ( !empty($cases[$key]['investigation_title']) )  {
			if ( strlen($cases[$key]['investigation_title']) > 35 )  {
				$cases[$key]['investigation_title'] = substr($cases[$key]['investigation_title'], 0, 35);
				$cases[$key]['investigation_title'] .= '...';
			}
			print "<strong>" . $cases[$key]['investigation_title'] . "</strong> <br />\n";
		}
		else  {
			print "<strong> &lt;No Title&gt; </strong> <br />\n";
		}

		// Description.
		if ( !empty($cases[$key]['description']) )  {
			if ( strlen($cases[$key]['description']) > 120 )  {
				$cases[$key]['description'] = substr($cases[$key]['description'], 0, 120);
				$cases[$key]['description'] .= '...';
			}
			print $cases[$key]['description'] . " <br />\n";
		}
		else  {
			print "<br />\n";
		}

		// Date.
		if ( !empty($cases[$key]['date']) )  {
			print $cases[$key]['date'] . " <br />\n";
		}

		print "<p>\n";

		// Case Functions.
		print "<div align=\"center\">\n";
		print "<a href=\"" . $REPORTSDB . "?task=view_case&case_id=" . $key . "\">" . TASK_VIEW_CASE . "</a>";
		if ( case_check_perms('w', $key) == 0 )  {
			print " | <a href=\"" . $REPORTSDB . "?task=edit_case&case_id=" . $key . "\">" . TASK_EDIT_CASE . "</a>";

			if ( case_isopen( $key ) == 0 )  {
				print " | ";
				print "<a href=\"" . $REPORTSDB . "?task=close_case&case_id=" . $key . "\">" . TASK_CLOSE_CASE . "</a>";
			}
			else  {
				print " | ";
				print "<a href=\"" . $REPORTSDB . "?task=open_case&case_id=" . $key . "\">" . TASK_OPEN_CASE . "</a>";
			}
		}
		if ( case_check_perms('d', $key) == 0 )  {
			print " | <a href=\"" . $REPORTSDB . "?task=delete_case&case_id=" . $key . "&confirm_delete=0\">" . TASK_DELETE_CASE . "</a> <br />\n";
		}
		print "</div>\n";

		print "<div class=\"separator\"></div>\n";

		if ( ++$count >= $limit )  {
			break;
		}
	}

	return 0;
 }




?>
