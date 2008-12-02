<?php

define('FOLDER', '');
define('OUTPUTDIR', 'output');
define('VIEWDIR', OUTPUTDIR.'/views');
define('LAYOUTDIR', OUTPUTDIR.'/layouts');
define('PARTIALSDIR', OUTPUTDIR.'/partials');
define('COMPONENTDIR', 'components');
define('BINDINGSDIR', 'bindings');
define('LIBRARYDIR', 'library');
define('HELPERSDIR', LIBRARYDIR.'/helpers');
define('LIBCOMPONENTSDIR', LIBRARYDIR.'/components');
define('EXTENSIONSDIR', LIBRARYDIR.'/extensions');
define('SYSDIR', 'sys');
define('CONFIGDIR', SYSDIR.'/config');
define('COREDIR', SYSDIR.'/core');

function stateful_load_core_object($name, $state = null) {
	include( COREDIR . "/$name.php" );
	if(file_exists(EXTENSIONSDIR . "/$name.php")) {
		include(EXTENSIONSDIR . "/$name.php");
		$className = $name . '_extension';
		if(class_exists($className)) {
			return new $className($state);
		}
	}
	$className = 'stateful_' . $name;
	var_dump($state);
	return new $className($state);
}

function &getStateObject() {
	global $state;
	return $state;
}

/**
 * 
 */
function __autoload($class_name) {
	
	$locations = array(
						HELPERSDIR . '/'. $class_name . '.php',
						HELPERSDIR . '/' . $class_name . '/' . $class_name . '.php',
					);
    
	foreach($locations as $loc) {
		if(file_exists($loc)) {
			require($loc);
			break;
		}
	}
	
}

function StatefulErrorHandler($level, $message, $file, $line) {

	$errortype = array(
		E_ERROR => 'Error',
		E_WARNING => 'Warning',
		E_PARSE => 'Parsing Error',
		E_NOTICE => 'Notice',
		E_CORE_ERROR => 'Core Error',
		E_CORE_WARNING => 'Core Warning',
		E_COMPILE_ERROR => 'Compile Error',
		E_COMPILE_WARNING => 'Compile Warning',
		E_USER_ERROR => 'User Error',
		E_USER_WARNING => 'User Warning',
		E_USER_NOTICE => 'User Notice',
		E_STRICT => 'Runtime Notice'
		//E_RECOVERABLE_ERROR => 'Catchable Fatal Error'
	);

	switch ($level) {
		case E_USER_ERROR:
			echo "<b>My ERROR</b> [$level] $message<br />\n";
			echo "  Fatal error on line $line in file $file";
			echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
			echo "Aborting...<br />\n";
			exit(1);
		break;
		case E_STRICT:
		break;
		default:
			if(!isset($errortype[$level])) {
				$error = 'Error';
			} else {
				$error = $errortype[$level];
			}
			profiler::addError($error, $file, $line, $message);
		break;
	}
}

/**
 * @todo make this actually do something.
 */
function StatefulExceptionHandler($exception) {
	
	echo "exception thrown".$exception->getFile().$exception->getLine().$exception->getMessage();
	//profiler::addError('Exception', $exception->getFile(), $exception->getLine(), $exception->getMessage());
			
}

?>