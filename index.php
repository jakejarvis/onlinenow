<?php

// Enforce https on production
if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == "http" && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
  header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
  exit();
}

/**
 * This sample app is provided to kickstart your experience using Facebook's
 * resources for developers.  This sample app provides examples of several
 * key concepts, including authentication, the Graph API, and FQL (Facebook
 * Query Language). Please visit the docs at 'developers.facebook.com/docs'
 * to learn more about the resources available to you
 */

// Provides access to Facebook specific utilities defined in 'fb_utils.php'
require_once('fb_utils.php');
// Provides access to app specific values such as your app id and app secret.
// Defined in 'config.php'
require_once('config.php');
// This provides access to helper functions defined in 'utils.php'
require_once('utils.php');

/*****************************************************************************
 *
 * The content below provides examples of how to fetch Facebook data using the
 * Graph API and FQL.  It uses the helper functions defined in 'utils.php' to
 * do so.  You should change this section so that it prepares all of the
 * information that you want to display to the user.
 *
 ****************************************************************************/

// Log the user in, and get their access token
$token = FBUtils::login(AppInfo::getHome());
if ($token) {

  // Fetch the viewer's basic information, using the token just provided
  $basic = FBUtils::fetchFromFBGraph("me?access_token=$token");
  $my_id = assertNumeric(idx($basic, 'id'));

  // Fetch the basic info of the app that they are using
  $app_id = AppInfo::appID();
  $app_info = FBUtils::fetchFromFBGraph("$app_id?access_token=$token");

  // This fetches some things that you like . 'limit=*" only returns * values.
  // To see the format of the data you are retrieving, use the "Graph API
  // Explorer" which is at https://developers.facebook.com/tools/explorer/
  $likes = array_values(
    idx(FBUtils::fetchFromFBGraph("me/likes?access_token=$token&limit=4"), 'data', null, false)
  );

  // This fetches 4 of your friends.
  $friends = array_values(
    idx(FBUtils::fetchFromFBGraph("me/friends?access_token=$token&limit=4"), 'data', null, false)
  );

  // And this returns 16 of your photos.
  $photos = array_values(
    idx($raw = FBUtils::fetchFromFBGraph("me/photos?access_token=$token&limit=4"), 'data', null, false)
  );

  // Here is an example of a FQL call that fetches all of your friends that are
  // using this app
  $app_using_friends = FBUtils::fql(
    "SELECT uid, name, is_app_user, pic_square FROM user WHERE uid in (SELECT uid2 FROM friend WHERE uid1 = me()) AND is_app_user = 1",
    $token
  );

  // This formats our home URL so that we can pass it as a web request
  $encoded_home = urlencode(AppInfo::getHome());
  $redirect_url = $encoded_home . 'close.php';

  // These two URL's are links to dialogs that you will be able to use to share
  // your app with others.  Look under the documentation for dialogs at
  // developers.facebook.com for more information
  $send_url = "https://www.facebook.com/dialog/send?redirect_uri=$redirect_url&display=popup&app_id=$app_id&link=$encoded_home";
  $post_to_wall_url = "https://www.facebook.com/dialog/feed?redirect_uri=$redirect_url&display=popup&app_id=$app_id";
} else {
  // Stop running if we did not get a valid response from logging in
  exit("Invalid credentials");
}
?>

<!-- This following code is responsible for rendering the HTML   -->
<!-- content on the page.  Here we use the information generated -->
<!-- in the above requests to display content that is personal   -->
<!-- to whomever views the page.  You would rewrite this content -->
<!-- with your own HTML content.  Be sure that you sanitize any  -->
<!-- content that you will be displaying to the user.  idx() by  -->
<!-- default will remove any html tags from the value being      -->
<!-- and echoEntity() will echo the sanitized content.  Both of  -->
<!-- these functions are located and documented in 'utils.php'.  -->
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?php echo(idx($app_info, 'name')) ?></title>
    <link rel="stylesheet" href="stylesheets/reset.css">
    <link rel="stylesheet" href="stylesheets/base.css">

    <!-- These are Open Graph tags.  They add meta data to your  -->
    <!-- site that facebook uses when your content is shared     -->
    <!-- over facebook.  You should fill these tags in with      -->
    <!-- your data.  To learn more about Open Graph, visit       -->
    <!-- 'https://developers.facebook.com/docs/opengraph/'       -->
    <meta property="og:title" content=""/>
    <meta property="og:type" content=""/>
    <meta property="og:url" content=""/>
    <meta property="og:image" content=""/>
    <meta property="og:site_name" content=""/>
    <?php echo('<meta property="fb:app_id" content="' . AppInfo::appID() . '" />'); ?>

  </head>
  <body>
    <div id="wrapper">
      <div id="main">
        <section id="header">
          <h1>Welcome to <a href="<?php echo(idx($app_info, 'link'));?>"><?php echo(idx($app_info, 'name')); ?></a>!</h1>
          <img src="images/devices.png" id="devices" alt="app on multiple devices">
          <!-- By passing a valid access token here, we are able to display -->
          <!-- the user's images without having to download or prepare -->
          <!-- them ahead of time -->
          <p id="picture" style="background-image: url(https://graph.facebook.com/me/picture?type=normal&access_token=<?php echoEntity($token) ?>)"></p>
          <h3><strong><?php echo idx($basic, 'name'); ?></strong>, your very own Facebook app is now running on AppFog!</h3>
        </section>

        <section id="build">
          <h2>Build</h2>
          <ul>
            <li>
              <a href="https://developers.facebook.com/docs/guides/web/"><img src="images/icon-for-websites.jpg" alt="build for websites" class="icon"></a>
              <h4><a href="https://developers.facebook.com/docs/guides/web/">For Websites</a></h4>
              <p>Drive growth and engagement on your site through Facebook Login and Social Plugins.</p>
            </li>
            <li>
              <a href="https://developers.facebook.com/docs/guides/mobile/"><img src="images/icon-for-mobile.jpg" alt="build for mobile" class="icon"></a>
              <h4><a href="https://developers.facebook.com/docs/guides/mobile/">For Mobile</a></h4>
              <p>Let users find and connect to their friends in mobile apps and games.</p>
            </li>
            <li>
              <a href="https://developers.facebook.com/docs/guides/canvas/"><img src="images/icon-apps-on-fb.jpg" alt="build apps on facebook" class="icon"></a>
              <h4><a href="https://developers.facebook.com/docs/guides/canvas/">Apps on Facebook</a></h4>
              <p>Integrate with our core experience by building apps that operate within Facebook.</p>
            </li>
          </ul>
        </section>
        <section id="learn">
          <h2>Learn</h2>
          <ul>
            <li>
              <a href="https://developers.facebook.com/docs/opengraph/"><img src="images/icon-hack-graph.jpg" alt="learn to hack the graph" class="icon"></a>
              <h4><a href="https://developers.facebook.com/docs/opengraph/">Hack the Graph</a></h4>
              <p>Build with the Open Graph. Integrate deeply into the Facebook experience. Grow lasting connections with your users.</p>
            </li>
            <li>
              <a href="https://developers.facebook.com/html5/"><img src="images/icon-html5.jpg" alt="learn html5" class="icon"></a>
              <h4><a href="https://developers.facebook.com/html5/">HTML5</a></h4>
              <p>With animation, offline capabilities, audio and more, HTML5 yields a new class of web standards enabling developers to build amazing products.</p>
            </li>
            <li>
              <a href="https://developers.facebook.com/blog/post/541/"><img src="images/icon-social-design.jpg" alt="learn social design" class="icon"></a>
              <h4><a href="https://developers.facebook.com/blog/post/541/">Social Design</a></h4>
              <p>Social Design is a way of thinking about product design that puts social experiences at the core. </p>
            </li>
          </ul>
        </section>
        <div class="clearfix"></div>
        <section id="examples">
          <h2>Example Facebook APIs</h2>

          <div id="friends-list">
            <h3>Your friends</h3>
            <ul class="friends">
              <?php
                foreach ($friends as $friend) {
                  // Extract the pieces of info we need from the requests above
                  $id = assertNumeric(idx($friend, 'id'));
                  $name = idx($friend, 'name');
                  // Here we link each friend we display to their profile
                  echo('
                    <li>
                      <a href="#" onclick="window.open(\'http://www.facebook.com/' . $id . '\')">
                        <img src="https://graph.facebook.com/' . $id . '/picture?type=square" alt="' . $name . '">'
                        . $name . '
                      </a>
                    </li>');
                }
              ?>
            </ul>
          </div>

          <div id="liked-list">
            <h3>Your likes</h3>
            <ul class="things">
              <?php
                foreach ($likes as $like) {
                  // Extract the pieces of info we need from the requests above
                  $id = assertNumeric(idx($like, 'id'));
                  $item = idx($like, 'name');
                  // This display's the object that the user liked as a link to
                  // that object's page.
                  echo('
                    <li>
                      <a href="#" onclick="window.open(\'http://www.facebook.com/' .$id .'\')">
                        <img src="https://graph.facebook.com/' . $id . '/picture?type=square" alt="' . $item . '">
                        ' . $item . '
                      </a>
                    </li>');
                }
              ?>
            </ul>
          </div>

          <div id="photos-list">
            <h3>Your photos</h3>
            <ul class="photos">
              <?php
                foreach ($photos as $key => $photo) {
                  // Extract the pieces of info we need from the requests above
                  $src = idx($photo, 'source');
                  $name = idx($photo, 'name');
                  $id = assertNumeric(idx($photo, 'id'));

                  // Here we link each photo we display to it's location on Facebook
                  echo('
                    <li>
                      <a href="#" onclick="window.open(\'http://www.facebook.com/' .$id . '\')">
                        <img src="' . $src . ')">
                        ' . $name . '
                      </a>
                    </li>'
                  );
                }
              ?>
            </ul>
          </div>
          <div class="clearfix"></div>
        </section>
        <section id="samples">
          <h3>Samples &amp; How-Tos</h3>
          <ul>
            <li>
              <a href="https://developers.facebook.com/docs/samples/"><img src="images/winefriends.jpg" alt="wine friends"></a>
              <h4><a href="https://developers.facebook.com/docs/samples/">winefriends</a></h4>
              <p>This sample outlines how to build a social web site with the following features: Like Button, Activity Feed, Recommendations, Comments Box and Open Graph Protocol.</p>
            </li>
            <li>
              <a href="https://developers.facebook.com/docs/samples/canvas/"><img src="images/run-with-friends.jpg" alt="run with friends"></a>
              <h4><a href="https://developers.facebook.com/docs/samples/canvas/">Run with Friends</a></h4>
              <p>This sample outlines how to build a Facebook Canvas Application with the following features: JavaScript SDK, Social Plugin (Login with Faces), Platform Dialogs, Graph API using OAuth 2.0 and Real-time Updates.</p>
            </li>
            <li>
              <a href="https://developers.facebook.com/docs/samples/"><img src="images/exports-data.jpg" alt="export insights data"></a>
              <h4><a href="https://developers.facebook.com/docs/samples/">Export Insights Data</a></h4>
              <p>This sample demonstrates how to export data about your Facebook Apps and Pages.</p>
            </li>
          </ul>
          <div class="clearfix"></div>
        </section>
      </div> <!-- end of main -->
      <footer>
        <a href="http://www.appfog.com" id="running-appfog">
          <img src="images/running-appfog.png">
        </a>
      </footer>
    </div> <!-- end of wrapper-->

  </body>
</html>
