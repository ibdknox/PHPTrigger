<?php

/**
 * 
 */
class stateful_loader {
	
	public function __construct() {
		//nothing to do here?
	}
	
	public function loadCore() {
		$core_files = array(
							'router',
							'view',
							'component'
							);
		foreach($core_files as $file) {
			$this->loadCoreObject($file);
		}
	}
	
	public function loadCoreObject($name) {
		include( COREDIR . "/$name.php" );
	}
	
	public function component($name, $lib = false) {
		
		$path = COMPONENTDIR."/$name.php";
		
		if($lib) {
			$path = LIBCOMPONENTSDIR."/$name.php";
		}
		
		if(file_exists( $path )) {
			include( $path );
			return new $name();
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