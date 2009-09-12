<?php

class unit_test {
	
	public $errors;
	public $numtests;
    public $numPassed;
    public $numFailed;
	public $failedtests;
	private $curTestFunc;

    public $failed;
	
	public function __construct() {
		$this->event =& getEventObject();
		$this->errors = array();
		$this->failedtests = array();
		$this->passedtests = array();
		$this->numtests = 0;
	}
	
	public function run() {

        profiler::start("TotalTestTime");
		$methods = get_class_methods($this);
		foreach($methods as $m) {
			if(stristr($m, '_test')) {
                profiler::start("curTest");
				$this->passedtests[] = $m;
				if(in_array('setup', $methods)) {
					call_user_func(array(&$this, 'setup'), array());
				}
				$this->numtests++;
				$this->curTestFunc = $m; 
				call_user_func(array(&$this, $m), array());
                profiler::end("curTest");
                $this->times[$m] = profiler::time("curTest");
			}
		}
		foreach($this->failedtests as $f) {
			$key = array_search($f,$this->passedtests);
			unset($this->passedtests[$key]);
		}

        $this->numPassed = $this->numtests - $this->numFailed;
        profiler::end("TotalTestTime");
        $this->times['total'] = profiler::time("TotalTestTime");

        profiler::removeMark("curTest");
        profiler::removeMark("TotalTestTime");
		
	}
	
	public function assertTrue($a1) {
		if(!$a1) {
			$this->throwError("expected true");
		}
	}

	public function assertFalse($a1) {
		if($a1) {
			$this->throwError("expected false");
		}
	}

	public function assertEquals($a1, $a2) {
		if($a1 != $a2) {
            $this->throwError(array($a1, $a2));
		}
	}
	
	public function throwError($msg) {

        $this->failed = true;
        $this->numFailed++;

		$stack = debug_backtrace();
		$curfunction = $stack[2]["function"];
		
		if($curfunction != $this->curTestFunc) {
			//display the test name
			$curfunction = $this->curTestFunc." ($curfunction)";
		}
		
		$curline = $stack[1]["line"];
		$this->errors[] = array("line"=>$curline, "function"=>$curfunction, "msg"=>$msg);
		if(!in_array($this->curTestFunc, $this->failedtests)) {
			$this->failedtests[] = $this->curTestFunc;
		}
	}
}

class unit {
	
	static $units;
	static $event;
    static $time; 
	
	static function runUnits() {
        profiler::start("suiteTime");
		self::$units = array();
		self::$event =& getEventObject();

        self::$event->view->useView('helpers/unit/output.php');
		
		if(! $dir = config::get('unit.dir') ) {
			foreach(config::get('unit.tests') as $test) {
				self::executeTest($test);
			}
		} else {
			if( stripos($dir, '::') === false ) {
				$dir .= '::';
			}
			
			$dirPath = COMPONENTDIR . '/' . str_replace('::', '/', $dir);
			foreach (new DirectoryIterator($dirPath) as $entry) {
				if( substr($entry, -4) == '.php' ) {
					self::executeTest( $dir . basename($entry, '.php') );
				}
			}
		}
        profiler::end("suiteTime");
        self::$time = profiler::time("suiteTime");

        profiler::removeMark("suiteTime");
	}
	
	static function executeTest($test) {
		self::$event->call($test.'::run');
		$parts = explode('::', $test);
		self::$units[$test] =& self::$event->getComponent(end($parts));
	}
	
    static function passFail($result) {
        return $result->failed ? "failed" : "passed";
    }

    static function passFailHeader($result) {

        if($result->failed) {
            return "$result->numFailed " . inflector::singular("tests", $result->numFailed) . " failed";
        } else {
            return "$result->numPassed " . inflector::singular("tests", $result->numPassed) . " passed";
        }

    }

    static function formatError($failedResult) {
        
        if( is_array($failedResult['msg']) ) {
            //run a diff on the two items to find out why they're different
            $str = "<span>Diff:</span>";
            $str .= '<p>'. diff::inline(array($failedResult['msg'][0]), array($failedResult['msg'][1])) . '</p>';
            return $str;
        }

        return $failedResult['msg'];

    }
	
}
