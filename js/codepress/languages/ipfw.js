Language.snippets = []
Language.complete = []
Language.shortcuts = []
Language.syntax = [
	{ input : /#(.*?)(<br>|<\/P>)/g, output : '<i>#$1</i>$2' }, // comments
	{ input : /\b(add|deny|or|to|from|out|in|ip|tcp|udp|icmp|any)\b/g, output : '<b>$1</b>'}, // keywords
	{ input : /\b(deny|reject)\b/g, output : '<s>$1</s>'}, // keywords
	{ input : /\b(allow|pass)\b/g, output : '<u>$1</u>'} // keywords
]
