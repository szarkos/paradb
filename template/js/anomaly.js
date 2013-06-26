/*
 AGHOST Paranormal Reporting Database (ParaDB)
 Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>

 See the file COPYING for terms of use and redistribution.
*/

 function add_tech_anomaly (room_num)  {
	var err = 0;
	if (tech_anomaly_field[room_num] > 9 || tech_anomaly_field[room_num] < 1)  {
		return 0;
	}

	var tech_anomaly = document.getElementById('tech_anomaly_data_'+tech_anomaly_field[room_num]+'_'+room_num);

	// Destination Fields.
	var tech_anomaly_time = document.getElementById('tech_anomaly_time_'+tech_anomaly_field[room_num]+'_'+room_num);
	var tech_anomaly_type = document.getElementById('tech_anomaly_type_'+tech_anomaly_field[room_num]+'_'+room_num);
	var tech_anomaly_plusminus = document.getElementById('tech_anomaly_plusminus_'+tech_anomaly_field[room_num]+'_'+room_num);
	var tech_anomaly_value = document.getElementById('tech_anomaly_value_'+tech_anomaly_field[room_num]+'_'+room_num);
	var tech_anomaly_units = document.getElementById('tech_anomaly_units_'+tech_anomaly_field[room_num]+'_'+room_num);

	// Source Fields.
	var tech_anomaly_time_hour = document.getElementById('tech_anomaly_time_hour_'+room_num);
	var tech_anomaly_time_minute = document.getElementById('tech_anomaly_time_minute_'+room_num);
	var tech_anomaly_time_second = document.getElementById('tech_anomaly_time_second_'+room_num);
	var tech_anomaly_type_value = document.getElementById('tech_anomaly_type_'+room_num);
	var tech_anomaly_plusminus_value = document.getElementById('tech_anomaly_plusminus_'+room_num);
	var tech_anomaly_value_value = document.getElementById('tech_anomaly_value_'+room_num);
	var tech_anomaly_units_value = document.getElementById('tech_anomaly_units_'+room_num);

	// Check the values, set err if there is a problem.
	if (get_select_value(tech_anomaly_type_value) == "")  {
		err = 1;
	}
	else if (get_select_value(tech_anomaly_plusminus_value) == "")  {
		err = 1;
	}
	else if (tech_anomaly_value_value.value == "")  {
		err = 1;
	}

	// Set values.
	tech_anomaly_time.value = get_select_value(tech_anomaly_time_hour)+':'+get_select_value(tech_anomaly_time_minute)+':'+get_select_value(tech_anomaly_time_second);
	tech_anomaly_type.value = get_select_text(tech_anomaly_type_value);
	tech_anomaly_plusminus.value = get_select_text(tech_anomaly_plusminus_value);
	tech_anomaly_value.value = replaceChars(tech_anomaly_value_value.value, '|', '/');
	tech_anomaly_units.value = get_select_text(tech_anomaly_units_value);

	// Return and don't set document.main.tech_anomaly.value if there's a problem.
	if ( err == 0 )  {
		// Unhide div and set main value.
		document.getElementById('tech_anomaly_div_'+tech_anomaly_field[room_num]+'_'+room_num).style.display='block';
		tech_anomaly.value = tech_anomaly_time.value+'|'+get_select_value(tech_anomaly_type_value)+'|'+get_select_value(tech_anomaly_plusminus_value)+'|'+tech_anomaly_value_value.value+'|'+get_select_value(tech_anomaly_units_value);
		tech_anomaly_field[room_num]++;
	}

	// Clear the original values.
	tech_anomaly_type_value[0].selected = true;
	tech_anomaly_plusminus_value[0].selected = true;
	tech_anomaly_value_value.value = "";
	tech_anomaly_units_value[0].selected = true;
 }
 function delete_tech_anomaly (field_num,room_num)  {
	var tech_anomaly = document.getElementById('tech_anomaly_data_'+field_num+'_'+room_num);
	var tech_anomaly_time = document.getElementById('tech_anomaly_time_'+field_num+'_'+room_num);
	var tech_anomaly_type = document.getElementById('tech_anomaly_type_'+field_num+'_'+room_num);
	var tech_anomaly_plusminus = document.getElementById('tech_anomaly_plusminus_'+field_num+'_'+room_num);
	var tech_anomaly_value = document.getElementById('tech_anomaly_value_'+field_num+'_'+room_num);
	var tech_anomaly_units = document.getElementById('tech_anomaly_units_'+field_num+'_'+room_num);

	// Delete Fields.
	tech_anomaly_time.value = "";
	tech_anomaly.value = "";
	tech_anomaly_type.value = "";
	tech_anomaly_plusminus.value = "";
	tech_anomaly_value.value = "";
	tech_anomaly_units.value = "";
	document.getElementById('tech_anomaly_div_'+field_num+'_'+room_num).style.display='none';
 }
 function add_anomaly_anomaly (room_num)  {
	var err = 0;
	if (anomaly_anomaly_field[room_num] > 9 || anomaly_anomaly_field[room_num] < 1)  {
		return 0;
	}

	// Checkbox Fields.
	var anomaly_type_ecto = document.getElementById('anomaly_type_ecto_'+room_num);
	var anomaly_type_apparition = document.getElementById('anomaly_type_apparition_'+room_num);
	var anomaly_type_orb = document.getElementById('anomaly_type_orb_'+room_num);
	var anomaly_type_audible = document.getElementById('anomaly_type_audible_'+room_num);
	var anomaly_type_visual = document.getElementById('anomaly_type_visual_'+room_num);
	var anomaly_type_physical = document.getElementById('anomaly_type_physical_'+room_num);
	var anomaly_description = document.getElementById('anomaly_description_'+room_num);
	var description = anomaly_description.value;

	// Time Field.
	var anomaly_anomaly_time_hour = document.getElementById('anomaly_anomaly_time_hour_'+room_num);
	var anomaly_anomaly_time_minute = document.getElementById('anomaly_anomaly_time_minute_'+room_num);
	var anomaly_anomaly_time_second = document.getElementById('anomaly_anomaly_time_second_'+room_num);

	// Destination Field.
	var anomaly_anomaly_data = document.getElementById('anomaly_anomaly_data_'+anomaly_anomaly_field[room_num]+'_'+room_num);

	// Set the values in the destination field.
	anomaly_anomaly_data.value = get_select_value(anomaly_anomaly_time_hour)+':'+get_select_value(anomaly_anomaly_time_minute)+':'+get_select_value(anomaly_anomaly_time_second)+'|';
	var anomaly_type = document.getElementsByName('anomaly_type_'+room_num);
	var count = 0;
	for (i=0; i<anomaly_type.length; i++)  {
		var types = '';
		if (anomaly_type[i].checked)  {
			if ( count > 0 )  {
				anomaly_anomaly_data.value += ',';
			}
			if (	anomaly_type[i].value == 'apparition' ||
				anomaly_type[i].value == 'physical' ||
				anomaly_type[i].value == 'audible' ||
				anomaly_type[i].value == 'visual')  {
				var anomaly_subtype = document.getElementsByName('anomaly_'+anomaly_type[i].value+'_type_'+room_num);
				for (j=0; j<anomaly_subtype.length; j++)  {
					if (anomaly_subtype[j].checked)  {
						if (types.length > 0)  {
							types += ';';
						}
						types += anomaly_subtype[j].value;
						anomaly_subtype[j].checked = false;
					}
				}
				if (types.length > 0)  {
					types = '(' + types + ')';
				}
			}
			anomaly_anomaly_data.value += anomaly_type[i].value + types;
			anomaly_type[i].checked = false;
			count++;
		}
	}

	// Set Description.
	if (anomaly_description.value.length > 20 )  {
		description = anomaly_description.value.substring(0,20);
	}
	anomaly_anomaly_data.value += '|' + replaceChars(description, '|', '/');
	anomaly_description.value = '';

	if (anomaly_anomaly_data.value.length == 0)  {
		err = 1;
	}
	if ( err == 0 )  {
		// Unhide div and set main value.
		document.getElementById('anomaly_anomaly_div_'+anomaly_anomaly_field[room_num]+'_'+room_num).style.display='block';
		anomaly_anomaly_field[room_num]++;
	}

	// Hide the subcategories.
	var room = document.getElementById('physical_anomaly_types_'+room_num);
	room.style.display = 'none';
	var room = document.getElementById('apparition_types_'+room_num);
	room.style.display = 'none';
	var room = document.getElementById('audible_types_'+room_num);
	room.style.display = 'none';
	var room = document.getElementById('visual_types_'+room_num);
	room.style.display = 'none';
 }
 function delete_anomaly_anomaly (field_num,room_num)  {
	// Clear the values.
	var type = document.getElementById('anomaly_anomaly_data_'+field_num+'_'+room_num);
	type.value = '';

	// Hide the div.
	var div = document.getElementById('anomaly_anomaly_div_'+field_num+'_'+room_num);
	div.style.display = 'none';
 }
 function add_evp_anomaly (room_num)  {
	var err = 0;
	if (evp_anomaly_field[room_num] > 9 || evp_anomaly_field[room_num] < 1)  {
		return 0;
	}

	// Source Fields.
	var label = document.getElementById('evp_anomaly_label_'+room_num);
	var hour = document.getElementById('evp_anomaly_hour_'+room_num);
	var min = document.getElementById('evp_anomaly_minute_'+room_num);
	var sec = document.getElementById('evp_anomaly_second_'+room_num);
	var realhour = document.getElementById('evp_anomaly_realhour_'+room_num);
	var realmin = document.getElementById('evp_anomaly_realminute_'+room_num);
	var realsec = document.getElementById('evp_anomaly_realsecond_'+room_num);
	var descr = document.getElementById('evp_anomaly_descr_'+room_num);

	// Destination Field.
	var data = document.getElementById('evp_anomaly_data_'+evp_anomaly_field[room_num]+'_'+room_num);

	if (label.value.length == 0)  {
		err = 1;
	}
	else if (hour.value.length == 0 && min.value.length == 0 && sec.value.length == 0)  {
		err = 1;
	}

	// Tape Time Fields
	if (hour.value.length == 0)  {
		hour.value = '00';
	}
	else if (hour.value.length == 1)  {
		hour.value = '0'+hour.value;
	}
	if (min.value.length == 0)  {
		min.value = '00';
	}
	else if (min.value.length == 1)  {
		min.value = '0'+min.value;
	}
	if (sec.value.length == 0)  {
		sec.value = '00';
	}
	else if (sec.value.length == 1)  {
		sec.value = '0'+sec.value;
	}
	// Tape Time Fields

	// Real Time Fields
	if (realhour.value.length == 0)  {
		realhour.value = '00';
	}
	else if (realhour.value.length == 1)  {
		realhour.value = '0'+realhour.value;
	}
	if (realmin.value.length == 0)  {
		realmin.value = '00';
	}
	else if (realmin.value.length == 1)  {
		realmin.value = '0'+realmin.value;
	}
	if (realsec.value.length == 0)  {
		realsec.value = '00';
	}
	else if (realsec.value.length == 1)  {
		realsec.value = '0'+realsec.value;
	}
	// Real Time Fields

	if ( err == 0 )  {
		data.value = replaceChars(label.value, '|', '/') + '|';
		data.value += replaceChars(hour.value, '|', '/') + ':' + replaceChars(min.value, '|', '/') + ':' + replaceChars(sec.value, '|', '/') + '|';
		data.value += replaceChars(realhour.value, '|', '/') + ':' + replaceChars(realmin.value, '|', '/') + ':' + replaceChars(realsec.value, '|', '/');
		data.value += '|' + replaceChars(descr.value, '|', '/');

		// Display the block.
		var div = document.getElementById('evp_anomaly_div_'+evp_anomaly_field[room_num]+'_'+room_num);
		div.style.display = 'block';
		evp_anomaly_field[room_num]++;

		// Clear the source values.
		label.value = hour.value = min.value = sec.value = realhour.value = realmin.value = realsec.value = descr.value = '';
	}
 }
 function delete_evp_anomaly (field_num,room_num)  {
	var data = document.getElementById('evp_anomaly_data_'+field_num+'_'+room_num);
	data.value = '';
	var div = document.getElementById('evp_anomaly_div_'+field_num+'_'+room_num);
	div.style.display = 'none';
 }
 function add_video_anomaly (room_num)  {
	var err = 0;
	if (video_anomaly_field[room_num] > 9 || video_anomaly_field[room_num] < 1)  {
		return 0;
	}

	// Source Fields.
	var label = document.getElementById('video_anomaly_label_'+room_num);
	var hour = document.getElementById('video_anomaly_hour_'+room_num);
	var min = document.getElementById('video_anomaly_minute_'+room_num);
	var sec = document.getElementById('video_anomaly_second_'+room_num);
	var realhour = document.getElementById('video_anomaly_realhour_'+room_num);
	var realmin = document.getElementById('video_anomaly_realminute_'+room_num);
	var realsec = document.getElementById('video_anomaly_realsecond_'+room_num);
	var descr = document.getElementById('video_anomaly_descr_'+room_num);

	// Destination Field.
	var data = document.getElementById('video_anomaly_data_'+video_anomaly_field[room_num]+'_'+room_num);

	if (label.value.length == 0)  {
		err = 1;
	}
	else if (hour.value.length == 0 && min.value.length == 0 && sec.value.length == 0)  {
		err = 1;
	}

	// Tape Time Fields
	if (hour.value.length == 0)  {
		hour.value = '00';
	}
	else if (hour.value.length == 1)  {
		hour.value = '0'+hour.value;
	}
	if (min.value.length == 0)  {
		min.value = '00';
	}
	else if (min.value.length == 1)  {
		min.value = '0'+min.value;
	}
	if (sec.value.length == 0)  {
		sec.value = '00';
	}
	else if (sec.value.length == 1)  {
		sec.value = '0'+sec.value;
	}
	// Tape Time Fields

	// Real Time Fields
	if (realhour.value.length == 0)  {
		realhour.value = '00';
	}
	else if (realhour.value.length == 1)  {
		realhour.value = '0'+realhour.value;
	}
	if (realmin.value.length == 0)  {
		realmin.value = '00';
	}
	else if (realmin.value.length == 1)  {
		realmin.value = '0'+realmin.value;
	}
	if (realsec.value.length == 0)  {
		realsec.value = '00';
	}
	else if (realsec.value.length == 1)  {
		realsec.value = '0'+realsec.value;
	}
	// Real Time Fields

	if ( err == 0 )  {
		data.value = replaceChars(label.value, '|', '/') + '|';
		data.value += replaceChars(hour.value, '|', '/') + ':' + replaceChars(min.value, '|', '/') + ':' + replaceChars(sec.value, '|', '/') + '|';
		data.value += replaceChars(realhour.value, '|', '/') + ':' + replaceChars(realmin.value, '|', '/') + ':' + replaceChars(realsec.value, '|', '/');
		data.value += '|' + replaceChars(descr.value, '|', '/');

		// Display the block.
		var div = document.getElementById('video_anomaly_div_'+video_anomaly_field[room_num]+'_'+room_num);
		div.style.display = 'block';
		video_anomaly_field[room_num]++;

		// Clear the source values.
		label.value = hour.value = min.value = sec.value = realhour.value = realmin.value = realsec.value = descr.value = '';
	}
 }
 function delete_video_anomaly (field_num,room_num)  {
	var data = document.getElementById('video_anomaly_data_'+field_num+'_'+room_num);
	data.value = '';
	var div = document.getElementById('video_anomaly_div_'+field_num+'_'+room_num);
	div.style.display = 'none';
 }
