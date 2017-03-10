<?php

class DictionaryAttributeTest extends PHPUnit_Framework_TestCase
{
	protected
		$attr_types,
		$new_chapter_id, $new_word_ids,
		$new_object_types, $new_object_ids,
		$new_attr_ids,
		$AM_count, $AM_attrs,
		$AV_count;

	public function setUp ()
	{
		$sig = sprintf ('%s_%s', get_class(), getmypid());

		$this->attr_types = array();
		foreach (array ('string', 'uint', 'float', 'dict', 'date') as $attr_type)
		{
			usePreparedInsertBlade ('Attribute', array ('type' => $attr_type, 'name' => "${attr_type}_${sig}"));
			$this->attr_types[lastInsertID()] = $attr_type;
		}

		usePreparedInsertBlade ('Chapter', array ('name' => $sig));
		$this->new_chapter_id = lastInsertId();

		$this->new_word_ids = array();
		foreach (array ('A', 'B', 'C', 'D', 'E') as $word)
		{
			usePreparedInsertBlade ('Dictionary', array ('chapter_id' => $this->new_chapter_id, 'dict_value' => "${word} ${sig}"));
			$this->new_word_ids[$word] = lastInsertId();
		}

		$this->new_object_types = array();
		foreach (array ('none', 'obj', 'map', 'obj_map', 'obj_map_val') as $code)
		{
			usePreparedInsertBlade ('Dictionary', array ('chapter_id' => CHAP_OBJTYPE, 'dict_value' => "${code} ${sig}"));
			$this->new_object_types[$code] = lastInsertId();
		}

		$this->new_object_ids = array();
		foreach ($this->new_object_types as $code => $type_id)
			if ($code != 'none' && $code != 'map')
				$this->new_object_ids[$code] = commitAddObject ("${code} ${sig}", NULL, $type_id, NULL);

		$this->new_attr_ids = array();
		foreach (array ('F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O') as $each)
		{
			usePreparedInsertBlade ('Attribute', array ('type' => 'dict', 'name' => "${each} ${sig}"));
			$this->new_attr_ids[$each] = lastInsertId();
		}

		$AM_values = array
		(
			array ($this->new_attr_ids['F'], $this->new_object_types['map'], $this->new_chapter_id),
			array ($this->new_attr_ids['G'], $this->new_object_types['map'], $this->new_chapter_id),
			array ($this->new_attr_ids['H'], $this->new_object_types['obj_map'], $this->new_chapter_id),
			array ($this->new_attr_ids['I'], $this->new_object_types['obj_map'], $this->new_chapter_id),
			array ($this->new_attr_ids['J'], $this->new_object_types['obj_map'], $this->new_chapter_id),
			array ($this->new_attr_ids['K'], $this->new_object_types['obj_map'], $this->new_chapter_id),
			array ($this->new_attr_ids['L'], $this->new_object_types['obj_map'], $this->new_chapter_id),
			array ($this->new_attr_ids['M'], $this->new_object_types['obj_map_val'], $this->new_chapter_id),
			array ($this->new_attr_ids['N'], $this->new_object_types['obj_map_val'], $this->new_chapter_id),
			array ($this->new_attr_ids['O'], $this->new_object_types['obj_map_val'], $this->new_chapter_id),
		);
		$this->AM_count = array();
		$this->AM_attrs = array();
		foreach ($AM_values as $each)
		{
			call_user_func_array ('commitSupplementAttrMap', $each);
			$this->AM_count[$each[1]] = array_key_exists ($each[1], $this->AM_count) ? $this->AM_count[$each[1]] + 1 : 1;
			if (! array_key_exists ($each[1], $this->AM_attrs))
				$this->AM_attrs[$each[1]] = array();
			$this->AM_attrs[$each[1]][] = $each[0];
		}

		$AV_values = array
		(
			array ($this->new_object_ids['obj_map_val'], $this->new_attr_ids['M'], $this->new_word_ids['A']),
			array ($this->new_object_ids['obj_map_val'], $this->new_attr_ids['N'], $this->new_word_ids['B']),
			array ($this->new_object_ids['obj_map_val'], $this->new_attr_ids['O'], $this->new_word_ids['C']),
		);
		$this->AV_count = array();
		foreach ($AV_values as $each)
		{
			call_user_func_array ('commitUpdateAttrValue', $each);
			$this->AV_count[$each[2]] = array_key_exists ($each[2], $this->AV_count) ? $this->AV_count[$each[2]] + 1 : 1;
		}
	} // setUp()

	/**
	 * @group small
	 */
	public function testGetAttrType ()
	{
		foreach ($this->attr_types as $attr_id => $attr_type)
			$this->assertEquals ($attr_type, getAttrType ($attr_id));
	}

	/**
	 * @group small
	 */
	public function testGetObjTypeAttrMap ()
	{
		foreach ($this->AM_attrs as $objtype_id => $attr_ids)
		{
			$am = getObjTypeAttrMap ($objtype_id);
			$this->assertEquals ($attr_ids, array_keys ($am));
			foreach ($am as $each)
			{
				$this->assertEquals ('dict', $each['type']);
				$this->assertEquals ($this->new_chapter_id, $each['chapter_id']);
			}
		}
	}

	/**
	 * @group small
	 */
	public function testReadChapter ()
	{
		$this->assertEquals (array_values ($this->new_word_ids), array_keys (readChapter ($this->new_chapter_id, 'r')));
		// The object types chapter always exists and always contains record.
		$this->assertGreaterThan (0, count (readChapter (CHAP_OBJTYPE)));
	}

	/**
	 * @group small
	 * @expectedException EntityNotFoundException
	 */
	public function testReadChapterNE ()
	{
		readChapter (-1);
	}

	/**
	 * @group small
	 */
	public function testGetChapterRefc ()
	{
		$refc = getChapterRefc (CHAP_OBJTYPE, $this->new_object_types);
		// An object type with no objects and no other references.
		$this->assertEquals (0, $refc[$this->new_object_types['none']]);
		// An object type for that one object exists and there are no other references.
		$this->assertEquals (1, $refc[$this->new_object_types['obj']]);
		// An object type with only AttributeMap references.
		$this->assertEquals ($this->AM_count[$this->new_object_types['map']], $refc[$this->new_object_types['map']]);
		// An object type with one existing object and AttributeMap references.
		$this->assertEquals (1 + $this->AM_count[$this->new_object_types['obj_map']], $refc[$this->new_object_types['obj_map']]);
		// An object type with one existing object, AttributeMap and AttributeValue references.
		// Could be 1 + two times the amount of attributes, but as far as the
		// current revision of getChapterRefc() goes it is not.
		$this->assertEquals (1 + $this->AM_count[$this->new_object_types['obj_map_val']], $refc[$this->new_object_types['obj_map_val']]);

		// The custom chapter contents goes through a different code path.
		$refc = getChapterRefc ($this->new_chapter_id, $this->new_word_ids);
		foreach ($this->AV_count as $dict_key => $use_count)
			$this->assertEquals ($use_count, $refc[$dict_key]);
	}

	/**
	 * @group small
	 */
	public function testGetChapterList ()
	{
		$cl = getChapterList();
		$this->assertArrayHasKey ($this->new_chapter_id, $cl);
		$subset = array ('id' => $this->new_chapter_id, 'sticky' => 'no', 'wordc' => count ($this->new_word_ids));
		$this->assertArraySubset ($subset, $cl[$this->new_chapter_id]);
	}

	public function tearDown ()
	{
		foreach ($this->new_object_ids as $each)
			commitDeleteObject ($each); // includes AttributeValue
		usePreparedDeleteBlade ('AttributeMap', array ('attr_id' => $this->new_attr_ids));
		usePreparedDeleteBlade ('Attribute', array ('id' => $this->new_attr_ids));
		usePreparedDeleteBlade ('Dictionary', array ('dict_key' => $this->new_object_types));
		usePreparedDeleteBlade ('Dictionary', array ('dict_key' => $this->new_word_ids));
		usePreparedDeleteBlade ('Chapter', array ('id' => $this->new_chapter_id));

		usePreparedDeleteBlade ('Attribute', array ('id' => array_keys ($this->attr_types)));
	}
}

?>
