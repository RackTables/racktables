CodeMirror.defineMode('rackcode', function() {
  var allowkeywords = /^(allow)\b/i;
  var denykeywords = /^(deny)\b/i;
  var contextkeywords = /^(context|clear|insert|remove|on)\b/i;
  var operatorkeywords = /^(define|and|or|not|true|false)\b/i;

  return {
    token: function(stream, state) {

      if (stream.eatSpace())
        return null;

      var w;

      if (stream.eatWhile(/\w/)) {
        w = stream.current();


		if (allowkeywords.test(w)) {
            return 'positive';
		} else if (denykeywords.test(w)) {
            return 'negative';
        } else if (operatorkeywords.test(w)) {
            return 'operator';
        } else if (contextkeywords.test(w)) {
            return 'keyword';
        }

      } else if (stream.eat('#')) {
        stream.skipToEnd();
        return 'comment';
      } else if (stream.eat('{')) {
        while (w = stream.next()) {
          if (w == '}')
            break;

          if (w == '\\')
            stream.next();
        }
        return 'tag';
      }  else {
        stream.next();
      }
      return null;
    }
  };
});

CodeMirror.defineMIME("text/x-rackcode", "rackcode");
