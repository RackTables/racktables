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
