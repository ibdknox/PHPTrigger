<?php

/*	CLASS Benchmark
 *	Used to gather time information for the profiler.
 *
 *	@author Chris
 */

class bm {
	static $marks = array(); // Associative array of start and end mark times
	static $queries = array(); // Array of all queries performed during the controller execution
	static $queryNum = 0;
	static $errors = array();
	static $debug = array();

	/*	logQuery
	 *	This function logs any query performed via bm::db->query().
	 *	Records both the start-time of the query and the actual sql executed.
	 *
	 *	@param $sql = the text of the query
	 *	@author Chris
	 */
	static function logQuery($sql) {
		bm::$queries[bm::$queryNum][0] = $sql;
		bm::$queries[bm::$queryNum][1] = bm::microtime_float();
		return bm::$queryNum;
	}

	/*	failedQuery
	 *	This function notes a failed query and stores the error message.
	 *
	 *	@author Chris
	 */
	static function failedQuery($error) {
		bm::$queries[bm::$queryNum][2] = $error;
	}

	/*	endQuery
	 *	This function is called after log query to figure out the end time of the query
	 *	and to increment the number of queries recorded
	 *
	 *	@author Chris
	 */
	static function endQuery() {
		if (!isset(bm::$queries[bm::$queryNum][2])) {
			bm::$queries[bm::$queryNum][1] = bm::microtime_float()-bm::$queries[bm::$queryNum][1];
		} else {
			bm::$queries[bm::$queryNum][1] = bm::$queries[bm::$queryNum][2];
		}
		bm::$queryNum++;
	}

	/*	start
	 *	This function creates a new mark in the marks array with the index of $name.
	 *	It also stores the beginning time in second dimension array.
	 *
	 *	@param $name = name of the mark
	 *	@author Chris
	 */
	static function start($name) {
		bm::$marks[$name][0] = bm::microtime_float();
	}

	/*	end
	 *	This function adds the end time to the mark with key $name
	 *
	 *	@param $name = name of the mark
	 *	@author Chris
	 */
	static function end($name) {
		bm::$marks[$name][1] = bm::microtime_float();
	}

	/*	time
	 *	This function returns the total time between the start and end of a mark with index $name
	 *
	 *	@param $name = name of the mark
	 *	@return float
	 *	@author Chris
	 */
	static function time($name) {
		return bm::$marks[$name][1] - bm::$marks[$name][0];
	}
	
	static function addError($level, $file, $line, $message) {
		bm::$errors[] = array($level, $file, $line, $message);
	}

	/*	microtime_float
	 *	This utility function returns the float version of microtime()
	 *
	 *	@return float of current microtime
	 */
	static function microtime_float() {
	    list($usec, $sec) = explode(" ", microtime());
	    return ((float)$usec + (float)$sec);
	}
	
	static function debug($var) {
		$tree = debug_backtrace();
		$file = preg_replace('/\/.*\//','',$tree[0]['file']);
		bm::$debug[] = array(
				"file"=>$file,
				"line"=>$tree[0]['line'],
				"value"=>$var
			);
	}	
	
	static function benchmarkDisplay($values, $color = '#000', $label = 'BENCHMARKS') {
		$buildstring = '<a name="'.strtolower($label).'"></a><fieldset id="bm_'.strtolower($label).'" style="border: 1px solid '.$color.'; background: #EEE; margin-bottom: 2em; padding: .5em 1em 1em;">
			<legend style="color: '.$color.'; padding: 0 .5em; border: 1px solid '.$color.'; background: #EEE; margin-top: 0; line-height: 200%;">'.$label.'</legend>
			<table cellspacing="1" style="background: #FFF; color: '.$color.'; width: 100%;">';
				switch ($label) {
					case 'ERRORS':
						$buildstring .= '<thead><tr style="text-align: left;"><th style="background: #CCC; text-align: center;">#</th><th style="background: #CCC;">Level</th><th style="background: #CCC;">File</th><th style="background: #CCC; text-align: center;">Line</th><th style="background: #CCC;">Message</th></tr></thead>';
					break;
					case 'QUERIES':
						$buildstring .= '<thead><tr style="text-align: left;"><th style="background: #CCC; text-align: center;">#</th><th style="background: #CCC;">Query</th><th style="background: #CCC;">Result</th></tr></thead>';
					break;
					case 'BENCHMARKS':
						$buildstring .= '<thead><tr style="text-align: left;"><th style="background: #CCC;">Benchmark</th><th style="background: #CCC;">Time</th></tr></thead>';
					break;
					case 'DEBUG':
						$buildstring .= '<thead><tr style="text-align: left;"><th style="background: #CCC;">File</th><th style="background: #CCC;">Line</th><th style="background: #CCC;">Value</th></tr></thead>';
					break;
					default:
						$buildstring .= '<thead><tr style="text-align: left;"><th style="background: #CCC;">Field</th><th style="background: #CCC;">Value</th></tr></thead>';
					break;
				}
				$buildstring .= '<tbody>';
				switch ($label) {
					case 'ERRORS':
						$count = 1;
						foreach ($values as $value) {
							$buildstring .= '<tr><td style="background: #DDD; text-align: center;">'.$count++.'</td><td style="background: #DDD;">'.$value[0].'</td><td style="background: #DDD;">'.$value[1].'</td><td style="background: #DDD; text-align: center;">'.$value[2].'</td><td style="background: #DDD;">'.$value[3].'</td></tr>';
						}
					break;

					case 'QUERIES':
						$count = 1;
						foreach ($values as $key => $value) {
							$buildstring .= '<tr><td style="background: #DDD; text-align: center;"><a name="queries'.$count.'">'.$count++.'</a></td><td style="background: #DDD;">'.treat::xss($value[0]).'</td><td style="background: #DDD;">'.treat::xss($value[1]).'</td></tr>';
						}
					break;

					case 'BENCHMARKS':
					case '$_POST':
					case '$_GET':
						foreach ($values as $key => $value) {
								$buildstring .= '<tr><td style="width: 25%; background: #DDD;">'.treat::xss($key).'</td><td style="width: 75%; background: #DDD;">'.treat::xss($value).'</td></tr>';
						}
					break;
					case 'STATEMACHINE':
						foreach ($values as $value) {
							$val = (is_string($value[1]) ? treat::xss($value[1]) : '<pre>'.print_r($value[1], true).'</pre>');
							$buildstring .= '<tr><td style="width: 25%; background: #DDD;">'.treat::xss($value[0]).'</td><td style="width: 75%; background: #DDD;">'.$val.'</td></tr>';
						}
					break;
					case 'DEBUG':
						foreach ($values as $value) {
							$val = print_r($value['value'], true);
							$buildstring .= '<tr><td style="background: #DDD;">'.$value['file'].'</td><td style="background: #DDD;">'.$value['line'].'</td><td style="background: #DDD;"><pre>'.$val.'</pre></td></tr>';
						}
					break;

					default:
						foreach ($values as $key => $value) {
							if (is_array($value)) {
								$buildstring .= '<tr><td style="width: 25%; background: #DDD;">'.treat::xss($value[0]).'</td><td style="width: 75%; background: #DDD;">'.treat::xss($value[1]).'</td></tr>';
							} else {
								$buildstring .= '<tr><td style="width: 25%; background: #DDD;">'.treat::xss($key).'</td><td style="width: 75%; background: #DDD;">'.treat::xss($value).'</td></tr>';
							}
						}
					break;
				}

				$buildstring .= '</tbody></table>
		</fieldset>';

		return "\r\n\t".$buildstring."\r\n";
	}

	static function addProfileInfo($file) {

		if (PROFILER && !STATUS) {
			$rep = '<div style="padding: 2em; clear: both;">';

			/* Time Benchmarks. */
			if (count(bm::$marks) > 0) {
				$values = array();
				foreach(array_keys(bm::$marks) as $key) { $values[$key] = bm::time($key); }
				$rep .= self::benchmarkDisplay($values, '#060', 'BENCHMARKS');
			}	

			/* Queries. */
			if (count(bm::$queries) > 0) {
				$rep .= self::benchmarkDisplay(bm::$queries, '#009', 'QUERIES');
			}
			
			/* Debug. */
			if (count(bm::$debug) > 0) {
				$values = array();
				$rep .= self::benchmarkDisplay(bm::$debug, '#288', 'DEBUG');
			}

			/* $_POST Values. */
			if (isset($_POST) && count($_POST) > 0) {
				$rep .= self::benchmarkDisplay($_POST, '#900', '$_POST');
			}

			/* $_GET Values. */
			if (isset($_GET) && count($_GET) > 0) {
				$rep .= self::benchmarkDisplay($_GET, '#909', '$_GET');
			}

			/* State Machine. */
			if (count(state::$sm) > 0) {
				$values = array();
				foreach(state::$sm as $key => $value) { $values[] = array('Var: '.$key, $value); }
				$rep .= self::benchmarkDisplay($values, '#055', 'STATEMACHINE');
			}

			$rep .= '</div>';
			$file = str_replace('</body>',$rep."\r\n</body>",$file);
		}

		if (!STATUS) {
			/* Errors. */
			if (count(bm::$errors) > 0) {
				$rep = '<div style="padding: 2em; clear: both;">';
				$rep .= self::benchmarkDisplay(bm::$errors, '#900', 'ERRORS');
				$rep .= '</div>';
				$file = str_replace('<body>',"<body>\r\n".$rep, $file);
			}
		}

		echo $file;
	}

}

?>