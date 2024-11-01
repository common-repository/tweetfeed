<?php

$_url = $_GET['url'];
//$_url = "http://snipurl.com/28lj8";
//echo $_url;

if ($_url) {
	$doc = new DOMDocument;
	@$doc->loadHTMLFile($_url);

	if ($doc) {
	    if ($doc->getElementById('tweet_img')) {
	        $imageLink = $doc->getElementById('tweet_img')->getAttribute('src');
	    }
	    
	    $imgsFromContentDiv = $doc->getElementById('content')->getElementsByTagName('img');
	    if ($imgsFromContentDiv) {
		foreach ($imgsFromContentDiv as $image) {
		    $imageLink = $image->getAttribute('src');
		}
	    }
	
	    if ($imageLink) {
		echo "<a href='".$_url."' target='_blank'><img src='".$imageLink."' style='width: 30px; border: 1px solid #848484; padding: 1px;' alt='Bild-Link'></a>";
	    } else {
		echo "<a href='".$_url."' target='_blank'>[Bild]</a>";
	    }
	}

} else {
	die("Fehler");
}

?>