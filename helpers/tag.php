<?php

class tag {
	
	static function a($page, $text, $attr = '') {
		/*
		if(strpos($_SERVER['REQUEST_URI'], FOLDER."/$page") !== false) {
			if(is_array($attr)) {
				if(isset($attr['class'])) {
					$attr['class'] .= ' focused';
				} else {
					$attr['class'] = 'focused';
				}
			} else {
				$attr = array('class'=>'focused');
			}
		}
		*/
		return '<a href="'.FOLDER."/$page\" ".form::attributeString($attr).'>'.$text.'</a>';
	}
	
	static function img($filename, $attributes = false) {
		return '<img src="'.FOLDER."/assets/images/$filename\" ".form::attributeString($attributes).'/>';
	}
	
	static function script($scriptname) {
		return '<script type="text/javascript" src="'.FOLDER."/assets/js/$scriptname.js\"> </script>";
	}

	static function style($filename, $ie7 = false) {
		$tag = '<style type="text/css">'."@import url('".FOLDER."/assets/css/$filename.css');</style>";
		if($ie7) {
			return '<!--[if IE 7]>'.$tag.'<![endif]-->';
		} else {
			return $tag;
		}		
	}
	
}


?>