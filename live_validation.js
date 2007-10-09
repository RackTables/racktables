var Validate = {
	elements:[],
	//Some General purpose functions
	hasClass:function(ele,cls) {
		return ele.className.match(new RegExp('(\\s|^)'+cls+'(\\s|$)'));
	},
	addClass:function (ele,cls) {
		if (!this.hasClass(ele,cls)) ele.className += " "+cls;
	},
	removeClass:function (ele,cls) {
		if (this.hasClass(ele,cls)) {
			var reg = new RegExp('(\\s|^)'+cls+'(\\s|$)');
			ele.className=ele.className.replace(reg,' ');
		}
	},
	stopEvent:function(e) {
		//e.cancelBubble is supported by IE - this will kill the bubbling process.
		e.cancelBubble = true;
		e.returnValue = false;
		//e.stopPropagation works in Mozilla.
		if (e.stopPropagation) {
			e.stopPropagation();
			e.preventDefault();
		}
	},
	success:function(ele) {
		this.removeClass(ele,"validation-error");
		this.addClass(ele,"validation-success");
	},
	failure:function(ele) {
		this.removeClass(ele,"validation-success");
		this.addClass(ele,"validation-error");
	},
	//Logic starts here...
	//Deny keyboard input for some specific chars.
	validateKeys : function(e,ele) {
		//Find Which key is pressed
		if (e.keyCode) code = e.keyCode;
		else if (e.which) code = e.which;
		var character = String.fromCharCode(code);

		//We need only valid keypresses - not stuff like shift, Enter, Backspace etc.
		if(	e.ctrlKey || 
			code == 46 //Delete Key
		) return;
		if(e.shiftKey) {
			if(!(code >= 37 && code <= 40)) return; //Up,Down,Right and Left - same keycode as %,&,' and (
		} else {
			if(!(code >= 41 && code <= 126)) return;
		}
	
		if(ele.getAttribute("allowedkeys")) {
			var allowed_chars = new RegExp(ele.getAttribute("allowedkeys"));
			if(!allowed_chars.test(character)) { //If a character was entered that is not allowed.
				this.stopEvent(e);
			}
		}
	},
	//See if the match is made - this is called on every keyup event.
	testMatch : function(ele) {
		if(ele.getAttribute("match")) {
			var match_reg = new RegExp(ele.getAttribute("match"));
			if(match_reg.test(ele.value)) this.success(ele);
			else {
				this.failure(ele);
				return false;//Don't continue if it is a faluire
			}
		}
		if(ele.getAttribute("equals")) {
			if(ele.value == eval(ele.getAttribute("equals"))) this.success(ele);//Yes I know, 'evil eval'.
			else {
				this.failure(ele);
				return false;
			}
		}
		if(ele.getAttribute("istrue")) {
			if(eval(ele.getAttribute("istrue"))) this.success(ele);//Again, eval.
			else {
				this.failure(ele);
				return false;
			}
		}
		return true;
	},
	//Show error message on form submission
	attachForm : function(form_id) {
		var ths = this;//Closure
		$(form_id).onsubmit=function(e) {
			if(!e) var e = window.event;
			var ele = this;
			var err = 0;
			for(var i=0,len=ths.elements.length; i<len; i++) {//Go thru all validation elements
				var ele = ths.elements[i];
				if(!ths.testMatch(ele)) {
					err++;
					if(document.getElementById("validation-error-message-"+ele.id)) continue; //Error message there already.
					//Insert an error message span after the input element.
					var msg = ele.getAttribute('errormessage');
					if(!msg) msg = "Error!!";
					
					var error_message = document.createElement("span");
					error_message.className = "validation-error-message";
					error_message.setAttribute("id","validation-error-message-"+ele.id);
					error_message.appendChild(document.createTextNode(msg));

					ele.parentNode.insertBefore(error_message,ele.nextSibling);
				}
			}
			if(err) {//Stop the submit action
				ths.stopEvent(e);
			}
		}
	},
	init : function () {
		var all_elements = document.getElementsByTagName("*");
		var ths = this;
		for(var i=0;ele=all_elements[i],i<all_elements.length;i++) {
			if(!ele.className.match(new RegExp('(\\s|^)live\-validate(\\s|$)'))) continue;
			this.elements.push(ele);
			//Attach the keyup event to the function. We are doing this in a round-about way because we need the 'this' keyword functionality
			if(ele.getAttribute("allowedkeys")) ele.onkeypress = function(e) {
				if(!e) var e = window.event;
				ths.validateKeys(e,this);
			}

			if(ele.getAttribute("match") || ele.getAttribute("equals") || ele.getAttribute("istrue")) {
				this.testMatch(ele);//The first mach should happen at page load
				ele.onkeyup = function() {
					ths.testMatch(this);
				}
			}
		}
	}
}
