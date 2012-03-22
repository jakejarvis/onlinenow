<?php

/**
 * This sample app is provided to kickstart your experience using Facebook's
 * resources for developers.  This sample app provides examples of several
 * key concepts, including authentication, the Graph API, and FQL (Facebook
 * Query Language). Please visit the docs at 'developers.facebook.com/docs'
 * to learn more about the resources available to you
 */

# Provides access to app specific values such as your app id and app secret.
# Defined in 'AppInfo.php'
require_once 'AppInfo.php';

# Stop making excess function calls
$app_id = AppInfo::appID();
$app_url = AppInfo::getUrl();

# Enforce https on production
if (substr($app_url, 0, 8) != 'https://' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1' && $_SERVER['REMOTE_ADDR'] != '::1') {
    header('Location: https://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}

# This provides access to helper functions defined in 'utils.php'
require_once 'utils.php';


/*****************************************************************************
 *
 * The content below provides examples of how to fetch Facebook data using the
 * Graph API and FQL.  It uses the helper functions defined in 'utils.php' to
 * do so.  You should change this section so that it prepares all of the
 * information that you want to display to the user.
 *
 ****************************************************************************/

require_once 'sdk/src/facebook.php';

$facebook = new Facebook(array(
    'appId'  => $app_id,
    'secret' => AppInfo::appSecret(),
));

$user_id = $facebook->getUser();
if ($user_id) {
    try {
        # Fetch the viewer's basic information
        $basic = $facebook->api('/me');
    } catch (FacebookApiException $e) {
        # If the call fails we check if we still have a user. The user will be
        # cleared if the error is because of an invalid accesstoken
        if (!$facebook->getUser()) {
            header('Location: ' . AppInfo::getUrl($_SERVER['REQUEST_URI']));
            exit();
        }
    }

    # This fetches some things that you like . 'limit=*" only returns * values.
    # To see the format of the data you are retrieving, use the "Graph API
    # Explorer" which is at https://developers.facebook.com/tools/explorer/
    $likes = idx($facebook->api('/me/likes?limit=4'), 'data', array());

    # This fetches 4 of your friends.
    $friends = idx($facebook->api('/me/friends?limit=4'), 'data', array());

    # And this returns 16 of your photos.
    $photos = idx($facebook->api('/me/photos?limit=16'), 'data', array());

    # Here is an example of a FQL call that fetches all of your friends that are
    # using this app
    $app_using_friends = $facebook->api(array(
        'method' => 'fql.query',
        'query' => 'SELECT uid, name FROM user WHERE uid IN(SELECT uid2 FROM friend WHERE uid1 = me()) AND is_app_user = 1'
    ));
}

# Fetch the basic info of the app that they are using
$app_info = $facebook->api('/'. AppInfo::appID());
$app_name = he(idx($app_info, 'name', ''));

$he_user_id = he($user_id);

?>
<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes" />

        <title><?= $he_app_name; ?></title>
        <link rel="stylesheet" href="stylesheets/screen.css" media="Screen" type="text/css" />
        <link rel="stylesheet" href="stylesheets/mobile.css" media="handheld, only screen and (max-width: 480px), only screen and (max-device-width: 480px)" type="text/css" />

        <!--[if IEMobile]>
        <link rel="stylesheet" href="mobile.css" media="screen" type="text/css"  />
        <![endif]-->

        <!-- These are Open Graph tags.  They add meta data to your  -->
        <!-- site that facebook uses when your content is shared     -->
        <!-- over facebook.  You should fill these tags in with      -->
        <!-- your data.  To learn more about Open Graph, visit       -->
        <!-- 'https://developers.facebook.com/docs/opengraph/'       -->
        <meta property="og:title" content="<?= $app_name; ?>" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="<?= $app_url; ?>" />
        <meta property="og:image" content="<?= AppInfo::getUrl('/logo.png'); ?>" />
        <meta property="og:site_name" content="<?= $app_name; ?>" />
        <meta property="og:description" content="My first app" />
        <meta property="fb:app_id" content="<?= $app_id; ?>" />

        <script type="text/javascript" src="/javascript/jquery-1.7.1.min.js"></script>

        <script type="text/javascript">
        function log(response) {
            if (console && console.log) {
                console.log('The response was', response);
            }
        }

        $(function(){
            // Set up so we handle click on the buttons
            $('#postToWall').click(function() {
                FB.ui({
                    method: 'feed',
                    link  : $(this).attr('data-url')
                },
                function (response) {
                    // If response is null the user canceled the dialog
                    if (response != null) {
                        logResponse(response);
                    }
                });
            });

            $('#sendToFriends').click(function() {
                FB.ui({
                    method: 'send',
                    link  : $(this).attr('data-url')
                },
                function (response) {
                    // If response is null the user canceled the dialog
                    if (response != null) {
                        log(response);
                    }
                });
            });

            $('#sendRequest').click(function() {
                FB.ui({
                    method : 'apprequests',
                    message: $(this).attr('data-message')
                },
                function (response) {
                    // If response is null the user canceled the dialog
                    if (response != null) {
                        logResponse(response);
                    }
                });
            });
        });
        </script>
        <!--[if IE]>
        <script type="text/javascript">
        var tags = ['header', 'section'];
        while(tags.length) {
            document.createElement(tags.pop());
        }
        </script>
        <![endif]-->
    </head>
    <body>
        <div id="fb-root"></div>
        <script type="text/javascript">
        window.fbAsyncInit = function() {
            FB.init({
                appId     : '<?= $app_id; ?>', // App ID
                channelUrl: '//<?= $_SERVER["HTTP_HOST"]; ?>/channel.html', // Channel File
                status    : true, // check login status
                cookie    : true, // enable cookies to allow the server to access the session
                xfbml     : true // parse XFBML
            });

            // Listen to the auth.login which will be called when the user logs in
            // using the Login button
            FB.Event.subscribe('auth.login', function(response) {
                // We want to reload the page now so PHP can read the cookie that the
                // Javascript SDK sat. But we don't want to use
                // window.location.reload() because if this is in a canvas there was a
                // post made to this page and a reload will trigger a message to the
                // user asking if they want to send data again.
                window.location = window.location;
            });

            FB.Canvas.setAutoGrow();
        };

        // Load the SDK Asynchronously
        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/en_US/all.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
        </script>

        <header class="clearfix">
            <?php if (isset($basic)) { ?>
            <p id="picture" style="background-image: url(https://graph.facebook.com/<?= $he_user_id; ?>/picture?type=normal)"></p>

            <div>
                <h1>Welcome, <strong><?= he(idx($basic, 'name')); ?></strong></h1>
                <p class="tagline">
                    This is your app
                    <a href="<?= he(idx($app_info, 'link'));?>" target="_top"><?= $app_name; ?></a>
                </p>

                <div id="share-app">
                    <p>Share your app:</p>
                    <ul>
                        <li>
                            <a href="#" class="facebook-button" id="postToWall" data-url="<?= $app_url; ?>">
                                <span class="plus">Post to Wall</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="facebook-button speech-bubble" id="sendToFriends" data-url="<?= $app_url; ?>">
                                <span class="speech-bubble">Send Message</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="facebook-button apprequests" id="sendRequest" data-message="Test this awesome app">
                                <span class="apprequests">Send Requests</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <?php } else { ?>
            <div>
                <h1>Welcome</h1>
                <div class="fb-login-button" data-scope="user_likes,user_photos"></div>
            </div>
            <?php } ?>
        </header>

        <section id="get-started">
            <p>Welcome to your Facebook app, running on <span>PHP Fog</span>!</p>
        </section>

        <?php if ($user_id) { ?>

        <section id="samples" class="clearfix">
            <h1>Examples of the Facebook Graph API</h1>

            <div class="list">
                <h3>A few of your friends</h3>
                <ul class="friends">
                    <?php
                    foreach ($friends as $friend) {
                        # Extract the pieces of info we need from the requests above
                        $id = he(idx($friend, 'id'));
                        $name = he(idx($friend, 'name'));
                    ?>
                    <li>
                        <a href="https://www.facebook.com/<?= $id; ?>" target="_top">
                            <img src="https://graph.facebook.com/<?= $id ?>/picture?type=square" alt="<?= $name; ?>">
                            <?= $name; ?>
                        </a>
                    </li>
                    <?php
                    }
                    ?>
                </ul>
            </div>

            <div class="list inline">
                <h3>Recent photos</h3>
                <ul class="photos">
                    <?php
                    $i = 0;
                    foreach ($photos as $photo) {
                        # Extract the pieces of info we need from the requests above
                        $id = idx($photo, 'id');
                        $picture = he(idx($photo, 'picture'));
                        $link = he(idx($photo, 'link'));
                        $class = ($i++ % 4 === 0) ? 'first-column' : '';
                    ?>
                    <li style="background-image: url(<?= $picture; ?>);" class="<?= $class; ?>">
                        <a href="<?= $link; ?>" target="_top"></a>
                    </li>
                    <?php
                    }
                    ?>
                </ul>
            </div>

            <div class="list">
                <h3>Things you like</h3>
                <ul class="things">
                    <?php
                    foreach ($likes as $like) {
                        # Extract the pieces of info we need from the requests above
                        $id = he(idx($like, 'id'));
                        $item = he(idx($like, 'name'));

                        # This display's the object that the user liked as a link to
                        # that object's page.
                    ?>
                    <li>
                        <a href="https://www.facebook.com/<?= $id; ?>" target="_top">
                            <img src="https://graph.facebook.com/<?= $id ?>/picture?type=square" alt="<?= $item; ?>">
                            <?= $item; ?>
                        </a>
                    </li>
                    <?php
                    }
                    ?>
                </ul>
            </div>

            <div class="list">
                <h3>Friends using this app</h3>
                <ul class="friends">
                    <?php
                    foreach ($app_using_friends as $auf) {
                        # Extract the pieces of info we need from the requests above
                        $id = he(idx($auf, 'uid'));
                        $name = he(idx($auf, 'name'));
                    ?>
                    <li>
                        <a href="https://www.facebook.com/<?= $id; ?>" target="_top">
                            <img src="https://graph.facebook.com/<?= $id ?>/picture?type=square" alt="<?= $name; ?>">
                            <?= $name; ?>
                        </a>
                    </li>
                    <?php
                    }
                    ?>
                </ul>
            </div>
        </section>

        <?php
        }
        ?>

        <section id="guides" class="clearfix">
            <h1>Learn More About PHP Fog &amp; Facebook Apps</h1>
            <ul>
                <li>
                    <a href="https://developers.facebook.com/docs/guides/web/" target="_top" class="icon websites">Websites</a>
                    <p>Drive growth and engagement on your site with Facebook Login and Social Plugins.</p>
                </li>
                <li>
                    <a href="https://developers.facebook.com/docs/guides/mobile/" target="_top" class="icon mobile-apps">Mobile Apps</a>
                    <p>Integrate with our core experience by building apps that operate within Facebook.</p>
                </li>
                <li>
                    <a href="https://developers.facebook.com/docs/guides/canvas/" target="_top" class="icon apps-on-facebook">Apps on Facebook</a>
                    <p>Let users find and connect to their friends in mobile apps and games.</p>
                </li>
            </ul>
        </section>
    </body>
</html>
