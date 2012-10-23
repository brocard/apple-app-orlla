<?php
/**
 * $Id: class_html.php 875 2008-01-10 09:51:32Z legend $
 */

class Html
{
    var $tokens = array();
	var $cols = array();
	var $rows = 0;
	var $th = false;

    var $styles  = array('misc/admincp.css');
    var $scripts = array();

	var $errors = array();

	function html($title = '')
	{
		$this->tokens = array();
		$this->cols = array();
		$this->rows = 0;
		$this->th = false;
	}
    
	function header($title = '')
	{
		$html  = '';

		$html .= "<html>\n";
		$html .= "<head>\n";
		$html .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n";
		$html .= "<title>$title</title>\n";
        foreach ($this->styles as $style)
        {
		    $html .= "<link rel=\"stylesheet\" href=\"$style\" type=\"text/css\" />\n";
        }

        foreach ($this->scripts as $script)
        {
            $html .= "<script type=\"text/javascript\" src=\"$script\"></script>";
        }

		$html .= "</head>\n";
		$html .= "<body>\n";

		array_push($this->tokens, 'html');
		array_push($this->tokens, 'body');

		return $html;
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

	function end($token = '')
	{
		$token = strtolower($token);
        
		$html = '';

		if ($token)
		{
			$p = array_search($token, array_reverse($this->tokens));

			if ($p === false)
			{
				$html .= "</$token>\n";
				return $html;
			}
            
			//$p = sizeof($this->tokens) - 1 - $p;

			for ($i = 0; $i < $p; $i ++)
			{
				$token = array_pop($this->tokens);
				$html .= "</$token>\n";
			}
		}

		$token = array_pop($this->tokens);
		$html .= "</$token>\n";
        
		if ($token == 'body')
		{
			$token = array_pop($this->tokens);
			$html .= "</$token>\n";
		}
        
		/*
		if ($token == 'html')
		{
			echo $this->contents;
		}
		*/

		return $html;
	}
    
	function flush()
	{
		/*
		while (sizeof($this->tokens))
		{
			$token = array_pop($this->tokens);
			$this->contents .= "</$token>\n";
		}
		*/

		//echo $this->contents;
	}
    
	function text()
	{
		return $text;
	}

	function table($caption = '', $width = '100%', $class = '')
	{
		$this->cols = array();
		$this->rows = 0;
		$this->th = false;

		$html = "<table cellpadding=\"3\" cellspacing=\"1\" width=\"$width\" border=\"0\" class=\"$class\">\n";
		if ($caption)
		{
            $html .= "<caption>$caption</caption>\n";
		}

		array_push($this->tokens, 'table');

		return $html;
	}
    
	// text = '', width, align = 'center'
	function th()
	{
		$num = func_num_args();
		$arg = func_get_arg();

		if ($num == 1 && is_array($arg))
		{
			// array(array(
			$arg = func_get_arg();
			$this->cols = array_merge($this->cols, $arg);
			return;
		}
        
		$args = func_get_args();
		if (!isset($args[1])) $args[1] = '';
        if (!isset($args[2])) $args[2] = 'left';

		$this->cols[] = $args;
	}

	function tr()
	{
		$args = func_get_args();
		
		$html = '';
        
        		
		if ($this->cols && !$this->th)
		{
			$display = false;
			$html .= "<tr class=\"header\">";
			foreach ($this->cols as $th)
			{
				$html .= "<th width=\"" . $th[1] . "\" align=\"" . $th[2]. "\">" . $th[0] . "</th>";

				if ($th[0] != '')
				{
					$display = true;
				}
			}
			$html .= "</tr>\n";

			if (!$display)
			{
				$html = '';
			}

			$this->th = true;
		}

		/*
		if (substr($this->contents, -6) != "</tr>\n")
		{
			$html .= "<tr class=\"header\">";
			for ($i = 0; $i < sizeof($this->cols); $i ++)
			{
				$th = $this->cols[$i];
				$html .= "<th width=\"" . $th[1] . "\" align=\"" . $th[2]. "\">" . $th[0] . "</th>";
			}
			$html .= "</tr>\n";
		}
		*/

		if (!sizeof($args))
		{
			$html .= "<tr><td colspan=\"" . sizeof($this->cols) . "\"></td></tr>\n";
			return $html;
		}
        
		$class = $this->rows % 2 == 0 ? 'alt1' : 'alt2';
		$html .= "<tr class=\"$class\">";
		for ($i = 0; $i < sizeof($args); $i ++)
		{
			//$col = array_shift($args);
			$col = $args[$i];
			$align = isset($this->cols[$i][2]) ? ' align="' . $this->cols[$i][2] . '"' : '';
			$width = isset($display) && $display == false ? ' width="' . $this->cols[$i][1] . '"' : '';
			$html .= "<td$width$align>$col</td>";
		}
		$html .= "</tr>\n";

		$this->rows ++;

		return $html;
	}

	function form($action = '', $method = 'post', $target = '')
	{
		array_push($this->tokens, 'form');

		$html  = "<form action=\"$action\" method=\"$method\" ";
		$html .= $target ? "target=\"$target\"" : "";
		$html .= ">\n";

		return $html;
	}
    
	function upload_form($action = '', $method = 'post', $target = '')
	{
		array_push($this->tokens, 'form');

		$html  = "<form action=\"$action\" method=\"$method\" enctype=\"multipart/form-data\" ";
		$html .= $target ? "target=\"$target\"" : "";
		$html .= ">\n";

		return $html;
	}

	function input($name, $value = '', $size = '40')
	{
		return "<input type=\"text\" name=\"$name\" value=\"$value\" size=\"$size\" />";
	}

	function file($name = 'file', $size = '40')
	{
		return "<input type=\"file\" name=\"$name\" size=\"$size\"/>&nbsp; ";
	}

	function textarea($name, $value = '', $cols = '40', $rows = "5")
	{
		return "<textarea name=\"$name\" cols=\"$cols\" rows=\"$rows\">$value</textarea>";
	}

	function button($name, $value, $onclick = '')
	{
		$html  = "<input type=\"button\" name=\"$name\" value=\"$value\"";
		$html .= $onclick ? " onclick=\"$onclick\"" : "";
		$html .= "/>\n";

		return $html;
	}
    
	function submit($value = 'Submit', $name = 'submit')
	{
		return "<input type=\"submit\" name=\"$name\" value=\"$value\" />";
	}

	function reset($value = 'Submit', $name = 'submit')
	{
		return "<input type=\"reset\" name=\"$name\" value=\"$value\" />";
	}

	function hidden($name, $value)
	{
		return "<input type=\"hidden\" name=\"$name\" value=\"$value\" />";
	}

	function radio($name, $value, $text, $checked = 0)
	{
		$html  = "<input type=\"radio\" name=\"$name\"";
		$html .= $checked ? ' checked="checked"' : '';
		$html .= "/>";

		return $html;
	}

	function yesno($name, $value = 1)
	{
		$html  = "<input type=\"radio\" name=\"$name\" value=\"1\" " . ($value == 1 ? 'checked="checked"' : '') . "/>是 ";
		$html .= "<input type=\"radio\" name=\"$name\" value=\"0\" " . ($value == 0 ? 'checked="checked"' : '') . "/>否";

		return $html;
	}

	function select($name, $options = array(), $selected = 0)
	{
		$html  = "<select name=\"$name\">";
		foreach ($options as $option)
		{
			$s = $option[0] == $selected ? ' selected="selected"' : '';
		    $html .= "<option value=\"" . $option[0] . "\"$s>" . $option[1] . "</option>";
		}
		$html .= "</select>";

		return $html;
	}

	function option($value, $text, $selected = 0)
	{
		$html  = "<option value=\"$value\"";
		$html .= $selected ? ' selected="selected"' : '';
		$html .= ">$text</option>";

		return $html;
	}
    
	function div($contents = '', $extra = '')
	{
		return $this->element('div', $contents, $extra);
	}

	function p($contents = '', $extra = '')
	{
		return $this->element('p', $contents, $extra);
	}

	function span($contents = '', $extra = '')
	{
		return $this->element('span', $contents, $extra);
	}
	
	function b($contents = '', $extra = '')
	{
		return $this->element('b', $contents, $extra);
	}

	// div, p, span etc...
	function element($token, $contents = '', $extra = '')
	{
		$html  = "<$token";
		$html .= $extra ? " $extra" : "";
		$html .= ">\n";
		$html .= $contents ? "$contents\n" : "";

		if (!$contents)
		{
			array_push($this->tokens, $token);
			return $html;
		}

		$html .= "</$token>\n";

		return $html;
	}
    
	function image($src, $alt = '', $width = '', $height = '')
	{
		return "<img src=\"$src\" alt=\"$alt\" border=\"0\"/>";
	}

	function link($url, $text, $target = '')
	{
		$html  = "<a href=\"$url\"";
		$html .= $target ? " target=\"$target\"" : '';
		$html .= ">$text</a>";

		return $html;
	}

	function confirm_link($url, $text, $msg = '')
	{
		return "<a href=\"$url\" onclick=\"if (!window.confirm('$msg')) return false\">$text</a>";
	}

	function flash($url, $message, $sec = 0)
	{
		$html  = "<script type=\"text/javascript\">\n";
		$html .= "setTimeout(\"window.location='$url'\", $sec * 1000);";
		$html .= "</script>";

		$html .= "<div class=\"div_flash_message\">\n";
		$html .= "<div>$message</div>\n";
		$html .= "<a href=\"$url\">页面在 $sec 秒后跳转, 请稍候...</a>";
		$html .= "</div>";

		return $html;
	}

	function error()
	{
		$args = func_get_args();
		$str  = array_shift($args);

		array_push($this->errors, array('str' => $str, 'args' => $args));
	}
    
	function has_error()
	{
		if (sizeof($this->errors))
		{
			return true;
		}

		return false;
	}

	function no_error()
	{
		if (sizeof($this->errors))
		{
			return false;
		}

		return true;
	}

	function errors()
	{
		$html  = '<div class="div_errors">';
		$html .= '<ul class="ul_errors">';

		foreach ($this->errors as $error)
		{   
			array_unshift($error['args'], "<li>$error[str]</li>");
            $html .= call_user_func_array("sprintf", $error['args']);
		}

		$html .= '</ul>';
		$html .= '</div>';

		return $html;
	}
}
?>