(function(mod) {
  if (typeof exports == "object" && typeof module == "object") // CommonJS
    mod(require("../../lib/codemirror"));
  else if (typeof define == "function" && define.amd) // AMD
    define(["../../lib/codemirror"], mod);
  else // Plain browser env
    mod(CodeMirror);
})(function(CodeMirror) {
"use strict";
CodeMirror.defineMode('rackcode', function()
{
	return {
		token: function (stream)
		{
			const WORDS =
			{
				'allow':   'keyword positive',
				'deny':    'keyword negative',
				'define':  'keyword',
				'context': 'keyword',
				'clear':   'keyword',
				'insert':  'keyword',
				'remove':  'keyword',
				'on':      'keyword',
				'true':    'atom',
				'false':   'atom',
				'and':     'operator',
				'or':      'operator',
				'not':     'operator',
			};
			return stream.eatSpace() ? null :
				stream.eat ('(') ? 'bracket' :
				stream.eat (')') ? 'bracket' :
				stream.match (/^#.*$/) ? 'comment' :
				stream.match (/^{[^{}]+}/) ? 'variable' : // a tag
				stream.match (/^\[[^\[\]]+\]/) ? 'def' : // a predicate
				stream.eatWhile (/\S/) ? WORDS[stream.current()] :
				null;
		}
	};
});

CodeMirror.defineMIME("text/x-rackcode", "rackcode");
});
