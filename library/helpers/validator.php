<?php

class validator {
	
	static $errors = array();
	static $defaults = array(
			'required' => 'This field is required.',
			'exists' => 'This value must be provided.',
			'number' => 'This value must be numeric.',
			'maxlength' => 'This value is too long.',
			'minlength' => 'This value is too short.',
			'phone' => 'This value must be a phone number.',
			'email' => 'This value must be an email address.'
		);
	
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
	
	static function valid() {
		return !self::hasErrors();
	}

	static function hasErrors() {
		return !empty(self::$errors);
	}
	
	static function error($name) {
		if(isset(self::$errors[$name])) {
			return self::$errors[$name];
		} 
		return false;
	}
	
	static function rule($rule, $name, $msg = '') {
					
		if(is_array($rule)) {
			$ruleName = array_shift($rule);	
		} else {
			$ruleName = $rule;
			$rule = array();
		}
		
		if(!is_array($name)) {
			$name = array($name);
		}
		
		foreach($name as $field) {
			if(!self::error($field)) {
			
				$params = $rule;
				$fieldVal = (isset($_POST[$field]) ? $_POST[$field] : null);
			
				array_unshift($params, $fieldVal);			
			
				if(!call_user_func_array(array('confirm', $ruleName), $params)) {
					self::setError($field, $ruleName, $msg);
				}
			}
		}
	}
	
	static function setError($name, $ruleName, $msg = '') {
		if($msg == '') {
			$msg = self::$defaults[$ruleName];
		}
		self::$errors[$name] = $msg;
	}
	
}