<?php
/**
 $Id: class_db.php 517 2007-10-25 02:01:15Z legend $
*/

class DB
{
	var $username;
	var $password;
	var $db;
	var $host;
	var $port = '';

    var $pconnect = false;

	var $conn_id = false;
	var $result_id = false;

	var $db_debug = false;
	var $benchmark = 0;
	var $query_count = 0;

	var $queries = array();

	function db($params)
	{
		$this->init($params);

		// connect
		$this->conn_id = ($this->pconnect == FALSE) ? $this->db_connect() : $this->db_pconnect();
        
		if (!$this->conn_id)
		{
            $this->fatal_error();
		}

		if (!$this->db_select())
		{
			$this->fatal_error();
		}
	}
    
	// initialize
	function init($params)
	{
		if (is_array($params))
		{
			$items = array (
				'host' => '',
				'port' => '',
				'db' => '',
				'username' => '',
				'password' => '',
				'pconnect' => false
			);

			foreach ($items as $item => $value)
			{
				$this->$item = isset($params[$item]) ? $params[$item] : $value;
			}
		}        
	}

	function query($sql)
	{
		$this->queries[] = $sql;
		$this->query_count ++;

		$time_start = list($sm, $ss) = explode(' ', microtime());

		if (false === ($this->result_id = $this->_execute($sql)))
		{
			$this->fatal_error();
		
		    return false;
		}

		$time_end = list($em, $es) = explode(' ', microtime());
		$this->benchmark += ($em + $es) - ($sm + $ss);
        
        $this->queries[$this->query_count - 1] = $sql . " | " . (($em + $es) - ($sm + $ss));

		return true;
		
	}
    
	function fetch_row()
	{
		return $this->_fetch_assoc();
	}
    
	function get_row($sql)
	{
		$this->query($sql);
		return $this->_fetch_assoc();
	}
    
	function get_field($sql)
	{
		$row = $this->get_row($sql);
		if ($row)
		{
			return array_shift($row);
		}
		
		return '';
	}

	function get_all($sql)
	{
		$this->query($sql);
		
		$rows = array();
		while ($row = $this->_fetch_assoc())
		{
			$rows[] = $row;
		}

		return $rows;
	}

    function found_rows()
	{
		return $this->_found_rows();
	}

    function build_limit($page, $perpage)
	{
		$page = $page <= 0 ? 1 : $page;

		return " LIMIT " . ($page - 1) * $perpage . ", $perpage";
	}

	function is_write($sql)
	{
		if (!preg_match('/^\s*"?(INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|LOAD DATA|COPY|ALTER|GRANT|REVOKE|LOCK|UNLOCK)\s+/i', $sql))
		{
			return false;
		}

		return true;
	}
    
	// elapsed time
	function elapsed_time($decimals = 6)
	{
		return number_format($this->benchmark, $decimals);
	}

	// get total queries
	function total_queries()
	{
		return $this->query_count;
	}
    
	// return last sql
	function last_query()
	{
		return end($this->queries);
	}
	
	// escape string
	function escape($str)
	{
		switch (gettype($str))
		{
			case 'string'	:	$str = "'".$this->escape_str($str)."'";
				break;
			case 'boolean'	:	$str = ($str === FALSE) ? 0 : 1;
				break;
			default			:	$str = ($str === NULL) ? 'NULL' : $str;
				break;
		}		

		return $str;
	}
    
	// insert
	function insert($table, $data)
	{
		$fields = array();	
		$values = array();
		
		foreach($data as $key => $val)
		{
			$fields[] = $key;
			$values[] = $this->escape($val);
		}
        
		$sql = $this->_insert($table, $fields, $values);
		return $this->query($sql);
	}
	
	// replace
	function replace($table, $data)
	{
		$fields = array();	
		$values = array();
		
		foreach($data as $key => $val)
		{
			$fields[] = $key;
			$values[] = $this->escape($val);
		}
        
		$sql = $this->_replace($table, $fields, $values);

		return $this->query($sql);
	}

    // update
	function update($table, $data, $where)
	{
		if ($where == '')
		{
			return false;
		}
		
		$fields = array();
		foreach($data as $key => $val)
		{
			$fields[$key] = $this->escape($val);
		}

		$sql = $this->_update($table, $fields, $where);
		return $this->query($sql);
	}
    
	function fatal_error()
	{
        if (!$this->conn_id)
        {
            echo "<p><div style='color: #c00; font-family: courier new'>DB Connection Error. " . $this->_error_number() . ": " . $this->_error_message() . "</div>";
        }
        else
        {
            echo "<p><div style='color: #c00; font-family: courier new'>" . $this->last_query() . "</div>";
            echo "<div style='font-family: courier new'>" . $this->_error_number() . ": " . $this->_error_message() . "</div></p>";
        }

		exit;
	}

	function close()
	{
		if (is_resource($this->conn_id))
		{
			$this->_close($this->conn_id);
		}

		$this->conn_id = false;
	}
}
?>