$(document).ready (function () {
	// popup form for port/ip config update
	$('.slb-checks.editable li').click (function (e) {
		if (e.target.tagName != 'LI')
			return true;
		// clone form into new_form, re-setting all selectbox values which are cleared by clone()
		var old_form = $(this).find('form');
		var new_form = old_form.clone();
		new_form.find('select').each (function (i, elem) {
			var corresponding = old_form.find('select[name="' + $(this).attr('name') + '"]');
			if (corresponding.length)
				$(this).val(corresponding.val());
		});
		var form = $('<div>').addClass ('popup-box').appendTo ('body').append (new_form);
		// confirmation box on port/vip deletion
		$('a.del-used-slb').click (function (e) {
			return confirm ('This entity is used. Please confirm the deletion');
		});

		$(this).thumbPopup (form, { event: e });
		return false;
	});

	// confirmation box on triplet deletion
	$('form#del input').click (function (e) {
		return confirm ('Please confirm the deletion of triplet');
	});

	// new triplet form stage1 handler
	var forms = $('form#addLink');
	forms.submit (function() { return false });
	forms.find('input[name="submit"]').click (function (e) {
		var form = $(this).parents('form');
		$.ajax ({
			type: 'POST',
			url: 'index.php',
			data: {
				'module': 'ajax',
				'ac': 'get-slb-form',
				'form': form.serialize(),
				'action': form.attr('action')
			},
			'success': function (data) {
				form.replaceWith (data);
			}
		});

		return false;
	});
});

function slb_config_preview (e, object_id, vs_id, rspool_id) {
	$.ajax ({
		type: 'POST',
		url: 'index.php',
		data: {
			module: 'ajax',
			ac: 'triplet-preview',
			object_id: object_id,
			vs_id: vs_id,
			rspool_id: rspool_id
		},
		success: function (data) {
			var div = $('<div />').addClass('popup-box').appendTo('body');
			div.html(data);
			$(this).thumbPopup (div, { event: e });
		}
	});
}
