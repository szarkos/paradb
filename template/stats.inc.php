<?php  global $REPORTSDB, $ERR_MSG;  ?>

 <script type="text/javascript" src="template/js/jquery.js"></script>
 <script type="text/javascript">
        // Links for jquery.js, used for animated show/hide links.
        // Some of these need to be autogenerated with PHP, so were putting this directly
        // in the template.
        $(document).ready(function() {
                $('#adv_search-toggle').click(function() {$('#adv_search').slideToggle(450);return false;});
                $('#gen_plots-toggle').click(function() {$('#gen_plots').slideToggle(450);return false;});
        });
 </script>


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
      <div class="center_column">

        <?php
          if ( !empty($ERR_MSG) )  {
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

        <table align="center" cellpadding="0" cellspacing="0" width="100%" style="padding-left:4px">
          <tr>
            <td valign="top" style="vertical-align:top">

              <!-- Advanced Search -->
              <div id="reportheader" style="padding-top:20px;">
                <div class="row">
                  <span class="left"> <strong><?php print ADVANCED_SEARCH; ?></strong> </span>
                  <span class="right"> <a href="#" id="adv_search-toggle">Show/Hide</a> </span>
                </div>
              </div>

              <div id="adv_search" style="display:block">
                <form name="adv_search" action="<?php print $REPORTSDB; ?>" method="post">

                  <table width="100%">
                    <tr>
                      <td valign="middle">
                        <strong> Search Query </strong>
                        <input type="text" size="20" name="adv_query" id="adv_query"/>
                      </td>
                      <td valign="middle">
                        <strong> Document Type </strong>
                        <select id="adv_search_doc_type" name="adv_search_doc_type" size="1">
                          <option value="any" selected>Any</option>
                          <option value="case">Case</option>
                          <option value="report">Report</option>
                        </select>

                        <br/>
                        <table width="100%">
                          <tr>
                            <td valign="middle">

                              <strong> Month </strong>
                              <select id="adv_search_month" name="adv_search_month" size="1">
                                <option value="any" selected>Any</option>
                                <option value="01">January</option>
                                <option value="02">February</option>
                                <option value="03">March</option>
                                <option value="04">April</option>
                                <option value="05">May</option>
                                <option value="06">June</option>
                                <option value="07">July</option>
                                <option value="08">August</option>
                                <option value="09">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                              </select>

                            </td>
                            <td valign="middle">

                              <strong> Year </strong>
                              <select id="adv_search_year" name="adv_search_year" size="1">
                                <option value="any" selected>Any</option>
                                <?php
					// FIXME: list relevant years.
                                ?>
                              </select>

                            </td>
                          </tr>
                        </table>

                      </td>
                    </tr>
                  </table>

                </form>
              </div>
              <!-- Advanced Search -->


              <!-- Generate Plots -->
              <div id="reportheader" style="padding-top:20px;">
                <div class="row">
                  <span class="left"> <strong><?php print GENERATE_PLOTS; ?></strong> </span>
                  <span class="right"> <a href="#" id="gen_plots-toggle">Show/Hide</a> </span>
                </div>
              </div>
              <div id="gen_plots" style="display:block">
                <table width="100%">
                  <tr>
                    <td valign="middle">
                      <strong> Data Type </strong>
                      <select id="gen_plot_type" name="gen_plot_type" size="1">
                        <option value="geomagnetic" selected>Geomagnetic</option>
                        <option value="xray">Xray</option>
                      </select>
                    </td>
                    <td valign="middle">
                      <strong> Year </strong>
                      <select id="gen_plot_year" name="gen_plot_year" size="1">
                        <option value="any" selected>Any</option>
                        <?php
				// FIXME: list relevant years.
                        ?>
                      </select>
                    </td>
                  </tr>
                </table>

              </div>
              <!-- Generate Plots -->

<?php
	// todo:
	//	open plots in new window.
	//	build search engine.
	//		link back to search.
	//	generate relevant years.
?>

                <?php
//			$status = print_geomag_plot();
//			$status = print_xray_plot();
//			if ( file_exists('images/stats_images/geomag_stats.png') )  {
                ?>
<!--
                <img src="images/stats_images/geomag_stats.png"> <br/>
                <img src="images/stats_images/xray_stats.png">

                <br /><br /><br />
                <strong> Note: </strong> The above graph is just a sample of the sort of plots and
                statistics we can generate here. This section will continue to be developed in later
                releases.
                <?php
//			}
//			if ( $status == ERR_UNDEF )  {
//				print "<p><div style=\"text-align:left\">\n";
//				print "<strong>Note:</strong> Unable to access PEAR package <a href=\"http://pear.php.net/package/Image_Graph\" target=\"_blank\">Image_Graph</a>.\n";
//				print "</div></p>\n";
//			}
//			elseif ( $status == ERR_FWRITE )  {
//				global $server_path;
//				print "<p><div style=\"text-align:left\">\n";
//				print "<strong>Note:</strong> Unable to write to " . $server_path . "/images/stats_images.\n";
//				print "</div></p>\n";
//			}
                ?>

                <br/> &nbsp;
              </div>
-->


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


