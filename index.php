<?php
	require 'facebook.php';
	
	if($_SERVER['HTTP_X_FORWARDED_PORT'] == 443 || $_SERVER['SERVER_PORT'] == 443)
		$protocol = "https:";
	else
		$protocol = "http:";
	
	// get FB credentials from PHPFog so the source is safe to share!
	$app_id = getenv('FACEBOOK_APP_ID');
	$app_secret = getenv('FACEBOOK_SECRET');
	
	$canvas_url = $protocol."//".getenv('FACEBOOK_CANVAS_URL');
	$static_url = $protocol."//".getenv('STATIC_ASSETS_URL');
	$callback_url = $protocol."//".getenv('FACEBOOK_CALLBACK_URL');
	
	// lifestreet ad code
	function ad($protocol) {
		return '<div class="ad"><iframe width="728" height="90" frameborder="no" framespacing="0" scrolling="no" src="'.$protocol.'//ads.lfstmedia.com/slot/slot25079?ad_size=728x90&amp;adkey=21b"></iframe></div>'."\n";
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Online Now</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="<?php echo $static_url ?>/style.css" />
	<link rel="icon" type="image/x-icon" href="<?php echo $static_url ?>/images/favicon.ico" />
</head>
<body>

<div id="fb-root"></div>
<script src="<?php echo $protocol ?>//connect.facebook.net/en_US/all.js"></script>
<script type="text/javascript">
	FB.init({
		appId      : '<?php echo $app_id ?>',
		channelUrl : '<?php echo $callback_url ?>/channel.php',
		xfbml      : true,
	});
	
	FB.Canvas.setAutoGrow(1000);
</script>

<?php echo ad($protocol) ?>

<div id="wrapper">
	
	<div id="header">
		<a href="<?php echo $canvas_url ?>/" target="_top"><h2>Online Now</h2></a>
		<div class="fb-subscribe" data-href="https://www.facebook.com/jakejarvis" data-show-faces="false" data-width="400"></div>
	</div>

<?php
	// display the number of users
	echo "\t".'<div id="count">You have ';
	if ($total > 0)
		echo $total;
	else
		echo "no";
	echo ' friend';
	if($total == 0 || $total > 1)
		echo 's';
	echo ' online.</div>'."\n\n";

	for ($i = 0; $i < $total; $i++) {
		if($result[$i]['online_presence'] == "offline" || !$result[$i]['online_presence'])
			$result[$i]['online_presence'] = "idle";

		// crazy ad display algorithm... only display middle ads if more than 5 friends are online. if less than 20 only display one, if greater than 20 display 2
		if( ( $total > 5 ) && ( $total < 20 && floor($total/2) == $i ) || ( $total >= 20 && floor($total/3) == $i ) || ( $total >= 20 && floor($total/1.5) == $i ) ) {
			echo "\t".ad($protocol)."\n";
		}

		echo "\t".'<a class="person" href="'.$protocol.'//www.facebook.com/';

		if($result[$i]['username']) echo $result[$i]['username'];
		else echo 'profile.php?id='.$result[$i]['uid'];

		echo '" target="_top">
		<img src="'.$result[$i]['pic_square'].'" class="profile_pic" alt="'.$result[$i]['name'].'" title="'.$result[$i]['name'].'" />
		<img src="'.$static_url.'/images/'.$result[$i]['online_presence'].'.png" class="status" alt="'.$result[$i]['online_presence'].'" title="'.$result[$i]['online_presence'].'" />
		<span class="name">'.$result[$i]['name'].'</span>
	</a>'."\n\n";
	}
?>
</div>

<?php echo ad($protocol) ?>

<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', 'UA-1563964-18']);
	_gaq.push(['_setDomainName', 'none']);
	_gaq.push(['_setAllowLinker', true]);
	_gaq.push(['_trackPageview']);

	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
</script>

<script type="text/javascript">
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