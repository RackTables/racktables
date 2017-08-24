<?php

class DBMutexTest extends RTTestCase
{
	/**
	 * @group small
	 */
	public function testExisting ()
	{
		$name = $this->myString ('mutex1');
		$this->assertSame (TRUE, setDBMutex ($name));
		$this->assertSame (TRUE, releaseDBMutex ($name));
	}

	/**
	 * @group small
	 */
	public function testNonExisting ()
	{
		$this->assertSame (FALSE, releaseDBMutex ($this->myString ('mutex2')));
	}
}
