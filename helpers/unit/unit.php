<?php

class unit_test {
	
	var $errors;
	var $numtests;
	var $failedtests;
	
	function __construct() {
		$this->event =& getEventObject();
		$this->errors = array();
		$this->failedtests = array();
		$this->passedtests = array();
		$this->numtests = 0;
	}
	
	function run() {

		$methods = get_class_methods($this);
		foreach($methods as $m) {
			if(stristr($m, '_test')) {
				$this->passedtests[] = $m;
				if(in_array('setup', $methods)) {
					call_user_func(array(&$this, 'setup'), array());
				}
				$this->numtests++;
				call_user_func(array(&$this, $m), array());
			}
		}
		foreach($this->failedtests as $f) {
			$key = array_search($f,$this->passedtests);
			unset($this->passedtests[$key]);
		}
		
	}
	
	function assertTrue($a1) {
		if(!$a1) {
			$this->throwError("expected true");
		}
	}

	function assertFalse($a1) {
		if($a1) {
			$this->throwError("expected false");
		}
	}

	function assertEquals($a1, $a2) {
		if($a1 != $a2) {
			$this->throwError("first value = '$a1', but '$a2' was expected");
		}
	}
	
	function throwError($msg) {
		$stack = debug_backtrace();
		$curfunction = $stack[2]["function"];
		$curline = $stack[1]["line"];
		$this->errors[] = array("line"=>$curline, "function"=>$curfunction, "msg"=>$msg);
		if(!in_array($curfunction, $this->failedtests)) {
			$this->failedtests[] = $curfunction;
		}
	}
}

class unit {
	
	static $units;
	static $event;
	
	function runUnits() {
		self::$units = array();
		self::$event =& getEventObject();
		
		foreach(config::get('unit.tests') as $test) {
			self::$event->call($test.'::run');
			$parts = explode('::', $test);
			self::$units[$test] =& self::$event->getComponent(end($parts));
		}
		self::results();
	}
	
	function results() {
		$str = '';
		foreach(self::$units as $name=>$value) {
			$object =& self::$units[$name];
			
			if(count($object->errors) == 0) {
				$str .= "<span style='color:#3b3;'>$name</span> ( PASSED $object->numtests tests )<br/>{ <br/>";
				foreach($object->passedtests as $e) {
					$str .= "<span style='padding-left:15px; color:#3b3; font:bold 110% Arial, serif;'>$e</span><br/>";
				}
				$str .= "}";
			} else {
				$num = count($object->errors);
				$passed = $object->numtests - count($object->failedtests);
				$str .= "<span style='color:#b33;'>$name</span> ( FAILED :: $num ERRORS :: PASSED $passed TESTS )<br/>{<br/>";
				foreach($object->errors as $e) {
					$str .= "<span style='padding-left:15px; color:#b33; font:bold 110% Arial, serif;'>$e[function]</span> - $e[msg] on line $e[line]<br/>";
				}
				foreach($object->passedtests as $e) {
					$str .= "<span style='padding-left:15px; color:#3b3; font:bold 110% Arial, serif;'>$e</span><br/>";
				}
				$str .= "}<br/>";
			}
			
		}
		echo $str;
	}
	
}
