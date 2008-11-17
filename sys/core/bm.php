<?php

/**
 *  CLASS Benchmark
 *	Used to gather time information for the profiler.
 *
 *	@author Chris
 */

class stateful_bm {
	
	public $events = array();
	public $marks = array(); // Associative array of start and end mark times

	/*	start
	 *	This function creates a new mark in the marks array with the index of $name.
	 *	It also stores the beginning time in second dimension array.
	 *
	 *	@param $name = name of the mark
	 *	@author Chris
	 */
	function start($name) {
		$this->marks[$name][0] = $this->microtime_float();
	}

	/*	end
	 *	This function adds the end time to the mark with key $name
	 *
	 *	@param $name = name of the mark
	 *	@author Chris
	 */
	function end($name) {
		$this->marks[$name][1] = $this->microtime_float();
	}

	/*	time
	 *	This function returns the total time between the start and end of a mark with index $name
	 *
	 *	@param $name = name of the mark
	 *	@return float
	 *	@author Chris
	 */
	function time($name) {
		return $this->marks[$name][1] - $this->marks[$name][0];
	}
	
	function triggered($trigger) {
		$this->events['triggered'][] = $trigger;
	}
	
	function handled($path, $trigger) {
		$this->events['handled'][] = array($path, $trigger);
		
	}
	
	/*	microtime_float
	 *	This utility function returns the float version of microtime()
	 *
	 *	@return float of current microtime
	 */
	static function microtime_float() {
	    list($usec, $sec) = explode(" ", microtime());
	    return ((float)$usec + (float)$sec);
	}

}

?>