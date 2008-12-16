<?php 

class ORM {
	
	static $link = false;
	
	private $table = '';
	private $order = array();
	private $where = array();
	private $join = array();
	private $limit = '';
	private $offset = '';
	
	
	private $secondaryKey = '';
	private $primaryKey = 'ID';
	
	
	static function factory($table) {
		return new ORM($table);
	}
	
	public function __construct($table) {
		
		if( !self::$link ) {
			self::$link = $this->connect();
		}
		$this->table = $table;
		
	}
	
	public function escape($text) {
		return mysql_real_escape_string($text);
	}
	
	public function fetch($ID = false) {

		$this->limitByKey($ID);
		
		$sql = "SELECT $this->table.* FROM $this->table";
		
		if( !empty( $this->where ) ) {
			$sql .= ' WHERE ';
			foreach($this->where as $where) {
				$sql .= $where;
			}
		}
		
		profiler::debug($sql);
		profiler::debug($this);
	}
	
	public function limitByKey($key) {
				
		if($key === false) {
			return;
		}

		//this is probably the pkey if it is numeric
		if( is_int( $key ) ) {			
			
			$this->andWhere($this->table.'.'.$this->primaryKey . ' = "?"', $key);
		
		//otherwise see if a secondary key is set
		} else if($this->secondaryKey) {
			
			$this->andWhere($this->table . '.' . $this->secondaryKey . ' = "?"', $key);
			
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
				while($row = mysql_fetch_assoc($result)) {
					$ob[] = $row;
				}
			}
				
				$this->numrows = mysql_num_rows($result);
										
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
	
}