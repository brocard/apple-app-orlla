<?php
/**
 * $Id: cron.php 399 2007-08-02 08:09:51Z legend $
 */

require_once "../init.php";
require_once SOURCES_PATH . "functions_cron.php";

session_write_close();
set_time_limit(0);
ignore_user_abort();

header('Content-Type: image/png');
echo base64_decode("iVBORw0KGgoAAAANSUhEUgAAAB0AAAAKCAYAAABIQFUsAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEgAACxIB0t1+/AAAABZ0RVh0Q3JlYXRpb24gVGltZQAwNi8yNi8wN7zCS0UAAAAfdEVYdFNvZnR3YXJlAE1hY3JvbWVkaWEgRmlyZXdvcmtzIDi1aNJ4AAAAYklEQVQ4jcWUSw4AIQhD24n3vzKuJKg4avx1pxD7SogUEcFlBXsgWTWcYFLTZHgjeOi35EAkFaycTOve1gDgmyG0j1mQPwivNpTUo13RVNJdemKq401L0lqOUl7/6Pj54nOIRIw1Eou+6FkAAAAASUVORK5CYII=");

$tasks = $db->get_all("SELECT * FROM cron WHERE NextTime <= " . TIMENOW . " AND Active = 1");
if (!$tasks)
{
    exit();
}

foreach ($tasks as $task)
{
    $db->query("UPDATE cron SET NextTime = " . get_next_time($task['Time'], TIMENOW) . " WHERE ID = " . $task['ID']);
}

foreach ($tasks as $task)
{
    include_once $site->vars['site']['cron_path'] . $task['File'];
}

exit(0);
