<?php
/**
 *
*/

require_once './init.php';

$now = time();

// accept "time" (day|week|month|all)

if (isset($site->input['time']) AND $site->input['time'] == 'month')
{
    $where = "WHERE DateLine > $now - 86400*30";
}
elseif (isset($site->input['time']) AND $site->input['time'] == 'week')
{
    $where = "WHERE DateLine > $now - 86400*7";
}
elseif (isset($site->input['time']) AND $site->input['time'] == 'all')
{
    $where = "";
}
else
{
    $where = "WHERE DateLine > $now - 86400";
    $site->input['time'] = 'day';
}

$tpl->set("time", $site->input['time']);

$topics = $digs = $authors = $forums = $cities = array();

// get topic id string from dig_topic table
$targetstr = "";
$top20 = $db->get_all("SELECT TopicID, SUM(DigNum) AS DigCount FROM dig_topic $where GROUP BY TopicID ORDER BY DigCount DESC, TopicID DESC LIMIT 20");
foreach($top20 as $top)
{
    if ($top['DigCount'] <= 1)
    {
        continue;
    }
    else
    {
        $targetstr .= $targetstr ? "," . $top['TopicID'] : $top['TopicID'];
        $digs[$top['TopicID']] = $top['DigCount'];
    }
}

// get topics 

$db->query("SELECT t.*, p.PostContent FROM topics t LEFT JOIN posts p ON t.TopicID = p.TopicID WHERE t.TopicID IN ($targetstr) AND t.TopicType = 'forum' AND t.AutoDel = 0 AND p.ParentID = 0 ORDER BY t.Created DESC");

//$db->query("SELECT d.TopicID, t.Title, t.Author, t.AuthorID, t.Created, t.CityID, t.ParentID, SUM(d.DigNum) AS DigCount, t.DigNum, t.PostNum, t.HitNum FROM dig_topic d LEFT JOIN topics t ON d.TopicID = t.TopicID WHERE d.DateLine > $now $querystring GROUP BY d.TopicID ORDER BY DigCount DESC, d.TopicID DESC LIMIT 20");
while ($topic = $db->fetch_row())
{
    $topic['Created'] = date("Y-m-d H:i", $topic['Created']);
    $topic['DigCount'] = number_format($digs[$topic['TopicID']]);
    $topic['PostNum'] = number_format($topic['PostNum']-1);
    $topic['HitNum'] = number_format($topic['HitNum']);
    $topic['PostContent'] = $site->remove_bbcode($topic['PostContent']);
    $topic['PostContent'] = $site->smart_cut($topic['PostContent'], 500);
    $topic['PostContent'] = preg_replace('/http:\/\/static\.doyouhike\.net\/files\//i', "", $topic['PostContent']);
    $topic['PostContent'] = preg_replace('/\.(jpg|png|gif)\s?<br \/>/i', ".$1 ", $topic['PostContent']);
    $topic['PostContent'] = preg_replace('/(20[0-1][0-9]\/[0-1][0-9]\/[0-3][0-9]\/.*?)\.(jpg|png|gif)/i', "<img src=\"" . $site->vars['site']['data_url'] . "$1_s.jpg\"> ", $topic['PostContent']);

    array_push($topics, $topic); 
    array_push($authors, $topic['AuthorID']);
    array_push($forums, $topic['ParentID']);
    array_push($cities, $topic['CityID']);
}
$tpl->set("topics", $topics);

// get authors

$author_str = join(',', $authors);
unset($authors);
$db->query("SELECT * FROM users WHERE UserID IN ($author_str)");
while ($user = $db->fetch_row())
{
    $user['Face'] = $site->get_user_face($user, 'thumb');
    $authors[$user['UserID']] = $user;
}
$tpl->set("authors", $authors);

$tpl->set("topics", $topics);

// get forums

$forum_str = join(',', $forums);
unset($forums);
$db->query("SELECT * FROM forums WHERE ForumID IN ($forum_str) AND ForumID != 0");
while ($forum = $db->fetch_row())
{
    $forums[$forum['ForumID']] = $forum;
}
$tpl->set("forums", $forums);

// get cities

$city_str = join(',', $cities);
unset($cities);
$db->query("SELECT * FROM cities WHERE CityID IN ($city_str) AND CityID != 0");
while ($city = $db->fetch_row())
{
    $cities[$city['CityID']] = $city;
}
$tpl->set("cities", $cities);

$tpl->import_style('site.css', 'index.css');
$tpl->import_script('common.js', 'forum.js');

$tpl->set_title("最近好评话题");
$tpl->display('hot.tpl.php');
