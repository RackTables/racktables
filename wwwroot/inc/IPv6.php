<?php

class IPv6Address
{
	const zero_address = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0"; // 16 bytes
	protected $words = self::zero_address;

function __construct ($bin_str = self::zero_address)
{
	if (strlen ($bin_str) < 16)
		$bin_str .= substr (self::zero_address, 0, 16 - strlen ($bin_str));
	elseif (strlen ($bin_str) > 16)
		$bin_str = substr ($bin_str, 0, 16);
	$this->words = $bin_str;
}

// returns 16-byte binary string
function getBin ()
{
	return $this->words;
}

// returns string for PTR DNS query (reversed IPv6 address)
function getArpa()
{
	$ret = '';
	foreach (array_reverse (unpack ('C*', $this->words)) as $octet)
	{
		$ret .= dechex ($octet & 0xF) . ".";
		$ret .= dechex ($octet >> 4) . ".";
	}
	return $ret . "ip6.arpa";
}

private static function set_word_value (&$haystack, $nword, $hexvalue)
{
	// check that $hexvalue is like /^[0-9a-fA-F]*$/
	for ($i = 0; $i < strlen ($hexvalue); $i++)
	{
		$char = ord ($hexvalue[$i]);
		if (! ($char >= 0x30 && $char <= 0x39 || $char >= 0x41 && $char <= 0x46 || $char >=0x61 && $char <= 0x66))
			return FALSE;
	}
	$haystack = substr_replace ($haystack, pack ('n', hexdec ($hexvalue)), $nword * 2, 2);
	return TRUE;
}

// returns bool - was the object modified or not.
// return true only if address syntax is completely correct.
function parse ($str_ipv6)
{
	if (empty ($str_ipv6))
		return FALSE;

	$result = self::zero_address;
	// remove one of double beginning/tailing colons
	if (substr ($str_ipv6, 0, 2) == '::')
		$str_ipv6 = substr ($str_ipv6, 1);
	elseif (substr ($str_ipv6, -2, 2) == '::')
		$str_ipv6 = substr ($str_ipv6, 0, strlen ($str_ipv6) - 1);

	$tokens = explode (':', $str_ipv6);
	$last_token = $tokens[count ($tokens) - 1];
	$split = explode ('.', $last_token);
	if (count ($split) == 4)
	{
		$hex_tokens = array();
		$hex_tokens[] = dechex ($split[0] * 256 + $split[1]);
		$hex_tokens[] = dechex ($split[2] * 256 + $split[3]);
		array_splice ($tokens, -1, 1, $hex_tokens);
	}
	if (count ($tokens) > 8)
		return FALSE;
	for ($i = 0; $i < count ($tokens); $i++)
	{
		if ($tokens[$i] != '')
		{
			if (! self::set_word_value ($result, $i, $tokens[$i]))
				return FALSE;
		}
		else
		{
			$k = 8; //index in result string (last word)
			for ($j = count ($tokens) - 1; $j > $i; $j--) // $j is an index in $tokens for reverse walk
				if ($tokens[$j] == '')
					break;
				elseif (! self::set_word_value ($result, --$k, $tokens[$j]))
					return FALSE;
			if ($i != $j)
				return FALSE; //error, more than 1 '::' range
			break;
		}
	}
	if (! isset ($k) && count ($tokens) != 8)
		return FALSE;
	$this->words = $result;
	return TRUE;
}

function format ()
{
	// maybe this is IPv6-to-IPv4 address?
	if (substr ($this->words, 0, 12) == "\0\0\0\0\0\0\0\0\0\0\xff\xff")
		return '::ffff:' . implode ('.', unpack ('C*', substr ($this->words, 12, 4)));

	$result = array();
	$hole_index = NULL;
	$max_hole_index = NULL;
	$hole_length = 0;
	$max_hole_length = 0;

	for ($i = 0; $i < 8; $i++)
	{
		$value = array_shift (unpack ('n', substr ($this->words, $i * 2, 2)));
		$result[] = dechex ($value & 0xffff);
		if ($value != 0)
		{
			unset ($hole_index);
			$hole_length = 0;
		}
		else
		{
			if (! isset ($hole_index))
				$hole_index = $i;
			if (++$hole_length >= $max_hole_length)
			{
				$max_hole_index = $hole_index;
				$max_hole_length = $hole_length;
			}
		}
	}
	if (isset ($max_hole_index))
	{
		array_splice ($result, $max_hole_index, $max_hole_length, array (''));
		if ($max_hole_index == 0 && $max_hole_length == 8)
			return '::';
		elseif ($max_hole_index == 0)
			return ':' . implode (':', $result);
		elseif ($max_hole_index + $max_hole_length == 8)
			return implode (':', $result) . ':';
	}
	return implode (':', $result);
}

// returns new object with applied mask, or NULL if mask is incorrect
function get_first_subnet_address ($prefix_length)
{
	if ($prefix_length < 0 || $prefix_length > 128)
		return NULL;
	$result = clone $this;
	if ($prefix_length == 128)
		return $result;
	$char_num = intval ($prefix_length / 8);
	if (0xff00 != $bitmask = 0xff00 >> ($prefix_length % 8))
	{
		$result->words[$char_num] = chr (ord ($result->words[$char_num]) & $bitmask);
		++$char_num;
	}
	for ($i = $char_num; $i < 16; $i++)
		$result->words[$i] = "\0";
	return $result;
}

// returns new object with applied mask, or NULL if mask is incorrect
function get_last_subnet_address ($prefix_length)
{
	if ($prefix_length < 0 || $prefix_length > 128)
		return NULL;
	$result = clone $this;
	if ($prefix_length == 128)
		return $result;
	$char_num = intval ($prefix_length / 8);
	if (0xff != $bitmask = 0xff >> ($prefix_length % 8))
	{
		$result->words[$char_num] = chr (ord ($result->words[$char_num]) | $bitmask);
		++$char_num;
	}
	for ($i = $char_num; $i < 16; $i++)
		$result->words[$i] = "\xff";
	return $result;
}

// returns new object
function next ()
{
	$result = clone $this;
	for ($i = 15; $i >= 0; $i--)
	{
		if ($result->words[$i] == "\xff")
			$result->words[$i] = "\0";
		else
		{
			$result->words[$i] = chr (ord ($result->words[$i]) + 1);
			break;
		}
	}
	return $result;
}

// returns new object
function prev ()
{
	$result = clone $this;
	for ($i = 15; $i >= 0; $i--)
	{
		if ($result->words[$i] == "\0")
			$result->words[$i] = "\xff";
		else
		{
			$result->words[$i] = chr (ord ($result->words[$i]) - 1);
			break;
		}
	}
	return $result;
}

# $a == $b
public static function eq (IPv6Address $a, IPv6Address $b)
{
	return $a->words === $b->words;
}

# $a > $b
public static function gt (IPv6Address $a, IPv6Address $b)
{
	for ($i = 0; $i < 16; $i++)
		if ($a->words[$i] > $b->words[$i])
			return TRUE;
		elseif ($a->words[$i] < $b->words[$i])
			return FALSE;
	return FALSE;
}

# $a < $b
public static function lt (IPv6Address $a, IPv6Address $b)
{
	return ! self::eq ($a, $b) && ! self::gt ($a, $b);
}

# $a >= $b
public static function ge (IPv6Address $a, IPv6Address $b)
{
	return self::eq ($a, $b) || self::gt ($a, $b);
}

# $a <= $b
public static function le (IPv6Address $a, IPv6Address $b)
{
	return self::eq ($a, $b) || ! self::gt ($a, $b);
}

} // class IPv6Address

?>
