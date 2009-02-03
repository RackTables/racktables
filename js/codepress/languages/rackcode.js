Language.snippets = []
Language.complete = []
Language.shortcuts = []
Language.syntax = [
	{ input : /{(.*?)(}|<br>|<\/P>)/g, output : '<s>{$1$2</s>'}, // tags
	{ input : /\[(.*?)(\]|<br>|<\/P>)/g, output : '<s>[$1$2</s>'}, // predicatess
	{ input : /\b(define|allow|deny|true|false|and|not|or|context|clear|insert|remove|on)\b/g, output : '<b>$1</b>'}, // keywords
	{ input : /([^:]|^)#(.*?)(<br|<\/P)/g, output : '$1<i>#$2</i>$3'} // comments //	
]
