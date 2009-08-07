<?php

class ormSQLGeneration_test extends unit_test {
	
	public function setup() {
		
		config::set('database.config.group', 'localhost');
		config::set('database.config.localhost', array(
				'username'     => 'root',
				'password'     => 'root',
				'hostname'     => 'localhost',
				'database' => 'test'
		));

		config::set('schema.company.has_one', array('companytype'));
		config::set('schema.company.has_many', array('user'));
		config::set('schema.company.has_and_belongs_to_many', array('category'));
		config::set('schema.user.has_one', array('usertype', 'email'));
		config::set('schema.email.has_one', array('emailtype'));
		config::set('schema.companytype.belongs_to_many', 'company');
		
		config::set('schema.company.secondaryKey', 'name');
		
	}
	
	public function basic_test() {
		
		$query = ORM::factory('company');
		$this->assertTrue(is_object($query));
		
	}
	
	public function basicQuery_test() {

		$query = ORM::factory('company')->sqlMode(true)->fetch();
		$this->assertEquals($query, 'SELECT company.* FROM company');
		
	}
	
	public function basicOrder_test() {
		$query = ORM::factory('company')->order('company.name DESC')->sqlMode(true)->fetch();
		$this->assertEquals($query, 'SELECT company.* FROM company ORDER BY company.name DESC');
	}
	
	public function multiOrder_test() {
		$query = ORM::factory('company')->order('company.name ASC')->order('company.ID DESC')->sqlMode(true)->fetch();
		$this->assertEquals($query, 'SELECT company.* FROM company ORDER BY company.name ASC, company.ID DESC');
	}
	
	public function basicGroup_test() {
		$query = ORM::factory('company')->group('company.name')->sqlMode(true)->fetch();
		$this->assertEquals($query, 'SELECT company.* FROM company GROUP BY company.name');
	}
	
	public function multiGroup_test() {
		$query = ORM::factory('company')->group('company.name')->group('company.ID')->sqlMode(true)->fetch();
		$this->assertEquals($query, 'SELECT company.* FROM company GROUP BY company.name, company.ID');
	}
	
	public function distinctQuery_test() {
		$query = ORM::factory('company')->distinct()->sqlMode(true)->fetch();
		$this->assertEquals($query, 'SELECT DISTINCT company.* FROM company');
	}
	
	public function calcFoundQuery_test() {
		$query = ORM::factory('company')->calcFound()->sqlMode(true)->fetch();
		$this->assertEquals($query, 'SELECT SQL_CALC_FOUND_ROWS company.* FROM company');
	}
	
	public function has_one_WithQuery_test() {
		
		$query = ORM::factory('company')->with('companytype')->sqlMode(true)->fetch();
		//single join created for the many-to-one or one-to-one relationship
		$this->assertEquals($query, 'SELECT company.*, companytype.* FROM company LEFT JOIN companytype ON company.companytype_ID = companytype.ID');
		
	}
	
	public function has_many_WithQuery_test() {
		
		$query = ORM::factory('company')->with('user')->sqlMode(true)->fetch();
		//single join created for the one-to-many relationship
		$this->assertEquals($query, 'SELECT company.*, user.* FROM company LEFT JOIN user ON company.ID = user.company_ID');
		
	}
	
	public function belongs_to_many_WithQuery_test() {
		
		$query = ORM::factory('companytype')->with('company')->sqlMode(true)->fetch();
		//single join created for the one-to-many relationship
		$this->assertEquals($query, 'SELECT companytype.*, company.* FROM companytype LEFT JOIN company ON company.companytype_ID = companytype.ID');
		
	}
	
	public function has_and_belongs_to_many_WithQuery_test() {
		
		$query = ORM::factory('company')->with('category')->sqlMode(true)->fetch();
		//should use a junction table and then join through that
		//two joins created.		
		$this->assertEquals($query, 'SELECT company.*, category.* FROM company LEFT JOIN category_company ON company.ID = category_company.company_ID LEFT JOIN category ON category_company.category_ID = category.ID');
		
	}
	
	public function multiWith_test() {
		$query = ORM::factory('company')->with('companytype', 'category', 'user')->sqlMode(true)->fetch();		
		$this->assertEquals($query, 'SELECT company.*, companytype.*, category.*, user.* FROM company LEFT JOIN companytype ON company.companytype_ID = companytype.ID LEFT JOIN category_company ON company.ID = category_company.company_ID LEFT JOIN category ON category_company.category_ID = category.ID LEFT JOIN user ON company.ID = user.company_ID');
	}
	
	public function nestedWith_test() {
		$query = ORM::factory('company')->with(array('user', 'usertype'))->sqlMode(true)->fetch();		
		$this->assertEquals($query, 'SELECT company.*, user.*, usertype.* FROM company LEFT JOIN user ON company.ID = user.company_ID LEFT JOIN usertype ON user.usertype_ID = usertype.ID');
	}
	
	public function fullQuery_test() {
		
		$query = ORM::factory('company')->with('companytype', 'category', array('user', 'usertype', array('email', 'emailtype')))->order('user.name DESC')->calcFound()->sqlMode(true)->fetch('massive');		
		$this->assertEquals($query, 'SELECT SQL_CALC_FOUND_ROWS company.*, companytype.*, category.*, user.*, usertype.*, email.*, emailtype.* FROM company LEFT JOIN companytype ON company.companytype_ID = companytype.ID LEFT JOIN category_company ON company.ID = category_company.company_ID LEFT JOIN category ON category_company.category_ID = category.ID LEFT JOIN user ON company.ID = user.company_ID LEFT JOIN usertype ON user.usertype_ID = usertype.ID LEFT JOIN email ON user.email_ID = email.ID LEFT JOIN emailtype ON email.emailtype_ID = emailtype.ID WHERE company.name = "massive" ORDER BY user.name DESC');
		
	}
	
}
