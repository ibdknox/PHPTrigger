<?php

class test_test extends unit_test {
	
	function addA_test() {
		$str = 'look';
		$this->assertEquals($this->event->call('test::addA', $str), 'lookA!');
	}
	
	function super_awesome_test() {
		$this->assertFalse(true);
	}
	
	function woot() {
		echo 'yay';
	}
	
}
