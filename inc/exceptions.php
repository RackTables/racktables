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
