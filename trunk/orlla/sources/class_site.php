<?php
/**
 * $Id$
*/

class Site
{
    /**
     * �������е�ȫ�����ñ���
     *
     * @var array
     */
	var $vars;
	
    /**
     * ��ǰ������Ĭ������$_GET['act']
     *
     * @var string
     */
	var $act;

    /**
     * ��ǰ����ģ�飬����url��ʶ��
     *
     * @var string
     */
	var $module = '';

    /**
     * ��ǰҳ��url
     *
     * @var string
     */
	var $url = '';

    /**
     * �ѷ������Ժ�ɶ���Ϊ��������·����url
     *
     * @var string
     */
	var $uri = ''; // encoded url

    /**
     * urlencode֮���url
     *
     * @var string
     */
    var $encoded_url = '';

    /**
     * �Ƿ��мƻ�������Ҫִ��
     *
     * @var boolean
     */
    var $cron = false;

    /**
     * ��ǰ�û�����Ϣ���ṹͬusers��
     *
     * @var array
     */
    var $user;

    /**
     * ��ǰ�û�ID�������û�Ϊ0 
     *
     * @var integer
     */
	var $userid = 0;

    /**
     * ��ǰ�û����û���������Ϊ��
     *
     * @var string
     */
	var $username = '';
    
    /**
     * ��ǰ�û��������
     */
    var $browser = array('name' => 'MSIE', 'version' => 6.0);

    /**
     * ��ǰ�û���ip��ַ
     *
     * @var string
     */
	var $ip;
    
    /**
     * ���ݿ���������
     *
     * @var object
     */
	var $db;

    /**
     * ģ����������
     *
     * @var object
     */
	var $tpl;

    /**
     * session���������(��ʱ����������)
     *
     * @var object
     */
	var $session;
	
    /**
     * ��ǰ�Ѿ����ֵĴ�����Ϣ����
     *
     * @var array
     */
	var $errors   = array();
    var $messages = array();
    
    /**
     * ����POST��GET���������ڴ������У�POST�Ḳ��GET�е�ͬ������
     *
     * @var array
     */
	var $input = array();
    
    /**
     * ���캯��
     *
     * @return void
     */
	function site($vars)
	{
		// config and settings
		$this->vars = $vars;

		$this->_clean_input();

		// ip address
        if (!isset($_SERVER['REMOTE_ADDR'])) {
            $_SERVER['REMOTE_ADDR']='';
        }
		$this->ip = $_SERVER['REMOTE_ADDR'];
        
		$this->act = isset($this->input['act']) ? $this->input['act'] : '';

        if (!isset($_SERVER['REQUEST_URI'])) {
            $_SERVER['REQUEST_URI']='';
        }
        $this->url = $_SERVER['REQUEST_URI'];
		$this->uri = urlencode($this->url);

        $this->encoded_url = urlencode($this->url);
        

        $this->detect_browser();

		//register_shutdown_function(array(&$this, '_site'));
	}
    
    /**
     * ��������
     *
     * @return void
     */
	function _site()
	{
		if ($this->db)
		{
			$this->db->close();
		}
	}
    
    function detect_browser()
    {
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        
        if (preg_match('/MSIE ([\d\.]+?)/', $agent, $m))
        {
            $this->browser['version'] = floatval($m[1]);
        }
        else if (preg_match('/Gecko/', $agent))
        {
            $this->browser['name'] = 'Gecko';
            if (preg_match('/Firefox\/([\d\.]+?)/', $agent, $m))
            {
                $this->browser['name'] = 'Firefox';
                $this->browser['version'] = floatval($m[1]);
            }
        }
        else if (preg_match('/Opera\/([\d\.]+?)/', $agent, $m))
        {
            $this->browser['name'] = 'Opera';
            $this->browser['version'] = floatval($m[1]);
        }
    }

	/**
     * ����һ������Ϣ��������ɱ䣬����һ������Ϊ������Ϣ��Ӧ��key���ڶ���Ϊ
     * ���󼶱�1Ϊ����������2Ϊ�������󣬴������һ��������������ʾ������Ϣ
     * ����ֹҳ��ִ�С�
     *
     * @return void
     */
	function error()
	{
		$args = func_get_args();

		$key   = array_shift($args);
		$level = array_shift($args);
        
        array_push($this->errors, array('key' => $key, 'level' => $level, 'args' => $args));

        if ($level > 1)
        {
            $this->fatal_error();
        }
	}

    /**
     * ����keyword, ��������Ϣ
     * 
     * @return void
     */
    function get_error_messages()
    {
        if (empty($this->errors))
        {
            return;
        }
        
        $error_keys = array();
        $messages   = array();

        //end 
        $last_error = end($this->errors);

        if ($last_error['level'] > 1)
        {
            $this->errors = array($last_error);
        }

        foreach ($this->errors as $error)
        {
            if ( !isset( $this->messages[ $error['key'] ] ) )
            {
                array_push($error_keys, $this->db->escape($error['key']));
            }
            else
            {
                $messages[ $error['key'] ] = $this->messages[ $error['key'] ];
            }
        }
        
        if (!empty($error_keys))
        {
            $this->db->query("SELECT * FROM error_messages WHERE Error IN (" . join(", ", $error_keys) . ")");
            while ($row = $this->db->fetch_row())
            {
                $messages[ $row['Error'] ] = $row['Message'];
            }
        }

        foreach ($this->errors as $k => $error)
        {
            $args = $error['args'];
            $format = isset($messages[ $error['key'] ]) ? $messages[ $error['key'] ] : $error['key'];
            array_unshift($args, $format);
            $msg = call_user_func_array("sprintf", $args);
            
            $this->errors[$k]['msg'] = $msg;
        }
    }

	/**
     * �жϵ�ǰҳ���Ƿ���ڴ���
     *
     * @return boolean
     */
    function has_error()
    {
        if (sizeof($this->errors))
        {
            return true;
        }

        return false;
    }

	/**
     * �жϵ�ǰҳ���Ƿ��޴�
     *
     * @return boolean
     */
    function no_error()
    {
        if (sizeof($this->errors))
        {
            return false;
        }

        return true;
    }
    
	/**
     * �ڲ���������������Ĵ�����
     *
     * @return void
     */
    function fatal_error()
    {
        if (!$this->errors)
        {
            array_push($this->errors, array('key' => '', 'level' => 2, 'msg' => ''));
        }
        
        foreach ($this->errors as $error)
        {
            if ($error['level'] > 1)
            {
                $this->tpl->set_title("Error");
                $this->tpl->import_style('site.css');
                $this->tpl->display($this->vars['template']['errors_stand_alone']);
                exit();
            }
        }
    }
    
	/**
     * ��鵱ǰ�û��Ƿ����ĳһ�������������ȿɱ䡣��ǰ�ɼ�����ĿΪ��
     * referer: ����û���һҳ�Ƿ�Ϊ�����ڵ�ǰ��
     *
     * @return void
     */
    function check_user()
    {
        $args = func_get_args();
        
        if (in_array('referer', $args))
        {
            $legal = false;
            if (isset($_SERVER['HTTP_REFERER']))
            {
                $url_info = parse_url($_SERVER['HTTP_REFERER']);
                if (strtolower($url_info['host']) == strtolower($_SERVER['SERVER_NAME']))
                {
                    $legal = true;
                }
            }

            if (!$legal)
            {
                $this->error('invalid_referer', 2);
            }
        }
    }
    

	/**
     * ҳ���ض���
     *
     * @param string $str ����ת��ʱ��ʾ����Ϣ
     * @param string $url ��ת�򵽵ĵ�ַ
     *
     * @return void
     */
	function flash($url, $msg = '')
	{
        header("Refresh: 1; url=$url");

        $this->tpl->set_title("Info");
        $this->tpl->import_style('site.css');
        $this->tpl->set("msg", $msg);
        $this->tpl->set("url", $url);
        $this->tpl->display($this->vars['template']['flash']);
        exit();
	}
   

	/**
     * �����û����룬�����$site->input����
     * 
     * @return void
     */
    function _clean_input()
	{
		@set_magic_quotes_runtime(0);
		
		$this->_clean_globals($_REQUEST);
		$this->_clean_globals($_GET);
		$this->_clean_globals($_POST);
		$this->_clean_globals($_COOKIE);
		
		$input = $this->_clean_recursively($_GET, array());
		$input = $this->_clean_recursively($_POST, $input);
		
		$this->input = $input;
		
		unset($input);

        if (!isset($_SERVER['REQUEST_METHOD'])) {
            $_SERVER['REQUEST_METHOD']='';
        }
		
		$this->input['request_method'] = strtolower($_SERVER['REQUEST_METHOD']);
	}
    
	/**
     * �ݹ����������Ϸ�����(�ڲ�����)
     *
     * @param mixed &$data ����
     * @param array $input
     * @param integer $iteration ���ݹ����
     *
     * @return array
     */ 
	function _clean_recursively(&$data, $input=array(), $iteration = 0)
	{
		if($iteration >= 10 )
		{
			return $input;
		}
		
		if(count( $data ))
		{
			foreach($data as $k => $v)
			{
				if (is_array($v))
				{
					$input[$k] = $this->_clean_recursively($data[$k], array(), $iteration ++);
				}
				else
				{	
					$k = $this->_clean_key($k);
					$v = $this->_clean_value($v);
					
					$input[$k] = $v;
				}
			}
		}
		
		return $input;
	}

	/**
     * �ڲ�����
     */
	function _clean_globals(&$data, $iteration = 0)
	{
		if( $iteration >= 10 )
		{
			return $data;
		}
		
		if(count($data))
		{
			foreach($data as $k => $v)
			{
				if (is_array($v))
				{
					$this->_clean_globals($data[$k], $iteration ++);
				}
				else
				{	
					# Null byte characters
					$v = preg_replace('/\\\0/' , '', $v);
					$v = preg_replace('/\\x00/', '', $v);
					$v = str_replace('%00', '', $v);
					
					# File traversal
					$v = str_replace('../', '&#46;&#46;/', $v);
					
					$data[$k] = $v;
				}
			}
		}
	}

	/**
     * �ڲ�����
     */
    function _clean_key($key)
    {
    	if ($key == "")
    	{
    		return "";
    	}
    	
    	$key = htmlspecialchars(urldecode($key));
    	$key = str_replace( ".."           , ""  , $key );
    	$key = preg_replace( "/\_\_(.+?)\_\_/"  , ""  , $key );
    	$key = preg_replace( "/^([\w\.\-\_]+)$/", "$1", $key );
    	
    	return $key;
    }
   
	/**
     * �ڲ�����
     */
	function _clean_value($val)
	{
		return $this->_strip_slashes($val);
	}

	/**
     * �ڲ�����
     */
	function _strip_slashes($t)
	{
		if (get_magic_quotes_gpc())
		{
    		$t = stripslashes($t);
    		//$t = preg_replace( "/\\\(?!&amp;#|\?#)/", "&#092;", $t );
    	}
    	
    	return $t;
    }
    
	/**
     * ���˷Ƿ����벢ת��html�ַ�(�ɵݹ����)
     *
     * @param mixed $val �����������
     * @param boolean $nl2br �Ƿ񽫻��з��滻Ϊ<br />
     *
     * @return void
     */    
	function escape_html($val, $nl2br = false)
	{
    	if ( $val == "" )
    	{
    		return "";
    	}
        
		if (is_array($val))
		{
			return array_map(array(&$this, 'escape_html'), $val, $nl2br);
		}

    	$val = str_replace( "&#032;", " ", $this->_strip_slashes($val) );    	
    	
    	$val = str_replace( "&"				, "&amp;"         , $val );
    	$val = str_replace( "<!--"			, "&#60;&#33;--"  , $val );
    	$val = str_replace( "-->"			, "--&#62;"       , $val );
    	$val = preg_replace( "/<script/i"	, "&#60;script"   , $val );
    	$val = str_replace( ">"				, "&gt;"          , $val );
    	$val = str_replace( "<"				, "&lt;"          , $val );
    	$val = str_replace( '"'				, "&quot;"        , $val );
    	//$val = str_replace( "$"				, "&#036;"        , $val );
    	$val = str_replace( "\r"			, ""              , $val ); // Remove literal carriage returns
    	//$val = str_replace( "!"				, "&#33;"         , $val );
    	$val = str_replace( "'"				, "&#39;"         , $val ); // IMPORTANT: It helps to increase sql query safety.
    	
        if ($nl2br)
        {
    	    $val = str_replace( "\n"	    , "<br />"        , $val ); // Convert literal newlines
        }

     
		//$val = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $val );
		
		$val = preg_replace( "/&#(\d+?)([^\d;])/i", "&#\\1;\\2", $val );
    	
    	return $val;
	}
    
    function unescape_html($val)
    {
    	if ( $val == "" )
    	{
    		return "";
    	}
        
		if (is_array($val))
		{
			return array_map(array(&$this, 'unescape_html'), $val);
		}

    	//$val = str_replace( "&#032;", " ", $this->_strip_slashes($val) );
    	
    	$val = str_replace( "&#39;"			 , "'"             , $val ); // IMPORTANT: It helps to increase sql query safety.
        //$val = str_replace( "!"				, "&#33;"         , $val );       
        //$val = str_replace( "$"				, "&#036;"        , $val );
        $val = str_replace( '&quot;'		  , '"'             , $val );
        $val = str_replace( "&lt;"			  , "<"             , $val );
        $val = str_replace( "&gt;"			  , ">"             , $val );
        $val = preg_replace( "/&#60;script/i" , "<script"       , $val );
        $val = str_replace( "--&#62;"		  , "-->"           , $val );
        $val = str_replace( "&#60;&#33;--"	  , "<!--"          , $val );
        $val = str_replace( "&amp;"			  , "&"             , $val );

        return $val;
    }

    /**
     * 
     */
    function editor_to_db($text)
    {
        $text = $this->escape_html($text, true);

        return $text;
    }

    /**
     * 
     */
    function db_to_editor($text)
    {
        $text = str_replace("<br />", "\n", $text);
        $text = str_replace("<BR>", "\n", $text);
        $text = str_replace("&nbsp;", " ", $text);

        $text = $this->unescape_html($text);

        return $text;
    }

    /**
     * 
     */
    function db_to_display($text, $parse_bbcode = true, $parse_smiles = true)
    {
        if ($parse_bbcode)
        {
            $text = parse_bbcode($text);
        }

        if ($parse_smiles)
        {
            $text = parse_smiles($text, $this->vars['template']['static_path'] . "images/smiles/");
        }

        // replace [$nbsp]...
        $text = preg_replace('/\[\$(.+?)\]/', "&\\1;", $text);
        $text = str_replace('  ', '&nbsp;&nbsp;', $text);

        return $text;
    }
    
    /**
     *
     */
    function remove_bbcode($str)
    {
        $patterns = array(
#            '/\[(code|quote|url|email|img|file).*?\].*?[\/\\1]/is',
#            '/\[(b|i|u|color|font|size|center|sub|sup).*?\]/i',
#            '/\[\/(b|i|u|color|font|size|center|sub|sup)]/i',
            '/\[quote.*?\].*?\[\/quote\]/i',
            '/\[code.*?\].*?\[\/code\]/i',
            '/\[\/?(b|i|u|color.*?|font|size|center|sub|sup|url.*?|email.*?|file.*?|img.*?|iframe)\]/is',
            '/\[\$nbsp\]/i',
            '/(&nbsp;){2,}/i',
            '/(<br \/>\s*){2,}/i',
        );

        $replacements = array(
            '',
            '',
            '',
            '&nbsp;',
            '&nbsp;',
            '<br />'
        );

        $str = preg_replace($patterns, $replacements, $str);

        return $str;
    }

    function escape_title($text)
    {
        $text = trim($text);
        $text = $this->unescape_html($text);
        $text = $this->escape_html($text);
        $text = str_replace(array("\n", "\r"), '', $text);

        return $text;
    }

	function escape_key($key)
	{
		$key = trim($key);
		$key = str_replace(array("_", "%", "*"), '', $key);

		return $key;
	}



	/**
     * ��html�����е�<br />�滻Ϊ���з�
     *
     * @param string $t �����������
     *
     * @return string
     */
    function br2nl($t="")
    {
        $t = preg_replace("#(?:\n|\r)?<br />(?:\n|\r)?#", "\n", $t);
        $t = preg_replace("#(?:\n|\r)?<br>(?:\n|\r)?#"  , "\n", $t);
        
        return $t;
    }
    
	/**
     * ��ǰҳ������󷽷��Ƿ�ΪGET
     *
     * @return boolean
     */
    function is_get()
    {
		if ($_SERVER['REQUEST_METHOD'] == 'GET')
		{
			return true;
		}

		return false;
	}

	/**
     * ��ǰҳ������󷽷��Ƿ�Ϊ:POST��ͨ�������жϱ��Ƿ��ύ
     *
     * @return boolean
     */
    function is_post()
    {
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			return true;
		}

		return false;
	}

	/**
     * ����cookie
     *
     * @param string $name cookie����
     * @param mixed  $val  ֵ
     * @param integer $expire ���ʱ��
     *
     * @return boolean
     */
	function set_cookie($name, $val, $expire = 0)
	{
		setcookie($this->vars['cookie']['prefix'] . $name, $val, $expire ? time() + $expire : 0, '/', $this->vars['cookie']['domain']);
	}
    
	/**
     * ��ȡcookieֵ
     *
     * @param string $name cookie����
     * @return mixed
     */
	function get_cookie($name)
	{
		if (isset($_COOKIE[$this->vars['cookie']['prefix'] . $name]))
		{
			return $_COOKIE[$this->vars['cookie']['prefix'] . $name];
		}

		return false;
	}
    
	/**
     * ����ip��ȡ��ַλ����Ϣ�����س���id
     *
     * @param string $ip ip��ַ
     *
     * @return array
     */
	function get_city_by_ip($ip)
	{
		$ip = ip2long($this->ip);
		$city = $this->db->get_row("SELECT * FROM geo_ip WHERE Start <= $ip AND End >= $ip");

		return $city;
	}


	/**
     * ��������ַ���
     *
     * @param integer $length ����
     *
     * @return string
     */
	function rand_str($length = 8)
	{
		$rand  = "";
		$chars = array(
			"1","2","3","4","5","6","7","8","9","0",
			"a","A","b","B","c","C","d","D","e","E","f","F","g","G","h","H","i","I","j","J",
			"k","K","l","L","m","M","n","N","o","O","p","P","q","Q","r","R","s","S","t","T",
			"u","U","v","V","w","W","x","X","y","Y","z","Z");

		$count = count($chars) - 1;

		srand((double)microtime()*1000000);

		for($i = 0; $i < $length; $i++)
		{
			$rand .= $chars[rand(0, $count)];
		}

		return($rand);
	}

	/**
     * �жϸ����ַ�����ָ�����ȣ���mbstring����
     *
     * @param string $str �ַ���
     * @param integer $length ����
     *
     * @return string
     */
    function str_cut($str, $length = 0)
    {
        return mb_strcut($str, 0, $length, 'utf-8');
    }

	/**
     * �жϸ����ַ�����ָ�����ȣ�һ�����ֳ��ȼ�Ϊ2
     *
     * @param string $str �ַ���
     * @param integer $length ����
     *
     * @return string
     */
    function smart_cut($str, $len)
    {
        $len_utf8 = mb_strlen($str, 'utf-8');

        $cur = 0;
        $i = 0;
        $output = '';

        while ($i < $len_utf8)
        {
            $char = mb_substr($str, $i ++, 1, 'utf-8');

            if (ord($char) > 127)
            {
                $cur ++;
            }

            $cur ++;
            
            if ($cur > $len - 4)
            {
                $output .= ' ...';
                break;
            }

            $output .= $char;
        }

        return $output;
    }

	/**
     * ����utf-8������ַ������ȣ�һ�����ֳ��ȼ�Ϊ2
     *
     * @param string $str �ַ���
     *
     * @return integer
     */
    function smart_len($str)
    {
        $len_utf8 = mb_strlen($str, 'utf-8');
        
        $len = 0;
        $i = 0;

        while ($i < $len_utf8)
        {
            $char = mb_substr($str, $i ++, 1, 'utf-8');
            if (ord($char) > 127)
            {
                $len ++;
            }

            $len ++;
        }

        return $len;
    }

	/**
     * ��ʽ��ʱ��
     *
     * @param integer $time ʱ���
     *
     * @return string
     */
    function format_time($time)
    {
        return date("Y-m-d H:i:s", $time);
    }

	/**
     * ��ʽ������
     *
     * @param integer $time ʱ���
     *
     * @return string
     */
    function format_date($time)
    {
        return date("Y-m-d", $time);
    }


    /**
     * �ض���ҳ��
     *
     * @return void
     */
	function redirect($url)
	{
		header("Location: $url");
		exit();
	}

    /**
     * �ֶ������û�session
     *
     * @param integer $userid �û�id
     * @param string session����
     * @param mixed  ֵ
     *
     * @return boolean
     */
	function update_session($userid = 0, $key, $value)
	{
		$data = $this->db->get_field("SELECT Data FROM sessions WHERE UserID = " . intval($userid));
		if (!$data)
		{
			return false;
		}

        $vars = preg_split('/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\|/', $data, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE ); 
        for ($i = 0; isset($vars[$i]); $i ++)
		{ 
            $decoded[$vars[$i ++]] = unserialize($vars[$i]); 
        }

		if (isset($decoded[$key]))
		{
			$decoded[$key] = $value;
		}
		else
		{
			return false;
		}
        
		$encoded = '';
		foreach ($decoded as $k => $v)
		{
			$encoded .= $k . "|" .serialize($v);
		}

		$this->db->query("UPDATE sessions SET Data = " . $this->db->escape($encoded) . " WHERE UserID = " . intval($userid));

		return true;
	}

    /**
     * ��ʼ��phpmailer����
     *
     * @param string $user smtp�û���
     * @param string $pass smtp����
     * @param string $name ����������
     *
     * @return object
     */
	function init_mailer($user = '', $pass = '', $name = '')
	{
		if (!class_exists('PHPMailer'))
		{
			require_once $this->vars['site']['sources_path'] . "phpmailer/class_phpmailer.php";
		}
        
		$mailer = new phpmailer();

        $mailer->From     = $user ? $user : $this->vars['mail']['smtp_user'];
        $mailer->FromName = $name ? $name : $this->vars['mail']['sender_name'];
		$mailer->Host     = $this->vars['mail']['smtp_host'];
		$mailer->Mailer   = "smtp";
		$mailer->SMTPAuth = true;
		$mailer->Username = $user ? $user : $this->vars['mail']['smtp_user'];
		$mailer->Password = $pass ? $pass : $this->vars['mail']['smtp_pass'];
		$mailer->CharSet  = "UTF-8";
		$mailer->ContentType = 'text/html';
		$mailer->SetLanguage('en', $this->vars['site']['sources_path'] . 'phpmailer/language/');
		
		return $mailer;
	}

    /**
     * �����ʼ�
     *
     * @param object $mailer phpmailer����
     * @param string $to ������
     * @param string $subject ����
     * @param string $body ����
     *
     * @return boolean
     */
    function send_mail($mailer, $to, $subject, $body)
    {
        $mailer->AddAddress($to, $to);
        $mailer->Subject  = $subject;
        $mailer->Body  = $body;

        if (!$mailer->Send())
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    
    /**
     * ���ɷ�ҳ��
     *
     * @param integer $total �ܼ�¼��
     * @param integer $perpage ÿҳ��ʾ��¼��
     * @param integer $page ��ǰҳ��
     * @param string $url ����,���е�__page__����ҳ���滻
     *
     * @return string
     */
	function build_pagebar($total, $perpage, $page, $url)
	{
		$pages = ceil($total / $perpage);
		$page = $page <= 0 ? 1 : $page;        		
        
		$total = $total <= 0 ? 1 : $total;
        
        /*
		if (false === strpos($url, ".") && substr($url, -1) != '/')
		{
			$url .= '/';
		}
        */

		$html = '<div class="pages">';

        if ($pages <= 11)
        {
            $start = 1;
            $end   = $pages;
        }
        else if ($page > 6 && $page + 5 <= $pages)
        {
            $start = $page - 5;
            $end   = $page + 5;
        }
        else if ($page + 5 > $pages)
        {
            $start = $pages - 10;
            $end   = $pages;			
        }
        else if ($page <= 6)
        {
            $start = 1;
            $end   = 11;	
        }
        
		// 
        if ($page == 1)
        {
            $html .= "<span>&laquo; Prev</span>";   
        }
        else
        {
            $html .= "<a href=\"" . str_replace("__page__", $page - 1, $url) . "\">&laquo; Prev</a>";
        }

		if ($start > 1)
		{
			$html .= "<a href=\"" . str_replace("__page__", 1, $url) . "\">1</a>";
		}

		if ($start > 2)
		{
			$html .= "<a href=\"" . str_replace("__page__", 2, $url) . "\">2</a>";
		}

		if ($start > 3)
		{
			$html .= "<span>...</span>";
		}
		//

        for ($i = $start; $i <= $end; $i ++)
        {
            if ($page == $i)
            {
                $html .= "<a href=\"" . str_replace("__page__", $i, $url) . "\" class=\"current\">$i</a>";
            }
            else
            {
                $html .= "<a href=\"" . str_replace("__page__", $i, $url) . "\">$i</a>";
            }
        }
        
		if ($end < $pages - 1)
		{
			$html .= "<span>...</span>";
		}
        
        /*
		if ($end < $pages - 1)
		{
			$html .= "<a href=\"" . str_replace("__page__", $pages - 1, $url) . "\">" . ($pages - 1) . "</a>";
		}
        */

		if ($end < $pages)
		{
			$html .= "<a href=\"" . str_replace("__page__", $pages, $url) . "\">$pages</a>";
		}

        if ($page >= $pages)
        {
            $html .= "<span>Next &raquo;</span>";
        }
        else
        {
            $html .= "<a href=\"" . str_replace("__page__", $page + 1, $url) . "\">Next &raquo;</a>";
        }
		//
        
		$html .= "</div>";

		return $html;
	}
}
?>
