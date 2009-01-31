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
 * LEX_CONTEXT
 * LEX_CLEAR
 * LEX_INSERT
 * LEX_REMOVE
 * LEX_ON
 *
 */

// Complain about martian char.
function lexError1 ($state, $text, $pos, $ln = 'N/A')
{
	return array
	(
		'result' => 'NAK',
		'load' => "Invalid character '" . mb_substr ($text, $pos, 1) . "' near line ${ln}"
	);
}

// Complain about martian keyword.
function lexError2 ($word, $ln = 'N/A')
{
	return array
	(
		'result' => 'NAK',
		'load' => "Invalid keyword '${word}' near line ${ln}"
	);
}

// Complain about wrong FSM state.
function lexError3 ($state, $ln = 'N/A')
{
	return array
	(
		'result' => 'NAK',
		'load' => "Lexical error during '${state}' near line ${ln}"
	);
}

function lexError4 ($s, $ln = 'N/A')
{
	return array
	(
		'result' => 'NAK',
		'load' => "Invalid name '${s}' near line ${ln}"
	);
}

// Produce a list of lexems from the given text. Possible lexems are:
function getLexemsFromRackCode ($text)
{
	$ret = array();
	$textlen = mb_strlen ($text);
	$lineno = 1;
	$state = "ESOTSM";
	for ($i = 0; $i < $textlen; $i++) :
		$char = mb_substr ($text, $i, 1);
		$newstate = $state;
		switch ($state) :
			case 'ESOTSM':
				switch (TRUE)
				{
					case ($char == '('):
						$ret[] = array ('type' => 'LEX_LBRACE', 'lineno' => $lineno);
						break;
					case ($char == ')'):
						$ret[] = array ('type' => 'LEX_RBRACE', 'lineno' => $lineno);
						break;
					case ($char == '#'):
						$newstate = 'skipping comment';
						break;
					case (mb_ereg ('[[:alpha:]]', $char) > 0):
						$newstate = 'reading keyword';
						$buffer = $char;
						break;
					case ($char == "\n"):
						$lineno++; // fall through
					case ($char == " "):
					case ($char == "\t"):
						// nom-nom...
						break;
					case ($char == '{'):
						$newstate = 'reading tag 1';
						break;
					case ($char == '['):
						$newstate = 'reading predicate 1';
						break;
					default:
						return lexError1 ($state, $text, $i, $lineno);
				}
				break;
			case 'reading keyword':
				switch (TRUE)
				{
					case (mb_ereg ('[[:alpha:]]', $char) > 0):
						$buffer .= $char;
						break;
					case ($char == "\n"):
						$lineno++; // fall through
					case ($char == " "):
					case ($char == "\t"):
					case ($char == ')'): // this will be handled below
						// got a word, sort it out
						switch ($buffer)
						{
							case 'allow':
							case 'deny':
								$ret[] = array ('type' => 'LEX_DECISION', 'load' => $buffer, 'lineno' => $lineno);
								break;
							case 'define':
								$ret[] = array ('type' => 'LEX_DEFINE', 'lineno' => $lineno);
								break;
							case 'and':
							case 'or':
								$ret[] = array ('type' => 'LEX_BOOLOP', 'load' => $buffer, 'lineno' => $lineno);
								break;
							case 'not':
								$ret[] = array ('type' => 'LEX_NOT', 'lineno' => $lineno);
								break;
							case 'false':
							case 'true':
								$ret[] = array ('type' => 'LEX_BOOLCONST', 'load' => $buffer, 'lineno' => $lineno);
								break;
							case 'context':
								$ret[] = array ('type' => 'LEX_CONTEXT', 'lineno' => $lineno);
								break;
							case 'clear':
								$ret[] = array ('type' => 'LEX_CLEAR', 'lineno' => $lineno);
								break;
							case 'insert':
								$ret[] = array ('type' => 'LEX_INSERT', 'lineno' => $lineno);
								break;
							case 'remove':
								$ret[] = array ('type' => 'LEX_REMOVE', 'lineno' => $lineno);
								break;
							case 'on':
								$ret[] = array ('type' => 'LEX_ON', 'lineno' => $lineno);
								break;
							default:
								return lexError2 ($buffer, $lineno);
						}
						if ($char == ')')
							$ret[] = array ('type' => 'LEX_RBRACE', 'lineno' => $lineno);
						$newstate = 'ESOTSM';
						break;
					default:
						return lexError1 ($state, $text, $i, $lineno);
				}
				break;
			case 'reading tag 1':
				switch (TRUE)
				{
					case ($char == "\n"):
						$lineno++; // fall through
					case ($char == " "):
					case ($char == "\t"):
						// nom-nom...
						break;
					case (mb_ereg ('[[:alnum:]\$]', $char) > 0):
						$buffer = $char;
						$newstate = 'reading tag 2';
						break;
					default:
						return lexError1 ($state, $text, $i, $lineno);
				}
				break;
			case 'reading tag 2':
				switch (TRUE)
				{
					case ($char == '}'):
						$buffer = rtrim ($buffer);
						if (!validTagName ($buffer, TRUE))
							return lexError4 ($buffer, $lineno);
						$ret[] = array ('type' => 'LEX_TAG', 'load' => $buffer, 'lineno' => $lineno);
						$newstate = 'ESOTSM';
						break;
					case (mb_ereg ('[[:alnum:]\. _~-]', $char) > 0):
						$buffer .= $char;
						break;
					default:
						return lexError1 ($state, $text, $i, $lineno);
				}
				break;
			case 'reading predicate 1':
				switch (TRUE)
				{
					case (preg_match ('/^[ \t\n]$/', $char)):
						// nom-nom...
						break;
					case (mb_ereg ('[[:alnum:]]', $char) > 0):
						$buffer = $char;
						$newstate = 'reading predicate 2';
						break;
					default:
						return lexError1 ($state, $text, $i, $lineno);
				}
				break;
			case 'reading predicate 2':
				switch (TRUE)
				{
					case ($char == ']'):
						$buffer = rtrim ($buffer);
						if (!validTagName ($buffer))
							return lexError4 ($buffer, $lineno);
						$ret[] = array ('type' => 'LEX_PREDICATE', 'load' => $buffer, 'lineno' => $lineno);
						$newstate = 'ESOTSM';
						break;
					case (mb_ereg ('[[:alnum:]\. _~-]', $char) > 0):
						$buffer .= $char;
						break;
					default:
						return lexError1 ($state, $text, $i, $lineno);
				}
				break;
			case 'skipping comment':
				switch ($char)
				{
					case "\n":
						$lineno++;
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
		return lexError3 ($state, $lineno);
	return array ('result' => 'ACK', 'load' => $ret);
}

// Parse the given lexems stream into a list of RackCode sentences. Each such
// sentence is a syntax tree, suitable for tag sequence evaluation. The final
// parse tree may contain the following nodes:
// LEX_TAG
// LEX_PREDICATE
// LEX_BOOLCONST
// SYNT_NOTEXPR (1 argument, holding SYNT_EXPR)
// SYNT_BOOLOP (2 arguments, each holding SYNT_EXPR)
// SYNT_DEFINITION (2 arguments: term and definition)
// SYNT_GRANT (2 arguments: decision and condition)
// SYNT_ADJUSTMENT (context modifier with action(s) and condition)
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
	// symbol, SYNT_CODETEXT). When reducing, set the "line number" of
	// the reduction result to the line number of the "latest" item of the
	// reduction base (the one on the stack top). This will help locating
	// parse errors, if any.
	while (TRUE)
	{
		$stacktop = $stacksecondtop = $stackthirdtop = $stackfourthtop = array ('type' => 'null');
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
					if ($stacksize >= 4)
					{
						$stackfourthtop = array_pop ($stack);
						array_push ($stack, $stackfourthtop);
					}
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
				// reduce!
				array_pop ($stack);
				array_pop ($stack);
				array_push
				(
					$stack,
					array
					(
						'type' => 'SYNT_DEFINE',
						'lineno' => $stacktop['lineno'],
						'load' => $stacktop['load']
					)
				);
				continue;
			}
			if
			(
				$stacktop['type'] == 'LEX_CLEAR'
			)
			{
				// reduce!
				array_pop ($stack);
				array_push
				(
					$stack,
					array
					(
						'type' => 'SYNT_CTXMOD',
						'lineno' => $stacktop['lineno'],
						'load' => array ('op' => 'clear')
					)
				);
				continue;
			}
			if
			(
				$stacktop['type'] == 'LEX_TAG' and
				$stacksecondtop['type'] == 'LEX_INSERT'
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
						'type' => 'SYNT_CTXMOD',
						'lineno' => $stacktop['lineno'],
						'load' => array ('op' => 'insert', 'tag' => $stacktop['load'])
					)
				);
				continue;
			}
			if
			(
				$stacktop['type'] == 'LEX_TAG' and
				$stacksecondtop['type'] == 'LEX_REMOVE'
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
						'type' => 'SYNT_CTXMOD',
						'lineno' => $stacktop['lineno'],
						'load' => array ('op' => 'remove', 'tag' => $stacktop['load'])
					)
				);
				continue;
			}
			if
			(
				$stacktop['type'] == 'SYNT_CTXMOD' and
				$stacksecondtop['type'] == 'SYNT_CTXMODLIST'
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
						'type' => 'SYNT_CTXMODLIST',
						'lineno' => $stacktop['lineno'],
						'load' => array_merge ($stacksecondtop['load'], array ($stacktop['load']))
					)
				);
				continue;
			}
			if
			(
				$stacktop['type'] == 'SYNT_CTXMOD'
			)
			{
				// reduce!
				array_pop ($stack);
				array_push
				(
					$stack,
					array
					(
						'type' => 'SYNT_CTXMODLIST',
						'lineno' => $stacktop['lineno'],
						'load' => array_merge ($stacktop['load'])
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
						'lineno' => $stacktop['lineno'],
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
						'lineno' => $stacktop['lineno'],
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
				$stacksecondtop['lineno'] = $stacktop['lineno'];
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
						'lineno' => $stacktop['lineno'],
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
						'lineno' => $stacktop['lineno'],
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
						'lineno' => $stacktop['lineno'],
						'term' => $stacksecondtop['load'],
						'definition' => $stacktop['load']
					)
				);
				continue;
			}
			if
			(
				$stacktop['type'] == 'SYNT_EXPR' and
				$stacksecondtop['type'] == 'LEX_ON' and
				$stackthirdtop['type'] == 'SYNT_CTXMODLIST' and
				$stackfourthtop['type'] == 'LEX_CONTEXT'
			)
			{
				// reduce!
				array_pop ($stack);
				array_pop ($stack);
				array_pop ($stack);
				array_pop ($stack);
				array_push
				(
					$stack,
					array
					(
						'type' => 'SYNT_ADJUSTMENT',
						'lineno' => $stacktop['lineno'],
						'modlist' => $stackthirdtop['load'],
						'condition' => $stacktop['load']
					)
				);
				continue;
			}
			if
			(
				($stacktop['type'] == 'SYNT_GRANT' or $stacktop['type'] == 'SYNT_DEFINITION' or $stacktop['type'] == 'SYNT_ADJUSTMENT') and
				$stacksecondtop['type'] == 'SYNT_CODETEXT'
			)
			{
				// reduce!
				array_pop ($stack);
				array_pop ($stack);
				$stacksecondtop['load'][] = $stacktop;
				$stacksecondtop['lineno'] = $stacktop['lineno'];
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
				$stacktop['type'] == 'SYNT_DEFINITION' or
				$stacktop['type'] == 'SYNT_ADJUSTMENT'
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
						'lineno' => $stacktop['lineno'],
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
		// No luck. Prepare to complain.
		if ($lineno = locateSyntaxError ($stack))
			return array ('result' => 'NAK', 'load' => 'Syntax error near line ' . $lineno);
		// HCF
		return array ('result' => 'NAK', 'load' => 'Syntax error: empty text');
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

// Do adjustment: execute request list, return TRUE on any changes done.
function processAdjustmentSentence ($modlist, &$chain)
{
	$ret = FALSE;
	foreach ($modlist as $mod)
		switch ($mod['op'])
		{
			case 'insert':
				break;
			case 'remove':
				break;
			case 'clear':
				$ret = TRUE;
				break;
			default: // HCF
				showError ('Internal error', __FUNCTION__);
				die;
		}
	return $ret;
}

// The argument doesn't include explicit and implicit tags. This allows us to derive implicit chain
// each time we modify the given argument (and work with the modified copy from now on).
// After the work is done the global $impl_tags is silently modified
function gotClearanceForTagChain ($const_base)
{
	global $rackCode, $expl_tags, $impl_tags;
	$ptable = array();
	foreach ($rackCode as $sentence)
	{
		switch ($sentence['type'])
		{
			case 'SYNT_DEFINITION':
				$ptable[$sentence['term']] = $sentence['definition'];
				break;
			case 'SYNT_GRANT':
				if (eval_expression ($sentence['condition'], array_merge ($const_base, $expl_tags, $impl_tags), $ptable))
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
			case 'SYNT_ADJUSTMENT':
				if
				(
					eval_expression ($sentence['condition'], array_merge ($const_base, $expl_tags, $impl_tags), $ptable) and
					processAdjustmentSentence ($sentence['modlist'], $expl_tags)
				) // recalculate implicit chain only after actual change, not just on matched condition
					$impl_tags = getImplicitTags ($expl_tags); // recalculate
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
			case 'SYNT_ADJUSTMENT':
				// Only condition part gets tested, because it's normal to set (or even to unset)
				// something, that's not set.
				$up = firstUnrefPredicate ($predicatelist, $sentence['condition']);
				if ($up !== NULL)
					return array
					(
						'result' => 'NAK',
						'load' => "adjustment sentence uses unknown predicate [${up}]"
					);
				break;
			default:
				return array ('result' => 'NAK', 'load' => 'unknown sentence type');
		}
	return array ('result' => 'ACK', 'load' => $code);
}

// Accept a stack and figure out the cause of it not being parsed into a tree.
// Return the line number or zero.
function locateSyntaxError ($stack)
{
	// The first SYNT_CODETEXT node, if is present, holds stuff already
	// successfully processed. Its line counter shows, where the last reduction
	// took place (it _might_ be the same line, which causes the syntax error).
	// The next node (it's very likely to exist) should have its line counter
	// pointing to the place, where the first (of 1 or more) error is located.
	if (isset ($stack[0]['type']) and $stack[0]['type'] == 'SYNT_CODETEXT')
		unset ($stack[0]);
	foreach ($stack as $node)
		// Satisfy with the first line number met.
		if (isset ($node['lineno']))
			return $node['lineno'];
	return 0;
}

function refRCLineno ($ln)
{
	global $root;
	return "<a href='${root}?page=perms&tab=default#line${ln}'>line ${ln}</a>";
}

function getRackCodeWarnings ()
{
	$ret = array();
	global $rackCode;
	// tags
	foreach ($rackCode as $sentence)
		switch ($sentence['type'])
		{
			case 'SYNT_DEFINITION':
				$ret = array_merge ($ret, findTagWarnings ($sentence['definition']));
				break;
			case 'SYNT_GRANT':
			case 'SYNT_ADJUSTMENT':
				$ret = array_merge ($ret, findTagWarnings ($sentence['condition']));
				break;
			default:
				$ret[] = array
				(
					'header' => 'internal error',
					'class' => 'error',
					'text' => "Skipped sentence of unknown type '${sentence['type']}'"
				);
		}
	// autotags
	foreach ($rackCode as $sentence)
		switch ($sentence['type'])
		{
			case 'SYNT_DEFINITION':
				$ret = array_merge ($ret, findAutoTagWarnings ($sentence['definition']));
				break;
			case 'SYNT_GRANT':
			case 'SYNT_ADJUSTMENT':
				$ret = array_merge ($ret, findAutoTagWarnings ($sentence['condition']));
				break;
			default:
				$ret[] = array
				(
					'header' => 'internal error',
					'class' => 'error',
					'text' => "Skipped sentence of unknown type '${sentence['type']}'"
				);
		}
	// predicates
	$plist = array();
	foreach ($rackCode as $sentence)
		if ($sentence['type'] == 'SYNT_DEFINITION')
			$plist[$sentence['term']] = $sentence['lineno'];
	foreach ($plist as $pname => $lineno)
	{
		foreach ($rackCode as $sentence)
			switch ($sentence['type'])
			{
				case 'SYNT_DEFINITION':
					if (referencedPredicate ($pname, $sentence['definition']))
						continue 3; // clear, next term
					break;
				case 'SYNT_GRANT':
				case 'SYNT_ADJUSTMENT':
					if (referencedPredicate ($pname, $sentence['condition']))
						continue 3; // idem
					break;
			}
		$ret[] = array
		(
			'header' => refRCLineno ($lineno),
			'class' => 'warning',
			'text' => "Predicate '${pname}' is defined, but never used."
		);
	}
	// expressions
	foreach ($rackCode as $sentence)
		switch (invariantExpression ($sentence))
		{
			case 'always true':
				$ret[] = array
				(
					'header' => refRCLineno ($sentence['lineno']),
					'class' => 'warning',
					'text' => "Expression is always true."
				);
				break;
			case 'always false':
				$ret[] = array
				(
					'header' => refRCLineno ($sentence['lineno']),
					'class' => 'warning',
					'text' => "Expression is always false."
				);
				break;
			default:
				break;
		}
	// Duplicates. Use the same hash function we used to.
	$fpcache = array ('SYNT_DEFINITION' => array(), 'SYNT_GRANT' => array());
	foreach ($rackCode as $sentence)
		switch ($sentence['type'])
		{
			case 'SYNT_DEFINITION':
				$fp = hash (PASSWORD_HASH, serialize (effectiveValue ($sentence['definition'])));
				if (isset ($fpcache[$sentence['type']][$fp]))
					$ret[] = array
					(
						'header' => refRCLineno ($sentence['lineno']),
						'class' => 'warning',
						'text' => 'Effective definition equals that at line ' . $fpcache[$sentence['type']][$fp]
					);
				$fpcache[$sentence['type']][$fp] = $sentence['lineno'];
				break;
			case 'SYNT_GRANT':
				$fp = hash (PASSWORD_HASH, serialize (effectiveValue ($sentence['condition'])));
				if (isset ($fpcache[$sentence['type']][$fp]))
					$ret[] = array
					(
						'header' => refRCLineno ($sentence['lineno']),
						'class' => 'warning',
						'text' => 'Effective condition equals that at line ' . $fpcache[$sentence['type']][$fp]
					);
				$fpcache[$sentence['type']][$fp] = $sentence['lineno'];
				break;
		}
	// bail out
	$nwarnings = count ($ret);
	$ret[] = array
	(
		'header' => 'summary',
		'class' => $nwarnings ? 'error' : 'success',
		'text' => "Analysis complete, ${nwarnings} issues discovered."
	);
	return $ret;
}

// Scan the given expression and return any issues found about its autotags.
function findAutoTagWarnings ($expr)
{
	switch ($expr['type'])
	{
		case 'LEX_BOOLCONST':
		case 'LEX_PREDICATE':
			return array();
		case 'LEX_TAG':
			switch (TRUE)
			{
				case (mb_ereg_match ('^\$id_', $expr['load'])):
					$recid = mb_ereg_replace ('^\$id_', '', $expr['load']);
					if (recordExists ($recid, 'object'))
						return array();
					return array (array
					(
						'header' => refRCLineno ($expr['lineno']),
						'class' => 'warning',
						'text' => "An object with ID '${recid}' does not exist."
					));
				case (mb_ereg_match ('^\$ipv4netid_', $expr['load'])):
					$recid = mb_ereg_replace ('^\$ipv4netid_', '', $expr['load']);
					if (recordExists ($recid, 'ipv4net'))
						return array();
					return array (array
					(
						'header' => refRCLineno ($expr['lineno']),
						'class' => 'warning',
						'text' => "IPv4 network with ID '${recid}' does not exist."
					));
				case (mb_ereg_match ('^\$userid_', $expr['load'])):
					$recid = mb_ereg_replace ('^\$userid_', '', $expr['load']);
					if (recordExists ($recid, 'user'))
						return array();
					return array (array
					(
						'header' => refRCLineno ($expr['lineno']),
						'class' => 'warning',
						'text' => "User account with ID '${recid}' does not exist."
					));
				case (mb_ereg_match ('^\$username_', $expr['load'])):
					global $accounts;
					$recid = mb_ereg_replace ('^\$username_', '', $expr['load']);
					if (isset ($accounts[$recid]))
						return array();
					return array (array
					(
						'header' => refRCLineno ($expr['lineno']),
						'class' => 'warning',
						'text' => "User account with name '${recid}' does not exist."
					));
				default:
					return array();
			}
		case 'SYNT_NOTEXPR':
			return findAutoTagWarnings ($expr['load']);
		case 'SYNT_BOOLOP':
			return array_merge
			(
				findAutoTagWarnings ($expr['left']),
				findAutoTagWarnings ($expr['right'])
			);
		default:
			return array (array
			(
				'header' => 'internal error',
				'class' => 'error',
				'text' => "Skipped expression of unknown type '${expr['type']}'"
			));
	}
}

// Idem WRT tags.
function findTagWarnings ($expr)
{
	global $taglist;
	switch ($expr['type'])
	{
		case 'LEX_BOOLCONST':
		case 'LEX_PREDICATE':
			return array();
		case 'LEX_TAG':
			// Only verify stuff, that has passed through the saving handler.
			if (!mb_ereg_match (TAGNAME_REGEXP, $expr['load']))
				return array();
			foreach ($taglist as $taginfo)
				if ($taginfo['tag'] == $expr['load'])
					return array();
			return array (array
			(
				'header' => refRCLineno ($expr['lineno']),
				'class' => 'warning',
				'text' => "Tag '${expr['load']}' does not exist."
			));
		case 'SYNT_NOTEXPR':
			return findTagWarnings ($expr['load']);
		case 'SYNT_BOOLOP':
			return array_merge
			(
				findTagWarnings ($expr['left']),
				findTagWarnings ($expr['right'])
			);
		default:
			return array (array
			(
				'header' => 'internal error',
				'class' => 'error',
				'text' => "Skipped expression of unknown type '${expr['type']}'"
			));
	}
}

// Return true, if the expression makes use of the predicate given.
function referencedPredicate ($pname, $expr)
{
	switch ($expr['type'])
	{
		case 'LEX_BOOLCONST':
		case 'LEX_TAG':
			return FALSE;
		case 'LEX_PREDICATE':
			return $pname == $expr['load'];
		case 'SYNT_NOTEXPR':
			return referencedPredicate ($pname, $expr['load']);
		case 'SYNT_BOOLOP':
			return referencedPredicate ($pname, $expr['left']) or referencedPredicate ($pname, $expr['right']);
		default: // This is actually an internal error.
			return FALSE;
	}
}

// Return 'always true', 'always false' or any other verdict.
function invariantExpression ($expr)
{
	$self = __FUNCTION__;
	switch ($expr['type'])
	{
		case 'SYNT_GRANT':
			return $self ($expr['condition']);
		case 'SYNT_DEFINITION':
			return $self ($expr['definition']);
		case 'LEX_BOOLCONST':
			if ($expr['load'] == 'true')
				return 'always true';
			return 'always false';
		case 'LEX_TAG':
		case 'LEX_PREDICATE':
			return 'sometimes something';
		case 'SYNT_NOTEXPR':
			return $self ($expr['load']);
		case 'SYNT_BOOLOP':
			$leftanswer = $self ($expr['left']);
			$rightanswer = $self ($expr['right']);
			// "true or anything" is always true and thus const
			if ($expr['subtype'] == 'or' and ($leftanswer == 'always true' or $rightanswer == 'always true'))
				return 'always true';
			// "false and anything" is always false and thus const
			if ($expr['subtype'] == 'and' and ($leftanswer == 'always false' or $rightanswer == 'always false'))
				return 'always false';
			// "true and true" is true
			if ($expr['subtype'] == 'and' and ($leftanswer == 'always true' and $rightanswer == 'always true'))
				return 'always true';
			// "false or false" is false
			if ($expr['subtype'] == 'or' and ($leftanswer == 'always false' and $rightanswer == 'always false'))
				return 'always false';
			return '';
		default: // This is actually an internal error.
			break;
	}
}

// FIXME: First do line number stripping, otherwise hashes will always differ even
// for the obviously equivalent expressions. In some longer term mean transform the
// expression into minimalistic and ordered form.
function effectiveValue ($expr)
{
	return $expr;
}

?>
