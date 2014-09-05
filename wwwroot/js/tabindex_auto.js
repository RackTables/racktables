// insert tabindex to input elements
$(document).ready(function()
{
	$(":input, a[name=submit], a.input").attr("tabindex", 1);
});
