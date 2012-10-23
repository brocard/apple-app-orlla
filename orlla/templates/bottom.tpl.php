<div class="site_wrap">
    <div class="site_footer">        
    <?php
    if ( $site->input['debug'] == 'true' ) { 
        $site->timer->end();
        echo '<div style="padding:10px;margin:10px;border:1px solid #bbb;background-color:#FFFF99;color:black;font-size:14px;">';
        echo '<p><span style="display:inline-block;width:70px;">Time:</span>' . $site->timer->time . ' sec.</p>';
        echo '<p><span style="display:inline-block;width:70px;">Memory:</span>' . number_format(memory_get_usage()) . ' bytes</p>';
        echo '<p><span style="display:inline-block;width:70px;">Queries:</span>' . $site->db->total_queries() . ' query</p>';
        echo '<ul style="margin-left:70px;">';
        foreach ( $site->db->queries as $sql ) {
            echo '<li>' . $sql . '</li>';
        }
        echo '</ul>';
        echo '</div>';

        echo '<p> <a href="http://validator.w3.org/check?uri=referer">Check this page with W3C Markup Validator</a> </p>';
    }
    ?>
    </div>
</div>
