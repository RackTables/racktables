<?php

class GetRowsCountTest extends RTTestCase
{
	protected $table_name;

	public function setUp () : void
	{
		$this->table_name = $this->myString ('tmptest');
		usePreparedExecuteBlade
		(
			'CREATE TABLE `' . $this->table_name . '` (' .
			'`id` int(10) unsigned NOT NULL auto_increment, ' .
			'`name` char(64) default NULL, ' .
			'PRIMARY KEY  (`id`), ' .
			'UNIQUE KEY `name` (`name`) '.
			') ENGINE=InnoDB'
		);
	}

	/**
	 * @group small
	 */
	public function testAll ()
	{
		$names = array ('alpha', 'beta', 'gamma', 'delta');
		$this->assertEquals (0, getRowsCount ($this->table_name));
		$ids = array();
		foreach ($names as $name)
		{
			usePreparedInsertBlade ($this->table_name, array ('name' => $name));
			$ids[] = lastInsertID();
		}
		$this->assertEquals (count ($names), getRowsCount ($this->table_name));
		foreach ($ids as $id)
			usePreparedDeleteBlade ($this->table_name, array ('id' => $id));
		$this->assertEquals (0, getRowsCount ($this->table_name));
	}

	public function tearDown () : void
	{
		usePreparedExecuteBlade ('DROP TABLE `' . $this->table_name . '`');
	}
}
