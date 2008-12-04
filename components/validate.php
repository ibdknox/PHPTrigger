<?php

class validate extends trigger_component {
	
	function login() {
		validator::rule('required', 'username');
	}
	
}

?>