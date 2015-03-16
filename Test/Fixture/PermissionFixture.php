<?php
/**
 * PermissionFixture
 *
 */
class PermissionFixture extends CakeTestFixture {

	/**
	 * Fields
	 *
	 * @var array
	 */
	public $fields = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'UUID primary key identifying Privileges granted to a User.', 'charset' => 'utf8'),
		'user_id' => array('type' => 'string', 'null' => false, 'default' => '00000000-0000-0000-0000-000000000000', 'length' => 36, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'UUID User.id for this Permission.', 'charset' => 'utf8'),
		'privilege_id' => array('type' => 'string', 'null' => false, 'default' => '00000000-0000-0000-0000-000000000000', 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'UUID Privilege.id granted to the User for this Permission.', 'charset' => 'utf8'),
		'can_write' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'Bool when false User can only read the associated area. When true, can add/edit/delete as well.'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'Date and time of record creation.'),
		'creator_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'UUID of User that created the record.', 'charset' => 'utf8'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'Date and time of last modification.'),
		'modifier_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'UUID of last User to modify the record.', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'user_id' => array('column' => 'user_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = array(
		// test user - maintenance - can write
		array(
			'id' => '54187198-d98c-4859-9262-221e9f49749c',
			'user_id' => '7d5b22bd-fc92-11e3-b153-080027dec79b',
			'privilege_id' => '54187d6a-9f44-49df-bc7c-24179f49749c',
			'can_write' => 1,
			'created' => '2014-09-16 17:21:28',
			'creator_id' => '7d5b22bd-fc92-11e3-b153-080027dec79b',
			'modified' => '2014-09-16 17:21:28',
			'modifier_id' => '7d5b22bd-fc92-11e3-b153-080027dec79b'
		),
		// test user - certificates - can not write
		array(
			'id' => '6bbfd5e4-65d0-11e4-a97c-08002786663d',
			'user_id' => '7d5b22bd-fc92-11e3-b153-080027dec79b',
			'privilege_id' => '54187d75-97f8-4e76-a2c3-24329f49749c',
			'can_write' => 0,
			'created' => '2014-09-16 17:21:28',
			'creator_id' => '7d5b22bd-fc92-11e3-b153-080027dec79b',
			'modified' => '2014-09-16 17:21:28',
			'modifier_id' => '7d5b22bd-fc92-11e3-b153-080027dec79b'
		),
		// full admin user - admin - can write
		array(
			'id' => '7bbfd5e4-65d0-11e4-a97c-08002786663d',
			'user_id' => 'aaabbbcc-7193-11e4-b74c-000c290352bb',
			'privilege_id' => '54187d5d-9ed4-4f95-8357-23e19f49749c',
			'can_write' => 1,
			'created' => '2014-09-16 17:21:28',
			'creator_id' => '7d5b22bd-fc92-11e3-b153-080027dec79b',
			'modified' => '2014-09-16 17:21:28',
			'modifier_id' => '7d5b22bd-fc92-11e3-b153-080027dec79b'
		),
	);
}
