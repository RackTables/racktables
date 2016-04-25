<?php

class GetRowsCount extends PHPUnit_Framework_TestCase
{
	protected $table_name;

	public function setUp ()
	{
		$this->table_name = sprintf ('tmptest_%s_%u', get_class(), getmypid());
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
		$this->assertEquals (0, getRowsCount ($this->table_name));
		usePreparedInsertBlade ($this->table_name, array ('name' => 'alpha'));
		usePreparedInsertBlade ($this->table_name, array ('name' => 'beta'));
		usePreparedInsertBlade ($this->table_name, array ('name' => 'gamma'));
		usePreparedInsertBlade ($this->table_name, array ('name' => 'delta'));
		$this->assertEquals (4, getRowsCount ($this->table_name));
	}

	public function tearDown ()
	{
		usePreparedExecuteBlade ('DROP TABLE `' . $this->table_name . '`');
	}
}

?>
