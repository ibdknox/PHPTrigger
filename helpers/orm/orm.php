<?php 

class ORM {
	
	static $link = false;
	
	private $table = '';
	private $order = array();
	private $where = array();
	private $join = array();
	private $values = array();
	private $from = array();
	private $selectModifiers = array();
	private $limit = '';
	private $offset = '';
	
	private $getCount = false;
	
	private $numRows = 0;
	private $numFound = 0;
	
	private $result;
	private $changed = false;
	private $objectTree;
	
	private $primaryKey = 'ID';
	private $secondaryKey = 'name';
	
	
	static function factory($table) {
		return new ORM($table);
	}
	
	public function __construct($table) {
		
		if( !self::$link ) {
			self::$link = $this->connect();
		}
		
		$this->table = $table;
		$this->from($table);
		
		$this->objectTree = (object) NULL;
		$this->result = (object) NULL;
	}
	
	public function fetch($ID = false) {
		
		$this->numFound = 0;

		$this->limitByKey($ID);
		
		$sql = "SELECT ";
		
		$sql .= $this->concatSection('selectModifiers', '', ' ');
		$sql .= $this->concatSection('values', ' ', ', ');
		$sql .= $this->concatSection('from', ' FROM ', ', ').' ';
		
		if( !empty( $this->join ) ) {
			foreach($this->join as $join) {
				$sql .= $join[0]. ' JOIN '.$join[1] .' ON '.$join[2].' ';
			}
		}
		
		$sql .= $this->concatSection('where', 'WHERE ');
		$sql .= $this->concatSection('group', ' GROUP BY ');
		$sql .= $this->concatSection('order', ' ORDER BY ');
		
		if( !empty( $this->limit ) ) {
			$sql .= " LIMIT $this->limit ";
		}
		
		if( !empty( $this->offset ) ) {
			$sql .= " OFFSET $this->offset ";
		}
		
		profiler::debug($sql);
		profiler::debug($this);
	}
	
	public function save($ID = false) {
		
		if( !$this->changed ) {
			return $this;
		}
		
		if(!$this->ID && $ID === false) {
			$add = true;
		} else {
			$this->limitByKey($ID);
		}
		
		//perform an add or an update as appropriate
		
	}
	
	public function delete($ID = false) {
		
	}
	
	public function update($ID = false) {
		
	}
	
	public function add($objectsToAdd) {
		
	}
	
	public function getNumFound() {
		
		if( !$this->numFound ) {
			$result = $this->query('SELECT FOUND_ROWS() as count');
			$this->numFound = $result[0]->count;
		}
		return $this->numFound; 
		
	}
	
	private function concatSection($section, $prefix = '', $separator = '') {
		
		$result = '';
		
		if( !empty( $this->$section ) ) {
			$result .= $prefix;
			foreach($this->$section as $s) {
				$result .= $s.$separator;
			}
			
			$result = rtrim($result, $separator);
		}
		
		return $result;
	}
	
	public function with() {
		
		$tables = func_get_args();
				
		$this->internalWith($tables, $this->table, $this->objectTree);
		
		return $this;
		
	}
	
	private function internalWith($tables, $curTable, $curBranch) {
		
		$recurse = false;
		
		foreach($tables as $rel) {
			
			if( is_array($rel) ) {
				$cur = array_shift($rel);
				$subRel = $rel;
				$rel = $cur;
				$recurse = true;
			}
		
			$curBranch->$rel = (object) NULL;
		
			$relationships = config::get('schema.'.$curTable);

			if($key = $this->recursive_array_search($rel, $relationships)) {
			
				$this->$key($rel, $curTable);
				
			}
			
			if( $recurse ) {
				$this->internalWith($subRel, $rel, $curBranch->$rel);
				$recurse = false;
			}
		}
		
	}
	
	private function recursive_array_search($needle, $haystack) {
		
		foreach($haystack as $key => $value) {
			if( is_array( $value ) ) {
				$found = $this->recursive_array_search($needle, $value);
				if($found !== false) {
					return $key;
				}
			} else {
				if( $value == $needle) {
					return $key;
				}
			}
		}
		
		return false;
	}
	
	private function has_one($tableToJoin, $origTable) {
		$this->leftJoin($tableToJoin, "$origTable.{$tableToJoin}_ID = $tableToJoin.ID");
	}
	
	private function belongs_to_many($tableToJoin, $origTable) {
		$this->leftJoin($tableToJoin, "$tableToJoin.{$origTable}_ID = $tableToJoin.ID");		
	}
	
	private function has_many($tableToJoin, $origTable) {
		$this->leftJoin($tableToJoin, "$origTable.ID = $tableToJoin.{$origTable}_ID");
	}
	
	private function has_and_belongs_to_many($tableToJoin, $origTable) {
		if($tableToJoin[0] > $origTable[0]) {
			$junctionName = $origTable.'_'.$tableToJoin;
		} else {
			$junctionName = $tableToJoin.'_'.$origTable;
		}
		
		$this->leftJoin($junctionName, "$origTable.ID = $junctionName.{$origTable}_ID");
		$this->leftJoin($tableToJoin, "$junctionName.{$tableToJoin}_ID = $tableToJoin.ID");
	}
	
	private function limitByKey($key) {
				
		if($key === false) {
			return;
		}

		//this is probably the pkey if it is numeric
		if( ctype_digit( $key ) ) {			
			
			$this->andWhere($this->table.'.'.$this->primaryKey . ' = "?"', $key);
		
		//otherwise see if a secondary key is set
		} else if( $this->secondaryKey && is_string( $this->secondaryKey ) ) {
			
			$this->andWhere($this->table . '.' . $this->secondaryKey . ' = "?"', $key);
			
		}
		
		
	}
	
	public function from() {
		
		$tables = func_get_args();
		
		foreach($tables as $table) {
			$this->from[] = $table;
		}
		
	}
	
	public function select() {
		
		$values = func_get_args();
		
		foreach($values as $value) {
			$this->values[] = $value;
		}
		
	}
	
	public function where($where, $value = false) {
		
		if( $value !== false ) {
			
			
			if( is_array($value) ) {
				
				$valString = '';
				foreach($value as $val) {
					$valString =  '"'.$this->escape($val).'", ';
				}
				$valString = rtrim($valString, ', ');
				
			} else {
				
				$valString = $this->escape($value);
				
			}
			
			$where = str_replace('?', $valString, $where);
		}
		
		$this->where[] = $where;
		
		return $this;
	}
	
	public function andWhere($where, $value) {
		
		$andWhere = $where;
		if( !empty( $this->where ) ) {
			$andWhere = ' AND '.$where;
		}
		
		$this->where($andWhere, $value);
	}
	
	public function orWhere($where, $value) {
		
		$orWhere = $where;
		if( !empty( $this->where ) ) {
			$andWhere = ' OR '.$where;
		}
		
		$this->where($orWhere, $value);
	}
	
	public function order($order) {
		$this->order[] = $order;
		
		return $this;
	}
	
	public function limit($limit) {
		$this->limit = $limit;
		
		return $this;
	}
	
	public function offset($offset) {
		$this->offset = $offset;
		
		return $this;
	}
	
	public function join($type, $table, $on) {
		$this->join[] = array($type, $table, $on);
		
		return $this;
	}
	
	public function innerJoin($table, $on) {
		
		$this->join('INNER', $table, $on);
		
		return $this; 
	}
	
	public function leftJoin($table, $on) {
		
		$this->join('LEFT', $table, $on);
		
		return $this; 
	}
	
	public function escape($text) {
		return mysql_real_escape_string($text);
	}
	
	public function calcFound() {
		$this->selectModifiers[] = 'SQL_CALC_FOUND_ROWS ';
	}
	
	public function distinct() {
		$this->selectModifiers[] = 'DISTINCT ';
	}
	
	public function query($sql) {
		
		$querynum = profiler::logQuery($sql); //benchmark the query
		$result = mysql_query($sql); //execute the sql
		profiler::endQuery($sql); //end benchmark

		$ob = array();
		
		if (!$result) {
			trigger_error('mysql: ['.mysql_errno().'] '.mysql_error().(PROFILER ? ' <a href="#queries'.($querynum + 1).'">(see profiler query '.($querynum + 1).')</a>' : ''), E_USER_WARNING);
			profiler::failedQuery('ERROR '.mysql_errno().': '.mysql_error());
		} else {
			
			if( is_resource($result) ) {
				while($row = mysql_fetch_object($result)) {
					$ob[] = $row;
				}
			}
				
				$this->numRows = mysql_num_rows($result);
										
		}
		
		
		return $ob;
	}
	
	public function connect() {
		
		$group = config::get('database.config.group');
		$dbconfig = config::get('database.config.' . $group);
		$link = mysql_connect($dbconfig['hostname'],$dbconfig['username'],$dbconfig['password']);
		mysql_select_db($dbconfig['database'], $link);

		return $link;
		
	}
	
	public function __set($name, $value) {
		if( !isset($this->result->$name) || $this->result->$name != $value) {
			$this->changed = true;
		}
		
		$this->result->$name = $value;
		return true;
	}
	
	public function __get($name) {
		if( isset($this->result->$name) ) {
			return $this->result->$name;
		}
		
		return false;
	}
	
}