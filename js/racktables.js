// JavaScript functions

// Used for (un)checking an entire row of rackspace atoms
function toggleRowOfAtoms (rackId, rowId) {
	var checkboxId;
	for (var i=0; i<=2; i++) {
		checkboxId = "atom_" + rackId + "_" + rowId + "_" + i;

		// Abort if the box is disabled
		if (document.getElementById(checkboxId).disabled == true) continue;

		// Box isn't disabled, toggle it
		if (document.getElementById(checkboxId).checked == false) {
			document.getElementById(checkboxId).checked = true;
		} else {
			document.getElementById(checkboxId).checked = false;
		}
	}
}

// Used for (un)checking an entire column of rackspace atoms
function toggleColumnOfAtoms (rackId, columnId, numRows) {
	var checkboxId;
	for (var i=1; i<numRows+1; i++) {
		checkboxId = "atom_" + rackId + "_" + i + "_" + columnId;

		// Abort if the box is disabled
		if (document.getElementById(checkboxId).disabled == true) continue;

		// Box isn't disabled, toggle it
		if (document.getElementById(checkboxId).checked == false) {
			document.getElementById(checkboxId).checked = true;
		} else {
			document.getElementById(checkboxId).checked = false;
		}
	}
}

// used by 802.1Q sync form
function checkColumnOfRadios (prefix, numRows, suffix)
{
	var elemId;
	for (var i=1; i < numRows+1; i++)
	{
		elemId = prefix + i + suffix;
		// Not all radios are present on each form. Hence each time
		// we are requested to switch from left to right (or vice versa)
		// it is better to half-complete the request by setting to the
		// middle position, than to fail completely due to missing
		// target input.
		if (document.getElementById(elemId) == null)
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
