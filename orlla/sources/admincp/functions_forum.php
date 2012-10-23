<?php
/**
 * $Id: functions_forum.php 774 2007-12-29 18:13:03Z legend $
 */

function get_forums_tree($pid = 0, $flags = 0)
{
	global $site, $db;
    
    $opts   = $site->vars['forum']['options'];

    $where = ' 1 = 1 ';
    $where .= $flags & $opts['active'] ? ' AND Active = 1' : ' AND Active != 1';

	$db->query("SELECT * FROM forums WHERE $where ORDER BY Position, ForumID");

    $tree = array ();
	$p = array ();

	while ($forum = $db->fetch_row())
	{
		// 索引已建立，但无值，赋值
		if (isset($p[$forum['ForumID']]) && !$p[$forum['ForumID']]['forum'])
		{			
			$p[$forum['ForumID']]['forum'] = $forum;
		}
        
		// 根节点
		if ($forum['ParentID'] == 0)
		{
			$n = count($tree);
            
			// 如果已存在此根节点，加入树
			if (isset($p[$forum['ForumID']]))
			{				
				$tree[$n] = & $p[$forum['ForumID']];
			}
			// 否则建立此节点
			else
			{
				$tree[$n] = array ('forum' => $forum, 'childs' => array());				
			}
            
			// 更新索引
			$p[$forum['ForumID']] = & $tree[$n];

			continue;
		}
        
		// 子节点
		// 其父节点尚未索引，建立空索引
		if ($forum['ParentID'] && !isset($p[$forum['ParentID']]))
		{
			$p[$forum['ParentID']] = array ('forum' => null, 'childs' => array());
		}
        
		// 将当前节点链接到其父节点
		$n = count($p[$forum['ParentID']]['childs']);
		$childs = isset($p[$forum['ForumID']]) ? $p[$forum['ForumID']]['childs'] : array();
		$p[$forum['ParentID']]['childs'][$n] = array ('forum' => $forum, 'childs' => $childs);

		// 更新索引
		$p[$forum['ForumID']] = & $p[$forum['ParentID']]['childs'][$n];
	}

	if ($pid)
	{
		return $p[$pid]['childs'];
	}

    return $tree;
}

function update_forum_childs($pid = 0)
{
	global $db;

	$db->query("SELECT ForumID, ParentID FROM forums WHERE Active = 1 ORDER BY Position, ForumID");

    $tree = array ();
	$p = array ();

	while ($forum = $db->fetch_row())
	{
		// 索引已建立，但无值，赋值
		if (isset($p[$forum['ForumID']]) && !$p[$forum['ForumID']]['forum'])
		{
			$p[$forum['ForumID']]['forum'] = $forum;
		}
        
		// 根节点
		if ($forum['ParentID'] == 0)
		{
			$n = count($tree);
            
			// 如果已存在此根节点，加入树
			if (isset($p[$forum['ForumID']]))
			{				
				$tree[$n] = & $p[$forum['ForumID']];
			}
			// 否则建立此节点
			else
			{
				$tree[$n] = array ('forum' => $forum, 'childs' => array());				
			}
            
			// 更新索引
			$p[$forum['ForumID']] = & $tree[$n];

			continue;
		}
        
		// 子节点
		// 其父节点尚未索引，建立空索引
		if ($forum['ParentID'] && !isset($p[$forum['ParentID']]))
		{
			$p[$forum['ParentID']] = array ('forum' => null, 'childs' => array());
		}
        
		// 将当前节点链接到其父节点
		$n = count($p[$forum['ParentID']]['childs']);
		$childs = isset($p[$forum['ForumID']]) ? $p[$forum['ForumID']]['childs'] : array();
		$p[$forum['ParentID']]['childs'][$n] = array ('forum' => $forum, 'childs' => $childs);

		// 更新索引
		$p[$forum['ForumID']] = & $p[$forum['ParentID']]['childs'][$n];
	}

	foreach ($p as $item)
	{
		$childs = array($item['forum']['ForumID']);
		get_plain_childs($item['childs'], $childs);

		$childs = join(",", $childs);
		$db->query("UPDATE forums SET Childs = '$childs' WHERE ForumID = " . $item['forum']['ForumID']);
	}
}

function get_plain_childs($items, &$childs)
{
	if (!$items)
	{
		return false;
	}
    
	foreach ($items as $item)
	{
		array_push($childs, $item['forum']['ForumID']);
		get_plain_childs($item['childs'], $childs);
	}
}

function update_forums_js_object()
{
    global $site, $db;

    $tree = get_forums_tree();    

    function get_childs($forums)
    {
        $subs = array();

        if (!sizeof($forums))
        {
            return "{}";
        }

        foreach ($forums as $f)
        {
            $childs = get_childs($f['childs']);
            $length = sizeof($f['childs']);
            $forum = $f['forum']['ForumID'] . ": {id: " . $f['forum']['ForumID'] . ", title: '" . $f['forum']['Title'] . "', slug: '" . $f['forum']['Slug']. "', childs: $childs, length: $length}";
            array_push($subs, $forum);
        }

        return '{' . join(", ", $subs) . '}';
    }
    
    $forums = array();
    foreach ($tree as $f)
    {
        if ($f['forum']['Special'])
        {
            continue;
        }

        $childs = get_childs($f['childs']);
        $length = sizeof($f['childs']);
        $forum = $f['forum']['ForumID'] . ": {id: " . $f['forum']['ForumID'] . ", title: '" . $f['forum']['Title'] . "', slug: '" . $f['forum']['Slug'] . "', childs: $childs, length: $length}";
        array_push($forums, $forum);
    }
    
    $js  = '<script type="text/javascript">' . "\n";
    $js .=  "var forums = {" . join(", ", $forums) . "};\n";
    $js .= '</script>';

    $fp = fopen($site->vars['template']['templates_path'] . "city/forums_nav.tpl.php", "w");
    fwrite($fp, $js);
    fclose($fp);    
}


function make_forums_select_options($tree)
{
	$options = array();
	array_push ($options, array(0, '磨房论坛'));
    
	function make_options(&$options, $tree, $prefix = '')
	{ 
		foreach ($tree as $item)
		{
			$forum  = $item['forum'];
			$childs = $item['childs'];
			
			array_push($options, array($forum['ForumID'], $prefix . " " . $forum['Title']));

			if (sizeof($childs))
			{
				make_options($options, $childs, "----" . $prefix);
			}
		}
	}

	make_options($options, $tree, '----');

	return $options;
}
?>