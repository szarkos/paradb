<?php
	global $REPORTS_VALID_FIELDS, $REPORTS_IMPRESSIONS_VALID_FIELDS, $REPORTS_ROOM_DATA_VALID_FIELDS, $NUM_ROOMS;
	global $REPORTSDB, $USER_PERMS, $ERR_MSG, $TITLE, $TECH_UNITS;
	global $LOGIN_DB_ID_COL, $LOGIN_DB_USER_COL, $LOGIN_DB_NAME_COL;
	for ( $i=1; $i<$NUM_ROOMS+1; $i++ )  {
		global ${'ROOM_DATA_'.$i};
	}
?>

 <script type="text/javascript" src="template/js/tabber-minimized.js"></script>
 <script type="text/javascript" src="template/js/misc.js"></script>
 <script type="text/javascript" src="template/js/report.js"></script>
 <link rel="stylesheet" href="template/tabs.css" type="text/css">

 <?php
	if ( is_printpage() )  {
		global $PRINTABLE_WIDTH;
		$width = $PRINTABLE_WIDTH;
		include( 'template/printable_header.inc.php' );
	}
	else  {
		$width = "100%";
	}
 ?>

<table width="<?php print $width; ?>" cellpadding="0" cellspacing="0" align="center">
  <tr>
    <td style="height:5px"> </td>
  </tr>
  <tr>

    <?php if ( !is_printpage() )  {  ?>
    <td valign="top" style="width:150px;">

      <?php  include('left_navbar.inc.php');  ?>

    </td>
    <?php  }  ?>

    <td valign="top" style="padding:0px;margin:0;" width="100%">

      <!-- Center Column -->
      <div class="center_column">

        <?php
          if ( $ERR_MSG != "" )  {
        ?>
        <div width="100%" align="center">
          <div class="error">
            <?php  print $ERR_MSG . "\n";  ?>
          </div>
        </div>
        <br /><br />
        <?php  }  ?>


        <?php
                if ( isset($_REQUEST['confirm_delete']) && $_REQUEST['confirm_delete'] == 0 )  {
        ?>
        <table width="100%" style="margin-left: 25px;">
          <tr>
            <td>
              <h2> Confirm Delete </h2>
              Are you sure you would like to delete the report for Case ID 
              <strong><?php print $REPORTS_VALID_FIELDS['case_id']['value']; ?></strong>?

              <p>
              <strong>Note:</strong> There is no way to undo this action.

              <p>
              <form name="confirm_del" action="<?php print $REPORTSDB; ?>" method="post">
                <input type="hidden" name="task" value="delete_report" />
                <input type="hidden" name="case_id" value="<?php print $REPORTS_VALID_FIELDS['case_id']['value']; ?>" />
                <input type="hidden" name="owner_id" value="<?php print $_SESSION['user_id']; ?>" />
                <input type="hidden" name="confirm_delete" value="1" />
                <button type="submit">Delete Report</button>
                <button type="submit" onClick="javascript:document.confirm_del.task.value='view_report';">Cancel</button>
              </form>
              <br /><br />
            </td>
          </tr>
        </table>
        <?php  }  ?>


        <!-- Report and Case Information -->
        <table cellspacing="0" cellpadding="0" align="center" class="form_table_main">
          <tr>
            <td colspan="3" style="height:10px;">

              <div class="medium" id="toplinks">
                <?php
			if ( !is_printpage() )  {
				if ( report_exists($REPORTS_VALID_FIELDS['case_id']['value'], $REPORTS_VALID_FIELDS['owner_id']['value']) == 0 )  {
					if ( report_check_perms('w', $REPORTS_VALID_FIELDS['case_id']['value'], $REPORTS_VALID_FIELDS['owner_id']['value']) == 0 )  {
						print "<a href=\"" . $REPORTSDB . "?task=edit_report&case_id=" . $REPORTS_VALID_FIELDS['case_id']['value'] . "&owner_id=" . $REPORTS_VALID_FIELDS['owner_id']['value'] . "\">" . TASK_EDIT_REPORT . "</a>\n";
					}
					print " | <a href=\"" . $REPORTSDB . "?task=view_report&case_id=" . $REPORTS_VALID_FIELDS['case_id']['value'] . "&owner_id=" . $REPORTS_VALID_FIELDS['owner_id']['value'] . "&view=printable\" target=\"_blank\">" . TASK_VIEW_REPORT_PRINT . "</a>\n";
					if ( report_check_perms('d', $REPORTS_VALID_FIELDS['case_id']['value'], $REPORTS_VALID_FIELDS['owner_id']['value']) == 0 )  {
						print " | <a href=\"" . $REPORTSDB . "?task=delete_report&case_id=" . $REPORTS_VALID_FIELDS['case_id']['value'] . "&owner_id=" . $REPORTS_VALID_FIELDS['owner_id']['value'] . "&confirm_delete=0\">" . TASK_DELETE_REPORT . "</a>\n";
					}
				}
			}
                ?>
                &nbsp;
              </div>
              <div style="padding:3px"> </div>

            </td>
          </tr>
          <tr>
            <td width="45%" valign="top" style="padding:0px 10px 0px 5px;">

              <strong> <?php print REPORT_LABEL_REPORT_DETAILS; ?> </strong>
              <abbr title="<?php print HELP_REPORT_DETAILS; ?>"><img src="images/help.gif"></abbr>
              <table cellpadding="0" cellspacing="4" width="100%" class="readonly">
                <tr>
                  <td id="label1"> <?php print CASE_ID; ?> </td>
                  <td id="value1">
                    <?php print $REPORTS_VALID_FIELDS['case_id']['value']; ?>
                  </td>
                </tr>
                <tr>
                  <td id="label1"> <?php print REPORT_INVESTIGATOR_NAME; ?> </td>
                  <td id="value1">
                    <?php
			$user = get_userinfo( $REPORTS_VALID_FIELDS['owner_id']['value'] );
                    ?>
                    <?php print $user['name']; ?>
                  </td>
                </tr>
                <tr>
                  <td id="label1"> <?php print REPORT_REPORT_STATE; ?> </td>
                  <td id="value1">
                    <?php print ucfirst( $REPORTS_VALID_FIELDS['report_state']['value'] ); ?>
                  </td>
                </tr>
                <tr>
                  <td id="label1"> <?php print REPORT_TITLE; ?> </td>
                  <td id="value1">
                    <?php print $REPORTS_VALID_FIELDS['case_title']['value']; ?>
                  </td>
                </tr>
                <tr>
                  <td id="label1" valign="top"> <?php print REPORT_DESCRIPTION; ?> </td>
                  <td id="value1" valign="top" style="height:90px;vertical-align:top">
                    <?php
			if ( !empty($REPORTS_VALID_FIELDS['description']['value']) )  {
				print preg_replace( '/\n/', '<br />', $REPORTS_VALID_FIELDS['description']['value'] );
			}
			else  {
				print "&nbsp;\n";
			}
                    ?>
                  </td>
                </tr>
                <tr>
                  <td id="label1"> <?php print REPORT_START_TIME; ?> </td>
                  <td id="value1">
                    <?php print $REPORTS_VALID_FIELDS['start_time_hour']['value']; ?>
                    <strong> : </strong>
                    <?php print $REPORTS_VALID_FIELDS['start_time_minute']['value']; ?>
                  </td>
                </tr>
                <tr>
                  <td id="label1"> <?php print REPORT_END_TIME; ?> </td>
                  <td id="value1">
                    <?php print $REPORTS_VALID_FIELDS['end_time_hour']['value']; ?>
                    <strong> : </strong>
                    <?php print $REPORTS_VALID_FIELDS['end_time_minute']['value']; ?>
                  </td>
                </tr>
              </table>

            </td>
            <td valign="top">

              <?php
		$case_id = $REPORTS_VALID_FIELDS['case_id']['value'];
		$case_info = list_case_info( $case_id );
              ?>
              <p><strong> <?php print CASE_LABEL_CASE_DETAILS; ?> </strong>
              <abbr title="<?php print HELP_CASE_DETAILS; ?>"><img src="images/help.gif"></abbr>
              <table cellpadding="0" cellspacing="4" width="100%" class="readonly">
                <tr>
                  <td id="label1"> <?php print CASE_INVESTIGATION_TITLE; ?> </td>
                  <td id="value1">
                    <?php print ucfirst( $case_info[$case_id]['investigation_title'] ); ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" width="40%" nowrap="nowrap"> <?php print REPORT_CASE_OWNER; ?> </td>
                  <td id="value1">
                    <?php
			$user = get_userinfo( $case_info[$case_id]['owner_id'] );
			if ( strlen($user['name']) > 20 )  {
				$user['name'] = substr( $user['name'], 0, 20 );
			}
			print $user['name'];
			if ( !empty($user['name']) )  {
				print " (<a href=\"javascript:void(0)\" onclick=\"window.open('" . $REPORTSDB . "?task=showstats&user_id=" . $user['id'] . "','" . $TITLE . " -- " . $user['username'] . "')\">" . $user['username'] . "</a>)\n";
			}
                    ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" width="40%" nowrap="nowrap"> <?php print REPORT_CASE_STATE; ?> </td>
                  <td id="value1">
                    <?php print ucfirst( $case_info[$case_id]['case_open'] ); ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" width="40%" nowrap="nowrap"> <?php print CASE_TYPE; ?> </td>
                  <td id="value1">
                    <?php print ucfirst( $case_info[$case_id]['investigation_type'] ); ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" width="40%" nowrap="nowrap"> <?php print REPORT_CASE_DATE_TIME; ?> </td>
                  <td id="value1">
                    <?php
			if ( $case_info[$case_id]['date'] != '0000-00-00' )  {
				print $case_info[$case_id]['date'];
				if ( !empty($case_info[$case_id]['time']) )  {
					print ", " . $case_info[$case_id]['time'];
				}
			}
                    ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" width="40%" nowrap="nowrap"> <?php print CASE_EXPIRATION; ?> </td>
                  <td id="value1" nowrap="nowrap">
                    <?php
			if ( $case_info[$case_id]['expiration_date'] != '0000-00-00' )  {
				print $case_info[$case_id]['expiration_date'];
			}
                    ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" width="40%" nowrap="nowrap"> <?php print CASE_LOCATION_TYPE; ?> </td>
                  <td id="value1">
                    <?php print ucfirst( $case_info[$case_id]['loc_type'] ); ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" width="40%" nowrap="nowrap"> <?php print CASE_CITY; ?> </td>
                  <td id="value1">
                    <?php print ucfirst( $case_info[$case_id]['city'] ); ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" width="40%" nowrap="nowrap"> <?php print CASE_STATE; ?> </td>
                  <td id="value1">
                    <?php print strtoupper( $case_info[$case_id]['state'] ); ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" width="40%" nowrap="nowrap"> <?php print REPORT_CASE_RECAP_DATE_TIME; ?> </td>
                  <td id="value1">
                    <?php
			if ( $case_info[$case_id]['recap_date'] != '0000-00-00' )  {
				print $case_info[$case_id]['recap_date'];
				if ( !empty($case_info[$case_id]['recap_time']) )  {
					print ", " . $case_info[$case_id]['recap_time'];
				}
			}
                    ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" width="40%" nowrap="nowrap"> <?php print CASE_RECAP_LOCATION; ?> </td>
                  <td id="value1" style="vertical-align:top;">
                    <?php print $case_info[$case_id]['recap_location']; ?>
                    &nbsp;
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        <!-- Report and Case Information -->


        <div class="divider"> </div>


        <!-- Equipment Information -->
        <div id="reportheader">
          <strong> <?php print REPORT_VIEW_HEADER_EQUIP; ?> </strong>
        </div>
        <table cellspacing="0" cellpadding="0" align="center" class="form_table_main">
          <tr>
            <td valign="top" style="padding:5px 0px 5px 10px;width:19%;white-space:nowrap;">

              <!-- Tech Equipment -->
              <strong> <?php print REPORT_VIEW_EQUIP_TECH; ?> </strong>
              <div style="padding:4px;">
              <?php
		global $TECH_EQUIPMENT;
		$count = 0;
		foreach ( array_keys($TECH_EQUIPMENT) as $key )  {
			if ( !isset($TECH_EQUIPMENT[$key]['ischecked']) )  {
				// We only want boolean (checkbox) values.
				continue;
			}
			if ( strtolower($TECH_EQUIPMENT[$key]['ischecked']) == 'yes' || $TECH_EQUIPMENT[$key]['ischecked'] == "1" )  {
				print "<div id=\"readonly-value\">\n";
				print eval("print " . $TECH_EQUIPMENT[$key]['lang'] . ";") . "\n";
                                print "</div>\n";
				$count++;
			}
		}

		$misc_fields = explode( "|", $TECH_EQUIPMENT['tech_equip_misc']['value'] );
		foreach ( array_keys($misc_fields) as $key )  {
			if ( !empty($misc_fields[$key]) )  {
				print "<div id=\"readonly-value\">\n";
					print $misc_fields[$key] . " <br />\n";
				print "</div>\n";
				$count++;
			}
		}
		if ( $count == 0 )  {
			print REPORT_VIEW_NOT_DEFINED . "\n";
		}
              ?>
              </div>
              <!-- Tech Equipment -->

            </td>
            <td valign="top" class="equip_view">

              <!-- Audio Equipment -->
              <strong> <?php print REPORT_VIEW_EQUIP_AUDIO; ?> </strong>

              <div style="padding:4px;">
              <?php
		global $AUDIO_EQUIPMENT;
		$count = 0;
		foreach ( array_keys($AUDIO_EQUIPMENT) as $key )  {
			if ( !isset($AUDIO_EQUIPMENT[$key]['ischecked']) )  {
				// We only want boolean (checkbox) values.
				continue;
			}
			if ( strtolower($AUDIO_EQUIPMENT[$key]['ischecked']) == 'yes' || $AUDIO_EQUIPMENT[$key]['ischecked'] == "1" )  {
				print "<div id=\"readonly-value\">\n";
				print eval("print " . $AUDIO_EQUIPMENT[$key]['lang'] . ";") . "\n";
				print "</div>\n";
				$count++;
			}
		}

		$misc_fields = explode( "|", $AUDIO_EQUIPMENT['audio_equip_misc']['value'] );
		foreach ( array_keys($misc_fields) as $key )  {
			if ( !empty($misc_fields[$key]) )  {
				print "<div id=\"readonly-value\">\n";
				print $misc_fields[$key] . " <br />\n";
				print "</div>\n";
				$count++;
			}
		}
		if ( $count == 0 )  {
			print REPORT_VIEW_NOT_DEFINED . "\n";
		}
              ?>
              </div>
              <!-- Audio Equipment -->

            </td>
            <td valign="top" class="equip_view">

              <!-- Video Equipment -->
              <strong> <?php print REPORT_VIEW_EQUIP_VIDEO; ?> </strong>

              <div style="padding:4px;">
              <?php
		global $VIDEO_EQUIPMENT;
		$count = 0;
		foreach ( array_keys($VIDEO_EQUIPMENT) as $key )  {
			if ( !isset($VIDEO_EQUIPMENT[$key]['ischecked']) )  {
				// We only want boolean (checkbox) values.
				continue;
			}
			if ( strtolower($VIDEO_EQUIPMENT[$key]['ischecked']) == 'yes' || $VIDEO_EQUIPMENT[$key]['ischecked'] == "1" )  {
				print "<div id=\"readonly-value\">\n";
				print eval("print " . $VIDEO_EQUIPMENT[$key]['lang'] . ";") . "\n";
                                print "</div>\n";
				$count++;
			}
		}

		$misc_fields = explode( "|", $VIDEO_EQUIPMENT['video_equip_misc']['value'] );
		foreach ( array_keys($misc_fields) as $key )  {
			if ( !empty($misc_fields[$key]) )  {
				print "<div id=\"readonly-value\">\n";
				print $misc_fields[$key] . " <br />\n";
				print "</div>\n";
				$count++;
			}
		}
		if ( $count == 0 )  {
			print REPORT_VIEW_NOT_DEFINED . "\n";
		}
              ?>
              </div>
              <!-- Video Equipment -->

            </td>
            <td valign="top" class="equip_view">

              <!-- Photo Equipment -->
              <strong> <?php print REPORT_VIEW_EQUIP_PHOTO; ?> </strong>

              <div style="padding:4px;">
              <?php
		global $PHOTO_EQUIPMENT;
		$count = 0;
		foreach ( array_keys($PHOTO_EQUIPMENT) as $key )  {
			if ( !isset($PHOTO_EQUIPMENT[$key]['ischecked']) )  {
				// We only want boolean (checkbox) values.
				continue;
			}
			if ( strtolower($PHOTO_EQUIPMENT[$key]['ischecked']) == 'yes' || $PHOTO_EQUIPMENT[$key]['ischecked'] == "1" )  {
				print "<div id=\"readonly-value\">\n";
				print eval("print " . $PHOTO_EQUIPMENT[$key]['lang'] . ";") . "\n";
				print "</div>\n";
				$count++;
			}
		}

		$misc_fields = explode( "|", $PHOTO_EQUIPMENT['photo_equip_misc']['value'] );
		foreach ( array_keys($misc_fields) as $key )  {
			if ( !empty($misc_fields[$key]) )  {
				print "<div id=\"readonly-value\">\n";
				print $misc_fields[$key] . " <br />\n";
				print "</div>\n";
				$count++;
			}
		}
		if ( $count == 0 )  {
			print REPORT_VIEW_NOT_DEFINED . "\n";
		}
              ?>
              <!-- Photo Equipment -->

            </td>
            <td valign="top" class="equip_view">

              <!-- PSI Equipment -->
              <strong> <?php print REPORT_VIEW_EQUIP_PSI; ?> </strong>

              <div style="padding:4px;">
              <?php
		global $PSI_EQUIPMENT;
		$count = 0;
		foreach ( array_keys($PSI_EQUIPMENT) as $key )  {
			if ( !isset($PSI_EQUIPMENT[$key]['ischecked']) )  {
				// We only want boolean (checkbox) values.
				continue;
			}
			if ( strtolower($PSI_EQUIPMENT[$key]['ischecked']) == 'yes' || $PSI_EQUIPMENT[$key]['ischecked'] == "1" )  {
				print "<div id=\"readonly-value\">\n";
				print eval("print " . $PSI_EQUIPMENT[$key]['lang'] . ";") . "\n";
				print "</div>\n";
				$count++;
			}
		}

		$misc_fields = explode( "|", $PSI_EQUIPMENT['psi_equip_misc']['value'] );
		foreach ( array_keys($misc_fields) as $key )  {
			if ( !empty($misc_fields[$key]) )  {
				print "<div id=\"readonly-value\">\n";
				print $misc_fields[$key] . " <br />\n";
				print "</div>\n";
				$count++;
			}
		}
		if ( $count == 0 )  {
			print REPORT_VIEW_NOT_DEFINED . "\n";
		}
              ?>
              <!-- PSI Equipment -->

            </td>
          </tr>
        </table>
        <!-- Equipment Information -->


        <div class="divider"> </div>


        <table cellspacing="0" cellpadding="0" width="100%">
          <tr>
            <td valign="top" style="width:33%">

              <!-- GEOMAGNETIC -->
              <div id="reportheader">
                <strong> <?php print REPORT_GEOMAGNETIC; ?> </strong>
              </div>
              <table cellspacing="0" cellpadding="0" align="center" class="form_table_main">
                <tr>
                  <td valign="top" style="padding:4px">
                    <div class="prop" id="geo"> </div>

                    <?php
			$kp = explode( ':', $REPORTS_VALID_FIELDS['geomag_kp']['value'] );
			if ( empty($kp) || count($kp) == 1 )  {
				$kp = array( "&nbsp;","&nbsp;","&nbsp;","&nbsp;","&nbsp;","&nbsp;","&nbsp;","&nbsp;" );
			}
                    ?>
                    <table cellpadding="0" cellspacing="0" style="width:100% !important;width:98%;" align="center" class="geomag">
                      <tr>
                        <td align="center" colspan="8">
                          <strong> <?php print GEOMAG_KP; ?> </strong>
                        </td>
                      </tr>
                      <tr>
                        <td id="kp-elem"> 0-3 </td>
                        <td id="kp-elem"> 3-6 </td>
                        <td id="kp-elem"> 6-9 </td>
                        <td id="kp-elem"> 9-12 </td>
                        <td id="kp-elem"> 12-15 </td>
                        <td id="kp-elem"> 15-18 </td>
                        <td id="kp-elem"> 18-21 </td>
                        <td id="kp-elem"> 21-24 </td>
                      </tr>
                      <tr>
                        <?php
				$i = 0;
				foreach ( array_keys($kp) as $key )  {
                        ?>
                        <td id="readonly-value" align="center">
                          <?php print $kp[$key]; ?>
                        </td>
                        <?php
					$i++;
				}
                        ?>
                      </tr>
                    </table>

                    <p>
                    <table align="center" style="width:100% !important;width:98%;padding-top:5px;" class="geomag">
                      <tr>
                        <td>
                          <strong><?php print GEOMAG_AP; ?></strong>:
                        </td>
                        <td id="readonly-value" style="padding-left:6px">
                          <?php print $REPORTS_VALID_FIELDS['geomag_ap']['value']; ?>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <strong><?php print GEOMAG_SUMMARY; ?></strong>:
                        </td>
                        <td id="readonly-value" width="70%" style="padding-left:6px">
                          <?php
				if ( empty($REPORTS_VALID_FIELDS['geomag_summary']['value']) )  {
					print '&nbsp;';
				}
				else  {
					print $REPORTS_VALID_FIELDS['geomag_summary']['value'];
				}
                          ?>
                        </td>
                      </tr>
                    </table>
                    <div style="padding:2px;"></div>
                    <div align="center" id="list_plot_geo">
                      <a href="<?php print $REPORTS_VALID_FIELDS['geomag_list']['value']; ?>" target="_blank" id="geomag_list"><?php print GEOMAG_LIST; ?></a> |
                      <a href="<?php print $REPORTS_VALID_FIELDS['geomag_plot']['value']; ?>" target="_blank" id="geomag_list"><?php print GEOMAG_PLOT; ?></a>
                    </div>

                    <div class="clear"> </div>
                  </td>
                </tr>
              </table>
              <!-- GEOMAGNETIC -->


            </td><td valign="top" style="width:33%;padding-left:6px;">


              <!-- XRAY -->
              <div id="reportheader">
                <strong> <?php print REPORT_XRAY; ?> </strong>
              </div>
              <table cellspacing="0" cellpadding="0" align="center" class="form_table_main">
                <tr>
                  <td valign="top" style="padding:4px;">
                    <div class="prop" id="xray"> </div>

                    <table cellpadding="0" cellspacing="2" align="center" class="xray">
                      <tr>
                        <td>
                          <strong><?php print XRAY_LONG; ?>:</strong>
                        </td>
                        <td id="readonly-value" style="padding-left:6px">
                          <?php print $REPORTS_VALID_FIELDS['xray_long']['value']; ?>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <strong><?php print XRAY_SHORT; ?>:</strong>
                        </td>
                        <td id="readonly-value" style="padding-left:6px">
                          <?php print $REPORTS_VALID_FIELDS['xray_short']['value']; ?>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <strong><?php print XRAY_HIGH; ?>:</strong>
                        </td>
                        <td id="readonly-value" style="padding-left:6px">
                          <?php print $REPORTS_VALID_FIELDS['xray_high']['value']; ?>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <strong><?php print XRAY_LOW; ?>:</strong>
                        </td>
                        <td id="readonly-value" style="padding-left:6px">
                          <?php print $REPORTS_VALID_FIELDS['xray_low']['value']; ?>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <strong><?php print XRAY_PEAK; ?>:</strong>
                        </td>
                        <td id="readonly-value" style="padding-left:6px">
                          <?php
				if ( empty($REPORTS_VALID_FIELDS['xray_peak']['value']) )  {
					print '&nbsp;';
				}
				else  {
					print $REPORTS_VALID_FIELDS['xray_peak']['value'];
				}
                          ?>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <strong> <?php print XRAY_SUMMARY; ?>: </strong>
                        </td>
                        <td id="readonly-value" style="padding-left:6px">
                          <?php
				if ( empty($REPORTS_VALID_FIELDS['xray_summary']['value']) )  {
					print '&nbsp;';
				}
				else  {
					$REPORTS_VALID_FIELDS['xray_summary']['value'] = preg_replace( '/</', '&lt;', $REPORTS_VALID_FIELDS['xray_summary']['value'] );
					$REPORTS_VALID_FIELDS['xray_summary']['value'] = preg_replace( '/>/', '&gt;', $REPORTS_VALID_FIELDS['xray_summary']['value'] );
					print $REPORTS_VALID_FIELDS['xray_summary']['value'];
				}
                          ?>
                        </td>
                      </tr>
                    </table>
                    <div style="padding:2px;"></div>
                    <div align="center" id="list_plot_xray">
                      <a href="<?php print $REPORTS_VALID_FIELDS['xray_list']['value']; ?>" target="_blank" id="xray_list"><?php print XRAY_LIST; ?></a> |
                      <a href="<?php print $REPORTS_VALID_FIELDS['xray_plot']['value']; ?>" target="_blank" id="xray_list"><?php print XRAY_PLOT; ?></a>
                    </div>
                    <div class="clear"></div>

                  </td>
                </tr>
              </table>
              <!-- XRAY -->


            </td><td valign="top" style="width:33%;padding-left:6px;">


              <!-- Moon Phase -->
              <div id="reportheader">
                <strong> <?php print REPORT_MOON; ?> </strong>
              </div>
              <table cellspacing="0" cellpadding="0" align="center" class="form_table_main">
                <tr>
                  <td valign="top" style="padding:4px;">
                    <div class="prop" id="moon"> </div>

                    <br />
                    <table cellpadding="0" cellspacing="0" align="center" class="moon_phase">
                      <tr>
                        <td valign="middle" align="center">
                          <div id="moon_phase_image" style="padding:5px;">
                            <?php
				if ( !empty($REPORTS_VALID_FIELDS['moon_image']['value']) )  {
					print "<img src=\"" . $REPORTS_VALID_FIELDS['moon_image']['value'] . "\">\n";
				}
				else  {
					print "<div style=\"height:48px;\"> </div>\n";
				}
                            ?>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <strong> <?php print MOON_PHASE; ?> </strong> <br />
                          <div id="moon_phase_data">
                            <?php print $REPORTS_VALID_FIELDS['moon_illum']['value'] . " " . $REPORTS_VALID_FIELDS['moon_phase']['value']; ?>
                            <?php
				if ( empty($REPORTS_VALID_FIELDS['moon_illum']['value']) && empty($REPORTS_VALID_FIELDS['moon_phase']['value']) )  {
					print "<div class=\"small\"> No Info </div>\n";
				}
                            ?>
                          </div>
                        </td>
                      </tr>
                    </table>
                    <div class="clear"></div>

                  </td>
                </tr>
              </table>
              <!-- Moon Phase -->

            </td>
          </tr>
        </table>


        <div class="divider"> </div>


        <!-- Outside Impression Information -->
        <div id="reportheader">
          <strong> <?php print REPORT_OUTSIDE_IMPRESSION; ?> </strong>
        </div>
        <table cellspacing="0" cellpadding="0" align="center" class="form_table_main" style="padding-bottom:0;">
          <tr>
            <td>
              <div id="readonly-value" style="margin:8px;padding:4px;width:auto !important;width:100%;">
              <div class="prop-small"> </div>
                <?php
			if ( !empty($REPORTS_IMPRESSIONS_VALID_FIELDS['outside_impr']['value']) )  {
				print preg_replace( '/\n/', '<br />', $REPORTS_IMPRESSIONS_VALID_FIELDS['outside_impr']['value'] );
			}
			else  {
				print REPORT_VIEW_NOT_DEFINED;
			}
                ?>
                <div class="clear"> </div>
              </div>
            </td>
          </tr>
        </table>
        <!-- Outside Impression Information -->


        <div class="divider"> </div>


        <!-- Walk-in Impression Information -->
        <div id="reportheader">
          <strong> <?php print REPORT_WALKIN_IMPRESSION; ?> </strong>
        </div>
        <table cellspacing="0" cellpadding="0" align="center" class="form_table_main" style="padding-bottom:0;">
          <tr>
            <td>
              <div id="readonly-value" style="margin:8px;padding:4px;width:auto !important;width:100%;">
                <div class="prop-small"> </div>
                <?php
			if ( !empty($REPORTS_IMPRESSIONS_VALID_FIELDS['walkin_impr']['value']) )  {
				print preg_replace( '/\n/', '<br />', $REPORTS_IMPRESSIONS_VALID_FIELDS['walkin_impr']['value'] );
			}
			else  {
				print REPORT_VIEW_NOT_DEFINED;
			}
                ?>
                <div class="clear"></div>
              </div>
            </td>
          </tr>
        </table>
        <!-- Walk-in Impression Information -->


        <div class="divider"> </div>


        <!-- Closing Impression Information -->
        <div id="reportheader">
          <strong> <?php print REPORT_CLOSING_IMPRESSION; ?> </strong>
        </div>
        <table cellspacing="0" cellpadding="0" align="center" class="form_table_main" style="padding-bottom:0;">
          <tr>
            <td>
              <div id="readonly-value" style="margin:8px;padding:4px;width:auto !important;width:100%;">
                <div class="prop-small"> </div>
                <?php
			if ( !empty($REPORTS_IMPRESSIONS_VALID_FIELDS['closing_impr']['value']) )  {
				print preg_replace( '/\n/', '<br />', $REPORTS_IMPRESSIONS_VALID_FIELDS['closing_impr']['value'] );
			}
			else  {
				print REPORT_VIEW_NOT_DEFINED;
			}
                ?>
                <div class="clear"></div>
              </div>
            </td>
          </tr>
        </table>
        <!-- Closing Impression Information -->


        <!-- Print Room Data -->
        <?php
          for ( $i=1; $i<$NUM_ROOMS+1; $i++ )  {
		if ( !room_isempty( $i ) )  {
			$style = 'display:block;';
		}
		else  {
			continue;
		}

		if ( $i == $NUM_ROOMS/2 )  {
			print "<!-- Set up the tabs early. -->\n";
			print "<script type=\"text/javascript\">\n";
			print "	// <!--\n";
			print "	tabberAutomatic();\n";
			print "	// -->\n";
			print "</script>\n";
		}
        ?>

        <!-- Room <?php print $i; ?> -->
        <div class="divider"> </div>
        <div id="reportheader">
          <?php
		if ( !empty(${'ROOM_DATA_'.$i}['room_id']['value']) )  {
			print "<strong> " . ${'ROOM_DATA_'.$i}['room_id']['value'] . " </strong>";
		}
		else  {
			print "<strong> " . REPORT_HEADER_ROOM . $i . " </strong>";
		}
          ?>
        </div>
        <table cellspacing="4" cellpadding="0" align="center" class="form_table_main">
          <tr>
            <td>

              <table width="100%">
                <tr>
                  <td>
                    <?php print REPORT_ROOM_ID; ?>
                  </td>
                </tr>
                <tr>
                  <td id="readonly-value" align="left">
                    <?php print ${'ROOM_DATA_'.$i}['room_id']['value']; ?> &nbsp;
                  </td>
                </tr>
              </table>


              <?php
		$empty = 'yes';
		for ( $j=1; $j<10; $j++ )  {
			if ( !empty(${'ROOM_DATA_'.$i}['tech_anomaly_data_'.$j]['value']) )  {
				$empty = 'no';
				break;
			}
		}
		if ( $empty == 'yes' )  {
			$tech_vars = array (	'ac_emf_electric', 'ac_emf_magnetic',
						'dc_emf_electric', 'dc_emf_magnetic',
						'dc_emf_sum', 'temp', 'rel_humidity', 'barometric' );
			foreach ( array_keys($tech_vars) as $key )  {
				if ( !empty(${'ROOM_DATA_'.$i}[$tech_vars[$key]]['value']) )  {
					$empty = 'no';
					break;
				}
			}
		}
		if ( $empty == 'no' )  {
              ?>
              <!-- Technical Measurements -->
              <div style="padding-top:5px;"> </div>
              <div id="roomitemheader">
                <strong> Technical Measurements </strong>
              </div>
              <table cellspacing="4" cellpadding="0" align="center" class="form_table_main">
                <tr>
                  <td class="form_table">

                    <table cellpadding="0" cellspacing="0" width="100%">
                      <tr>
                        <td valign="top" style="width:35%;padding-right:10px;">

                          <strong> Baseline Measurements </strong>
                          <table cellpadding="0" cellspacing="4" width="100%" class="readonly">

                            <?php if ( !empty(${'ROOM_DATA_'.$i}['ac_emf_magnetic']['value']) )  {  ?>
                            <tr>
                              <td id="label1" nowrap> <?php print REPORT_AC_EMF_MAGNETIC; ?> </td>
                              <td id="value1">
                                <?php print ${'ROOM_DATA_'.$i}['ac_emf_magnetic']['value']; ?>
                                <?php print $TECH_UNITS[${'ROOM_DATA_'.$i}['ac_emf_magnetic_units']['value']]; ?>
                              </td>
                            </tr>
                            <?php  }  ?>

                            <?php if ( !empty(${'ROOM_DATA_'.$i}['ac_emf_electric']['value']) )  {  ?>
                            <tr>
                              <td id="label1" nowrap> <?php print REPORT_AC_EMF_ELECTRIC; ?> </td>
                              <td id="value1">
                                <?php print ${'ROOM_DATA_'.$i}['ac_emf_electric']['value']; ?>
                                <?php print $TECH_UNITS[${'ROOM_DATA_'.$i}['ac_emf_electric_units']['value']]; ?>
                              </td>
                            </tr>
                            <?php  }  ?>

                            <?php if ( !empty(${'ROOM_DATA_'.$i}['dc_emf_magnetic']['value']) )  {  ?>
                            <tr>
                              <td id="label1" nowrap> <?php print REPORT_DC_EMF_MAGNETIC; ?> </td>
                              <td id="value1">
                                <?php print ${'ROOM_DATA_'.$i}['dc_emf_magnetic']['value']; ?>
                                <?php print $TECH_UNITS[${'ROOM_DATA_'.$i}['dc_emf_magnetic_units']['value']]; ?>
                              </td>
                            </tr>
                            <?php  }  ?>

                            <?php if ( !empty(${'ROOM_DATA_'.$i}['dc_emf_electric']['value']) )  {  ?>
                            <tr>
                              <td id="label1" nowrap> <?php print REPORT_DC_EMF_ELECTRIC; ?> </td>
                              <td id="value1">
                                <?php print ${'ROOM_DATA_'.$i}['dc_emf_electric']['value']; ?>
                                <?php print $TECH_UNITS[${'ROOM_DATA_'.$i}['dc_emf_electric_units']['value']]; ?>
                              </td>
                            </tr>
                            <?php  }  ?>

                            <?php if ( !empty(${'ROOM_DATA_'.$i}['dc_emf_sum']['value']) )  {  ?>
                            <tr>
                              <td id="label1" nowrap> <?php print REPORT_DC_EMF_SUM; ?> </td>
                              <td id="value1">
                                <?php print ${'ROOM_DATA_'.$i}['dc_emf_sum']['value']; ?>
                                <?php print $TECH_UNITS[${'ROOM_DATA_'.$i}['dc_emf_sum_units']['value']]; ?>
                              </td>
                            </tr>
                            <?php  }  ?>

                            <?php if ( !empty(${'ROOM_DATA_'.$i}['temp']['value']) )  {  ?>
                            <tr>
                              <td id="label1" nowrap> <?php print REPORT_TEMP; ?> </td>
                              <td id="value1">
                                <?php print ${'ROOM_DATA_'.$i}['temp']['value']; ?>
                                <?php print $TECH_UNITS[${'ROOM_DATA_'.$i}['temp_units']['value']]; ?>
                              </td>
                            </tr>
                            <?php  }  ?>

                            <?php if ( !empty(${'ROOM_DATA_'.$i}['rel_humidity']['value']) )  {  ?>
                            <tr>
                              <td id="label1" nowrap> <?php print REPORT_RELATIVE_HUMIDITY; ?> </td>
                              <td id="value1">
                                <?php print ${'ROOM_DATA_'.$i}['rel_humidity']['value']; ?>
                                <?php print $TECH_UNITS[${'ROOM_DATA_'.$i}['rel_humidity_units']['value']]; ?>
                              </td>
                            </tr>
                            <?php  }  ?>

                            <?php if ( !empty(${'ROOM_DATA_'.$i}['barometric']['value']) )  {  ?>
                            <tr>
                              <td id="label1" nowrap> <?php print REPORT_BAROMETRIC; ?> </td>
                              <td id="value1">
                                <?php print ${'ROOM_DATA_'.$i}['barometric']['value']; ?>
                                <?php print $TECH_UNITS[${'ROOM_DATA_'.$i}['barometric_units']['value']]; ?>
                              </td>
                            </tr>
                            <?php  }  ?>

                          </table>

                        </td>
                        <td valign="top" style="border-left:2px solid #999999;padding-left:10px;">

                          <strong> Anomalous Measurements </strong>
                          <div style="padding:4px 0px 0px 4px;">
                            <table cellspacing="4" cellpadding="0" width="100%" class="readonly">
                              <tr>
                                <td id="label1-nowidth" align="center"> Time </td>
                                <td id="label1-nowidth" align="center"> Type </td>
                                <td id="label1-nowidth" align="center" style="width:3em"> +/- </td>
                                <td id="label1-nowidth" align="center"> Value </td>
                                <td id="label1-nowidth" align="center"> Units </td>
                              </tr>

                              <?php
				for ( $j=1; $j<10; $j++ )  {
					if ( empty(${'ROOM_DATA_'.$i}['tech_anomaly_data_'.$j]['value']) )  {
						continue;
					}
					else  {
						// The tech anomalies are split up into 5 fields, so we 
						// need to split up the fields from the database source.
						$tech_fields = explode( '|', ${'ROOM_DATA_'.$i}['tech_anomaly_data_'.$j]['value'] );
						foreach ( array_keys($tech_fields) as $key )  {
							if ( empty($tech_fields[$key]) )  {
								$tech_fields[$key] = '&nbsp;';
							}
						}
					}
                              ?>

                              <tr>
                                <td id="value1">
                                  <?php print $tech_fields[0]; ?>
                                </td>
                                <td id="value1" nowrap>
                                  <?php print $tech_fields[1]; ?>
                                </td>
                                <td id="value1" align="center">
                                  <?php print $tech_fields[2]; ?>
                                </td>
                                <td id="value1">
                                  <?php print $tech_fields[3]; ?>
                                </td>
                                <td id="value1">
                                  <?php print $tech_fields[4]; ?>
                                </td>
                              </tr>
                            <?php  }  ?>
                            </table>

                          </div>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              <!-- Technical Measurements -->
              <?php  }  ?>


              <!-- Anomalies -->
              <?php
		$count = 0;
		for ( $j=1; $j<10; $j++ )  {
			if ( !empty(${'ROOM_DATA_'.$i}['anomaly_anomaly_data_'.$j]['value']) )  {
				$count++;
				break;
			}
		}
		if ( $count > 0 )  {
              ?>
              <div style="padding-top:5px;"> </div>
              <div id="roomitemheader">
                <strong> Anomalies Reported </strong>
              </div>
              <table cellspacing="4" cellpadding="0" align="center" class="form_table_main">
                <tr>
                  <td class="form_table">

                    <table cellpadding="0" cellspacing="4" width="100%" class="readonly">
                      <tr>
                        <td id="label1" width="10%" nowrap> <strong> Time </strong> </td>
                        <td id="label1" width="40%"> <strong> Type(s) </strong> </td>
                        <td id="label1"> <strong> Description </strong> </td>
                      </tr>

                      <?php
			for ( $j=1; $j<10; $j++ )  {
				if ( empty(${'ROOM_DATA_'.$i}['anomaly_anomaly_data_'.$j]['value']) )  {
					continue;
				}
				$data = explode( '|', ${'ROOM_DATA_'.$i}['anomaly_anomaly_data_'.$j]['value'] );
				if ( empty($data[0]) )  {  $data[0] = '&nbsp;';  }
				if ( empty($data[1]) )  {  $data[1] = '&nbsp;';  }
				if ( empty($data[2]) )  {  $data[2] = '&nbsp;';  }
                      ?>
                      <tr>
                        <td id="value1" valign="top"> <?php print $data[0]; ?> </td>
                        <td id="value1" valign="top"> <?php print $data[1]; ?> </td>
                        <td id="value1" valign="top"> <?php print preg_replace( '/\n/', '<br />', $data[2] ); ?> </td>
                      </tr>
                      <?php  }  ?>

                    </table>
                  </td>
                </tr>
              </table>
              <?php  }  ?>
              <!-- Anomalies -->


              <!-- EVP -->
              <?php
		$count = 0;
		for ( $j=1; $j<10; $j++ )  {
			if ( !empty(${'ROOM_DATA_'.$i}['evp_anomaly_data_'.$j]['value']) )  {
				$count++;
				break;
			}
		}
		if ( $count > 0 )  {
              ?>
              <div style="padding-top:5px;"> </div>
              <div id="roomitemheader">
                <strong> EVPs Reported </strong>
              </div>
              <table cellspacing="4" cellpadding="0" align="center" class="form_table_main">
                <tr>
                  <td class="form_table">

                    <table cellpadding="0" cellspacing="4" width="100%" class="readonly">
                      <tr>
                        <td id="label1" style="width:20% !important"> <strong> Label </strong> </td>
                        <td id="label1" style="width:15% !important" nowrap> <strong> Tape Time Code </strong> </td>
                        <td id="label1" style="width:15% !important" nowrap> <strong> <?php print REPORT_REAL_TIME; ?> </strong> </td>
                        <td id="label1"> <strong> Description </strong> </td>
                      </tr>

                      <?php
			for ( $j=1; $j<10; $j++ )  {
				if ( empty(${'ROOM_DATA_'.$i}['evp_anomaly_data_'.$j]['value']) )  {
					continue;
				}
				$data = explode( '|', ${'ROOM_DATA_'.$i}['evp_anomaly_data_'.$j]['value'] );
				if ( count($data) == 3 )  {
					// Added "real time" later, so must account for the missing value in older reports.
					array_push( $data, $data[2] );
					$data[2] = '00:00:00';
				}
				foreach ( array_keys($data) as $key )  {
					if ( empty($data[$key]) || $data[$key] == '00:00:00' )  {
						$data[$key] = REPORT_VIEW_NOT_DEFINED;
					}
				}
                      ?>
                      <tr>
                        <td id="value1" valign="top"> <?php print $data[0]; ?> </td>
                        <td id="value1" valign="top"> <?php print $data[1]; ?> </td>
                        <td id="value1" valign="top"> <?php print $data[2]; ?> </td>
                        <td id="value1" valign="top"> <?php print preg_replace( '/\n/', '<br />', $data[3] ); ?> </td>
                      </tr>
                      <?php  }  ?>

                    </table>
                  </td>
                </tr>
              </table>
              <?php  }  ?>
              <!-- EVP -->


              <!-- Video -->
              <?php
		$count = 0;
		for ( $j=1; $j<10; $j++ )  {
			if ( !empty(${'ROOM_DATA_'.$i}['video_anomaly_data_'.$j]['value']) )  {
				$count++;
				break;
			}
		}
		if ( $count > 0 )  {
              ?>
              <div style="padding-top:5px;"> </div>
              <div id="roomitemheader">
                <strong> Video Anomalies Reported </strong>
              </div>
              <table cellspacing="4" cellpadding="0" align="center" class="form_table_main">
                <tr>
                  <td class="form_table">

                    <table cellpadding="0" cellspacing="4" width="100%" class="readonly">
                      <tr>
                        <td id="label1" style="width:20% !important"> <strong> Label </strong> </td>
                        <td id="label1" style="width:15% !important" nowrap> <strong> Video Time Code </strong> </td>
                        <td id="label1" style="width:15% !important" nowrap> <strong> <?php print REPORT_REAL_TIME; ?> </strong> </td>
                        <td id="label1"> <strong> Description </strong> </td>
                      </tr>

                      <?php
			for ( $j=1; $j<10; $j++ )  {
				if ( empty(${'ROOM_DATA_'.$i}['video_anomaly_data_'.$j]['value']) )  {
					continue;
				}
				$data = explode( '|', ${'ROOM_DATA_'.$i}['video_anomaly_data_'.$j]['value'] );
				if ( count($data) == 3 )  {
					// Added "real time" later, so must account for the missing value in older reports.
					array_push( $data, $data[2] );
					$data[2] = '00:00:00';
				}
				foreach ( array_keys($data) as $key )  {
					if ( empty($data[$key]) || $data[$key] == '00:00:00' )  {
						$data[$key] = REPORT_VIEW_NOT_DEFINED;
					}
				}
                      ?>
                      <tr>
                        <td id="value1" valign="top"> <?php print $data[0]; ?> </td>
                        <td id="value1" valign="top"> <?php print $data[1]; ?> </td>
                        <td id="value1" valign="top"> <?php print $data[2]; ?> </td>
                        <td id="value1" valign="top"> <?php print preg_replace( '/\n/', '<br />', $data[3] ); ?> </td>
                      </tr>
                      <?php  }  ?>

                    </table>
                  </td>
                </tr>
              </table>
              <?php  }  ?>
              <!-- Video -->


              <!-- Room Notes Field -->
              <?php
		if ( !empty(${'ROOM_DATA_'.$i}['notes']['value']) )  {
              ?>
              <div style="padding-top:10px;"> </div>
              <div id="roomitemheader">
                <strong> Notes </strong>
              </div>

              <div id="readonly-value" style="padding:8px;">
                <?php print preg_replace( '/\n/', '<br />', ${'ROOM_DATA_'.$i}['notes']['value'] ); ?> &nbsp;
              </div>
              <?php  }  ?>
              <!-- Room Notes Field -->


            </td>
          </tr>
        </table>
        <!-- Room <?php print $i; ?> -->

        <?php
          }  // End printing room data.
        ?>


      </div>
      <!-- Center Column -->

    </td>
  </tr>
</table>


