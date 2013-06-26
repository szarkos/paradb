<?php

 //-------------------------------------------------------------------//
 //
 // AGHOST Paranormal Reporting Database (ParaDB)
 // Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>
 //
 // See the file COPYING for terms of use and redistribution.
 //
 // File: reports_print.php
 //	  This file contains several report-related data output
 //	  functions.
 //
 //-------------------------------------------------------------------//

 // File: reports_print.php


 // Function: print_equip_form()
 // Print the equipment lists that are displayed on the report form.
 function print_equip_form ( &$VALID_FIELDS, $cols=3 )  {

	print "<table cellspacing=\"0\" cellpadding=\"0\" height=\"100%\" align=\"center\">\n";
	print "  <tr>\n";

	$tr_count = 0;
	$count = 0;

	// We need to manually count the number of boolean values in array.
	// Values like case_id, owner_id, and *_equip_misc don't count.
	foreach ( array_keys($VALID_FIELDS) as $key )  {
		if ( isset($VALID_FIELDS[$key]['ischecked']) )  {
			$count++;
		}
	}
	$max_rows = ceil( $count/$cols );

	foreach ( array_keys($VALID_FIELDS) as $key )  {
		if ( !isset($VALID_FIELDS[$key]['ischecked']) )  {
			// We only want boolean (checkbox) values.
			continue;
		}
		if ( $tr_count == $max_rows )  {
			$tr_count = 0;
			print "</table>\n</td>\n";
		}
		if ( $tr_count == 0 )  {
			print "<td class=\"equip_tab\">\n";
			print "<table cellspacing=\"2\" cellpadding=\"0\" class=\"equip_form\">\n";
		}
		print "  <tr>\n";
		print "    <td id=\"label1\"> ";
		print eval("print " . $VALID_FIELDS[$key]['lang'] . ";") . " </td>\n";
		print "    <td id=\"value1\"> <input type=\"checkbox\" name=\"" . $key . "\" value=\"1\""; // value is always "1".
		if ( strtolower($VALID_FIELDS[$key]['ischecked']) == 'yes' || $VALID_FIELDS[$key]['ischecked'] == "1" )  {
			print " CHECKED ";
		}
		print "> </td>\n";
		print "  </tr>\n";
		$tr_count++;
	}
	print "    </table>\n";
	print "  </td>\n";
	print "</tr>\n";
	print "</table>\n";

	return 0;
 }



 // Function: print_equip_misc_fields( [equip_field], [equip_misc_data] )
 // This prints the misc. input fields for the various $*_EQUIPMENT arrays.
 function print_equip_misc_fields( $equip_field, $equip_misc_data )  {

	global $TECH_EQUIPMENT;
	$field_num = 0;
	$equip_field = preg_replace('/_+$/', '', $equip_field );

	for ( $i=1; $i<6; $i++ )  {
		print "<div id=\"" . $equip_field . "_div_" . $i . "\"";
		if ( $i != 1 )  {
			print " class=\"equip_input\"";
		}
		print ">\n";
		print "  <input type=\"text\" size=\"20\" name=\"" . $equip_field . "_misc[" . $field_num . "]\" value=\"" . $equip_misc_data[$field_num] . "\" maxlength=\"" . $TECH_EQUIPMENT['tech_equip_misc']['maxlength'] . "\" style=\"width:49%\"> \n";
		print "  <input type=\"text\" size=\"20\" name=\"" . $equip_field . "_misc[" . ($field_num+1) . "]\" value=\"" . $equip_misc_data[$field_num+1] . "\" maxlength=\"" . $TECH_EQUIPMENT['tech_equip_misc']['maxlength'] . "\" style=\"width:49%\"> \n";
		print "</div>\n";
		$field_num += 2;
	}

	return 0;
 }



 // Function: print_reports( [owner_id] )
 // Output list of owner_id's reports.
 function print_reports( $owner_id="", $limit=0 )  {

	global $REPORTSDB;

	if ( empty($owner_id) )  {
		return ERR_UNDEF;
	}
	if ( empty($limit) || $limit == 0 )  {
		$limit = 1000;
	}

	$reports = list_reports( $owner_id, $limit );
	foreach ( array_keys($reports) as $key )  {

		// Case Title.
		if ( $reports[$key]['case_title'] != "" )  {
			print "<a href=\"" . $REPORTSDB . "?task=view_case&case_id=" . $key . "\">" . $key . "</a>: \n";
			print "<span class=\"report_title\">" . $reports[$key]['case_title'] . " </span> <br />";
		}
		else  {
			print "<a href=\"" . $REPORTSDB . "?task=view_case&case_id=" . $key . "\">" . $key . "</a> <br />\n";
		}

		// Last Edited Date.
		print "<strong>" . REPORT_LAST_EDITED . "</strong> " . $reports[$key]['last_edited'] . " <br />\n";

		// Description.
		if ( $reports[$key]['description'] != "" )  {
			print "<div style=\"padding-left: 10px;\">\n";
			if ( strlen($reports[$key]['description']) > 120 )  {
				$reports[$key]['description'] = substr($reports[$key]['description'], 0, 120);
				$reports[$key]['description'] .= '...';
			}
			print $reports[$key]['description'] . "\n";
			print "</div>\n";
		}

		// Report Functions.
		if ( isset($_REQUEST['task']) && $_REQUEST['task'] == 'reports_view_all' )  {
			$view_report = TASK_VIEW_REPORT_LONG;
			$edit_report = TASK_EDIT_REPORT_LONG;
			$delete_report = TASK_DELETE_REPORT_LONG;
			$publish_report = TASK_PUBLISH_REPORT_LONG;
			$unpublish_report = TASK_UNPUBLISH_REPORT_LONG;
		}
		else  {
			$view_report = TASK_VIEW_REPORT;
			$edit_report = TASK_EDIT_REPORT;
			$delete_report = TASK_DELETE_REPORT;
			$publish_report = TASK_PUBLISH_REPORT;
			$unpublish_report = TASK_UNPUBLISH_REPORT;
		}

		print "<p><div align=\"center\">\n";
		if ( report_check_perms('r', $key, $owner_id) == 0 )  {
			print "<a href=\"" . $REPORTSDB . "?task=view_report&case_id=" . $key . "&owner_id=" . $owner_id . "\">" . $view_report . "</a>";
		}
		if ( report_check_perms('w', $key, $owner_id) == 0 )  {
			print " | <a href=\"" . $REPORTSDB . "?task=edit_report&case_id=" . $key . "&owner_id=" . $owner_id . "\">" . $edit_report . "</a>";
			if ( report_ispublished( $key, $_SESSION['user_id'] ) == 0 )  {
				print " | ";
				print "<a href=\"" . $REPORTSDB . "?task=report_unpublish&case_id=" . $key . "&owner_id=" . $owner_id . "\">" . $unpublish_report . "</a>";
			}
			else  {
				print " | ";
				print "<a href=\"" . $REPORTSDB . "?task=report_publish&case_id=" . $key . "&owner_id=" . $owner_id . "\">" . $publish_report . "</a>";
			}
		}
		if ( report_check_perms('d', $key, $owner_id) == 0 )  {
			print " | <a href=\"" . $REPORTSDB . "?task=delete_report&case_id=" . $key . "&owner_id=" . $owner_id . "&confirm_delete=0\">" . $delete_report . "</a> <br />\n";
		}
		print "</div>\n";
		print "<div class=\"separator\"></div>\n";
	}

	return 0;
 }



 // Function: print_unfinished_reports( [owner_id] )
 // Output list of unfinished reports.
 function print_unfinished_reports( $owner_id="" )  {

	global $REPORTSDB;

	if ( empty($owner_id) )  {
		return ERR_UNDEF;
	}

	$unfinished = list_unfinished_reports( $_SESSION['user_id'] );
	foreach ( array_keys($unfinished) as $key )  {
		if ( report_check_perms('w', $key, $owner_id) != 0 )  {
			// Don't list report if the user cannot edit or create one.
			continue;
		}

		print "<a href=\"" . $REPORTSDB . "?task=view_case&case_id=" . $key . "\">" . $key . "</a>: ";
		if ( !empty($unfinished[$key]['investigation_title']) )  {
			if ( strlen($unfinished[$key]['investigation_title']) > 30 )  {
				$unfinished[$key]['investigation_title'] = substr( $unfinished[$key]['investigation_title'], 0, 30 );
				$unfinished[$key]['investigation_title'] .= '...';
			}
			print ' <strong>' . $unfinished[$key]['investigation_title'] . "</strong> <br />\n";
		}

		if ( !empty($unfinished[$key]['city']) )  {
			print '<strong>' . ucfirst($unfinished[$key]['city']) . '</strong>';
		}
		if ( !empty($unfinished[$key]['state']) )  {
			print ", <strong>" . strtoupper($unfinished[$key]['state']) . "</strong>\n";
		}
		print " (" . $unfinished[$key]['date'] . ") <br />\n";

		if ( !empty($unfinished[$key]['description']) )  {
			print "<div style=\"padding-left: 10px;\">\n";
			if ( strlen($unfinished[$key]['description']) > 120 )  {
				$unfinished[$key]['description'] = substr($unfinished[$key]['description'], 0, 120);
				$unfinished[$key]['description'] = $unfinished[$key]['description'] . '...';
			}
			print "</div>\n";
		}
		print  $unfinished[$key]['description'] . "\n";

		print "<p>\n";

		print "<div align=\"center\">\n";
		print "<a href=\"" . $REPORTSDB . "?task=view_case&case_id=" . $key . "\">" . TASK_VIEW_CASE . "</a>";
		if ( case_check_perms('w', $key) == 0 )  {
			print " | <a href=\"" . $REPORTSDB . "?task=edit_case&case_id=" . $key . "\">" . TASK_EDIT_CASE . "</a>";
		}
		print " | <a href=\"" . $REPORTSDB . "?task=edit_report&case_id=" . $key . "&owner_id=" . $_SESSION['user_id'] . "\">" . TASK_WRITE_REPORT . "</a>\n";
		print "</div>\n";
		print "<div class=\"separator\"></div>\n";
	}

	return 0;
 }


 // Function: print_newest_reports()
 // Output list of newest (recently edited) reports.
 function print_newest_reports()  {

	global $db_connection;
	global $REPORTSDB, $TITLE;
	global $HOME_LIST_LIMIT;
        global $LOGIN_DB_ID_COL;
        global $LOGIN_DB_USER_COL;
        global $LOGIN_DB_NAME_COL;

	$limit = "0," . $HOME_LIST_LIMIT;
	$reports = mysql_get_rows( 'reportsdb_reports', 'case_id,owner_id,case_title,description,report_edit_date', 'report_state=published AND report_edit_date!=0000-00-00', 'report_edit_date DESC', $limit, $db_connection );
	if ( mysql_num_rows($reports) == 0 )  {
		return 0;
	}

	// This is for the username links below ("Published By").
	print "<script language=\"JavaScript\">\n";
	print "// <!--\n";
	print "  // _parent doesn't work with the forms on the user_stats popup window, so I'm setting this directly.\n";
	print "  window.name='" . $TITLE . "';\n";
	print "// -->\n";
	print "</script>\n";

	// Print out the reports list.
	while ( $row = mysql_fetch_row($reports) )  {
		$case_id = $row[0];
		$owner_id = $row[1];
		$case_title = $row[2];
		$description = $row[3];
		$last_edited = preg_replace( '/\s+/', ', ', $row[4] );

		if ( report_check_perms( 'r', $case_id, $owner_id ) != 0 )  {
			continue;
		}
		$userinfo = get_userinfo( $owner_id );

		// Case Title.
		if ( !empty($case_title) )  {
			if ( strlen($case_title) > 45 )  {
				$case_title = substr( $case_title, 0, 55 ) . '...';
			}
			print "<a href=\"" . $REPORTSDB . "?task=view_case&case_id=" . $case_id . "\">" . $case_id . "</a>: ";
			print "<span class=\"report_title\"> <a href=\"" . $REPORTSDB . "?task=view_report&case_id=" . $case_id . "&owner_id=" . $owner_id . "\">" . $case_title . "</a> </span> <br />";
		}
		else  {
			print "<a href=\"" . $REPORTSDB . "?task=view_case&case_id=" . $case_id . "\">" . $case_id . "</a>: ";
			print "<span class=\"report_title\"> <a href=\"" . $REPORTSDB . "?task=view_report&case_id=" . $case_id . "&owner_id=" . $owner_id . "\">View Report</a> </span> <br />";
		}

		// Published By
		print "<strong>" . REPORT_PUBLISHED_BY . "</strong> " . $userinfo[$LOGIN_DB_NAME_COL] . " ";
		print "(<a href=\"javascript:void(0)\" onclick=\"window.open('" . $REPORTSDB . "?task=showstats&user_id=" . $userinfo[$LOGIN_DB_ID_COL] . "','" . $TITLE . " -- " . $userinfo[$LOGIN_DB_USER_COL] . "','height=250,width=450,toolbar=no,menubar=no,scrollbars=yes,resizable=yes,location=no,directories=no,status=no')\">" . $userinfo[$LOGIN_DB_USER_COL] . "</a>) <br />\n";

		// Last Edited.
		print "<strong>" . REPORT_LAST_EDITED . "</strong> " . $last_edited . " <br />\n";

		// Description.
		if ( !empty($description) )  {
			print "<div style=\"padding-left: 10px;\">\n";
			if ( strlen($description) > 120 )  {
				$description = substr( $description, 0, 120 );
				$description .= '...';
			}
			print $description . "\n";
			print "</div>\n";
		}

		print "<div class=\"separator\"></div>\n";
	}

	return 0;
 }





?>
