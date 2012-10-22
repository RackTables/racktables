(function ($) { //unnamed singletone class

var editMode = false;
var visible_pen = $();
var stop_propagation = false;
var waiting_response = false;
var editable_filter = 'span.editable';

var span = null;
var group = null;
var input = null;
var btn = null;

$(document).ready (function() {
	$(editable_filter).each (function (i, iSpan) {
		var container = $('<div class="edit-container"/>')
			.mouseover(onSpanMouseOver);
		$(iSpan)
			.after (container)
			.detach()
			.appendTo (container);
		container
			.append
			(
				$('<div class="empty" style="margin-left: 10px; width:12px; height:12px;"><img class="edit-btn" src="?module=chrome&uri=pix/pencil-icon.png" title="Edit text" /></div>')
				.click(onPencilClick)
			);
	});
	$('body').mouseover(onBodyMouseOver);
});

function onSpanMouseOver (event) {
	if (editMode)
		return;
	stop_propagation = true;
	var pen = $(event.target).closest('.edit-container').find('.edit-btn');
	if (! visible_pen.length || visible_pen[0] != pen[0]) {
		visible_pen.hide();
		visible_pen = pen;
		visible_pen.show();
	}
}

function onBodyMouseOver () {
	if (!editMode && !stop_propagation) {
		visible_pen.hide();
		visible_pen = $();
	}
	stop_propagation = false;
}

function hideEditForm () {
	input = null;
	btn = null;
	group.remove();
	span.show();
	editMode = false;
};

function onPencilClick (event) {
	// hide original plain text comment
	span = $(event.target).closest('.edit-container').find(editable_filter);
	var width = span[0].offsetWidth + 50;
	if (width < 150)
		width = 150;
	span.hide();

	// hide pencil
	onBodyMouseOver();
	editMode = true;

	// construct editor form
	group = $('<span class="reservation-form" />');
	input = $('<input type="text" />')
		.val(span.html().replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>'))
		.css('width', '' + width + 'px')
		.keydown(
			function (event) {
				var code = event.keyCode ? event.keyCode : event.which;
				if (code == 13)
					onFormSubmit();
				else if (code == 27)
					hideEditForm();
			})
		.appendTo(group);
	group.append('&nbsp;');
	btn = $('<img src="?module=chrome&uri=pix/tango-document-save-16x16.png" title="Save changes" />')
		.css('cursor', 'pointer')
		.click( onFormSubmit )
		.appendTo(group);
	span.after(group);
	input[0].focus();
	doSetCaretPosition (input[0], input[0].value.length);

	group.click(function(event) { event.stopPropagation();	});
	$('body').one('click',
		function (event) {
			if (! waiting_response)
				hideEditForm();
			onSpanMouseOver(event);
		});
	event.stopPropagation(); // prevent the initial click to immediately close edit form
}

function doSetCaretPosition (input, iCaretPos) {
	if (input.setSelectionRange) {
		input.setSelectionRange(iCaretPos, iCaretPos);
	}
	else if (input.createTextRange) {
		var range = input.createTextRange();
		range.moveEnd("character", iCaretPos);
		range.moveStart("character", iCaretPos);
		range.select();
	}
}

function onFormSubmit () {
	var text = input.val();
	input.attr('disabled', 'true');
	btn.replaceWith('<img src="?module=chrome&uri=pix/ajax-loader.gif" title="Please wait" />');
	waiting_response = true;

	var op = '';
	var item_id = '';
	var list = span[0].className.split (/\s+/);
	for (var i in list) {
		var cn = list[i];
		var m;
		if (m = cn.match (/^id-(.*)/)) {
			item_id = m[1];
		}
		else if (m = cn.match (/^op-(.*)/)) {
			op = m[1];
		}
	}

	$.ajax({
		type: 'POST',
		url: 'index.php',
		data: {
			'module': 'ajax',
			'ac': op,
			'id': item_id,
			'text': text
		},
		success: function(data, textStatus, XMLHttpRequest) {
			if (data == 'OK')
				span.html(text);
			else
				alert ('Error updating port: ' + data);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert ('Error updating port: ' + textStatus);
		},
		complete: function() {
			hideEditForm();
			waiting_response = false;
		}
	});
}

})(jQuery);
