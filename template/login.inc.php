<?php global $REPORTSDB, $TITLE; ?>

<html>
<head>
  <title> <?php print $TITLE; ?> </title>
  <link rel="stylesheet" href="template/template.css" type="text/css" />
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

<table width="100%" cellpadding="2" cellspacing="0" align="center" border="0">
  <tr>
    <td height="40">
      &nbsp;
    </td>
  </tr>
  <tr>
    <td>

      <form action="<?php print $REPORTSDB; ?>" name="login" method="post">
      <?php
	if ( isset($_REQUEST['task']) && !empty($_REQUEST['task']) )  {
		print "<input type=\"hidden\" name=\"task\" value=\"" . $_REQUEST['task'] . "\" />\n";
	}
	if ( isset($_REQUEST['case_id']) && !empty($_REQUEST['case_id']) )  {
		print "<input type=\"hidden\" name=\"case_id\" value=\"" . $_REQUEST['case_id'] . "\" />\n";
	}
	if ( isset($_REQUEST['owner_id']) && !empty($_REQUEST['owner_id']) )  {
		print "<input type=\"hidden\" name=\"owner_id\" value=\"" . $_REQUEST['owner_id'] . "\" />\n";
	}
	if ( isset($_REQUEST['view']) && !empty($_REQUEST['view']) )  {
		print "<input type=\"hidden\" name=\"view\" value=\"" . $_REQUEST['view'] . "\" />\n";
	}
      ?>

      <table cellpadding="2" cellspacing="0" align="center" class="login">
        <tr>
          <td colspan="2" align="center" class="login_header">
            <b> Login &gt; </b>
          </td>
        </tr>
        <tr>
          <td colspan="2" align="center" class="small">
              Cookies must be enabled beyond this point.
          </td>
        </tr>
        <tr>
          <td class="small">
            &nbsp;
          </td>
        </tr>
        <tr>
          <td>
            &nbsp; <?php print LOGIN_USERNAME; ?>:
          </td>
          <td align="center">
            <input type="text" name="username" size="18" maxlength="25" />
          </td>
        </tr>
        <tr>
          <td>
            &nbsp; <?php print LOGIN_PASSWORD; ?>:
          </td>
          <td align="center">
            <input type="password" name="password" size="18" maxlength="25" />
          </td>
        </tr>
        <tr>
          <td> &nbsp; </td>
          <td style="padding-left:14px;">
            <input type="checkbox" name="rememberme" value="1" />
            <?php print LOGIN_REMEMBERME; ?>
          </td>
        </tr>
        <tr>
          <td colspan="2" align="center">
           <br />
           <input type="submit" value="Login">
           <input type="reset" value="Clear">
          </td>
        </tr>

	<script type="text/javascript">
	// <!--
		document.login.username.focus();
	// -->
	</script>

        <?php  if ( $msg != "" )  {  ?>

          <tr>
            <td colspan="2" align="center" class="error" style="border:0px;">
              <?php print $msg; ?>
            </td>
          </td>

        <?php  }  ?>

        <tr>
          <td colspan="2">
            <font size="1"> &nbsp; </font>
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>


