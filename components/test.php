<?php

class test extends trigger_component {
	
	function test() {
		parent::__construct();

		$this->agg = '';
	}
	
	function event() {
		//profiler::debug($this->event);
		$this->get('lib::lib_test::woot');
		echo 'responding to an event<br/>';
	}
	
	function event2($info) {
		echo 'second response to "event" - with info: '.$info.'<br/>';
		$this->event->trigger('kaboom');
	}
	
	function bombsquad() {
		echo 'bomb diffused<br/>';
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