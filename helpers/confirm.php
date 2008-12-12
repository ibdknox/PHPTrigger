<?php

/**
 * checks that data conforms to certain rules
 * 
 * @author Nathan Hammond
 * @author Chris Granger
 * @copyright Nathan Hammond and Chris Granger 2008
 * @package core.utils
 */
class confirm {
	
	/**
	 * Determines whether a given variable isset
	 * 
	 */
	static function exists(&$variable) {
		return isset($variable);
	}
	
	/**
	 * 
	 * Determines if the var meets the criteria for being set and non null.
	 * 
	 * @return var exists and it does not equal empty string?
	 * 
	 */
	static function required($var) {
		if(confirm::exists($var)) {
			return $var != '';
		}
		return false;
	}
	
	/**
	 * 
	 * Determines if two values are equal (non-identity check)
	 * 
	 */
	static function match($a, $b) {
		return $a === $b;
	}
	
	/**
	 * 
	 * Determines if two values are not equal (non-identity check)
	 * 
	 */
	static function notmatch($a, $b) {
		return $a !== $b;
	}

	//TODO :: finish commenting/reorganizing

	static function maxlength($value, $limit) {
		return (strlen(utf8_decode($value)) <= $limit);
	}

	static function minlength($value, $limit) {
		return (strlen(utf8_decode($value)) >= $limit);
	}

	static function number($value) {
		return ctype_digit($value);
	}
	
	static function url($url) {
		return preg_match("/^(http(s?):\\/\\/|ftp:\\/\\/{1})?((\w+\.)+)\w{2,}(\/?)$/i", $url);
	}

	static function email($value) {
		$regex = '/^[+a-z0-9_-]+(\.[+a-z0-9_-]+)*@([a-z0-9-]+\.)+[a-z]{2,6}$/iD';
		return preg_match($regex, $value);
	}

	static function attribute($value, $type) {
		// Attribute types specified at:
		// http://www.w3.org/TR/html401/index/attributes.html

		switch ($type) {
			case "fragmentid":
				$regex = "/^[A-Za-z][A-Za-z0-9:_.-]*$/";
				return preg_match($regex, $value);
			break;
			default:
				return false;
			break;
		}
	}

}

?>
