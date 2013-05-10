{"friend": [
<?php

require_once('config.php');
require_once('lib/facebook.php');

// reject if not inside facebook.com canvas
if(!isset($_GET['signed_request']))
  die('<p class="error">Please don\'t load this page directly. Visit https:'.FACEBOOK_CANVAS_URL.'/ to use Online Now.</p>');

// create our application instance
$facebook = new Facebook(array(
  'appId'  => FACEBOOK_APP_ID,
  'secret' => FACEBOOK_SECRET,
  'sharedSession' => true
));

// run fql query
$result = $facebook->api(array(
  'method' => 'fql.query',
  'query' => "SELECT uid, name, pic_square, online_presence, username FROM user WHERE (uid IN (SELECT uid2 FROM friend WHERE uid1 = me()) AND (online_presence=\"active\" OR online_presence=\"idle\")) ORDER BY first_name"
));

for ($i = 0; $i < count($result); $i++) {
  if($result[$i]['username']) $url = $result[$i]['username'];
  else $url = 'profile.php?id='.$result[$i]['uid'];

  echo '  {"name": "'.$result[$i]['name'].'", "url": "//www.facebook.com/'.$url.'", "pic": "'.$result[$i]['pic_square'].'", "presence": "'.$result[$i]['online_presence'].'"}';

  if($i != (count($result) - 1))
    echo ",";

  echo "\n";
}

?>
]}