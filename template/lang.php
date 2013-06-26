<?php

 DEFINE("WELCOME", "Welcome");
 DEFINE("POWERED", REPORTS_DB_NAME_LINK . " v" . REPORTS_DB_VERSION);
 DEFINE("LOGIN_USERNAME", "USERNAME");
 DEFINE("LOGIN_PASSWORD", "PASSWORD");
 DEFINE("LOGIN_REMEMBERME", "Remember Me");

 DEFINE("HOME_MY_REPORTS", "My Reports");
 DEFINE("HOME_UNFINISHED_REPORTS", "Reports To Do");
 DEFINE("HOME_RECENTLY_EDITED_REPORTS", "Recently Published");

 DEFINE("NAVBAR_HOME", "Home");
 DEFINE("NAVBAR_OPEN_CASE", "Open Case");
 DEFINE("NAVBAR_CLOSE_EXPIRED", "Close Expired Cases");
 DEFINE("NAVBAR_WRITE_REPORT", "Write Report");
 DEFINE("NAVBAR_USER_MANAGEMENT", "User Management");
 DEFINE("NAVBAR_STATISTICS", "Statistics");
 DEFINE("NAVBAR_LOGOUT", "Logout");
 DEFINE("NAVBAR_MY_CASES", "My Cases");
 DEFINE("NAVBAR_MY_REPORTS", "My Reports");
 DEFINE("NAVBAR_CASE_SELECT", "Case Select");

 DEFINE("SEARCH_SEARCH", "Search");
 DEFINE("SEARCH_NO_RESULTS", "No Results");
 DEFINE("SEARCH_RESULTS", "Results");
 DEFINE("SEARCH_CASE", "Case");
 DEFINE("SEARCH_REPORT", "Report");

 DEFINE("REPORT_FORM_ERROR", "I'm sorry, we were unable to save your report.<br />Please make certain that you have filled in all the required fields and then try to save the report again.");
 DEFINE("CASE_FORM_ERROR", "Error: Case not saved.<br />Please make certain that you have filled in all the required fields.");
 DEFINE("CASE_OVERDUE_INVESTIGATORS", "<strong>WARNING:</strong> One or more investigators has exceeded the allowed case/reports threshold.");
 DEFINE("CASE_ERROR_CASEID_FORMAT", "Error: Case not saved.<br />Reason: The <i>Investigation Type</i> is not in the proper syntax.");
 DEFINE("CASE_ERROR_CASE_EXISTS", "Sorry, you do not have permission to edit this case.<br />This error may occur if the case was reassigned, or if the Case ID conflicts with an existing case.");

 DEFINE("REPORT_DELETE_SUCCESS", "Report deleted successfully.");
 DEFINE("REPORT_DELETE_UNSUCCESS", "Error: Unable to delete report.");
 DEFINE("CASE_DELETE_SUCCESS", "Case deleted successfully.");
 DEFINE("CASE_DELETE_UNSUCCESS", "Error: Unable to delete case.");

 DEFINE("REPORT_PUBLISH_SUCCESS", "Report published.");
 DEFINE("REPORT_PUBLISH_UNSUCCESS", "Error: Unable to publish report.");
 DEFINE("REPORT_UNPUBLISH_SUCCESS", "Report unpublished.");
 DEFINE("REPORT_UNPUBLISH_UNSUCCESS", "Error: Unable to unpublish report.");

 DEFINE("CASE_CLOSE_SUCCESS", "Case successfully closed.");
 DEFINE("CASE_CLOSE_UNSUCCESS", "Error: unable to close case.");
 DEFINE("CASE_OPEN_SUCCESS", "Case successfully re-opened.");
 DEFINE("CASE_OPEN_UNSUCCESS", "Error: unable to re-open case.");

 DEFINE("ERR_CASEID", "Error: A case ID is required." );
 DEFINE("ERR_PERM_CASEVIEW", "Sorry, you do not have permission to view this case." );
 DEFINE("ERR_PERM_CASEEDIT", "Sorry, you do not have permission to edit this case." );
 DEFINE("ERR_PERM_CASEDELETE", "Sorry, you do not have permission to delete this case." );
 DEFINE("ERR_PERM_CASECLOSED", "This case has been closed. You may only edit or delete open cases." );
 DEFINE("ERR_DATE", "Error: Invalid date format." );
 DEFINE("ERR_XRAY_FILE_RETR", "Unable to retrieve xray data from the server.");
 DEFINE("ERR_GEOM_FILE_RETR", "Unable to retrieve geomagnetic data from the server.");
 DEFINE("ERR_ORPHANED_REPORTS", "There are orphaned reports.");
 DEFINE("ERR_PERMISSION_DENIED", "Error: Permission Denied.");
 DEFINE("ERR_REPORT_CASE_CLOSED", "The case associated with this report has been closed or has expired.");
 DEFINE("ERR_REPORT_CASE_EXPIRED", "The case associated with this report has been closed or has expired.");
 DEFINE("ERR_REPORT_NOT_INVESTIGATOR", "You are not listed as an investigator on this case.");
 DEFINE("ERR_REPORT_NOEXIST", "Report does not exist.");

 DEFINE("ERR_USER_UPDATE", "Error updating user.");
 DEFINE("ERR_USER_MGMT_DENIED", "Error: Unable to update user information, permission denied.");
 DEFINE("USER_ADD_FORM_INCOMPLETE", "Error: Unable to create user. Please make sure that you have filled in all the required fields.");
 DEFINE("ERR_USER_EXISTS", "Error: Username already exists.");
 DEFINE("ERR_USER_NOEXIST", "Error: Unable to update user, no such user.");
 DEFINE("ERR_USER_CREATE", "Error: unable to create user.");

 DEFINE("TASK_VIEW_CASE", "View Case" );
 DEFINE("TASK_EDIT_CASE", "Edit Case" );
 DEFINE("TASK_SAVE_CASE", "Save Case" );
 DEFINE("TASK_DELETE_CASE", "Delete Case" );
 DEFINE("TASK_OPEN_CASE", "Open Case" );
 DEFINE("TASK_CLOSE_CASE", "Close Case" );
 DEFINE("TASK_VIEW_CASE_PRINT", "Printable View" );
 DEFINE("TASK_VIEW_REPORT_PRINT", "Printable View" );
 DEFINE("TASK_VIEW_REPORT", "View" );
 DEFINE("TASK_WRITE_REPORT", "Write Report" );
 DEFINE("TASK_EDIT_REPORT", "Edit" );
 DEFINE("TASK_SAVE_REPORT", "Save Report" );
 DEFINE("TASK_DELETE_REPORT", "Delete" );
 DEFINE("TASK_PUBLISH_REPORT", "Publish" );
 DEFINE("TASK_UNPUBLISH_REPORT", "Unpublish" );
 DEFINE("TASK_VIEW_REPORT_LONG", "View Report" );
 DEFINE("TASK_WRITE_REPORT_LONG", "Write Report" );
 DEFINE("TASK_EDIT_REPORT_LONG", "Edit Report" );
 DEFINE("TASK_SAVE_REPORT_LONG", "Save Report" );
 DEFINE("TASK_DELETE_REPORT_LONG", "Delete Report" );
 DEFINE("TASK_PUBLISH_REPORT_LONG", "Publish Report" );
 DEFINE("TASK_UNPUBLISH_REPORT_LONG", "Unpublish Report" );
 DEFINE("TASK_VIEW_REPORT_PRINT_LONG", "Printable View" );
 DEFINE("TASK_SAVE_USERS", "Save Users" );
 DEFINE("TASK_ADD_USER", "Add User" );
 DEFINE("TASK_ADD_USER_SAVE", "Save User" );
 DEFINE("SAVE_CASE_CONFIRM", "Save Anyway?");

 DEFINE("CASE_ID", "Case ID:" );
 DEFINE("CASE_OWNER", "Owner ID:" );
 DEFINE("CASE_INVESTIGATION_TITLE", "Case Title:" );
 DEFINE("CASE_OWNER_ID", "Owner ID:" );
 DEFINE("CASE_STATUS", "Status:" );
 DEFINE("CASE_DATE", "Investigation Date:" );
 DEFINE("CASE_TIME", "Investigation Time:" );
 DEFINE("CASE_TYPE", "Investigation Type:" );
 DEFINE("CASE_CONTACT_NAME", "Contact Name:" );
 DEFINE("CASE_CONTACT_PRIMARY_PHONE", "Primary Phone:" );
 DEFINE("CASE_CONTACT_OFFICE_PHONE", "Office Phone:" );
 DEFINE("CASE_CONTACT_MOBILE_PHONE", "Mobile Phone:" );
 DEFINE("CASE_CONTACT_EMAIL", "Email Address:" );
 DEFINE("CASE_EXPIRATION", "Expiration Date:" );
 DEFINE("CASE_ADDRESS", "Address:" );
 DEFINE("CASE_CITY", "City:" );
 DEFINE("CASE_STATE", "State:" );
 DEFINE("CASE_ZIP", "Postal Code:" );
 DEFINE("CASE_COUNTRY", "Country:" );
 DEFINE("CASE_LOCATION_TYPE", "Location Type:" );
 DEFINE("CASE_DESCRIPTION", "Description:" );
 DEFINE("CASE_NOTES", "Notes:" );
 DEFINE("CASE_INVESTIGATORS", "Investigator" );
 DEFINE("CASE_RECAP_DATE", "Case Recap Date:" );
 DEFINE("CASE_RECAP_TIME", "Case Recap Time:" );
 DEFINE("CASE_RECAP_LOCATION", "Recap Location:" );
 DEFINE("CASE_INVESTIGATOR_USERS", "Users" );
 DEFINE("CASE_INVESTIGATOR_ROLE", "Role" );
 DEFINE("CASE_INVESTIGATOR_DATA_SUBMITTED", "Data Submitted" );
 DEFINE("CASE_INVESTIGATOR_REPORT_SUBMITTED", "Report Submitted" );
 DEFINE("CASE_INVESTIGATOR_EMAIL_REMINDER", "Email Reminder" );

 DEFINE("CASE_LABEL_CASE_DETAILS", "Case Information" );
 DEFINE("CASE_LABEL_CONTACT_INFO", "Primary Contact Information" );
 DEFINE("CASE_LABEL_ADDRESS", "Location Information" );
 DEFINE("CASE_LABEL_DESCRIPTION", "Description" );
 DEFINE("REPORT_LABEL_REPORT_DETAILS", "Report Information");

 DEFINE("REPORT_CASE_ID", "Case ID:" );
 DEFINE("REPORT_OWNER_ID", "Owner ID:" );
 DEFINE("REPORT_INVESTIGATOR_NAME", "Investigator:" );
 DEFINE("REPORT_TITLE", "Location:" );
 DEFINE("REPORT_DATE", "Date:" );
 DEFINE("REPORT_START_TIME", "Start Time:" );
 DEFINE("REPORT_END_TIME", "End Time:" );
 DEFINE("REPORT_DESCRIPTION", "Description:" );
 DEFINE("REPORT_REPORT_STATE", "Report State:" );
 DEFINE("REPORT_GEOMAGNETIC", "Geomagnetic" );
 DEFINE("REPORT_XRAY", "X-Ray" );
 DEFINE("REPORT_MOON", "Moon Phase" );
 DEFINE("REPORT_OUTSIDE_IMPRESSION", "Outside Impression" );
 DEFINE("REPORT_WALKIN_IMPRESSION", "Walk-in Impression" );
 DEFINE("REPORT_CLOSING_IMPRESSION", "Closing Impression" );
 DEFINE("REPORT_EQUIP_TECH", "Technical" );
 DEFINE("REPORT_EQUIP_AUDIO", "Audio" );
 DEFINE("REPORT_EQUIP_VIDEO", "Video" );
 DEFINE("REPORT_EQUIP_PHOTO", "Photographic" );
 DEFINE("REPORT_EQUIP_PSI", "PSI" );
 DEFINE("REPORT_ROOM_ID", "Room ID:" );
 DEFINE("REPORT_AC_EMF_ELECTRIC", "AC EMF (Electric):" );
 DEFINE("REPORT_AC_EMF_MAGNETIC", "AC EMF (Magnetic):" );
 DEFINE("REPORT_DC_EMF_ELECTRIC", "DC EMF (Electric):" );
 DEFINE("REPORT_DC_EMF_MAGNETIC", "DC EMF (Magnetic):" );
 DEFINE("REPORT_DC_EMF_SUM", "DC EMF (Sum):" );
 DEFINE("REPORT_TEMP", "Temperature:" );
 DEFINE("REPORT_RELATIVE_HUMIDITY", "Relative Humidity:" );
 DEFINE("REPORT_BAROMETRIC", "Barometric:" );
 DEFINE("REPORT_NOTES", "Notes:" );
 DEFINE("REPORT_LAST_EDITED", "Last Edited:" );
 DEFINE("REPORT_PUBLISHED_BY", "Author:");
 DEFINE("REPORT_CASE_STATE", "Case State:" );
 DEFINE("REPORT_CASE_OWNER", "Case Owner:" );
 DEFINE("REPORT_CASE_DATE", "Investigation Date:" );
 DEFINE("REPORT_CASE_DATE_TIME", "Investigation Date/Time:" );
 DEFINE("REPORT_CASE_RECAP_DATE_TIME", "Recap Date/Time:" );
 DEFINE("REPORT_ANOMALIES", "Anomalies" );
 DEFINE("REPORT_EVP", "EVP" );
 DEFINE("REPORT_VIDEO", "Video" );
 DEFINE("REPORT_TAPE_TIME", "Tape Time Code:" );
 DEFINE("REPORT_REAL_TIME", "Real Time:" );

 DEFINE("REPORT_VIEW_EQUIP_TECH", "Technical Equipment" );
 DEFINE("REPORT_VIEW_EQUIP_AUDIO", "Audio Equipment" );
 DEFINE("REPORT_VIEW_EQUIP_VIDEO", "Video Equipment" );
 DEFINE("REPORT_VIEW_EQUIP_PHOTO", "Photo Equipment" );
 DEFINE("REPORT_VIEW_EQUIP_PSI", "PSI Equipment" );
 DEFINE("REPORT_VIEW_NOT_DEFINED", "&lt; NONE DEFINED &gt;" );

 DEFINE("CASE_HEADER_USER_MANAGEMENT", "Team Management");
 DEFINE("CASE_HEADER_NOTES", "Case Notes");
 DEFINE("REPORT_HEADER_EQUIP", "Equipment and Space Weather Information");
 DEFINE("REPORT_HEADER_IMPRESSIONS", "Impressions");
 DEFINE("REPORT_HEADER_ROOM", "Data For Room #");
 DEFINE("REPORT_VIEW_HEADER_EQUIP", "Equipment Information");

 DEFINE("TEXTAREA_TALLER", "Taller" );
 DEFINE("TEXTAREA_SHORTER", "Shorter" );

 DEFINE("EQUIP_TRIFIELD_AC", "Trifield AC EMF" );
 DEFINE("EQUIP_TRIFIELD_DC", "Trifield Natural" );
 DEFINE("EQUIP_SPERRY_EMF_200A", "Sperry EMF-200A" );
 DEFINE("EQUIP_GAUSSMASTER", "Gaussmaster" );
 DEFINE("EQUIP_CELLSENSOR", "Cellsensor" );
 DEFINE("EQUIP_THERMOCOUPLE", "Thermocouple" );
 DEFINE("EQUIP_INFRA_THERMOMETER", "Infrared Thermometer" );
 DEFINE("EQUIP_REL_HUMIDITY", "Relative Humidity" );
 DEFINE("EQUIP_BAROMETRIC_PRESSURE", "Barometric Pressure" );
 DEFINE("EQUIP_IR_MOTION", "IR Motion Sensor" );
 DEFINE("EQUIP_SONIC_MOTION", "Sonic Motion Sensor" );
 DEFINE("EQUIP_ION_SENSOR", "Ion Sensor" );
 DEFINE("EQUIP_GAS_ANALYZER", "Gas Analyzer" );
 DEFINE("EQUIP_VISIBLE_LIGHT", "Visible Light" );
 DEFINE("EQUIP_SPECTRE", "S.P.E.C.T.R.E." );

 DEFINE("EQUIP_RECORDER_ANALOG", "Analog Recorder" );
 DEFINE("EQUIP_RECORDER_DIGITAL", "Digital Recorder" );
 DEFINE("EQUIP_RECORDER_THREEHEAD", "Three-head Recorder" );
 DEFINE("EQUIP_MIC_DYNAMIC", "Dynamic Microphone" );
 DEFINE("EQUIP_MIC_CONDENSOR", "Condensor Microphone" );
 DEFINE("EQUIP_MIC_UNI", "Unidirectional Microphone" );
 DEFINE("EQUIP_MIC_OMNI", "Omnidirectional Microphone" );
 DEFINE("EQUIP_RECORDER_REELTOREEL", "Reel-to-Reel" );

 DEFINE("EQUIP_VIDEO_DVR", "DVR System" );
 DEFINE("EQUIP_VIDEO_HI8", "Hi8" );
 DEFINE("EQUIP_VIDEO_DIGITAL8", "Digital8" );
 DEFINE("EQUIP_VIDEO_MINIDV", "MiniDV" );
 DEFINE("EQUIP_VIDEO_DVD", "DVD Recording" );
 DEFINE("EQUIP_VIDEO_HIDEF", "Hi-Def" );
 DEFINE("EQUIP_VIDEO_HDD", "HDD Handycam" );

 DEFINE("EQUIP_PHOTO_35MM", "35MM" );
 DEFINE("EQUIP_PHOTO_35MM_SLR", "35MM SLR" );
 DEFINE("EQUIP_PHOTO_DIGITAL_UNDER_5MP", "Digital >5MP" );
 DEFINE("EQUIP_PHOTO_DIGITAL_ABOVE_5MP", "Digital <5MP" );
 DEFINE("EQUIP_PHOTO_DIGITAL_SLR", "Digital SLR" );
 DEFINE("EQUIP_PHOTO_POLAROID", "Polaroid" );

 DEFINE("EQUIP_PSI_PK_BOARD", "PK Board" );
 DEFINE("EQUIP_PSI_OUIJA", "Ouija Board" );
 DEFINE("EQUIP_PSI_PENDULUM", "Pendulum" );
 DEFINE("EQUIP_PSI_DOWSING_RODS", "Dowsing Rods" );

 DEFINE("GEOMAG_KP", "Kp Indices");
 DEFINE("GEOMAG_AP", "Ap");
 DEFINE("GEOMAG_SUMMARY", "Summary");
 DEFINE("GEOMAG_LIST", "List");
 DEFINE("GEOMAG_PLOT", "Plot");

 DEFINE("XRAY_LONG", "XL (1.0-8.0&Aring;)");
 DEFINE("XRAY_SHORT", "XS (0.5-3.0&Aring;)");
 DEFINE("XRAY_HIGH", "High");
 DEFINE("XRAY_LOW", "Low");
 DEFINE("XRAY_PEAK", "Peak");
 DEFINE("XRAY_SUMMARY", "Summary");
 DEFINE("XRAY_LIST", "List");
 DEFINE("XRAY_PLOT", "Plot");

 DEFINE("MOON_PHASE", "Moon Phase");

 DEFINE("HELP_REPORT_STATE", "A report is either <i>published</i> or <i>unpublished</i>. Unpublished reports are private and can only be read by you. Published reports become public so that others can read them, but can still be edited until the case is closed.");
 DEFINE("HELP_OUTSIDE_IMPRESSION", "Please provide any notes or details about the outside of the location.");
 DEFINE("HELP_WALKIN_IMPRESSION", "Please provide any notes or details you notice about the location as you walk in. This can include pyschic impressions, as well as technical or structural details about the location.");
 DEFINE("HELP_CLOSING_IMPRESSION", "This area can be used to express your own thoughts and ideas about the investigation or walkthrough.");
 DEFINE("HELP_ROOM_ID", "<strong>Required:</strong> Please provide a short name that describes the room.");
 DEFINE("HELP_REPORT_ANOMALY_MIST", "Mist or ecto can be seen with the naked eye, but often appears in photographs.");
 DEFINE("HELP_REPORT_ANOMALY_APPARITION", "Apparitions are the physical manifestation of a ghost or spirit.");
 DEFINE("HELP_REPORT_ANOMALY_ORB", "Round spheres of light often seen in photographs. Dust, rain and other airborn debris is often mistaken for orb phenomenon.");
 DEFINE("HELP_REPORT_ANOMALY_AUDIBLE", "An audible sound heard without the aid of audio equipment - such as a knock, scratch or even a voice or whisper. These are not EVPs.");
 DEFINE("HELP_REPORT_ANOMALY_VISUAL", "Any anomaly that is seen by the naked eye, but does not fit into any other category (such as apparition).");
 DEFINE("HELP_REPORT_ANOMALY_PHYSICAL", "This anomaly can include a touch, push/pull/grab, scrape, burn or any other physical anomalous event experienced by the investigator.");
 DEFINE("HELP_REPORT_ANOMALY_EVP_LABEL", "<strong>Required:</strong> Note here either the label on the cassette tape, or the filename of the digital recording.");
 DEFINE("HELP_REPORT_ANOMALY_EVP_TIME", "<strong>Required:</strong> Note the time <i>on the tape</i> at which the EVP occurred.");
 DEFINE("HELP_REPORT_ANOMALY_EVP_REALTIME", "<strong>Optional:</strong> Note the actual time of the day at which the EVP occurred.");
 DEFINE("HELP_REPORT_ANOMALY_EVP_DESCR", "<strong>Optional:</strong> Describe the EVP and what was happening when it was captured.");
 DEFINE("HELP_REPORT_ANOMALY_VIDEO_LABEL", "<strong>Required:</strong> Note here either the label on the tape (Hi8, MiniDV, etc.), or the filename of the digital video recording.");
 DEFINE("HELP_REPORT_ANOMALY_VIDEO_TIME", "<strong>Required:</strong> Note the time <i>on the tape</i> at which the video anomaly occurred.");
 DEFINE("HELP_REPORT_ANOMALY_VIDEO_REALTIME", "<strong>Optional:</strong> Note the actual time of day at which the video anomaly occurred.");
 DEFINE("HELP_REPORT_ANOMALY_VIDEO_DESCR", "<strong>Optional:</strong> Describe the video anomaly and what was happening when it was captured.");
 DEFINE("HELP_REPORT_START_TIME", "<strong>Start Time</strong><br />The is the time the investigation <i>started</i>. The time is expressed in 24-hour format. <i>Example:</i> 12:30AM is 00:30, 11:00PM is 23:00.");
 DEFINE("HELP_REPORT_END_TIME", "<strong>End Time</strong><br />The is the time the investigation <i>ended</i>. The time is expressed in 24-hour format. <i>Example:</i> 12:30AM is 00:30, 11:00PM is 23:00.");
 DEFINE("HELP_REPORT_DETAILS", "<strong>" . REPORT_LABEL_REPORT_DETAILS . "</strong><br />General information about the investigation.");

 DEFINE("HELP_USER_STATS", "<strong>User Statistics, A/B/C</strong><br/><strong>A:</strong> Number of reports (published and unpublished).<br/><strong>B:</strong> Data submitted for each case.<br/><strong>C:</strong> Number of cases investigator has been on.");

 DEFINE("HELP_CASE_DETAILS", "<strong>" . CASE_LABEL_CASE_DETAILS . "</strong><br />General information about the investigation, walkthrough or expedition. This information cannot be edited, and is displayed here for informational purposes only.");
 DEFINE("HELP_CASE_EXPIRATION", "When a case expires it is automatically <i>closed</i>. When a case is closed any associated reports are set to the <i>published</i> state and may no longer be edited.");
 DEFINE("HELP_CASE_RECAP_DATE", "<strong>Recap Date</strong><br />This is the date of the recap meeting after the investigation. During a recap meeting, investigators typically meet to discuss their findings and share data.");
 DEFINE("HELP_CASE_RECAP_TIME", "<strong>Recap Time</strong><br />This is the time of the recap meeting. The time is expressed in 24-hour format. <i>Example:</i> 12:30AM is 00:30, 11:00PM is 23:00.");
 DEFINE("HELP_CASE_INVESTIGATION_TIME", "<strong>Investigation Time</strong><br />This is the time of the investigation. The time is expressed in 24-hour format. <i>Example:</i> 12:30AM is 00:30, 11:00PM is 23:00.");

// DEFINE("HELP_CASE_TYPE", "<strong>Investigation:</strong> An investigation is a rigorous and detailed search of a location for any paranormal activity. The investigation team will typically consist of at least one team lead, a technician, an observer and a psychic.
// <br /><br /><strong>Walkthrough:</strong> An walkthrough is typically a less-rigorous type of investigation. The first visit to a location will often be in the form of a walkthrough. The investigators will often take baseline measurements and conduct the interview during the walkthrough.
// <br /><br /><strong>Expedition:</strong> An expedition typically takes place in an uncontrolled or open environment, such as a cemetery. These events allow ghost hunters to practice using their tools or natural skills in a low-stress environment.");
 DEFINE("HELP_CASE_CONTACT_INFO", "Please provide contact information for the team leader's primary contact for the case, such as the home or busines owner. If there are alternate contacts available list them in the <i>notes</i> section.");
 DEFINE("HELP_CASE_LOCATION_DETAILS", "This should be the address of the location itself. Other investigators will only be able to see the city and state, but will not be able to see the actual address.");
 DEFINE("HELP_CASE_DESCRIPTION", "Provide any and all other information about the case here.");
 DEFINE("HELP_CASE_NOTES", "Provide any and all other information about the case here.");
 DEFINE("HELP_INVESTIGATOR_USERS", "<strong>Users</strong><br />Select users to assign to an investigation.");
 DEFINE("HELP_INVESTIGATOR_NAME", "<strong>Investigator</strong><br />The full name of the investigator.");
 DEFINE("HELP_INVESTIGATOR_ROLE", "<strong>Role</strong><br />The role the investigator will take on the investigation or walkthrough.");
 DEFINE("HELP_INVESTIGATOR_DATA_SUBMITTED", "<strong>Data Submitted</strong><br />The investigator has submitted his/her data to the team lead or case manager. Case managers must manually select this checkbox when the investigator turns in their data.");
 DEFINE("HELP_INVESTIGATOR_REPORT_SUBMITTED", "<strong>Report Submitted</strong><br />The investigator has submitted a report. However, the report may not yet be completed or published.");
 DEFINE("HELP_INVESTIGATOR_SEND_REMINDER", "<strong>Send Email Reminder</strong><br />Select one or more checkboxes below, and then click &quot;Save Case&quot; to send a message to the user reminding them to complete their report.");

 DEFINE("HELP_HOME_MY_REPORTS", "<strong>" . HOME_MY_REPORTS . "</strong><br />This column lists the reports you are currently working on. These reports will be available for editing until the case is <i>closed</i>.");
 DEFINE("HELP_HOME_UNFINISHED_REPORTS", "<strong>" . HOME_UNFINISHED_REPORTS . "</strong><br /> This column lists reports that have not yet been started. You may start these reports as long as the case remains open.");
 DEFINE("HELP_HOME_RECENTLY_EDITED_REPORTS", "<strong>" . HOME_RECENTLY_EDITED_REPORTS . "</strong><br /> This column lists the most recently edited published reports.");

 DEFINE("HELP_NAVBAR_CASE_SELECT", "<strong>" . NAVBAR_CASE_SELECT . "</strong><br />Use this dropdown to view any case currently in the database.");
 DEFINE("HELP_NAVBAR_MY_REPORTS", "<strong>" . NAVBAR_MY_REPORTS . "</strong><br />Use this dropdown to quickly access your reports.");
 DEFINE("HELP_NAVBAR_MY_CASES", "<strong>" . NAVBAR_MY_CASES . "</strong><br />Case managers can use this dropdown to view any case they currently <strong>own</strong>.");

 DEFINE("STATS_USERLIST", "User List");
 DEFINE("STATS_GENERAL", "DB Statistics");
 DEFINE("STATS_DATA_SUBMITTED", "Data Submitted:" );
 DEFINE("STATS_REPORT_SUBMITTED", "Report Submitted:" );
 DEFINE("STATS_TOTAL_USERS", "Total Users:");
 DEFINE("STATS_TOTAL_USERS_LEADS", "Team Leads:");
 DEFINE("STATS_TOTAL_CASES", "Total Cases:");
 DEFINE("STATS_TOTAL_OPEN_CASES", "Open Cases:");
 DEFINE("STATS_TOTAL_REPORTS", "Total Reports:");
 DEFINE("STATS_TOTAL_PUBLISHED_REPORTS", "Published Reports:");
 DEFINE("STATS_SHOW_BLOCKED", "Show Disabled Accounts");

 DEFINE("LINK_SHOW_MORE", "Add More Rows");

 DEFINE("CASE_REASSIGN_SUBJECT", "New Case Assignment");
 DEFINE("CASE_REASSIGN_BODY", "\nThis email has been sent by the " . REPORTS_DB_NAME . " application.  Please do not respond to this email.  If you have any questions please email " . $ADMIN_EMAIL . ".\n\nYou have been assigned a new case by user %olduser%! You are now the proud owner of Case ID #%case_id%. You may now log in and make changes to the case, add investigators, etc. Please use the link below to access your case:\n\n" . $REPORTSDB . "?task=edit_case&case_id=%case_id%\n\n");
 DEFINE("CASE_REASSIGN_SUCCESS", "Case ID %case_id% successfully reassigned to %newuser%.");

 DEFINE("CASE_SEND_REMINDER_SUBJECT", "Friendly Reminder");
 DEFINE("CASE_SEND_REMINDER_BODY", "\nThis email has been sent by the " . REPORTS_DB_NAME . " application.  Please do not respond to this email.  If you have any questions please email " . $ADMIN_EMAIL . ".\n\nThis message has been sent to remind you that you still need to complete a report for Case ID %case_id%.\n\nPlease click the following link to start your report:\n" . $REPORTSDB . "?task=edit_report&case_id=%case_id%&owner_id=%owner_id%\n\n");
 DEFINE("CASE_SEND_REMINDER_SUCCESS", "Email reminders sent.");
 DEFINE("CASE_SEND_REMINDER_UNSUCCESS", "Error: An error occurred while sending one or more reminders. Please contact the administrator: <a href=\"mailto:" . $ADMIN_EMAIL . "\">" . $ADMIN_EMAIL . "</a>");



?>
