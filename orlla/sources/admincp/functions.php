<?php
/**
 * $Id: functions.php 290 2007-06-30 12:35:41Z legend $
 */


function update_user_permission($userid)
{
    global $site, $db;

    $perms = $site->fetch_perms($userid);
    
    $site->update_session($userid, 'perms', $perms);

    return true;
}
?>