<?php

class test extends trigger_component {
	
	function test() {
		parent::__construct();

		$this->agg = '';
	}
	
	function event() {
		//profiler::debug($this->event);
		//echo 'responding to an event<br/>';
	}
	
	function event2($info) {
		//echo 'second response to "event" - with info: '.$info.'<br/>';
		$this->event->trigger('kaboom');
	}
	
	function bombsquad() {
		//echo 'bomb diffused<br/>';
	}

    function ormMiniTest() {
        
        ORM::init();

        config::set("schema.user", array( "address" => RelTypes::HasMany, 'email' => RelTypes::HasOne, 'post' => RelTypes::HasMany ));
        config::set("schema.address", array( "addresstype" => RelTypes::RefsOne ) );
        config::set("schema.email", array( "type" => RelTypes::HasOne ) );
        config::set("schema.post", array( "tag" => RelTypes::RefsMany ) );
        config::set("schema.user_test", array( "address_test" => RelTypes::HasMany ) );
        config::set("schema.address_test", array( "addresstype" => RelTypes::RefsMany, "state" => RelTypes::RefsOne ) );

        $orm = ORM::select("user:name", "user.address:line1,city,state,zip", "user.address.addresstype:value", "user.email:address")->where("cool");
        profiler::debug($orm->getSQL());
    }
	
	function info() {
		return $this->agg;
	}
	
	function woot() {
		$gen = new generator(array('woot', 'yar', 'cool'));
		$gen->mutate('test::addA');
		$gen->mutate('test::lowercase');
		$gen->mutate('test::aggregate');
		return $gen;
	}
	
	function validateLogin() {
		profiler::debug('here');
	}
	
	function addA($node) {
		return $node .= 'A!';
	}
	
	function lowercase($node) {
		return strtolower($node);
	}
	
	function aggregate($node) {
		$this->agg .= $node;
		return $node;
	}
	
}

?>
