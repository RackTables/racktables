// JavaScript functions
function bbcodes() {
	$commentblock = document.getElementsByClassName("commentblock")
  $format_search =  [
      /\[b\](.*?)\[\/b\]/ig,
      /\[i\](.*?)\[\/i\]/ig,
      /\[u\](.*?)\[\/u\]/ig
  ];
  $format_replace = [
      '<strong>$1</strong>',
      '<em>$1</em>',
      '<span style="text-decoration: underline;">$1</span>'
  ];
  Array.prototype.forEach.call($commentblock, function(str) {
      $str = str.innerHTML
      for (var i =0;i<$format_search.length;i++) $str = $str.replace($format_search[i], $format_replace[i]);
      str.innerHTML=$str;
  });
}
// Used for (un)checking an entire row of rackspace atoms
function toggleRowOfAtoms (rackId, rowId) {
	var toSet;
	var element;
	toSet = null;
	for (var columnId=0; columnId<=2; columnId++) {
		element = document.getElementById("atom_" + rackId + "_" + rowId + "_" + columnId);
		if (element != null && ! element.disabled) {
			if (toSet == null)
				toSet = !element.checked;
			element.checked = toSet;
		}
	}
}

// Used for (un)checking an entire column of rackspace atoms
function toggleColumnOfAtoms (rackId, columnId, numRows) {
	var toSet;
	var element;
	toSet = null;
	for (var rowId=1; rowId<numRows+1; rowId++) {
		element = document.getElementById("atom_" + rackId + "_" + rowId + "_" + columnId);
		if (element != null && ! element.disabled) {
			if (toSet == null)
				toSet = !element.checked;
			element.checked = toSet;
		}
	}
}

// Unchecks all checkboxes in the specified rack.
function uncheckAllAtoms (rackId, numRows) {
	var element;
	for (var rowId=1; rowId<numRows+1; rowId++) {
		for (var columnId=0; columnId<=2; columnId++) {
			element = document.getElementById("atom_" + rackId + "_" + rowId + "_" + columnId);
			if (element != null && ! element.disabled)
				element.checked = false;
		}
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
