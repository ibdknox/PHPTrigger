<?php

class trigger_view {
	
	private $viewFile = '';
	private $templateFile = '';
	
	function __construct($event) {
		$this->event =& $event;
	}
	
	public function render() {
		
		$this->event->trigger('sys::preViewRender');
		
		if(is_file($this->viewFile)) {
			
			$yield = $this->getView($this->viewFile);
			
		} else {
			$yield =  "no view defined";
		}
		$this->event->trigger('sys::postViewRender', $yield);
		
		$this->event->trigger('sys::preTemplateRender');
		if(is_file($this->templateFile)) {
			
			$output = $this->getView($this->templateFile, $yield);
			
		} else {
			$output = $yield;
		}
		$this->event->trigger('sys::postTemplateRender', $output);
		
		return $output;
	}
	
	private function getView($file, $yield = '') {
		ob_start();
		include($file);
		$result = ob_get_contents();
		ob_end_clean();
		
		return $result;
	}
	
	public function useView($view) {
		
		if( !$view  || $this->viewFile === false ) {
			$this->viewFile = false;
			return;
		}
		
		if($view[0] == '/') {
			$this->viewFile = VIEWDIR.$view.'.php';
		} else {
			$this->viewFile = VIEWDIR.'/'.$view.'.php';
		}
	}
	
	public function partial($___name, $___vars = array(), $___path = PARTIALSDIR ) {

		if( is_array( $___vars ) ) {
			extract( $___vars );
		}
		
		ob_start();
		include($___path.'/'.$___name.'.php');
		$___partial = ob_get_contents();
		ob_end_clean();
		
		return $___partial;
		
	}
	
	public function useTemplate($template) {
		
		if( !$template || $this->templateFile === false ) {
			$this->templateFile = false;
			return;
		}
		
		$this->templateFile = LAYOUTDIR.'/'.$template.'.php';
		
	}
	
	public function get($path, $info = array()) {
		return $this->event->call($path);
	}
	
}

?>