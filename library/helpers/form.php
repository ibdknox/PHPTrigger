<?php

class form {
	
	static $posted = false;
	static $preloads;
	
	static function getVal($name) {
		if(isset(self::$preloads->$name)) {			
			return self::$preloads->$name;
		} else if(isset($_POST[$name])) {
			return $_POST[$name];
		}
		return false;
	}
	
	static function preload($object) {
		if(is_object($object)) {
			self::$preloads =& $object;
		} else if(is_array($object)) {
			self::$preloads = (object) NULL;
			foreach($object as $key=>$value) {
				self::$preloads->$key = $value;
			}
		}
	}
	
	static function forSQL($key) {
		$val = self::getVal($key);
		return ($val !== false ? treat::sql($val) : false);
	}
	
	static function forDisp($key) {
		$val = self::getVal($key);
		return ($val !== false ? treat::xss($val) : false);
	}
	
	static function textarea($name, $default = '', $attr = '') {
		$value = form::forDisp($name);
		$value = ($value ? $value : $default);
		return "<textarea name='$name' id='$name' ".form::attributeString($attr).">".$value."</textarea>";
	}

	static function text($name, $default = '', $attr = '') {
		$value = form::forDisp($name);
		$value = ($value ? $value : $default);
		return "<input type='text' name='$name' id='$name' value=\"".$value."\" ".form::attributeString($attr)."/>";
	}

	static function password($name, $attr = '') {
		return "<input type='password' name='$name' id='$name' value='' ".form::attributeString($attr)."/>";
	}

	static function select($name, $values, $attr = '') {
		$attr = form::attributeString($attr);
		$str = "<select name='$name' id='$name' $attr>\r\n";
		$postVal = form::forDisp($name);
		if(is_assoc($values)){
			foreach($values as $key => $val) {				
				$str .= "<option value='$key'".($postVal == $key ? 'selected="selected"' : '').">$val</option>";
			}
		} else {
			foreach($values as $val) {
				$str .= "<option value='$val'".($postVal == $val ? 'selected="selected"' : '').">$val</option>";
			}
		}
		$str .= "</select>";
		return $str;
	}

	static function checkbox($name, $default = false, $attr = '') {
		return "<input type='checkbox' name='$name' id='$name' value='1' ".(form::forDisp($name) != false || $default ? 'checked=\'checked\'' : '')." ".form::attributeString($attr)."/>";
	}

	static function radio($id, $group, $value, $default = false, $attr = '') {
		return "<input type='radio' name='$group' id='$id' value='$value' ".(form::forDisp($group) == $value || ($default && form::forDisp($group) == '') ? 'checked=\'\'' : '')." ".form::attributeString($attr)."/>";
	}
	
	static function attributeString($attributeArray) {
		$return = '';
		if(is_array($attributeArray)) {
			foreach($attributeArray as $key=>$value) {
				$return .= "$key=\"$value\" ";
			}
		} else if(is_string($attributeArray)) {
			$return = $attributeArray;
		}
		return $return;
	}
	
}


?>
