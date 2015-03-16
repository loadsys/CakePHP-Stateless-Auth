<?php
/**
 * PrivilegeFixture
 *
 */
class PrivilegeFixture extends CakeTestFixture {

	/**
	 * Fields
	 *
	 * @var	array
	 */
	public $fields = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'UUID primary key identifying the Privilege.', 'charset' => 'utf8'),
		'slug' => array('type' => 'string', 'null' => false, 'default' => 'none', 'length' => 50, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'comment' => 'Used in all code in place of IDs for readability.', 'charset' => 'utf8'),
		// NOTE! `name` is a Translatable field! Make sure you set
		// `Privilege->locale = array('eng')` in your tests!
		// The field is left here as a warning and reference.
		// 'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'legacy_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'The primary interger ID for the record as imported from the legacy application.'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'slug' => array('column' => 'slug', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	/**
	 * Records
	 *
	 * @var	array
	 */
	public $records = array(
		array(
			'id' => '54187d55-4d2c-4f18-aeaf-23c69f49749c',
			'slug' => 'lab',
			// 'name' => 'Lab',
			'legacy_id' => '1'
		),
		array(
			'id' => '54187d5d-9ed4-4f95-8357-23e19f49749c',
			'slug' => 'admin',
			// 'name' => 'Admin',
			'legacy_id' => '2'
		),
		array(
			'id' => '54187d64-54c0-4221-b24a-23fc9f49749c',
			'slug' => 'reports',
			// 'name' => 'Reports',
			'legacy_id' => '3'
		),
		array(
			'id' => '54187d6a-9f44-49df-bc7c-24179f49749c',
			'slug' => 'maintenance',
			// 'name' => 'Maintenance',
			'legacy_id' => '4'
		),
		array(
			'id' => '54187d75-97f8-4e76-a2c3-24329f49749c',
			'slug' => 'certificates',
			// 'name' => 'Certificates',
			'legacy_id' => '20'
		),
		array(
			'id' => '54187d7c-5334-41b2-ac85-244d9f49749c',
			'slug' => 'analyses',
			// 'name' => 'Analyses',
			'legacy_id' => '17'
		),
		array(
			'id' => '54187d84-c96c-499a-9ef1-24689f49749c',
			'slug' => 'customers',
			// 'name' => 'Customers',
			'legacy_id' => '12'
		),
		array(
			'id' => '54187d8e-da94-4e59-b8fd-24839f49749c',
			'slug' => 'grades',
			// 'name' => 'Grades',
			'legacy_id' => '13'
		),
		array(
			'id' => '54187d95-4eb8-4e8f-a6a8-249e9f49749c',
			'slug' => 'resin-products',
			// 'name' => 'Resin Products',
			'legacy_id' => '14'
		),
		array(
			'id' => '54187d9b-e544-4a27-b0b9-24b99f49749c',
			'slug' => 'stacks',
			// 'name' => 'Stacks',
			'legacy_id' => '15'
		),
		array(
			'id' => '54187da5-7d20-4db9-9ee6-24d49f49749c',
			'slug' => 'users',
			// 'name' => 'Users',
			'legacy_id' => '16'
		),
		array(
			'id' => '54187dae-dbfc-4f16-9dd0-24ef9f49749c',
			'slug' => 'grade-certificates',
			// 'name' => 'Grade Certificates',
			'legacy_id' => '23'
		),
	);

}
