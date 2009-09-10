<?php 

class ORM {
	
	static $link = false;
	
    private $_sql = "";

	private $_from = array();
	private $_order = array();
	private $_where = array();
	private $_joins = array();
    private $_group = array();
    private $_modifiers = array();
	private $_limit = 0;
	private $_offset = 0;
	
	private $_select = array();
    private $_selectOrder = array();

    private $_relationships = array();
    private $_objectRoots = array();
	
	private $numRows = 0;
	private $numFound = 0;
	
	
	public function __construct() {
		
		if( !self::$link ) {
			self::$link = $this->connect();
		}
		
	}

    public function init() {
        //loads in all the helper classes
    }

    public function select() {

        $args = func_get_args();
        
        if( !isset( $this->_selectOrder ) ) {
            $cur = new ORM();
            return call_user_func_array(array($cur, 'select'), $args);
        }

        $this->_queryType = QueryTypes::Select;
        
        foreach($args as $selection) {
            $parts = explode(":", $selection);
            $path = $parts[0];
            $val = $parts[1];

            $pathParts = explode(".", $path);
            $valParts = explode(",", $val);

            if (count($pathParts) > 1) {
                
                if ( count( $this->_from) == 0 ) {
                    $this->fromTable($pathParts[0]);
                }

                if ( ! in_array( $pathParts[0], $this->_selectOrder ) ) {
                    $this->_selectOrder[] = $pathParts[0];
                }

                $curTable = $this->joinPath( array_shift($pathParts) , $pathParts);

            } else {
                
                if ( ! in_array( $pathParts[0], $this->_selectOrder ) ) {
                    array_unshift( $this->_selectOrder, $pathParts[0]);
                }

                $this->fromTable($pathParts[0]);
                $curTable = $pathParts[0];
            }

            if ( ! isset( $this->_select[$curTable] ) ) {

                if ( $key = array_search( "id", $valParts) ) {
                   unset( $valParts[$key] ); 
                }

                array_unshift( $valParts, "id");
                $this->_select[$curTable] = $valParts;

            } else {

                array_merge( $this->_select[$curTable], $valParts );

            }

        }

        return $this;

    }

    private function joinPath($parent, $path) {

        if ( count( $path ) == 1 ) {
            $this->_objectRoots[$path[0]] = $parent;
        }

        if ( count( $path ) > 0 ) {

            if ( ! in_array( $path[0], $this->_selectOrder ) ) {
                $parentIndex = array_search( $parent, $this->_selectOrder) + 1;
                array_splice( $this->_selectOrder, $parentIndex, 0, $path[0] ); 
            }

            $newParent = array_shift( $path );
            $this->relJoin( $parent, $newParent );
            return $this->joinPath( $newParent, $path );

        } else {

            if ( ! in_array( $parent, $this->_selectOrder ) ) {
                $this->_selectOrder[] = $parent;
            }
            return $parent;
        }

    }

	private function fromTable($table) {
        $this->_from[] = $table;
	}

    private function getRel($parent, $child) {
        
        if ( $rel = config::get("schema.$parent.$child") )
            return $rel;

        //throw if not found
    }

    private function relJoin($parent, $child) {
        
        if ( isset( $this->_joins[$parent.$child] ) ) 
            return;

        $rel = $this->getRel($parent, $child);

        if ( $rel == RelTypes::HasMany ) 
            $this->HasMany($parent, $child);

        else if ( $rel == RelTypes::HasOne )
            $this->HasOne($parent, $child);

        else if ( $rel == RelTypes::RefsOne )
            $this->RefsOne($parent, $child);

        else if ( $rel == RelTypes::RefsMany )
            $this->RefsMany($parent, $child);

    }

	public function join($type, $parent, $child, $onClause) {
		
	    $this->_joins[$parent.$child] = "$type JOIN $child ON $onClause";
		return $this;

	}
	
	private function HasOne($parent, $child) {

        $onClause = "$child.{$parent}_id = $parent.id";
        $this->join(JoinTypes::Left, $parent, $child, $onClause);

	}
	
	private function RefsOne($parent, $child) {

        $onClause = "$parent.{$child}_id = $child.id";
        $this->join(JoinTypes::Left, $parent, $child, $onClause);

	}
	
	private function HasMany($parent, $child) {

        $onClause = "$parent.id = $child.{$parent}_id";
        $this->join(JoinTypes::Left, $parent, $child, $onClause);

	}
	
	private function RefsMany($parent, $child) {
		
		if($parent > $child) {
			$junctionTable = $child.'_'.$parent;
		} else {
			$junctionTable = $parent.'_'.$child;
		}
		
        //join junction table
        $onClause = "$parent.id = $junctionTable.{$parent}_id";
        $this->join(JoinTypes::Left, $parent, $junctionTable, $onClause);

        //join the child table
        $onClause = "$child.id = $junctionTable.{$child}_id";
        $this->join(JoinTypes::Left, $parent, $child, $onClause);
		
	}
	
	public function where() {

        $this->privWhere(func_get_args(), "AND");
		return $this;

	}

    private function privWhere($args, $type) {

        $where = array_shift($args);

        $clause = $this->escapeClause($where, $args);

        if( count( $this->_where ) > 0 ) {
            $clause = " $type $clause";  
        }

        $this->_where[] = $clause;
    }
	
	public function andWhere() {
		
        $this->privWhere(func_get_args(), "AND");
        return $this;

	}
	
	public function orWhere($where, $value) {
		
        $this->privWhere(func_get_args(), "OR");
        return $this;

	}
	
	public function order() {
        $args = func_get_args();
		$this->_order = array_merge($this->_order, $args);
		return $this;
	}
	
	public function group() {
        $args = func_get_args();
		$this->_group = array_merge($this->_group, $args);
		return $this;
	}
	
	public function limit($limit) {
		$this->_limit = $limit;
		return $this;
	}
	
	public function offset($offset) {
		$this->_offset = $offset;
		return $this;
	}

    private function wrapKeys($val) {
        return '{'.$val.'}';
    }
	
    private function escapeClause($clause, $values) {

        $values = array_map(array($this, 'escape'), $values); 
        $wrappedKeys = array_map(array($this, 'wrapKeys'), array_keys($values));

        $clause = str_replace($wrappedKeys, $values, $clause);

        return $clause;
    }
	
	public function escape($text) {
		return mysql_real_escape_string($text);
	}
	
	public function lastInsertID() {
		return mysql_insert_id();
	}
	
	public function calcFound() {
		$this->_modifiers[] = 'SQL_CALC_FOUND_ROWS ';
		return $this;
	}
	
	public function distinct() {
		$this->_modifiers[] = 'DISTINCT ';
		return $this;
	}

    public function page($pageNum) {
        $this->offset($this->_limit * $pageNum);
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
	
	public function buildObject($rows) {
		
		//given a 0 indexed array of results
        $result = array();
        $rootName = $this->_selectOrder[0];

        $prevIds = array();
        $prevIds[$rootName] = 0;
        $prevObjects = array();

        $rowCounter = 0;
        $numRows = count($rows);

        
		
        while( $rowCounter < $numRows ) {

            $fieldCounter = 0;
            $curRow = $rows[$rowCounter];

            if( $prevIds[$rootName] != $curRow[$fieldCounter] ) {

                $rootObject = (object) null;
                $result[] = $rootObject;
                $prevIds = array();
                $prevIds[$rootName] = $curRow[0];
                $prevObjects[$rootName] = $rootObject;

            }

            $curObjectName = $rootName;

            foreach($this->_selectOrder as $table) {

                if( $table == $rootName ) {
                    $curObject = $rootObject;
                } else {
                    if( $this->_objectRoots[$table] != $curObjectName ) {
                        
                        
                        $curObject = $prevObjects[$this->_objectRoots[$table]];
                        $curObjectName = $this->_objectRoots[$table];
                    }

                    $rel = $this->getRel($curObjectName, $table);
                    

                    if( $rel == RelTypes::HasMany || $rel == RelTypes::RefsMany ) {
                        
                        if( !isset( $curObject->$table ) ) {
                            $curObject->$table = array();
                            $prevIds[$table] = 0;
                        }

                        if( $prevIds[$table] != $curRow[$fieldCounter] ) {
                            $newArray =& $curObject->$table;
                            $newArray[] = (object) null;
                            $curObject = end($newArray);
                            
                        } else {
                            $curObject = end($curObject->$table);
                        }

                    } else if( !isset( $curObject->$table ) ) {
                        
                        
                            $curObject->$table = (object) null;
                            $curObject = $curObject->$table;
                        
                    } else {
                        $curObject = $curObject->$table;
                    }
                }

                $curObjectName = $table;
                $prevIds[$curObjectName] = $curRow[$fieldCounter];

                foreach( $this->_select[$table] as $field ) {
                    $curObject->$field = $curRow[$fieldCounter];
                    $fieldCounter++;
                }

                $prevObjects[$curObjectName] = $curObject;
                
                
            }

            $rowCounter++;
        }

        return $result;
		
	}
	
	public function connect() {
		
		$group = config::get('database.config.group');
		$dbconfig = config::get('database.config.' . $group);
		$link = mysql_connect($dbconfig['hostname'],$dbconfig['username'],$dbconfig['password']);
		mysql_select_db($dbconfig['database'], $link);

		return $link;
		
	}
	
	public function fetch($ID = false) {
		
		$this->numFound = 0;

		$this->limitByKey($ID);
		
        $this->query($this->_sql);
	}

    public function delete($table, $id = null) {
        
        $returnObj = new ORM();

        $returnObj->_queryType = QueryTypes::Delete;
        $returnObj->_sql = "DELETE FROM $table";

        if( $id != null ) {
            $returnObj->where("$table.id = '$id'");
        }

        return $returnObj;

    }
	
	
	public function update($table, $object) {

        $returnObj = new ORM();

        $returnObj->_queryType = QueryTypes::Update;
		
		$sql = "UPDATE $table SET ";
		
		foreach($object as $field => $value) {

			if(!is_object($value) && !is_array($value)) {
				$sql .= "$table.$field = '$value', ";
			} else if( is_object($value) ) {
                
                $rel = $returnObj->getRel($table, $field);
                if( isset($value->id) && $rel == RelTypes::HasOne) {
                    $sql .= "$table.{$field}_id = '$value->id', ";
                }

            }

		}
		$returnObj->_sql = substr($sql, 0, -2);
		
        if( isset( $object->id ) ) {
            $returnObj->where("$table.id = '$object->id'");
        }

        return $returnObj;
		
	}
	
	public function add($table, $objectsToAdd) {

        $returnObj = new ORM();
		
		if( !is_array($objectsToAdd) ) {
			
			$objectsToAdd = array($objectsToAdd);
			
		}
		
		$firstObject = $objectsToAdd[0];
		
		$fields = array_keys( get_object_vars( $firstObject ) );
		$fieldsString = '';
		
		foreach($fields as $field) {
			if( is_object( $firstObject->$field ) ) {
                $fieldsString .= "`{$field}_id`, ";
			} else {
				$fieldsString .= "`$field`, ";
			}
		}
		
		$fieldsString = substr($fieldsString, 0, -2);
		
		$sql = "INSERT INTO `$table` ($fieldsString) VALUES ";
		foreach($objectsToAdd as $object) {
			$sql .= "( ";
			foreach($fields as $curField) {
				
				$value = $object->$curField;
				
                $rel = $returnObj->getRel($table, $field);

				if( !is_object($value) && !is_array($value) ) {
					$sql .= "'$value', ";
				} else if ( is_object($value) && $rel == RelTypes::HasOne ) {

                    if( isset( $value->id ) ) {
                        $sql .= "'$value->id', ";
                    } else {
                        $sql .= "'0', ";
                    }
					
				}
			}
			$sql = substr($sql, 0, -2) . ' ), ';
		}
		$sql = substr($sql, 0, -2);

        $returnObj->_sql = $sql;
        $returnObj->_queryType = QueryTypes::Insert;
        return $returnObj;
		
	}

	public function getNumFound() {
		
		if( !$this->numFound ) {
			$result = $this->query('SELECT FOUND_ROWS() as count');
			$this->numFound = $result[0]->count;
		}
		return $this->numFound; 
		
	}

    private function prepareSelectSQL() {
        $sql = "SELECT ";
		
		$sql .= $this->concatSection($this->_modifiers, ' ', ' ');
		
		if(!empty($this->_select)) {
            foreach( $this->_select as $table => $values ) {
                $sql .= $this->concatSection($values, '', ', ', "$table.").", ";
            }
            $sql = rtrim($sql, ", "); 
		}
		
		$sql .= $this->concatSection($this->_from, ' FROM ', ', ')." ";
		
		if( !empty( $this->_joins ) ) {
			foreach($this->_joins as $join) {
				$sql .= $join." ";
			}
		}
        $sql = trim($sql);
		
		$sql .= $this->concatSection($this->_where, ' WHERE ');
		$sql .= $this->concatSection($this->_group, ' GROUP BY ', ', ');
		$sql .= $this->concatSection($this->_order, ' ORDER BY ', ', ');
		
		if( !empty( $this->_limit ) ) {
			$sql .= " LIMIT $this->_limit";
		}
		
		if( !empty( $this->_offset ) ) {
			$sql .= " OFFSET $this->_offset ";
		}
		
		$this->_sql = trim($sql);
    }

    //TODO : Implement
    private function prepareDeleteSQL() {

		$this->_sql .= $this->concatSection($this->_where, ' WHERE ');
		
		if( !empty( $this->_limit ) ) {
			$this->_sql .= " LIMIT $this->_limit";
		}

    }

    //TODO : Implement
    private function prepareInsertSQL() {

        $this->_sql;

    }

    //TODO : Implement
    private function prepareUpdateSQL() {

		$this->_sql .= $this->concatSection($this->_where, ' WHERE ');
		
		if( !empty( $this->_limit ) ) {
			$this->_sql .= " LIMIT $this->_limit";
		}
		
    }

    public function getSQL() {
		
        switch($this->_queryType) {

            case QueryTypes::Select:
                $this->prepareSelectSQL();
            break;

            case QueryTypes::Delete:
                $this->prepareDeleteSQL();
            break;

            case QueryTypes::Insert:
                $this->prepareInsertSQL();
            break;

            case QueryTypes::Update:
                $this->prepareUpdateSQL();
            break;
        }

        return $this->_sql;
    }
	
	private function concatSection($section, $sectionPrefix = '', $separator = '', $valuePrefix = '') {
		
		$result = '';
	    
		if( !empty( $section ) ) {
            $result = $sectionPrefix;
			foreach($section as $s) {
				$result .= $valuePrefix.$s.$separator;
			}
			
			$result = rtrim($result, $separator);
		}
		
		return $result;
	}
}

class RelTypes {
    
    const HasMany = "has_many";
    const HasOne = "has_one";
    const RefsOne = "belongs_to_many";
    const RefsMany = "has_and_belongs_to_many";

}

class JoinTypes {
    
    const Left = "LEFT";
    const Inner = "INNER";
    const Outer = "OUTER";
    const Right = "RIGHT";

}

class QueryTypes {

    const Select = "Select";
    const Delete = "Delete";
    const Insert = "Insert";
    const Update = "Update";

}
