<?php
/**
 * $Id: class_session.php 815 2008-01-04 05:13:45Z legend $
 */

class Session
{
	var $db;
	var $site;
	var $data;

	function session(&$site)
	{
		$this->db = &$site->db;
		$this->site = &$site;

        session_module_name('user'); 
        ini_set("session.gc_maxlifetime", $this->site->vars['session']['lifetime']);
        ini_set("session.bug_compat_42", "Off");
        ini_set("session.bug_compat_warn", "Off"); 

        session_set_save_handler( 
            array(&$this, 'open'), 
            array(&$this, 'close'), 
            array(&$this, 'read'), 
            array(&$this, 'write'), 
            array(&$this, 'destroy'), 
            array(&$this, 'gc') 
        );
        
		register_shutdown_function("session_write_close");

		session_start();
	}

	function open()
	{
		return true;
	}

	function close()
	{
		$this->gc(ini_get("session.gc_maxlifetime"));

		return true;
	}

	function read($sid)
    {
		$data = $this->db->get_field("SELECT data FROM sessions WHERE SessionID = " . $this->db->escape($sid));
        if ($data)
		{
			return $data;
		}

		return '';
	}

	function write($sid, $data)
	{
		$session = array (
		    'SessionID' => $sid,
			'UserID' => $this->site->userid,
			'UserName' => $this->site->username,
			'IP' => $this->site->ip,
            'UserAgent' => empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'],
			'Data' => $data,
			'Module' => $this->site->module,
			'LastActivity' => time()
		);
        
        
		$this->db->replace('sessions', $session);

		return true;
	}

	function destroy($sid)
	{
		$this->db->query("DELETE FROM sessions WHERE SessionID = " . $this->db->escape($sid));		

		return true;
	}

	function gc($lifetime = 1200)
	{
		//$this->db->query("DELETE FROM sessions WHERE " . time() . " - LastActivity > " . intval($lifetime));
        //$this->db->query("OPTIMIZE TABLE sessions");

		return true;
	}
}
?>