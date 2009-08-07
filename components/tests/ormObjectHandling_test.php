<?php

class ormObjectHandling_test extends unit_test {
	
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
		
		config::set('schema.company.fields', array('ID', 'name', 'companytype_ID', 'created_ts', 'modified_ts'));
		config::set('schema.companytype.fields', array('ID', 'value', 'created_ts', 'modified_ts'));
		
		config::set('schema.company.secondaryKey', 'name');
		
	}
	
	private function standardAssumptionForCompanyQuery($query) {
		$this->assertTrue(is_object($query));
		$this->assertEquals($query->count(), 2);
		
		$this->assertEquals($query[0]->name, 'skookum');
		$this->assertEquals($query[1]->name, 'wrenchlabs');
	}
	
	public function basic_test() {
		
		$query = ORM::factory('company');
		$this->assertTrue(is_object($query));
		
	}
	
	public function basicQuery_test() {

		$query = ORM::factory('company')->fetch();
				
		$this->standardAssumptionForCompanyQuery($query);
	}
	
	public function has_one_WithQuery_test() {
		
		$query = ORM::factory('company')->with('companytype')->fetch();
				
		$this->standardAssumptionForCompanyQuery($query);
		
		$this->assertEquals($query[0]->companytype->value, 'small');
		
		
	
		/*
		$query->companytype = ORM::object('companytype');
		$query->companytype->ID = '5';
		$query->companytype->name = 'woot';
		/*
		$query->ID = 3;
		$query->name = 'chris';
		$query->category = ORM::factory('category')->fetch('super');

		$query[1] = clone $query[0];
		$query[1]->ID = 1;
		$query[1]->name = 'robert';


		foreach($query as $key=>$value) {
			profiler::debug($value);
		}

		profiler::debug($query);
		*/
		//$query->save();

		/*$query = ORM::factory('company')
									->with('companytype', 'user')
									->where('company.name = "?"', 'woot')
									->fetch('skookum');
									*/
	}
	
}
