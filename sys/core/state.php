<?php

class stateful {
	
	const CLASSNAME = 0;
	const FUNCTIONNAME = 1;
	const REQUESTURI = 0;
	const REQUESTSEGMENTS = 1;
	
	var $components = array();
	var $preventTrigger = false;
	
	function stateful() {
		if(isset($_SESSION['stateful::storedPost'])) {
			$_POST = unserialize($_SESSION['stateful::storedPost']);
			unset($_SESSION['stateful::storedPost']);
		}
		
		if(isset($_SESSION['stateful::storedGet'])) {
			$_GET = unserialize($_SESSION['stateful::storedGet']);
			unset($_SESSION['stateful::storedGet']);
		}
	}
	
	function run() {
		global $stateful_bm; 
		$this->bm =& $stateful_bm;
		
		$this->bm->start('sys::core_load_time');
		include( COREDIR . '/loader.php');
				
		$this->loader = new stateful_loader();		
		$this->loader->loadCore();
		$this->bm->end('sys::core_load_time');
		
		//clean the output buffer to prevent any extraneous whitspace from 
		//screwing up header settings
		ob_end_clean();
		
		//instantiate our router
		$this->router = new stateful_router();
		//go ahead and establish our requestURI and uri-segments
		$routePieces = $this->router->route();
		//store these
		$this->requestURI = $routePieces[ self::REQUESTURI ];
		$this->requestSegments = $routePieces[ self::REQUESTSEGMENTS] ;
		
		//create a new view object
		$this->view = new stateful_view($this);
		$this->view->useView($this->requestURI);
		
		$this->bm->start('sys::binding_time');
		$this->loader->bindings($this);
		$this->bm->end('sys::binding_time');
		
		if(isset($_POST['formName'])) {
			$this->bm->start('sys::form_trigger');
			$this->trigger('sys::preFormTrigger');
			$this->trigger('submit::'.$_POST['formName']);
			$this->trigger('sys::postFormTrigger');
			$this->bm->end('sys::form_trigger');
		} 
		
		if(!$this->preventTrigger) {
			$this->bm->start('sys::url_trigger');
			$this->trigger('sys::preUrlTrigger');
			$this->trigger('url::'.$this->requestURI);		
			$this->trigger('sys::postUrlTrigger');
			$this->bm->end('sys::url_trigger');
		}
		
		$this->bm->start('sys::view_render');
		$file = $this->view->render();
		$this->trigger('sys::postRender', $file);
		$this->bm->end('sys::view_render');
		
		$_SESSION['stateful::prevUrl'] = $this->requestURI;
		
		$stateful_bm->end('sys::all');
		
		$this->trigger('sys::preOutput', &$file);
		echo $file;
		
	}
	
	function segment($num) {
		return $this->requestSegments[$num-1];
	}
	
	function revert() {
		unset($_POST['formName']);
		$_SESSION['stateful::storedPost'] = serialize($_POST);
		$_SESSION['stateful::storedGet'] = serialize($_GET);
		$this->trigger('sys::preRedirect');
		$this->router->redirect($_SESSION['stateful::prevUrl']);
	}
	
	function preventTrigger() {
		$this->preventTrigger = true;
	}
	
	function _($path, &$info = array(), $returnVal = true) {
		$parts = $this->parseListener($path);
		if(!isset($this->components[$parts[self::CLASSNAME]])) {
			$this->components[$parts[self::CLASSNAME]] = $this->loader->component($parts[self::CLASSNAME]);
		}
		if(is_callable(array($this->components[$parts[self::CLASSNAME]], $parts[self::FUNCTIONNAME]))) {
			
			$funcName = $parts[self::FUNCTIONNAME];
			$val = $this->components[$parts[self::CLASSNAME]]->$funcName($info);
			
			if($returnVal) {
				return $val;
			}
			
			return true;
			
		} else {
			
			$val = call_user_func(array($parts[self::CLASSNAME], $parts[self::FUNCTIONNAME]), &$info);
			
			if($returnVal) {
				return $val;
			}
			
			return true;
		}
		
		return false;
	}
	
	function register($event, $listener) {
		$this->events[$event][] = $listener;
	}
	
	function trigger($event, &$info = array()) {
		$this->bm->triggered($event);
		if(isset($this->events[$event])) {
			foreach($this->events[$event] as $listener) {
				if($this->preventTrigger) {
					break;
				}
				
				if($this->_($listener, $info, false)) {
					$this->bm->handled($listener, $event);
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