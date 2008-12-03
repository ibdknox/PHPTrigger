<?php

class validator {
	
	static $validForm = true;
	
	static function dispatch() {

		$event =& geteventObject();
		$form = $event->postedForm();
		
		if(!$path = config::get('validator.form.'.$form)) {
			$path = config::get('validator.form.default');
		}

		if($path) {
			if(stripos($path, '::') !== false) {
				$event->call($path);
			} else {
				$event->call($path.'::'.$form);
			}
		}
		
	}
	
	static function rule() {
		
	}
	
	static function valid() {
		return self::$validForm;
	}
	
	
}


?>