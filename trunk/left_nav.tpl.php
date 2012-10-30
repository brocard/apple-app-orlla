<ul class="nav nav-tabs">
    <li<?=($app_tb == 'app'  ? ' class="active" ' : '')?>><a href="./?app_tb=app">apple</a>
    <?php 
    if ($app_tb == 'papa') {
        if (isset($_REQUEST['tag'])) {
            echo '<li><a href="./?app_tb=papa">papa</a>';
            echo '<li class="active"><a href="./?tag=">tags</a>'; 
        } else {
            echo '<li class="active"><a href="./?app_tb=papa">papa</a>';
            echo '<li><a href="./?tag=">tags</a>'; 
        }
    } else {
        echo '<li><a href="./?app_tb=papa">papa</a>';
    }
    ?>
</ul>

<ul class="nav nav-list"> 
<?php

if (isset($_REQUEST['tag'])) {
    $sql = "SELECT * FROM tags"; 
    $rows = mysql_query($sql, $conn); 
    while ( $row = mysql_fetch_array($rows) ) {
        echo '<li'.($tag == $row['tag'] ? ' class="active" ' : '').'>';
        echo '<a href="?tag='.$row['tag'].'">' .$row['tag'].'</a>'; 
    } 

    echo '<br/><input id="new_tag" type="text" class="input-medium"><br />';
    echo '<span class="btn" onclick="add_tag();">Add Tag</span>'; 

} else { 
    $sql = "SELECT DISTINCT primaryGenreName FROM $app_tb"; 
    $mem_key = $app_tb.'_cat_'.$cat; 
    $app_category = $mem->get($mem_key);
    if ( ! $app_category ) {
        if ($miss_hit) echo '<p>cat miss';
        $rows = mysql_query($sql, $conn); 

        $app_category = '<li'. ($cat==''? ' class="active"':'') .'><a href="./?">All</a>'; 
        while ( $row = mysql_fetch_array($rows) ) {
            $app_category .= '<li'.($cat==$row['primaryGenreName'] ? ' class="active"':'') .'>';
            $app_category .= '<a href="?cat='.$row['primaryGenreName'].'">' .$row['primaryGenreName'].'</a>'; 
        } 

        $mem->set($mem_key, $app_category, 0, 600); 
    } else {
        if ($miss_hit) echo '<p>cat hit';
    }

    echo $app_category; 
} 

?>
</ul>
