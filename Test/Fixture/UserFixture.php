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
		'last_login_at' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'username' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'password' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'first_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 180, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'last_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 180, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'is_tech' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
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
			'last_login_at' => null,
			'password' => '49342febad93ceee33ef5635e750aacd163b2106', // == `test`
			'first_name' => 'Test',
			'last_name' => 'User',
			'is_tech' => 0,
		),
		array(
			'id' => 'c64ddc9c-7193-11e4-b74c-000c290352bb',
			'token' => 'bb0a91cfa4f3b703531c1dc4f5f64b89',
			'last_login_at' => '2011-06-04 13:49:06', // Will be udpated to NOW() when fixure is instantiated.
			'username' => 'blowfish',
			'password' => '$2a$10$GETe7PdF2xekpYqdVfK5EOGi9MsBw6MbNI.NLZkLQoDipykAy3h76',
			'first_name' => 'Blowfish',
			'last_name' => 'Hash',
			'is_tech' => 0,
		),
		array(
			'id' => 'aaabbbcc-7193-11e4-b74c-000c290352bb',
			'token' => 'cc0a91cfa4f3b703531c1dc4f5f64b89',
			'last_login_at' => '2011-06-04 13:49:06', // Will be udpated to NOW() when fixure is instantiated.
			'username' => 'fulladmin',
			'password' => '$2a$10$GETe7PdF2xekpYqdVfK5EOGi9MsBw6MbNI.NLZkLQoDipykAy3h76',
			'first_name' => 'Full Admin',
			'last_name' => 'Access',
			'is_tech' => 0,
		),
	);

	/**
	 * Apply any necessary runtime settings to the data.
	 */
	public function __construct() {
		parent::__construct();
		$this->records[1]['last_login_at'] = (new DateTime())->format('Y-m-d H:i:s');
		$this->records[2]['last_login_at'] = (new DateTime())->format('Y-m-d H:i:s');
	}
}
