<?php

 //-------------------------------------------------------------------//
 //
 // AGHOST Paranormal Reporting Database (ParaDB)
 // Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>
 //
 // See the file COPYING for terms of use and redistribution.
 //
 // File: stats_print.php
 //	  Print out parts of the statistics pages.
 //
 //-------------------------------------------------------------------//


 // Function print_user_stats_list()
 // Print user list with reports/cases stats.
 function print_user_stats_list()  {

	global $REPORTSDB, $TITLE;

	$active_users = array();
	$blocked_users = array();
	$users = get_user_stats();
	foreach ( array_keys($users) as $key )  {
		if ( $users[$key]['block'] == 1 )  {
			$title = "User_Stats_" . $users[$key]['name'];
			$blocked_users[$users[$key]['name']] = "<a href=\"javascript:void(0);\" onclick=\"window.open('" . $REPORTSDB . "?task=showstats&user_id=" . $users[$key]['id'] . "','" . $title . "','height=300,width=450,toolbar=no,menubar=no,scrollbars=yes,resizable=yes,location=no,directories=no,status=no')\">" . $users[$key]['name'] . "</a> (" . $users[$key]['reports'] . '/' . $users[$key]['data'] . '/' . $users[$key]['cases'] . ") <br />\n";
		}
		else  {
			$title = "User_Stats_" . $users[$key]['name'];
			$active_users[$users[$key]['name']] = "<a href=\"javascript:void(0);\" onclick=\"window.open('" . $REPORTSDB . "?task=showstats&user_id=" . $users[$key]['id'] . "','" . $title . "','height=300,width=450,toolbar=no,menubar=no,scrollbars=yes,resizable=yes,location=no,directories=no,status=no')\">" . $users[$key]['name'] . "</a> (" . $users[$key]['reports'] . '/' . $users[$key]['data'] . '/' . $users[$key]['cases'] . ") <br />\n";
		}
	}
	ksort( $active_users );
	foreach ( array_keys($active_users) as $key )  {
		print $active_users[$key];
	}
	$active_users = array();

	if ( !empty($blocked_users) )  {
		ksort( $blocked_users );
		print "<br /><br />\n";
		print "<a name=\"block\" />\n";
		print "<a href=\"#block\" onClick=\"JavaScript:showhide('blocked');\" style=\"font-weight:bold\">" . STATS_SHOW_BLOCKED . "</a> <br />\n";
		print "<div id=\"blocked\" style=\"display:none;\">\n";
		foreach ( array_keys($blocked_users) as $key )  {
			print "<div style=\"padding-left:4px\">" . $blocked_users[$key] . "</div>";
		}
		print "</div>\n";
	}
	$blocked_users = array();

	print "<br /><br />\n";
	print "<script language=\"JavaScript\">\n";
	print "// <!--\n";
	print "  // _parent doesn't work with the forms on the user_stats popup window, so I'm setting this directly.\n";
	print "  window.name='" . $TITLE . "';\n";
	print "// -->\n";
	print "</script>\n";

	return 0;
}


 // Function print_user_stats( [user_id] )
 // Print stats for a particular user.
 function print_user_stats( $user_id="" )  {

	global $REPORTSDB, $TITLE;

	if ( empty($user_id) )  {
		return ERR_UNDEF;
	}

	print " <html>\n";
	print "  <head>\n";
	print "    <title>" . $TITLE . "</title>\n";
	print "    <link rel=\"stylesheet\" href=\"template/template.css\" type=\"text/css\">\n";
	print "  </head>\n";
	print "  <body>\n";

	$published = 0;
	$unpublished = 0;
	$total_reports = list_reports( $user_id );
	foreach ( array_keys($total_reports) as $report )  {
		if ( $total_reports[$report]['report_state'] == 'published' )  {
			$published++;
		}
		elseif ( $total_reports[$report]['report_state'] == 'unpublished' )  {
			$unpublished++;
		}
	}

	$user = get_user_stats( $user_id );
	foreach ( array_keys($user) as $key )  {

		print "<table cellpadding=\"0\" cellspacing=\"0\" align=\"center\" class=\"stats_user_info\">\n";

                print "<div align=\"center\"><h3> " . $user[$key]['name'] . " </h3></div>\n";

		// User ID.
		print "<tr>\n";
		print " <td id=\"label\"> <strong>User_ID:</strong> </td>\n";
		print " <td id=\"value\"> " . $user[$key]['id'] . " </td>\n";
		print "</tr>\n";

		// Real Name.
		print "<tr>\n";
		print " <td id=\"label\"> <strong>Name:</strong> </td>\n";
		print " <td id=\"value\"> " . $user[$key]['name'] . " </td>\n";
		print "</tr>\n";

		// Username.
		print "<tr>\n";
		print " <td id=\"label\"> <strong>Username:</strong> </td>\n";
		print " <td id=\"value\"> " . $key . " </td>\n";
		print "</tr>\n";

		// Email Address.
		print "<tr>\n";
		print " <td id=\"label\"> <strong>Email:</strong> </td>\n";
		print " <td id=\"value\"> <a href=\"mailto:" . $user[$key]['email'] . "\">" . $user[$key]['email'] . " </td>\n";
		print "</tr>\n";
                print "</table>\n";

		print "<p> \n";
		print "<table cellpadding=\"0\" cellspacing=\"0\" align=\"center\" class=\"stats_user_info\">\n";
		print "<tr>\n";

		print " <td id=\"label\" align=\"center\"> <strong> Total Cases </strong> </td>\n";
		print " <td id=\"label\" align=\"center\"> <strong> Total Reports </strong> </td>\n";
		print " <td id=\"label\" align=\"center\"> <strong> Unfinished Reports </strong> </td>\n";

		print " </tr><tr> \n";


		// Total Cases.
		print " <td valign=\"top\" id=\"value\"> Total: " . $user[$key]['cases'];
		$cases = list_cases( $user[$key]['id'] );
		if ( count($cases) > 0 )  {
			print " <br /> \n";
			print " <form action=\"". $REPORTSDB . "\" method=\"post\" target=\"" . $TITLE . "\"> \n";
			print "   <input type=\"hidden\" name=\"task\" value=\"view_case\" /> \n";
			print "   <input type=\"hidden\" name=\"owner_id\" value=\"" . $user[$key]['id'].  "\" /> \n";
			print "   <select name=\"case_id\" onchange=\"JavaScript:submit();\">\n";
			print "     <option>\n";
			foreach ( array_keys($cases) as $case )  {
				print "<option value=\"" . $case . "\">" . $case . "\n";
			}
			print "   </select>\n";
			print " </form>\n";
			print " <br />\n";
			print "<a href=\"" . $REPORTSDB . "?task=cases_view&owner_id=" . $user[$key]['id'] . "\" target=\"" . $TITLE . "\">View All</a>\n";
		}
		print " </td>";


		// Total Reports.
		print " <td valign=\"top\" id=\"value\"> Total: " . $user[$key]['reports'];
		if ( count($total_reports) > 0 )  {
			print " <br /> \n";
			print " <form action=\"". $REPORTSDB . "\" method=\"post\" target=\"" . $TITLE . "\"> \n";
			print "   <input type=\"hidden\" name=\"task\" value=\"view_report\" /> \n";
			print "   <input type=\"hidden\" name=\"owner_id\" value=\"" . $user[$key]['id'].  "\" /> \n";
			print "   <select name=\"case_id\" onchange=\"JavaScript:submit();\">\n";
			print "     <option>\n";
			foreach ( array_keys($total_reports) as $report )  {
				print "<option value=\"" . $report . "\">" . $report . "\n";
			}
			print "   </select>\n";
			print " </form>\n";
			print " <br />\n";
			print "<a href=\"" . $REPORTSDB . "?task=reports_view_all&owner_id=" . $user[$key]['id'] . "\" target=\"" . $TITLE . "\">View All</a>\n";
		}
		print " </td>";


		// Unfinished Reports.
		print " <td valign=\"top\" id=\"value\"> Total: " . count(list_unfinished_reports($user[$key]['id']));
		$unfinished = list_unfinished_reports( $user_id );
		if ( count($unfinished) > 0 )  {
			print " <br />\n";
			print " <form action=\"". $REPORTSDB . "\" method=\"post\" target=\"" . $TITLE . "\"> \n";
			print "   <input type=\"hidden\" name=\"task\" value=\"view_case\" /> \n";
			print "   <input type=\"hidden\" name=\"owner_id\" value=\"" . $user[$key]['id'].  "\" /> \n";
			print "   <select name=\"case_id\" onchange=\"JavaScript:submit();\">\n";
			print "     <option>\n";
			foreach ( array_keys($unfinished) as $report )  {
				print "<option value=\"" . $report . "\">" . $report . "\n";
			}
			print "   </select>\n";
			print " </form>\n";
		}
		print " </td>";

		print "</tr></table>\n";
	}

	print "<p> &nbsp; </p>\n";

	global $ADMIN_EMAIL;
	print "<div align=\"center\" style=\"padding-top:20px;\">\n";
	print POWERED . "<br />\n";
	print "Copyright &copy; " . date("Y") . " <a href=\"mailto:" . $ADMIN_EMAIL . "\" style=\"text-decoration:none;\">Stephen A. Zarkos</a> <br />\n";
	print "</div>\n";
	print "</body></html>\n";

	return 0;
}







?>
