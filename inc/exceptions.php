<?php

class EntityNotFoundException extends RackTablesError
{
	function __construct($entity, $id)
	{
		parent::__construct ("Object '$entity'#'$id' does not exist");
	}
	public function dispatch()
	{
		RackTablesError::genHTMLPage ('Missing record', "<h2>Missing record</h2><br>" . $this->message);
	}
}

class InvalidArgException extends Exception
{
	function __construct ($name, $value, $reason=NULL)
	{
		$message = "Argument '${name}' of value '".var_export($value,true)."' is invalid.";
		if (!is_null($reason))
			$message .= ' ('.$reason.')';
		parent::__construct ($message);
	}
}

// The only purpose of below two classes is to provide a convenient
// mean to catch "soft" failures in process.php.
class InvalidRequestArgException extends RackTablesError
{
	function __construct ($name, $value, $reason=NULL)
	{
		$message = "Request parameter '${name}' of value '".var_export($value,true)."' is invalid.";
		if (!is_null($reason))
			$message .= ' ('.$reason.')';
		parent::__construct ($message);
	}
	public function dispatch()
	{
		RackTablesError::genHTMLPage ('Assertion failed', '<h2>Assertion failed</h2><br>' . $this->message);
	}
}

class RTDBConstraintError extends RackTablesError
{
	public function dispatch()
	{
		RackTablesError::genHTMLPage ('Database constraint violation', '<h2>Constraint violation</h2><br>' . $this->message);
	}
}

function dumpArray($arr)
{
	echo '<table class="exceptionParametersDump">';
	foreach($arr as $key=>$value)
	{
		echo "<tr><th>$key</th><td>$value</td></tr>";
	}
	echo '</table>';
}

function stringTrace($trace)
{
	$ret = '';
	foreach($trace as $line) {
		$ret .= $line['file'].':'.$line['line'].' '.$line['function'].'(';
		$f = true;
		if (isset($line['args']) and is_array($line['args'])) foreach ($line['args'] as $arg) {
			if (!$f) $ret .= ', ';
			if (is_string($arg))
				$printarg = "'".$arg."'";
			elseif (is_null($arg))
				$printarg = 'NULL';
			elseif (is_array($arg))
				$printarg = print_r($arg, 1);
			else
				$printarg = $arg;
			$ret .= $printarg;
			$f = false;
		}
		$ret .= ")\n";
	}
	return $ret;
}

function printPDOException($e)
{
	header("HTTP/1.1 500 Internal Server Error");
	header ('Content-Type: text/html; charset=UTF-8');
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'."\n";
	echo "<head><title> PDO Exception </title>\n";
	echo '</head> <body>';
	echo '<h2>Pdo exception: '.get_class($e).'</h2><code>'.$e->getMessage().'</code> (<code>'.$e->getCode().'</code>)';
	echo '<p>at file <code>'.$e->getFile().'</code>, line <code>'.$e->getLine().'</code></p><pre>';
	echo stringTrace($e->getTrace());
	echo '</pre>';
	echo '<h2>Error info:</h2>';
	echo '<pre>';
	print_r($e->errorInfo);
	echo '</pre>';
	echo '<h2>Parameters:</h2>';
	echo '<h3>GET</h3>';
	dumpArray($_GET);
	echo '<h3>POST</h3>';
	dumpArray($_POST);
	echo '<h3>COOKIE</h3>';
	dumpArray($_COOKIE);
	echo '</body></html>';

}

function printGenericException($e)
{
	header("HTTP/1.1 500 Internal Server Error");
	header ('Content-Type: text/html; charset=UTF-8');
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'."\n";
	echo "<head><title> Exception </title>\n";
	echo "<link rel=stylesheet type='text/css' href=pi.css />\n";
	echo "<link rel=icon href='pix/racktables.ico' type='image/x-icon' />";
	echo '</head> <body>';
	echo '<h2>Uncaught exception: '.get_class($e).'</h2><code>'.$e->getMessage().'</code> (<code>'.$e->getCode().'</code>)';
	echo '<p>at file <code>'.$e->getFile().'</code>, line <code>'.$e->getLine().'</code></p><pre>';
	echo stringTrace($e->getTrace());
	echo '</pre>';
	echo '<h2>Parameters:</h2>';
	echo '<h3>GET</h3>';
	dumpArray($_GET);
	echo '<h3>POST</h3>';
	dumpArray($_POST);
	echo '<h3>COOKIE</h3>';
	dumpArray($_COOKIE);
	echo '</body></html>';

}

class RackTablesError extends Exception
{
	const NOT_AUTHENTICATED = 4;
	const MISCONFIGURED = 6;
	protected final function genHTMLPage ($title, $text)
	{
		header ('Content-Type: text/html; charset=UTF-8');
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
		echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'."\n";
		echo "<head><title>${title}</title>";
		echo "</head><body>${text}</body></html>";
	}
	public function dispatch()
	{
		$msgheader = array
		(
			self::NOT_AUTHENTICATED => 'Not authenticated',
			self::MISCONFIGURED => 'Configuration error',
		);
		$msgbody = array
		(
			self::NOT_AUTHENTICATED => '<h2>This system requires authentication. You should use a username and a password.</h2>',
			self::MISCONFIGURED => '<h2>Configuration error</h2><br>' . $this->message,
		);
		switch ($this->code)
		{
		case self::NOT_AUTHENTICATED:
			header ('WWW-Authenticate: Basic realm="' . getConfigVar ('enterprise') . ' RackTables access"');
			header ("HTTP/1.1 401 Unauthorized");
		case self::MISCONFIGURED:
			genHTMLPage ($msgheader[$code], $msgbody[$code]);
			break;
		default:
			throw new Exception ('Dispatching error, unknown code ' . $this->code, E_INTERNAL);
		}
	}
}

function printException($e)
{
	if ($e instanceof RackTablesError)
		$e->dispatch();
	elseif (get_class($e) == 'PDOException')
		printPDOException($e);
	else
		printGenericException($e);
}

?>
