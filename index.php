<?php
  require 'lib/facebook.php';
  
  // get FB credentials from PHPFog so the source is safe to share!
  $app_id = getenv('FACEBOOK_APP_ID');
  $app_secret = getenv('FACEBOOK_SECRET');
  
  $canvas_url = "//".getenv('FACEBOOK_CANVAS_URL');
  $static_url = "//".getenv('STATIC_ASSETS_URL');
  $callback_url = "//".getenv('FACEBOOK_CALLBACK_URL');
  
  // lifestreet ad code
  function ad() {
    return '<div class="ad"><iframe width="728" height="90" frameborder="no" framespacing="0" scrolling="no" src="//ads.lfstmedia.com/slot/slot25079?ad_size=728x90&amp;adkey=21b"></iframe></div>'."\n";
  }
  
  // reject if not inside facebook.com canvas
  if(!isset($_POST['signed_request']))
    die("Please don't load this page directly. <a href=\"".$canvas_url."/\">Click here</a> to use Online Now.");
  
  // create our application instance
  $facebook = new Facebook(array(
    'appId'  => $app_id,
    'secret' => $app_secret,
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Online Now</title>
  <link rel="stylesheet" href="<?php echo $static_url ?>/css/style.css">
  <link rel="icon" href="<?php echo $static_url ?>/img/favicon.ico">
</head>
<body>
  <div id="fb-root"></div>
  <script src="//connect.facebook.net/en_US/all.js"></script>
  <script>
    FB.init({
      appId      : '<?php echo $app_id ?>',
      channelUrl : '<?php echo $callback_url ?>/channel.php',
      xfbml      : false,
    });
    FB.Canvas.setAutoGrow(1000);
  </script>
  <?php echo ad() ?>
  <div id="wrapper">
    <div id="header">
      <a href="<?php echo $canvas_url ?>/" target="_top"><h2>Online Now</h2></a>
      <iframe scrolling="no" src="https://www.facebook.com/plugins/subscribe.php?api_key=200473816679120&amp;colorscheme=light&amp;href=https%3A%2F%2Fwww.facebook.com%2Fjakejarvis&amp;layout=standard&amp;locale=en_US&amp;show_faces=false&amp;width=400"></iframe>
    </div>
<?php
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
      echo "    ".ad();
    }

    echo "    ".'<a class="person" href="//www.facebook.com/';

    if($result[$i]['username']) echo $result[$i]['username'];
    else echo 'profile.php?id='.$result[$i]['uid'];

    echo '" target="_top">
      <img src="'.$result[$i]['pic_square'].'" class="profile_pic" alt="'.$result[$i]['name'].'" title="'.$result[$i]['name'].'">
      <img src="'.$static_url.'/img/'.$result[$i]['online_presence'].'.png" class="status" alt="'.$result[$i]['online_presence'].'" title="'.$result[$i]['online_presence'].'">
      <span class="name">'.$result[$i]['name'].'</span>
    </a>'."\n";
  }
?>  </div>
  <?php echo ad() ?>
  <script>
    var _gaq = [['_setAccount', 'UA-1563964-18'], ['_setDomainName', 'none'], ['_setAllowLinker', true], ['_trackPageview']];
    (function() {
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
    var _gauges = _gauges || [];
    (function() {
      var t   = document.createElement('script');
      t.type  = 'text/javascript';
      t.async = true;
      t.id    = 'gauges-tracker';
      t.setAttribute('data-site-id', '4fd5510cf5a1f50c7b000052');
      t.src = '//secure.gaug.es/track.js';
      var s = document.getElementsByTagName('script')[0];
      s.parentNode.insertBefore(t, s);
    })();
  </script>
</body>
</html>