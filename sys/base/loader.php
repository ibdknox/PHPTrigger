<?php

class stateful_loader {
	
	function stateful_loader() {
		include('sys/config/base.php');
		include('sys/base/router.php');
		include('sys/base/view.php');
		include('sys/base/component.php');
	}
	
	function component($name) {
		if(file_exists(COMPONENTDIR."/$name.php")) {
			include(COMPONENTDIR."/$name.php");
			return new $name();
		}
	}
	
	function bindings($state) {
		foreach (new DirectoryIterator(BINDINGSDIR) as $entry) {
			if (substr($entry, strlen($entry)-4, 4) == '.php') {
				require_once(BINDINGSDIR .'/'. $entry);
			}
		}
	}
	
}


?>