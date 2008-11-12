<?php

class component {
	
	var $state; 
	
	function component() {
		global $state;
		$this->state =& $state;
	}
	
}

?>