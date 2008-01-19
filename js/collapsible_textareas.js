/*  Collapsible Textareas, version 1.0
 *  (c) 2007 SiteCrafting, Inc. <service@sitecrafting.com>
 *
 *  Collapsible Textareas is available under the Creative Commons Attribution
 *  3.0 License (http://creativecommons.org/licenses/by/3.0/).
 *
/*--------------------------------------------------------------------------*/


// find all the forms with textareas we want to allow to collapse
function setupTextareas() {
	var pageForms = document.getElementsByTagName("form");

	for( var j=0; j<pageForms.length; j++) {
		var formArea = pageForms[j];

		if( formArea.className.indexOf("collapse_tareas") > -1 ) {
			var txtAreas = formArea.getElementsByTagName("textarea");
			for( var i=0; i<txtAreas.length; i++ ) {
				var thisTxtArea = txtAreas[i];

				if( thisTxtArea.addEventListener ) {
					thisTxtArea.addEventListener("focus", bigSmlTextarea, false);
					thisTxtArea.addEventListener("blur", bigSmlTextarea, false);
				} else { // IE
					thisTxtArea.attachEvent("onfocus", bigSmlTextarea);
					thisTxtArea.attachEvent("onblur", bigSmlTextarea);
				}
			}
		}
	}
}

// collapse or expand a textarea
function bigSmlTextarea(e)
{
	var node = ( e.target ? e.target : e.srcElement );

	if( node.className.indexOf("expanded") == -1 )
		node.className += " expanded";
	else
		node.className = node.className.replace(/expanded/gi, "");
}
