<?php

class stateful {
	
	const CLASSNAME = 0;
	const FUNCTIONNAME = 1;
	
	var $events = array();
	var $components = array();
	var $preventTrigger = false;
	
	function stateful() {
		define('OUTPUTDIR', 'output');
		define('VIEWDIR', OUTPUTDIR.'/views');
		define('LAYOUTDIR', OUTPUTDIR.'/layouts');
		define('COMPONENTDIR', 'components');
	}
	
	function run() {
		global $bm; 
		include('sys/base/loader.php');
		
		ob_end_clean();
		
		$this->loader = new stateful_loader();
		
		$this->router = new stateful_router();
		$routePieces = $this->router->route();
		$this->requestURI = $routePieces[0];
		
		$this->view = new stateful_view();
		$this->view->useView($this->requestURI);
		
		if(isset($_POST['formName'])) {
			$this->trigger('submit::'.$_POST['formName']);
		} 
		
		if(!$this->preventTrigger) {
			$this->trigger('url::'.$this->requestURI);		
		}
		
		$file = $this->view->render();
		echo $file;
		
		$_SESSION['prevUrl'] = $this->requestURI;
	}
	
	function revert() {
		$this->router->redirect($_SESSION['prevUrl']);
	}
	
	function _($path, $info = array()) {
		$parts = $this->parseListener($path);
		if(!isset($this->components[$parts[self::CLASSNAME]])) {
			$this->components[$parts[self::CLASSNAME]] = $this->loader->component($parts[self::CLASSNAME]);
		}
		if(is_callable(array($this->components[$parts[self::CLASSNAME]], $parts[self::FUNCTIONNAME]))) {
			$funcName = $parts[self::FUNCTIONNAME];
			return $this->components[$parts[self::CLASSNAME]]->$funcName($info);
		} else {
			//TODO throw an error
		}
	}
	
	function register($event, $listener) {
		$this->events[$event][] = $listener;
	}
	
	function trigger($event, $info = array()) {
		if(isset($this->events[$event])) {
			foreach($this->events[$event] as $listener) {
				if($this->preventTrigger) {
					break;
				}
				
				$parts = $this->parseListener($listener);
				if(!isset($this->components[$parts[self::CLASSNAME]])) {
					$this->components[$parts[self::CLASSNAME]] = $this->loader->component($parts[self::CLASSNAME]);
				}
				if(is_callable(array($this->components[$parts[self::CLASSNAME]], $parts[self::FUNCTIONNAME]))) {
					$funcName = $parts[self::FUNCTIONNAME];
					$this->components[$parts[self::CLASSNAME]]->$funcName($info);
				}
			}
		}
	}
	
	function parseListener($path) {
		$pieces = explode('::', $path);
		return $pieces;
	}
	
}


?>