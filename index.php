<?php

session_start();

ini_set('display_errors','1');
ini_set('display_startup_errors','1');
ini_set('html_errors','1');
ini_set('docref_root','http://www.php.net/');

error_reporting(E_ALL);

ob_start();
include('sys/config/base.php');

set_error_handler('StatefulErrorHandler');
set_exception_handler('StatefulExceptionHandler');

$stateful_bm = stateful_load_core_object('bm');

$stateful_bm->start('sys::all');

$state = stateful_load_core_object('state');
$state->run();

?>