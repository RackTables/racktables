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


class RCParserError extends Exception
{
	public $lineno;
	function __construct ($message, $lineno = 0)
	{
		$this->message = $message;
		$this->lineno = $lineno;
	}
};

/*
 * This class implements a recursive descent parser for the RackCode.
 *  It is possible because the RackCode is a LL(1) language.
 *
 * Usage:
 * 	$parser = new RackCodeParser();
 * 	$statements = $parser->parse ($text1);
 * 	$expression = $parser->parse ($text2, 'expr');
 */
class RackCodeParser
{
	function parse ($text, $root_nt = 'prog')
	{
		$this->defined_preds = array();
		$this->prog_mode = FALSE;
		// init lex
		$this->i = 0;
		$this->text = $text;
		$this->text_len = strlen ($text);
		$this->lineno = 1;
		$this->lex_value = NULL;
		try
		{
			$this->token = $this->get_token();
			$ret = $this->$root_nt();

			// check that all input characters are consumed by grammar
			if ($this->token !== NULL)
				throw new RCParserError ("unexpected {$this->token}");
			return $ret;
		}
		catch (RCParserError $e)
		{
			$e->lineno = $this->lineno;
			throw $e;
		}
	}

	// $sym could either be a string or a list of strings.
	// returns the value of the $sym class in the current position or throws the RCParserError.
	function expect ($sym)
	{
		if (NULL !== $ret = $this->accept ($sym))
			return $ret;
		throw new RCParserError ("expecting $sym");
	}

	// $sym could either be a string or a list of strings.
	// returns the value of the $sym class in the current position or NULL.
	function accept ($sym)
	{
		$curr = $this->token;
		$value = $this->lex_value;
		if
		(
			$curr !== NULL && (
				! isset ($sym) ||
				is_array ($sym) && in_array ($curr, $sym) ||
				! is_array ($sym) && $sym == $curr
			)
		)
		{
			$this->token = $this->get_token();
			return $value;
		}
	}

	##########################################
	# lexer
	##########################################

	// Returns the first char matching $char_mask (if invert_mask = 0).
	// Supports only single-byte chars in mask
	// Input text could be in UTF-8
	function stop_on_char ($char_mask, &$buff, $invert_mask = FALSE)
	{
		if ($invert_mask)
			$offset = strspn ($this->text, $char_mask, $this->i);
		else
			$offset = strcspn ($this->text, $char_mask, $this->i);
		if ($offset)
			$buff .= substr ($this->text, $this->i, $offset);
		$this->i += $offset;
		return $this->get_char();
	}

	// Returns the next available character or string "END"
	// Deals with UTF-8 encoded string $this->text.
	function get_char()
	{
		if ($this->i >= $this->text_len)
			return 'END';
		$c = $this->text[$this->i];
		if (ord ($c) < 0xc0)
		{
			$this->i++;
			return $c;
		}
		$mb_char = mb_substr (substr ($this->text, $this->i, 6), 0, 1);
		$this->i += strlen ($mb_char);
		return $mb_char;
	}

	// Implements the lexer FSM.
	// Returns the next input token (or token class) or NULL.
	// Fills $this->lex_value with the string value of a token.
	const LEX_S_INIT = 0;
	const LEX_S_COMMENT = 1;
	const LEX_S_KEYWORD = 2;
	const LEX_S_TAG = 3;
	const LEX_S_PREDICATE = 4;
	function get_token()
	{
		$state = self::LEX_S_INIT;
		$buffer = '';
		while ($this->i < $this->text_len) :
			switch ($state) :
				case self::LEX_S_INIT:
					$char = $this->stop_on_char(" \t", $buffer, TRUE); // skip spaces
					switch ($char)
					{
						case '(':
						case ')':
							$this->lex_value = $char;
							return $char;
						case '#':
							$state = self::LEX_S_COMMENT;
							break;
						case "\n":
							$this->lineno++;
							// skip NL
							break;
						case '{':
							$state = self::LEX_S_TAG;
							$buffer = '';
							break;
						case '[':
							$state = self::LEX_S_PREDICATE;
							$buffer = '';
							break;
						case 'END':
							break;
						default:
							if (preg_match ('/[\p{L}]/u', $char))
							{
								$state =  self::LEX_S_KEYWORD;
								$buffer = $char;
							}
							else
								throw new RCParserError ("Invalid char '$char'");
					}
					break;
				case self::LEX_S_KEYWORD:
					$char = $this->stop_on_char(") \t\n", $buffer); // collect keyword chars
					switch ($char)
					{
						case ')':
						case "\n":
						case ' ':
						case "\t":
							$this->i--;
							// fall-through
						case 'END':
							// got a word, sort it out
							$this->lex_value = $buffer;
							return $buffer;
						default:
							throw new RackTablesError ("Lex FSM error, state == ${state}, char == ${char}");
					}
					break;
				case self::LEX_S_TAG:
				case self::LEX_S_PREDICATE:
					$tag_mode = ($state == self::LEX_S_TAG);
					$breaking_char = $tag_mode ? '}' : ']';
					$char = $this->stop_on_char ("$breaking_char\n", $buffer); // collect tagname chars
					switch ($char)
					{
						case $breaking_char:
							$buffer = trim ($buffer, "\t ");
							if (!validTagName ($buffer, $tag_mode))
								throw new RCParserError ("Invalid tag name '$buffer'");
							$this->lex_value = $buffer;
							return $tag_mode ? 'LEX_TAG' : 'LEX_PREDICATE';
						case "\n":
						case 'END':
							throw new RCParserError ("Expecting '$breaking_char' character");
						default:
							throw new RackTablesError ("Lex FSM error, state == ${state}, char == ${char}");
					}
					break;
				case self::LEX_S_COMMENT:
					$char = $this->stop_on_char ("\n", $buffer); // collect tagname chars
					switch ($char)
					{
						case "\n":
							$this->lineno++;
							// fall-through
						case 'END':
							$state = self::LEX_S_INIT;
							$buffer = '';
							break;
						default:
							throw new RackTablesError ("Lex FSM error, state == ${state}, char == ${char}");
					}
					break;
				default:
					throw new RackTablesError ("Lex FSM error, state == ${state}");
			endswitch;
		endwhile;
		return NULL;
	}

	##########################################
	# RackCode syntax functions below
	##########################################

	// PROG:
	//     | PROG STATEMENT
	function prog()
	{
		$this->prog_mode = TRUE;
		$statements = array();
		while (NULL !== $this->token)
			$statements[] = $this->statement();
		return $statements;
	}

	// STATEMENT: GRANT
	//          | define PRED EXPR
	//          | context CTXMODLIST on EXPR
	// GRANT    : allow EXPR
	//          | deny EXPR
	function statement()
	{
		switch ($this->token)
		{
			case 'allow':
			case 'deny':
				$lineno = $this->lineno;
				$decision = $this->expect (array('allow', 'deny'));
				return array(
					'type' => 'SYNT_GRANT',
					'condition' => $this->expr(),
					'decision' => ($decision == 'allow' ? TRUE : FALSE),
					'lineno' => $lineno,
				);
			case 'define':
				$lineno = $this->lineno;
				$this->expect ('define');
				$pred_name = $this->expect ('LEX_PREDICATE');
				if (isset ($this->defined_preds[$pred_name]))
					throw new RCParserError ("Duplicate definition of [$pred_name]");
				$ret = array(
					'type' => 'SYNT_DEFINITION',
					'term' => $pred_name,
					'definition' => $this->expr(),
					'lineno' => $lineno,
				);
				$this->defined_preds[$pred_name] = 1;
				return $ret;
			case 'context':
				$lineno = $this->lineno;
				$this->expect ('context');
				$modlist = $this->ctxmodlist();
				$this->expect ('on');
				return array(
					'type' => 'SYNT_ADJUSTMENT',
					'modlist' => $modlist,
					'condition' => $this->expr(),
					'lineno' => $lineno,
				);
			default:
				throw new RCParserError ("unexpected {$this->token}");
		}
	}

	// CTXMODLIST: CTXMODLIST CTXMOD
	//           | CTXMOD
	// CTXMOD    : 'clear'
	//           | 'insert' TAG
	//           | 'remove' TAG
	function ctxmodlist()
	{
		$list = array();
		while (TRUE)
		{
			$lineno = $this->lineno;
			if ($op = $this->accept (array('insert', 'remove')))
				$list[] = array(
					'op' => $op,
					'tag' => $this->expect ('LEX_TAG'),
					'lineno' => $lineno,
				);
			elseif ($this->accept ('clear'))
				$list[] = array(
					'op' => 'clear',
					'lineno' => $lineno,
				);
			elseif (! $list) // a CTXMODLIST consists of at least one CTXMOD
				throw new RCParserError ("expecting CTXMOD");
			else
				break;
		}
		return $list;
	}

	// EXPR    : AND_EXPR
	//         | AND_EXPR 'or' EXPR
	// AND_EXPR: UN_EXPR
	//         | UN_EXPR 'and' AND_EXPR
	function expr ($op = 'or')
	{
		switch ($op)
		{
			case 'or':
				$type = 'SYNT_EXPR';
				$left = $this->expr ('and');
				break;
			case 'and':
				$type = 'SYNT_AND_EXPR';
				$left = $this->un_expr();
				break;
			default:
				throw new InvalidArgException ('op', $op);
		}

		if ($this->accept ($op))
		{
			$tag_args = array();
			$tag_lines = array();
			$expr_args = array();
			if ($left['type'] == 'LEX_TAG')
			{
				$tag_args[] = $left['load'];
				$tag_lines[] = $left['lineno'];
			}
			else
				$expr_args[] = $left;

			$right = $this->expr ($op);
			if ($right['type'] == $type)
			{
				$tag_args = array_merge ($tag_args, $right['tag_args']);
				$tag_lines = array_merge ($tag_lines, $right['tag_lines']);
				$expr_args = array_merge ($expr_args, $right['expr_args']);
			}
			elseif ($right['type'] == 'LEX_TAG')
			{
				$tag_args[] = $right['load'];
				$tag_lines[] = $right['lineno'];
			}
			elseif ($right['type'] == 'LEX_BOOL')
			{
				if ($right['load'] == ($op == 'or'))
				{
					$right['lineno'] = $left['lineno'];
					return $right;
				}
			}
			else
				$expr_args[] = $right;

			return array(
				'type' => $type,
				'tag_args' => $tag_args,
				'expr_args' => $expr_args,
				'lineno' => $left['lineno'],
				'tag_lines' => $tag_lines,
			);
		}
		return $left;
	}

	// UN_EXPR: '(' EXPR ')'
	//        | 'not' UN_EXPR
	//        | BOOL
	//        | PRED
	//        | TAG
	function un_expr()
	{
		$lineno = $this->lineno;
		if ($this->accept ('('))
		{
			$expr = $this->expr();
			$this->expect (')');
			return $expr;
		}
		elseif ($this->accept ('not'))
		{
			$expr = $this->un_expr();
			if ($expr['type'] == 'LEX_BOOL')
			{
				$expr['load'] = ! $expr['load'];
				return $expr;
			}
			return array(
				'type' => 'SYNT_NOT_EXPR',
				'load' => $expr,
				'lineno' => $lineno,
			);
		}
		elseif ($k = $this->accept (array('true', 'false')))
			return array(
				'type' => 'LEX_BOOL',
				'load' => $k == 'true',
				'lineno' => $lineno,
			);
		elseif ($k = $this->accept ('LEX_PREDICATE'))
		{
			if ($this->prog_mode && ! isset ($this->defined_preds[$k]))
				throw new RCParserError ("Undefined predicate [$k] refered");
			return array(
				'type' => 'LEX_PREDICATE',
				'load' => $k,
				'lineno' => $lineno,
			);
		}
		elseif ($k = $this->accept ('LEX_TAG'))
			return array(
				'type' => 'LEX_TAG',
				'load' => $k,
				'lineno' => $lineno,
			);
		throw new RCParserError ("Unexpected token {$this->token}");
	}
}

function refRCLineno ($ln)
{
	return "<a href='index.php?page=perms&tab=default&line=${ln}'>line ${ln}</a>";
}

// returns warning message or NULL
function checkAutotagName ($atag_name)
{
	global $user_defined_atags;
	static $entityIDs = array // $Xid_YY auto tags
	(
		'' => array ('object', 'Object'),
		'ipv4net' => array ('ipv4net', 'IPv4 network'),
		'ipv6net' => array ('ipv6net', 'IPv6 network'),
		'rack' => array ('rack', 'Rack'),
		'row' => array ('row', 'Row'),
		'location' => array ('location', 'Location'),
		'ipvs' => array ('ipvs', 'Virtual service'),
		'ipv4rsp' => array ('ipv4rspool', 'RS pool'),
		'file' => array ('file', 'File'),
		'vst' => array ('vst', '802.1Q template'),
		'user' => array ('user', 'User'),
	);
	// autotags that don't require a regexp to match
	$simple_autotags = array
	(
		'$aggregate',
		'$any_file',
		'$any_ip4net',
		'$any_ip6net',
		'$any_ipv4rsp',
		'$any_ipv4vs',
		'$any_location',
		'$any_net',
		'$any_object',
		'$any_op',
		'$any_rack',
		'$any_row',
		'$any_rsp',
		'$any_vs',
		'$nameless',
		'$no_asset_tag',
		'$portless',
		'$runs_8021Q',
		'$type_mark',
		'$type_tcp',
		'$type_udp',
		'$unmounted',
		'$untagged',
		'$unused',
	);
	switch (1)
	{
		case (in_array ($atag_name, $simple_autotags)):
			break;
		case preg_match ('/^\$(.*)?id_(\d+)$/', $atag_name, $m) && isset ($entityIDs[$m[1]]):
			list ($realm, $description) = $entityIDs[$m[1]];
			$recid = $m[2];
			try
			{
				spotEntity ($realm, $m[2]);
			}
			catch (EntityNotFoundException $e)
			{
				return "$description with ID '${recid}' does not exist.";
			}
			break;
		case (preg_match ('/^\$username_/', $atag_name)):
			$recid = preg_replace ('/^\$username_/', '', $atag_name);
			global $require_local_account;
			if ($require_local_account && NULL === getUserIDByUsername ($recid))
				return "Local user account '${recid}' does not exist.";
			break;
		case (preg_match ('/^\$page_([\p{L}0-9]+)$/u', $atag_name, $m)):
			$recid = $m[1];
			global $page;
			if (! isset ($page[$recid]))
				return "Page number '${recid}' does not exist.";
			break;
		case (preg_match ('/^\$(tab|op)_[\p{L}0-9_]+$/u', $atag_name)):
		case (preg_match ('/^\$typeid_\d+$/', $atag_name)): // FIXME: check value validity
		case (preg_match ('/^\$cn_.+$/', $atag_name)): // FIXME: check name validity and asset existence
		case (preg_match ('/^\$lgcn_.+$/', $atag_name)): // FIXME: check name validity
		case (preg_match ('/^\$(vlan|fromvlan|tovlan)_\d+$/', $atag_name)):
		case (preg_match ('/^\$(masklen_eq|spare)_\d{1,3}$/', $atag_name)):
		case (preg_match ('/^\$attr_\d+(_\d+)?$/', $atag_name)):
		case (preg_match ('/^\$ip4net(-\d{1,3}){5}$/', $atag_name)):
		case (preg_match ('/^\$(8021Q_domain|8021Q_tpl)_\d+$/', $atag_name)):
		case (preg_match ('/^\$client_([0-9a-fA-F.:]+)$/', $atag_name)):
			break;
		default:
			foreach ($user_defined_atags as $regexp)
				if (preg_match ($regexp, $atag_name))
					break 2;
			return "Martian autotag {{$atag_name}}.";
	}
}

function findTagWarnings ($expr)
{
	$self = __FUNCTION__;
	switch ($expr['type'])
	{
		case 'LEX_BOOL':
		case 'LEX_PREDICATE':
			return array();
		case 'LEX_TAG':
			$tag = $expr['load'];
			if ('$' === substr ($tag, 0, 1))
			{
				if (NULL !== $msg = checkAutotagName ($tag))
					return array (array
					(
						'header' => refRCLineno ($expr['lineno']),
						'class' => 'warning',
						'text' => $msg
					));
			}
			elseif (getTagByName ($tag) === NULL)
				return array (array
				(
					'header' => refRCLineno ($expr['lineno']),
					'class' => 'warning',
					'text' => "Tag {{$tag}} does not exist."
				));
			return array();
		case 'SYNT_NOT_EXPR':
			return $self ($expr['load']);
		case 'SYNT_AND_EXPR':
		case 'SYNT_EXPR':
			$ret = array();
			foreach ($expr['tag_args'] as $i => $tag)
				if ('$' === substr ($tag, 0, 1))
				{
					if (NULL !== $msg = checkAutotagName ($tag))
						$ret[] = array
						(
							'header' => refRCLineno ($expr['tag_lines'][$i]),
							'class' => 'warning',
							'text' => $msg
						);
				}
				elseif (getTagByName ($tag) === NULL)
					$ret[] = array
					(
						'header' => refRCLineno ($expr['tag_lines'][$i]),
						'class' => 'warning',
						'text' => "Tag {{$tag}} does not exist."
					);
			foreach ($expr['expr_args'] as $sub)
				$ret = array_merge ($ret, $self ($sub));
			return $ret;
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
		if (($mod['op'] == 'insert' || $mod['op'] == 'remove') && NULL === getTagByName ($mod['tag']))
			$ret[] = array
			(
				'header' => refRCLineno ($mod['lineno']),
				'class' => 'warning',
				'text' => "Tag {{$mod['tag']}} does not exist."
			);
	return $ret;
}

// Return list of used predicates
function getReferencedPredicates ($expr)
{
	$self = __FUNCTION__;
	switch ($expr['type'])
	{
		case 'LEX_BOOL':
		case 'LEX_TAG':
			return array();
		case 'LEX_PREDICATE':
			return array ($expr['load'] => $expr['load']);
		case 'SYNT_NOT_EXPR':
			return $self ($expr['load']);
		case 'SYNT_AND_EXPR':
		case 'SYNT_EXPR':
			$ret = array();
			foreach ($expr['expr_args'] as $sub)
				$ret += $self ($sub);
			return $ret;
		default:
			throw new RackTablesError ("Unknown expr type {$expr['type']}");
	}
}

// Return 'always true', 'always false' or any other verdict.
function invariantExpression ($expr)
{
	$self = __FUNCTION__;
	switch ($expr['type'])
	{
		case 'LEX_BOOL':
			return $expr['load'];
		case 'SYNT_NOT_EXPR':
			$ret = $self ($expr['load']);
			if (isset ($ret))
				return ! $ret;
			break;
		case 'SYNT_AND_EXPR':
		case 'SYNT_EXPR':
			$stop_on = $expr['type'] == 'SYNT_EXPR';
			foreach ($expr['expr_args'] as $sub_expr)
				if ($stop_on === $self ($sub_expr))
					return $stop_on;
			break;
	}
}

function getRackCodeStats ()
{
	global $rackCode, $pTable;
	$grantc = $modc = 0;
	foreach ($rackCode as $s)
		switch ($s['type'])
		{
			case 'SYNT_GRANT':
				$grantc++;
				break;
			case 'SYNT_ADJUSTMENT':
				$modc++;
				break;
			default:
				break;
		}
	return array
	(
		'Definition sentences' => count ($pTable),
		'Grant sentences' => $grantc,
		'Context mod sentences' => $modc
	);
}

function getRackCodeWarnings ()
{
	$ret = array();
	global $rackCode, $pTable;

	$seen_exprs = array();
	foreach ($pTable as $pname => $expr)
		$seen_exprs[] = $expr;

	foreach ($rackCode as $sentence)
		switch ($sentence['type'])
		{
			case 'SYNT_ADJUSTMENT':
				$seen_exprs[] = $sentence['condition'];
				$ret = array_merge ($ret, findCtxModWarnings ($sentence['modlist']));
				break;
			case 'SYNT_GRANT':
				$seen_exprs[] = $sentence['condition'];
				break;
			default:
				$ret[] = array
				(
					'header' => 'internal error',
					'class' => 'error',
					'text' => "Skipped sentence of unknown type '${sentence['type']}'"
				);
		}

	$used_preds = array();
	foreach ($seen_exprs as $expr)
	{
		$ret = array_merge ($ret, findTagWarnings ($expr));
		$used_preds += getReferencedPredicates ($expr);
		if (NULL !== $const = invariantExpression ($expr))
			$ret[] = array
			(
				'header' => refRCLineno ($sentence['lineno']),
				'class' => 'warning',
				'text' => sprintf ("Expression is always %s.", $const ? "true" : "false")
			);
	}

	foreach ($pTable as $pname => $pexpr)
		if (! isset ($used_preds[$pname]))
			$ret[] = array
			(
				'header' => refRCLineno ($pexpr['lineno']),
				'class' => 'neutral',
				'text' => "Predicate [{$pname}] is defined, but never used."
			);

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
