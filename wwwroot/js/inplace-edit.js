(function ($) { //unnamed singletone class

var editMode = false;
var visible_pen = $();
var stop_propagation = false;
var waiting_response = false;

var span = null;
var group = null;
var input = null;
var btn = null;

$(document).ready(function() {
	initCommentTD($('.rsv-port'));
	$('body').mouseover(onBodyMouseOver);
});

function initCommentTD (jqSet) {
	jqSet
		.append('<img class="edit-btn" src="?module=chrome&uri=pix/pencil-icon.png" title="Edit reservation comment" />')
		.mouseover(onTDMouseOver);
	jqSet.find('.edit-btn')
		.css('cursor', 'pointer')
		.click(onPencilClick);
}

function onTDMouseOver (event) {
	if (editMode)
		return;
	stop_propagation = true;
	var pen = $(event.target).closest('.rsv-port').find('.edit-btn');
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
	span = $(event.target).siblings('.rsvtext');
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
	input[0].setSelectionRange(255, 255);

	group.click(function(event) { event.stopPropagation();	});
	$('body').one('click',
		function (event) {
			if (! waiting_response)
				hideEditForm();
			onTDMouseOver(event);
		});
	event.stopPropagation(); // prevent the initial click to immediately close edit form
}

function onFormSubmit () {
	var comment = input.val();
	var item_id = 0;
	var a = $(input).closest('tr').find('a.ancor');
	var mode;
	if (a.length) {
		item_id = a[0].name.replace(/^(port|ip)-/, '');
		mode = a[0].name.replace(/^(port|ip)-.*/, '$1');
	}
	if (mode == undefined)
		throw "cant determine run mode: neither 'port' nor 'ip'";

	waiting_response = true;
	input.attr('disabled', 'true');
	btn.replaceWith('<img src="?module=chrome&uri=pix/ajax-loader.gif" title="Please wait" />');
	$.ajax({
		type: 'POST',
		url: 'index.php',
		data: {
			'module': 'ajax',
			'ac': 'upd-reservation-' + mode,
			'id': item_id,
			'comment': comment
		},
		dataType: 'json',
		success: function(data, textStatus, XMLHttpRequest) {
			if (mode == 'port') {
				var td1 = input.closest('td.rsv-port');
				var td0 = td1.prev('td');
				hideEditForm();
				var tr = td1.closest('tr');
				td0.replaceWith(data[0]);
				td1.replaceWith(data[1]);
				initCommentTD($(tr).find('.rsv-port'));
			}
			else if (mode == 'ip' && data == 'OK') {
				span.html(input.val());
				hideEditForm();
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			hideEditForm();
			alert ('Error updating port: ' + textStatus);
		},
		complete: function() {
			waiting_response = false;
		}
	});
}

})(jQuery);
