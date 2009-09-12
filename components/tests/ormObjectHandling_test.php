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
		
        $this->resultArray = array(
                    array( "1", "chris", "1", "308 108th ave ne", "1", "Home", "2", "WA"), 
                    array( "1", "chris", "1", "308 108th ave ne", "2", "Current", "2", "WA"), 
                    array( "1", "chris", "3", "12650 woodside falls rd", "3", "Original", "1", "NC" ),
                    array( "2", "ryan", "4", "12650 woodside falls rd", "1", "Home", "1", "NC" ),
                );
	}

    public function hasOne_test() {
        $resultArray = array(
                    array( "1", "ibdknox@mail.com", "1", "home"),
                );

        $email = (object) null;
        $email->id = "1";
        $email->address = "ibdknox@mail.com";
        $email->type = (object) null;
        $email->type->id = "1";
        $email->type->value = "home";

        $expected = array( $email );
        
        $orm = ORM::select("email:address", "email.type:value"); 
        $this->assertEquals($orm->buildObject($resultArray), $expected);

    }

    public function refsOne_test() {
        $resultArray = array(
                    array( "1", "308 108th ave", "1", "home"),
                );

        $address = (object) null;
        $address->id = "1";
        $address->line1 = "308 108th ave";
        $address->addresstype = (object) null;
        $address->addresstype->id = "1";
        $address->addresstype->value = "home";

        $expected = array( $address );
        
        $orm = ORM::select("address:line1", "address.addresstype:value"); 
        $this->assertEquals($orm->buildObject($resultArray), $expected);

    }

    public function hasMany_test() {

        $resultArray = array(
                    array( "1", "chris", "1", "308 108th ave ne"), 
                    array( "1", "chris", "3", "12650 woodside falls rd"),
                );

        $chris = (object) null;
        $chris->id = "1";
        $chris->first = "chris";
        $chris->address_test = array();
        $chris->address_test[0] = (object) null;
        $chris->address_test[0]->id = "1";
        $chris->address_test[0]->line1 = "308 108th ave ne";
        $chris->address_test[1] = (object) null;
        $chris->address_test[1]->id = "3";
        $chris->address_test[1]->line1 = "12650 woodside falls rd";

        $expected = array( $chris );
        
        $orm = ORM::select("user_test:first", "user_test.address_test:line1"); 
        $this->assertEquals($orm->buildObject($resultArray), $expected);
    }

    public function refsMany_test() {

        $resultArray = array(
                    array( "1", "awesome post!", "1", "newtag"), 
                    array( "1", "awesome post!", "2", "besttag"),
                );

        $post = (object) null;
        $post->id = "1";
        $post->title = "awesome post!";
        $post->tag = array();
        $post->tag[0] = (object) null;
        $post->tag[0]->id = "1";
        $post->tag[0]->value = "newtag";
        $post->tag[1] = (object) null;
        $post->tag[1]->id = "2";
        $post->tag[1]->value = "besttag";

        $expected = array( $post );
        
        $orm = ORM::select("post:title", "post.tag:value"); 
        $this->assertEquals($orm->buildObject($resultArray), $expected);
    }

    public function complexObject_test() {

        array_pop($this->resultArray);

        $chris = (object) null;
        $chris->id = "1";
        $chris->first = "chris";
        $chris->address_test = array();
        $chris->address_test[0] = (object) null;
        $chris->address_test[0]->id = "1";
        $chris->address_test[0]->line1 = "308 108th ave ne";
        $chris->address_test[0]->state = (object) null;
        $chris->address_test[0]->state->id = "2";
        $chris->address_test[0]->state->value = "WA";
        $chris->address_test[0]->addresstype = array();
        $chris->address_test[0]->addresstype[0] = (object) null;
        $chris->address_test[0]->addresstype[0]->id = "1";
        $chris->address_test[0]->addresstype[0]->value = "Home";
        $chris->address_test[0]->addresstype[1] = (object) null;
        $chris->address_test[0]->addresstype[1]->id = "2";
        $chris->address_test[0]->addresstype[1]->value = "Current";
        $chris->address_test[1] = (object) null;
        $chris->address_test[1]->id = "3";
        $chris->address_test[1]->line1 = "12650 woodside falls rd";
        $chris->address_test[1]->state = (object) null;
        $chris->address_test[1]->state->id = "1";
        $chris->address_test[1]->state->value = "NC";
        $chris->address_test[1]->addresstype = array();
        $chris->address_test[1]->addresstype[0] = (object) null;
        $chris->address_test[1]->addresstype[0]->id = "3";
        $chris->address_test[1]->addresstype[0]->value = "Original";

        $expected = array( $chris );

        $cur = ORM::select("user_test:first", "user_test.address_test:line1",  "user_test.address_test.state:value", "user_test.address_test.addresstype:value");
        
        $this->assertEquals($cur->buildObject($this->resultArray), $expected);

    }
    
    public function multiComplexObject_test() {

        $chris = (object) null;
        $chris->id = "1";
        $chris->first = "chris";
        $chris->address_test = array();
        $chris->address_test[0] = (object) null;
        $chris->address_test[0]->id = "1";
        $chris->address_test[0]->line1 = "308 108th ave ne";
        $chris->address_test[0]->state = (object) null;
        $chris->address_test[0]->state->id = "2";
        $chris->address_test[0]->state->value = "WA";
        $chris->address_test[0]->addresstype = array();
        $chris->address_test[0]->addresstype[0] = (object) null;
        $chris->address_test[0]->addresstype[0]->id = "1";
        $chris->address_test[0]->addresstype[0]->value = "Home";
        $chris->address_test[0]->addresstype[1] = (object) null;
        $chris->address_test[0]->addresstype[1]->id = "2";
        $chris->address_test[0]->addresstype[1]->value = "Current";
        $chris->address_test[1] = (object) null;
        $chris->address_test[1]->id = "3";
        $chris->address_test[1]->line1 = "12650 woodside falls rd";
        $chris->address_test[1]->state = (object) null;
        $chris->address_test[1]->state->id = "1";
        $chris->address_test[1]->state->value = "NC";
        $chris->address_test[1]->addresstype = array();
        $chris->address_test[1]->addresstype[0] = (object) null;
        $chris->address_test[1]->addresstype[0]->id = "3";
        $chris->address_test[1]->addresstype[0]->value = "Original";

        $ryan = (object) null;
        $ryan->id = "2";
        $ryan->first = "ryan";
        $ryan->address_test = array();
        $ryan->address_test[0] = (object) null;
        $ryan->address_test[0]->id = "4";
        $ryan->address_test[0]->line1 = "12650 woodside falls rd";
        $ryan->address_test[0]->state = (object) null;
        $ryan->address_test[0]->state->id = "1";
        $ryan->address_test[0]->state->value = "NC";
        $ryan->address_test[0]->addresstype = array();
        $ryan->address_test[0]->addresstype[0] = (object) null;
        $ryan->address_test[0]->addresstype[0]->id = "1";
        $ryan->address_test[0]->addresstype[0]->value = "Home";

        $expected = array( $chris, $ryan );

        $cur = ORM::select("user_test:first", "user_test.address_test:line1",  "user_test.address_test.state:value", "user_test.address_test.addresstype:value");
        
        $this->assertEquals($cur->buildObject($this->resultArray), $expected);

    }

    public function add_test() {

        $email = (object) null;
        $email->id = "1";
        $email->address = "ibdknox@mail.com";
        $email->type = (object) null;
        $email->type->id = "1";
        $email->type->value = "home";

        $expected = "INSERT INTO `email` (`id`, `address`, `type_id`) VALUES ( '1', 'ibdknox@mail.com', '1' )";

        $this->assertEquals(ORM::add('email', $email)->getSQL(), $expected);
        
    }
	
    public function multiAdd_test() {

        $email = (object) null;
        $email->id = "1";
        $email->address = "ibdknox@mail.com";
        $email->type = (object) null;
        $email->type->id = "1";
        $email->type->value = "home";


        $email2 = (object) null;
        $email2->id = "2";
        $email2->address = "chris@mail.com";
        $email2->type = (object) null;
        $email2->type->id = "2";
        $email2->type->value = "home";

        $objToAdd = array( $email, $email2 );

        $expected = "INSERT INTO `email` (`id`, `address`, `type_id`) VALUES ( '1', 'ibdknox@mail.com', '1' ), ( '2', 'chris@mail.com', '2' )";

        $this->assertEquals(ORM::add('email', $objToAdd)->getSQL(), $expected);
        
    }

    public function update_test() {
        
        $email = (object) null;
        $email->id = "1";
        $email->address = "ibdknox@mail.com";
        $email->type = (object) null;
        $email->type->id = "1";
        $email->type->value = "home";

        $expected = "UPDATE email SET email.id = '1', email.address = 'ibdknox@mail.com', email.type_id = '1' WHERE email.id = '1'";

        $this->assertEquals(ORM::update('email', $email)->getSQL(), $expected);

    }

    public function delete_test() {

        $expected = "DELETE FROM email WHERE email.id = '1'";

        $this->assertEquals( ORM::delete('email', 1)->getSQL(), $expected );
    }
	
}
