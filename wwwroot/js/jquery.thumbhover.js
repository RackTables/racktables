// This library is based on jquery.thumbhover.js from unknown author.
// Far and away modified for using in RackTables.
// Usage: jquery_object_a.thumbPopup(jquery_object_b, [options])
//   after that call, object_b will be hidden
//   object_a, when hovered, will show object_b floating and moving by the cursor.
//   When clicked on object_a, object_b will stay visible until outside click
(function($) {
	$.fn.thumbPopup = function(popup, options)
	{
		//Combine the passed in options with the default settings
		var settings = jQuery.extend({
			//popupId: "thumbPopup",
			cursorTopOffset: 15,
			cursorLeftOffset: 15,
			event: null, // if set, the popup will be displayed immediately, and no mouse handlers will be binded to target
			showFreezeHint: true
		}, options);

		var freezeHint = null;
		var timeout_id = null;
		var hintHeight = 0;
		if (! freezeHint && settings.showFreezeHint && settings.event == null) {
			freezeHint = $('<div />').html('click to freeze').css('position', 'absolute').css('border', '1px solid black').css('background-color', 'white').appendTo('body');
			hintHeight = freezeHint[0].offsetHeight;
			freezeHint.hide();
		}
		
		if (! popup.length)
			return false;
		popup.css("position", "absolute").hide();
		$(this).css("cursor", "pointer");
		
		//Attach hover events that manage the popup
		if (settings.event != null)
		{
			setPopup (settings.event);
			stickPopup (settings.event);
		}
		else {
			$(this)
			.bind('mouseenter', setPopup)
			.bind('mousemove', updatePopupPosition)
			.bind('mouseleave', hidePopup)
			.click(stickPopup);
		}
		
		function stickPopup(event) {
			popup.data("sticked", true);
			if (freezeHint)
				freezeHint.hide();
			$('body').bind('click', function(event) {
				if (! (event.target == popup[0] || $(event.target).parents().filter(popup).length))
				{
					popup.data("sticked", false);
					hidePopup (event);
				}
				return true;
			});
			return false;
		}
		
		function ShowFreezeHint() {
			freezeHint.show();
		}
		
		function setPopup(event) {
			if (popup.data("sticked"))
				return;
			popup.data("hovered", true);
			updatePopupPosition(event);
			$(popup).show();
		}
		
		function updatePopupPosition(event) {
			var windowSize = getWindowSize();
			var popupSize = getPopupSize();
			var bPopupShownAbove = windowSize.height + windowSize.scrollTop < event.pageY + popupSize.height + settings.cursorTopOffset;
			if (freezeHint) {
				var deltaHint = bPopupShownAbove ? 20 :  - 5 - hintHeight;
				freezeHint.hide();
				freezeHint.css('top', event.pageY + deltaHint).css('left', event.pageX);
				clearTimeout(timeout_id);
				timeout_id = setTimeout(ShowFreezeHint, 1000);
			}
			if (popup.data("sticked"))
				return;
			var left;
			if (windowSize.width + windowSize.scrollLeft < event.pageX + popupSize.width + settings.cursorLeftOffset){
				left = event.pageX - popupSize.width - settings.cursorLeftOffset;
			} else {
				left = event.pageX + settings.cursorLeftOffset;
			}
			// center pop-up if it does not fit entirely into window
			if (
				left < windowSize.scrollLeft ||
				left + popupSize.width > windowSize.scrollLeft + windowSize.width
			) {
				left = (windowSize.width - popupSize.width) / 2;
			}
			$(popup).css("left", left);

			if (bPopupShownAbove) {
				$(popup).css("top", event.pageY - popupSize.height - settings.cursorTopOffset);
			} else {
				$(popup).css("top", event.pageY + settings.cursorTopOffset);
			}
		}
		
		function hidePopup(event) {
			popup.data("hovered", false);
			if (freezeHint) {
				freezeHint.hide();
				clearTimeout(timeout_id);
			}
			if (popup.data("sticked"))
				return;
			$(popup).hide();
			if (settings.event != null)
				$(popup).remove();
		}
		
		function getWindowSize() {
			return {
				scrollLeft: $(window).scrollLeft(),
				scrollTop: $(window).scrollTop(),
				width: $(window).width(),
				height: $(window).height()
			};
		}
		
		function getPopupSize() {
			return {
				width: $(popup).width(),
				height: $(popup).height()
			};
		}
		
		//Return original selection for chaining
		return this;
	};
})(jQuery);
