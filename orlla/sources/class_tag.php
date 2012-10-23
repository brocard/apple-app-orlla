<?php
/**
 * $Id$
 */

class Tag
{
    /**
     * parse string and get tag ids
     *
     * @param string $str    the string
     * @param string $system
     *
     * @return array $tags (only id)
     */
    function parse_string($str)
    {
        global $db;

        $symbols = array(",", "/", "\\", ".", ";", ":", "\"", "!", "~", "`","^", "(", ")", "?", "-", "\t", "\n", "'", "<", ">", "\r", "\r\n","$", "&", "%", "#", "@", "+", "=", "{", "}", "[", "]", "：", "）","（", "．", "。", "，", "！", "；", "“", "”", "‘", "’", "［", "］", "、", "—","　", "《", "》", "－", "…", "【", "】",);
        $str = trim(str_replace($symbols, ' ', $str));

        $words = explode(" ", $str);
        
        $tags = array();
        foreach ($words as $word)
        {
            $word = trim($word);
            if (empty($word))
            {
                continue;
            }

            if ($tag = $db->get_row('SELECT * FROM tags WHERE Name = ' . $db->escape($word)))
            {
                array_push($tags, $tag['TagID']);
                continue;
            }
            
            $tag = array (
                'Name' => $word,
                'System' => 'unsorted'
            );
            
            $db->insert("tags", $tag);
            $tag['TagID'] = $db->insert_id();

            array_push($tags, $tag['TagID']);
        }

        return $tags;
    }

    /**
     * @return array
     */
    function get_tags_by_id($ids)
    {
        global $db;

        if (!is_array($ids))
        {
            $ids = explode(",", $ids);
        }

        $tags = array();
        $db->query('SELECT TagID, Name FROM tags WHERE TagID IN (' . join(", ", $ids) . ')');
        while ($row = $db->fetch_row())
        {
            array_push($tags, $row['Name']);
        }

        return $tags;
    }
}
?>