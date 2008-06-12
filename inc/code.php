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
						$buffer = rtrim ($buffer);
						if (!preg_match ('/^[a-zA-Z0-9]$/', substr ($buffer, -1)))
							abortLex1 ($state, $text, $i);
						$ret[] = array ('type' => 'LEX_TAG', 'load' => $buffer);
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
						$buffer = rtrim ($buffer);
						if (!preg_match ('/^[a-zA-Z0-9]$/', substr ($buffer, -1)))
							abortLex1 ($state, $text, $i);
						$ret[] = array ('type' => 'LEX_PREDICATE', 'load' => $buffer);
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
// sentence is a syntax tree, suitable for tag sequence evaluation. It will
// contain all of the input lexems framed into a parse tree built from the
// following nodes:
// SYNT_NOT (1 argument, holding SYNT_EXPR)
// SYNT_BOOLOP (2 arguments, each holding SYNT_EXPR)
// SYNT_EXPR (1 argument, different types)
// SYNT_DEFINE (keyword with 1 term)
// SYNT_DEFINITION (2 arguments: term and definition)
// SYNT_GRANT (2 arguments: decision and condition)
// SYNT_CODESENTENCE (either a grant or a definition)
// SYNT_CODETEXT (sequence of sentences)
function getSentencesFromLexems ($lexems)
{
	$stack = array(); // subject to array_push() and array_pop()
	$done = 0; // $lexems[$done] is the next item in the tape
	$todo = count ($lexems);

	// Perform shift-reduce processing. The "accept" actions occurs with an
	// empty input tape and the stack holding only one symbol (the start
	// symbol, SYNT_CODETEXT).
	while (TRUE)
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
			if
			(
				$stacktop['type'] == 'SYNT_EXPR' and
				($done < $todo) and 
				$lexems[$done]['type'] == 'LEX_BOOLOP'
			)
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
			// First detect definition start to save the predicate from being reduced into
			// expression.
			if
			(
				$stacktop['type'] == 'LEX_PREDICATE' and
				$stacksecondtop['type'] == 'LEX_DEFINE'
			)
			{
				array_pop ($stack);
				array_pop ($stack);
				array_push
				(
					$stack,
					array
					(
						'type' => 'SYNT_DEFINE',
						'load' => $stacktop['load']
					)
				);
				continue;
			}
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
				// reduce!
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
				// reduce!
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
				// reduce!
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
				// reduce!
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
			if
			(
				$stacktop['type'] == 'SYNT_EXPR' and
				$stacksecondtop['type'] == 'LEX_DECISION'
			)
			{
				// reduce!
				array_pop ($stack);
				array_pop ($stack);
				array_push
				(
					$stack,
					array
					(
						'type' => 'SYNT_GRANT',
						'decision' => $stacksecondtop['load'],
						'condition' => $stacktop
					)
				);
				continue;
			}
			if
			(
				$stacktop['type'] == 'SYNT_EXPR' and
				$stacksecondtop['type'] == 'SYNT_DEFINE'
			)
			{
				// reduce!
				array_pop ($stack);
				array_pop ($stack);
				array_push
				(
					$stack,
					array
					(
						'type' => 'SYNT_DEFINITION',
						'term' => $stacksecondtop,
						'definition' => $stacktop
					)
				);
				continue;
			}
			if
			(
				$stacktop['type'] == 'SYNT_GRANT' or
				$stacktop['type'] == 'SYNT_DEFINITION'
			)
			{
				// reduce!
				array_pop ($stack);
				array_push
				(
					$stack,
					array
					(
						'type' => 'SYNT_CODESENTENCE',
						'load' => $stacktop
					)
				);
				continue;
			}
			if
			(
				$stacktop['type'] == 'SYNT_CODESENTENCE' and
				$stacksecondtop['type'] == 'SYNT_CODETEXT'
			)
			{
				// reduce!
				array_pop ($stack);
				array_pop ($stack);
				$stacksecondtop['load'][] = $stacktop;
				array_push
				(
					$stack,
					$stacksecondtop
				);
				continue;
			}
			if
			(
				$stacktop['type'] == 'SYNT_CODESENTENCE'
			)
			{
				// reduce!
				array_pop ($stack);
				$stacksecondtop['log'][] = $stacktop;
				array_push
				(
					$stack,
					array
					(
						'type' => 'SYNT_CODETEXT',
						'load' => array ($stacktop)
					)
				);
				continue;
			}
		}
		// The fact we execute here means, that no reduction or early shift
		// has been done. The only way to enter another iteration is to "shift"
		// more, if possible. If the tape is empty, we are facing the
		// "accept"/"reject" dilemma. The only possible way to "accept" is to
		// have sole starting nonterminal on the stack (SYNT_CODETEXT).
		if ($done < $todo)
		{
			array_push ($stack, $lexems[$done++]);
			continue;
		}
		// The moment of truth.
		if (count ($stack) == 1 and $stack[0]['type'] == 'SYNT_CODETEXT')
			return $stack[0];
		return NULL;
	}
}

?>
