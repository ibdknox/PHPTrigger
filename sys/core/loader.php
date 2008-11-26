<?php

/**
 * 
 */
class stateful_loader {
	
	public function __construct($state) {
		$this->state =& $state;
	}
	
	public function loadCore() {
		$core_files = array(
							'router',
							'view',
							'component'
							);
		foreach($core_files as $file) {
			$this->state->$file = stateful_load_core_object($file, $this->state);
		}
		
		$this->config();
	}
	
	public function component($name, $path = false) {
		
		if(!$path) {
			$path = COMPONENTDIR;
		}
		
		$path .= "/$name.php";
		
		if(file_exists( $path )) {
			include( $path );
			return new $name();
		}
	}
	
	public function config() {
		foreach (new DirectoryIterator(CONFIGDIR) as $entry) {
			if (substr($entry, strlen($entry)-4, 4) == '.php' && $entry != 'base.php') {
				include( CONFIGDIR . "/$entry" );
			}
		}
	}
	
	public function bindings($state) {
		foreach (new DirectoryIterator(BINDINGSDIR) as $entry) {
			if (substr($entry, strlen($entry)-4, 4) == '.php') {
				require_once( BINDINGSDIR .'/'. $entry );
			}
		}
	}
	
}


?>