<?php
/**
 $Id: class_mysql.php 517 2007-10-25 02:01:15Z legend $
*/

class MySql extends DB
{
	function mysql($params)
	{
		parent::db($params);
	}

	function db_connect()
	{
		return @mysql_connect($this->host, $this->username, $this->password, true);
	}

	function db_pconnect()
	{        
		return @mysql_pconnect($this->host, $this->username, $this->password);
	}

	function db_select()
	{
		return @mysql_select_db($this->db, $this->conn_id);
	}

	function _execute($sql)
	{
		//$sql = $this->_prep_query($sql);
		return @mysql_query($sql, $this->conn_id);
	}

	function num_rows()
	{
		return @mysql_num_rows($this->result_id);
	}

	function _fetch_assoc()
	{
		return mysql_fetch_assoc($this->result_id);
	}
	
	function _fetch_object()
	{
		return mysql_fetch_object($this->result_id);
	}

    function _found_rows()
    {
        $rs = $this->query("SELECT FOUND_ROWS() as `found_rows`");

        if ($rs)
        {
			$row = $this->_fetch_object();

			return $row->found_rows;
        }

		return 0;
    }

	function escape_str($str)	
	{
		if (function_exists('mysql_real_escape_string'))
		{
			return mysql_real_escape_string($str, $this->conn_id);
		}
		elseif (function_exists('mysql_escape_string'))
		{
			return mysql_escape_string($str);
		}
		else
		{
			return addslashes($str);
		}
	}

	function affected_rows()
	{
		return @mysql_affected_rows($this->conn_id);
	}

	function insert_id()
	{
		return @mysql_insert_id($this->conn_id);
	}

	function _insert($table, $keys, $values)
	{	
		return "INSERT INTO " . $table ." (".implode(', ', $keys).") VALUES (".implode(', ', $values).")";
	}
	
	function _replace($table, $keys, $values)
	{	
		return "REPLACE INTO " . $table ." (".implode(', ', $keys).") VALUES (".implode(', ', $values).")";
	}

	function _update($table, $values, $where)
	{
		foreach($values as $key => $val)
		{
			$valstr[] = $key." = ".$val;
		}
	
		return "UPDATE ". $table ." SET ".implode(', ', $valstr)." WHERE ". $where;
	}
	

	function _delete($table, $where)
	{
		return "DELETE FROM ". $table . " WHERE " . $where;
	}

	function _error_message()
	{        
		$error = $this->conn_id ? @mysql_error($this->conn_id) : @mysql_error();

        return $error;
	}
	

	function _error_number()
	{
        $no = $this->conn_id ? @mysql_errno($this->conn_id) : @mysql_errno();

		return $no;
	}

	function _close($conn_id)
	{
		@mysql_close($conn_id);
	}
}

?>