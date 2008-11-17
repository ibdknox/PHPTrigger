<?php

class user extends stateful_component {
	
	function login() {

		if($_POST['username'] != 'awesome') {
			//echo "failed";
			//$this->state->preventTrigger('cart::');
			//$this->state->revert();
		} else {
			echo 'you tried to login!<br/>';
			echo $this->get('test::info').'<br />';
		}
		
	}
	
}

?>