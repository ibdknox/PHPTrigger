<?php

session_start();

ini_set('display_errors','1');
ini_set('display_startup_errors','1');
ini_set('html_errors','1');
ini_set('docref_root','http://www.php.net/');

error_reporting(E_ALL);

ob_start();
include('sys/config/base.php');

set_error_handler('triggerErrorHandler');
set_exception_handler('triggerExceptionHandler');

$trigger_bm = trigger_load_core_object('bm');

$trigger_bm->start('sys::all');

$event = trigger_load_core_object('event');
$event->run();

?>