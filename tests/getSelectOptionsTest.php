<?php

class getSelectOptionsTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider provider
	 */
	public function testStringInsertHrefs ($input, $output)
	{
		$this->assertEquals ($output, getSelectOptions ($input['options'], $input['selected_id']));
	}

	public function provider ()
	{
		return array
		(
			array
			(
				array
				(
					'options' => array
					(
						1 => 'one',
						2 => 'two',
						3 => 'three',
					),
					'selected_id' => NULL,
				),
				'<option value=\'1\'>one</option>' .
				'<option value=\'2\'>two</option>' .
				'<option value=\'3\'>three</option>'
			),
			array
			(
				array
				(
					'options' => array
					(
						1 => 'one',
						2 => 'two',
						3 => 'three',
					),
					'selected_id' => 2,
				),
				'<option value=\'1\'>one</option>' .
				'<option value=\'2\' selected>two</option>' .
				'<option value=\'3\'>three</option>'
			),
			array
			(
				array
				(
					'options' => array
					(
						1 => 'one',
						2 => 'two',
						3 => 'three',
					),
					'selected_id' => array(),
				),
				'<option value=\'1\'>one</option>' .
				'<option value=\'2\'>two</option>' .
				'<option value=\'3\'>three</option>'
			),
			array
			(
				array
				(
					'options' => array
					(
						1 => 'one',
						2 => 'two',
						3 => 'three',
					),
					'selected_id' => array (2, 3),
				),
				'<option value=\'1\'>one</option>' .
				'<option value=\'2\' selected>two</option>' .
				'<option value=\'3\' selected>three</option>'
			),
			array
			(
				array
				(
					'options' => array
					(
						1 => '<one>',
						2 => '&two;',
						3 => '\'"three"\'',
						4 => '    ',
					),
					'selected_id' => NULL,
				),
				'<option value=\'1\'>&lt;one&gt;</option>' .
				'<option value=\'2\'>&amp;two;</option>' .
				'<option value=\'3\'>&#039;&quot;three&quot;&#039;</option>' .
				'<option value=\'4\'>    </option>'
			),
		);
	}
}
?>
