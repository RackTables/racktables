<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

/*
 * This file implements lexical scanner and syntax analyzer for the
 * RackCode language. These functions are necessary for parsing and
 * analysis, which don't happen at every script invocation, and this
 * file is only included when necessary.
 *
 */

// Complain about martian char.
function lexError1 ($state, $text, $pos, $ln = 'N/A')
{
	$char = mb_substr ($text, $pos, 1);
	switch ($char)
	{
		case "\n":
			$char = '\n';
			break;
		case "\r":
			$char = '\r';
			break;
		case "\t":
			$char = '\t';
			break;
	}
	return array
	(
		'result' => 'NAK',
		'load' => "Invalid character '${char}' near line ${ln}"
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
		'load' => "Lexical error in scanner state '${state}' near line ${ln}"
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

/* Produce a list of lexems (tokens) from the given text. Possible lexems are:
 *
 * LEX_LBRACE
 * LEX_RBRACE
 * LEX_ALLOW
 * LEX_DENY
 * LEX_DEFINE
 * LEX_TRUE
 * LEX_FALSE
 * LEX_NOT
 * LEX_TAG
 * LEX_AUTOTAG
 * LEX_PREDICATE
 * LEX_AND
 * LEX_OR
 * LEX_CONTEXT
 * LEX_CLEAR
 * LEX_INSERT
 * LEX_REMOVE
 * LEX_ON
 *
 * FIXME: would these work better as normal (integer) constants?
 */
function getLexemsFromRawText ($text)
{
	$ret = array();
	// Add a mock character to aid in synchronization with otherwise correct,
	// but short or odd-terminated final lines.
	$text .= ' ';
	$textlen = mb_strlen ($text);
	$lineno = 1;
	$state = 'ESOTSM';
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
					case (preg_match ('/[\p{L}]/u', $char) == 1):
						$newstate = 'reading keyword';
						$buffer = $char;
						break;
					case ($char == "\n"):
					case ($char == ' '):
					case ($char == "\t"):
						// nom-nom...
						break;
					case ($char == '{'):
						$newstate = 'reading tag';
						$buffer = '';
						break;
					case ($char == '['):
						$newstate = 'reading predicate';
						$buffer = '';
						break;
					default:
						return lexError1 ($state, $text, $i, $lineno);
				}
				break;
			case 'reading keyword':
				switch (TRUE)
				{
					case (preg_match ('/[\p{L}]/u', $char) == 1):
						$buffer .= $char;
						break;
					case ($char == "\n"):
					case ($char == ' '):
					case ($char == "\t"):
					case ($char == ')'): // this will be handled below
						// got a word, sort it out
						switch ($buffer)
						{
							case 'allow':
								$ret[] = array ('type' => 'LEX_ALLOW', 'lineno' => $lineno);
								break;
							case 'deny':
								$ret[] = array ('type' => 'LEX_DENY', 'lineno' => $lineno);
								break;
							case 'define':
								$ret[] = array ('type' => 'LEX_DEFINE', 'lineno' => $lineno);
								break;
							case 'and':
								$ret[] = array ('type' => 'LEX_AND', 'lineno' => $lineno);
								break;
							case 'or':
								$ret[] = array ('type' => 'LEX_OR', 'lineno' => $lineno);
								break;
							case 'not':
								$ret[] = array ('type' => 'LEX_NOT', 'lineno' => $lineno);
								break;
							case 'true':
								$ret[] = array ('type' => 'LEX_TRUE', 'lineno' => $lineno);
								break;
							case 'false':
								$ret[] = array ('type' => 'LEX_FALSE', 'lineno' => $lineno);
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
			case 'reading tag':
			case 'reading predicate':
				$tag_mode = ($state == 'reading tag');
				$breaking_char = $tag_mode ? '}' : ']';
				switch ($char)
				{
					case $breaking_char:
						$buffer = trim ($buffer, "\t ");
						if (!validTagName ($buffer, $tag_mode))
							return lexError4 ($buffer, $lineno);
						if ($tag_mode)
							$lex_type = ($buffer[0] == '$' ? 'LEX_AUTOTAG' : 'LEX_TAG');
						else
							$lex_type = 'LEX_PREDICATE';
						$ret[] = array ('type' => $lex_type, 'load' => $buffer, 'lineno' => $lineno);
						$newstate = 'ESOTSM';
						break;
					case "\n":
						return lexError1 ($state, $text, $i, $lineno);
					default:
						$buffer .= $char;
						break;
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
		if ($char == "\n")
			$lineno++;
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
// LEX_AUTOTAG
// LEX_PREDICATE
// LEX_TRUE
// LEX_FALSE
// SYNT_NOT_EXPR (one arg in "load")
// SYNT_AND_EXPR (two args in "left" and "right")
// SYNT_EXPR (idem), in fact it's boolean OR, but we keep the naming for compatibility
// SYNT_DEFINITION (2 args in "term" and "definition")
// SYNT_GRANT (2 args in "decision" and "condition")
// SYNT_ADJUSTMENT (context modifier with action(s) and condition)
// SYNT_CODETEXT (sequence of sentences)
//
// After parsing the input successfully a list of SYNT_GRANT and SYNT_DEFINITION
// trees is returned.
//
// P.S. The above is true for input that is a complete and correct RackCode text.
// Other inputs may produce different combinations of lex/synt structures. Calling
// function must check the parse tree itself.
function getParseTreeFromLexems ($lexems)
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
			// on "shift" action to make SYNT_AND_EXPR parsing hungry.
			// P.S. Same action is taken for SYNT_EXPR (logical-OR) to prevent
			// premature reduction of "condition" for grant/definition/context
			// modifier sentences. The shift tries to be conservative, it advances
			// by only one token on the tape.
			if
			(
				$stacktop['type'] == 'SYNT_AND_EXPR' and $done < $todo and $lexems[$done]['type'] == 'LEX_AND' or
				$stacktop['type'] == 'SYNT_EXPR' and $done < $todo and $lexems[$done]['type'] == 'LEX_OR'
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
			// unary expression.
			// DEFINE ::= define PREDICATE
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
			// CTXMOD ::= clear
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
			// CTXMOD ::= insert TAG
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
						'load' => array ('op' => 'insert', 'tag' => $stacktop['load'], 'lineno' => $stacktop['lineno'])
					)
				);
				continue;
			}
			// CTXMOD ::= remove TAG
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
						'load' => array ('op' => 'remove', 'tag' => $stacktop['load'], 'lineno' => $stacktop['lineno'])
					)
				);
				continue;
			}
			// CTXMODLIST ::= CTXMODLIST CTXMOD
			if
			(
				$stacktop['type'] == 'SYNT_CTXMOD' and
				$stacksecondtop['type'] == 'SYNT_CTXMODLIST'
			)
			{
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
			// CTXMODLIST ::= CTXMOD
			if
			(
				$stacktop['type'] == 'SYNT_CTXMOD'
			)
			{
				array_pop ($stack);
				array_push
				(
					$stack,
					array
					(
						'type' => 'SYNT_CTXMODLIST',
						'lineno' => $stacktop['lineno'],
						'load' => array ($stacktop['load'])
					)
				);
				continue;
			}
			// Try "replace" action only on a non-empty stack.
			// If a handle is found for reversing a production rule, do it and start a new
			// cycle instead of advancing further on rule list. This will preserve rule priority
			// in the grammar and keep us from an extra shift action.
			// UNARY_EXPRESSION ::= true | false | TAG | AUTOTAG | PREDICATE
			if
			(
				$stacktop['type'] == 'LEX_TAG' or // first look for tokens that are most
				$stacktop['type'] == 'LEX_AUTOTAG' or // likely to appear in the text
				$stacktop['type'] == 'LEX_PREDICATE' or // supplied by user
				$stacktop['type'] == 'LEX_TRUE' or
				$stacktop['type'] == 'LEX_FALSE'
			)
			{
				// reduce!
				array_pop ($stack);
				array_push
				(
					$stack,
					array
					(
						'type' => 'SYNT_UNARY_EXPR',
						'lineno' => $stacktop['lineno'],
						'load' => $stacktop
					)
				);
				continue;
			}
			// UNARY_EXPRESSION ::= (EXPRESSION)
			// Useful trick about AND- and OR-expressions is to check, if the
			// node we are reducing contains only 1 argument. In this case
			// discard the wrapper and join the "load" argument into new node directly.
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
					array
					(
						'type' => 'SYNT_UNARY_EXPR',
						'lineno' => $stacksecondtop['lineno'],
						'load' => isset ($stacksecondtop['load']) ? $stacksecondtop['load'] : $stacksecondtop
					)
				);
				continue;
			}
			// UNARY_EXPRESSION ::= not UNARY_EXPRESSION
			if
			(
				$stacktop['type'] == 'SYNT_UNARY_EXPR' and
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
						'type' => 'SYNT_UNARY_EXPR',
						'lineno' => $stacktop['lineno'],
						'load' => array
						(
							'type' => 'SYNT_NOT_EXPR',
							'load' => $stacktop['load']
						)
					)
				);
				continue;
			}
			// AND_EXPRESSION ::= AND_EXPRESSION and UNARY_EXPRESSION
			if
			(
				$stacktop['type'] == 'SYNT_UNARY_EXPR' and
				$stacksecondtop['type'] == 'LEX_AND' and
				$stackthirdtop['type'] == 'SYNT_AND_EXPR'
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
						'type' => 'SYNT_AND_EXPR',
						'lineno' => $stacktop['lineno'],
						'left' => isset ($stackthirdtop['load']) ? $stackthirdtop['load'] : $stackthirdtop,
						'right' => $stacktop['load']
					)
				);
				continue;
			}
			// AND_EXPRESSION ::= UNARY_EXPRESSION
			if
			(
				$stacktop['type'] == 'SYNT_UNARY_EXPR'
			)
			{
				array_pop ($stack);
				array_push
				(
					$stack,
					array
					(
						'type' => 'SYNT_AND_EXPR',
						'lineno' => $stacktop['lineno'],
						'load' => $stacktop['load']
					)
				);
				continue;
			}
			// EXPRESSION ::= EXPRESSION or AND_EXPRESSION
			if
			(
				$stacktop['type'] == 'SYNT_AND_EXPR' and
				$stacksecondtop['type'] == 'LEX_OR' and
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
						'lineno' => $stacktop['lineno'],
						'left' => isset ($stackthirdtop['load']) ? $stackthirdtop['load'] : $stackthirdtop,
						'right' => isset ($stacktop['load']) ? $stacktop['load'] : $stacktop
					)
				);
				continue;
			}
			// EXPRESSION ::= AND_EXPRESSION
			if
			(
				$stacktop['type'] == 'SYNT_AND_EXPR'
			)
			{
				array_pop ($stack);
				array_push
				(
					$stack,
					array
					(
						'type' => 'SYNT_EXPR',
						'lineno' => $stacktop['lineno'],
						'load' => isset ($stacktop['load']) ? $stacktop['load'] : $stacktop
					)
				);
				continue;
			}
			// GRANT ::= allow EXPRESSION | deny EXPRESSION
			if
			(
				$stacktop['type'] == 'SYNT_EXPR' and
				($stacksecondtop['type'] == 'LEX_ALLOW' or $stacksecondtop['type'] == 'LEX_DENY')
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
						'decision' => $stacksecondtop['type'],
						'condition' => isset ($stacktop['load']) ? $stacktop['load'] : $stacktop
					)
				);
				continue;
			}
			// DEFINITION ::= DEFINE EXPRESSION
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
						'definition' => isset ($stacktop['load']) ? $stacktop['load'] : $stacktop
					)
				);
				continue;
			}
			// ADJUSTMENT ::= context CTXMODLIST on EXPRESSION
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
						'condition' => isset ($stacktop['load']) ? $stacktop['load'] : $stacktop
					)
				);
				continue;
			}
			// CODETEXT ::= CODETEXT GRANT | CODETEXT DEFINITION | CODETEXT ADJUSTMENT
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
			// CODETEXT ::= GRANT | DEFINITION | ADJUSTMENT
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
		// more, if possible. If shifting isn't possible due to empty input tape,
		// we are facing the final "accept"/"reject" dilemma. In this case our
		// work is done here, so return the whole stack to the calling function
		// to decide depending on what it is expecting.
		if ($done < $todo)
		{
			array_push ($stack, $lexems[$done++]);
			continue;
		}
		// The moment of truth.
		return $stack;
	}
}

// Return NULL, if the given expression can be evaluated against the given
// predicate list. Return the name of the first show stopper otherwise.
function firstUnrefPredicate ($plist, $expr)
{
	$self = __FUNCTION__;
	switch ($expr['type'])
	{
		case 'LEX_TRUE':
		case 'LEX_FALSE':
		case 'LEX_TAG':
		case 'LEX_AUTOTAG':
			return NULL;
		case 'LEX_PREDICATE':
			return in_array ($expr['load'], $plist) ? NULL : $expr['load'];
		case 'SYNT_NOT_EXPR':
			return $self ($plist, $expr['load']);
		case 'SYNT_EXPR':
		case 'SYNT_AND_EXPR':
			if (($tmp = $self ($plist, $expr['left'])) !== NULL)
				return $tmp;
			if (($tmp = $self ($plist, $expr['right'])) !== NULL)
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
				// A predicate can only be defined once.
				if (in_array ($sentence['term'], $predicatelist))
					return array
					(
						'result' => 'NAK',
						'load' => "[${sentence['term']}] has already been defined earlier"
					);
				// Check below makes sure, that definitions are built from already existing
				// tokens. This also makes recursive definitions impossible.
				$up = firstUnrefPredicate ($predicatelist, $sentence['definition']);
				if ($up !== NULL)
					return array
					(
						'result' => 'NAK',
						'load' => "definition of [${sentence['term']}] refers to [${up}], which is not (yet) defined"
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
	// took place (it _might_ be the same line that causes the syntax error).
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
	return "<a href='index.php?page=perms&tab=default#line${ln}'>line ${ln}</a>";
}

// Scan the given expression and return any issues found about its autotags.
function findAutoTagWarnings ($expr)
{
	global $user_defined_atags;
	$self = __FUNCTION__;
	static $entityIDs = array // $Xid_YY auto tags
	(
		'' => array ('object', 'Object'),
		'ipv4net' => array ('ipv4net', 'IPv4 network'),
		'ipv6net' => array ('ipv6net', 'IPv6 network'),
		'rack' => array ('rack', 'Rack'),
		'row' => array ('row', 'Row'),
		'location' => array ('location', 'Location'),
		'ipvs' => array ('ipv4vs', 'Virtual service'),
		'ipv4rsp' => array ('ipv4rspool', 'RS pool'),
		'file' => array ('file', 'File'),
		'vst' => array ('vst', '802.1Q template'),
		'user' => array ('user', 'User'),
	);
	switch ($expr['type'])
	{
		case 'LEX_TRUE':
		case 'LEX_FALSE':
		case 'LEX_PREDICATE':
		case 'LEX_TAG':
			return array();
		case 'LEX_AUTOTAG':
			switch (1)
			{
				case preg_match ('/^\$(.*)?id_(\d+)$/', $expr['load'], $m) && isset ($entityIDs[$m[1]]):
					list ($realm, $description) = $entityIDs[$m[1]];
					try
					{
						spotEntity ($realm, $m[2]);
						return array();
					}
					catch (EntityNotFoundException $e)
					{
						return array (array
						(
							'header' => refRCLineno ($expr['lineno']),
							'class' => 'warning',
							'text' => "$description with ID '${recid}' does not exist."
						));
					}
				case (preg_match ('/^\$username_/', $expr['load'])):
					$recid = preg_replace ('/^\$username_/', '', $expr['load']);
					global $require_local_account;
					if (!$require_local_account)
						return array();
					if (NULL !== getUserIDByUsername ($recid))
						return array();
					return array (array
					(
						'header' => refRCLineno ($expr['lineno']),
						'class' => 'warning',
						'text' => "Local user account '${recid}' does not exist."
					));
				case (preg_match ('/^\$page_([\p{L}0-9]+)$/u', $expr['load'], $m)):
					$recid = $m[1];
					global $page;
					if (isset ($page[$recid]))
						return array();
					return array (array
					(
						'header' => refRCLineno ($expr['lineno']),
						'class' => 'warning',
						'text' => "Page number '${recid}' does not exist."
					));
				case (preg_match ('/^\$(tab|op)_[\p{L}0-9_]+$/u', $expr['load'])):
				case (preg_match ('/^\$any_(op|rack|object|ip4net|ip6net|net|ipv4vs|vs|ipv4rsp|rsp|file|location|row)$/', $expr['load'])):
				case (preg_match ('/^\$typeid_\d+$/', $expr['load'])): // FIXME: check value validity
				case (preg_match ('/^\$cn_.+$/', $expr['load'])): // FIXME: check name validity and asset existence
				case (preg_match ('/^\$lgcn_.+$/', $expr['load'])): // FIXME: check name validity
				case (preg_match ('/^\$(vlan|fromvlan|tovlan)_\d+$/', $expr['load'])):
				case (preg_match ('/^\$(aggregate|unused|nameless|portless|unmounted|untagged|no_asset_tag|runs_8021Q)$/', $expr['load'])):
				case (preg_match ('/^\$(masklen_eq|spare)_\d{1,3}$/', $expr['load'])):
				case (preg_match ('/^\$attr_\d+(_\d+)?$/', $expr['load'])):
				case (preg_match ('/^\$ip4net(-\d{1,3}){5}$/', $expr['load'])):
				case (preg_match ('/^\$(8021Q_domain|8021Q_tpl)_\d+$/', $expr['load'])):
				case (preg_match ('/^\$type_(tcp|udp|mark)$/', $expr['load'])):
					return array();
				default:
					foreach ($user_defined_atags as $regexp)
						if (preg_match ($regexp, $expr['load']))
							return array();
					return array (array
					(
						'header' => refRCLineno ($expr['lineno']),
						'class' => 'warning',
						'text' => "Martian autotag '${expr['load']}'"
					));
			}
		case 'SYNT_NOT_EXPR':
			return $self ($expr['load']);
		case 'SYNT_AND_EXPR':
		case 'SYNT_EXPR':
			return array_merge
			(
				$self ($expr['left']),
				$self ($expr['right'])
			);
		default:
			return array (array
			(
				'header' => "internal error in ${self}",
				'class' => 'error',
				'text' => "Skipped expression of unknown type '${expr['type']}'"
			));
	}
}

// Idem WRT tags.
function findTagWarnings ($expr)
{
	$self = __FUNCTION__;
	switch ($expr['type'])
	{
		case 'LEX_TRUE':
		case 'LEX_FALSE':
		case 'LEX_PREDICATE':
		case 'LEX_AUTOTAG':
			return array();
		case 'LEX_TAG':
			if (getTagByName ($expr['load']) !== NULL)
				return array();
			return array (array
			(
				'header' => refRCLineno ($expr['lineno']),
				'class' => 'warning',
				'text' => "Tag '${expr['load']}' does not exist."
			));
		case 'SYNT_NOT_EXPR':
			return $self ($expr['load']);
		case 'SYNT_AND_EXPR':
		case 'SYNT_EXPR':
			return array_merge
			(
				$self ($expr['left']),
				$self ($expr['right'])
			);
		default:
			return array (array
			(
				'header' => "internal error in ${self}",
				'class' => 'error',
				'text' => "Skipped expression of unknown type '${expr['type']}'"
			));
	}
}

// Check context modifiers, warn about those that try referencing non-existent tags.
function findCtxModWarnings ($modlist)
{
	$ret = array();
	foreach ($modlist as $mod)
		if (($mod['op'] == 'insert' or $mod['op'] == 'remove') and NULL === getTagByName ($mod['tag']))
			$ret[] = array
			(
				'header' => refRCLineno ($mod['lineno']),
				'class' => 'warning',
				'text' => "Tag '${mod['tag']}' does not exist."
			);
	return $ret;
}

// Return true, if the expression makes use of the predicate given.
function referencedPredicate ($pname, $expr)
{
	$self = __FUNCTION__;
	switch ($expr['type'])
	{
		case 'LEX_TRUE':
		case 'LEX_FALSE':
		case 'LEX_TAG':
		case 'LEX_AUTOTAG':
			return FALSE;
		case 'LEX_PREDICATE':
			return $pname == $expr['load'];
		case 'SYNT_NOT_EXPR':
			return $self ($pname, $expr['load']);
		case 'SYNT_AND_EXPR':
		case 'SYNT_EXPR':
			return $self ($pname, $expr['left']) or $self ($pname, $expr['right']);
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
		case 'LEX_TRUE':
			return 'always true';
		case 'LEX_FALSE':
			return 'always false';
		case 'LEX_TAG':
		case 'LEX_AUTOTAG':
		case 'LEX_PREDICATE':
			return 'sometimes something';
		case 'SYNT_NOT_EXPR':
			return $self ($expr['load']);
		case 'SYNT_AND_EXPR':
			$leftanswer = $self ($expr['left']);
			$rightanswer = $self ($expr['right']);
			// "false and anything" is always false and thus const
			if ($leftanswer == 'always false' or $rightanswer == 'always false')
				return 'always false';
			// "true and true" is true
			if ($leftanswer == 'always true' and $rightanswer == 'always true')
				return 'always true';
			return '';
		case 'SYNT_EXPR':
			$leftanswer = $self ($expr['left']);
			$rightanswer = $self ($expr['right']);
			// "true or anything" is always true and thus const
			if ($leftanswer == 'always true' or $rightanswer == 'always true')
				return 'always true';
			// "false or false" is false
			if ($leftanswer == 'always false' and $rightanswer == 'always false')
				return 'always false';
			return '';
		default: // This is actually an internal error.
			break;
	}
}

?>
