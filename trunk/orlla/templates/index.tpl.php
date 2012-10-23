<?php include "header.tpl.php"; ?>
<?php include "top.tpl.php"; ?>


        <?php
        if (count($wikiPage)>0) {
echo ' <div class="container"> 
    <div class="row">
        <div id="articleList" style="padding:10px 0px;">';
        foreach ($wikiPage as $Page) { 
            $title   = $Page['page_title'];
            $counter = $Page['page_counter'];

             $r = dechex($counter*7>255 ? 255 : $counter*7);
                $r = (strlen($r) == 1 ? '0' : '') . $r;
                            
                                 $color = "style='color:#$r" . "88cc'";


            echo "<span $color><a href='/wiki/$title' $color>$title</a> $counter</span>";
        }
echo ' </div>
    </div> 
</div>';
        }
        ?>

<div class="container"> 
    <div class="row">
    <p>
    <a href="/">最热文章</a> <a href="?order=new">最新文章</a>
    </p>
        <?php echo '页面总数：' . count($page_counter) . ' 个';?>

        <div id="article-list" style="padding:10px 0px;">
        <?php
        foreach ($page_counter as $Page) { 
            $title   = $Page['page_title'];
            $counter = $Page['page_counter']; 
            echo "<span><a href='/wiki/$title'>$title</a> ($counter)</span>";
        }
        ?>
        </div>
    </div> 
</div>

<?php include "bottom.tpl.php"; ?>
<?php include "footer.tpl.php"; ?>
