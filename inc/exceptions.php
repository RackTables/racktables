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
	private $location;
	function __construct ($where = '[N/A]')
	{
		$this->location = $where;
		parent::__construct ("One or more arguments to function ${where} are invalid");
	}
	function getLocation()
	{
		return $this->location;
	}
}

?>
