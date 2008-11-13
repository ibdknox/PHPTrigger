<?php

class stateful_router {
	
	function route() {
		
		if (isset($_SERVER['REQUEST_URI'])) {
			$requestURI = $_SERVER['REQUEST_URI'];

			// Remove the folder from the beginning of REQUEST_URI.
			if (FOLDER != '' && strpos($requestURI, FOLDER) === 0) {
				$requestURI = substr($requestURI, strlen(FOLDER), strlen($requestURI));
			}

			// The only script inside the redox installation that will ever be executing is index.php at root.
			if (strpos($requestURI, '/index.php') === 0) {
				$requestURI = str_replace('/index.php', '', $requestURI);
			}

			// Remove querystring arguments.
			$requestURI = (strpos($requestURI, '?') !== false ? substr($requestURI, 0, strpos($requestURI, '?')) : $requestURI);

			$segments = array();
			
			$tok = strtok($requestURI, '/');
			
			$actualURI = '';
			$stop = false;
			
			while ($tok !== false) {
				
				if(!$stop) {					
					if(file_exists(VIEWDIR . '/' . $actualURI . '/' . $tok . '.php')) {
						$actualURI .= '/'.$tok;
						$stop = true;
					} else if (file_exists(VIEWDIR . '/' . $actualURI . '/' . $tok )) {
						$actualURI .= '/'.$tok;
					} else {
						$stop = true;
					}
				} else {
					$segments[] = $tok;
				}
				
				$tok = strtok('/');
			}
			
			if($actualURI == '') {
				if(!file_exists(VIEWDIR.'/index.php')) {
					//TODO error out
					header("HTTP/1.0 404 Not Found");
				} else {
					$actualURI = '/index';
				}
			}
			
			return array($actualURI, $segments);

		}
		
	}
	
	function redirect($url) {
		$server = $_SERVER['SERVER_NAME'];
		header('Location: http://'.$server.$url);
		exit;
	}
		
}

?>