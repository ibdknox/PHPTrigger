<?php

class gallery extends trigger_component {
	
	const IMAGEDIR = 'out/assets/images/';
	
	function gallery() {
		parent::__construct();
		
		$this->OUTPUTDIR = FOLDER.'/assets/images/';
		
		$this->group = $this->event->segment(1);
		$this->picName = $this->event->segment(2);
	}
	
	public function pics() {
		
		$pics = array();
		
		if(!$this->picName) {	
			foreach (new DirectoryIterator(self::IMAGEDIR.$this->group) as $pic) {
				if(substr($pic, 0, 1) != '.') {
					$pics[] = $this->OUTPUTDIR."$this->group/$pic";
				}
			}
		} else {
			$pics[] = $this->OUTPUTDIR.$this->group.'/'.$this->picName;
		}
		
		return $pics;
	}
	
	public function group() {
		return $this->group;
	}
	
}