<?php
date_default_timezone_set('PRC');

function get_next_time($line, $prev = 0)
{
    $sections = preg_split("/\s+/", $line);

    if (!$prev)
    {
        $prev = time();
    }

    $ranges = array (
        0 => array(0, 59),  // 分
        1 => array(0, 23),  // 時
        2 => array(1, 31),  // 日
        3 => array(1, 12),  // 月
        4 => array(0, 6),   // 星期
    );

    $expanded = array();

    for ($sec = 0; $sec < sizeof($sections); $sec ++)
    {
        $section = $sections[$sec];
        $res = array ();
        
        // range and step
        if (strpos($section, "/"))
        {
            list($range, $step) = explode("/", $section);
        }
        else
        {
            $range = $section;
            $step  = 1;
        }
        
        // generate item
        if (is_numeric($range))
        {
            $res = array($range);
        }
        elseif (strpos($range, ","))
        {
            $res = explode(",", $range);
        }
        else
        {
            $step = $step == "*" ? 1 : $step;

            if (preg_match("/(\d+)-(\d+)/", $range, $m))
            {
                $low  = $m[1];
                $high = $m[2];
            }
            elseif ($range == '*')
            {
                $low  = $ranges[$sec][0];
                $high = $ranges[$sec][1];
            }
            // not implement
            else
            {
                return false;
            }

            //echo "$low $high $step";
            if ($range == '*' && $step == 1)
            {
                array_push($res, '*');
            }
            else
            {
                for ($i = $low; $i <= $high; $i += $step)
                {
                    array_push($res, $i);
                }
            }
        }

        //print_r($res);
        array_push($expanded, $res);
    }
    
    // inline function
    if (!function_exists('get_nearest'))
    {
        function get_nearest($x, $to_check)
        {
            for ($i = 0; $i < sizeof($to_check); $i ++)
            {
                if ($to_check[$i] >= $x)
                {
                    return $to_check[$i];
                }
            }

            return false;
        }
    }

    // calculating time
    list ($src_year, $src_sec, $src_min, $src_hour, $src_day, $src_mon, $src_weekday) = explode(" ", date("Y s i H d n w", $prev));

    list ($dst_year, $dst_sec, $dst_min, $dst_hour, $dst_day, $dst_mon, $dst_weekday) = array($src_year, $src_sec, $src_min, $src_hour, $src_day, $src_mon, $src_weekday);

    while ($dst_year <= $src_year + 1)
    {
        // check month
        if ($expanded[3][0] !== '*') 
        {
            echo 'year';
            if (false == ($dst_mon = get_nearest($dst_mon, $expanded[3])))
            {
                $dst_mon = $expanded[0];
                $dst_year ++;
            }
        }

        // check for day of month
        if ($expanded[2][0] !== '*')
        {
            //echo "$dst_year-$dst_mon-$dst_day $dst_hour:$dst_min:$dst_sec <b>day</b><br />";
            if ($dst_mon != $src_mon)
            {
                $dst_day = $expanded[2][0];
            }
            else
            {
                if (false === ($dst_day = get_nearest($dst_day, $expanded[2])))
                {
                    // next day matched is within the next month. ==> redo it
                    $dst_day = $expanded[2][0];
                    $dst_mon ++;

                    if ($dst_mon > 12)
                    {
                        $dst_mon = 1;
                        $dst_year ++;
                    }

                    continue;
                }
            }
        }
        else
        {
            $dst_day = $dst_mon == $src_mon ? $dst_day : 1;
        }
        
        // heck for day of week
        if ($expanded[4][0] !== '*')
        {
            //echo "$dst_year-$dst_mon-$dst_day $dst_hour:$dst_min:$dst_sec $dst_weekday<b>weekday</b><br />";
            $weekday = $dst_weekday;
            $dst_weekday = get_nearest($dst_weekday, $expanded[4]);
            $dst_weekday = false !== $dst_weekday ? $dst_weekday : $expanded[4][0];            

            if ($dst_mon != $src_mon)
            {
                $dst_day = 1;
            }
            
            $days = $dst_weekday < $weekday ? (7 + $dst_weekday - $weekday) : ($dst_weekday - $weekday);
            $dst = mktime($dst_hour, $dst_min, $dst_sec, $dst_mon, $dst_day + $days, $dst_year);
            list($year, $mon, $day) = explode(" ", date("Y m d", $dst));

            if ($mon != $dst_mon || $year != $dst_year)
            {
                $dst_mon = $mon;
                $dst_year = $year;
                $dst_day = 1;
                //$dst_weekday = date("w", mktime(0, 0, 0, $dst_mon, $dst_day, $dst_year));
                continue;
            }

            $dst_day = $day;
        }
        else
        {
            if (!$dst_day)
            {
                $dst_day = $dst_mon == $src_mon ? $dst_day : 1;
            }
        }
        
        if ($expanded[1][0] !== '*')
        {
            //echo "$dst_year-$dst_mon-$dst_day $dst_hour:$dst_min:$dst_sec <b>hour</b><br />";
            if ($dst_day != $src_day)
            {
                $dst_hour = $expanded[1][0];
            }
            else
            {
                if (false === ($dst_hour = get_nearest($dst_hour, $expanded[1])))
                {
                    $dst_hour = $expanded[1][0];
                    
                    $dst = mktime(24 + $dst_hour, $dst_min, $dst_sec, $dst_mon, $dst_day, $dst_year);
                    list($dst_year, $dst_mon, $dst_day, $dst_weekday) = explode(" ", date("Y m d w", $dst));
                    continue;
                }
            }
        }
        else
        {
            $dst_hour = $dst_day == $src_day ? $dst_hour : 0;
        }
        
        // check for minute
        if ($expanded[0][0] !== '*')
        {
            //echo "$dst_year-$dst_mon-$dst_day $dst_hour:$dst_min:$dst_sec <b>min</b><br />";
            if ($dst_hour != $src_hour)
            {
                $dst_min = $expanded[0][0];
            }
            else
            {
                if (false === ($dst_min = get_nearest($dst_min, $expanded[0])))
                {                    
                    $dst_min = $expanded[0][0];
                    $dst = mktime($dst_hour, 60 + $dst_min, $dst_sec, $dst_mon, $dst_day, $dst_year);
                    list($dst_year, $dst_mon, $dst_day, $dst_hour, $dst_weekday) = explode(" ", date("Y m d H w", $dst));
                    continue;
                }
            }
        }
        else
        {
            $dst_min = $dst_hour == $src_hour ? $dst_min : 0;
        }

        $dst_sec = 0;
        
        $dst = mktime($dst_hour,$dst_min, $dst_sec, $dst_mon, $dst_day, $dst_year);

        if ($dst == mktime($src_hour,$src_min, 0, $src_mon, $src_day, $src_year))
        {
            $dst += 60;
            list($dst_year, $dst_mon, $dst_day, $dst_hour, $dst_min, $dst_weekday) = explode(" ", date("Y m d H i w", $dst));
            continue;
        }

        return $dst;
    }
}

function cron_log($type, $extra = '')
{
    global $db;
    
    $log = array (
        'Type' => $type,
        'DateLine' => time(),
        'Extra' => $extra
    );

    $db->insert('cron_log', $log);
}
?>