<?php

class component {
	
	var $state; 
	
	function component() {
		global $state;
		$this->state =& $state;
	}
	
	function get($path, $info = array()) {
		$this->state->_($path, $info);
	}
	
}

?>