<?php
/**
 * Class designed to be extended by components created for the app and
 * simply provides a reference to the event object 
 * 
 * @author Chris Granger
 * @copyright Chris Granger 2008
 */
class trigger_component {
	
	/**
	 * reference to the global event object 
	 * @var trigger
	 */ 
	public $event; 
	
	/**
	 * Initializes the event object reference
	 */
	public function __construct() {
		$this->event =& getEventObject();
	}
	
	/**
	 * Alias of the trigger object's get function
	 * 
	 * @uses trigger
	 * @param string $path the component path to call
	 * @param mixed $info the parameter to send to the function
	 * @return mixed result of the called component function
	 */
	public function get($path, $info = array()) {
		return $this->event->call($path, $info);
	}
	
}

?>