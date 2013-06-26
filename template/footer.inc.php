 <?php
	if ( is_printpage() )  {
		global $PRINTABLE_WIDTH;
		$width = $PRINTABLE_WIDTH;
	}
	else  {
		$width = "100%";
	}
 ?>

<div style="height:30px;"> </div>

<?php  if ( !is_printpage() )  {  ?>
<table cellpadding="0" cellspacing="0" border="0" width="<?php print $width; ?>" align="center">
  <tr>

    <?php  if ( !empty($_SESSION) )  {  ?>
    <td class="footer_left">
      &nbsp;
    </td>
    <?php  }  ?>

    <td class="footer_right">
	<?php
		global $ADMIN_EMAIL;
		print POWERED . "<br />\n";
		print "Copyright &copy; " . date("Y") . " <a href=\"mailto:obsid@sentry.net\" style=\"text-decoration:none;\">" . DEV_NAME . "</a> <br />\n";
        ?>
    </td>

  </tr>
</table>
<div style="height:15px;"> </div>
<?php  }  ?>

</div> <!-- Closing #headerdiv. -->
<div id="dhtmldiv" style="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></div>

</body>
</html>
