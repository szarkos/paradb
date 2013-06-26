<?php

 //-------------------------------------------------------------------//
 //
 // AGHOST Paranormal Reporting Database (ParaDB)
 // Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>
 //
 // See the file COPYING for terms of use and redistribution.
 //
 // File: report.inc.php
 //	  This file declares variables and arrays needed for working
 //	  with reports.
 //
 //-------------------------------------------------------------------//


///////////////////////////////////////////////////////////////////////////////////////////////
// Internal Variables - Do Not Edit.
///////////////////////////////////////////////////////////////////////////////////////////////

 // $REPORTS_VALID_FIELDS
 // Report header, data from the reportsdb_reports table.
 // First array key is the name of a column in the database.
 // 'value' is propogated with data from the form or from the database.
 // 'maxlength' is the maximum allowed length of the data in 'value'.
 $REPORTS_VALID_FIELDS = array ( 'case_id'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"20",
									'required'	=>	"yes" ),

				 'owner_id'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"10",
									'required'	=>	"yes" ),

				 'case_title'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"50",
									'required'	=>	"yes" ),

				 'date'			=>	array ( 'value'		=>	"",
									'maxlength'	=>	"12",
									'required'	=>	"yes" ),

				 'report_edit_date'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"12",
									'required'	=>	"no" ),

				 'start_time_minute'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"2",
									'required'	=>	"yes" ),

				 'start_time_hour'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"2",
									'required'	=>	"yes" ),

				 'end_time_minute'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"2",
									'required'	=>	"yes" ),

				 'end_time_hour'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"2",
									'required'	=>	"yes" ),

				 'description'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"50000",
									'required'	=>	"no" ),

				 'report_state'		=>	array ( 'value'		=>	"unpublished",
									'maxlength'	=>	"20",
									'required'	=>	"yes" ),

				 'geomag_kp'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"35",
									'required'	=>	"no" ),

				 'geomag_ap'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"5",
									'required'	=>	"no" ),

				 'geomag_summary'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"30",
									'required'	=>	"no" ),

				 'geomag_list'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"100",
									'required'	=>	"no" ),

				 'geomag_plot'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"100",
									'required'	=>	"no" ),

				 'xray_long'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"15",
									'required'	=>	"no" ),

				 'xray_short'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"15",
									'required'	=>	"no" ),

				 'xray_high'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"15",
									'required'	=>	"no" ),

				 'xray_low'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"15",
									'required'	=>	"no" ),

				 'xray_peak'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"30",
									'required'	=>	"no" ),

				 'xray_summary'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"30",
									'required'	=>	"no" ),

				 'xray_list'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"100",
									'required'	=>	"no" ),

				 'xray_plot'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"100",
									'required'	=>	"no" ),

				 'moon_image'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"100",
									'required'	=>	"no" ),

				 'moon_phase'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"15",
									'required'	=>	"no" ),

				 'moon_illum'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"15",
									'required'	=>	"no" ),
 ); // End $REPORTS_VALID_FIELDS


 // $REPORTS_IMPRESSIONS_VALID_FIELDS
 // Impression data from the report, data from the reportsdb_reports_impressions table.
 // First array key is the name of a column in the database.
 // 'value' is propogated with data from the form or from the database.
 // 'maxlength' is the maximum allowed length of the data in 'value'.
 $REPORTS_IMPRESSIONS_VALID_FIELDS = array (	'case_id'		=>	array ( 'value'		=>	"",
											'maxlength'	=>	"20",
											'required'	=>	"yes" ),

					 	'owner_id'		=>	array ( 'value'		=>	"",
											'maxlength'	=>	"10",
											'required'	=>	"yes" ),

				 		'outside_impr'		=>	array ( 'value'		=>	"",
											'maxlength'	=>	"50000",
											'required'	=>	"no" ),

				 		'walkin_impr'		=>	array ( 'value'		=>	"",
											'maxlength'	=>	"50000",
											'required'	=>	"no" ),

				 		'closing_impr'		=>	array ( 'value'		=>	"",
											'maxlength'	=>	"50000",
											'required'	=>	"no" ),
 );  // End $REPORTS_IMPRESSIONS_VALID_FIELDS


 // $REPORTS_ROOM_DATA_VALID_FIELDS
 // Data from the report, data from the reportsdb_room_data table.
 // First array key is the name of a column in the database.
 // 'value' is propogated with data from the form or from the database.
 // 'maxlength' is the maximum allowed length of the data in 'value'.
 $REPORTS_ROOM_DATA_VALID_FIELDS = array ( 'case_id'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"20",
									'required'	=>	"yes" ),

				'owner_id'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"10",
									'required'	=>	"yes" ),

				'room_id'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"50",
									'required'	=>	"yes" ),

				'ac_emf_electric'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"25",
									'required'	=>	"no" ),

				'ac_emf_electric_units'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"10",
									'required'	=>	"no" ),

				'ac_emf_magnetic'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"25",
									'required'	=>	"no" ),

				'ac_emf_magnetic_units'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"10",
									'required'	=>	"no" ),

				'dc_emf_electric'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"25",
									'required'	=>	"no" ),

				'dc_emf_electric_units'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"10",
									'required'	=>	"no" ),

				'dc_emf_magnetic'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"25",
									'required'	=>	"no" ),

				'dc_emf_magnetic_units'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"10",
									'required'	=>	"no" ),

				'dc_emf_sum'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"25",
									'required'	=>	"no" ),

				'dc_emf_sum_units'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"10",
									'required'	=>	"no" ),

				'temp'			=>	array ( 'value'		=>	"",
									'maxlength'	=>	"25",
									'required'	=>	"no" ),

				'temp_units'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"10",
									'required'	=>	"no" ),

				'rel_humidity'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"25",
									'required'	=>	"no" ),

				'rel_humidity_units'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"10",
									'required'	=>	"no" ),

				'barometric'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"25",
									'required'	=>	"no" ),

				'barometric_units'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"10",
									'required'	=>	"no" ),

				'tech_anomaly_data_1'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'tech_anomaly_data_2'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'tech_anomaly_data_3'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'tech_anomaly_data_4'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'tech_anomaly_data_5'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'tech_anomaly_data_6'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'tech_anomaly_data_7'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'tech_anomaly_data_8'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'tech_anomaly_data_9'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'anomaly_anomaly_data_1'=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'anomaly_anomaly_data_2'=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'anomaly_anomaly_data_3'=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'anomaly_anomaly_data_4'=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'anomaly_anomaly_data_5'=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'anomaly_anomaly_data_6'=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'anomaly_anomaly_data_7'=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'anomaly_anomaly_data_8'=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'anomaly_anomaly_data_9'=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'evp_anomaly_data_1'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'evp_anomaly_data_2'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'evp_anomaly_data_3'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'evp_anomaly_data_4'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'evp_anomaly_data_5'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'evp_anomaly_data_6'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'evp_anomaly_data_7'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'evp_anomaly_data_8'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'evp_anomaly_data_9'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'video_anomaly_data_1'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'video_anomaly_data_2'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'video_anomaly_data_3'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'video_anomaly_data_4'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'video_anomaly_data_5'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'video_anomaly_data_6'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'video_anomaly_data_7'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'video_anomaly_data_8'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'video_anomaly_data_9'	=>	array ( 'value'		=>	"",
									'maxlength'	=>	"1200",
									'required'	=>	"no" ),

				'notes'			=>	array ( 'value'		=>	"",
									'maxlength'	=>	"50000",
									'required'	=>	"no" ),
 );  // End $REPORTS_ROOM_DATA_VALID_FIELDS


 // Create and set of the ROOM_DATA_{1..nn} arrays.
 for ( $i=1; $i<$NUM_ROOMS+1; $i++ )  {
	${'ROOM_DATA_'.$i} = array();
	foreach ( array_keys($REPORTS_ROOM_DATA_VALID_FIELDS) as $key )  {
		${'ROOM_DATA_'.$i}[$key]['value'] = $REPORTS_ROOM_DATA_VALID_FIELDS[$key]['value'];
		${'ROOM_DATA_'.$i}[$key]['maxlength'] = $REPORTS_ROOM_DATA_VALID_FIELDS[$key]['maxlength'];
		${'ROOM_DATA_'.$i}[$key]['required'] = $REPORTS_ROOM_DATA_VALID_FIELDS[$key]['required'];
	}
 }


 // Tech Equipment.
 $TECH_EQUIPMENT = array (	'case_id'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"20" ),

				'owner_id'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"10" ),

				'trifield_ac'		=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_TRIFIELD_AC',
									'maxlength'	=>	"5" ),

				'trifield_dc'		=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_TRIFIELD_DC',
									'maxlength'	=>	"5" ),

				'sperry_emf_200a'	=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_SPERRY_EMF_200A',
									'maxlength'	=>	"5" ),

				'gaussmaster'		=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_GAUSSMASTER',
									'maxlength'	=>	"5" ),

				'cellsensor'		=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_CELLSENSOR',
									'maxlength'	=>	"5" ),

				'thermocouple'		=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_THERMOCOUPLE',
									'maxlength'	=>	"5" ),

				'infra_thermometer'	=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_INFRA_THERMOMETER',
									'maxlength'	=>	"5" ),

				'rel_humidity_meter'	=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_REL_HUMIDITY',
									'maxlength'	=>	"5" ),

				'barometric_pressure'	=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_BAROMETRIC_PRESSURE',
									'maxlength'	=>	"5" ),

				'ir_motion'		=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_IR_MOTION',
									'maxlength'	=>	"5" ),

				'sonic_motion'		=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_SONIC_MOTION',
									'maxlength'	=>	"5" ),

				'ion_sensor'		=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_ION_SENSOR',
									'maxlength'	=>	"5" ),

				'gas_analyzer'		=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_GAS_ANALYZER',
									'maxlength'	=>	"5" ),

				'visible_light'		=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_VISIBLE_LIGHT',
									'maxlength'	=>	"5" ),

				'spectre'		=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_SPECTRE',
									'maxlength'	=>	"5" ),

				'tech_equip_misc'	=>	array ( 'value'		=>	"|||||||||",
									'lang'		=>	'',
									'maxlength'	=>	1000 ),
 );

 // Audio Equipment.
 $AUDIO_EQUIPMENT = array (	'case_id'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"20" ),

				'owner_id'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"10" ),

				'recorder_cassette'	=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_RECORDER_ANALOG',
									'maxlength'	=>	"5" ),

				'recorder_digital'	=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_RECORDER_DIGITAL',
									'maxlength'	=>	"5" ),

				'recorder_threehead'	=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_RECORDER_THREEHEAD',
									'maxlength'	=>	"5" ),

				'mic_dynamic'		=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_MIC_DYNAMIC',
									'maxlength'	=>	"5" ),

				'mic_condensor'		=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_MIC_CONDENSOR',
									'maxlength'	=>	"5" ),

				'mic_unidirectional'	=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_MIC_UNI',
									'maxlength'	=>	"5" ),

				'mic_omnidirectional'	=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_MIC_OMNI',
									'maxlength'	=>	"5" ),

				'recorder_reeltoreel'	=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_RECORDER_REELTOREEL',
									'maxlength'	=>	"5" ),

				'audio_equip_misc'	=>	array ( 'value'		=>	"|||||||||",
									'lang'		=>	'',
									'maxlength'	=>	1000 ),
 );

 // Video Equipment.
 $VIDEO_EQUIPMENT = array (	'case_id'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"20" ),

				'owner_id'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"10" ),

				'video_dvr'		=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_VIDEO_DVR',
									'maxlength'	=>	"5" ),

				'video_hi8'		=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_VIDEO_HI8',
									'maxlength'	=>	"5" ),

				'video_digital8'	=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_VIDEO_DIGITAL8',
									'maxlength'	=>	"5" ),

				'video_minidv'		=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_VIDEO_MINIDV',
									'maxlength'	=>	"5" ),

				'video_dvd'		=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_VIDEO_DVD',
									'maxlength'	=>	"5" ),

				'video_hdd'		=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_VIDEO_HDD',
									'maxlength'	=>	"5" ),

				'video_hidef'		=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_VIDEO_HIDEF',
									'maxlength'	=>	"5" ),

				'video_equip_misc'	=>	array ( 'value'		=>	"|||||||||",
									'lang'		=>	'',
									'maxlength'	=>	1000 ),
 );

 // Photographic Equipment.
 $PHOTO_EQUIPMENT = array (	'case_id'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"20" ),

				'owner_id'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"10" ),

				'photo_35mm'		=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_PHOTO_35MM',
									'maxlength'	=>	"5" ),

				'photo_35mm_slr'	=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_PHOTO_35MM_SLR',
									'maxlength'	=>	"5" ),

				'photo_digital_under_5'	=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_PHOTO_DIGITAL_UNDER_5MP',
									'maxlength'	=>	"5" ),

				'photo_digital_above_5'	=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_PHOTO_DIGITAL_ABOVE_5MP',
									'maxlength'	=>	"5" ),

				'photo_digital_slr'	=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_PHOTO_DIGITAL_SLR',
									'maxlength'	=>	"5" ),

				'photo_polaroid'	=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_PHOTO_POLAROID',
									'maxlength'	=>	"5" ),

				'photo_equip_misc'	=>	array ( 'value'		=>	"|||||||||",
									'lang'		=>	'',
									'maxlength'	=>	1000 ),
 );

 // PSI Equipment.
 $PSI_EQUIPMENT = array (	'case_id'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"20" ),

				'owner_id'		=>	array ( 'value'		=>	"",
									'maxlength'	=>	"10" ),

				'psi_pk_board'		=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_PSI_PK_BOARD',
									'maxlength'	=>	"5" ),

				'psi_ouija'		=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_PSI_OUIJA',
									'maxlength'	=>	"5" ),

				'psi_pendulum'		=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_PSI_PENDULUM',
									'maxlength'	=>	"5" ),

				'psi_dowsing_rods'	=>	array ( 'value'		=>	"1",
									'ischecked'	=>	"0",
									'lang'		=>	'EQUIP_PSI_DOWSING_RODS',
									'maxlength'	=>	"5" ),

				'psi_equip_misc'	=>	array ( 'value'		=>	"|||||||||",
									'lang'		=>	'',
									'maxlength'	=>	1000 ),
 );


 // Units for technical measurements.
 $TECH_UNITS = array (	'emf_mg'	=>	'mG',
			'emf_kvm'	=>	'kV/m',
			'emf_vm'	=>	'V/m',
			'emf_mt'	=>	'&micro;T',
			'temp_f'	=>	'&deg;C',
			'temp_c'	=>	'&deg;F',
			'rh_percent'	=>	'mbar',
			'mbar'		=>	'inHg',
			'inHg'		=>	'hPa',
			'hPa'		=>	'%'
 );




?>
