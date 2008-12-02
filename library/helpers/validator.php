<?php

class validator {
	
	function dispatch() {

		$state =& getStateObject();
		$form = $state->postedForm();
		
		if(!$path = config::get('validator.form.'.$form)) {
			$path = config::get('validator.form.default');
		}

		if($path) {
			if(stripos($path, '::') !== false) {
				$state->_($path);
			} else {
				$state->_($path.'::'.$form);
			}
		}
		
	}
	
	static function rule() {
		
	}
	
	
	
}


?>