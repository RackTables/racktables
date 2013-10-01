if (enabled_elements == null)
	enabled_elements = [];
enabled_elements_count = enabled_elements.length;
port_cmenu_items = [];
wait_count = 0;
bk_event = null;

(function () {
	var menu_item_candidates = {
		trace: {'Trace this port': {onclick: tracePortClicked, className: 'itemname-trace'}},
		link: {'Show links status': {onclick: menuItemClicked, className: 'itemname-link'}},
		conf: {'Show ports configuration': {onclick: menuItemClicked, className: 'itemname-conf'}},
		mac: {'Show learned MACs': {onclick: menuItemClicked, className: 'itemname-mac'}}
	};
	var portinfo_enabled = false;
	for (var i in enabled_elements) {
		var name = enabled_elements[i];
		port_cmenu_items.push(menu_item_candidates[name]);
		if (menu_item_candidates[name] != null && name.match (/^(link|conf|mac)$/))
			portinfo_enabled = true;
	}
	if (portinfo_enabled) {
		port_cmenu_items.unshift($.contextMenu.separator);
		port_cmenu_items.unshift({'Show all info': {onclick: showAllClicked, className: 'itemname-all'}});
	}
})();

function showAllClicked(menuItem, menu) {
	for (var i in enabled_elements) {
		var name = enabled_elements[i];
		if (name.match (/^(link|conf|mac)$/))
			menuItemClicked($('.context-menu-item.itemname-' + name)[0], menu);
	}
}

$(document).ready(function () {
	// create global object port_cmenu
	if (port_cmenu_items.length) {
		port_cmenu = $.contextMenu.create(port_cmenu_items, {theme:'vista'});
		// add popup button after every portname
		$('.interactive-portname.port-menu').after('<a title="Show info from switch" class="port-popup" href="#">&#133;</a>');
		$('<div />').hide().addClass('cursor-shadow').css('position', 'absolute').css('width', 16).css('height', 16).css("background-image", "url(index.php?module=chrome&uri=pix/ajax-loader.gif").appendTo('body');

		// bind popup menu to every link with class port-popup
		$('a.port-popup').bind('click', function(e){port_cmenu.show(this,e);return false;});
		$('body').bind('mouseup', rememberCursorPosition);
	}
});

function disableMenuItem(menuItem) {
	setTimeout (function() {
		if (! $(menuItem).hasClass($.contextMenu.disabledItemClassName)) {
			$(menuItem).addClass($.contextMenu.disabledItemClassName);
			if (--enabled_elements_count <= 0) {
				$('.context-menu-item.itemname-all').addClass($.contextMenu.disabledItemClassName);
			}
		}
	}, 50);
}

function setItemIcon(menuItem, iconName) {
	var iconURL;
	if (iconName == 'wait')
		iconURL = 'index.php?module=chrome&uri=pix/ajax-loader.gif';
	else if (iconName == 'ok')
		iconURL = 'index.php?module=chrome&uri=pix/checkbox_yes.png';
	$(menuItem).children('.' + $.contextMenu.innerDivClassName).css("background-image", "url(" + iconURL + ")");
}

function menuItemClicked(menuItem, menu) {
	// if the item is already disabled, do not react on click
	if ($(menuItem).hasClass($.contextMenu.disabledItemClassName))
		return;

	// determine the typename of menuItem ('link', 'mac' or 'conf')
	var matches = menuItem.className.match(/itemname-([^ ]+)/);
	if (! matches)
		return;
	var type = matches[1];

	var bSuccessIcon = false;
	$.ajax({
		async: true,
		beforeSend: function(XMLHttpRequest) {
			if (++wait_count > 0) {
				cursor_shadow = $('.cursor-shadow');
				$('body').bind('mousemove', waitCursorMove);
				if (bk_event)
					waitCursorMove(bk_event);
			}
			setItemIcon(menuItem, 'wait');
		},
		complete: function(XMLHttpRequest, textStatus) {
			if (--wait_count <= 0) {
				$('.cursor-shadow').hide();
				$('body').unbind('mousemove', waitCursorMove);
				$('body').unbind('click', rememberCursorPosition);
			}
			if (! bSuccessIcon)
				setItemIcon(menuItem, '');
		},
		type: 'GET',
		url: 'index.php',
		data: {
			'module': 'ajax',
			'ac': 'get-port-' + type,
			'object_id': getQueryString('object_id')
		},
		dataType: 'json',
		success: function(data, textStatus, XMLHttpRequest) {
			if (! data)
				return;
			bSuccessIcon = true;
			setItemIcon(menuItem, 'ok');
			if (type == 'link')
				applyLinkData(data);
			else if (type == 'mac')
				applyMacData(data);
			else if (type == 'conf')
				applyConfData(data);
		}
	});
	disableMenuItem(menuItem);
}

function tracePortClicked(event) {
	var portnameElem = $(this).siblings('.interactive-portname')[0];
	var port_id = portnameElem.name.replace (/port-/, '');
	window.open (
		"?module=popup&helper=traceroute&port=" + port_id,
		'findlink',
		'height=700, width=700, location=no, menubar=no, resizable=yes, scrollbars=yes, status=no, titlebar=no, toolbar=no'
	);
}

function rememberCursorPosition(event) {
	bk_event = event;
}

function waitCursorMove(event) {
	cursor_shadow.css("left", event.pageX + 15).css("top", event.pageY + 15).show();
}

function applyLinkData(data) {
	var seen_portnames = {};
	$('.interactive-portname').each(function() {
		var portname = getPortName (this);
		var item = data[portname];
		if (item != null) {
			var prepended;
			if (item['inline'])
				prepended = $('<span />').addClass('port-link').html(item['inline']).insertBefore(this);
			if (item['popup'] && prepended) {
				if (! seen_portnames[portname])
					seen_portnames[portname] = $('<div />').addClass('popup-box').html(item['popup']).appendTo('body');
				prepended.thumbPopup(seen_portnames[portname]);
			}
		}
	});
}

function applyMacData(data) {
	var seen_portnames = {};
	$('.interactive-portname').each(function() {
		var portname = getPortName (this);
		var item = data[portname];
		if (item != null) {
			var prepended;
			if (item['inline'])
				appended = $('<div />').addClass('mac-count').html(item['inline']).appendTo($(this).parent());
			if (item['popup'] && appended) {
				if (! seen_portnames[portname])
					seen_portnames[portname] = $('<div />').addClass('popup-box mac-list').html(item['popup']).appendTo('body');
				appended.thumbPopup(seen_portnames[portname]);
			}
		}
	});
}

function applyConfData(data) {
	var seen_portnames = {};
	$('.interactive-portname').each(function() {
		var portname = getPortName (this);
		var item = data[portname];
		if (item != null) {
			if (! seen_portnames[portname])
				seen_portnames[portname] = $('<div />').addClass('popup-box port-config').html(item['popup']).appendTo('body');
			$(this).thumbPopup(seen_portnames[portname]);
		}
	});
}

function getPortName (element) {
	if (element.type == 'text')
		return $(element).val();
	else
		return $(element).html();
}
