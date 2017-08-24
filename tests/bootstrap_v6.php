<?php

// for PHPUnit 6

require 'bootstrap_common.php';

use PHPUnit\Framework\TestCase;

class RTTestCase extends TestCase
{
	protected function myString ($s, $classname = NULL)
	{
		return sprintf ('%s-%s-%u', $s, get_class ($this), getmypid());
	}
}
