<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

/*
*
*  This file is a library of HTTP cache functions.
*  Intended to be small and effective and to be included only for some request types.
*
*/

define ('CACHE_DURATION', 604800); // 7 * 24 * 3600

// if client passes If-Modified-Since header, and it is greater or equal to $creation_ts, and
// $expire seconds not elapsed since IMS, the function sends HTTP-304 with $creation_ts
// returns TRUE on cache-hit, FALSE otherwise. Calling side should call exit if the result is TRUE.
function checkCachedResponse ($creation_ts, $expire)
{
	$client_time = HTTPDateToUnixTime (@$_SERVER['HTTP_IF_MODIFIED_SINCE']);
	$server_time = time();
	$result =
	(
		$client_time !== FALSE && $client_time !== -1 && // IMS header is readable
		! in_array ('no-cache', preg_split ('/\s*,\s*/', @$_SERVER['HTTP_CACHE_CONTROL'])) && // no-cache parameter unset
		$client_time <= $server_time && // not in future
		$client_time >= $creation_ts && // not modified since
		(! $expire || $client_time + $expire >= $server_time) // expiration timeout is not set, or not expired
	);
	$last_modified = $creation_ts > 0 ? $creation_ts : ($client_time > 0 ? $client_time : $server_time);

	header ("Cache-Control: private, max-age=$expire, pre-check=$expire");
	if ($result)
		header ('Last-Modified: ' . gmdate (DATE_RFC1123, $last_modified), TRUE, 304);
	else
		header ('Last-Modified: ' . gmdate (DATE_RFC1123, $last_modified));
	return $result;
}

function HTTPDateToUnixTime ($string)
{
	// Written per RFC 2616 3.3.1 - Full Date
	// http://www.w3.org/Protocols/rfc2616/rfc2616-sec3.html
	$month_number = array
	(
		'Jan' => 1,
		'Feb' => 2,
		'Mar' => 3,
		'Apr' => 4,
		'May' => 5,
		'Jun' => 6,
		'Jul' => 7,
		'Aug' => 8,
		'Sep' => 9,
		'Oct' => 10,
		'Nov' => 11,
		'Dec' => 12,
	);

	$formats = array();
	// RFC2616 dictates exchanged timestamps to be in GMT TZ, and RFC822
	// (which RFC1123 relies on) explicitly defines that "GMT" is equivalent
	// to "-0000" and "+0000".
	$formats['rfc1123'] = '/^(Sun|Mon|Tue|Wed|Thu|Fri|Sat), (\d{2}) (Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec) (\d{4}) (\d{2}):(\d{2}):(\d{2}) (?:GMT|[-+]0000)$/';
	$formats['rfc850'] = '/^(Sunday|Monday|Tuesday|Wednesday|Thursday|Friday|Saturday), (\d{2})-(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)-(\d{2}) (\d{2}):(\d{2}):(\d{2}) (?:GMT|[-+]0000)$/';
	$formats['asctime'] = '/^(Sun|Mon|Tue|Wed|Thu|Fri|Sat) (Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec) (\d{2}| \d{1}) (\d{2}):(\d{2}):(\d{2}) (\d{4})$/';

	$matches = array();
	if (preg_match ($formats['rfc1123'], $string, $matches))
	{
		$hours = $matches[5];
		$minutes = $matches[6];
		$seconds = $matches[7];
		$month = $month_number[$matches[3]];
		$day = $matches[2];
		$year = $matches[4];
	}
	elseif (preg_match ($formats['rfc850'], $string, $matches))
	{
		$hours = $matches[5];
		$minutes = $matches[6];
		$seconds = $matches[7];
		$month = $month_number[$matches[3]];
		$day = $matches[2];
		$year = $matches[4];
	}
	elseif (preg_match ($formats['asctime'], $string, $matches))
	{
		$hours = $matches[4];
		$minutes = $matches[5];
		$seconds = $matches[6];
		$month = $month_number[$matches[2]];
		$day = $matches[3];
		$year = $matches[7];
	}
	else
		return FALSE;
	if ($hours > 23 || $minutes > 59 || $seconds > 59 || ! checkdate ($month, $day, $year))
		return FALSE;
	return gmmktime ($hours, $minutes, $seconds, $month, $day, $year);
}
