
<script type="text/javascript">
var MISC_PATH = '<?=$STATIC_PATH?>';
var SITE_DOMAIN = '<?=$site->vars['site']['domain']?>';
</script>

<?php foreach ($this->scripts as $tmp_script):?>
<script type="text/javascript" src="<?=$STATIC_PATH . 'scripts/' . $tmp_script?>?v<?=$VERSION?>"></script>
<?php endforeach;?>
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

</body>
</html>
<?php ob_end_flush(); ?>
