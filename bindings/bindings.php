<?php 

config::set('profiler.display', true);
config::set('profiler.showErrors', true);

config::set('unit.url', '/test');
config::set('unit.dir', 'tests');
//config::set('unit.tests', array('tests::user_test', 'tests::test_test'));

config::set('validator.form.default', 'validate');


/*
access::restrict('/admin', array(
								'priv' => 'admin',
								'revert' => '/'
							));
							*/

//url listeners
$this->event->register('url::/index','test::event');
$this->event->register('url::/index', 'test::event2');

$this->event->register('url::/lang', 'test::ormMiniTest');



$this->event->register('url::/cool/test', 'test::event');

//form listeners
$this->event->register('submit::login', 'user::login');
$this->event->register('submit::login', 'test::event');


//custom
$this->event->register('kaboom', 'test::bombsquad');

//sys
$this->event->register('sys::preOutput', 'profiler::addProfileInfo');
$this->event->register('sys::preForm', 'validator::dispatch');

$this->event->register('url::'.config::get('unit.url'), 'unit::runUnits');
