<?php
	global $CASE_VALID_FIELDS, $REPORTSDB, $USER_PERMS, $ERR_MSG;
	global $LOGIN_DB_ID_COL, $LOGIN_DB_USER_COL, $LOGIN_DB_NAME_COL;
	$users = listusers();
	if ( empty($CASE_VALID_FIELDS['owner_id']['value']) )  {
		$CASE_VALID_FIELDS['owner_id']['value'] = $_SESSION['user_id'];
	}
	$username = get_username($CASE_VALID_FIELDS['owner_id']['value']);  // Currently logged-in user's username.
?>

 <script type="text/javascript" src="template/js/case.js"></script>
 <script type="text/javascript" src="template/js/misc.js"></script>
 <script type="text/javascript" src="template/js/CalendarPopup.js"></script>
 <script language="JavaScript">document.write(getCalendarStyles());</script>
 <script type="text/javascript" src="template/js/jquery.js"></script>
 <script type="text/javascript">
	$(document).ready(function() { $('#notes_field-toggle').click(function() {$('#notes_field').slideToggle(450);return false;}); });
 </script>


<table width="100%" cellpadding="0" cellspacing="0" align="center">
  <tr>
    <td style="height:5px;"> </td>
  </tr>
  <tr>
    <td valign="top" style="width:150px;">

      <?php  include('left_navbar.inc.php');  ?>

    </td>
    <td valign="top" style="padding:0px;margin:0;">

      <!-- Center Column -->
      <div id="center_column">

        <?php
		if ( $_REQUEST['task'] == 'save_case' )  {
			print set_required_fields( $CASE_VALID_FIELDS );
		}
        ?>


        <!-- Print Error Message -->
        <?php
          if ( !empty($ERR_MSG) )  {
        ?>
        <div width="100%" align="center">
          <div class="error">
            <?php  print $ERR_MSG . "\n";  ?>
          </div>
        </div>
        <p>
        <?php  }  ?>
        <!-- Print Error Message -->

        <!-- Start the Case Form -->
        <form name="main" action="<?php print $REPORTSDB; ?>" method="post">
        <!-- Start the Case Form -->


        <?php
		if ( isset($_REQUEST['confirm_save']) && $_REQUEST['confirm_save'] == 0 )  {
        ?>
        <p>
        <table width="100%" style="margin-left: 25px;">
          <tr>
            <td align="center">
              <h4> <?php print SAVE_CASE_CONFIRM; ?> </h4>

              <input type="hidden" name="confirm_save" value="1" />
              <button type="submit">Save Case</button>
              <button type="submit" onClick="javascript:document.main.task.value='edit_case';">Cancel</button>
              <br /><br />
            </td>
          </tr>
        </table>
        <?php  }  ?>


        <!-- Case Form -->
        <table cellspacing="0" cellpadding="0" align="center" class="form_table_main">
          <tr>
            <td colspan="3" style="padding:0px;">
              <div class="medium" id="toplinks">
                <?php
		if ( !empty($CASE_VALID_FIELDS['case_id']['value']) && $CASE_VALID_FIELDS['case_id']['value'] != "new" )  {
			print "<a href=\"" . $REPORTSDB . "?task=view_case&case_id=" . $CASE_VALID_FIELDS['case_id']['value'] . "\">" . TASK_VIEW_CASE . "</a>\n";
			print " | <a href=\"" . $REPORTSDB . "?task=view_case&case_id=" . $CASE_VALID_FIELDS['case_id']['value'] . "&view=printable\" target=\"_blank\">" . TASK_VIEW_CASE_PRINT . "</a>\n";
			if ( case_check_perms('d', $CASE_VALID_FIELDS['case_id']['value']) == 0 )  {
				print " | <a href=\"" . $REPORTSDB . "?task=delete_case&case_id=" . $CASE_VALID_FIELDS['case_id']['value'] . "&confirm_delete=0\">" . TASK_DELETE_CASE . "</a>\n";
			}
		}
                ?>
                &nbsp;
              </div>
              <div style="height:10px;"> </div>
            </td>
          </tr>
          <tr>
            <td valign="top" width="50%" style="padding:0px 10px 10px 5px;">


              <!-- Case Details -->
              <p><strong> <?php print CASE_LABEL_CASE_DETAILS; ?> </strong>
              <abbr title="<?php print HELP_CASE_DETAILS; ?>"><img src="images/help.gif"></abbr>
              <table cellpadding="0" cellspacing="4" width="100%">
                <tr>
                  <td id="label1"> <?php print CASE_ID; ?> </td>
                  <td id="value1">

                    <?php
			// Autogenerate case_id.
			if ( empty($CASE_VALID_FIELDS['case_id']['value']) )  {
				$case_id = generate_caseid();
                    ?>
                    <input type="text" id="case_id" name="case_id" size="12" maxlength="<?php print $CASE_VALID_FIELDS['case_id']['maxlength']; ?>" value="<?php print $case_id; ?>" READONLY="true" style="background:#dddddd">
                    <?php
			}  else  {
                    ?>
                    <input type="text" id="case_id" name="case_id" size="12" maxlength="<?php print $CASE_VALID_FIELDS['case_id']['maxlength']; ?>" value="<?php print $CASE_VALID_FIELDS['case_id']['value']; ?>" READONLY="true" style="background:#dddddd;">
                    <?php  }  ?>
                    <a href="javascript:void(0);" id="change_caseid" class="small" onclick="javascript:change_caseid('change_caseid');">Change</a>
                  </td>
                </tr>
                <tr>
                  <td id="label1" valign="top"> <?php print CASE_OWNER; ?> </td>
                  <td id="value1">
                    <input type="hidden" id="owner_id" name="owner_id" size="12" maxlength="<?php print $CASE_VALID_FIELDS['owner_id']['maxlength']; ?>" value="<?php print $CASE_VALID_FIELDS['owner_id']['value']; ?>">

                    <!-- User list to assign ownership -->
                    <div style="display:none" onChange="JavaScript:change_ownerid();" id="change_ownerid_div_select">
                      <select name="assign_owner_id" id="assign_owner_id" size="1">
                      <?php
			foreach ( array_keys($users) as $key )  {
				if ( $users[$key]['block'] == 1 )  {
					continue;
				}
				if ( $users[$key]['id'] == $CASE_VALID_FIELDS['owner_id']['value'] )  {
					$name = $users[$key]['name'];
					print "<option selected value=\"" . $users[$key]['id'] . "\">" . $users[$key]['name'] . "\n";
				}
				elseif ( $users[$key]['reports_db_perm'] >= $USER_PERMS['team-lead'])  {
					print "<option value=\"" . $users[$key]['id'] . "\">" . $users[$key]['name'] . "\n";
				}
			}
                      ?>
                      </select>
                    </div>
                    <!-- User list to assign ownership -->

                    <!-- Unnamed text box to display current owner -->
                    <div id="change_ownerid_div_text">
                      <input type="text" size="20" maxlength="50" value="<?php print $users[$username]['name']; ?>" READONLY="true" style="background:#dddddd">
                      <?php
			if ( $_SESSION['user_id'] == $CASE_VALID_FIELDS['owner_id']['value'] )  {
                      ?>
                      <a href="javascript:void(0);" id="change_ownerid" class="small" onClick="JavaScript:assign_ownerid('change_ownerid','change_ownerid_div_text','change_ownerid_div_select');">Assign</a>
                      <?php  }  ?>
                    </div>
                    <!-- Unnamed text box to display current owner -->

                  </td>
                </tr>
                <tr>
                  <td id="label1"> <?php print CASE_INVESTIGATION_TITLE; ?> </td>
                  <td id="value1">
                    <input type="text" id="investigation_title" name="investigation_title" size="30" maxlength="<?php print $CASE_VALID_FIELDS['investigation_title']['maxlength']; ?>" value="<?php print $CASE_VALID_FIELDS['investigation_title']['value']; ?>">
                  </td>
                </tr>
                <tr>
                  <td id="label1"> <?php print CASE_STATUS; ?> </td>
                  <td id="value1">
                    <select id="case_open" name="case_open" size="1">
                      <?php
			if ( empty($CASE_VALID_FIELDS['case_id']['value']) )  {
				print "<option selected value=\"open\">Open\n";
				print "<option value=\"closed\">Closed\n";
			}
			else  {
				$result = case_isopen( $CASE_VALID_FIELDS['case_id']['value'] );
				if ( $result == 0 || $result == ERR_DB_NODATA )  {
					print "<option selected value=\"open\">Open\n";
					print "<option value=\"closed\">Closed\n";
				}
				else  {
					print "<option value=\"open\">Open\n";
					print "<option selected value=\"closed\">Closed\n";
				}
			}
                      ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td id="label1"> <?php print CASE_TYPE; ?> </td>
                  <td id="value1">

                    <select id="investigation_type" name="investigation_type" size="1">
                      <?php
			if ( $CASE_VALID_FIELDS['investigation_type']['value'] == 'walkthrough' )  {
                      ?>
                        <option value="investigation">Investigation
                        <option selected value="walkthrough">Walkthrough
                        <option value="expedition">Expedition
                      <?php
			}  elseif ( $CASE_VALID_FIELDS['investigation_type']['value'] == 'expedition' )  {
                      ?>
                        <option value="investigation">Investigation
                        <option value="walkthrough">Walkthrough
                        <option selected value="expedition">Expedition
                      <?php  }  else  {  ?>
                        <option selected value="investigation">Investigation
                        <option value="walkthrough">Walkthrough
                        <option value="expedition">Expedition
                      <?php  }  ?>
                    </select>

                  </td>
                </tr>
                <tr>
                  <td id="label1"> <?php print CASE_DATE; ?> </td>
                  <td id="value1" valign="top" nowrap="nowrap">
                    <?php
			if ( empty($CASE_VALID_FIELDS['date']['value']) )  {
				$day = $month = "";
				$year = date("Y");
			}
			else  {
				$date = explode( '-', $CASE_VALID_FIELDS['date']['value'] );
				$day = $date[2];
				$month = $date[1];
				$year = $date[0];
			}
                    ?>
                    <script language="JavaScript">
			// <!--
			<?php  if ( is_browserie() )  {  ?>
			  var cal1 = new CalendarPopup();
			<?php  }  else  {  ?>
			  var cal1 = new CalendarPopup("dhtmldiv");
                        <?php  }  ?>
			cal1.setReturnFunction("setMultipleValues1");
			function setMultipleValues1(y,m,d) {
				document.main.date_year.value=y;
				document.main.date_month.selectedIndex=m;
				document.main.date_day.selectedIndex=d;
			}
			// -->
                    </script>

                    <!-- date_month -->
                    <select name="date_month" id="date_month" size="1">
                      <option>
                      <option value="01">January
                      <option value="02">February
                      <option value="03">March
                      <option value="04">April
                      <option value="05">May
                      <option value="06">June
                      <option value="07">July
                      <option value="08">August
                      <option value="09">September
                      <option value="10">October
                      <option value="11">November
                      <option value="12">December
                    </select>
                    <?php
			if ( !empty($month) )  {
                    ?>
                    <script language="JavaScript">
			// <!--
			selectoption('date_month',<?php print $month; ?>);
			// -->
                    </script>
                    <?php  }  ?>
                    <!-- date_month -->


                    <!-- date_day -->
                    <select name="date_day" id="date_day" size="1">
                      <option>
                      <?php
			for ( $i=1; $i<32; $i++ )  {
				print "<option value=\"" . $i . "\">" . $i . "\n";
			}
                      ?>
                    </select>
                    <?php
			if ( !empty($day) )  {
                    ?>
                    <script language="JavaScript">
			// <!--
			selectoption('date_day',<?php print $day; ?>);
			// -->
                    </script>
                    <?php  }  ?>
                    <!-- date_day -->

                    <input type="text" id="date" name="date_year" size="4" maxlength="4" value="<?php print $year; ?>">
                    <a href="#" onClick="cal1.showCalendar('anchor1');return false;" title="cal1.showCalendar('anchor1'); return false;" name="anchor1" id="anchor1"><img src="images/calendar.gif" align="top" border="0"></a>
                  </td>
                </tr>
                <tr>
                  <td id="label1"> <?php print CASE_TIME; ?> </td>
                  <td id="value1" nowrap="nowrap">

                    <!-- time_hour -->
                    <select name="time_hour" id="time_hour" size="1">
                      <option>
                      <?php
			for ( $i=0; $i<24; $i++ )  {
				if ( $i < 10 )  {  $i = 0 . $i;  }
				print "<option value=\"" . $i . "\">" . $i . "\n";
			}
                      ?>
                    </select>
                    <?php
			if ( !empty($CASE_VALID_FIELDS['time_hour']['value']) )  {
                    ?>
                    <script language="JavaScript">
			// <!--
			selectoption('time_hour',<?php print $CASE_VALID_FIELDS['time_hour']['value']; ?>);
			// -->
                    </script>
                    <?php  }  ?>
                    <!-- time_hour -->

                    <span style="height:100%;padding: 0 0 3px 0;"><strong>:</strong></span>

                    <!-- time_minute -->
                    <select name="time_minute" id="time_minute" size="1">
                      <option>
                      <?php
			for ( $i=0; $i<60; $i++ )  {
				if ( $i < 10 )  {  $i = 0 . $i;  }
				print "<option value=\"" . $i . "\">" . $i . "\n";
			}
                      ?>
                    </select>
                    <?php
			if ( !empty($CASE_VALID_FIELDS['time_minute']['value']) )  {
                    ?>
                    <script language="JavaScript">
			// <!--
			selectoption('time_minute',<?php print $CASE_VALID_FIELDS['time_minute']['value']; ?>);
			// -->
                    </script>
                    <?php  }  ?>
                    <!-- time_minute -->

                    <!-- timezone -->
                    <select id="timezone" name="timezone" size="1" style="font-family:monospace;">
                      <option>
                      <?php
			$tz = get_tz( 'sort_utc' );
			foreach ( array_keys($tz) as $key )  {
				print "<option value=\"" . $key . "\">" . $tz[$key]['abbr'] . " [UTC" . $tz[$key]['utc'] . "]\n";
			}
                      ?>
                    </select>
                    <?php
			if ( empty($CASE_VALID_FIELDS['timezone']['value']) )  {
				$tzone = strtolower( date("T") ); // Attempt to use the server's local timezone.
			}
			else  {
				$tzone = $CASE_VALID_FIELDS['timezone']['value'];
			}
                    ?>
                    <script language="JavaScript">
			// <!--
			selectoption('timezone','<?php print $tzone; ?>');
			// -->
                    </script>
                    <!-- timezone -->

                    <abbr title="<?php print HELP_CASE_INVESTIGATION_TIME; ?>"><img src="images/help.gif"></abbr>
                  </td>
                </tr>
                <tr>
                  <td id="label1"> <?php print CASE_EXPIRATION; ?> </td>
                  <td id="value1" nowrap="nowrap">
                    <?php
                       if ( empty($CASE_VALID_FIELDS['expiration_date']['value']) )  {
                                $day = $month = $year = "";
                       }
                       else  {
                                $date = explode( '-', $CASE_VALID_FIELDS['expiration_date']['value'] );
                                $day = $date[2];
                                $month = $date[1];
                                $year = $date[0];
                      }
                    ?>
                    <script language="JavaScript">
			// <!--
			<?php  if ( is_browserie() )  {  ?>
			  var cal2 = new CalendarPopup();
			<?php  }  else  {  ?>
			  var cal2 = new CalendarPopup("dhtmldiv");
                        <?php  }  ?>
			cal2.setReturnFunction("setMultipleValues2");
			function setMultipleValues2(y,m,d) {
				document.main.expiration_year.value=y;
				document.main.expiration_month.selectedIndex=m;
				document.main.expiration_day.selectedIndex=d;
			}
			// -->
                    </script>
                    <select name="expiration_month" id="expiration_month" size="1">
                      <option>
                      <option value="01">January
                      <option value="02">February
                      <option value="03">March
                      <option value="04">April
                      <option value="05">May
                      <option value="06">June
                      <option value="07">July
                      <option value="08">August
                      <option value="09">September
                      <option value="10">October
                      <option value="11">November
                      <option value="12">December
                    </select>
                    <?php
			if ( !empty($month) )  {
                    ?>
                    <script language="JavaScript">
			// <!--
			selectoption('expiration_month',<?php print $month; ?>);
			// -->
                    </script>
                    <?php  }  ?>

                    <!-- expiration_day -->
                    <select name="expiration_day" id="expiration_day" size="1">
                      <option>
                      <?php
			for ( $i=1; $i<32; $i++ )  {
				print "<option value=\"" . $i . "\">" . $i . "\n";
			}
                      ?>
                    </select>
                    <?php
			if ( !empty($day) )  {
                    ?>
                    <script language="JavaScript">
			// <!--
			selectoption('expiration_day',<?php print $day; ?>);
			// -->
                    </script>
                    <?php  }  ?>
                    <!-- expiration_day -->


                    <input type="text" id="expiration_year" name="expiration_year" size="4" maxlength="<?php print $CASE_VALID_FIELDS['expiration_date']['maxlength']; ?>" value="<?php print $year; ?>">
                    <a href="#" onClick="cal2.showCalendar('anchor2');return false;" title="cal2.showCalendar('anchor2'); return false;" name="anchor2" id="anchor2"><img src="images/calendar.gif" align="top" border="0"></a>
                    <abbr title="<?php print HELP_CASE_EXPIRATION; ?>"><img src="images/help.gif"></abbr>
                  </td>
                </tr>
                <tr>
                  <td id="label1"> <?php print CASE_RECAP_DATE; ?> </td>
                  <td id="value1">
                    <?php
                       if ( empty($CASE_VALID_FIELDS['recap_date']['value']) || $CASE_VALID_FIELDS['recap_date']['value'] == '0000-00-00' )  {
				$day = $month = $year = "";
                       }
                       else  {
				$date = explode( '-', $CASE_VALID_FIELDS['recap_date']['value'] );
				$day = $date[2];
				$month = $date[1];
				$year = $date[0];
                      }
                    ?>
                    <script language="JavaScript">
			// <!--
			<?php  if ( is_browserie() )  {  ?>
			  var cal3 = new CalendarPopup();
			<?php  }  else  {  ?>
			  var cal3 = new CalendarPopup("dhtmldiv");
                        <?php  }  ?>
			cal3.setReturnFunction("setMultipleValues3");
			function setMultipleValues3(y,m,d) {
				document.main.recap_date_year.value=y;
				document.main.recap_date_month.selectedIndex=m;
				document.main.recap_date_day.selectedIndex=d;
			}
			// -->
                    </script>
                    <select name="recap_date_month" id="recap_date_month" size="1">
                      <option>
                      <option value="01">January
                      <option value="02">February
                      <option value="03">March
                      <option value="04">April
                      <option value="05">May
                      <option value="06">June
                      <option value="07">July
                      <option value="08">August
                      <option value="09">September
                      <option value="10">October
                      <option value="11">November
                      <option value="12">December
                    </select>
                    <?php
			if ( !empty($month) )  {
                    ?>
                    <script language="JavaScript">
			// <!--
			selectoption('recap_date_month',<?php print $month; ?>);
			// -->
                    </script>
                    <?php  }  ?>


                    <!-- recap_date_day -->
                    <select name="recap_date_day" id="recap_date_day" size="1">
                      <option>
                      <?php
			for ( $i=1; $i<32; $i++ )  {
				print "<option value=\"" . $i . "\">" . $i . "\n";
			}
                      ?>
                    </select>
                    <?php
			if ( !empty($day) )  {
                    ?>
                    <script language="JavaScript">
			// <!--
			selectoption('recap_date_day',<?php print $day; ?>);
			// -->
                    </script>
                    <?php  }  ?>
                    <!-- recap_date_day -->

                    <input type="text" id="recap_date_year" name="recap_date_year" size="4" maxlength="4" value="<?php print $year; ?>">
                    <a href="#" onClick="cal3.showCalendar('anchor3');return false;" title="cal3.showCalendar('anchor3'); return false;" name="anchor3" id="anchor3"><img src="images/calendar.gif" align="top" border="0"></a>
                    <abbr title="<?php print HELP_CASE_RECAP_DATE; ?>"><img src="images/help.gif"></abbr>
                  </td>
                </tr>
                <tr>
                  <td id="label1"> <?php print CASE_RECAP_TIME; ?> </td>
                  <td id="value1">

                    <!-- recap_time_hour -->
                    <select name="recap_time_hour" id="recap_time_hour" size="1">
                      <option>
                      <?php
			for ( $i=0; $i<24; $i++ )  {
				if ( $i < 10 )  {  $i = 0 . $i;  }
				print "<option value=\"" . $i . "\">" . $i . "\n";
			}
                      ?>
                    </select>
                    <?php
			if ( !empty($CASE_VALID_FIELDS['recap_time_hour']['value']) )  {
                    ?>
                    <script language="JavaScript">
			// <!--
			selectoption('recap_time_hour',<?php print $CASE_VALID_FIELDS['recap_time_hour']['value']; ?>);
			// -->
                    </script>
                    <?php  }  ?>
                    <!-- recap_time_hour -->

                    <span style="height:100%;padding: 0 0 3px 0;"><strong>:</strong></span>

                    <!-- recap_time_minute -->
                    <select name="recap_time_minute" id="recap_time_minute" size="1">
                      <option>
                      <?php
			for ( $i=0; $i<60; $i++ )  {
				if ( $i < 10 )  {  $i = 0 . $i;  }
				print "<option value=\"" . $i . "\">" . $i . "\n";
			}
                      ?>
                    </select>
                    <?php
			if ( !empty($CASE_VALID_FIELDS['recap_time_minute']['value']) )  {
                    ?>
                    <script language="JavaScript">
			// <!--
			selectoption('recap_time_minute',<?php print $CASE_VALID_FIELDS['recap_time_minute']['value']; ?>);
			// -->
                    </script>
                    <?php  }  ?>
                    <!-- recap_time_minute -->


                    <!-- recap_time_timezone -->
                    <select id="recap_time_timezone" name="recap_time_timezone" size="1" style="font-family:monospace;">
                      <option>
                      <?php
			foreach ( array_keys($tz) as $key )  {
				print "<option value=\"" . $key . "\">" . $tz[$key]['abbr'] . " [UTC" . $tz[$key]['utc'] . "]\n";
			}
                      ?>
                    </select>
                    <?php
			if ( empty($CASE_VALID_FIELDS['recap_time_timezone']['value']) )  {
				$tzone = strtolower( date("T") ); // Attempt to use the server's local timezone.
			}
			else  {
				$tzone = $CASE_VALID_FIELDS['recap_time_timezone']['value'];
			}
                    ?>
                    <script language="JavaScript">
			// <!--
			selectoption('recap_time_timezone','<?php print $tzone; ?>');
			// -->
                    </script>
                    <!-- recap_time_timezone -->

                    <abbr title="<?php print HELP_CASE_RECAP_TIME; ?>"><img src="images/help.gif"></abbr>
                  </td>
                </tr>
                <tr>
                  <td id="label1"> <?php print CASE_RECAP_LOCATION; ?> </td>
                  <td id="value1">
                    <input type="text" id="recap_location" name="recap_location" size="30" maxlength="<?php print $CASE_VALID_FIELDS['recap_location']['maxlength']; ?>" value="<?php print $CASE_VALID_FIELDS['recap_location']['value']; ?>">
                  </td>
                </tr>
              </table>
              <!-- Case Details -->


              <!-- Primary Contact Info -->
              <div style="padding:2px;"></div>
              <strong> <?php print CASE_LABEL_CONTACT_INFO; ?> </strong>
              <abbr title="<?php print HELP_CASE_CONTACT_INFO; ?>"><img src="images/help.gif"></abbr>
              <table cellpadding="0" cellspacing="4" width="100%">
                <tr>
                  <td id="label1"> <?php print CASE_CONTACT_NAME; ?> </td>
                  <td id="value1">
                    <input type="text" id="contact_name" name="contact_name" size="30" maxlength="<?php print $CASE_VALID_FIELDS['contact_name']['maxlength']; ?>" value="<?php print $CASE_VALID_FIELDS['contact_name']['value']; ?>">
                  </td>
                </tr>
                <tr>
                  <td id="label1"> <?php print CASE_CONTACT_PRIMARY_PHONE; ?> </td>
                  <td id="value1">
                    <input type="text" id="contact_primary_phone" name="contact_primary_phone" size="30" maxlength="<?php print $CASE_VALID_FIELDS['contact_primary_phone']['maxlength']; ?>" value="<?php print $CASE_VALID_FIELDS['contact_primary_phone']['value']; ?>">
                  </td>
                </tr>
                <tr>
                  <td id="label1"> <?php print CASE_CONTACT_OFFICE_PHONE; ?> </td>
                  <td id="value1">
                    <input type="text" id="contact_office_phone" name="contact_office_phone" size="30" maxlength="<?php print $CASE_VALID_FIELDS['contact_office_phone']['maxlength']; ?>" value="<?php print $CASE_VALID_FIELDS['contact_office_phone']['value']; ?>">
                  </td>
                </tr>
                <tr>
                  <td id="label1"> <?php print CASE_CONTACT_MOBILE_PHONE; ?> </td>
                  <td id="value1">
                    <input type="text" id="contact_mobile_phone" name="contact_mobile_phone" size="30" maxlength="<?php print $CASE_VALID_FIELDS['contact_mobile_phone']['maxlength']; ?>" value="<?php print $CASE_VALID_FIELDS['contact_mobile_phone']['value']; ?>">
                  </td>
                </tr>
                <tr>
                  <td id="label1"> <?php print CASE_CONTACT_EMAIL; ?> </td>
                  <td id="value1">
                    <input type="text" id="contact_email" name="contact_email" size="30" maxlength="<?php print $CASE_VALID_FIELDS['contact_email']['maxlength'] ?>" value="<?php  print $CASE_VALID_FIELDS['contact_email']['value']  ?>">
                  </td>
                </tr>
              </table>
              <!-- Primary Contact Info -->


            </td>
            <td valign="top">


              <!-- Location Details -->
              <p><strong> <?php print CASE_LABEL_ADDRESS; ?> </strong>
              <abbr title="<?php print HELP_CASE_LOCATION_DETAILS; ?>"><img src="images/help.gif"></abbr>
              <table cellpadding="0" cellspacing="4" class="form_table">
                <tr>
                  <td id="label1" valign="top"> <?php print CASE_ADDRESS; ?> </td>
                  <td id="value1">
                    <textarea id="address" name="address" rows="4" cols="35" style="width:90%"><?php print $CASE_VALID_FIELDS['address']['value']; ?></textarea>
                  </td>
                </tr>
                <tr>
                  <td id="label1"> <?php print CASE_CITY; ?> </td>
                  <td id="value1">
                    <input type="text" id="city" name="city" size="20" maxlength="<?php print $CASE_VALID_FIELDS['city']['maxlength']; ?>" value="<?php print $CASE_VALID_FIELDS['city']['value']; ?>">
                  </td>
                </tr>
                <tr>
                  <td id="label1"> <?php print CASE_STATE; ?> </td>
                  <td id="value1">
                    <input type="text" id="state" name="state" size="20" maxlength="<?php print $CASE_VALID_FIELDS['state']['maxlength']; ?>" value="<?php print $CASE_VALID_FIELDS['state']['value']; ?>">
                  </td>
                </tr>
                <tr>
                  <td id="label1"> <?php print CASE_ZIP; ?> </td>
                  <td id="value1">
                    <input type="text" id="zip" name="zip" size="20" maxlength="<?php print $CASE_VALID_FIELDS['zip']['maxlength']; ?>" value="<?php print $CASE_VALID_FIELDS['zip']['value'];  ?>">
                  </td>
                </tr>
                <tr>
                  <td id="label1"> <?php print CASE_COUNTRY; ?> </td>
                  <td id="value1">
                    <input type="text" id="country" name="country" size="20" maxlength="<?php print $CASE_VALID_FIELDS['country']['maxlength']; ?>" value="<?php print $CASE_VALID_FIELDS['country']['value']; ?>">
                  </td>
                </tr>
                <tr>
                  <td id="label1"> <?php print CASE_LOCATION_TYPE; ?> </td>
                  <td id="value">
                    <select id="loc_type" name="loc_type" size="1">
                      <?php
			$SELECTED = '';
			if ( $CASE_VALID_FIELDS['loc_type']['value'] == 'home' )  {
				$SELECTED = 'selected';
			}
                      ?>
                      <option <?php print $SELECTED; ?> value="home">Private Home

                      <?php
			$SELECTED = '';
			if ( $CASE_VALID_FIELDS['loc_type']['value'] == 'business' )  {
				$SELECTED = 'selected';
			}
                      ?>
                      <option <?php print $SELECTED; ?> value="business">Business

                      <?php
			$SELECTED = '';
			if ( $CASE_VALID_FIELDS['loc_type']['value'] == 'cemetery' )  {
				$SELECTED = 'selected';
			}
                      ?>
                      <option <?php print $SELECTED; ?> value="cemetery">Cemetery

                      <?php
			$SELECTED = '';
			if ( $CASE_VALID_FIELDS['loc_type']['value'] == 'other' )  {
				$SELECTED = 'selected';
			}
                      ?>
                      <option <?php print $SELECTED; ?> value="other">Other
                    </select>
                  </td>
                </tr>
              </table>
              <!-- Location Details -->


              <!-- Description -->
              <p><strong> <?php print CASE_LABEL_DESCRIPTION; ?> </strong>
              <abbr title="<?php print HELP_CASE_DESCRIPTION; ?>"><img src="images/help.gif"></abbr>
              <table cellpadding="0" cellspacing="4" class="form_table">
                <tr>
                  <td id="label1-nowidth" valign="top" style="width:90px"> <?php print CASE_DESCRIPTION; ?> &nbsp; </td>
                  <td id="value1">
                    <textarea id="description" name="description" rows="10" cols="35" style="width:97%"><?php print $CASE_VALID_FIELDS['description']['value']; ?></textarea>
                    <div style="text-align:right;padding-right:20px;">
                      <a href="JavaScript:taller('description')" class="small"><?php print TEXTAREA_TALLER; ?></a> |
                      <a href="JavaScript:shorter('description')" class="small"><?php print TEXTAREA_SHORTER; ?></a>
                    </div>
                  </td>
                </tr>
              </table>
              <!-- Description -->

            </td>
          </tr>
        </table>
        <!-- Case Details -->


        <p>
        <!-- User Management -->
        <div id="reportheader">
          <div class="row">
            <strong> <?php print CASE_HEADER_USER_MANAGEMENT; ?> </strong>
          </div>
        </div>
        <table cellspacing="0" cellpadding="0" align="center" style="padding-top:10px;" class="form_table_main">
          <tr>
            <td align="center">

              <?php
		// Set some of the global javascript variables we'll need later.
		global $NUM_INVESTIGATORS;
		if ( empty($CASE_VALID_FIELDS['investigators']['value']) )  {
			$layer_num = 1;
		}
		else  {
			$layer_num = count( explode(',', $CASE_VALID_FIELDS['investigators']['value']) ) + 1;
		}
		print "<script type=\"text/javascript\">\n";
		print "// <!--\n";
		print "  layer_num=" . $layer_num . ";\n";
		print "  visible_rows=6;\n";
		print "  if (layer_num > visible_rows)  { visible_rows=layer_num; }\n";
		print "  min_rows=6;\n";
		print "  max_rows=" . $NUM_INVESTIGATORS . ";\n";
		print "// -->\n";
		print "</script>\n";
	      ?>

              <div class="prop-vert"></div>
              <table align="center" cellpadding="0" cellspacing="0" width="100%" class="investigators">
                <tr>
                  <td valign="top" align="center" style="width:158px;">
                    <div class="users_form" style="width:158px;" id="inv_label">
                      <div style="height:30%;overflow:hidden;"> </div>
                      <abbr title="<?php print HELP_INVESTIGATOR_USERS; ?>">
                        <strong> <?php print CASE_INVESTIGATOR_USERS; ?> </strong>
                      </abbr>
                    </div>

                    <select name="users" size="8" style="width:156px;height:170px;" multiple="multiple">
                    <?php
			// Do no add users that are on the case to this field.
			$investigators = explode( ',', $CASE_VALID_FIELDS['investigators']['value'] );
			$inv_list = array();
			foreach ( $users as $key )  {
				if ( $key['block'] == 1 )  {
					continue;
				}
				if ( in_array($key[$LOGIN_DB_ID_COL], $investigators) )  {
					continue;
				}
				$name = $key[$LOGIN_DB_NAME_COL];
				if ( strlen($name) > 20 )  {
					$name = substr( $name, 0, 20 );
				}
				array_push( $inv_list, $name . ',' . $key[$LOGIN_DB_ID_COL] );
			}
			sort( $inv_list );
			foreach ( $inv_list as $investigator )  {
				$investigator = explode( ',', $investigator );
				print "<option value=\"" . $investigator[1] . "\">" . $investigator[0] . "\n";
			}
                    ?>
                    </select>

                    <div style="padding:4px;text-align:center;">
                      <button type="button" onclick="add_investigator(this.form.users);">Add User(s)</button>
                    </div>

                  </td>
                  <td valign="top" id="investigators_col">
                    <div class="inv_name" id="inv_label">
                      <div style="height:30%;overflow:hidden;"></div>
                      <abbr title="<?php print HELP_INVESTIGATOR_NAME; ?>">
                        <strong> <?php print CASE_INVESTIGATORS; ?> </strong>
                      </abbr>
                    </div>
                    <div class="inv_role" id="inv_label">
                      <div style="height:30%;overflow:hidden;"></div>
                      <abbr title="<?php print HELP_INVESTIGATOR_ROLE; ?>">
                        <strong> <?php print CASE_INVESTIGATOR_ROLE; ?> </strong>
                      </abbr>
                    </div>
                    <div class="inv_data_submit" id="inv_label">
                      <abbr title="<?php print HELP_INVESTIGATOR_DATA_SUBMITTED; ?>">
                        <strong> <?php print CASE_INVESTIGATOR_DATA_SUBMITTED; ?> </strong>
                      </abbr>
                    </div>
                    <div class="inv_report_submit" id="inv_label">
                      <abbr title="<?php print HELP_INVESTIGATOR_REPORT_SUBMITTED; ?>">
                        <strong> <?php print CASE_INVESTIGATOR_REPORT_SUBMITTED; ?> </strong>
                      </abbr>
                    </div>
                    <div class="inv_report_submit" id="inv_label">
                      <abbr title="<?php print HELP_INVESTIGATOR_SEND_REMINDER; ?>">
                        <strong> <?php print CASE_INVESTIGATOR_EMAIL_REMINDER; ?> </strong>
                      </abbr>
                    </div>
                    <div style="clear:both"></div>

                    <?php
			global $NUM_INVESTIGATORS;

			$min_show = 6;
			$investigators = parse_investigators();
			$userids = array('');
			$username = array('');
			$roles = array('');
			$data = array('');
			foreach ( array_keys($investigators) as $key )  {
				array_push( $userids, $key );
				array_push( $roles, $investigators[$key]['role'] );
				array_push( $data, $investigators[$key]['data'] );

				foreach ( array_keys($users) as $uname )  {
					if ( $users[$uname][$LOGIN_DB_ID_COL] == $key )  {
						array_push( $username, $users[$uname][$LOGIN_DB_NAME_COL] );
						break;
					}
				}
			}
			$investigators = array();

			$style = 'display:block;';
			$num_show = count($userids)-1;
			if ( $num_show < $min_show )  {
				$num_show = $min_show;
			}
			for ( $i=1; $i<$NUM_INVESTIGATORS; $i++ )  {
				if ( $i == $num_show+1 )  {
					$style = 'display:none;';
				}
				if ( !isset($userids[$i]) || empty($userids[$i]) )  {
					$userids[$i] = "";
					$username[$i] = "";
					$roles[$i] = "";
					$data[$i] = "";
				}
                    ?>

                    <!-- Investigator <?php print $i; ?> -->
                    <div id="investigator_div_<?php print $i; ?>" style="<?php print $style; ?>">
                      <input type="hidden" name="investigator_userid_<?php print $i; ?>" id="investigator_userid_<?php print $i; ?>" value="<?php print $userids[$i]; ?>" />
                      <div class="inv_name">
                        <input type="text" id="investigator_name_<?php print $i; ?>" name="investigator_name_<?php print $i; ?>" size="26" maxlength="50" style="width:99%;" value="<?php print $username[$i]; ?>" />
                      </div>
                      <div class="inv_role">
                        <select name="investigator_pos_<?php print $i; ?>" id="investigator_pos_<?php print $i; ?>" size="1" style="width:99%;">
                          <?php
				$SELECTED = '';
                          ?>
                          <?php if ( $roles[$i] == 'obs' )  {  $SELECTED = 'SELECTED'; } ?>
                          <option value="obs" <?php print $SELECTED; ?>>Observer
                          <?php $SELECTED=''; ?>

                          <?php if ( $roles[$i] == 'tech' )  {  $SELECTED = 'SELECTED'; } ?>
                          <option value="tech" <?php print $SELECTED; ?>>Tech
                          <?php $SELECTED=''; ?>

                          <?php if ( $roles[$i] == 'psi' )  {  $SELECTED = 'SELECTED'; } ?>
                          <option value="psi" <?php print $SELECTED; ?>>PSI
                          <?php $SELECTED=''; ?>

                          <?php if ( $roles[$i] == 'research' )  {  $SELECTED = 'SELECTED'; } ?>
                          <option value="research" <?php print $SELECTED; ?>>Researcher
                          <?php $SELECTED=''; ?>

                          <?php if ( $roles[$i] == 'lead/obs' )  {  $SELECTED = 'SELECTED'; } ?>
                          <option value="lead/obs" <?php print $SELECTED; ?>>Lead/Observer
                          <?php $SELECTED=''; ?>

                          <?php if ( $roles[$i] == 'lead/tech' )  {  $SELECTED = 'SELECTED'; } ?>
                          <option value="lead/tech" <?php print $SELECTED; ?>>Lead/Tech
                          <?php $SELECTED=''; ?>

                        </select>
                      </div>
                      <div class="inv_data_submit">
                        <?php
				if ( $data[$i] == 1 )  {  $CHECKED = 'CHECKED';  }
				else  {  $CHECKED = '';  }
                        ?>
                        <input type="checkbox" name="investigator_data_<?php print $i; ?>" value="1" <?php print $CHECKED; ?> />
                      </div>
                      <div class="inv_report_submit">
                        <?php
				$report_exists = report_exists( $CASE_VALID_FIELDS['case_id']['value'], $userids[$i] );
				if ( $report_exists == 0 )  {
					print "<div style=\"padding-top:2px;\">";
					if ( report_check_perms('r', $CASE_VALID_FIELDS['case_id']['value'], $userids[$i]) == 0 )  {
						print "<a href=\"" . $REPORTSDB . "?task=view_report&case_id=" . $CASE_VALID_FIELDS['case_id']['value'] . "&owner_id=" . $userids[$i] . "\" class=\"green\" target=\"_blank\">YES</a>";
					}
					else  {
						print "<span class=\"green\">YES</span>";
					}
					print "</div>\n";
				}
				elseif ( !empty($userids[$i]) )  {
					print "<div class=\"red\" style=\"padding-top:2px;\">NO</div>\n";
				}
				else  {
					print "<div style=\"padding-top:2px;\"> N/A </div>\n";
				}
			?>
                      </div>
                      <div class="inv_data_submit">
                        <?php
				if ( $report_exists == 0 || empty($userids[$i]) )  {  $DISABLED = 'DISABLED';  }
				else  {  $DISABLED = '';  }
                        ?>
                        <input type="checkbox" name="investigator_reminder_<?php print $i; ?>" value="1" <?php print $DISABLED; ?> />
                      </div>
                      <div style="float:left;padding:8px 0px 0px 8px;">
                        <a href="javascript:del_investigator(<?php print $i; ?>, document.main.users);" class="small" style="text-decoration:none;">Delete</a>
                      </div>
                      <div style="clear:both;"> </div>
                    </div>
                    <!-- Investigator <?php print $i; ?> -->

                    <?php  }  ?>

                    <div style="text-align:left;padding:6px 0 0 4px;">
                      <a href="javascript:showmore('morelink');" class="small" id="morelink" style="text-decoration:none;"><?php print LINK_SHOW_MORE; ?></a>
                    </div>

                  </td>
                </tr>
              </table>

            </td>
          </tr>
        </table>
        <!-- User Management -->

	<?php
		// Add currently logged-in user to the list of investigators.
		if ( empty($CASE_VALID_FIELDS['case_id']['value']) || $CASE_VALID_FIELDS['case_id']['value'] == 'new' )  {
			if ( empty($CASE_VALID_FIELDS['investigators']['value']) )  {
				print "<script type=\"text/javascript\">\n";
				print "// <!--\n";
				print "  for (i=0; i<document.main.users.length; i++)  {\n";
				print "    if (document.main.users.options[i].value == " . $_SESSION['user_id'] . ")  {\n";
				print "      document.main.users.options[i].selected = true;\n";
				print "      break;\n";
				print "    }\n";
				print "  }\n";
				print "  add_investigator(document.main.users);\n";
				print "  selectoption('investigator_pos_1','lead/obs');\n";
				print "// -->\n";
				print "</script>\n";
			}
		}
	?>


        <p>
        <!-- Notes -->
        <div id="reportheader">
          <div class="row">
            <span class="left"> <strong><?php print CASE_HEADER_NOTES; ?></strong></span>
            <span class="right"> <a href="#" id="notes_field-toggle">Show/Hide</a> </span>
          </div>
        </div>
        <div id="notes_field" style="display:block">
        <table cellspacing="4" cellpadding="0" align="center" style="padding:10px 0 5px 0" class="form_table_main">
          <tr>
            <td id="label1-nowidth" valign="top" style="width:15%;white-space:nowrap">
              <?php print CASE_NOTES; ?>
              <abbr title="<?php print HELP_CASE_NOTES; ?>"><img src="images/help.gif"></abbr>
            </td>
            <td id="value1" style="padding-left:4px">
              <textarea id="notes" name="notes" rows="5" cols="35" style="width:99%"><?php print $CASE_VALID_FIELDS['notes']['value']; ?></textarea>
              <div style="text-align:right;padding:2px 10px 0 0">
                 <a href="JavaScript:taller('notes_textarea')" class="small"><?php print TEXTAREA_TALLER; ?></a> |
                 <a href="JavaScript:shorter('notes_textarea')" class="small"><?php print TEXTAREA_SHORTER; ?></a>
              </div>
            </td>
          </tr>
          <tr>
            <td> </td>
          </tr>
        </table>
        </div>
        <!-- Notes -->


        <p>
        <table cellspacing="0" cellpadding="0" align="center" class="form_table_main" style="padding:4px;">
          <tr>
            <td align="center" valign="middle">
              <input type="hidden" name="task" value="save_case" />
              <button type="submit">Save Case</button>
            </td>
          </tr>
        </table>
        </form>
        <!-- Case Form -->

      </div>
      <!-- Center Column -->

    </td>
  </tr>
</table>


		
