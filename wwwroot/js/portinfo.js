if (enabled_elements == null)
	enabled_elements = [];
enabled_elements_count = enabled_elements.length;
port_cmenu_items = [];
wait_count = 0;
bk_event = null;
port_clicked = null;

(function () {
	var menu_item_candidates = {
		link: {'Show links status': {onclick: menuItemClicked, className: 'itemname-link'}},
		conf: {'Show ports configuration': {onclick: menuItemClicked, className: 'itemname-conf'}},
		mac: {'Show device learned MACs': {onclick: menuItemClicked, className: 'itemname-mac'}},
		portmac: {'Show port learned MACs': {onclick: menuItemClicked, className: 'itemname-portmac'}}
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
		if (name.match (/^(link|conf|mac)$/)) {
			port_clicked = this;
			menuItemClicked($('.context-menu-item.itemname-' + name)[0], menu);
		}
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
	else
		iconURL = '';
	$(menuItem).children('.' + $.contextMenu.innerDivClassName).css("background-image", "url(" + iconURL + ")");
}

function menuItemClicked(menuItem, menu) {
	if (!port_clicked)
		port_clicked = this;
	// if the item is already disabled, do not react on click
	if ($(menuItem).hasClass($.contextMenu.disabledItemClassName))
		return;

	// determine the typename of menuItem ('link', 'mac' or 'conf')
	var matches = menuItem.className.match(/itemname-([^ ]+)/);
	if (! matches)
		return;
	var type = matches[1];
	var per_port_cmd = type == 'portmac';
	var portnameElem = $(port_clicked).siblings('.interactive-portname')[0];
	port_clicked = null;

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
			'object_id': getQueryString('object_id'),
			'port_name': getPortName(portnameElem)
		},
		dataType: 'json',
		success: function(data, textStatus, XMLHttpRequest) {
			if (! data)
				return;
			bSuccessIcon = true;

			if (per_port_cmd)
				setItemIcon(menuItem, '');
			else
				setItemIcon(menuItem, 'ok');

			if (type == 'link')
				applyLinkData(data);
			else if (type == 'mac' || type == 'portmac')
				applyMacData(data);
			else if (type == 'conf')
				applyConfData(data);
		}
	});
	if (!per_port_cmd)
		disableMenuItem(menuItem);
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
			{
				var obj = $(this).parent().find('.mac-count');
				if (!obj.length)
					appended = $('<div />').addClass('mac-count').html(item['inline']).appendTo($(this).parent());
				else
					appended = obj.html(item['inline']);
			}
			if (item['popup'] && appended) {
				var obj = $("[id='maclist-popup-"+portname+"']");
				if (! obj.size())
					obj = $('<div />').addClass('popup-box mac-list').attr('id', 'maclist-popup-'+portname).html(item['popup']).appendTo('body');
				else
					obj.html(item['popup']);
				appended.thumbPopup(obj);
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
