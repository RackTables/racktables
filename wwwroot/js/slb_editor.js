$(document).ready (function () {
	// popup form for port/ip config update
	$('.slb-checks.editable li').click (function (e) {
		if (e.target.tagName != 'LI')
			return true;
		var form = $('<div>').addClass ('popup-box').appendTo ('body').append ($(this).find('form').clone());
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
