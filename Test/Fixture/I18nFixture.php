<?php
/**
 * I18nFixture
 *
 */
class I18nFixture extends CakeTestFixture {

	/**
	 * Table name
	 *
	 * @var	string
	 */
	public $table = 'i18n';

	/**
	 * Fields
	 *
	 * @var	array
	 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => false, 'key' => 'primary'),
		'locale' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 6, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'model' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'foreign_key' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'Might contain UUID or INT primary key from another table.', 'charset' => 'utf8'),
		'field' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'content' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'locale' => array('column' => 'locale', 'unique' => 0),
			'model' => array('column' => 'model', 'unique' => 0),
			'row_id' => array('column' => 'foreign_key', 'unique' => 0),
			'field' => array('column' => 'field', 'unique' => 0)
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
			'id' => '1',
			'locale' => 'eng',
			'model' => 'Privilege',
			'foreign_key' => '54187d55-4d2c-4f18-aeaf-23c69f49749c',
			'field' => 'name',
			'content' => 'Lab'
		),
		array(
			'id' => '2',
			'locale' => 'eng',
			'model' => 'Privilege',
			'foreign_key' => '54187d5d-9ed4-4f95-8357-23e19f49749c',
			'field' => 'name',
			'content' => 'Admin'
		),
		array(
			'id' => '3',
			'locale' => 'eng',
			'model' => 'Privilege',
			'foreign_key' => '54187d64-54c0-4221-b24a-23fc9f49749c',
			'field' => 'name',
			'content' => 'Reports'
		),
		array(
			'id' => '4',
			'locale' => 'eng',
			'model' => 'Privilege',
			'foreign_key' => '54187d6a-9f44-49df-bc7c-24179f49749c',
			'field' => 'name',
			'content' => 'Maintenance'
		),
		array(
			'id' => '5',
			'locale' => 'eng',
			'model' => 'Privilege',
			'foreign_key' => '54187d75-97f8-4e76-a2c3-24329f49749c',
			'field' => 'name',
			'content' => 'Certificates'
		),
		array(
			'id' => '6',
			'locale' => 'eng',
			'model' => 'Privilege',
			'foreign_key' => '54187d7c-5334-41b2-ac85-244d9f49749c',
			'field' => 'name',
			'content' => 'Analyses'
		),
		array(
			'id' => '7',
			'locale' => 'eng',
			'model' => 'Privilege',
			'foreign_key' => '54187d84-c96c-499a-9ef1-24689f49749c',
			'field' => 'name',
			'content' => 'Customers'
		),
		array(
			'id' => '8',
			'locale' => 'eng',
			'model' => 'Privilege',
			'foreign_key' => '54187d8e-da94-4e59-b8fd-24839f49749c',
			'field' => 'name',
			'content' => 'Grades'
		),
		array(
			'id' => '9',
			'locale' => 'eng',
			'model' => 'Privilege',
			'foreign_key' => '54187d95-4eb8-4e8f-a6a8-249e9f49749c',
			'field' => 'name',
			'content' => 'Resin Products'
		),
		array(
			'id' => '10',
			'locale' => 'eng',
			'model' => 'Privilege',
			'foreign_key' => '54187d9b-e544-4a27-b0b9-24b99f49749c',
			'field' => 'name',
			'content' => 'Stacks'
		),
		array(
			'id' => '11',
			'locale' => 'eng',
			'model' => 'Privilege',
			'foreign_key' => '54187da5-7d20-4db9-9ee6-24d49f49749c',
			'field' => 'name',
			'content' => 'Users'
		),
		array(
			'id' => '12',
			'locale' => 'eng',
			'model' => 'Privilege',
			'foreign_key' => '54187dae-dbfc-4f16-9dd0-24ef9f49749c',
			'field' => 'name',
			'content' => 'Grade Certificates'
		),
	);

}
