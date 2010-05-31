<?php

class EntityNotFoundException extends Exception {
	private $entity;
	private $id;
	function __construct($entity, $id)
	{
		parent::__construct ("Object '$entity'#'$id' does not exist");
		$this->entity = $entity;
		$this->id = $id;
	}
	function getEntity()
	{
		return $this->entity;
	}
	function getId()
	{
		return $this->id;
	}
}

class RealmNotFoundException extends Exception {
	private $realm;
	function __construct($realm)
	{
		parent::__construct ("Realm '$realm' does not exist");
		$this->realm = $realm;
	}
	function getRealm()
	{
		return $this->realm;
	}
}

class NotUniqueException extends Exception
{
	private $subject;
	function __construct ($what = NULL)
	{
		$this->subject = $what;
		parent::__construct ('Cannot add duplicate record' . ($what === NULL ? '' : " (${what} must be unique)"));
	}
	function getSubject()
	{
		return $this->subject;
	}
}

class InvalidArgException extends Exception
{
	private $name;
	private $value;
	private $reason;
	function __construct ($name, $value, $reason=NULL)
	{
		$message = "Argument '${name}' of value '".var_export($value,true)."' is invalid.";
		if (!is_null($reason)) {
			$message .= ' ('.$reason.')';
		}
		parent::__construct ($message);
		$this->name = $name;
		$this->value = $value;
	}
	function getName()
	{
		return $this->name;
	}
	function getValue()
	{
		return $this->value;
	}
}

class InvalidRequestArgException extends Exception
{
	private $name;
	private $value;
	private $reason;
	function __construct ($name, $value, $reason=NULL)
	{
		$message = "Request parameter '${name}' of value '".var_export($value,true)."' is invalid.";
		if (!is_null($reason)) {
			$message .= ' ('.$reason.')';
		}
		parent::__construct ($message);
		$this->name = $name;
		$this->value = $value;
	}
	function getName()
	{
		return $this->name;
	}
	function getValue()
	{
		return $this->value;
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

function print404($e)
{
	header("HTTP/1.1 404 Not Found");
	header ('Content-Type: text/html; charset=UTF-8');
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'."\n";
	echo "<head><title> Exception </title>\n";
	printPageHeaders();
	echo '</head> <body>';
	echo '<h2>Object: '.$e->getEntity().'#'.$e->getId().' not found</h2>';
	echo '</body></html>';

}

function printPDOException($e)
{
	header("HTTP/1.1 500 Internal Server Error");
	header ('Content-Type: text/html; charset=UTF-8');
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'."\n";
	echo "<head><title> PDO Exception </title>\n";
	printPageHeaders();
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
	echo "<link rel=stylesheet type='text/css' href=css/pi.css />\n";
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

function printException($e)
{
	if (get_class ($e) == 'Exception')
		switch ($e->getCode())
		{
		case E_NOT_AUTHENTICATED:
			header ('WWW-Authenticate: Basic realm="' . getConfigVar ('enterprise') . ' RackTables access"');
			header ("HTTP/1.1 401 Unauthorized");
			header ('Content-Type: text/html; charset=UTF-8');
			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
			echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'."\n";
			echo "<head><title>Not authenticated</title>\n";
			printPageHeaders();
			echo '</head><body><h2>This system requires authentication. You should use a username and a password.</h2></body></html>';
			return;
		default:
		}
	if (get_class($e) == 'EntityNotFoundException')
		print404($e);
	elseif (get_class($e) == 'PDOException')
		printPDOException($e);
	else
		printGenericException($e);
}

?>
