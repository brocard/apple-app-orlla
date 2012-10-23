<?php
/**
 * $Id$
 */

//$db->query("UPDATE users u, sessions s SET u.LastActivity = s.LastActivity WHERE u.UserID = s.UserID");
$db->query("DELETE FROM sessions WHERE " . time() . " - LastActivity > " . $site->vars['session']['lifetime']);
$db->query("OPTIMIZE TABLE sessions");

cron_log('sessions');