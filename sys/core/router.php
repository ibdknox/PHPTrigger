<?php

/**
 * Maps URLs to the appropriate view files and breaks the REQUEST_URI into pieces
 * that the event object can then utilize.
 * 
 * @author Chris Granger
 */
class trigger_router {
	
	/**
	 * Splits the REQUEST_URI into meaningful pieces (actual request url, and params)
	 * as determined by the presence of view files
	 * 
	 * @return array array with the requested url (view-based) at [0] and params at [1]
	 */
	public function route($event) {
		
		if (isset($_SERVER['REQUEST_URI'])) {
			$requestURI = $_SERVER['REQUEST_URI'];

			// Remove the folder from the beginning of REQUEST_URI.
			if (FOLDER != '' && strpos($requestURI, FOLDER) === 0) {
				$requestURI = substr($requestURI, strlen(FOLDER), strlen($requestURI));
			}

			// The only script inside the trigger installation that will ever be executing is index.php at root.
			if (strpos($requestURI, '/index.php') === 0) {
				$requestURI = str_replace('/index.php', '', $requestURI);
			}

			// Remove querystring arguments.
			$requestURI = (strpos($requestURI, '?') !== false ? substr($requestURI, 0, strpos($requestURI, '?')) : $requestURI);

			//array to represent the params that follow the actual uri
			$params = array();
			
			//tokenize the requested url
			$tok = strtok($requestURI, '/');
			
			$actualURI = ''; //the url determined by the presence of views
			$stop = false; //flag for if the final token of the url has been reached
			$folder = false; //flag for if the final token of the url is a folder
			
			while ($tok !== false) {
				
				//if we have not reached a .php file in the views dir
				if(!$stop) {		
					
					//check to see if one exists			
					if(file_exists(VIEWDIR . $actualURI . '/' . $tok . '.php')) {
						$actualURI .= '/'.$tok;
						$stop = true;
						$folder = false;
						
					//check to see if that folder exists
					} else if (file_exists(VIEWDIR . $actualURI . '/' . $tok )) {
						$actualURI .= '/'.$tok;
						$folder = true;
						
					//otherwise we have reached a final point add the token to params and stop
					} else {
						$folder = true;
						$params[] = $tok;
						$stop = true;
					}
				
				//add to the params if we have stopped url synthesis
				} else {
					$params[] = $tok;
				}
				
				$tok = strtok('/');
			}

			//if the actualURI is empty and we aren't on a folder, the request is void
			if($actualURI == '' && !$folder) {
				
				//attempt to load the root index.php file in the view dir
				if(!file_exists(VIEWDIR.'/index.php')) {
					//TODO error out
					header("HTTP/1.0 404 Not Found");
				} else {
					$actualURI = '/index';
				}
			
			//if we are on a folder and params are not empty it's an invalid request
			//params should only be attached to actual pages
			} else if($folder && !empty($params)) {
                
                //unless there's an event directly attached to this URI, it's invalid
                if(! $event->events["url::$requestURI"] ) {
                    header("HTTP/1.0 404 Not Found");
                } else {
                    $actualURI = $requestURI;
                }
			
			//if we stopped on a folder, load index.php
            //
			} else if($folder) {
				$actualURI .= '/index';
			}
			
			return array($actualURI, $params);

		}
		
	}
	
	/**
	 * Redirects to the given url and exits execution
	 * 
	 * @param string $url the url to redirect to
	 */
	public function redirect($url) {
		$server = $_SERVER['SERVER_NAME'];
		header('Location: http://'.$server.$url);
		exit;
	}
		
}
