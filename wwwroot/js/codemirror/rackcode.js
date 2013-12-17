CodeMirror.defineMode('rackcode', function() {
  var allowkeywords = /^(allow)\b/i;
  var denykeywords = /^(deny)\b/i;
  var operatorkeywords = /^(and|or|not)\b/i;

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
