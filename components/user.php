<?php

class user extends component {
	
	function login() {
		
		if($_POST['username'] == '') {
			echo "failed";
			$this->state->preventTrigger = true;
			//$this->state->revert();
		} else {
			echo 'you tried to login!<br/>';
			echo $this->state->_('test::info').'<br />';
		}
		
	}
	
}

?>