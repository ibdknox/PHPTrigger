<?php

class trigger_event {
	
	const CLASSNAME = 0;
	const FUNCTIONNAME = 1;
	const REQUESTURI = 0;
	const REQUESTSEGMENTS = 1;
	
	var $components = array();
	var $preventTriggers = array();
	
	function trigger_event() {
		
	}
	
	function run() {
		global $trigger_bm; 
		$this->bm =& $trigger_bm;
		
		$this->bm->start('sys::core_load_time');
		include( COREDIR . '/loader.php');
				
		$this->loader = new trigger_loader($this);		
		$this->loader->loadCore();
		$this->bm->end('sys::core_load_time');
		
		//clean the output buffer to prevent any extraneous whitspace from 
		//screwing up header settings
		ob_end_clean();
		
		$this->bm->start('sys::binding_time');
		$this->loader->bindings($this);
		$this->bm->end('sys::binding_time');

		//go ahead and establish our requestURI and uri-segments
		$routePieces = $this->router->route($this);
		//store these
		$this->requestURI = $routePieces[ self::REQUESTURI ];
		$this->requestSegments = $routePieces[ self::REQUESTSEGMENTS ];
		
		//create a new view object
		$this->view->useView($this->requestURI);
		
		if($this->postedForm()) {
			$this->bm->start('sys::form_trigger');
			$this->trigger('sys::preForm');
			$this->trigger('submit::'.$_POST['formName']);
			$this->trigger('sys::postForm');
			$this->bm->end('sys::form_trigger');
		} 
		
		$this->bm->start('sys::url_trigger');
		$this->trigger('sys::preUrl');
		$this->trigger('url::'.$this->requestURI);		
		$this->trigger('sys::postUrl');
		$this->bm->end('sys::url_trigger');
		
		$this->bm->start('sys::view_render');
		$file = $this->view->render();
		$this->trigger('sys::postRender', $file);
		$this->bm->end('sys::view_render');
		
		$_SESSION['trigger::prevUrl'] = $this->requestURI;
		
		$trigger_bm->end('sys::all');
		
		$this->trigger('sys::preOutput', &$file);
		echo $file;
		
	}
	
	public function postedForm() {
		return isset($_POST['formName']) ? $_POST['formName'] : false;
	}
	
	function segment($num) {
		return isset($this->requestSegments[$num-1]) ? $this->requestSegments[$num-1] : false;
	}
	
	function revert() {
		$url = $_SESSION['trigger::prevUrl'];
		$this->trigger('sys::preRedirect', $url);
		$this->router->redirect($url);
	}
	
	function &getComponent($class) {
		return $this->components[$class];
	}
	
	function preventTrigger($path = 'all') {
		$this->preventTriggers[$path] = true;
	}
	
	function call($path, &$info = array(), $returnVal = true) {
		$parts = $this->parseListener($path);
		
		if(!isset($this->components[$parts[self::CLASSNAME]])) {
			$this->components[$parts[self::CLASSNAME]] =& $this->loader->component($parts[self::CLASSNAME], $parts['dir']);
		}
		if(is_callable(array($this->components[$parts[self::CLASSNAME]], $parts[self::FUNCTIONNAME]))) {
			
			$funcName = $parts[self::FUNCTIONNAME];
			$val = $this->components[$parts[self::CLASSNAME]]->$funcName($info);

			if($returnVal) {
				return $val;
			}
			
			return true;
			
		} else if (is_callable(array($parts[self::CLASSNAME], $parts[self::FUNCTIONNAME]))) {
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
	
	function triggerPrevented($eventName) {
		if(isset($this->preventTriggers['all']) || isset($this->preventTriggers[$eventName])) {
			return true;
		}
		
		foreach($this->preventTriggers as $name => $value) {
			if(stripos($eventName, $name) !== false) {
				return true;
			}
		}
		
		return false;
	}
	
	function trigger($event, &$info = array()) {
		
		if($this->triggerPrevented($event)) {
			return;
		}
		
		$this->bm->triggered($event);
		if(isset($this->events[$event])) {
			foreach($this->events[$event] as $listener) {
				
				if($this->call($listener, $info, false)) {
					$this->bm->handled($listener, $event);
				}
				
				if($this->triggerPrevented($event)) {
					return;
				}
				
			}
		}
	}
	
	function registerComponentPath($pathPart, $directory) {
		$this->componentPaths[$pathPart] = $directory;
	}
	
	function parseListener($path) {
		$pieces = explode('::', $path);
		$piecesCount = count($pieces);
		if($piecesCount > 2 && isset($this->componentPaths[$pieces[0]])) {
			$type = array_shift($pieces);
			$pieces['dir'] = $this->componentPaths[$type];
		} else if($piecesCount > 2) {
			$numPathParts = $piecesCount - 2;
			$pieces['dir'] = COMPONENTDIR;
			for($c = 0; $c < $numPathParts; $c++) {
				$pieces['dir'] .= '/'.array_shift($pieces);
			}
		} else {
			$pieces['dir'] = COMPONENTDIR;
		}
		return $pieces;
	}
	
}


