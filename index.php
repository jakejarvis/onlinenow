<?php require_once('config.php') ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Online Now</title>
  <script>var _sf_startpt=(new Date()).getTime()</script>
  <script src="//ads.lfstmedia.com/getad?site=81006"></script>
  <link rel="stylesheet" href="<?php echo STATIC_ASSETS_URL ?>/css/style.css">
  <link rel="icon" href="<?php echo STATIC_ASSETS_URL ?>/img/favicon.ico">
</head>
<body>
  <div id="fb-root"></div>
  <?php echo LIFESTREET_AD_CODE ?>
  <p class="info"><strong>Did you know that Online Now refreshes automatically now?</strong> Wait a minute and see for yourself!</p>
  <div id="wrapper">
    <div id="header">
      <a href="<?php echo FACEBOOK_CANVAS_URL ?>/" target="_top"><h2>Online Now</h2></a>
      <?php
        $random = mt_rand(0, 1);
        if($random == 0) {
          // facebook
          echo '<iframe src="//www.facebook.com/plugins/subscribe.php?api_key='.FACEBOOK_APP_ID.'&amp;href=https%3A%2F%2Fwww.facebook.com%2Fjakejarvis&amp;layout=standard&amp;colorscheme=light&amp;show_faces=false" scrolling="no"></iframe>';
        } else if($random == 1) {
          echo '<a href="https://twitter.com/jakejarvis" class="twitter-follow-button" data-show-count="true"></a>
      <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
        }
        echo "\n";
      ?>
    </div>
    <div id="friends">
<?php include('friends.php') ?>    </div>
  </div>
  <?php echo LIFESTREET_AD_CODE ?>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
  <script>window.jQuery || document.write(unescape('%3Cscript src="<?php echo STATIC_ASSETS_URL ?>/js/jquery.min.js"%3E%3C/script%3E'))</script>
  <script src="//connect.facebook.net/en_US/all.js"></script>
  <script>
    /* Facebook JS */
    FB.init({
      appId      : '<?php echo FACEBOOK_APP_ID ?>',
      channelUrl : '<?php echo FACEBOOK_CALLBACK_URL ?>/channel.php',
      status     : false,
      cookie     : false,
      xfbml      : false,
    });
    /* AJAX load friends */
    $(document).ready(function() {
      FB.Canvas.setSize();
    });
    setInterval(function() {
      $('#friends').load('friends.php', {'signed_request':'<?php echo $_POST['signed_request'] ?>'}, function() {
        $(this).css({opacity: 0});
        $(this).animate({opacity: 100}, 1000);
        FB.Canvas.setSize();
      });
    }, 60000);
    /* gaug.es */
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
    /* Chartbeat */
    var _sf_async_config = { uid: 38128, domain: '<?php echo substr(FACEBOOK_CALLBACK_URL, 2) ?>', useCanonical: true };
    (function() {
      function loadChartbeat() {
        window._sf_endpt = (new Date()).getTime();
        var e = document.createElement('script');
        e.setAttribute('language', 'javascript');
        e.setAttribute('type', 'text/javascript');
        e.setAttribute('src','//static.chartbeat.com/js/chartbeat.js');
        document.body.appendChild(e);
      };
      var oldonload = window.onload;
      window.onload = (typeof window.onload != 'function') ?
        loadChartbeat : function() { oldonload(); loadChartbeat(); };
    })();
    /* Google Analytics */
    var _gaq = [['_setAccount', '<?php echo GOOGLE_ANALYTICS_ACCOUNT ?>'], ['_setDomainName', 'none'], ['_setAllowLinker', true], ['_trackPageview']];
    (function() {
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
  </script>
</body>
</html>