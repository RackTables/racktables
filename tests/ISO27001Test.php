<?php

class ISO27001 extends RTTestCase
{
	protected static $complete_valuation = 7.7;
	protected $complete_id, $incomplete_id;
	protected $cga_id, $cgb_id;
	protected $caa_id, $cab_id, $cba_id, $cbb_id, $cbc_id;
	protected $agroup_id, $aowner_id, $amaint_id;

	public function setUp ()
	{
		// the general configuration
		usePreparedInsertBlade ('ISO27001AssetGroup', array ('name' => 'a test asset group'));
		$this->agroup_id = lastInsertID();
		usePreparedInsertBlade ('ISO27001AssetOwner', array ('name' => 'a test asset owner'));
		$this->aowner_id = lastInsertID();
		usePreparedInsertBlade ('ISO27001AssetMaintainer', array ('name' => 'a test asset maintainer'));
		$this->amaint_id = lastInsertID();

		usePreparedInsertBlade ('ISO27001CriterionGroup', array ('name' => 'test criterion group A'));
		$this->cga_id = lastInsertID();
		usePreparedInsertblade ('ISO27001Criterion', array ('cgroup_id' => $this->cga_id, 'weight' => 0.75, 'name' => 'test criterion A.a'));
		$this->caa_id = lastInsertID();
		usePreparedInsertblade ('ISO27001Criterion', array ('cgroup_id' => $this->cga_id, 'weight' => 1.25, 'name' => 'test criterion A.b'));
		$this->cab_id = lastInsertID();
		usePreparedInsertBlade ('ISO27001CriterionGroupValueSet', array ('cgroup_id' => $this->cga_id, 'value' => 1));
		usePreparedInsertBlade ('ISO27001CriterionGroupValueSet', array ('cgroup_id' => $this->cga_id, 'value' => 2));
		usePreparedInsertBlade ('ISO27001CriterionGroupValueSet', array ('cgroup_id' => $this->cga_id, 'value' => 3));

		usePreparedInsertBlade ('ISO27001CriterionGroup', array ('name' => 'test criterion group B'));
		$this->cgb_id = lastInsertID();
		usePreparedInsertblade ('ISO27001Criterion', array ('cgroup_id' => $this->cgb_id, 'weight' => 1.75, 'name' => 'test criterion B.a'));
		$this->cba_id = lastInsertID();
		usePreparedInsertblade ('ISO27001Criterion', array ('cgroup_id' => $this->cgb_id, 'weight' => 1.0, 'name' => 'test criterion B.b'));
		$this->cbb_id = lastInsertID();
		usePreparedInsertblade ('ISO27001Criterion', array ('cgroup_id' => $this->cgb_id, 'weight' => 1.25, 'name' => 'test criterion B.c'));
		$this->cbc_id = lastInsertID();
		usePreparedInsertBlade ('ISO27001CriterionGroupValueSet', array ('cgroup_id' => $this->cgb_id, 'value' => 0));
		usePreparedInsertBlade ('ISO27001CriterionGroupValueSet', array ('cgroup_id' => $this->cgb_id, 'value' => 1));

		// two assets
		$this->complete_id = commitAddObject ('', '', 4, '');
		usePreparedinsertBlade
		(
			'ISO27001Asset',
			array
			(
				'object_id' => $this->complete_id,
				'agroup_id' => $this->agroup_id,
				'aowner_id' => $this->aowner_id,
				'amaint_id' => $this->amaint_id,
				'criticality' => 1.4,
			)
		);
		$this->incomplete_id = commitAddObject ('', '', 4, '');
		usePreparedinsertBlade
		(
			'ISO27001Asset',
			array
			(
				'object_id' => $this->incomplete_id,
				'agroup_id' => $this->agroup_id,
				'aowner_id' => $this->aowner_id,
				'amaint_id' => $this->amaint_id,
				'criticality' => 2.5,
			)
		);

		// particular criteria values
		commitObjectISO27001CValues
		(
			$this->complete_id,
			array
			(
				$this->caa_id => 1,
				$this->cab_id => 2,
				$this->cba_id => 0,
				$this->cbb_id => 1,
				$this->cbc_id => 1,
			)
		);
		commitObjectISO27001CValues
		(
			$this->incomplete_id,
			array
			(
				$this->caa_id => 3,
				$this->cba_id => 0,
			)
		);
	}

	public function tearDown ()
	{
		$ids = array ($this->cga_id, $this->cgb_id);
		usePreparedDeleteBlade ('ISO27001AssetCriterionValue', array ('cgroup_id' => $ids));
		usePreparedDeleteBlade ('ISO27001Criterion', array ('cgroup_id' => $ids));
		usePreparedDeleteBlade ('ISO27001CriterionGroupValueSet', array ('cgroup_id' => $ids));
		usePreparedDeleteBlade ('ISO27001CriterionGroup', array ('id' => $ids));

		commitDeleteObject ($this->complete_id);
		commitDeleteObject ($this->incomplete_id);

		usePreparedDeleteBlade ('ISO27001AssetGroup', array ('id' => $this->agroup_id));
		usePreparedDeleteBlade ('ISO27001AssetOwner', array ('id' => $this->aowner_id));
		usePreparedDeleteBlade ('ISO27001AssetMaintainer', array ('id' => $this->amaint_id));
	}

	/**
	 * @group small
	 */
	public function testCompleteAsset ()
	{
		$a = getISO27001AssetInfo ($this->complete_id);
		$valuation = getISO27001AssetValuation (getISO27001Configuration(), getObjectISO27001CValues ($this->complete_id));
		$this->assertEquals (self::$complete_valuation, $a['criticality'] * $valuation);
	}

	/**
	 * @group small
	 * @expectedException InvalidArgException
	 */
	public function testIncompleteAsset ()
	{
		// Throws an exception because not every criterion has a value assigned.
		getISO27001AssetValuation (getISO27001Configuration(), getObjectISO27001CValues ($this->incomplete_id));
	}
}
