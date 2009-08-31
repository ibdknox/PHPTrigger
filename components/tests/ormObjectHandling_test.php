<?php

class ormObjectHandling_test extends unit_test {
	
	public function setup() {

        ORM::init();
		
		config::set('database.config.group', 'localhost');
		config::set('database.config.localhost', array(
				'username'     => 'root',
				'password'     => 'root',
				'hostname'     => 'localhost',
				'database' => 'test'
		));

        config::set("schema.user", array( "address" => RelTypes::HasMany, 'email' => RelTypes::HasOne, 'post' => RelTypes::HasMany ));
        config::set("schema.address", array( "addresstype" => RelTypes::RefsOne ) );
        config::set("schema.email", array( "type" => RelTypes::HasOne ) );
        config::set("schema.post", array( "tag" => RelTypes::RefsMany ) );
        config::set("schema.user_test", array( "address_test" => RelTypes::HasMany ) );
        config::set("schema.address_test", array( "addresstype" => RelTypes::RefsMany, "state" => RelTypes::RefsOne ) );
		
        $this->result = array(
                    array( "1", "chris", "1", "308 108th ave ne", "1", "Home", "2", "WA"), 
                    array( "1", "chris", "1", "308 108th ave ne", "2", "Current", "2", "WA"), 
                    array( "1", "chris", "3", "12650 woodside falls rd", "3", "Original", "1", "NC" ),
                    array( "2", "ryan", "4", "12650 woodside falls rd", "1", "Home", "1", "NC" ),
                );
	}
    
    public function faulty_test() {

        $cur = ORM::select("user_test:first", "user_test.address_test:line1",  "user_test.address_test.state:value", "user_test.address_test.addresstype:value");
        profiler::debug($cur->buildObject($this->result));
        $this->assertEquals("a! woot", "awesome! woot");

    }
	
	
}
