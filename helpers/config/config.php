<?php

/**
 * utility to handle global configuration concerns for lib components and 
 * helpers
 * 
 * @author Chris Granger
 */
class config {

	static $vars = array();

	/**
	 * get the configuration value at $path
	 * 
	 * @param string $path a '.' separated string representing the path to the config value
	 * @return mixed returns false if not found and the value at path otherwise
	 */
	static function get($path) {
		
		//return the node at the given path
		return self::parsePath($path);

	}

	/**
	 * set the configuration value at $path
	 * 
	 * @param string $path a '.' separated string representing the path to the config value
	 */
	static function set($path, $val) {
	
		//get the node ending in $path
		$node =& self::parsePath($path, true);
		//set its value to what was supplied
		$node = $val;

	}
	
	/**
	 * traverses the config tree base on the '.' separated path supplied and
	 * returns the node represented by the final segment of the path.
	 * 
	 * @param string $path a '.' separated string representing the path to the config value
	 * @param bool $buildPath flag for path creation (for set) or path traversal (for get)
	 * @return mixed node at the end of $path
	 */
	static function &parsePath($path, $buildPath = false) {
		
		//paths are case insensitive
		$path = strtolower($path);
		//get each path piece
		$pieces = explode('.', $path);
		
		//initialize our path start point
		$curNode =& self::$vars;
		
		foreach($pieces as $p) {
			//determine whether this node already exists
			$nodeExists = isset($curNode[$p]);

			if(!$nodeExists && $buildPath) {
				//we're building the path, so add this element
				$curNode[$p] = array();	
			} else if(!$nodeExists) {
				//we're searching and this doesn't exist, return false
				$temp = false;
				return $temp;
			}
			
			//set our new curNode
			$curNode =& $curNode[$p];
		}
		
		return $curNode;
	}

}

?>
