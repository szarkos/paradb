<?php
	global $CASE_VALID_FIELDS, $REPORTSDB, $USER_PERMS, $ERR_MSG;
	global $LOGIN_DB_ID_COL, $LOGIN_DB_USER_COL, $LOGIN_DB_NAME_COL;
	$users = listusers();
?>

 <script type="text/javascript" src="template/js/misc.js"></script>
 <script type="text/javascript" src="template/js/case.js"></script>

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
    <td style="height:5px;"> </td>
  </tr>
  <tr>

    <?php if ( !is_printpage() )  {  ?>
    <td valign="top" style="width:150px;">

      <?php  include('left_navbar.inc.php');  ?>

    </td>
    <?php  }  ?>

    <td valign="top" style="padding:0px;margin:0;">

      <!-- Center Column -->
      <div id="center_column">

        <?php
		if ( !empty($ERR_MSG) )  {
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
              Are you sure you would like to delete case ID
              <strong><?php print $CASE_VALID_FIELDS['case_id']['value']; ?></strong>?

              <p>
              <strong>Note:</strong> There is no way to undo this action.

              <p>
              <form name="confirm_del" action="<?php print $REPORTSDB; ?>" method="post">
                <input type="hidden" name="task" value="delete_case" />
                <input type="hidden" name="case_id" value="<?php print $CASE_VALID_FIELDS['case_id']['value']; ?>" />
                <input type="hidden" name="confirm_delete" value="1" />
                <button type="submit">Delete Case</button> &nbsp;&nbsp;
                <button type="submit" onClick="javascript:document.confirm_del.task.value='view_case';">Cancel</button>
              </form>
              <br /><br />
            </td>
          </tr>
        </table>
        <?php  }  ?>


        <!-- Case Form -->
        <table cellspacing="0" cellpadding="0" align="center" class="form_table_main">
          <tr>
            <td colspan="3" style="height:10px;padding:0;">

              <div class="medium" id="toplinks">
                <div style="float:left;text-align:left;overflow:hidden;">
                  <strong> <?php print $CASE_VALID_FIELDS['investigation_title']['value']; ?> </strong>
                </div>
                <div style="float:right;text-align:right;">
                  <?php
			if ( !is_printpage() )  {
				if ( case_check_perms('w', $CASE_VALID_FIELDS['case_id']['value']) == 0 )  {
					print "<a href=\"" . $REPORTSDB . "?task=edit_case&case_id=" . $CASE_VALID_FIELDS['case_id']['value'] . "\">" . TASK_EDIT_CASE . "</a>\n";
				}
 				print " | <a href=\"" . $REPORTSDB . "?task=view_case&case_id=" . $CASE_VALID_FIELDS['case_id']['value'] . "&view=printable\" target=\"_blank\">" . TASK_VIEW_CASE_PRINT . "</a>\n";
				if ( case_check_perms('d', $CASE_VALID_FIELDS['case_id']['value']) == 0 )  {
					print " | <a href=\"" . $REPORTSDB . "?task=delete_case&case_id=" . $CASE_VALID_FIELDS['case_id']['value'] . "&confirm_delete=0\">" . TASK_DELETE_CASE . "</a>\n";
				}
			}
                  ?>
                  &nbsp;
                </div>
                <div style="clear:both;"> </div>
              </div>
              <div style="height:10px;"></div>
            </td>
          </tr>
          <tr>
            <td valign="top" width="45%" style="padding:0px 10px 0px 5px;">


              <!-- Case Details -->
              <p><strong> <?php print CASE_LABEL_CASE_DETAILS; ?> </strong>
              <abbr title="<?php print HELP_CASE_DETAILS; ?>"><img src="images/help.gif"></abbr>
              <table cellpadding="0" cellspacing="4" width="100%" class="readonly">
                <tr>
                  <td id="label1" width="40%" nowrap="nowrap"> <?php print CASE_ID; ?> </td>
                  <td id="value1">
                    <?php print $CASE_VALID_FIELDS['case_id']['value']; ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" style="width:40%" nowrap="nowrap"> <?php print CASE_OWNER; ?> </td>
                  <td id="value1">
                    <?php
			foreach ( array_keys($users) as $key )  {
				if ( $users[$key]['id'] == $CASE_VALID_FIELDS['owner_id']['value'] )  {
					print ucfirst( $users[$key]['name'] );
				}
			}
                    ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" style="width:40%" nowrap="nowrap"> <?php print CASE_INVESTIGATION_TITLE; ?> </td>
                  <td id="value1">
                    <?php print ucfirst( $CASE_VALID_FIELDS['investigation_title']['value'] ); ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" style="width:40%" nowrap="nowrap"> <?php print CASE_STATUS; ?> </td>
                  <td id="value1">
                    <?php print ucfirst( $CASE_VALID_FIELDS['case_open']['value'] ); ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" style="width:40%" nowrap="nowrap"> <?php print CASE_TYPE; ?> </td>
                  <td id="value1">
                    <?php print ucfirst( $CASE_VALID_FIELDS['investigation_type']['value'] ); ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" style="width:40%" nowrap="nowrap"> <?php print CASE_DATE; ?> </td>
                  <td id="value1">
                    <?php
			if ( $CASE_VALID_FIELDS['date']['value'] != '0000-00-00' )  {
				print $CASE_VALID_FIELDS['date']['value'];
			}
                    ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" style="width:40%" nowrap="nowrap"> <?php print CASE_TIME; ?> </td>
                  <td id="value1">
                    <?php
			print $CASE_VALID_FIELDS['time_hour']['value'] . ':';
			print $CASE_VALID_FIELDS['time_minute']['value'];
			print " " . $CASE_VALID_FIELDS['timezone']['value'];
                    ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" style="width:40%" nowrap="nowrap"> <?php print CASE_EXPIRATION; ?> </td>
                  <td id="value1">
                    <?php
			if ( $CASE_VALID_FIELDS['expiration_date']['value'] != '0000-00-00' )  {
				print $CASE_VALID_FIELDS['expiration_date']['value'];
			}
                    ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" style="width:40%" nowrap="nowrap"> <?php print CASE_RECAP_DATE; ?> </td>
                  <td id="value1">
                    <?php
			if ( $CASE_VALID_FIELDS['recap_date']['value'] != '0000-00-00' )  {
				print $CASE_VALID_FIELDS['recap_date']['value'];
			}
                    ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" style="width:40%" nowrap="nowrap"> <?php print CASE_RECAP_TIME; ?> </td>
                  <td id="value1">
                    <?php
			if ( !empty($CASE_VALID_FIELDS['recap_time_hour']['value']) &&
				!empty($CASE_VALID_FIELDS['recap_time_minute']['value']) )  {
				print $CASE_VALID_FIELDS['recap_time_hour']['value'] . ':';
				print $CASE_VALID_FIELDS['recap_time_minute']['value'];
				print " " . $CASE_VALID_FIELDS['recap_time_timezone']['value'];
			}
                    ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" style="width:40%" nowrap="nowrap"> <?php print CASE_RECAP_LOCATION; ?> </td>
                  <td id="value1" valign="top">
                    <?php print $CASE_VALID_FIELDS['recap_location']['value']; ?>
                    &nbsp;
                  </td>
                </tr>
              </table>
              <!-- Case Details -->


              <!-- Primary Contact Info -->
              <div style="height:2px;"> </div>
              <strong> <?php print CASE_LABEL_CONTACT_INFO; ?> </strong>
              <abbr title="<?php print HELP_CASE_CONTACT_INFO; ?>"><img src="images/help.gif"></abbr>
              <table cellpadding="0" cellspacing="4" width="100%" class="readonly">
                <tr>
                  <td id="label1" style="width:40%" nowrap="nowrap"> <?php print CASE_CONTACT_NAME; ?> </td>
                  <td id="value1">
                    <?php print $CASE_VALID_FIELDS['contact_name']['value']; ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" style="width:40%" nowrap="nowrap"> <?php print CASE_CONTACT_PRIMARY_PHONE; ?> </td>
                  <td id="value1">
                    <?php print $CASE_VALID_FIELDS['contact_primary_phone']['value']; ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" style="width:40%" nowrap="nowrap"> <?php print CASE_CONTACT_OFFICE_PHONE; ?> </td>
                  <td id="value1">
                    <?php print $CASE_VALID_FIELDS['contact_office_phone']['value']; ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" style="width:40%" nowrap="nowrap"> <?php print CASE_CONTACT_MOBILE_PHONE; ?> </td>
                  <td id="value1">
                    <?php print $CASE_VALID_FIELDS['contact_mobile_phone']['value']; ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" style="width:40%" nowrap="nowrap"> <?php print CASE_CONTACT_EMAIL; ?> </td>
                  <td id="value1">
                    <?php $CASE_VALID_FIELDS['contact_email']['value']; ?>
                    &nbsp;
                  </td>
                </tr>
              </table>
              <!-- Primary Contact Info -->


            </td>
            <td valign="top" style="padding-left:5px;">


              <!-- Location Details -->
              <p><strong> <?php print CASE_LABEL_ADDRESS; ?> </strong>
              <abbr title="<?php print HELP_CASE_LOCATION_DETAILS; ?>"><img src="images/help.gif"></abbr>
              <table cellpadding="0" cellspacing="4" class="readonly" width="100%">
                <tr>
                  <td id="label1" valign="top" style="width:28% !important" nowrap="nowrap"> <?php print CASE_ADDRESS; ?> </td>
                  <td id="value1" valign="top" style="height:70px;vertical-align:top;">
                    <?php print preg_replace( '/\n/', '<br />', $CASE_VALID_FIELDS['address']['value'] ); ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" style="width:28%" nowrap="nowrap"> <?php print CASE_CITY; ?> </td>
                  <td id="value1">
                    <?php print $CASE_VALID_FIELDS['city']['value']; ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" style="width:28%" nowrap="nowrap"> <?php print CASE_STATE; ?> </td>
                  <td id="value1">
                    <?php print $CASE_VALID_FIELDS['state']['value']; ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" style="width:28%" nowrap="nowrap"> <?php print CASE_ZIP; ?> </td>
                  <td id="value1">
                    <?php print $CASE_VALID_FIELDS['zip']['value'];  ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" style="width:28%" nowrap="nowrap"> <?php print CASE_COUNTRY; ?> </td>
                  <td id="value1">
                    <?php print strtoupper( $CASE_VALID_FIELDS['country']['value'] ); ?>
                    &nbsp;
                  </td>
                </tr>
                <tr>
                  <td id="label1" style="width:28%" nowrap="nowrap"> <?php print CASE_LOCATION_TYPE; ?> </td>
                  <td id="value1">
                    <?php print ucfirst( $CASE_VALID_FIELDS['loc_type']['value'] ); ?>
                    &nbsp;
                  </td>
                </tr>
              </table>
              <!-- Location Details -->


              <!-- Description -->
              <p><strong> <?php print CASE_LABEL_DESCRIPTION; ?> </strong>
              <table cellpadding="0" cellspacing="4" width="100%" class="readonly">
                <tr>
                  <td id="label1" style="width:25% !important;" valign="top"> <?php print CASE_DESCRIPTION; ?> </td>
                  <td id="value1" valign="top" style="vertical-align:top;height:175px;">
                    <?php print preg_replace( '/\n/', '<br />', $CASE_VALID_FIELDS['description']['value'] ); ?>
                    &nbsp;
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
            <td colspan="2" align="center" valign="top">

              <table align="center" cellpadding="0" cellspacing="4" width="100%" class="readonly">
                <tr>
                  <td valign="middle" align="center" id="label1-nowidth">
                    <strong> <?php print CASE_INVESTIGATORS; ?> </strong>
                  </td>
                  <td valign="middle" align="center" id="label1-nowidth">
                    <strong> Role </strong>
                  <td valign="middle" align="center" id="label1-nowidth">
                    <strong> Data Submitted </strong>
                  <td valign="middle" align="center" id="label1-nowidth">
                    <strong> Report Submitted </strong>
                  </td>
                </tr>
                <tr>
                    <?php
			global $NUM_INVESTIGATORS;

			$investigators = parse_investigators();
			$userids = array('');
			$username = array('');
			$roles = array('');
			$data = array('');
			foreach ( array_keys($investigators) as $key )  {
				array_push( $userids, $key );
				array_push( $roles, $investigators[$key]['role'] );
				array_push( $data, $investigators[$key]['data'] );

				foreach ( array_keys($users) as $uname )  {  // ick.
					if ( $users[$uname][$LOGIN_DB_ID_COL] == $key )  {
						array_push( $username, $users[$uname][$LOGIN_DB_NAME_COL] );
						break;
					}
				}
			}
			$investigators = array();
			for ( $i=1; $i<$NUM_INVESTIGATORS; $i++ )  {
				if ( !isset($userids[$i]) || empty($userids[$i]) )  {
					continue;
				}
                    ?>


                    <!-- Investigator <?php print $i; ?> -->
                <tr>
                  <td id="value1" align="center">
                        <?php print $username[$i]; ?>
                  </td>
                  <td id="value1" align="center">
                        <?php
				if ( $roles[$i] == 'obs' )  {  print 'Observer';  }
				elseif ( $roles[$i] == 'tech' )  {  print 'Technician';  }
				elseif ( $roles[$i] == 'psi' )  {  print 'Psychic';  }
				elseif ( $roles[$i] == 'research' )  {  print 'Researcher';  }
				elseif ( $roles[$i] == 'lead/obs' )  {   print 'Lead/Observer';  }
				elseif ( $roles[$i] == 'lead/tech' )  {  print 'Lead/Tech';  }
                        ?>
                  </td>
                  <td id="value1" align="center">
                        <?php
				if ( $data[$i] == 1 )  {  print "<span style=\"color:green;\">YES</a>\n";  }
				else  {  print "<span style=\"color:#ff0000;\">NO</a>\n";  }
                        ?>
                  </td>
                  <td id="value1" align="center">
                        <?php
				if ( report_exists($CASE_VALID_FIELDS['case_id']['value'], $userids[$i]) == 0 )  {
					print "<div style=\"padding-top:2px;\">";
					if ( report_check_perms('r', $CASE_VALID_FIELDS['case_id']['value'], $userids[$i]) == 0 )  {
						print "<a href=\"" . $REPORTSDB . "?task=view_report&case_id=" . $CASE_VALID_FIELDS['case_id']['value'] . "&owner_id=" . $userids[$i] . "\" class=\"green\" target=\"_blank\">YES</a>";
						if ( !is_printpage() )  {
							print "\n<span class=\"small\">(<a href=\"" . $REPORTSDB . "?task=view_report&case_id=" . $CASE_VALID_FIELDS['case_id']['value'] . "&owner_id=" . $userids[$i] . "\" target=\"_blank\" class=\"small\">" . TASK_VIEW_REPORT . "</a>)</span>";
						}
					}
					else  {
						print "<span class=\"green\">YES</span>\n";
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
                  </td>
                    <!-- Investigator <?php print $i; ?> -->
                    <?php  }  ?>
                </tr>

                  </td>
                </tr>
              </table>

            </td>
          </tr>
        </table>
        <!-- User Management -->


	<?php
		global $CASE_VIEW_NOTES_IN_PRINTPAGE;
		if ( !is_printpage() || $CASE_VIEW_NOTES_IN_PRINTPAGE == 1 )  {
        ?>
        <p>
        <!-- Notes -->
        <div id="reportheader">
          <strong> <?php print CASE_HEADER_NOTES; ?> </strong>
        </div>
        <table cellspacing="4" cellpadding="0" align="center" style="padding:5px 0 5px 0" class="form_table_main">
          <tr>
            <td id="label1-nowidth" valign="top" style="width:15%;white-space:nowrap">
              <?php print CASE_NOTES; ?>
            </td>
            <td valign="top">
              <div id="readonly-value" style="margin:2px;padding:4px;width:auto !important;width:100%;">
              <div class="prop-small"> </div>
                <?php
			if ( !empty($CASE_VALID_FIELDS['notes']['value']) )  {
				print preg_replace( '/\n/', '<br />', $CASE_VALID_FIELDS['notes']['value'] );
			}
			else  {
				print '&nbsp;';
			}
                ?>
              <div class="clear"> </div>
            </td>
          </tr>
          <tr>
            <td> </td>
          </tr>
        </table>
        </div>
        <!-- Notes -->
        <?php
		}
        ?>

      </div>
      <!-- Center Column -->

    </td>
  </tr>
</table>


		
