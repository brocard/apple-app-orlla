<?php
/**
 $Id: class_template.php 930 2008-01-17 02:18:07Z legend $
*/

class Template
{
	var $site;

    var $templates_path;
	var $static_path;
            
    var $template;
    var $vars    = array();

	var $styles  = array();
	var $scripts = array();
    var $extra   = array();
	var $title   = array();
    
	// construct
    function Template(&$site)
    {
		$this->site = & $site;
        
		$templates_path = $site->vars['template']['templates_path'];
        $static_path = $site->vars['template']['static_path'];

        $this->templates_path = substr($templates_path, -1) == '/' ? $templates_path : $templates_path . "/";

		if ($static_path)
		{
			$this->static_path = substr($static_path, -1) == '/' ? $static_path : $static_path . "/";
		}
        
        if (isset($site->vars['site']['global_styles']))
        {
            $this->styles  = $site->vars['site']['global_styles'];            
        }

        if (isset($site->vars['site']['global_scripts']))
        {
            $this->scripts = $site->vars['site']['global_scripts'];
        }
    }
    
    function __set($var, $value)
    {
		$this->vars[$var] = $value;
    }
    
	function set($var, $value)
	{
		$this->vars[$var] = $value;
	}
    
	function set_title($title)
	{
		$this->title = $title;
	}

	function import_style()
	{
        $num  = func_num_args();
        $args = func_get_args();

        foreach ($args as $style)
        {
            if (is_array($style))
            {
                $this->styles = array_merge($this->styles, $style);
            }
            else
            {
                array_push($this->styles, $style);
            }
        }
	}

	function import_script()
	{
        $num  = func_num_args();
        $args = func_get_args();

        foreach ($args as $script)
        {
            if (is_array($script))
            {
                $this->scripts = array_merge($this->scripts, $script);
            }
            else
            {
                array_push($this->scripts, $script);
            }
        }
	}

	function head()
	{
        $num  = func_num_args();
        $args = func_get_args();

        foreach ($args as $line)
        {
            if (is_array($line))
            {
                $this->extra = array_merge($this->extra, $line);
            }
            else
            {
                array_push($this->extra, $line);
            }
        }
	}

	/*
	function load($template)
	{
		$template = str_replace('\\', '/', $this->templates_path . $template);
        
		extract($this->vars);
		include($template);
	}
	*/

	function display($template, $return = false)
	{
		$this->template = str_replace('\\', '/', $this->templates_path . $template);
		$this->template = preg_replace('/\/{2,}/', '/', $this->template);


        
		$site = & $this->site;
		$STATIC_PATH = $this->static_path;
		$TPL_PATH  = $this->templates_path;
        
        $site->get_error_messages();

		extract($this->vars, EXTR_REFS);

        //array_walk_recursive($site->input, 'htmlspecialchars');
        
		extract($this->hsc($site->input), EXTR_REFS | EXTR_PREFIX_ALL, '');

		error_reporting(E_ALL ^ E_NOTICE);        
		set_include_path($TPL_PATH);        
        
        ob_start();
		if ($return)
		{
			if (!@include($this->template))
            {
                trigger_error("Cannot load template <b>" . $template . "</b>", E_USER_ERROR);
                return;
            }

			$contents = ob_get_clean();
			return $contents;
		}
		else if (!@include($this->template))
		{
            trigger_error("Cannot load template <b>" . $template . "</b>", E_USER_ERROR);
            return;
		}        
	}
    
    // recursive HtmlSpecialChars
    function hsc($val)
    {
    	if ( $val == "" )
    	{
    		return "";
    	}
        
		if (is_array($val))
		{
			return array_map(array(&$this, 'hsc'), $val);
		}

        return htmlspecialchars($val, ENT_QUOTES);
    }
}
?>