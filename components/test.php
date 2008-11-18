<?php

class test extends stateful_component {
	
	function test() {
		parent::__construct();
		//echo 'initializing';
	}
	
	function event() {
		$this->get('lib::lib_test::woot');
		echo 'responding to an event<br/>';
	}
	
	function event2($info) {
		echo 'second response to "event" - with info: '.$info.'<br/>';
		$this->state->trigger('kaboom');
	}
	
	function bombsquad() {
		echo 'bomb diffused<br/>';
	}
	
	function info() {
		return 'cool';
	}
	
	function woot() {
		return array('woot', 'yar', 'cool');
	}
	
}

?>