// var new_vst_row is set through inline JS script

$(document).ready(function() {
	$('a.vst-add-rule').click(AddVSTRule)
	$('a.vst-del-rule').click(RemoveVSTRule)
	$('form#upd')
		.submit(VSTSubmit)
		.change(highlightUnsavedVST);
});

function AddVSTRule(event) {
	var tr = $(this).closest('tr');
	var new_tr;
	if ($(event.target).parents('.vst-add-rule.initial').length)
		new_tr = $('<tr />').html(new_vst_row);
	else
		new_tr = tr.clone();
	tr.after(new_tr);
	new_tr.find('a.vst-add-rule').click(AddVSTRule);
	new_tr.find('a.vst-del-rule').click(RemoveVSTRule);

	highlightUnsavedVST();
	return false;
}

function RemoveVSTRule(event) {
	$(this).closest('tr').remove();
	highlightUnsavedVST();
	return false;
}

function VSTSubmit() {
	var result = [];
	$('table.template-rules tr').each(function(i, item) {
		var line = {};
		$(item).find('input , select').each(function(i, item) {	line[item.name] = $(item).val(); });
		if (line['rule_no'] != undefined)
			result.push(line);
	});
	if (! result.length && ! confirm('The template is empty. Do you really want to save it?'))
		return false;
	$(this).find('input[name|="template_json"]').val(JSON.stringify(result));
	return true;
}

function highlightUnsavedVST() {
    // highlight only on first call of this function
    if ( typeof highlightUnsavedVST.isAlreadyCalled == 'undefined' ) {
        highlightUnsavedVST.isAlreadyCalled = true;
		$('form#upd input[name|="submit"]').before('<div class="msg_warning">Template is unsaved. Click here to save it</div>');
		console.log('a');
    }
}
