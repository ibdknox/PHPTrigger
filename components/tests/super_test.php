<?php

class super_test extends unit_test {
	
	function woot_test() {
		$this->assertTrue(true);
	}
	
	function super_awesome_test() {
		$this->assertFalse(true);
	}
	
}
