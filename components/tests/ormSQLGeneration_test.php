<?php

class ormSQLGeneration_test extends unit_test {
	
	public function setup() {

        ORM::init();
		
		config::set('database.config.group', 'localhost');
		config::set('database.config.localhost', array(
				'username'     => 'root',
				'password'     => 'root',
				'hostname'     => 'localhost',
				'database' => '_test'
		));

        config::set("schema.user", array( "address" => RelTypes::HasMany, 'email' => RelTypes::HasOne, 'post' => RelTypes::HasMany ));
        config::set("schema.address", array( "addresstype" => RelTypes::RefsOne ) );
        config::set("schema.email", array( "type" => RelTypes::HasOne ) );
        config::set("schema.post", array( "tag" => RelTypes::RefsMany ) );
        config::set("schema.user__test", array( "address_test" => RelTypes::HasMany ) );
        config::set("schema.address__test", array( "addresstype" => RelTypes::RefsMany, "state" => RelTypes::RefsOne ) );
		
	}

    public function baseSelect_test() {
        $cur = ORM::select("user:first");
        $this->assertEquals($cur->getSQL(), "SELECT user.id, user.first FROM user"); 
    }
	
    public function multiValueSelect_test() {
        $cur = ORM::select("user:first,last");
        $this->assertEquals($cur->getSQL(), "SELECT user.id, user.first, user.last FROM user");
    }

    public function  Where_test() {
        $cur = ORM::select("user:first,last")->where("user.first = '{0}'", "john");
        $this->assertEquals($cur->getSQL(), "SELECT user.id, user.first, user.last FROM user WHERE user.first = 'john'");
    }

    public function  DefaultAndWhere_test() {
        $cur = ORM::select("user:first,last")->where("user.first = '{0}'", "john")->where("user.last = '{0}'", "granger");
        $this->assertEquals($cur->getSQL(), "SELECT user.id, user.first, user.last FROM user WHERE user.first = 'john' AND user.last = 'granger'");
    }

    public function  AndWhere_test() { 
        $cur = ORM::select("user:first,last")->where("user.first = '{0}'", "john")->andWhere("user.last = '{0}'", "granger");
        $this->assertEquals($cur->getSQL(), "SELECT user.id, user.first, user.last FROM user WHERE user.first = 'john' AND user.last = 'granger'");
    }

    public function  OrWhere_test() {
        $cur = ORM::select("user:first,last")->where("user.first = '{0}'", "john")->orWhere("user.last = '{0}'", "granger");
        $this->assertEquals($cur->getSQL(), "SELECT user.id, user.first, user.last FROM user WHERE user.first = 'john' OR user.last = 'granger'");
    }

    public function  Order_test() {
        $cur = ORM::select("user:first,last")->order("user.first");
        $this->assertEquals($cur->getSQL(), "SELECT user.id, user.first, user.last FROM user ORDER BY user.first");
    }

    public function  MultiOrder_test() {
        $cur = ORM::select("user:first,last")->order("user.first", "user.last");
        $this->assertEquals($cur->getSQL(), "SELECT user.id, user.first, user.last FROM user ORDER BY user.first, user.last");
    }

    public function  MultiOrderStatements_test() {
        $cur = ORM::select("user:first,last")->order("user.first")->order("user.last");
        $this->assertEquals($cur->getSQL(), "SELECT user.id, user.first, user.last FROM user ORDER BY user.first, user.last");
    }

    public function  Group_test() {
        $cur = ORM::select("user:first,last")->group("user.first");
        $this->assertEquals($cur->getSQL(), "SELECT user.id, user.first, user.last FROM user GROUP BY user.first");
    }

    public function  MultiGroup_test() {
        $cur = ORM::select("user:first,last")->group("user.first", "user.last");
        $this->assertEquals($cur->getSQL(), "SELECT user.id, user.first, user.last FROM user GROUP BY user.first, user.last");
    }

    public function  MultiGroupStatements_test() {
        $cur = ORM::select("user:first,last")->group("user.first")->group("user.last");
        $this->assertEquals($cur->getSQL(), "SELECT user.id, user.first, user.last FROM user GROUP BY user.first, user.last");
    }

    public function  Join_test() {
        $cur = ORM::select("user:first", "user.address:line1");
        $this->assertEquals($cur->getSQL(), "SELECT user.id, user.first, address.id, address.line1 FROM user LEFT JOIN address ON user.id = address.user_id");
    }

    public function  MultiJoin_test() {
        $cur = ORM::select("user:first", "user.address:line1", "user.address.addresstype:value");
        $this->assertEquals($cur->getSQL(), "SELECT user.id, user.first, address.id, address.line1, addresstype.id, addresstype.value FROM user LEFT JOIN address ON user.id = address.user_id LEFT JOIN addresstype ON address.addresstype_id = addresstype.id");
    }

    public function  Limit_test() {
        $cur = ORM::select("user:first,last")->limit(1);
        $this->assertEquals($cur->getSQL(), "SELECT user.id, user.first, user.last FROM user LIMIT 1");
    }

    public function  Offset_test() {
        $cur = ORM::select("user:first,last")->offset(1);
        $this->assertEquals($cur->getSQL(), "SELECT user.id, user.first, user.last FROM user OFFSET 1");
    }

    public function  Page_test() {
        $cur = ORM::select("user:first,last")->limit(15)->page(2);
        $this->assertEquals($cur->getSQL(), "SELECT user.id, user.first, user.last FROM user LIMIT 15 OFFSET 30");
    }

    public function  Star_test() {
        $cur = ORM::select("user:*");
        $this->assertEquals($cur->getSQL(), "SELECT user.id, user.* FROM user");
        #TODO: building an object from this will fail epically
    }

    #_test for implicit association user.address.addresstype without user.address
    public function  ImplicitJoin_test() {
        $cur = ORM::select("user:first", "user.address.addresstype:value");
        $this->assertEquals($cur->getSQL(), "SELECT user.id, user.first, addresstype.id, addresstype.value FROM user LEFT JOIN address ON user.id = address.user_id LEFT JOIN addresstype ON address.addresstype_id = addresstype.id");
    }

    #_test for incorrectly ordered children, i.e. user.address, user.email, user.address.addresstype
    public function  IncorrectJoinOrder_test() {
        $cur = ORM::select("user:first", "user.address:line1", "user.email:address", "user.address.addresstype:value");
         $this->assertEquals($cur->getSQL(), "SELECT user.id, user.first, address.id, address.line1, email.id, email.address, addresstype.id, addresstype.value FROM user LEFT JOIN address ON user.id = address.user_id LEFT JOIN email ON email.user_id = user.id LEFT JOIN addresstype ON address.addresstype_id = addresstype.id");
        #the important part is that this still builds the object correctly
        #where this might break is in a multi, ancillary one, multi sort of situation
    }

    #explicitly _test all RelTypes
    public function  HasMany_test() {
        $cur = ORM::select("user.address:line1");
        $this->assertEquals($cur->getSQL(), "SELECT address.id, address.line1 FROM user LEFT JOIN address ON user.id = address.user_id");
    }

    public function  HasOne_test() {
        $cur = ORM::select("user.email:address");
        $this->assertEquals($cur->getSQL(), "SELECT email.id, email.address FROM user LEFT JOIN email ON email.user_id = user.id");
    }

    public function  RefsMany_test() {
        $cur = ORM::select("post.tag:value");
        $this->assertEquals($cur->getSQL(), "SELECT tag.id, tag.value FROM post LEFT JOIN post_tag ON post.id = post_tag.post_id LEFT JOIN tag ON tag.id = post_tag.tag_id");
    }

    public function  RefsOne_test() {
        $cur = ORM::select("address.addresstype:value");
        $this->assertEquals($cur->getSQL(), "SELECT addresstype.id, addresstype.value FROM address LEFT JOIN addresstype ON address.addresstype_id = addresstype.id");
    }
	
}
