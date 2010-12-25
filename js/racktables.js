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

// called from interface.php:renderCellFilterPortlet() if SHRINK_TAG_TREE_ON_CLICK is set
function init_cb_click() {
	$(document).ready(function () {
		$('.tag-cb , input[name=andor]').click(function (event) {
			var or_check = $('input[name=andor][value=or]')[0];
			if
			(
				event.target.type == 'checkbox' &&
				or_check.checked
				|| event.target.type == 'radio' &&
				$('.tag-cb:checked').size() == 0
			)
				return;
			
			$(event.target).closest('form')[0].submit();
		});
	});
}

// uses global tagShortList array
function compactTreeMode() {
	// reconfigure toggle link
	var link = $('a.toggleTreeMode')[0];
	if ($(link).filter(':visible')) {
		$(link).after('<p>');
	}
	link.onclick = function () {fullTreeMode(); return false;};
	$(link).html('show full tree').show();
	
	$('.tagtree').addClass('compact'); // disable hierachical padding
	
	var separator = false; // next visible row is separator
	var bPrevSeparator = true; // prev visible row was separator
	$('input.tag-cb').each(function (i, item) {
		var tr = $(item).closest('tr');
		
		if ($(item).hasClass('root'))
			separator = true;
		
		if (! item.checked && ! tagShortList[item.value]) {
			tr.hide();
			return;
		}
		
		if (separator && ! bPrevSeparator) { // do not draw two separators together or very first separator
			tr.addClass('separator');
			bPrevSeparator = true;
		}
		else {
			tr.removeClass('separator');
			bPrevSeparator = false;
		}
		separator = false;
	});
}

// uses global tagShortList array
function fullTreeMode() {
	// reconfigure toggle link
	var link = $('a.toggleTreeMode')[0];
	link.onclick = function () {compactTreeMode(); return false;};
	$(link).html('show compact tree').show();
	
	$('.tagtree').removeClass('compact'); // restore hierachical padding
	
	var bPrevSeparator = true; // prev visible row was separator
	$('input.tag-cb').each(function (i, item) { // // do not draw two separators together or very first separator
		var tr = $(item).closest('tr');
		tr.removeClass('separator');

		var separator = $(item).hasClass('root');
		if (separator && ! bPrevSeparator)
			tr.addClass('separator');
		bPrevSeparator = separator;

		tr.show();
	});
}

function blockToggleRowOfAtoms (rackId, rowId) {
	var checkboxId;
	var toSet;
	toSet = null;
	for (var i=0; i<=2; i++) {
		checkboxId = "atom_" + rackId + "_" + rowId + "_" + i;
		// Abort if the box is disabled
		if (document.getElementById(checkboxId).disabled == true) continue;
		if (toSet == null) {
			toSet = !document.getElementById(checkboxId).checked;
		}
		document.getElementById(checkboxId).checked = toSet;
	}
}

function blockToggleColumnOfAtoms (rackId, columnId, numRows) {
	var checkboxId;
	var toSet;
	toSet = null;
	for (var i=1; i<numRows+1; i++) {
		checkboxId = "atom_" + rackId + "_" + i + "_" + columnId;
		// Abort if the box is disabled
		if (document.getElementById(checkboxId).disabled == true) continue;
		if (toSet == null) {
			toSet = !document.getElementById(checkboxId).checked;
		}
		document.getElementById(checkboxId).checked = toSet;
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
