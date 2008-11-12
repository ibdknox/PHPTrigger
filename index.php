<?php

session_start();

ini_set('display_errors','1');
ini_set('display_startup_errors','1');
ini_set('html_errors','1');
ini_set('docref_root','http://www.php.net/');
error_reporting(E_ALL);

ob_start();
include('sys/base/bm.php');
$stateful_bm = new stateful_benchmarker();

$stateful_bm->start('sys::all');

include('sys/base/state.php');

$state = new stateful();
$state->run();

$stateful_bm->end('sys::all');

?>