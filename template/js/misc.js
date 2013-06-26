/*
 AGHOST Paranormal Reporting Database (ParaDB)
 Copyright (c) 2007 Stephen A. Zarkos <Obsid@Sentry.net>

 See the file COPYING for terms of use and redistribution.
*/


 function showhide (layer)  {
	if ( document.getElementById(layer).style.display == 'block' )  {
		document.getElementById(layer).style.display = 'none';
	}
	else  {
		document.getElementById(layer).style.display = 'block';
	}
 }
 function taller (layer)  {
	max_height = 25;
	var height = document.getElementById(layer).rows;
	if ( height < max_height )  {
		document.getElementById(layer).rows = ++height;
	}
 }
 function shorter (layer)  {
	min_height = 4;
	var height = document.getElementById(layer).rows;
	if ( height > min_height )  {
		document.getElementById(layer).rows = --height;
	}
 }
 function replaceChars(data, oldchar, newchar)  {
	var tmp = data;
	while (tmp.indexOf(oldchar)>-1)  {
		pos = tmp.indexOf(oldchar);
		tmp = tmp.substring(0, pos) + newchar + tmp.substring((pos + newchar.length), tmp.length);
	}
	return tmp;
 }
 function deleteOption(theSel, theIndex)  { 
	var selLength = theSel.length;
	if (selLength>0)  {
		theSel.options[theIndex] = null;
	}
 }
 function addOption(theSel, theText, theValue)  {
	var newOpt = new Option(theText, theValue);
	var selLength = theSel.length;
	theSel.options[selLength] = newOpt;
 }
 // Return the value of the first selected element of a select field.
 function get_select_value (formElement)  {
	for (var i=0; i < formElement.length; i++)  {
		if (formElement[i].selected == true)  {
			return formElement[i].value;
		}
	}
 }
 // Return the text of the first selected element of a select field.
 function get_select_text (formElement)  {
	for (var i=0; i < formElement.length; i++)  {
		if (formElement[i].selected == true)  {
			return formElement[i].text;
		}
	}
 }
 // Select a particular option given the name of the selectbox and the value of the option.
 function selectoption(select_id,value)  {
	var sel = document.getElementById(select_id);
	for (var i=sel.length-1; i>=0; i--)  {
		if (sel.options[i].value == value)  {
			sel.options[i].selected = true;
			break;
		}
	}
 }
