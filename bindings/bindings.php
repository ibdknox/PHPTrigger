<?php 

config::set('profiler.display', true);
config::set('profiler.showErrors', true);

config::set('unit.url', '/test');
config::set('unit.dir', 'tests');
//config::set('unit.tests', array('tests::user_test', 'tests::test_test'));

config::set('validator.form.default', 'validate');

config::set('database.config.group', 'localhost');
config::set('database.config.localhost', array(
		'username'     => 'root',
		'password'     => 'root',
		'hostname'     => 'localhost',
		'database' => 'test'
));

config::set('schema.company.has_one', array('companytype', 'user'));
config::set('schema.user.has_one', array('usertype', 'email'));
config::set('schema.email.has_one', array('emailtype'));
config::set('schema.companytype.belongs_to_many', 'company');

$yes = ORM::factory('company')->with('companytype', array('user', 'usertype', array('email', 'emailtype')))->order('user.name DESC')->fetch('massive');

/*$yes = ORM::factory('company')
							->with('companytype', 'user')
							->where('company.name = "?"', 'woot')
							->fetch('skookum');
							*/


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

$this->event->register('url::'.config::get('unit.url'), 'unit::runUnits');

?>