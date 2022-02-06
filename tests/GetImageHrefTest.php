<?php

class GetImageHrefTest extends RTTestCase
{
	const TAG = 'image';
	const NOSUCHTAG = 'no_such_image';

	public static function tearDownAfterClass(): void
	{
		global $image;

		unset ($image[self::myStringStatic (self::TAG, __CLASS__)]);
	}

	/**
	 * @group small
	 * @dataProvider providerUnary
	 */
	public function testUnary ($imgdecl, $output): void
	{
		global $image;

		$imgtag = $this->myString (self::TAG);
		$image[$imgtag] = $imgdecl;
		$this->assertEquals ($output, getImageHREF ($imgtag));
	}

	public function providerUnary(): array
	{
		return array
		(
			array
			(
				array
				(
					# implicit intpath
					'path' => 'pix/tango-user-trash-16x16.png',
					'width' => 16,
					'height' => 16,
				),
				'<img src="?module=chrome&amp;uri=pix/tango-user-trash-16x16.png" border="0" width="16" height="16">',
			),
			array
			(
				array
				(
					'srctype' => 'intpath',
					'path' => 'pix/tango-user-trash-16x16.png',
					'width' => 16,
					'height' => 16,
				),
				'<img src="?module=chrome&amp;uri=pix/tango-user-trash-16x16.png" border="0" width="16" height="16">',
			),
			array
			(
				array
				(
					'srctype' => 'exturl',
					'url' => 'https://racktables.org/img/RackTables-16x16.png',
					'width' => 16,
					'height' => 16,
				),
				'<img src="https://racktables.org/img/RackTables-16x16.png" border="0" width="16" height="16">',
			),
			array
			(
				array
				(
					'srctype' => 'dataurl',
					'data' => 'image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOwA=',
					'width' => 1,
					'height' => 1,
				),
				'<img src="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOwA=" border="0" width="1" height="1">',
			),
		);
	} # providerUnary()

	public function testNullaryRTE(): void
	{
		$this->expectException (RackTablesError::class);
		getImageHREF (self::NOSUCHTAG);
	}

	/**
	 * @group small
	 * @dataProvider providerUnaryRTE
	 */
	public function testUnaryRTE ($imgdecl): void
	{
		global $image;

		$imgtag = $this->myString (self::TAG);
		$image[$imgtag] = $imgdecl;
		$this->expectException (RackTablesError::class);
		getImageHREF ($imgtag);
	}

	public function providerUnaryRTE(): array
	{
		return array
		(
			array
			(
				'srctype' => 'invalid_type',
			),
			array
			(
				# implicit intpath, no path
				# PHPUnit 8.5.21 fails to convey an empty array to the test method.
				'height' => 1,
				'width' => 1,
			),
			array
			(
				'srctype' => 'intpath',
				# no path
			),
			array
			(
				'srctype' => 'exturl',
				# no URL
			),
			array
			(
				'srctype' => 'dataurl',
				# no data
			),
			array
			(
				'srctype' => 'intpath',
				'path' => 'pix/tango-user-trash-16x16.png',
				# no height, no width
			),
			array
			(
				'srctype' => 'intpath',
				'path' => 'pix/tango-user-trash-16x16.png',
				'height' => 16,
				# no width
			),
			array
			(
				'srctype' => 'intpath',
				'path' => 'pix/tango-user-trash-16x16.png',
				'width' => 16,
				# no height
			),
		);
	} # providerUnaryRTE()
}

