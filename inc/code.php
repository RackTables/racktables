<?php
/*
 * This file implement lexical scanner and syntax analyzer for the RackCode
 * access configuration language.
 *
 * The language consists of the following lexems:
 *
 * LEX_LBRACE
 * LEX_RBRACE
 * LEX_DECISION
 * LEX_DEFINE
 * LEX_BOOLCONST
 * LEX_NOT
 * LEX_TAG
 * LEX_PREDICATE
 * LEX_BOOLOP
 *
 * Comments last from the first # to the end of the line and are filtered out
 * automatically.
 */

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
	echo "Error! Could not parse word (current state is '${state}'): '${word}'.";
	die;
}

// Complain about wrong FSM state.
function abortLex3 ($state)
{
	echo "Error! Lexical scanner final state is still '${state}' after scanning the last char.";
	die;
}

function abortSynt ($lexname)
{
	echo "Error! Unknown lexeme '${lexname}'.";
	die;
}

// Produce a list of lexems from the given text. Possible lexems are:
function getLexemsFromRackCode ($text)
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
					case ($char == '('):
						$ret[] = array ('type' => 'LEX_LBRACE');
						break;
					case ($char == ')'):
						$ret[] = array ('type' => 'LEX_RBRACE');
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
							case 'deny':
								$ret[] = array ('type' => 'LEX_DECISION', 'load' => $buffer);
								break;
							case 'define':
								$ret[] = array ('type' => 'LEX_DEFINE');
								break;
							case 'and':
							case 'or':
								$ret[] = array ('type' => 'LEX_BOOLOP', 'load' => $buffer);
								break;
							case 'not':
								$ret[] = array ('type' => 'LEX_NOT');
								break;
							case 'false':
							case 'true':
								$ret[] = array ('type' => 'LEX_BOOLCONST', 'load' => $buffer);
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
						$ret[] = array ('type' => 'LEX_TAG', 'load' => rtrim ($buffer));
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
						$ret[] = array ('type' => 'LEX_PREDICATE', 'load' => rtrim ($buffer));
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
	if ($state != 'ESOTSM')
		abortLex3 ($state);
	return $ret;
}

function syntReduce_BOOLCONST (&$stack)
{
	if (count ($stack) < 1)
		return FALSE;
	$top = array_pop ($stack);
	if ($top['type'] == 'LEX_BOOLCONST')
	{
		$s = array
		(
			'type' => 'SYNT_EXPRESSION',
			'kids' => array ($top)
		);
		array_push ($stack, $s);
		return TRUE;
	}
	// No luck, push it back.
	array_push ($stack, $top);
	return FALSE;
}

// Parse the given lexems stream into a list of RackCode sentences. Each such
// sentence is a syntax tree, suitable for tag sequence evaluation. It may
// contain all of the lexems plus the following syntax contructs:
// SYNT_NOT
// SYNT_EXPRESSION
// SYNT_DEFINITION
// SYNT_GRANT
// SYNT_SENTENCE
// SYNT_CODE
function getSentencesFromLexems ($lexems)
{
	$ret = array(); // This is going to be the tree.
	$stack = array(); // subject to array_push() and array_pop()
	$done = 0; // $lexems[$done] is the next item in the tape
	$todo = count ($lexems);

	// Perform shift-reduce parsing.
	while ($done < $todo)
	{
		$stacktop = $stacksecondtop = $stackthirdtop = array ('type' => 'null');
		$stacksize = count ($stack);
		if ($stacksize >= 1)
		{
			$stacktop = array_pop ($stack);
			// It is possible to run into a S/R conflict, when having a syntaxically
			// correct sentence base on the stack and some "and {something}" items
			// on the input tape, hence let's detect this specific case and insist
			// on "shift" action to make EXPR parsing hungry one.
			if ($stacktop['type'] == 'SYNT_EXPR' and $lexems[$done]['type'] == 'LEX_BOOLOP')
			{
				// shift!
				array_push ($stack, $stacktop);
				array_push ($stack, $lexems[$done++]);
				continue;
			}
			if ($stacksize >= 2)
			{
				$stacksecondtop = array_pop ($stack);
				if ($stacksize >= 3)
				{
					$stackthirdtop = array_pop ($stack);
					array_push ($stack, $stackthirdtop);
				}
				array_push ($stack, $stacksecondtop);
			}
			array_push ($stack, $stacktop);
			// Try "replace" action only on a non-empty stack.
			// If a handle is found for reversing a production rule, do it and start a new
			// cycle instead of advancing further on rule list. This will preserve rule priority
			// in the grammar and keep us from an extra shift action.
			if
			(
				$stacktop['type'] == 'LEX_BOOLCONST' or
				$stacktop['type'] == 'LEX_TAG' or
				$stacktop['type'] == 'LEX_PREDICATE'
			)
			{
				array_pop ($stack);
				array_push
				(
					$stack,
					array
					(
						'type' => 'SYNT_EXPR',
						'load' => $stacktop
					)
				);
				continue;
			}
			if
			(
				$stacktop['type'] == 'SYNT_EXPR' and
				$stacksecondtop['type'] == 'LEX_NOT'
			)
			{
				array_pop ($stack);
				array_pop ($stack);
				array_push
				(
					$stack,
					array
					(
						'type' => 'SYNT_EXPR',
						'load' => array
						(
							'type' => 'SYNT_NOTEXPR',
							'arg' => $stacktop
						)
					)
				);
				continue;
			}
			if
			(
				$stacktop['type'] == 'LEX_RBRACE' and
				$stacksecondtop['type'] == 'SYNT_EXPR' and
				$stackthirdtop['type'] == 'LEX_LBRACE'
			)
			{
				array_pop ($stack);
				array_pop ($stack);
				array_pop ($stack);
				array_push
				(
					$stack,
					$stacksecondtop
				);
				continue;
			}
			if
			(
				$stacktop['type'] == 'SYNT_EXPR' and
				$stacksecondtop['type'] == 'LEX_BOOLOP' and
				$stackthirdtop['type'] == 'SYNT_EXPR'
			)
			{
				array_pop ($stack);
				array_pop ($stack);
				array_pop ($stack);
				array_push
				(
					$stack,
					array
					(
						'type' => 'SYNT_EXPR',
						'load' => array
						(
							'type' => 'SYNT_BOOLOP',
							'subtype' => $stacksecondtop['load'],
							'left' => $stackthirdtop,
							'right' => $stacktop
						)
					)
				);
				continue;
			}
		}
		// We are here because of either an empty stack or none reduction succeeded. Shift!
		array_push ($stack, $lexems[$done++]);
	}
	return $stack;
}

?>
