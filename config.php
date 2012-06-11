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

define('GOOGLE_ANALYTICS_ACCOUNT', 'UA-1563964-18');
define('GAUGES_SITE_ID',           '4fd5510cf5a1f50c7b000052');

define('LIFESTREET_AD_CODE', '<div class="ad"><iframe src="//ads.lfstmedia.com/slot/slot25079?ad_size=728x90&amp;adkey=21b" scrolling="no"></iframe></div>' . "\n");

?>