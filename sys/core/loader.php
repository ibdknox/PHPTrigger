<?php

/**
 * 
 */
class trigger_loader {
	
	public function __construct($event) {
		$this->event =& $event;
	}
	
	public function loadCore() {
		$core_files = array(
							'router',
							'view',
							'component'
							);
		foreach($core_files as $file) {
			$this->event->$file = trigger_load_core_object($file, $this->event);
		}
		
		$this->config();
	}

    public function helper($path) {
      
       if( file_exists( HELPERSDIR . "/$path.php" ) ) {
           include( HELPERSDIR . "/$path.php" ); 
       }

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
	
	public function bindings() {
		foreach (new DirectoryIterator(BINDINGSDIR) as $entry) {
			if (substr($entry, strlen($entry)-4, 4) == '.php') {
				require_once( BINDINGSDIR .'/'. $entry );
			}
		}
	}
	
}
