<?php
/**
 $Id: functions.php 325 2007-07-05 08:56:28Z legend $
*/

function load_class($class_file)
{
    require_once SOURCES_PATH . $class_file;
}

function import($func_file)
{
    require_once SOURCES_PATH . $func_file;
}

/*
// htmlspecialchars
function h($text)
{
	if (is_array($text))
    {
		return array_map('h', $text);
	}

	return htmlspecialchars($text);
}
*/

function strip_slashes($t)
{
	if (get_magic_quotes_gpc())
	{
		$t = stripslashes($t);
		$t = preg_replace( "/\\\(?!&amp;#|\?#)/", "&#092;", $t );
	}
	
	return $t;
}

function escape_html($val)
{
	if ( $val == "" )
	{
		return "";
	}
	
	if (is_array($val))
	{
		return array_map('escape_html', $val);
	}
    
	$val = str_replace( "&#032;", " ", strip_slashes($val) );
	
	/*
	if ( isset($this->vars['strip_space_chr']) AND $this->vars['strip_space_chr'] )
	{
		$val = str_replace( chr(0xCA), "", $val );  //Remove sneaky spaces
	}
	*/
	
	$val = str_replace("&"				, "&amp;"         , $val);
	$val = str_replace("<!--"			, "&#60;&#33;--"  , $val);
	$val = str_replace("-->"			, "--&#62;"       , $val);
	$val = preg_replace("/<script/i"	, "&#60;script"   , $val);
	$val = str_replace(">"				, "&gt;"          , $val);
	$val = str_replace("<"				, "&lt;"          , $val);
	$val = str_replace('"'				, "&quot;"        , $val);
	$val = str_replace("\n"			, "<br />"        , $val); // Convert literal newlines
	$val = str_replace("$"				, "&#036;"        , $val);
	$val = str_replace("\r"			, ""              , $val); // Remove literal carriage returns
	$val = str_replace("!"				, "&#33;"         , $val);
	$val = str_replace("'"				, "&#39;"         , $val); // IMPORTANT: It helps to increase sql query safety.
	
	// Ensure unicode chars are OK
	
	$val = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $val );
	
	//-----------------------------------------
	// Try and fix up HTML entities with missing ;
	//-----------------------------------------

	$val = preg_replace( "/&#(\d+?)([^\d;])/i", "&#\\1;\\2", $val );

	/*
	if ( $this->allow_unicode )
	{
		$val = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $val );
		
		//-----------------------------------------
		// Try and fix up HTML entities with missing ;
		//-----------------------------------------

		$val = preg_replace( "/&#(\d+?)([^\d;])/i", "&#\\1;\\2", $val );
	}
	*/
	    
	return $val;
}

// file_put_contents
if (!function_exists('file_put_contents'))
{
	function file_put_contents($fileName, $data)
    {
		if (is_array($data))
		{
			$data = join('', $data);
		}

		$res = @fopen($fileName, 'w+b');

		if ($res) {
			$write = @fwrite($res, $data);

			if($write === false)
			{
				return false;
			}
			else
			{
				return $write;
			}
		}
	}
}

function mkdirs($dir, $mode = 0777, $recursive = true)
{
    if( is_null($dir) || $dir === "" )
    {
      return FALSE;
    }

    if( is_dir($dir) || $dir === "/" )
    {
      return TRUE;
    }

    if(mkdirs(dirname($dir), $mode, $recursive))
    {
      return mkdir($dir, $mode);
    }

    return false;
}

function print_a($val, $return = false)
{
    $out  = "<pre style=\"background: #000; color: #ccc; font: 12px 'fixedsys'; text-align: left; width: 100%; padding: 5px\">\n";
    $out .= print_r($val, true);
    $out .= "</pre>\n";

    if ($return)
    {
        return $return;
    }

    echo $out;
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

    $html = '<div class="pagination"><ul>';

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
        $html .= "<li><a>Prev</a></li>";   
    }
    else
    {
        $html .= "<li><a href=\"" . str_replace("__page__", $page - 1, $url) . "\">Prev</a></li>";
    }

    if ($start > 1)
    {
        $html .= "<li><a href=\"" . str_replace("__page__", 1, $url) . "\">1</a></li>";
    }

    if ($start > 2)
    {
        $html .= "<li><a href=\"" . str_replace("__page__", 2, $url) . "\">2</a></li>";
    }

    if ($start > 3)
    {
        $html .= "<li><a>...</a></li>";
    }
    //

    for ($i = $start; $i <= $end; $i ++)
    {
        if ($page == $i)
        {
            $html .= "<li><a href=\"" . str_replace("__page__", $i, $url) . "\" class=\"current\">$i</a></li>";
        }
        else
        {
            $html .= "<li><a href=\"" . str_replace("__page__", $i, $url) . "\">$i</a></li>";
        }
    }
    
    if ($end < $pages - 1)
    {
        $html .= "<li><a>...</a></li>";
    } 

    if ($end < $pages)
    {
        $html .= "<li><a href=\"" . str_replace("__page__", $pages, $url) . "\">$pages</a></li>";
    }

    if ($page >= $pages)
    {
        $html .= "<li><a>Next</a></li>";
    }
    else
    {
        $html .= "<li><a href=\"" . str_replace("__page__", $page + 1, $url) . "\">Next</a></li>";
    }
    //
    
    $html .= "</ul></div>";

    return $html;
}

//end file
