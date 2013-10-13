<?php
//error_reporting(0);

function print_contents() {
  require_once('Cache/Lite.php');

  $id = 'neko';
  $options = array(
    'cacheDir' => '/tmp/',
    'lifeTime' => '60'
  );
  $cache = new Cache_Lite($options);

  if ($contents = $cache->get($id)) {
    // Cache hit!
    print "<!-- cached data -->\n";
    print $contents;
  } else {
    require_once('twitteroauth/twitteroauth.php');
    require_once('twitteroauth/config.php');

    $connection = new TwitterOAuth(
      CONSUMER_KEY,
      CONSUMER_SECRET,
      ACCESS_TOKEN,
      ACCESS_TOKEN_SECRET
    );
    $params = array(
      'q' => rawurlencode("#ねこ OR #猫 twitpic OR twipple OR instagr.am -RT"),
      'locale' => 'ja',
      'count' => '100',
      'include_entities' => 'true'
    );
    $json = $connection->OAuthRequest(
      'https://api.twitter.com/1.1/search/tweets.json',
      'GET', $params
    );
    $data = json_decode($json, true);

    $data = gen_list($data);
    $cache->save($data);

    print "<!-- generated data -->\n";
    print $data;
  }
}

function gen_list($data) {
  $urls = "";

  // [TODO] fix too deep nesting...
  if (!empty($data['statuses']) && is_array($data['statuses'])) {
    foreach ($data['statuses'] as $status) {
      if (!empty($status['entities']) && is_array($status['entities'])) {
        foreach ($status['entities']['urls'] as $url) {
          if (!empty($url['expanded_url'])) {
            $urls .= $url['expanded_url'] . "\n";
          }
        }
      }
    }
  }

  $pattern = array(
    '/(https?:)(\/\/[\x21-\x7e]+)/i',
    '/>(\/\/twitpic.com\/)([[:alnum:]\S\$\+\?\.-=_%,:@!#~*\/&]+)<\/a>/',
    '/>(\/\/yfrog.com\/)([[:alnum:]\S\$\+\?\.-=_%,:@!#~*\/&]+)<\/a>/',
    '/>(\/\/p.twipple.jp\/)([[:alnum:]\S\$\+\?\.-=_%,:@!#~*\/&]+)<\/a>/',
    '/>(\/\/instagr.am\/p\/)([[:alnum:]\S\$\+\?\.-=_%,:@!~*&]+)\/#?<\/a>/'
  );

  $replace = array(
    "<li><p><a href=\"$2\" target=\"_blank\">$2</a></p></li>",
    "><img class=\"media img-polaroid\" src=\"//twitpic.com/show/thumb/$2\"></a>",    // Twitpic
    "><img class=\"media img-polaroid\" src=\"//yfrog.com/$2.th.jpg\"></a>",          // yfrog
    "><img class=\"media img-polaroid\" src=\"//p.twipple.jp/show/large/$2\"></a>",   // ついっぷるフォト
    "><img class=\"media img-polaroid\" src=\"//instagr.am/p/$2/media/?size=t\"></a>" // instagram
  );

  $list = preg_replace($pattern, $replace, $urls);

  // remove text link
  $pattern = '/<li><p><a .*>(\/\/[\x21-\x7e]+).*<\/li>/i';
  $list = preg_replace($pattern, '<!-- removed -->', $list);

  return $list;
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>nekoViewer</title>
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/bootstrap-responsive.css">
<link rel="stylesheet" href="css/my-style.css">
<link href='//fonts.googleapis.com/css?family=Skranji:700' rel='stylesheet' type='text/css'>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
<script src="js/jquery.preloader.min.js"></script>
<script src="js/func.js"></script>
</head>
<body>
<div class="container">
<h1>nekoViewer</h1>
<hr>
<ul id="gallery" class="clearfix">
<?php print_contents(); ?>
</ul>
<hr>
<p>
<span class="label label-inverse">&copy; 2013 <a href="//twitter.com/tdkn_">tdkn</a></span>
</p>
</div>
</body>
</html>
