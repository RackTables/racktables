// JavaScript functions

// Used for (un)checking an entire row of rackspace atoms
function toggleRowOfAtoms (rackId, rowId) {
	var checkboxId;
	var toSet;
	toSet = null;
	for (var i=0; i<=2; i++) {
		checkboxId = "atom_" + rackId + "_" + rowId + "_" + i;

		// Abort if the box is disabled
		if (document.getElementById(checkboxId).disabled == true) continue;

		// Box isn't disabled, toggle it
		if (toSet == null) {
			toSet = !document.getElementById(checkboxId).checked;
		}
		document.getElementById(checkboxId).checked = toSet;
	}
}

// Used for (un)checking an entire column of rackspace atoms
function toggleColumnOfAtoms (rackId, columnId, numRows) {
	var checkboxId;
	var toSet;
	toSet = null;
	for (var i=1; i<numRows+1; i++) {
		checkboxId = "atom_" + rackId + "_" + i + "_" + columnId;

		// Abort if the box is disabled
		if (document.getElementById(checkboxId).disabled == true) continue;

		// Box isn't disabled, toggle it
		if (toSet == null) {
			toSet = !document.getElementById(checkboxId).checked;
		}
		document.getElementById(checkboxId).checked = toSet;
	}
}

function uncheckAll () {
    // Get all of the inputs that are in this form
    var elements = document.getElementsByTagName("input");

    for (var i = 0; i < elements.length; i++) {
	// Abort if the box is disabled
	if (elements[i].disabled == true) continue;

	// If it's a checkbox, uncheck it
        if (elements[i].type == "checkbox")
            elements[i].checked = false;
    }
}

// used by 802.1Q sync form
function checkColumnOfRadios8021Q (prefix, numRows, suffix)
{
	var elemId;
	for (var i=0; i < numRows; i++)
	{
		elemId = prefix + i + suffix;
		if (document.getElementById(elemId) == null) // no radios on this row
			continue;
		// Not all radios are present on each form. Hence each time
		// we are requested to switch from left to right (or vice versa)
		// it is better to half-complete the request by setting to the
		// middle position, than to fail completely due to missing
		// target input.
		if (document.getElementById(elemId).disabled == true)
			switch (suffix)
			{
			case '_asis':
				continue;
			case '_left':
			case '_right':
				elemId = prefix + i + '_asis';
			}
		document.getElementById(elemId).checked = true;
	}
}

// used by portinfo.js
function getQueryString(key, default_)
{
	if (default_==null) default_="";
	key = key.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
	var regex = new RegExp("[\\?&]"+key+"=([^&#]*)");
	var qs = regex.exec(window.location.href);
	if(qs == null)
		return default_;
	else
		return qs[1];
}

$(document).ready (function (e) {
	$('a.need-confirmation').click (function (e) {
		return confirm ("Are you sure?");
	})
});