<?php

require_once('config.php');
require_once('lib/facebook.php');

// reject if not inside facebook.com canvas
if(!isset($_POST['signed_request']))
  die("Please don't load this page directly. <a href=\"".FACEBOOK_CANVAS_URL."/\">Click here</a> to use Online Now.");

// create our application instance
$facebook = new Facebook(array(
  'appId'  => FACEBOOK_APP_ID,
  'secret' => FACEBOOK_SECRET,
  'cookie' => false,
));

// check if logged in
if(!$facebook->getUser())
  die("You need to be logged in to do that.");

// run fql query
$result = $facebook->api(array(
  'method' => 'fql.query',
  'query' => "SELECT uid, name, pic_square, online_presence, username FROM user WHERE (uid IN (SELECT uid2 FROM friend WHERE uid1 = me()) AND (online_presence=\"active\" OR online_presence=\"idle\")) ORDER BY first_name"
));

// count friends (moved up to calculate height)
$total = count($result);

// display the number of users
echo "    ".'<div id="count">You have ';
if ($total > 0)
  echo $total;
else
  echo "no";
echo ' friend';
if($total == 0 || $total > 1)
  echo 's';
echo ' online.</div>'."\n";

for ($i = 0; $i < $total; $i++) {
  if($result[$i]['online_presence'] == "offline" || !$result[$i]['online_presence'])
    $result[$i]['online_presence'] = "idle";

  // crazy ad display algorithm... only display middle ads if more than 5 friends are online. if less than 20 only display one, if greater than 20 display 2
  if( ( $total > 5 ) && ( $total < 20 && floor($total/2) == $i ) || ( $total >= 20 && floor($total/3) == $i ) || ( $total >= 20 && floor($total/1.5) == $i ) ) {
    echo "      ".LIFESTREET_AD_CODE;
  }

  echo "      ".'<a href="//www.facebook.com/';

  if($result[$i]['username']) echo $result[$i]['username'];
  else echo 'profile.php?id='.$result[$i]['uid'];

  echo '" target="_top">
      <img src="'.$result[$i]['pic_square'].'" class="pic" alt="'.$result[$i]['name'].'">
      <img src="'.STATIC_ASSETS_URL.'/img/'.$result[$i]['online_presence'].'.png" class="status" alt="'.$result[$i]['online_presence'].'">
      <span class="name">'.$result[$i]['name'].'</span>
    </a>'."\n";
}

?>