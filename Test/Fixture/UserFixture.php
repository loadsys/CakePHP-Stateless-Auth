<?php
/**
 * UserFixture
 *
 */
class UserFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'token' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'username' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'password' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'first_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 180, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'last_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 180, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'shift' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'is_operator' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'is_tech' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'creator_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'modifier_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'last_login_at' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '7d5b22bd-fc92-11e3-b153-080027dec79b',
			'token' => '40e9a32090f03eed13a6080e0b21a58d',
			'username' => 'test',
			'password' => '49342febad93ceee33ef5635e750aacd163b2106', // == `test`
			'first_name' => 'Test',
			'last_name' => 'User',
			'shift' => null,
			'is_operator' => 0,
			'is_tech' => 0,
			'creator_id' => '7d5b22bd-fc92-11e3-b153-080027dec79b',
			'created' => '2014-06-24 17:14:10',
			'modifier_id' => '7d5b22bd-fc92-11e3-b153-080027dec79b',
			'modified' => '2014-06-24 17:14:28',
			'last_login_at' => null,
		),
		array(
			'id' => 'c64ddc9c-7193-11e4-b74c-000c290352bb',
			'token' => 'bb0a91cfa4f3b703531c1dc4f5f64b89',
			'username' => 'blowfish',
			'password' => '$2a$10$GETe7PdF2xekpYqdVfK5EOGi9MsBw6MbNI.NLZkLQoDipykAy3h76',
			'first_name' => 'Blowfish',
			'last_name' => 'Hash',
			'shift' => null,
			'is_operator' => 0,
			'is_tech' => 0,
			'creator_id' => 1,
			'created' => '2014-06-04 13:49:06',
			'modifier_id' => 1,
			'modified' => '2014-06-04 13:49:06',
			'last_login_at' => '2011-06-04 13:49:06', // Will be udpated to NOW() when fixure is instantiated.
		),
		array(
			'id' => 'aaabbbcc-7193-11e4-b74c-000c290352bb',
			'token' => 'cc0a91cfa4f3b703531c1dc4f5f64b89',
			'username' => 'fulladmin',
			'password' => '$2a$10$GETe7PdF2xekpYqdVfK5EOGi9MsBw6MbNI.NLZkLQoDipykAy3h76',
			'first_name' => 'Full Admin',
			'last_name' => 'Access',
			'shift' => null,
			'is_operator' => 0,
			'is_tech' => 0,
			'creator_id' => 1,
			'created' => '2014-06-04 13:49:06',
			'modifier_id' => 1,
			'modified' => '2014-06-04 13:49:06',
			'last_login_at' => '2011-06-04 13:49:06', // Will be udpated to NOW() when fixure is instantiated.
		),
	);

	public function __construct() {
		parent::__construct();
		$this->records[1]['last_login_at'] = (new DateTime())->format('Y-m-d H:i:s');
		$this->records[2]['last_login_at'] = (new DateTime())->format('Y-m-d H:i:s');
	}
}
