<?php
/*
 * This file implements lexical scanner and syntax analyzer for the RackCode
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
 */

// Complain about martian char.
function lexError1 ($state, $text, $pos)
{
	$message = "invalid char with code " . ord (mb_substr ($text, $pos, 1));
	$message .= " at position ${pos} (FSM state is '${state}')";
	return array ('result' => 'NAK', 'load' => $message);
}

// Complain about martian keyword.
function lexError2 ($word)
{
	return array
	(
		'result' => 'NAK',
		'load' => "could not parse keyword '${word}'"
	);
}

// Complain about wrong FSM state.
function lexError3 ($state)
{
	return array
	(
		'result' => 'NAK',
		'load' => "FSM state is still '${state}' at end of input"
	);
}

function lexError4 ($s)
{
	return array
	(
		'result' => 'NAK',
		'load' => "Invalid tag or predicate name '${s}'"
	);
}

// Produce a list of lexems from the given text. Possible lexems are:
function getLexemsFromRackCode ($text)
{
	$ret = array();
	$textlen = mb_strlen ($text);
	$state = "ESOTSM";
	for ($i = 0; $i < $textlen; $i++) :
		$char = mb_substr ($text, $i, 1);
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
					case (mb_ereg ('[[:alpha:]]', $char) > 0):
						$newstate = 'reading keyword';
						$buffer = $char;
						break;
					case (preg_match ('/^[ \t\n\r]$/', $char)):
						// nom-nom...
						break;
					case ($char == '{'):
						$newstate = 'reading tag 1';
						break;
					case ($char == '['):
						$newstate = 'reading predicate 1';
						break;
					default:
						return lexError1 ($state, $text, $i);
				}
				break;
			case 'reading keyword':
				switch (TRUE)
				{
					case (mb_ereg ('[[:alpha:]]', $char) > 0):
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
								return lexError2 ($buffer);
						}
						$newstate = 'ESOTSM';
						break;
					default:
						return lexError1 ($state, $text, $i);
				}
				break;
			case 'reading tag 1':
				switch (TRUE)
				{
					case (preg_match ('/^[ \t\n\r]$/', $char)):
						// nom-nom...
						break;
					case (mb_ereg ('[[:alnum:]\$]', $char) > 0):
						$buffer = $char;
						$newstate = 'reading tag 2';
						break;
					default:
						return lexError1 ($state, $text, $i);
				}
				break;
			case 'reading tag 2':
				switch (TRUE)
				{
					case ($char == '}'):
						$buffer = rtrim ($buffer);
						if (!validTagName ($buffer, TRUE))
							return lexError4 ($buffer);
						$ret[] = array ('type' => 'LEX_TAG', 'load' => $buffer);
						$newstate = 'ESOTSM';
						break;
					case (mb_ereg ('[[:alnum:]\. _-~]', $char) > 0):
						$buffer .= $char;
						break;
					default:
						return lexError1 ($state, $text, $i);
				}
				break;
			case 'reading predicate 1':
				switch (TRUE)
				{
					case (preg_match ('/^[ \t\n\r]$/', $char)):
						// nom-nom...
						break;
					case (mb_ereg ('[[:alnum:]]', $char) > 0):
						$buffer = $char;
						$newstate = 'reading predicate 2';
						break;
					default:
						return lexError1 ($state, $text, $i);
				}
				break;
			case 'reading predicate 2':
				switch (TRUE)
				{
					case ($char == ']'):
						$buffer = rtrim ($buffer);
						if (!validTagName ($buffer))
							return lexError4 ($buffer);
						$ret[] = array ('type' => 'LEX_PREDICATE', 'load' => $buffer);
						$newstate = 'ESOTSM';
						break;
					case (mb_ereg ('[[:alnum:]\. _-~]', $char) > 0):
						$buffer .= $char;
						break;
					default:
						return lexError1 ($state, $text, $i);
				}
				break;
			case 'skipping comment':
				switch ($char)
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
	if ($state != 'ESOTSM' and $state != 'skipping comment')
		return lexError3 ($state);
	return array ('result' => 'ACK', 'load' => $ret);
}

// Parse the given lexems stream into a list of RackCode sentences. Each such
// sentence is a syntax tree, suitable for tag sequence evaluation. The final
// parse tree may contain the following nodes:
// LEX_TAG
// LEX_PREDICATE
// LEX_BOOLCONST
// SYNT_NOT (1 argument, holding SYNT_EXPR)
// SYNT_BOOLOP (2 arguments, each holding SYNT_EXPR)
// SYNT_DEFINITION (2 arguments: term and definition)
// SYNT_GRANT (2 arguments: decision and condition)
// SYNT_CODETEXT (sequence of sentences)
//
// After parsing the input successfully a list of SYNT_GRANT and SYNT_DEFINITION
// trees is returned.
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
							'load' => $stacktop['load']
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
							'left' => $stackthirdtop['load'],
							'right' => $stacktop['load']
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
						'condition' => $stacktop['load']
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
						'term' => $stacksecondtop['load'],
						'definition' => $stacktop['load']
					)
				);
				continue;
			}
			if
			(
				($stacktop['type'] == 'SYNT_GRANT' or $stacktop['type'] == 'SYNT_DEFINITION') and
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
			return array ('result' => 'ACK', 'load' => $stack[0]['load']);
		return array ('result' => 'NAK', 'load' => 'Syntax error!');
	}
}

function eval_expression ($expr, $tagchain, $ptable)
{
	switch ($expr['type'])
	{
		case 'LEX_TAG': // Return true, if given tag is present on the tag chain.
			foreach ($tagchain as $tagInfo)
				if ($expr['load'] == $tagInfo['tag'])
					return TRUE;
			return FALSE;
		case 'LEX_PREDICATE': // Find given predicate in the symbol table and evaluate it.
			$pname = $expr['load'];
			if (!isset ($ptable[$pname]))
			{
				showError ("Predicate '${pname}' is referenced before declaration", __FUNCTION__);
				return;
			}
			return eval_expression ($ptable[$pname], $tagchain, $ptable);
		case 'LEX_BOOLCONST': // Evaluate a boolean constant.
			switch ($expr['load'])
			{
				case 'true':
					return TRUE;
				case 'false':
					return FALSE;
				default:
					showError ("Could not parse a boolean constant with value '${expr['load']}'", __FUNCTION__);
					return; // should failure be harder?
			}
		case 'SYNT_NOTEXPR':
			return !eval_expression ($expr['load'], $tagchain, $ptable);
		case 'SYNT_BOOLOP':
			$leftresult = eval_expression ($expr['left'], $tagchain, $ptable);
			switch ($expr['subtype'])
			{
				case 'or':
					if ($leftresult)
						return TRUE; // early success
					return eval_expression ($expr['right'], $tagchain, $ptable);
				case 'and':
					if (!$leftresult)
						return FALSE; // early failure
					return eval_expression ($expr['right'], $tagchain, $ptable);
				default:
					showError ("Cannot evaluate unknown boolean operation '${boolop['subtype']}'");
					return;
			}
		default:
			showError ("Evaluation error, cannot process expression type '${expr['type']}'", __FUNCTION__);
			break;
	}
}

function gotClearanceForTagChain ($tagchain)
{
	global $rackCode;
	$ptable = array();
	foreach ($rackCode as $sentence)
	{
		switch ($sentence['type'])
		{
			case 'SYNT_DEFINITION':
				$ptable[$sentence['term']] = $sentence['definition'];
				break;
			case 'SYNT_GRANT':
				if (eval_expression ($sentence['condition'], $tagchain, $ptable))
					switch ($sentence['decision'])
					{
						case 'allow':
							return TRUE;
						case 'deny':
							return FALSE;
						default:
							showError ("Condition match for unknown grant decision '${sentence['decision']}'", __FUNCTION__);
							break;
					}
				break;
			default:
				showError ("Can't process sentence of unknown type '${sentence['type']}'", __FUNCTION__);
				break;
		}
	}
	return FALSE;
}

function getRackCode ($text)
{
	if (!mb_strlen ($text))
		return array ('result' => 'NAK', 'load' => 'The RackCode text was found empty in ' . __FUNCTION__);
	$text = str_replace ("\r", '', $text) . "\n";
	$lex = getLexemsFromRackCode ($text);
	if ($lex['result'] != 'ACK')
		return $lex;
	$synt = getSentencesFromLexems ($lex['load']);
	if ($synt['result'] != 'ACK')
		return $synt;
	// An empty sentence list is semantically valid, yet senseless,
	// so checking intermediate result once more won't hurt.
	if (!count ($synt['load']))
		return array ('result' => 'NAK', 'load' => 'Empty parse tree found in ' . __FUNCTION__);
	return semanticFilter ($synt['load']);
}

// Return NULL, if the given expression can be evaluated against the given
// predicate list. Return the name of the first show stopper otherwise.
function firstUnrefPredicate ($plist, $expr)
{
	switch ($expr['type'])
	{
		case 'LEX_BOOLCONST':
		case 'LEX_TAG':
			return NULL;
		case 'LEX_PREDICATE':
			return in_array ($expr['load'], $plist) ? NULL : $expr['load'];
		case 'SYNT_NOTEXPR':
			return firstUnrefPredicate ($plist, $expr['load']);
		case 'SYNT_BOOLOP':
			if (($tmp = firstUnrefPredicate ($plist, $expr['left'])) !== NULL)
				return $tmp;
			if (($tmp = firstUnrefPredicate ($plist, $expr['right'])) !== NULL)
				return $tmp;
			return NULL;
		default:
			return NULL;
	}
}

function semanticFilter ($code)
{
	$predicatelist = array();
	foreach ($code as $sentence)
		switch ($sentence['type'])
		{
			case 'SYNT_DEFINITION':
				$up = firstUnrefPredicate ($predicatelist, $sentence['definition']);
				if ($up !== NULL)
					return array
					(
						'result' => 'NAK',
						'load' => "definition [${sentence['term']}] uses unknown predicate [${up}]"
					);
				$predicatelist[] = $sentence['term'];
				break;
			case 'SYNT_GRANT':
				$up = firstUnrefPredicate ($predicatelist, $sentence['condition']);
				if ($up !== NULL)
					return array
					(
						'result' => 'NAK',
						'load' => "grant sentence uses unknown predicate [${up}]"
					);
				break;
			default:
				return array ('result' => 'NAK', 'load' => 'unknown sentence type');
		}
	return array ('result' => 'ACK', 'load' => $code);
}

?>
