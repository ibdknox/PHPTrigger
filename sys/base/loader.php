<?php

class stateful_loader {
	
	function stateful_loader() {
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
	
}


?>