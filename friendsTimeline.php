<?php

require('../../../wp-blog-header.php');

$options = get_option('tweetfeed_widget');

$oAuth_consumerKey = attribute_escape($options['oAuth_consumerKey']);
$oAuth_consumerSecret = attribute_escape($options['oAuth_consumerSecret']);
$oAuth_consumerToken = attribute_escape($options['oAuth_consumerToken']);
$oAuth_consumerTokenSecret = attribute_escape($options['oAuth_consumerTokenSecret']);

$tweetfeed_rSeed = attribute_escape($options['tweetfeed_rSeed']);		
if (!intval($tweetfeed_rSeed)) $tweetfeed_rSeed = 0;

$tweetfeed_count = attribute_escape($options['tweetfeed_count']);		
if (!intval($tweetfeed_count)) $tweetfeed_count = 3;

include('lib/twitteroauth/twitteroauth.php');

// create the oauth single token connect
$connection = new TwitterOAuth($oAuth_consumerKey, $oAuth_consumerSecret,$oAuth_consumerToken, $oAuth_consumerTokenSecret);

// request the friends timeline
$params = array();
$params["count"] = max($tweetfeed_count, $tweetfeed_rSeed);
$params["include_rts"] = "true";

$content = $connection->get("statuses/friends_timeline", $params);

// randomize if needed
if ($tweetfeed_rSeed > 0 && $tweetfeed_rSeed > $tweetfeed_count) {

	$checkArray = array();
	$newContent = array();
	while(count($checkArray) < $tweetfeed_count) {
		$newIndex = rand(0, $tweetfeed_rSeed - 1);
		if (!in_array($newIndex, $checkArray)) {
			array_push($checkArray, $newIndex);
			array_push($newContent, $content[$newIndex]);
		}
	}
	
	if (!empty($newContent)) $content = $newContent;
}


// respond

header('Cache-Control: no-cache, must-revalidate');
header('Content-type: text/javascript');

echo $_GET['callback']."(";
print json_encode($content);
echo ");";

?>