<?php  global $REPORTSDB, $ERR_MSG;  ?>


<table width="100%" cellpadding="0" cellspacing="0" align="center">
  <tr>
    <td style="height: 5px;"> </td>
  </tr>
  <tr>
    <td valign="top" style="width:150px;">

      <?php  include('left_navbar.inc.php');  ?>

    </td>
    <td valign="top" style="padding:0px;margin:0;">

      <!-- Center Column -->
      <div id="center_column">

        <?php
          if ( $ERR_MSG != "" )  {
        ?>
        <div class="error">
          <?php  print $ERR_MSG . "\n";  ?>
        </div>
        <?php  }  ?>


	<!-- Search Results -->
        <table align="center" cellpadding="0" cellspacing="0" width="100%" style="padding-left: 10px;">
          <tr>
            <td>

              <?php
		print_search_results();
              ?>

            </td>
          </tr>
        </table>
	<!-- Search Results -->


      </div>
      <!-- Center Column -->


    </tr>
  </td>
</table>


