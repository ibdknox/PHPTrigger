<?php

class stateful {
	
	const CLASSNAME = 0;
	const FUNCTIONNAME = 1;
	
	var $events = array();
	var $components = array();
	var $preventTrigger = false;
	
	function run() {
		global $stateful_bm; 
		$this->bm =& $stateful_bm;
		include('sys/base/loader.php');
		
		ob_end_clean();
		
		$this->loader = new stateful_loader();
		
		$this->router = new stateful_router();
		$routePieces = $this->router->route();
		$this->requestURI = $routePieces[0];
		
		$this->view = new stateful_view();
		$this->view->useView($this->requestURI);
		
		$this->bm->start('sys::binding_time');
		$this->loader->bindings($this);
		$this->bm->end('sys::binding_time');
		
		if(isset($_POST['formName'])) {
			$this->bm->start('sys::form_trigger');
			$this->trigger('submit::'.$_POST['formName']);
			$this->bm->end('sys::form_trigger');
		} 
		
		if(!$this->preventTrigger) {
			$this->bm->start('sys::url_trigger');
			$this->trigger('url::'.$this->requestURI);		
			$this->bm->end('sys::url_trigger');
		}
		
		$this->bm->start('sys::view_render');
		$file = $this->view->render();
		$this->trigger('sys::postRender', $file);
		$this->bm->end('sys::view_render');
		
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
	
	function trigger($event, &$info = array()) {
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