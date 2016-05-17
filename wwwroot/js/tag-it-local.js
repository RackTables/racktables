suggest_size = 10;
window.onerror = function () {
	   alert("An error occurred.");
	};

$(document).ready(function() {
	$("input[data-tagit=yes]").each(function (index, value) {
		var tag_input = value;
		var form = $(tag_input).closest("form");
		var value_name = $(tag_input).data("tagit-valuename");
		var ul = $(form).find("ul[data-tagit=yes][data-tagit-valuename=" + value_name + "]")[0];
		var tag_limit = $(tag_input).data("tagit-limit") || 0;
		var pass_value = $(tag_input).data("tagit-pass-value") || false;
		var specific_taglist = $(tag_input).data("tagit-values");
		var preselect = $(tag_input).data("tagit-preselect") || $(ul).data("tagit-preselect")  ||[];
		var use_taglist = specific_taglist || taglist;
		var expand_all_btn = form.find("span.icon-folder-open.tagit_input_" + value_name)[0];
	
		generateTagList(tag_input, ul, use_taglist, preselect, value_name, tag_limit, expand_all_btn, pass_value);
	});
});

function getTagChild(tag, tags_trace, tags_name_to_id) {
	var result = [];
	if (Object.keys(tags_trace).length > 0) {
		if (typeof tag == "undefined" || tag.length == 0) { // empty tag
			for(var k in tags_trace) {
				if (tags_trace[k].length == 0)
					result.push(k);
			};
		}
		else {
			tag_id = tags_name_to_id[tag];
			for(var k in tags_trace) {
				if (tags_trace[k].slice(-1)[0] == tag_id)
					result.push(k);
			};
			// if (result.length == 0)
			// result.push("no child");
		}
	}
	return result;
}

function finder (source, req_term) {
	var term = $.ui.autocomplete.escapeRegex(req_term);
	var startsWithMatcher = new RegExp("^" + term, "i");
	var startsWith = $.grep(source, function(value) {
		return startsWithMatcher.test(value.label || value.value || value);
	});
	var containsMatcher = new RegExp(term, "i");
	var contains = $.grep(source, function (value) {
		return $.inArray(value, startsWith) < 0 && containsMatcher.test(value.label || value.value || value);
	});
	return startsWith.concat(contains) || [];
}

function suggest_search (term) {
	var results = [];
	if (term == "*") {
		if (typeof(this.options.tags_trace) != 'undefined' &&  Object.keys(this.options.tags_trace).length > 0) {
			if (typeof(this.tagInput) != 'undefined')
				this.tagInput.autocomplete('widget').addClass('indented');
			var roots = [];
			var tree_styles = {};
			tree_styles[0] = [];
			for (tag in this.options.tags_trace) // root tags
				if (this.options.tags_trace[tag].length == 0) {
					roots.push(tag);
					tree_styles[0].push(this.options.tags_name_to_id[tag]);
				}
			var changes = 1;
			var i = 0;
			var taglist_tmp = $.extend(1, this.options.taglist);
			while (changes) {
				i++;
				tree_styles[i] = [];
				changes = 0;
				for (tag_id in taglist_tmp)
				{
					if (taglist_tmp[tag_id]['trace'].length == i)
					{
						var tag = this.options.tags_to_name[tag_id];
						tag_parent_id = this.options.taglist[tag_id]['trace'].slice(-1)[0];
						tag_parent = this.options.tags_to_name[tag_parent_id];
						index = roots.indexOf(tag_parent);
						roots.splice(index + 1, 0, tag);
						delete taglist_tmp[tag_id];
						tree_styles[i].push(tag_id);
						changes = 1;
					}
				}
			}
			results = roots;
		}
		else {
			$.each(this.options.all_tags, function(i, v) {results.push(v)});
			results.sort();
		}
	}
	else if (term.slice(-1) == "/" || term.slice(-1) == "\\") {
		var term_cutted = term.slice(0, -1);
		if (term_cutted.length == 0)
			results = getTagChild(undefined, this.options.tags_trace, this.options.tags_name_to_id);
		else {
			var found_tags = finder(this.options.available_tags, term_cutted);
			if (found_tags.length == 0) {
				found_tags = finder(this.options.all_tags, term_cutted);
			}
			results = getTagChild(found_tags[0], this.options.tags_trace, this.options.tags_name_to_id);
		}
		results.sort();
	}
	else {
		var wildcard = false;
		if (/\*$/.test(term))
		{
			wildcard = true;
			term = term.substring(0, term.length - 1);
		}
		results = finder(this.options.available_tags, term);
		if (results.length == 0) {
			results = finder(this.options.all_tags, term);
		}
		if (! wildcard)
			results = results.slice(0, suggest_size); // cutting
		results.sort();
	}
	
	return results;
};

function suggest (request, response) {
	var results = suggest_search.call(this, request.term);
	return results;
};

function generateTagList(input, ul, taglist, preselect, value_name, tag_limit, expand_all_btn, pass_value) {
	var tags_name_to_id = [];
	var tags_trace = {};
	var tags_to_name = [];
	var all_tags = [];
	var available_tags = [];
	var selected = [];

	function getHelp(tag) {
		s = [];
		var tag_trace = tags_trace[tag];
		for (var k in tag_trace)
		{
			s.push(tags_to_name[tag_trace[k]]);
		}
		s.push(tag);
		var result = s.join(" \u2192 "); // right arrow
		return result;
	}

	function generateClass(tag) {
		var result = [];
		result.push("tag-" + tags_name_to_id[tag]);
		result.push("etag-" + tags_name_to_id[tag]);
		if (typeof tags_trace[tag] != 'undefined')
			tag_trace = tags_trace[tag];
		else
			tag_trace = [];
		$.each(tag_trace, function (index, value) {result.push("tag-" + value);});
		result.push("tag-level" + tag_trace.length);
		tagclass = taglist[tags_name_to_id[tag]].tagclass;
		if(tagclass != 'undefined')
			result.push(tagclass);
		return result;
	}
	
	function beforeTagAdded(event, ui) {
		if (jQuery.inArray(ui.tagLabel, available_tags) < 0) { // skip not available tags
			return false;
		}
	}

	function afterTagRemoved(event, ui) {
		tag_id = ui.tagLabel;
		tag = tags_to_name[tag_id];
		selected.splice($.inArray(tag, selected), 1);
		available_tags.push(tag);
		available_tags.sort();
		if (tag_limit != 0 && $(ul).tagit("assignedTags").length < tag_limit) {
			$(ul).tagit("setStatus", true);
			if (typeof(expand_all_btn) != 'undefined')
				$(expand_all_btn).attr('disabled', '');
		}
	}

	function afterTagAdded(event, ui) {
		tag = ui.tagLabel;
		tag_id = tags_name_to_id[tag];
		li = ui.tag[0];
		hidded_value = $(li).find("input[value='" + tag + "']")[0]; // replace tag by tag id
		hidded_value.value = tag_id;
		tag_label = $(ui.tag[0]).find(".tagit-label");
		tag_classes = generateClass(tag);
		$.each(tag_classes, function (index, value) {tag_label.addClass(value);});
		// generate title
		r = [];
		r.push(tag_label.attr('title'));
		r.push(getHelp(tag));
		help_text = $.map(r, function (value, index) {if (value.length > 0) {return value;}});
		help_text = help_text.join("\n");
		tag_label.attr("title", help_text);
		// remove tag from available_tags
		available_tags.splice($.inArray(tag, available_tags), 1);
		selected.push(tag);
		if (tag_limit != 0 && $(ul).tagit("assignedTags").length >= tag_limit) {
			$(ul).tagit("setStatus", false);
			if (typeof(expand_all_btn) != 'undefined')
				$(expand_all_btn).attr('disabled', 'disabled');
		}
	}

	$.each(taglist, function (index, value) {tags_name_to_id[value['tag']] = index;});
	$.each(taglist, function (index, value) {
		if (typeof(value['trace']) != 'undefined')
			tags_trace[value['tag']] = value['trace'];
	});
	$.each(taglist, function (index, value) {tags_to_name[index] = value['tag'];});
	$.each(taglist, function (index, value) {all_tags.push(value['tag']);});

	var oldresizeMenu = $.ui.autocomplete.prototype._resizeMenu;
	$.ui.autocomplete.prototype._resizeMenu = function() {
		//oldresizeMenu.call(this);
		// do not interfere with other user-defined autocomplete inputs
		if ($(this.element).filter('input[data-tagit=yes]').length)
		{
			var ul = this.menu.element;
			ul.children("li:(.ui-menu-item)").addClass("tagit-menu-item");
			ul.removeClass("ui-widget-content");
		}
	};

	function renderItem (ul, item) {
		var li = $('<li>');
		var a = $('<a>');
		var tag = item.label;
		var tag_classes = generateClass(tag);
		var help_text = getHelp(tag);
		var m_div = $('<div style="display: inline-block; vertical-align: middle;"></div>');
		a.prepend(m_div);
		m_div.prepend($('<div style="float: left;">' + tag + '</div>'));
		if (jQuery.inArray(tag, available_tags) < 0) {
			if (jQuery.inArray(tag, selected) >= 0)
				help_text = "already assigned";
			else
				help_text = "is not assignable";
			tag_classes.push("disabled");
		}
		// add title to item
		a.attr("title", help_text);
		// add classes to item
		$.each(tag_classes, function (index, value) {
			li.addClass(value);
		});
		// add icons to item
		if (jQuery.inArray("disabled", tag_classes) > 0)
			m_div.append('<span style="float: left;" class="ui-icon ui-icon-locked"></span>');
		child_count = getTagChild(tag, tags_trace, tags_name_to_id).length;
		if(child_count > 0)
			m_div.append('<span style="float: left;" class="ui-icon ui-icon-arrowthick-1-se"></span>');
		return li.data('item.autocomplete', item).append(a).appendTo(ul);
	};
	
	function afetrRenderMenu (ul, items) {
		if (ul.find("li").length <= suggest_size) {
			ul.append(ul.find("li.disabled")); // move to down disabled tags
		}
	}

	function suggest_wrapper (request, response) {
		if (typeof(this.tagInput) != 'undefined')
			this.tagInput.autocomplete('widget').removeClass('indented');
		var results = suggest.call(this, request, response);
		response.call(this, results);
		return results;
	};
	
	// click handler for tag expand
	if (typeof(expand_all_btn) != 'undefined')
	{
		$(expand_all_btn).click(function() {
			if ($(this).attr('disabled') != "disabled") {
				e = jQuery.Event('keydown');
				if ($(input)[0].value == "*") {
					e.keyCode = e.which = $.ui.keyCode.ESCAPE;
					$(input).trigger(e);
					$(input).val("");
				}
				else {
					$(input).val("*");
					$(input).focus();
					$(input).autocomplete("search", "*");
				}
			}
		});
	}
	$.each(taglist, function (index, value) {
		if (typeof value["is_assignable"] == "undefined" || value["is_assignable"] == "yes") 
			available_tags.push(value['tag']);
	});
	

	function autocomplete_open(event, ui) {
			$(this).data("autocomplete").menu.element.addClass("autocomplete-menu");
			var menu_obj = $(this);
			// escape handler
			$(document).bind("keydown", function(event, arg) {
				menu_obj.data("autocomplete").close(event, arg);
			});
			$(this).data("autocomplete").close = function (e, ar) {
				if (typeof(e) == 'undefined' || (e.type == "menuselected" || e.keyCode == $.ui.keyCode.ESCAPE || e.keyCode == $.ui.keyCode.BACKSPACE))
					clearTimeout(this.closing), this.menu.element.is(":visible") && (this.menu.element.hide(), this.menu.deactivate(), this._trigger("close", e));
				else
					return false;
			};
	}
	function change_value(input, value) {
		if (pass_value)
			var tmp_value = value;
		else
			var tmp_value = tags_name_to_id[value] || "";
		input.attr("value", tmp_value);
	}

	var oldRenderMenu = $.ui.autocomplete.prototype._renderMenu;
	if (ul) {
		$(ul).tagit({
			fieldName: value_name + "[]",
			tagInput: $(input),
			showAutocompleteOnFocus: false,
			removeConfirmation: true,
			caseSensitive: false,
			allowDuplicates: false,
			allowSpaces: true,
			animate: false,
			readOnly: false,
			tagLimit: tag_limit,
			singleField: false,
			singleFieldDelimiter: ",",
			singleFieldNode: null,
			backspaceRemove: false,
			tabIndex: null,
			insertPosition: 'left',
			autocomplete: {
								autoFocus: true,
								source: suggest_wrapper,
								open: autocomplete_open,
								create: function(event, ui){
									$(this).data("autocomplete")._renderItem = function(ul, item) {
										renderItem.call(this, ul, item);
									};
									$(this).data("autocomplete")._renderMenu = function(ul, items) {
										oldRenderMenu.call(this, ul, items);
										afetrRenderMenu.call(this, ul, items);
									};
								},
			},
			// Events
			beforeTagAdded: beforeTagAdded,
			afterTagAdded: afterTagAdded,
			afterTagRemoved: afterTagRemoved,
			available_tags: available_tags,
			all_tags: all_tags,
			tags_trace: tags_trace,
			tags_to_name: tags_to_name,
			tags_name_to_id: tags_name_to_id,
			taglist: taglist,
		});
		$(ul).closest("form").find("input:hidden[name='" + value_name + "[]" + "']").remove(); // remove extra data
		$.each(preselect, function(index, value) {
			var title = "";
			if (typeof value['user'] != 'undefined' && value['user'] != null)
				title = value['user'] + ", " + value['time_parsed'];
			else
				title = "";
			$(ul).tagit("createTag", value['tag'], false, false, title);
		});
	}
	else {
		if (typeof(preselect) != 'undefined') {
			if (typeof (preselect['tag']) != 'undefined') {
				var selected_tag = preselect['tag'];
			}
			else if (typeof (preselect['id']) != 'undefined') {
				var selected_tag = tags_to_name[preselect['id']];
			}
			if (typeof (selected_tag) != 'undefined') {
				$(input).attr("value", selected_tag);
				available_tags.splice($.inArray(selected_tag, available_tags), 1);
				selected.push(selected_tag);
			}
		}
		var form = $(input).closest("form");
		var value_holder = $('<input type=hidden name="' + value_name + '" value="">');
		$(form).find("input:hidden[name='" + value_name + "']").remove();
		$(form).append(value_holder);

		$(input).autocomplete({
			source: suggest_wrapper,
			open: autocomplete_open,
			available_tags: available_tags,
			all_tags: all_tags,
			create: function(event, ui){
				$(this).data("autocomplete")._renderItem = function(event, ui) {
					renderItem.call(this, event, ui);
				};
				$(this).data("autocomplete")._renderMenu = function(ul, items) {
					oldRenderMenu.call(this, ul, items);
					afetrRenderMenu.call(this, ul, items);
				};
			},
			select: function(event, ui) {
				change_value(value_holder, ui.item.value);
			},
		});
		$(input).click(function(){
		    $(this).select();
		    $(this).autocomplete('close');
		});
		$(input).bind('input', function() {
			change_value(value_holder, this.value);
		});
		// for old browsers
		$(input).keyup(function() {
			change_value(value_holder, this.value);
		});
	}
}
