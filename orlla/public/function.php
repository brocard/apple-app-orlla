<?php

function getPageTitle($id)
{ 
    global $site;
    $sql = "SELECT page_title FROM page WHERE page_id=$id";
    return $site->db->get_field($sql);
}


//end file
