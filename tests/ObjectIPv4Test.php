<?php

class ObjectIPv4Test extends RTTestCase
{
	protected $rtr_object_id, $extnet_id, $intnet_id;

	public function setUp()
	{
		$this->extnet_id = createIPv4Prefix ('10.0.0.0/24', $this->myString ('external'), TRUE);
		$this->intnet_id = createIPv4Prefix ('192.168.0.0/24', $this->myString ('internal'), TRUE);
		$this->rtr_object_id = commitAddObject ($this->myString ('object'), NULL, 1, NULL);
	}

	public function tearDown()
	{
		commitDeleteObject ($this->rtr_object_id);
		destroyIPv4Prefix ($this->extnet_id);
		destroyIPv4Prefix ($this->intnet_id);
	}

	/**
	 * @group small
	 * @dataProvider providerNATv4Count
	 */
	public function testNATv4Count ($rules)
	{
		$this->assertEquals (0, getNATv4CountForObject ($this->rtr_object_id));
		if (! count ($rules))
			return;

		foreach ($rules as $rule)
			newPortForwarding
			(
				$this->rtr_object_id,
				ip4_parse ($rule['localaddr']),
				$rule['localport'],
				ip4_parse ($rule['remoteaddr']),
				$rule['remoteport'],
				$rule['protocol'],
				$this->myString ('NAT rule')
			);
		$this->assertEquals (count ($rules), getNATv4CountForObject ($this->rtr_object_id));

		foreach ($rules as $rule)
			deletePortForwarding
			(
				$this->rtr_object_id,
				ip4_parse ($rule['localaddr']),
				$rule['localport'],
				ip4_parse ($rule['remoteaddr']),
				$rule['remoteport'],
				$rule['protocol']
			);
		$this->assertEquals (0, getNATv4CountForObject ($this->rtr_object_id));
	}

	/**
	 * @group small
	 * @expectedException InvalidArgException
	 * @dataProvider providerNATv4Invalid
	 */
	public function testNATv4InvalidArg ($rule)
	{
		newPortForwarding
		(
			$this->rtr_object_id,
			ip4_parse ($rule['localaddr']),
			$rule['localport'],
			ip4_parse ($rule['remoteaddr']),
			$rule['remoteport'],
			$rule['protocol'],
			$this->myString ('NAT rule')
		);
	}

	function providerNATv4Count()
	{
		return array
		(
			array
			(
				array(),
			),
			array
			(
				array
				(
					array
					(
						'localaddr' => '192.168.0.1',
						'localport' => 43,
						'remoteaddr' => '10.0.0.50',
						'remoteport' => 43,
						'protocol' => 'TCP',
					),
				),
			),
			array
			(
				array
				(
					array
					(
						'localaddr' => '10.0.0.1',
						'localport' => 80,
						'remoteaddr' => '192.168.0.1',
						'remoteport' => 8080,
						'protocol' => 'TCP',
					),
					array
					(
						'localaddr' => '10.0.0.1',
						'localport' => 123,
						'remoteaddr' => '192.168.0.2',
						'remoteport' => 123,
						'protocol' => 'UDP',
					),
					array
					(
						'localaddr' => '10.0.0.2',
						'localport' => 0,
						'remoteaddr' => '192.168.0.3',
						'remoteport' => 0,
						'protocol' => 'ALL',
					),
				),
			),
		);
	}

	function providerNATv4Invalid()
	{
		return array
		(
			array
			(
				array
				(
					'localaddr' => '192.168.1.1', // invalid
					'localport' => 7,
					'remoteaddr' => '10.0.0.50',
					'remoteport' => 7,
					'protocol' => 'TCP',
				),
			),
			array
			(
				array
				(
					'localaddr' => '192.168.0.1',
					'localport' => 7,
					'remoteaddr' => '10.0.1.50', // invalid
					'remoteport' => 7,
					'protocol' => 'TCP',
				),
			),
			array
			(
				array
				(
					'localaddr' => '192.168.0.1',
					'localport' => 65536, // invalid
					'remoteaddr' => '10.0.0.50',
					'remoteport' => 7,
					'protocol' => 'TCP',
				),
			),
			array
			(
				array
				(
					'localaddr' => '192.168.0.1',
					'localport' => 0, // invalid
					'remoteaddr' => '10.0.0.50',
					'remoteport' => 7,
					'protocol' => 'TCP',
				),
			),
			array
			(
				array
				(
					'localaddr' => '192.168.0.1',
					'localport' => 7,
					'remoteaddr' => '10.0.0.50',
					'remoteport' => 65536, // invalid
					'protocol' => 'TCP',
				),
			),
			array
			(
				array
				(
					'localaddr' => '192.168.0.1',
					'localport' => 7,
					'remoteaddr' => '10.0.0.50',
					'remoteport' => 0, // invalid
					'protocol' => 'TCP',
				),
			),
		);
	}
}
