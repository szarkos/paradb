<?php
	global $db_connection, $REPORTSDB;
	global $CASE_VALID_FIELDS, $REPORTS_VALID_FIELDS, $TASK_REQ_PERMS, $SEARCH;
?>


      <!-- Left Column -->
      <div class="left_column_outer">
        <div class="left_column_inner">
          <div class="left_col_prop"> </div>

          <!-- Home -->
          <a href="<?php print $REPORTSDB ?>"><?php print NAVBAR_HOME; ?></a> <br />


          <!-- Open Case -->
          <?php  if ( $_SESSION['user_perm'] >= $TASK_REQ_PERMS['edit_case'] )  {  ?>
          <a href="<?php print $REPORTSDB ?>?task=edit_case"><?php print NAVBAR_OPEN_CASE; ?></a> <br />
          <?php  }  ?>


          <!-- Write Report -->
          <?php
		$unfinished = list_unfinished_reports($_SESSION['user_id']);
		foreach ( array_keys($unfinished) as $key )  {
			if ( check_case_expired($key) )  {
				unset( $unfinished[$key] );
			}
		}
		if ( !empty($unfinished) )  {
			print "<a href=\"" . $REPORTSDB . "?task=edit_report\">" . NAVBAR_WRITE_REPORT . "</a> <br/>\n";
		}
		else  {
			print "<span style=\"color:grey;text-decoration:underline\">" . NAVBAR_WRITE_REPORT . "</span> <br/>\n";
		}
		unset( $unfinished );
          ?>


          <!-- User Management -->
          <?php  if ( $_SESSION['user_perm'] >= $TASK_REQ_PERMS['user_mgmt'] )  {  ?>
          <a href="<?php print $REPORTSDB ?>?task=user_mgmt"><?php print NAVBAR_USER_MANAGEMENT; ?></a> <br />
          <?php  }  ?>


          <!-- Close Expired Cases -->
          <?php  if ( $_SESSION['user_perm'] >= $TASK_REQ_PERMS['edit_case'] )  {  ?>
          <a href="<?php print $REPORTSDB ?>?task=close_expired"><?php print NAVBAR_CLOSE_EXPIRED; ?></a> <br />
          <?php  }  ?>


          <!-- Statistics -->
          <a href="<?php print $REPORTSDB; ?>?task=showstats"><?php print NAVBAR_STATISTICS; ?></a> <br />


          <!-- Logout -->
          <?php
          if ( !empty($_SESSION) )  {
		print "<a href=\"" . $REPORTSDB . "?logout\">" . NAVBAR_LOGOUT . "</a>\n";
          }
          ?>


          <!-- My Reports -->
          <?php
		$data = mysql_get_rows( 'reportsdb_reports', 'case_id', 'owner_id=' . $_SESSION['user_id'], 'case_id', NULL, $db_connection );
		$numrows = mysql_num_rows( $data );
		if ( $numrows > 0 )  {
          ?>
          <br /><br />
          <strong> <?php print NAVBAR_MY_REPORTS; ?> </strong>
          <form action="<?php print $REPORTSDB; ?>" method="post">
            <input type="hidden" name="task" value="view_report" />
            <input type="hidden" name="owner_id" value="<?php print $_SESSION['user_id']; ?>" />
            <select name="case_id" onchange="JavaScript:submit();" style="width:100px;">
          <?php
                $selected = "";
		print "<option> \n";
		for ( $i=0; $i<$numrows; $i++ )  {
			$case_id = mysql_result( $data, $i, 'case_id' );
			if ( isset($_REQUEST['task']) && ($_REQUEST['task'] == 'view_report' || $_REQUEST['task'] == 'view_report') )  {
				if ( $case_id == $REPORTS_VALID_FIELDS['case_id']['value'] )  {
					$selected = 'selected';
				}
			}
			print "<option value=\"" . $case_id . "\"" . $selected . ">" . $case_id  . "\n";
		}
          ?>
            </select>
            <abbr title="<?php print HELP_NAVBAR_MY_REPORTS; ?>"><img src="images/help.gif"></abbr>
          </form>
          <br /><span style="padding-left:25px;"> <a href="<?php print $REPORTSDB; ?>?task=reports_view_all&owner_id=<?php print $_SESSION['user_id']; ?>" style="text-decoration:none;">View All</a> </span>
          <?php  }  // End if.  ?>
          <!-- My Reports -->


          <!-- Case Select -->
          <?php
		$data = mysql_get_rows( 'reportsdb_cases', 'case_id', NULL, NULL, NULL, $db_connection );
		$numrows = mysql_num_rows( $data );
		if ( $numrows > 0 )  {
          ?>
          <br /><br />
          <strong> <?php print NAVBAR_CASE_SELECT; ?> </strong>
          <form action="<?php print $REPORTSDB; ?>" method="post">
            <input type="hidden" name="task" value="view_case" />
            <select name="case_id" onchange="JavaScript:submit();" style="width:100px;">
          <?php
		print "<option value=\"\"> \n";
                $selected = "";
		for ( $i=0; $i<$numrows; $i++ )  {
			$case_id = mysql_result( $data, $i, 'case_id' );
			print "<option ";
			if ( $case_id == $CASE_VALID_FIELDS['case_id']['value'] )  {
				print "selected ";
			}
			print "value=\"" . $case_id . "\">" . $case_id  . "\n";
		}
          ?>
            </select>
            <abbr title="<?php print HELP_NAVBAR_CASE_SELECT; ?>"><img src="images/help.gif"></abbr>
          </form>
          <br /><span style="padding-left:25px;"> <a href="<?php print $REPORTSDB; ?>?task=cases_view_all" style="text-decoration:none;">View All</a> </span>
          <?php  }  // End if.  ?>
          <!-- Case Select -->


          <!-- My Cases -->
          <?php
		$data = mysql_get_rows( 'reportsdb_cases', 'case_id', 'owner_id=' . $_SESSION['user_id'], 'case_id', NULL, $db_connection );
		$numrows = mysql_num_rows( $data );
		if ( $numrows > 0 )  {
          ?>
          <br /><br />
          <strong> <?php print NAVBAR_MY_CASES; ?> </strong>
          <form action="<?php print $REPORTSDB; ?>" method="post">
            <input type="hidden" name="task" value="view_case" />
            <input type="hidden" name="owner_id" value="<?php print $_SESSION['user_id']; ?>" />
            <select name="case_id" onchange="JavaScript:submit();" style="width:100px;">
          <?php
		print "<option> \n";
		for ( $i=0; $i<$numrows; $i++ )  {
			$selected = "";
			$case_id = mysql_result( $data, $i, 'case_id' );
			if ( isset($_REQUEST['task']) && ($_REQUEST['task'] == 'edit_case' || $_REQUEST['task'] == 'view_case') )  {
				if ( $case_id == $CASE_VALID_FIELDS['case_id']['value'] )  {
					$selected = 'selected';
				}
			}
			print "<option value=\"" . $case_id . "\"" . $selected . ">" . $case_id  . "\n";
		}
          ?>
            </select>
            <abbr title="<?php print HELP_NAVBAR_MY_CASES; ?>"><img src="images/help.gif"></abbr>
          </form>
          <br /><span style="padding-left:25px;"> <a href="<?php print $REPORTSDB; ?>?task=mycases_view&owner_id=<?php print $_SESSION['user_id']; ?>" style="text-decoration:none;">View All</a> </span>
          <?php  }  // End if.  ?>
          <!-- My Cases -->


          <!-- Search -->
          <br /><br />
          <b> Search </b> <br />
          <form action="<?php print $REPORTSDB; ?>" method="post">
            <input type="hidden" name="task" value="search" />
            <input type="hidden" name="owner_id" value="<?php print $_SESSION['user_id']; ?>" />
            <input type="hidden" name="last_search" value="<?php print $SEARCH['last_search']['value']; ?>" />
            <input type="text" name="search_query" size="16" value="<?php print $SEARCH['search_query']['value']; ?>" />
            <div style="padding:2px;"></div>
            <button type="submit">Search</button>
          </form>
          <!-- Search -->

          <div style="padding-top: 80px;"> </div>
          <div class="clear"> </div>
        </div>
      </div>
      <!-- Left Column -->

