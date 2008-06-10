<?php

// Complain about martian char.
function abortLex1 ($state, $text, $pos)
{
	echo "Error! Could not parse the following text (current state is '${state}'): ";
	echo substr ($text, 0, $pos);
	echo '<font color = red>-&gt;' . $text{$pos} . '&lt;-</font>';
	echo substr ($text, $pos + 1);
	die;
}

// Complain about martian keyword.
function abortLex2 ($state, $word)
{
	echo "Error! Could not parse word (current state is '${state}'): '${word}'";
	die;
}

// Produce a list of lexems from the given text.
function getLexFromCodetext ($text)
{
	$ret = array();
	$textlen = strlen ($text);
	$state = "ESOTSM";
	for ($i = 0; $i < $textlen; $i++) :
		$char = $text{$i};
		$newstate = $state;
		switch ($state) :
			case 'ESOTSM':
				switch (TRUE)
				{
					case ($char == ';'):
						$ret[] = array ('lexem' => 'SEMICOLON');
						break;
					case ($char == '('):
						$ret[] = array ('lexem' => 'LBRACE');
						break;
					case ($char == ')'):
						$ret[] = array ('lexem' => 'RBRACE');
						break;
					case ($char == '#'):
						$newstate = 'skipping comment';
						break;
					case (preg_match ('/^[a-zA-Z]$/', $char)):
						$newstate = 'reading word';
						$buffer = $char;
						break;
					case (preg_match ('/^[ \t\n]$/', $char)):
						// nom-nom...
						break;
					case ($char == '{'):
						$newstate = 'reading tag 1';
						break;
					case ($char == '['):
						$newstate = 'reading predicate 1';
						break;
					default:
						abortLex1 ($state, $text, $i);
				}
				break;
			case 'reading word':
				switch (TRUE)
				{
					case (preg_match ('/^[a-zA-Z]$/', $char)):
						$buffer .= $char;
						break;
					case (preg_match ('/^[ \t\n]$/', $char)):
						// got a word, sort it out
						switch ($buffer)
						{
							case 'allow':
								$ret[] = array ('lexem' => 'ALLOW');
								break;
							case 'deny':
								$ret[] = array ('lexem' => 'DENY');
								break;
							case 'define':
								$ret[] = array ('lexem' => 'DEFINE');
								break;
							case 'and':
								$ret[] = array ('lexem' => 'AND');
								break;
							case 'or':
								$ret[] = array ('lexem' => 'OR');
								break;
							default:
								abortLex2 ($state, $buffer);
						}
						$newstate = 'ESOTSM';
						break;
					default:
						abortLex1 ($state, $text, $i);
				}
				break;
			case 'reading tag 1':
				switch (TRUE)
				{
					case (preg_match ('/^[ \t\n]$/', $char)):
						// nom-nom...
						break;
					case (preg_match ('/^[a-zA-Z\$]$/', $char)):
						$buffer = $char;
						$newstate = 'reading tag 2';
						break;
					default:
						abortLex1 ($state, $text, $i);
				}
				break;
			case 'reading tag 2':
				switch (TRUE)
				{
					case ($char == '}'):
						$ret[] = array ('lexem' => 'TAG', 'load' => rtrim ($buffer));
						$newstate = 'ESOTSM';
						break;
					case (preg_match ('/^[a-zA-Z0-9 _-]$/', $char)):
						$buffer .= $char;
						break;
					default:
						abortLex1 ($state, $text, $i);
				}
				break;
			case 'reading predicate 1':
				switch (TRUE)
				{
					case (preg_match ('/^[ \t\n]$/', $char)):
						// nom-nom...
						break;
					case (preg_match ('/^[a-zA-Z]$/', $char)):
						$buffer = $char;
						$newstate = 'reading predicate 2';
						break;
					default:
						abortLex1 ($state, $text, $i);
				}
				break;
			case 'reading predicate 2':
				switch (TRUE)
				{
					case ($char == ']'):
						$ret[] = array ('lexem' => 'PREDICATE', 'load' => rtrim ($buffer));
						$newstate = 'ESOTSM';
						break;
					case (preg_match ('/^[a-zA-Z0-9 _-]$/', $char)):
						$buffer .= $char;
						break;
					default:
						abortLex1 ($state, $text, $i);
				}
				break;
			case 'skipping comment':
				switch ($text{$i})
				{
					case "\n":
						$newstate = 'ESOTSM';
					default: // eat char, nom-nom...
						break;
				}
				break;
			default:
				die (__FUNCTION__ . "(): internal error, state == ${state}");
		endswitch;
		$state = $newstate;
	endfor;
	return $ret;
}

?>
