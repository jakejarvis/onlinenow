<?php
  
  /***********************
  *    CONFIGURATION
  ************************/
  
  // get FB credentials from PHPFog so the source is safe to share!
  define('FACEBOOK_APP_ID', getenv('FACEBOOK_APP_ID'));
  define('FACEBOOK_SECRET', getenv('FACEBOOK_SECRET'));
  
  define('FACEBOOK_CANVAS_URL',   "//" . getenv('FACEBOOK_CANVAS_URL'));
  define('FACEBOOK_CALLBACK_URL', "//" . getenv('FACEBOOK_CALLBACK_URL'));
  define('STATIC_ASSETS_URL',     "//" . getenv('STATIC_ASSETS_URL'));
  
  define('GAUGES_SITE_ID',           '4fd5510cf5a1f50c7b000052');
  define('GOOGLE_ANALYTICS_ACCOUNT', 'UA-1563964-18');
  
  define('LIFESTREET_AD_CODE', '<div class="ad"><iframe src="//ads.lfstmedia.com/slot/slot25079?ad_size=728x90&amp;adkey=21b" scrolling="no"></iframe></div>' . "\n");
  
  
  
  
  require 'lib/facebook.php';
  
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Online Now</title>
  <link rel="stylesheet" href="<?php echo STATIC_ASSETS_URL ?>/css/style.css">
  <link rel="icon" href="<?php echo STATIC_ASSETS_URL ?>/img/favicon.ico">
</head>
<body>
  <div id="fb-root"></div>
  <?php echo LIFESTREET_AD_CODE ?>
  <div id="wrapper">
    <div id="header">
      <a href="<?php echo FACEBOOK_CANVAS_URL ?>/" target="_top"><h2>Online Now</h2></a>
      <iframe src="//www.facebook.com/plugins/subscribe.php?api_key=<?php echo FACEBOOK_APP_ID ?>&amp;href=https%3A%2F%2Fwww.facebook.com%2Fjakejarvis&amp;layout=standard&amp;colorscheme=light&amp;show_faces=false" scrolling="no"></iframe>
    </div>
    <div id="friends">
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
?>    </div>
  </div>
  <?php echo LIFESTREET_AD_CODE ?>
  <script src="//connect.facebook.net/en_US/all.js"></script>
  <script>
    FB.init({
      appId      : '<?php echo FACEBOOK_APP_ID ?>',
      channelUrl : '<?php echo FACEBOOK_CALLBACK_URL ?>/channel.php',
      status     : false,
      cookie     : false,
      xfbml      : false,
    });
    FB.Canvas.setAutoGrow(1000);
  </script>
  <script>
    var _gauges = _gauges || [];
    (function() {
      var t   = document.createElement('script');
      t.type  = 'text/javascript';
      t.async = true;
      t.id    = 'gauges-tracker';
      t.setAttribute('data-site-id', '<?php echo GAUGES_SITE_ID ?>');
      t.src = '//secure.gaug.es/track.js';
      var s = document.getElementsByTagName('script')[0];
      s.parentNode.insertBefore(t, s);
    })();
  </script>
  <script>
    var _gaq = [['_setAccount', '<?php echo GOOGLE_ANALYTICS_ACCOUNT ?>'], ['_setDomainName', 'none'], ['_setAllowLinker', true], ['_trackPageview']];
    (function() {
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
  </script>
</body>
</html>