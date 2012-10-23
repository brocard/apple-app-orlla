<?php
/**
 * $Id$
*/

class Site
{
    /**
     * 保存所有的全局配置变量
     *
     * @var array
     */
	var $vars;
	
    /**
     * 当前操作，默认来自$_GET['act']
     *
     * @var string
     */
	var $act;

    /**
     * 当前所处模块，根据url来识别
     *
     * @var string
     */
	var $module = '';

    /**
     * 当前页面url
     *
     * @var string
     */
	var $url = '';

    /**
     * 已废弃，以后可定义为包含完整路径的url
     *
     * @var string
     */
	var $uri = ''; // encoded url

    /**
     * urlencode之后的url
     *
     * @var string
     */
    var $encoded_url = '';

    /**
     * 是否有计划任务需要执行
     *
     * @var boolean
     */
    var $cron = false;

    /**
     * 当前用户的信息，结构同users表
     *
     * @var array
     */
    var $user;

    /**
     * 当前用户ID，匿名用户为0 
     *
     * @var integer
     */
	var $userid = 0;

    /**
     * 当前用户的用户名，匿名为空
     *
     * @var string
     */
	var $username = '';
    
    /**
     * 当前用户的浏览器
     */
    var $browser = array('name' => 'MSIE', 'version' => 6.0);

    /**
     * 当前用户的ip地址
     *
     * @var string
     */
	var $ip;
    
    /**
     * 数据库对象的引用
     *
     * @var object
     */
	var $db;

    /**
     * 模板对象的引用
     *
     * @var object
     */
	var $tpl;

    /**
     * session对象的引用(暂时无特殊作用)
     *
     * @var object
     */
	var $session;
	
    /**
     * 当前已经出现的错误信息数组
     *
     * @var array
     */
	var $errors   = array();
    var $messages = array();
    
    /**
     * 所有POST及GET变量都存在此数组中，POST会覆盖GET中的同名变量
     *
     * @var array
     */
	var $input = array();
    
    /**
     * 构造函数
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
     * 析构函数
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
     * 触发一错误信息，参数表可变，但第一个参数为错误信息对应的key，第二个为
     * 错误级别。1为非致命错误，2为致命错误，此类错误一旦触发会立即显示错误信息
     * 并终止页面执行。
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
     * 依据keyword, 填充错误信息
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
     * 判断当前页面是否存在错误
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
     * 判断当前页面是否无错
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
     * 内部方法，致命错误的处理函数
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
     * 检查当前用户是否符合某一条件，参数表长度可变。当前可检查的项目为：
     * referer: 检查用户上一页是否为来自于当前域
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
     * 页面重定向
     *
     * @param string $str 欲在转向时显示的信息
     * @param string $url 欲转向到的地址
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
     * 处理用户输入，并填充$site->input变量
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
     * 递归调用清除不合法输入(内部方法)
     *
     * @param mixed &$data 数据
     * @param array $input
     * @param integer $iteration 最大递归深度
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
     * 内部方法
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
     * 内部方法
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
     * 内部方法
     */
	function _clean_value($val)
	{
		return $this->_strip_slashes($val);
	}

	/**
     * 内部方法
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
     * 过滤非法输入并转化html字符(可递归调用)
     *
     * @param mixed $val 欲处理的数据
     * @param boolean $nl2br 是否将换行符替换为<br />
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
     * 将html代码中的<br />替换为换行符
     *
     * @param string $t 欲处理的数据
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
     * 当前页面的请求方法是否为GET
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
     * 当前页面的请求方法是否为:POST，通常用于判断表单是否提交
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
     * 设置cookie
     *
     * @param string $name cookie名称
     * @param mixed  $val  值
     * @param integer $expire 存活时间
     *
     * @return boolean
     */
	function set_cookie($name, $val, $expire = 0)
	{
		setcookie($this->vars['cookie']['prefix'] . $name, $val, $expire ? time() + $expire : 0, '/', $this->vars['cookie']['domain']);
	}
    
	/**
     * 获取cookie值
     *
     * @param string $name cookie名称
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
     * 根据ip获取地址位置信息，返回城市id
     *
     * @param string $ip ip地址
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
     * 生成随机字符串
     *
     * @param integer $length 长度
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
     * 切断给定字符串到指定长度，用mbstring函数
     *
     * @param string $str 字符串
     * @param integer $length 长度
     *
     * @return string
     */
    function str_cut($str, $length = 0)
    {
        return mb_strcut($str, 0, $length, 'utf-8');
    }

	/**
     * 切断给定字符串到指定长度，一个汉字长度计为2
     *
     * @param string $str 字符串
     * @param integer $length 长度
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
     * 返回utf-8编码的字符串长度，一个汉字长度计为2
     *
     * @param string $str 字符串
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
     * 格式化时间
     *
     * @param integer $time 时间戳
     *
     * @return string
     */
    function format_time($time)
    {
        return date("Y-m-d H:i:s", $time);
    }

	/**
     * 格式化日期
     *
     * @param integer $time 时间戳
     *
     * @return string
     */
    function format_date($time)
    {
        return date("Y-m-d", $time);
    }


    /**
     * 重定向页面
     *
     * @return void
     */
	function redirect($url)
	{
		header("Location: $url");
		exit();
	}

    /**
     * 手动更新用户session
     *
     * @param integer $userid 用户id
     * @param string session名称
     * @param mixed  值
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
     * 初始化phpmailer对象
     *
     * @param string $user smtp用户名
     * @param string $pass smtp密码
     * @param string $name 发送人名称
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
     * 发送邮件
     *
     * @param object $mailer phpmailer对象
     * @param string $to 发送至
     * @param string $subject 标题
     * @param string $body 内容
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
     * 生成分页条
     *
     * @param integer $total 总记录数
     * @param integer $perpage 每页显示记录数
     * @param integer $page 当前页码
     * @param string $url 链接,其中的__page__将用页码替换
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
