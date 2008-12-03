<?php

class user extends trigger_component {
	
	function login() {

		if(!validator::valid()) {
			//echo "failed";
			//$this->event->preventTrigger('sys::preOutput');
			$this->event->revert();
		} else {
			echo 'you tried to login!<br/>';
			echo $this->get('test::info').'<br />';
		}
		
	}
	
}

?>