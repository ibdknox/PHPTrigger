<?php

class stateful_view {
	
	var $viewFile = '';
	var $templateFile = '';
	
	function stateful_view($state) {
		$this->state =& $state;
	}
	
	function render() {
		
		$this->state->trigger('sys::preViewRender');
		if(is_file($this->viewFile)) {
			ob_start();
			include($this->viewFile);
			$yield = ob_get_contents();
			ob_end_clean();
		} else {
			$yield =  "no view defined";
		}
		$this->state->trigger('sys::postViewRender', $yield);
		
		$this->state->trigger('sys::preTemplateRender');
		if(is_file($this->templateFile)) {
			ob_start();
			include($this->templateFile);
			$output = ob_get_contents();
			ob_end_clean();
		} else {
			$output = $yield;
		}
		$this->state->trigger('sys::postTemplateRender', $output);
		
		return $output;
	}
	
	function useView($view) {
		if($view[0] == '/') {
			$this->viewFile = VIEWDIR.$view.'.php';
		} else {
			$this->viewFile = VIEWDIR.'/'.$view.'.php';
		}
	}
	
	public function partial($name, $path = PARTIALSDIR ) {
		
		ob_start();
		include($path.'/'.$name.'.php');
		$partial = ob_get_contents();
		ob_end_clean();
		
		return $partial;
		
	}
	
	function useTemplate($template) {
		$this->templateFile = LAYOUTDIR.'/'.$template.'.php';
	}
	
	function get($path, $info = array()) {
		return $this->state->_($path);
	}
	
}

?>