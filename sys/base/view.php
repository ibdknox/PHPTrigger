<?php

class stateful_view {
	
	var $viewFile = '';
	var $templateFile = '';
	
	function render() {
		if(is_file($this->viewFile)) {
			ob_start();
			include($this->viewFile);
			$yield = ob_get_contents();
			ob_end_clean();
		} else {
			$yield =  "no view defined";
		}
		
		if(is_file($this->templateFile)) {
			ob_start();
			include($this->templateFile);
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		} else {
			return $yield;
		}
		
	}
	
	function useView($view) {
		if($view[0] == '/') {
			$this->viewFile = VIEWDIR.$view.'.php';
		} else {
			$this->viewFile = VIEWDIR.'/'.$view.'.php';
		}
	}
	
	function useTemplate($template) {
		$this->templateFile = LAYOUTDIR.'/'.$template.'.php';
	}
	
	function _($path, $info = array()) {
		global $state;
		return $state->_($path);
	}
	
}

?>