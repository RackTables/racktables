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
	function __construct ($name, $value)
	{
		parent::__construct ("Argument '${name}' of value '".var_export(${value},true)."' is invalid");
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

?>
