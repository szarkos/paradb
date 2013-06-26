<?php  global $REPORTSDB, $ERR_MSG;  ?>

<table width="100%" cellpadding="0" cellspacing="0" align="center">
  <tr>
    <td style="height: 5px;"> </td>
  </tr>
  <tr>
    <td valign="top" style="width:150px;">

      <?php  include('left_navbar.inc.php');  ?>

    </td>
    <td valign="top" style="padding:0;margin:0">

      <!-- Center Column -->
      <div class="center_column">

        <?php
          if ( $ERR_MSG != "" )  {
        ?>
        <table width="100%" align="center">
          <tr>
            <td align="center">
              <div class="error">
                <?php  print $ERR_MSG . "\n";  ?>
              </div>
            </td>
          </tr>
        </table>
        <br /><br />
        <?php  }  ?>

        <table align="center" cellpadding="0" cellspacing="0" width="100%" style="padding-left:4px;">
          <tr>
            <td valign="top">

              <br />
		<?php
			if ( $_REQUEST['task'] == 'cases_view' )  {
				// Print all cases owner_id has been on.
				print_cases( $_REQUEST['owner_id'] );
			}
			elseif ( $_REQUEST['task'] == 'mycases_view' )  {
				// Print all cases *owned* by current owner_id.
				print_mycases( $_REQUEST['owner_id'] );
			}
			elseif ( $_REQUEST['task'] == 'cases_view_all' )  {
				print_cases_all();
			}
			elseif ( $_REQUEST['task'] == 'reports_view_all' )  {
				// Print all reports owned by current owner_id.
				print_reports( $_REQUEST['owner_id'] );
			}
		?>

            </td>

            <td id="stats_userlist_body_outer">
              <div id="stats_userlist_body_outer">

                <div id="reportheader">
                  <div class="row">
                    <strong> <?php print STATS_GENERAL; ?> </strong>
                  </div>
                </div>

                <div class="stats_general_body_inner">
                  <?php
			$users = get_num_users();
			$cases = get_num_cases();
			$reports = get_num_reports();
                  ?>
                  <table cellpadding="0" cellspacing="4" class="readonly">
                    <tr>
                      <td id="label1" nowrap="nowrap"> <?php print STATS_TOTAL_USERS . " "; ?> </td>
                      <td id="value1">
                        <?php print $users['total_users']; ?>
                      </td>
                    </tr>
                    <tr>
                      <td id="label1" nowrap="nowrap"> <?php print STATS_TOTAL_USERS_LEADS . " "; ?> </td>
                      <td id="value1">
                        <?php print $users['team-lead']+$users['administrator']; ?>
                      </td>
                    </tr>
                    <tr>
                      <td id="label1" nowrap="nowrap"> <?php print STATS_TOTAL_CASES . " "; ?> </td>
                      <td id="value1">
                        <?php print $cases['total_cases']; ?>
                      </td>
                    </tr>
                    <tr>
                      <td id="label1" nowrap="nowrap">  <?php print STATS_TOTAL_OPEN_CASES . " "; ?> </td>
                      <td id="value1">
                        <?php print $cases['open']; ?>
                      </td>
                    </tr>
                    <tr>
                      <td id="label1" nowrap="nowrap"> <?php print STATS_TOTAL_REPORTS . " "; ?> </td>
                      <td id="value1">
                        <?php print $reports['total_reports']; ?>
                      </td>
                    </tr>
                    <tr>
                      <td id="label1" nowrap="nowrap"> <?php print STATS_TOTAL_PUBLISHED_REPORTS . " "; ?> </td>
                      <td id="value1">
                        <?php print $reports['published']; ?>
                      </td>
                    </tr>
                  </table>
                </div>

                <!-- User List -->
                <div style="padding:5px;"> </div>
                <div id="reportheader">
                  <div class="row">
                    <strong> <?php print STATS_USERLIST; ?> </strong>
                  </div>
                </div>
                <div class="stats_userlist_body_inner">
                  <?php
			print_user_stats_list();
                  ?>
                </div>
                <!-- User List -->

              </div>
            </td>
          </tr>
        </table>

      </div>
      <!-- Center Column -->


    </tr>
  </td>
</table>

