<?php
/**
 * Class designed to be extended by components created for the app and
 * simply provides a reference to the state object 
 * 
 * @author Chris Granger
 * @copyright Chris Granger 2008
 */
class component {
	
	/**
	 * reference to the global state object 
	 * @var stateful
	 */ 
	public $state; 
	
	/**
	 * Initializes the state object reference
	 */
	public function __construct() {
		global $state;
		$this->state =& $state;
	}
	
	/**
	 * Alias of the stateful object's get function
	 * 
	 * @uses stateful
	 * @param string $path the component path to call
	 * @param mixed $info the parameter to send to the function
	 * @return mixed result of the called component function
	 */
	public function get($path, $info = array()) {
		return $this->state->_($path, $info);
	}
	
}

?>