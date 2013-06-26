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
      <div id="center_column">

        <?php
          if ( !empty($ERR_MSG) )  {
        ?>
        <div class="error">
          <?php  print $ERR_MSG . "\n";  ?>
        </div>
        <?php  }  ?>


        <?php
		if ( isset($_REQUEST['confirm_delete']) && $_REQUEST['confirm_delete'] == 0 )  {
			global $LOGIN_DB_USER_COL, $LOGIN_DB_NAME_COL, $USER_FIELDS;
			if ( isset($_REQUEST[$LOGIN_DB_USER_COL]) || !empty($_REQUEST[$LOGIN_DB_USER_COL]) )  {
				if ( user_exists($_REQUEST[$LOGIN_DB_USER_COL]) )  {
					// Need to make sure vars a clean before we print them.
					set_user_fields( $_REQUEST[$LOGIN_DB_USER_COL] );

        ?>
        <table width="100%" style="margin-left:25px">
          <tr>
            <td>
              <h2> Confirm Delete </h2>
              Are you sure you would like to delete user
              <strong> <?php print $USER_FIELDS[$LOGIN_DB_NAME_COL]['value'] . ' (' . $USER_FIELDS[$LOGIN_DB_USER_COL]['value'] . ')?'; ?> </strong>

              <p>
              <strong>Note:</strong> There is no way to undo this action.

              <p>
              <form name="confirm_del" action="<?php print $REPORTSDB; ?>" method="post">
                <input type="hidden" name="task" value="del_user" />
                <input type="hidden" name="<?php print $LOGIN_DB_USER_COL; ?>" value="<?php print $USER_FIELDS[$LOGIN_DB_USER_COL]['value']; ?>" />
                <input type="hidden" name="confirm_delete" value="1" />
                <button type="submit">Delete User</button> &nbsp;&nbsp;
                <button type="submit" onClick="javascript:document.confirm_del.task.value='user_mgmt';">Cancel</button>
              </form>
              <br /><br />
            </td>
          </tr>
        </table>
        <?php
				}
			}
		}
        ?>


	<!-- User Management -->
        <table align="center" cellpadding="0" cellspacing="0" width="100%" style="padding-left: 10px;">
          <tr>
            <td>

              <?php
		if ( isset($_REQUEST['task']) )  {
			if ( $_REQUEST['task'] == 'add_user' || $_REQUEST['task'] == 'edit_user' )  {
				print_add_user_form();
			}
			elseif ( $_REQUEST['task'] == 'del_user' )  {
				print_del_user_form();
			}
			else  {
				print_user_mgmt_list();
			}
		}
		else  {
			print_user_mgmt_list();
		}
              ?>

            </td>
          </tr>
        </table>
	<!-- User Management -->


      </div>
      <!-- Center Column -->


    </tr>
  </td>
</table>


