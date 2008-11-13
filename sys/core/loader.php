<?php

class stateful_loader {
	
	function stateful_loader() {
	}
	
	function loadCore() {
		include( COREDIR . '/router.php' );
		include( COREDIR . '/view.php');
		include( COREDIR . '/component.php');
	}
	
	function component($name, $lib = false) {
		
		$path = COMPONENTDIR."/$name.php";
		
		if($lib) {
			$path = LIBCOMPONENTSDIR."/$name.php";
		}
		
		if(file_exists( $path )) {
			include( $path );
			return new $name();
		}
	}
	
	function bindings($state) {
		foreach (new DirectoryIterator(BINDINGSDIR) as $entry) {
			if (substr($entry, strlen($entry)-4, 4) == '.php') {
				require_once( BINDINGSDIR .'/'. $entry );
			}
		}
	}
	
}


?>