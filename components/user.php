<?php

class user extends stateful_component {
	
	function login() {

		if($_POST['username'] != 'awesome') {
			//echo "failed";
			//$this->state->preventTrigger('sys::preOutput');
			//$this->state->revert();
		} else {
			echo 'you tried to login!<br/>';
			echo $this->get('test::info').'<br />';
		}
		
	}
	
}

?>