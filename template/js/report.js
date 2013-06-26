/*
 AGHOST Paranormal Reporting Database (ParaDB)
 Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>

 See the file COPYING for terms of use and redistribution.
*/


 function addTechinput(morelink, layer) {
	var link = document.getElementById(morelink);
	link.blur();
	document.getElementById(layer + ++tech_index).style.display='block';
	if (tech_index >= 5) {
		link.style.display='none';
	}
 }
 function addAudioinput(morelink, layer) {
	var link = document.getElementById(morelink);
	link.blur();
	document.getElementById(layer + ++audio_index).style.display='block';
	if (audio_index >= 5) {
		link.style.display='none';
	}
 }
 function addVideoinput(morelink, layer) {
	var link = document.getElementById(morelink);
	link.blur();
	document.getElementById(layer + ++video_index).style.display='block';
	if (video_index >= 5) {
		link.style.display='none';
	}
 }
 function addPhotoinput(morelink, layer) {
	var link = document.getElementById(morelink);
	link.blur();
	document.getElementById(layer + ++photo_index).style.display='block';
	if (photo_index >= 5) {
		link.style.display='none';
	}
 }
 function addPsiinput(morelink, layer) {
	var link = document.getElementById(morelink);
	link.blur();
	document.getElementById(layer + ++psi_index).style.display='block';
	if (psi_index >= 5) {
		link.style.display='none';
	}
 }
 function addRoominput(morelink, layer) {
	var link = document.getElementById(morelink);
	link.blur();
	document.getElementById(layer + ++room_index).style.display='block';
	if (room_index >= 31) {
		link.style.display='none';
	}
 }
 function clear_geoxray () {
	document.getElementById('list_plot_geo').style.display='none';
	document.getElementById('list_plot_xray').style.display='none';
	document.main.geomag_kp.value = '';
	document.main.geomag_kp_0.value = '';
	document.main.geomag_kp_1.value = '';
	document.main.geomag_kp_2.value = '';
	document.main.geomag_kp_3.value = '';
	document.main.geomag_kp_4.value = '';
	document.main.geomag_kp_5.value = '';
	document.main.geomag_kp_6.value = '';
	document.main.geomag_kp_7.value = '';
	document.main.geomag_ap.value = '';
	document.main.geomag_summary.value = '';
	document.main.xray_long.value = '';
	document.main.xray_short.value = '';
	document.main.xray_high.value = '';
	document.main.xray_low.value = '';
	document.main.xray_peak.value = '';
	document.main.xray_summary.value = '';
	document.getElementById('moon_phase_image').style.display='none';
	document.getElementById('moon_phase_data').style.display='none';
	document.main.moon_image.value = '';
	document.main.moon_phase.value = '';
	document.main.moon_illum.value = '';
 }
