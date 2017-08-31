<?php

// for PHPUnit 4 and 5

require 'bootstrap_common.php';

class RTTestCase extends PHPUnit_Framework_TestCase
{
	protected function myString ($s, $classname = NULL)
	{
		return sprintf ('%s-%s-%u', $s, get_class ($this), getmypid());
	}
}
