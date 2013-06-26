/*
 AGHOST Paranormal Reporting Database (ParaDB)
 Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>

 See the file COPYING for terms of use and redistribution.
*/


 function change_caseid(linkname) {
	document.main.case_id.readOnly=false;
	document.main.case_id.style.background='#ffffff';
	var link = document.getElementById(linkname);
	link.blur();
	link.style.display='none';
 }
 function assign_ownerid(linkname,hidelayer,showlayer) {
	var hidediv = document.getElementById(hidelayer);
	var showdiv = document.getElementById(showlayer);
	hidediv.style.display='none';
	showdiv.style.display='block';

	var link = document.getElementById(linkname);
	link.blur();
	link.style.display='none';
 }
 function change_ownerid() {
	var owner_id = document.getElementById('owner_id');
	var select_ownerid = document.getElementById('assign_owner_id');
	for (var i=0; i < select_ownerid.length; i++)  {
		if (select_ownerid[i].selected == true)  {
			owner_id.value = select_ownerid[i].value;
			break;
		}
	}
 }
 function add_investigator(theSelFrom)  {
	var selLength=theSelFrom.length;
	var selectedText=new Array();
	var selectedValues=new Array();
	var selectedCount=0;
	var i;

	// Find the selected Options in reverse order
	// and delete them from the 'from' Select.
	for (i=selLength-1; i>=0; i--)  {
		if (theSelFrom.options[i].selected)  {
			selectedText[selectedCount]=theSelFrom.options[i].text;
			selectedValues[selectedCount]=theSelFrom.options[i].value;
			deleteOption(theSelFrom, i);
			selectedCount++;
		}
	}
	// Add the selected text/values in reverse order.
	// This will add the Options to the 'to' Select
	// in the same order as they were in the 'from' Select.
	for (i=selectedCount-1; i>=0; i--)  {
		show_investigator(layer_num, selectedText[i], selectedValues[i]);
		layer_num++;
	}
 }
 function show_investigator(layer, text, value)  {
	document.getElementById('investigator_div_' + layer).style.display='block';
	document.getElementById('investigator_name_' + layer).value=text;
	document.getElementById('investigator_userid_' + layer).value=value;
 }
 function del_investigator(layer, theSel)  {
	var text = document.getElementById('investigator_name_' + layer).value;
	var value = document.getElementById('investigator_userid_' + layer).value;
	if ( text != '' )  {
		addOption(theSel, text, value);
	}
	document.getElementById('investigator_name_' + layer).value='';
	document.getElementById('investigator_userid_' + layer).value='';
	document.getElementById('investigator_div_' + layer).style.display='none';
 }
 function showmore(morelink)  {
	if ( visible_rows < layer_num )  {
		visible_rows = layer_num;
	}
	visible_rows++;
	show_investigator(visible_rows, '', '');
	if ( visible_rows >= max_rows )  {
		var link = document.getElementById(morelink);
		link.blur();
		link.style.display='none';
	}
 }
