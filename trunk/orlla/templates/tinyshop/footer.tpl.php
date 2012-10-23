
 <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="<?=$STATIC_PATH.'scripts'?>/jquery-1.7.2.min.js"></script>
    <script src="<?=$STATIC_PATH.'scripts'?>/bootstrap-transition.js"></script>
    <script src="<?=$STATIC_PATH.'scripts'?>/bootstrap-alert.js"></script>
    <script src="<?=$STATIC_PATH.'scripts'?>/bootstrap-modal.js"></script>
    <script src="<?=$STATIC_PATH.'scripts'?>/bootstrap-dropdown.js"></script>
    <script src="<?=$STATIC_PATH.'scripts'?>/bootstrap-scrollspy.js"></script>
    <script src="<?=$STATIC_PATH.'scripts'?>/bootstrap-tab.js"></script>
    <script src="<?=$STATIC_PATH.'scripts'?>/bootstrap-tooltip.js"></script>
    <script src="<?=$STATIC_PATH.'scripts'?>/bootstrap-popover.js"></script>
    <script src="<?=$STATIC_PATH.'scripts'?>/bootstrap-button.js"></script>
    <script src="<?=$STATIC_PATH.'scripts'?>/bootstrap-collapse.js"></script>
    <script src="<?=$STATIC_PATH.'scripts'?>/bootstrap-carousel.js"></script>
    <script src="<?=$STATIC_PATH.'scripts'?>/bootstrap-typeahead.js"></script>


    <?php foreach ($this->scripts as $tmp_script):?>
    <script type="text/javascript" src="<?=$STATIC_PATH . 'scripts/' . $tmp_script?>?v<?=$VERSION?>"></script>
    <?php endforeach;?>

    <script type="text/javascript">
    var MISC_PATH = '<?=$STATIC_PATH?>';
    var SITE_DOMAIN = '<?=$site->vars['site']['domain']?>';

    function add_category(obj) {
        var form = '<form class="well form-search" method="post">'
                 + '<input type="hidden" name="act" value="add_tag">'
                 + '<input type="text" name="tag" class="input-medium search-query"> '
                 + '<button type="submit" class="btn">添加</button>'
                 + '</form>';
        $(obj).parent().parent().next().html(form);
    }

    function add_goods(obj) {
        var form = '<div class="span12">'
                 + '<form class="well form-horizontal" method="post">'
                 + '<input type="hidden" name="act" value="add_goods">'
                 + '<fieldset> ' 

                 + '<div class="control-group">' 
                 + '<label class="control-label" for="input01">名称</label>' 
                 + '<div class="controls">' 
                 + '<input type="text" class="input" id="input01" name="name">' 
                 + '</div>' 
                 + '</div>' 

                 + '<div class="control-group">' 
                 + '<label class="control-label" for="input02">分类</label>' 
                 + '<div class="controls" id="category_select">' 
                 + '</div>' 
                 + '</div>' 

                 + '<div class="control-group">' 
                 + '<div class="controls">' 
                 + '<button type="submit" class="btn">添加</button> '
                 + '<button type="cancel" class="btn">取消</button>'
                 + '</div>' 
                 + '</div>' 

                 + '</fieldset> '
                 + '</form>'
                 + '</div>';
        $(obj).parent().parent().next().html(form);

        $.get('./', {act: "category_select", }, function(msg){ 
            $('#category_select').html(msg);
        });
    }

    function goods_of_tag(obj, tag_id) {
        var form = 'goods of tag';
        $.get('./', {act: "goods_of_tag" , tag_id:tag_id}, function(msg){ 
            $(obj).parent().parent().html(msg);
        });
 
    }
    </script>

</body>
</html>
<?php ob_end_flush(); ?>
