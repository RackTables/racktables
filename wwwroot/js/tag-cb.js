var tag_cb = new function () { // singletone class

var self = this;
this.enableNegation = function()  { enableNegation = true; };
var enableNegation = false;

// called from interface.php:renderCellFilterPortlet() if SHRINK_TAG_TREE_ON_CLICK is set
this.enableSubmitOnClick = function() { enableSubmitOnClick = true; };
var enableSubmitOnClick = false;

this.setTagShortList = function(list) { tagShortList = list; };
var tagShortList = {};

$(document).ready (function () {
	if (! enableNegation)
		return;
	$('.tag-cb').click(function (event) {
		cbClick(event, false);
	});
	$('.tag-cb').closest('label')
		.mousedown(function(){return false})
		.click(function (event) {
			cbClick(event, true);
			return false;
		});
	$('input[name=andor]').click(function (event) {
		if ($('.tag-cb:checked').size() != 0 && event.target.value == 'and') {
			$(event.target).closest('form')[0].submit();
		}
	});
});

// this is a handler for both checkbox click and label click
// suppresses the default action, sets cb checked state and brings input focus on int
// changes the name of checkbox if it is negated
// submits form if this feature is enabled, and filter combination function is 'and'
function cbClick (event, bInvert) {
	event.stopImmediatePropagation();
	var td = $(event.target).closest('td');
	var cb = td.find('.tag-cb');
	if (cb.length != 1)
		return;
	var tagId = cb.attr('value');
	var checked = cb.attr('checked');
	if (bInvert)
		checked = ! checked;
	if ((event.ctrlKey || event.metaKey) && ! td.hasClass('inverted') && checked) {
		var name = cb.attr('name');
		if (name.match(/^cf([tp]\[\])$/))
			cb.attr('name', 'nf' + RegExp.$1);
		td.addClass ('inverted');
	}
	else {
		var name = cb.attr('name');
		if (name.match(/^nf([tp]\[\])$/))
			cb.attr('name', 'cf' + RegExp.$1);
		td.removeClass('inverted');
	}
	if (bInvert)
		cb.attr('checked', checked ? 'checked' : '');
	cb.focus();

	if (enableSubmitOnClick) {
		var and_check = $('input[name=andor][value=and]')[0];
		if (and_check.checked)
			$(event.target).closest('form')[0].submit();
	}
}

this.compactTreeMode = function() {
	// reconfigure toggle link
	var link = $('a.toggleTreeMode')[0];
	if ($(link).filter(':visible')) {
		$(link).after('<p>');
	}
	link.onclick = function () {self.fullTreeMode(); return false;};
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

this.fullTreeMode = function() {
	// reconfigure toggle link
	var link = $('a.toggleTreeMode')[0];
	link.onclick = function () {self.compactTreeMode(); return false;};
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

};
