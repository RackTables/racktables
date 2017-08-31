<?php

class UserAccountTest extends RTTestCase
{
	const PSWDHASH = 'f7563fd105b011264532ef2d405082015bed948e';
	const REALNAME = 'Test User Account';
	private $user_name;
	private $user_id;

	public function setUp ()
	{
		$this->user_name = $this->myString ('testuser');
		$this->user_id = commitCreateUserAccount ($this->user_name, self::REALNAME, self::PSWDHASH);
	}

	public function tearDown ()
	{
		usePreparedDeleteBlade ('UserAccount', array ('user_id' => $this->user_id));
	}

	/**
	 * @group small
	 */
	public function testNormal ()
	{
		$this->assertArrayHasKey ($this->user_id, getAccountSearchResult ($this->user_name));
		$this->assertEquals ($this->user_id, getUserIDByUsername ($this->user_name));
		$this->assertNull (getUserIDByUsername ('x' . $this->user_name));
		$user = spotEntity ('user', $this->user_id);
		$this->assertEquals ($this->user_name, $user['user_name']);
		$this->assertEquals (self::PSWDHASH, $user['user_password_hash']);
		$this->assertEquals (self::REALNAME, $user['user_realname']);

		$this->user_name = 'x' . $this->user_name;
		commitUpdateUserAccount ($this->user_id, $this->user_name, 'x' . self::REALNAME, sha1 (self::PSWDHASH));

		$this->assertArrayHasKey ($this->user_id, getAccountSearchResult ($this->user_name));
		$this->assertEquals ($this->user_id, getUserIDByUsername ($this->user_name));
		$this->assertNull (getUserIDByUsername ('x' . $this->user_name));
		$user = spotEntity ('user', $this->user_id, TRUE);
		$this->assertEquals ($this->user_name, $user['user_name']);
		$this->assertEquals (sha1 (self::PSWDHASH), $user['user_password_hash']);
		$this->assertEquals ('x' . self::REALNAME, $user['user_realname']);
	}

	/**
	 * @group small
	 * @expectedException RTDatabaseError
	 */
	public function testDuplicate ()
	{
		commitCreateUserAccount ($this->user_name, 'x' . self::REALNAME, sha1 (self::PSWDHASH));
	}
}
