 <?php
	global $CASE_VALID_FIELDS, $PRINTABLE_WIDTH;
 ?>

 <table cellspacing="0" cellpadding="0" align="center" width="<?php print $PRINTABLE_WIDTH; ?>" class="print_header">
   <tr>
     <td style="padding:4px;" width="175">
       <img src="images/logo.gif" style="border:2px solid black;">
     </td>
     <td valign="top" align="left">
       <div style="padding: 4px 4px 10px 4px;">

         <?php
		if ( $_REQUEST['task'] == 'view_case' )  { // view_case header.
         ?>
         <span class="large"><strong><?php print $CASE_VALID_FIELDS['investigation_title']['value']; ?></strong></span> <br />
         <strong> <?php print CASE_ID; ?> </strong> <?php print $CASE_VALID_FIELDS['case_id']['value']; ?> <br />
         <?php
		if ( !empty($CASE_VALID_FIELDS['city']['value']) )  {
			print '<strong> Location: </strong>';
			print ucfirst( $CASE_VALID_FIELDS['city']['value'] );
			if ( !empty($CASE_VALID_FIELDS['state']['value']) )  {
				print ', ' . strtoupper($CASE_VALID_FIELDS['state']['value']);
			}
			if ( !empty($CASE_VALID_FIELDS['country']['value']) )  {
				print ' (' . strtoupper($CASE_VALID_FIELDS['country']['value']) . ')';
			}
			print "<br />\n";
		}

		if ( !empty($CASE_VALID_FIELDS['date']['value']) && $CASE_VALID_FIELDS['date']['value'] != '0000-00-00' )  {
			print '<strong> ' . CASE_DATE . ' </strong>';
			print $CASE_VALID_FIELDS['date']['value'];
		}
         ?>
         <?php

		}  // End view_case header.

		elseif ( $_REQUEST['task'] == 'view_report' )  { // view_report header.

         ?>

         <?php
		$case_id = $REPORTS_VALID_FIELDS['case_id']['value'];
		$case_info = list_case_info( $case_id );
         ?>
         <span class="large"><strong><?php print $case_info[$case_id]['investigation_title']; ?></strong></span> <br />
         <strong> <?php print CASE_ID; ?> </strong> <?php print $REPORTS_VALID_FIELDS['case_id']['value']; ?> <br />
         <?php
		if ( !empty($case_info[$case_id]['city']) )  {
			print '<strong> Location: </strong>';
			print ucfirst( $case_info[$case_id]['city'] );
			if ( !empty($case_info[$case_id]['state']) )  {
				print ', ' . strtoupper($case_info[$case_id]['state']);
			}
			if ( !empty($case_info[$case_id]['state']) )  {
				print ' (' . strtoupper($case_info[$case_id]['state']) . ')';
			}
			print "<br />\n";
		}

		if ( !empty($case_info[$case_id]['date']) && $case_info[$case_id]['date'] != '0000-00-00' )  {
			print '<strong> ' . CASE_DATE . ' </strong>';
			print $case_info[$case_id]['date'];
		}
         ?>
         <?php  }  // End view_report header. ?>

       </div>
     </td>
   </tr>
 </table>


