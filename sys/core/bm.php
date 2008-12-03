<?php

/**
 *	Used to gather time information for profiling purposes.
 *
 *	@author Chris
 */

class trigger_bm {
	
	public $events = array(
						'handled' => array(), 
						'triggered' => array()
					);
	public $marks = array(); // Associative array of start and end mark times

	/**	
	 *	This function creates a new mark in the marks array with the index of $name.
	 *	It also stores the beginning time in second dimension array.
	 *
	 *	@param $name = name of the mark
	 *	@author Chris
	 */
	public function start($name) {
		$this->marks[$name][0] = $this->microtime_float();
	}

	/**	
	 *	This function adds the end time to the mark with key $name
	 *
	 *	@param $name = name of the mark
	 *	@author Chris
	 */
	public function end($name) {
		$this->marks[$name][1] = $this->microtime_float();
	}

	/**	
	 *	This function returns the total time between the start and end of a mark with index $name
	 *
	 *	@param $name = name of the mark
	 *	@return float
	 *	@author Chris
	 */
	public function time($name) {
		return $this->marks[$name][1] - $this->marks[$name][0];
	}
	
	public function triggered($trigger) {
		$this->events['triggered'][] = $trigger;
	}
	
	public function handled($path, $trigger) {
		$this->events['handled'][] = array($path, $trigger);
		
	}
	
	/**	
	 *	This utility function returns the float version of microtime()
	 *
	 *	@return float of current microtime
	 */
	public function microtime_float() {
	    list($usec, $sec) = explode(" ", microtime());
	    return ((float)$usec + (float)$sec);
	}

}

?>