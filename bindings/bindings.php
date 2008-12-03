<?php 

config::set('profiler.display', true);
config::set('profiler.showErrors', true);

config::set('validator.form.default', 'validate');

config::set('database.default', array(
	'benchmark'     => TRUE,
	'persistent'    => FALSE,
	'connection'    => array
	(
		'type'     => 'mysql',
		'user'     => 'root',
		'pass'     => 'root',
		'host'     => 'localhost',
		'port'     => FALSE,
		'socket'   => FALSE,
		'database' => 'test'
	),
	'character_set' => 'utf8',
	'table_prefix'  => '',
	'object'        => TRUE,
	'cache'         => FALSE,
	'escape'        => TRUE
));

/*
class Company_Model extends orm {}
class Project_Model extends orm {}


profiler::start('test');
$comp = ORM::factory('company');

$comp->select('value')->find(1);
//$comp->find(2);

//$projects = ORM::factory('project');
//$projects->select('name')->find_all();
profiler::end('test');
//var_dump($projects);
*/

//url listeners
$this->event->register('url::/index','test::event');
$this->event->register('url::/index', 'test::event2');

/*
access::restrict('/admin', array(
								'priv' => 'admin',
								'revert' => '/'
							));
							*/

$this->event->register('url::/cool/test', 'test::event');

//form listeners
$this->event->register('submit::login', 'user::login');
$this->event->register('submit::login', 'test::event');


//custom
$this->event->register('kaboom', 'test::bombsquad');

//sys
$this->event->register('sys::preOutput', 'profiler::addProfileInfo');
$this->event->register('sys::preForm', 'validator::dispatch');

?>