<?php 

class ORM {
	
	static $link = false;
	
    private $_sql = "";

	private $_from = array();
	private $_order = array();
	private $_where = array();
	private $_joins = array();
    private $_modifiers = array();
	private $_limit = '';
	private $_offset = '';
	
	private $_select = array();
    private $_selectOrder = array();

    private $relationships = array();
	
	private $getCount = false;
	
	private $numRows = 0;
	private $numFound = 0;
	
	private $result;
	private $changed = false;
	private $objectTree;
	
	private $primaryKey = 'ID';
	private $secondaryKey = false;
	
	
	static function factory($table) {
		return new ORM($table);
	}
	
	static function object($table) {
		return new resultObject($table, array( (object) null ));
	}
	
	public function __construct($table) {
		
		if( !self::$link ) {
			self::$link = $this->connect();
		}
		
		$this->table = $table;
		$this->from($table);
		$this->defaultValues[] = $table.'.*';
		
		$this->objectTree = (object) NULL;
		$this->result = (object) NULL;
	}
	
	public function fetch($ID = false) {
		
		$this->numFound = 0;

		$this->limitByKey($ID);
		
		$sql = "SELECT";
		
		$sql .= $this->concatSection('selectModifiers', ' ', ' ');
		
		if(!empty($this->values)) {
			$sql .= $this->concatSection('values', ' ', ', ');
		} else {
			$sql .= $this->concatSection('defaultValues', ' ', ', ');
		}
		
		$sql .= $this->concatSection('from', ' FROM ', ', ');
		
		if( !empty( $this->join ) ) {
			foreach($this->join as $join) {
				$sql .= ' '.$join[0]. ' JOIN '.$join[1] .' ON '.$join[2];
			}
		}
		
		$sql .= $this->concatSection('where', ' WHERE ');
		$sql .= $this->concatSection('group', ' GROUP BY ', ', ');
		$sql .= $this->concatSection('order', ' ORDER BY ', ', ');
		
		if( !empty( $this->limit ) ) {
			$sql .= " LIMIT $this->limit ";
		}
		
		if( !empty( $this->offset ) ) {
			$sql .= " OFFSET $this->offset ";
		}
		
		$sql = trim($sql);
		
		if(!$this->sqlMode) {
			$result = $this->query($sql);
			return new resultObject($this->table, $result);
		} else {
			return $sql;
		}
		
	}
	
	public function delete($ID = false) {
		
	}
	
	private function recursiveSave($field, $value) {
		
		//check the relationship type
		$relationship = $this->findRelationship($this->table, $field);
		
		switch( $relationship ) {
			
			case 'has_one':
				
				$cur = $value->as_array();
				$cur = $cur[0];
			
				if( !isset($cur->ID) ) {
					//add it
					$orm = ORM::factory($field);
					//TODO:: account for non-result objects
					$orm->add($cur);
					$cur->ID = $this->lastInsertID();
				} else {
					//TODO:: call an update here, has ones might change with the object
				}
								
				return array(
						'fieldName' => "$this->table.{$field}_ID", 
						'fieldValue' => $cur->ID
					);
				
			break;
			
			case 'has_many':
				
				//delete and add?
				
			break;
			
			case 'has_and_belongs_to_many':
			
				//delete all
				//add them in
			
			break;

		}
		
		return '';
		
	}
	
	public function update($object) {
		
		$sql = "UPDATE $this->table SET ";
		
		foreach($object as $field => $value) {
			if(!is_object($value) && !is_array($value)) {
				$sql .= "$this->table.$field = '$value', ";
			} else {
				
				$parts = $this->recursiveSave($field, $value);
				if( !empty( $parts ) ) {
					$sql .= "$parts[fieldName] = '$parts[fieldValue]', ";
				}
				
			}
		}
		$sql = substr($sql, 0, -2);
		$sql .= " WHERE $this->table.ID = '$object->ID'";
		
		if(!$this->sqlMode) {
			//TODO: perform query
		} else {
			return $sql;
		}
		
	}
	
	public function add($objectsToAdd) {
		
		if( !is_array($objectsToAdd) ) {
			
			$objectsToAdd = array($objectsToAdd);
			
		}
		
		$firstObject = $objectsToAdd[0];
		
		$fields = array_keys( get_object_vars( $firstObject ) );
		$fieldsString = '';
		
		foreach($fields as $field) {
			if( is_object( $firstObject->$field ) || is_array( $firstObject->$field ) ) {
				
				$type = $this->findRelationship($this->table, $field);
				if($type == 'has_one') {
					$fieldsString .= "`{$field}_ID`, ";
				}
				
			} else {
				$fieldsString .= "`$field`, ";
			}
		}
		
		$fieldsString = substr($fieldsString, 0, -2);
		
		$sql = "INSERT INTO $this->table ($fieldsString) VALUES ";
		foreach($objectsToAdd as $object) {
			$sql .= "( ";
			foreach($fields as $curField) {
				
				$value = $object->$curField;
				
				if( !is_object($value) && !is_array($value) ) {
					$sql .= "'$value', ";
				} else {

					$parts = $this->recursiveSave($curField, $value);

					if( !empty( $parts ) ) {					
						$sql .= "'$parts[fieldValue]', ";
					}
					
				}
			}
			$sql = substr($sql, 0, -2) . ' ), ';
		}
		$sql = substr($sql, 0, -2);
		
		if(!$this->sqlMode) {
			//TODO: perform query
		} else {
			return $sql;
		}
		
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

			if($key = $this->findRelationship($curTable, $rel)) {
			
				$this->$key($rel, $curTable);
				$curBranch->$rel = (object) NULL;
				
			}
			
			if( $recurse ) {
				$this->internalWith($subRel, $rel, $curBranch->$rel);
				$recurse = false;
			}
		}
		
	}
	
	private function findRelationship($curTable, $relatedTable) {

		$relationships = config::get('schema.'.$curTable);
	 	return $this->recursive_array_search($relatedTable, $relationships);

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

		$this->leftJoin($tableToJoin, "$tableToJoin.{$origTable}_ID = $origTable.ID");		

	}
	
	private function has_many($tableToJoin, $origTable) {

		$this->leftJoin($tableToJoin, "$origTable.ID = $tableToJoin.{$origTable}_ID");

	}
	
	private function has_and_belongs_to_many($tableToJoin, $origTable) {
		
		if($tableToJoin > $origTable) {
			$junctionName = $origTable.'_'.$tableToJoin;
		} else {
			$junctionName = $tableToJoin.'_'.$origTable;
		}
		
		$this->join('LEFT', $junctionName, "$origTable.ID = $junctionName.{$origTable}_ID", false);
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
		} else {
			
			if(!$this->secondaryKey) {
				$this->secondaryKey = config::get("schema.$this->table.secondaryKey");
			}
		
			if( $this->secondaryKey && is_string( $key ) ) {
				$this->andWhere($this->table . '.' . $this->secondaryKey . ' = "?"', $key);
			}
			
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
				$valString = substr($valString, 0, -2);
				
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
	
	public function group($group) {
		$this->group[] = $group;
		
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
	
	public function join($type, $table, $on, $addValues = true) {
		
		if( $addValues ) {
			$this->defaultValues[] = $table.'.*';
		}
		
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
	
	public function lastInsertID() {
		return mysql_insert_id();
	}
	
	public function calcFound() {
		$this->selectModifiers[] = 'SQL_CALC_FOUND_ROWS ';
		return $this;
	}
	
	public function distinct() {
		$this->selectModifiers[] = 'DISTINCT ';
		return $this;
	}
	
	public function sqlMode($onOff) {
		$this->sqlMode = $onOff;
		return $this;
	}
	
	public function query($sql) {
		
		$querynum = profiler::logQuery($sql); //benchmark the query
		$result = mysql_query($sql); //execute the sql
		profiler::endQuery($sql); //end benchmark

		$ob = array();
		
		if (!$result) {
			trigger_error('mysql: ['.mysql_errno().'] '.mysql_error().(config::get('profiler.display') ? ' <a href="#queries'.($querynum + 1).'">(see profiler query '.($querynum + 1).')</a>' : ''), E_USER_WARNING);
			profiler::failedQuery('ERROR '.mysql_errno().': '.mysql_error());
		} else {
			
			if( is_resource($result) ) {
				//TODO: make this build objects recursively.
				while($row = mysql_fetch_array($result)) {
					$ob[] = $row;
				}
			}
				
				$this->numRows = mysql_num_rows($result);
					
		}
		
		
		return $ob;
	}
	
	public function buildObject($array) {
		
		//given a 0 indexed array of results
		
		
	}
	
	public function connect() {
		
		$group = config::get('database.config.group');
		$dbconfig = config::get('database.config.' . $group);
		$link = mysql_connect($dbconfig['hostname'],$dbconfig['username'],$dbconfig['password']);
		mysql_select_db($dbconfig['database'], $link);

		return $link;
		
	}
	
}

class resultObject implements ArrayAccess, Countable, Iterator {
	
	private $__tablename;
	private $__results;
	private $__position = 0;
	private $__changed = false;
	
	function __construct($tablename, $results = array()) {
		$this->__tablename = $tablename;
		$this->__results = $results;
	}
	
	public function __set($name, $value) {
		$cur =& $this->current();
		
		if( !isset($cur->$name) || $cur->$name != $value) {
			$this->__changed = true;
		}
		
		$cur->$name = $value;
		return true;
	}
	
	public function __get($name) {
		
		$cur = $this->current();
		
		if( isset($cur->$name) ) {
			return $cur->$name;
		}
		
		return false;
	}
	
	public function as_array() {
		return $this->__results;
	}
	
	public function save() {
		if( !isset($this->current()->ID) ) {
			ORM::factory($this->__tablename)->add($this->__results);
		} else {
			foreach($this->__results as $res) {
				ORM::factory($this->__tablename)->update($res);
			}
		}
	}
	
	public function isEmpty() {
		return empty($this->__results);
	}
	
	public function rewind() {
        $this->__position = 0;
    }

    public function current() {
        return $this->__results[$this->key()];
    }

    public function key() {
        return $this->__position;
    }

    public function next() {
        ++$this->__position;
    }

    public function valid() {
        return isset($this->__results[$this->__position]);
    }
	
	public function count() {
		return count($this->__results);
	}
	
	public function offsetSet($offset, $value) {
		$this->__changed = true;
        $this->__results[$offset] = $value;
    }

    public function offsetExists($offset) {
        return isset($this->__results[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->__results[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->__results[$offset]) ? $this->__results[$offset] : null;
    }
	
}
