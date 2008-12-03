<?php

class validate extends trigger_component {
	
	function login() {
		//profiler::debug('I would validate here');
		if($_POST['username'] != 'awesome') {
			validator::$validForm = false;
		}
	}
	
}

?>