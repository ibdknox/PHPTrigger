<?php

class generator implements Iterator {
	
	private $array;
	private $keys;
	private $mutators;
	private $position;
	
	public function __construct($array) {
		
		if(is_array($array)) {
			$this->array = $array;
			$this->keys = array_keys($array);
		} else {
			throw new Exception('Array expected.');
		}
		
		global $state;
		
		$this->state =& $state;
		
		$this->mutators = array();
		$this->position = 0;
	}
	
	public function mutate($path) {
		$this->mutators[] = $path;
	}
	
	private function performMutation($preValue) {

		$value = $preValue;
		foreach($this->mutators as $mut) {
			$value = $this->state->_($mut, $value);
		}
		
		return $value;
		
	}
	
	function rewind() {
        $this->position = 0;
    }

    function current() {
        return $this->performMutation($this->array[$this->key()]);
    }

    function key() {
        return $this->keys[$this->position];
    }

    function next() {
        ++$this->position;
    }

    function valid() {
        return isset($this->keys[$this->position]);
    }
	
}


?>