<?php 

//url listeners
$state->register('url::/index','test::event');
$state->register('url::/index', 'test::event2');

$state->register('url::/cool/test', 'test::event');

//form listeners
$state->register('submit::login', 'user::login');
$state->register('submit::login', 'test::event');


//custom
$state->register('kaboom', 'test::bombsquad');

?>