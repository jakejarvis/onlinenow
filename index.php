<?php require_once('config.php') ?>
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
<?php include('friends.php') ?>    </div>
  </div>
  <?php echo LIFESTREET_AD_CODE ?>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
  <script>window.jQuery || document.write(unescape('%3Cscript src="<?php echo STATIC_ASSETS_URL ?>/js/jquery.min.js"%3E%3C/script%3E'))</script>
  <script>
    setInterval(function() {
      $('#friends').load('friends.php').fadeIn('fast');
    }, 10000);
  </script>
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