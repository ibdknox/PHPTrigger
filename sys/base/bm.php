<?php

/**
 *  CLASS Benchmark
 *	Used to gather time information for the profiler.
 *
 *	@author Chris
 */

class stateful_benchmaker {
	
	static $marks = array(); // Associative array of start and end mark times

	/*	start
	 *	This function creates a new mark in the marks array with the index of $name.
	 *	It also stores the beginning time in second dimension array.
	 *
	 *	@param $name = name of the mark
	 *	@author Chris
	 */
	static function start($name) {
		bm::$marks[$name][0] = bm::microtime_float();
	}

	/*	end
	 *	This function adds the end time to the mark with key $name
	 *
	 *	@param $name = name of the mark
	 *	@author Chris
	 */
	static function end($name) {
		bm::$marks[$name][1] = bm::microtime_float();
	}

	/*	time
	 *	This function returns the total time between the start and end of a mark with index $name
	 *
	 *	@param $name = name of the mark
	 *	@return float
	 *	@author Chris
	 */
	static function time($name) {
		return bm::$marks[$name][1] - bm::$marks[$name][0];
	}

}

?>