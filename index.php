<?php

session_start();

ini_set('display_errors','1');
ini_set('display_startup_errors','1');
ini_set('html_errors','1');
ini_set('docref_root','http://www.php.net/');
error_reporting(E_ALL);

ob_start();
include('sys/base/bm.php');
$bm = new bm();

$bm->start('all');
include('sys/base/state.php');
$state = new stateful();

include('meta/bindings.php');

$state->run();

$bm->end('all');
echo $bm->time('all');

?>