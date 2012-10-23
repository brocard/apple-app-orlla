<?php
/**
 * $Id: functions_bbcode.php 869 2008-01-10 03:32:50Z legend $
 */

define('BBCODE_OPTION', 1);
define('BBCODE_CALLBACK', 2);
define('BBCODE_TRIM', 4);
define('BBCODE_SINGLE', 8); 

$bb_tokens = array();
$bb_replacements  = array();
$bb_patterns = array();
$bb_smilies = array (
    array(':\)', 'smile.gif', 'smile'),
    array(':D', 'smile_big.gif', 'big smile'),
    array('8D', 'smile_cool.gif', 'cool'),
    array(':I', 'smile_blush.gif', 'blush'),
    array(':P', 'smile_tongue.gif', 'tongue'),
    array(':\}\)', 'smile_evil.gif', 'evil'),
    array(';\)', 'smile_wink.gif', 'wink'),
    array(':o\)', 'smile_clown.gif', 'clown'),
    array('B\)', 'smile_blackeye.gif', 'black eye'),
    array(':\(', 'smile_sad.gif', 'sad'),
    array(':8\)', 'smile_shy.gif', 'shy'),
    array(':O\)', 'smile_shock.gif', 'shocked'),
    array(':\!\(', 'smile_angry.gif', 'angry'),
    array('xx\(', 'smile_dead.gif', 'dead'),
    array('\|\)', 'smile_sleepy.gif', 'sleepy'),
    array(':X', 'smile_kisses.gif', 'kisses'),
    array(':\^\)', 'smile_approve.gif', 'approve'),
    array(':\~\)', 'smile_disapprove.gif', 'disapprove'),
    array(':\?\)', 'smile_question.gif', 'question'),
);

buildPatterns($bb_tokens, $bb_patterns, $bb_replacements);

function buildPatterns(&$tokens, &$patterns, &$replacements)
{
    $tokens = array (
        'code'   => BBCODE_CALLBACK | BBCODE_TRIM,
        'quote'  => BBCODE_OPTION | BBCODE_CALLBACK | BBCODE_TRIM,
        'b'      => BBCODE_SINGLE,
        'i'      => BBCODE_SINGLE,
        'u'      => BBCODE_SINGLE,
        'color'  => BBCODE_OPTION | BBCODE_SINGLE,
        'font'   => BBCODE_OPTION | BBCODE_SINGLE,
        'size'   => BBCODE_OPTION | BBCODE_SINGLE,
        'center' => BBCODE_SINGLE,
        'sub'    => BBCODE_SINGLE,
        'sup'    => BBCODE_SINGLE,
        'url'    => BBCODE_OPTION | BBCODE_CALLBACK | BBCODE_TRIM,
        'email'  => BBCODE_OPTION | BBCODE_CALLBACK | BBCODE_TRIM,
        'img'    => BBCODE_CALLBACK,
        //'iframe' => BBCODE_CALLBACK
    );

    $replacements = array (
        'code'   => "handle_code('\\1')",
        'quote'  => "handle_quote('\\3', '\\5')",
        'b'      => '<strong>',
        '/b'     => '</strong>',
        'i'      => '<em>',
        '/i'     => '</em>',
        'u'      => '<ins>',
        '/u'     => '</ins>',
        'color'  => '<span style="color: \\3">',
        '/color' => '</span>',
        'font'   => '<span style="font-family: \\3">',
        '/font'  => '</span>',
        'size'   => '<span style="font-size: \\3">',
        '/size'  => '</span>',
        'center' => '<div style="text-align: center">',
        '/center'=> '</div>',
        '/size'  => '</div>',
        'sub'    => '<sub>',
        '/sub'   => '</sub>',
        'sup'    => '<sup>',
        '/sup'   => '</sup>',
        'url'    => "handle_link('\\3', '\\5')",
        'email'  => "handle_email('\\3', '\\5')",
        'img'    => "handle_image('\\1')",
        //'iframe' => "handle_iframe('\\1")',
    );

    $patterns = array ();

    foreach ($tokens as $token => $options)
    {
        $pattern = "/\[$token";

        if ($options & BBCODE_OPTION)
        {
            $chars = in_array($token, array('color', 'font', 'size')) ? '[a-z0-9# ]' : '.';
            $pattern .='(=(["\'])?(' . $chars . '+?)(\\2)?)?';
        }

        $pattern .= "\]";
        
        if ($options & BBCODE_SINGLE)
        {
            $pattern .= "/is";

            array_push($patterns, $pattern);

            $pattern = "/\[\/$token\]/is";
            array_push($patterns, $pattern);
        }
        else
        {
            $recur = strtolower($token) == 'quote' ? '?' : '?';
            if ($options & BBCODE_TRIM)
            {
                $pattern .= "\s*?(.*$recur)\s*?";
            }
            else
            {
                $pattern .= "(.*$recur)";
            }

            $pattern .= "\[\/$token\]/is";

            if ($options & BBCODE_CALLBACK)
            {
                $pattern .= "e";
            }

            array_push($patterns, $pattern);
        }        
    }

    return true;
}

function parse_bbcode($text)
{
    global $site, $bb_patterns, $bb_replacements;    

    // auto detect links
    $text = detect_links($text);
    
    // doyouhike resources
    $text = preg_replace("/\[img\](\d{4}\/\d{2}\/\d{2}\/.+?)\[\/img\]/si", "[img]" . $site->vars['site']['data_url'] . "\\1[/img]", $text);
    $text = preg_replace("/\[url=(\d{4}\/\d{2}\/\d{2}\/.+?)\](.+?)\[\/url\]/si", 
                         "[url=" . $site->vars['site']['data_url'] . "\\1]\\2[/url]", $text);    
    $text = preg_replace("/\[file=(\d{4}\/\d{2}\/\d{2}\/.+?)\](.+?)\[\/file\]/si", 
                         "[url=" . $site->vars['site']['data_url'] . "\\1]\\2[/url]", $text);
    
    $text = preg_replace($bb_patterns, $bb_replacements, $text);

    return $text;
}

function escape_bbcode($text)
{
    global $bb_tokens;
    
    $patterns = array ();
    foreach ($bb_tokens as $token => $options)
    {
        $pattern = "/\[$token";

        if ($options & BBCODE_OPTION)
        {
            $pattern .='.*?';
        }

        $pattern .= "\]/i";
        
        array_push($patterns, $pattern);
        array_push($patterns, "/\[\/$token\]/i");
    }

    $text = preg_replace($patterns, '', $text);

    return $text;
}

function parse_smiles($text, $path = '')
{
    // ut's smiles
    global $bb_smilies;

    foreach ($bb_smilies as $smileys)
    {
        $text = preg_replace('/' . $smileys[0] . '/', '<img src="' . $path . $smileys[1] . '" alt="' . $smileys[2] . '" />', $text, 3);
    }

    return $text;
}

function detect_links($text)
{
    $links_patterns = array(
        "`(?<=^|[^]a-z0-9_\-=\"'/])((https?|ftp|gopher|news|telnet)://|www\.)([,\.\?\&%=a-z\_\-\|\d/#;]+)`si",
        //"/([^]_a-z0-9-=\"'\/])?((https?|ftp|gopher|news|telnet):\/\/|www\.)([,\.\?\&%=a-z\_\-\|\d\/]*)(?![^[]*\[\/url\])/si",
        "/^((https?|ftp|gopher|news|telnet):\/\/|www\.)([,\.\?\&%=a-z\_\-\|\d\/]+?)/si"
    );

    $links_replacements = array(
      "[url]\\1\\3[/url]",
      //"\\1[url]\\2\\4[/url]",
      "[url]\\1\\3[/url]"
    );

    $email_patterns = array(
      "/([ \n\r\t])([_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,4}))/si",
      "/^([_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,4}))/si"
    );

    $email_replacements = array(
      "\\1[email]\\2[/email]",
      "[email]\\0[/email]"
    );

    $text = preg_replace($links_patterns, $links_replacements, $text);

    if (strpos($text, "@"))
    {
        $text = preg_replace($email_patterns, $email_replacements, $text);
    }

    return $text;
}

function handle_code($code)
{
    $html  = '<div style="font-family: courier new; font-size: 12px; border: 1px solid #ccc; padding: 5px">';
    $html .= str_replace(array(" ", "\t"), array("&nbsp;", "&nbsp;&nbsp;"), $code);
    $html .= '</div>';

    return $html;
}

function handle_quote($user, $text)
{
    $html  = "<blockquote style='color: #0000a0'>";
    $html .= $user ? "<b>$user wrote:</b><br />" : '';
    $html .= parse_bbcode($text);
    $html .= "</blockquote>";

    return $html;
}

function handle_link($url, $text)
{
    if ($url && !preg_match("/^(https?|ftp|gopher|news|telnet):\/\//is", $url))
    {
        $url = "http://" . $url;
    }

    if (!$url)
    {
        if (!preg_match("/^(https?|ftp|gopher|news|telnet):\/\//is", $text))
        {
            $url = "http://" . $text;
        }
        else
        {
            $url = $text;
        }
        
        /*
        if (strlen($text) > 60)
        {
            $text = substr($text, 0, 47) . "..." . substr($text, -10);
        }
        */
    }

    return '<a href="' . $url . '" target="_blank">' . $text . '</a>';
}

function handle_email($email, $text)
{
    $tmp = $email ? $email : $text;

    if (!preg_match( "/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,4})(\]?)$/", $tmp))
    {
        $original  = "[email";
        $original .= $email ? ']' : "=$email]";
        $original .= "$text\[/email]";

        return $original;
    }
    
    return "<a href=\"mailto:$tmp\">$text</a>";
}

function handle_image($img)
{
    // disable dynamic scripts
    $tmp = $img;
    if (($pos = strpos($tmp, '?')) !== false)
    {
        $tmp = substr($tmp, 0, $pos);
    }

    $tmp = str_replace('#', '', $tmp);

    $ext = false === strrpos($tmp, '.') ? '' : strtolower(substr($tmp, strrpos($tmp, '.') + 1));

    if (!$tmp || !in_array($ext, array('jpg', 'jpeg', 'bmp', 'png', 'tiff', 'gif')))
    {
        return "[img]{$img}[/img]";
    }
    
    // todo: auto resize large image

    return "<img src=\"$tmp\" alt='' border=\"0\" onload='if (this.width > 800) this.width=800'/>";
}

function handle_iframe($url)
{
    // todo: parse maps tag from mapinbox

    return '<iframe src="' . $url. '" width="400" height="300"></iframe>';
}


?>