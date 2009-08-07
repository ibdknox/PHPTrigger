<?php

/**
 *	Used to gather time information for profiling purposes.
 *
 *	@author Chris
 *  @package core
 */

class trigger_bm {
	
	/**
	* Stores the events that have been triggered and handled
	* @var array
	*/
	public $events = array(
						'handled' => array(), 
						'triggered' => array()
					);
	/**
	* Associative array of start and end mark times
	* @var array
	*/
	public $marks = array();

	/**	
	 *	This function creates a new mark in the marks array with the index of $name.
	 *	It also stores the beginning time in second dimension array.
	 *
	 *	@param $name = name of the mark
	 *	@author Chris
	 */
	public function start($name) {
		$this->marks[$name][0] = microtime(true);
	}

	/**	
	 *	This function adds the end time to the mark with key $name
	 *
	 *	@param $name = name of the mark
	 *	@author Chris
	 */
	public function end($name) {
		$this->marks[$name][1] = microtime(true);
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
	
	/**	
	 *	Store the name of the event triggered
	 *
	 *	@param string $trigger the event triggered 
	 *	@author Chris
	 */
	public function triggered($trigger) {
		$this->events['triggered'][] = $trigger;
	}
	
	/**	
	 *	Store a handled event based on what was triggered and what handled it
	 *
	 *	@param string $handler the event responder
	 *  @param string $trigger the event triggered
	 *	@author Chris
	 */
	public function handled($handler, $trigger) {
		$this->events['handled'][] = array($handler, $trigger);
		
	}

}

?>