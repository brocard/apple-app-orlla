<?php include "tinyshop/header.tpl.php"; ?>
<?php include "tinyshop/top.tpl.php"; ?>

<div class="container">
    <div class="row">
        <div class="span12" style="text-align:center;">
            <img src="/G3_Lite/static/img/home_tinyshop.jpg" alt="site logo image" width="747" height="420" />
        </div>
    </div>

    <div class="row">
        <div class="span1" style="padding:2px 0px;text-align:center;">
            <input class="btn" type="button" onclick="add_category(this);" value="+ 分类">
        </div>

        <div class="span1" style="padding:2px 0px;text-align:center;">
            <input class="btn" type="button" onclick="add_goods(this);" value="+ 物品">
        </div>
    </div>

    <div class="row">
        <div id="articleList" style="padding:10px 0px;">
        <?php
        foreach ($category as $item) { 
            $id   = $item['id'];
            $tag = $item['tag'];
            echo "<span id='$id' onclick=\"goods_of_tag(this,'$id');\">$tag</span>";
        }
        ?>
        </div>
    </div> 

</div>

<?php include "tinyshop/bottom.tpl.php"; ?>
<?php include "tinyshop/footer.tpl.php"; ?>
