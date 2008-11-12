<?php

class test extends component {
	
	function test() {
		parent::component();
		//echo 'initializing';
	}
	
	function event() {
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