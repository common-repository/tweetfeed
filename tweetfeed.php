<?php
/*
Plugin Name: Tweetfeed Widget
Plugin URI: http://www.bytefrog.de
Description: Yet another Wordpress Twitter sidebar widget. But a good looking one ;-)
Version: 1.3.1
Author: Bjoern Teichmann
Author URI: http://www.bytefrog.de
Min WP Version: 2.9
*/



add_action('plugins_loaded', 'twitter_PL');
function twitter_PL() {
	if (!function_exists('register_sidebar_widget')) return;
	
	register_sidebar_widget('Tweetfeed', 'widget_twitterfeed');	
	register_widget_control( "Tweetfeed", "wp_tweetfeed_widget_control" );
}

add_action('wp_head', 'twitterCall');

function twitterCall() {
	$options = get_option('tweetfeed_widget');
	$tweetfeed_username = attribute_escape($options['tweetfeed_username']);
	$tweetfeed_count = attribute_escape($options['tweetfeed_count']);
	$tweetfeed_timeLineSwitch = attribute_escape($options['tweetfeed_timeLineSwitch']);	
	$tweetfeed_reload = attribute_escape($options['tweetfeed_reload']);


	if (!$tweetfeed_username || $tweetfeed_username == "") return;
	if (!intval($tweetfeed_count)) $tweetfeed_count = 3;
	if (!intval($tweetfeed_reload)) $tweetfeed_reload = 60;
	
	echo '<script src="' . plugins_url('tweetfeed/lib/scriptaculous/prototype.js') . '" type="text/javascript"></script>'."\n";
	echo '<script src="' . plugins_url('tweetfeed/lib/scriptaculous/scriptaculous.js') . '" type="text/javascript"></script>'."\n";

    ?>
    
    <script type="text/javascript">
	<!--
	
	function addLoadEvent(your_function) {
		if (window.attachEvent) {window.attachEvent('onload', your_function);}
		else if (window.addEventListener) {window.addEventListener('load', your_function, false);}
		else {document.addEventListener('load', your_function, false);}
	}
	
	function reloadTweetFeed() {
		if (<?php echo $tweetfeed_reload; ?> > 0) {
			var tfReloadTimer = window.setTimeout("reloadTweetFeed()", <?php echo $tweetfeed_reload; ?> * 1000);
		}
		tweetfeed_load();
		
		return true;
	}
	
	function tweetfeed_load() {
		if ($('tweetFeedReloader')) $('tweetFeedReloader').parentNode.removeChild($('tweetFeedReloader'));
	
	    var script = document.createElement('script');
	    script.type = 'text/javascript';
	    script.id = 'tweetFeedReloader';
	    
	    
	    <?php if ($tweetfeed_timeLineSwitch == "userTimeline") { ?>
//			script.src = "http://api.twitter.com/1/statuses/public_timeline.json?callback=twitterCallback&rand=" + new Date().getTime();
			script.src = "http://api.twitter.com/1/statuses/user_timeline.json?screen_name=<?php echo $tweetfeed_username; ?>&callback=twitterCallback&count=<?php echo $tweetfeed_count; ?>&rand=" + new Date().getTime();
		<?php } else { ?>
		    script.src = "<?php echo plugins_url('tweetfeed/friendsTimeline.php?callback=twitterCallback'); ?>&rand=" + new Date().getTime();
		<?php } ?>
				
	    document.body.appendChild(script);
	    
	    return true;
	}
	
	addLoadEvent(reloadTweetFeed);
	
	-->
    </script>
    <?php
}




/*********** WIDGETS ***********/

function widget_twitterfeed() {

	$options = get_option('tweetfeed_widget');

	$tweetfeed_count = attribute_escape($options['tweetfeed_count']);
	if (!intval($tweetfeed_count)) $tweetfeed_count = 3;

	$tweetfeed_username = attribute_escape($options['tweetfeed_username']);
	$tweetfeed_backgroundColor = attribute_escape($options['tweetfeed_backgroundColor']);
	$tweetfeed_fontColor = attribute_escape($options['tweetfeed_fontColor']);
	$tweetfeed_linkColor = attribute_escape($options['tweetfeed_linkColor']);
	$tweetfeed_timeColor = attribute_escape($options['tweetfeed_timeColor']);
	
	$tweetfeed_timeLineSwitch = attribute_escape($options['tweetfeed_timeLineSwitch']);	
	
	// check if everything is available
	if ($tweetfeed_timeLineSwitch == "userTimeline") {
		if (!$tweetfeed_username || $tweetfeed_username == "") return;
	} else {
		$oAuth_consumerKey = attribute_escape($options['oAuth_consumerKey']);
		if (!$oAuth_consumerKey || $oAuth_consumerKey == "") return;

		$oAuth_consumerSecret = attribute_escape($options['oAuth_consumerSecret']);
		if (!$oAuth_consumerSecret || $oAuth_consumerSecret == "") return;

		$oAuth_consumerToken = attribute_escape($options['oAuth_consumerToken']);
		if (!$oAuth_consumerToken || $oAuth_consumerToken == "") return;

		$oAuth_consumerTokenSecret = attribute_escape($options['oAuth_consumerTokenSecret']);
		if (!$oAuth_consumerTokenSecret || $oAuth_consumerTokenSecret == "") return;
	}
	
	if ($tweetfeed_backgroundColor == "") $tweetfeed_backgroundColor = "#7497B0";
	if ($tweetfeed_fontColor == "") $tweetfeed_fontColor = "white";
	if ($tweetfeed_timeColor == "") $tweetfeed_timeColor = "white";
	if ($tweetfeed_linkColor == "") $tweetfeed_linkColor = $tweetfeed_fontColor;


	echo $before_widget;
	echo $before_title;
	?>
		<script type="application/javascript">
			function showSM(){
				$$('#klappe_inhalt ul').each(function(linkTemp) {
					if (linkTemp.style.display == "none") {
						linkTemp.show();
					}
				});
			}
			function hideSM(){
				$$('#klappe_inhalt ul').each(function(linkTemp) {
					linkTemp.hide();
				});
			}
			function blindOutSM(){
				$$('#klappe_inhalt ul').each(function(linkTemp) {
					linkTemp.hide();
				});
			}

			function relative_time(time_value) {
				var values = time_value.split(" ");
				time_value = values[1] + " " + values[2] + ", " + values[5] + " " + values[3];
				var parsed_date = Date.parse(time_value);
				var relative_to = (arguments.length > 1) ? arguments[1] : new Date();
				var delta = parseInt((relative_to.getTime() - parsed_date) / 1000);
				delta = delta + (relative_to.getTimezoneOffset() * 60);
		
				if (delta < 60) {
					return "<?php _e('less than a minute ago'); ?>";
					
				} else if(delta < 120) {
					return <?php echo '"' . __('a minute ago') . '"' ?>;
					
				} else if(delta < (45*60)) {
					var _text = "<?php _e('%d minutes ago'); ?>";
					var _time = parseInt(delta / 60).toString();
					return _text.replace(/%d/, _time);
					
				} else if(delta < (90*60)) {
					return "<?php _e('an hour ago'); ?>";
					
				} else if(delta < (24*60*60)) {
					var _text = "<?php _e('%d hours ago'); ?>";
					var _time = parseInt(delta / 3600).toString();
					return _text.replace(/%d/, _time);
					
				} else if(delta < (48*60*60)) {
					return "<?php _e('a day ago'); ?>";
					
				} else {
					var _text = "<?php _e('%d days ago'); ?>";
					var _time = parseInt(delta / 86400).toString();
					return _text.replace(/%d/, _time);
					
				}
		  	}
		  	
		  	function randomString() {
				var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
				var string_length = 6;
				var randomstring = '';
				for	 (var i=0; i<string_length; i++) {
					var rnum = Math.floor(Math.random() * chars.length);
					randomstring += chars.substring(rnum,rnum+1);
				}
				return randomstring;
			}
	
			function htmlUnEntity(text) {
				return text.replace(/&amp;/g,'&').replace(/&gt;/g,'>').replace(/&lt;/g,'<').replace(/&quot;/g,'"');
			}
	
			
			function twinkleAtToURL(text) {
				text = text.replace(/@(\w+) /ig,"<a id='twinkleAtURL' target='_blank' style='color: <?php echo $tweetfeed_linkColor; ?>; background-color: <?php echo $tweetfeed_backgroundColor; ?>;' href='http://twitter.com/$1'>@$1</a> ");

				text = text.replace(/(\s)(\w+:\/\/\S+)$/ig,"$1<a id='twinkleAtURL' target='_blank' style='color: <?php echo $tweetfeed_linkColor; ?>; background-color: <?php echo $tweetfeed_backgroundColor; ?>;' href='$2'>$2</a>");
				text = text.replace(/^(\w+:\/\/\S+)(\s)/ig,"<a id='twinkleAtURL' target='_blank' style='color: <?php echo $tweetfeed_linkColor; ?>; background-color: <?php echo $tweetfeed_backgroundColor; ?>;' href='$1'>$1</a>$2");
				text = text.replace(/(\s)(\w+:\/\/\S+)(\s)/ig,"$1<a id='twinkleAtURL' target='_blank' style='color: <?php echo $tweetfeed_linkColor; ?>; background-color: <?php echo $tweetfeed_backgroundColor; ?>;' href='$2'>$2</a>$3");

				text = text.replace(/(\s)(www\.\S+)$/ig,"$1<a id='twinkleAtURL' target='_blank' style='color: <?php echo $tweetfeed_linkColor; ?>; background-color: <?php echo $tweetfeed_backgroundColor; ?>;' href='http://$2'>$2</a>");
				text = text.replace(/^(www\.\S+)(\s)/ig,"<a id='twinkleAtURL' target='_blank' style='color: <?php echo $tweetfeed_linkColor; ?>; background-color: <?php echo $tweetfeed_backgroundColor; ?>;' href='http://$1'>$1</a>$2");
				text = text.replace(/(\s)(www\.\S+)(\s)/ig,"$1<a id='twinkleAtURL' target='_blank' style='color: <?php echo $tweetfeed_linkColor; ?>; background-color: <?php echo $tweetfeed_backgroundColor; ?>;' href='http://$2'>$2</a>$3");

				return text;
			}
  
			function twitterCallback(tobj) {			
				var shouldUpdate = $('twitter_messages').hasChildNodes();
				
				var countMin = Math.min(<?php echo $tweetfeed_count; ?>, tobj.length);
			
				for (var index = 0; index < countMin; ++index) {
					var item = tobj[index];
					
					my_twitter_status = Builder.node('div',{style:'color: <?php echo $tweetfeed_fontColor; ?>; font-weight: bold; line-height: 1.5em; font-size: 9px; padding-left: 5px; word-wrap:break-word;'});
					
					<?php if ($tweetfeed_timeLineSwitch == "userTimeline") { ?>
						my_twitter_status.innerHTML = twinkleAtToURL(item.text);
					<?php } else { ?>
						my_twitter_status.innerHTML = "<a href='http://www.twitter.com/"+item.user.screen_name+"'>" + item.user.screen_name + "</a><br />" + twinkleAtToURL(item.text);
					<?php } ?>

					my_twitter_status_time = Builder.node('div',{style:'padding: 3px 0 0 10px; font-size: 8px;'});
					my_twitter_status_time.innerHTML = "<a target='_blank' href='http://twitter.com/" + item.user.screen_name + "/status/" + item.id + "' style='color: <?php echo $tweetfeed_timeColor; ?>; background-color: <?php echo $tweetfeed_backgroundColor; ?>; font-weight: bold; text-decoration: none;'>" + relative_time(item.created_at) + "</a>";
					
					twitter_badge_inner = Builder.node('div',{style:'padding: 0px; z-index: 1;background-color: <?php echo $tweetfeed_backgroundColor; ?>;'});
					twitter_badge_inner.appendChild(my_twitter_status);
					twitter_badge_inner.appendChild(my_twitter_status_time);

					twitter_badge_container = Builder.node('div', {id:'twitter_badge_'+index,style:'position: relative; z-index: 1; \
						width: 100%; margin: 10px 0 10px 0; display: none; \
						background-color: <?php echo $tweetfeed_backgroundColor; ?>; \
						border: 5px solid <?php echo $tweetfeed_backgroundColor; ?>; \
						border-radius: 10px; -moz-border-radius: 10px; -webkit-border-radius: 10px; \
						-webkit-box-shadow: 2px 2px 10px #222222; \
					'});
					twitter_badge_container.appendChild(twitter_badge_inner);
					
					if (index == 0 ) {
						twitter_badge_triangle = Builder.node('div',{style:'position: absolute; top: -45px; left: 15px; z-index: 0; height: 3px; width: 3px; margin:10px; background-color: <?php echo $tweetfeed_backgroundColor; ?>; border-width: 1px; border-style: solid; border-color: <?php echo $tweetfeed_backgroundColor; ?>; border-radius: 10px; -moz-border-radius: 10px; -webkit-border-radius: 10px; -webkit-box-shadow: 1px 1px 5px #222222;'});
						twitter_badge_inner.appendChild(twitter_badge_triangle);

						twitter_badge_triangle2 = Builder.node('div',{style:'position: absolute; top: -41px; left: 23px; z-index: 0; height: 5px; width: 5px; margin:10px; background-color: <?php echo $tweetfeed_backgroundColor; ?>; border-width: 3px; border-style: solid; border-color: <?php echo $tweetfeed_backgroundColor; ?>; border-radius: 10px; -moz-border-radius: 10px; -webkit-border-radius: 10px; -webkit-box-shadow: 1px 1px 5px #222222;'});
						twitter_badge_inner.appendChild(twitter_badge_triangle2);
						
						twitter_badge_triangle3 = Builder.node('div',{style:'position: absolute; top: -28px; left: 28px; z-index: 0; height: 5px; width: 5px; margin:10px; background-color: <?php echo $tweetfeed_backgroundColor; ?>; border-width: 7px; border-style: solid; border-color: <?php echo $tweetfeed_backgroundColor; ?>; border-radius: 10px; -moz-border-radius: 10px; -webkit-border-radius: 10px;'});
						twitter_badge_inner.appendChild(twitter_badge_triangle3);
					
					} else {
						var lInt = 0;
						var bSize = 4;
						var bTop = -26;
						if (index % 2 == 0) {
							lInt = 17;
							bSize = 3;
							bTop = -25;
						}
						twitter_badge_triangle = Builder.node('div',{style:'position: absolute; top: '+bTop+'px; left: '+lInt+'px; z-index: 0; height: 5px; width: 5px; margin:10px; background-color: <?php echo $tweetfeed_backgroundColor; ?>; border: '+bSize+'px solid <?php echo $tweetfeed_backgroundColor; ?>; border-radius: 10px; -moz-border-radius: 10px; -webkit-border-radius: 10px;'});
						twitter_badge_inner.appendChild(twitter_badge_triangle);
					}
					
					if (shouldUpdate) {
						$('twitter_messages').replaceChild(twitter_badge_container, $('twitter_messages').childNodes[index]);
						twitter_badge_container.show();
						//new Effect.Pulsate('twitter_badge_'+index, { duration: 0.5, from: 0.8, pulses: 1,queue:'end'});
					
					} else {
						$('twitter_messages').appendChild(twitter_badge_container);
						new Effect.BlindDown('twitter_badge_'+index, { duration: 0.5,queue:'end'});
					}
		
				};
			}
	

		</script>
	<div class="widget widget_text">
 		<img style="vertical-align: bottom; margin: 0; padding: 0;" src="<?php echo plugins_url('tweetfeed/twitter_logo.png'); ?>" title="TweetFeed Wordpress Plugin by bytefrog.de" alt="TweetFeed Wordpress Plugin by bytefrog.de">
		<ul class="tweetfeed">
			<div id="twitter_messages" style="width: 85%; margin: 0px auto;"></div>
			<div style="width: 180px; margin: 5px auto 10px auto; text-align: center; font-size: 0.8em;" class="navigation"><a href="http://www.twitter.com/<?php echo $tweetfeed_username; ?>" title="<?php echo $tweetfeed_username; ?> @ twitter.com"><img border=0 src="<?php echo plugins_url('tweetfeed/followMe.png'); ?>"></a></div>
		</ul>

	</div>
	<?php
	echo $after_widget;
}



function wp_tweetfeed_widget_control() {
		$options = $newoptions = get_option('tweetfeed_widget');
		if ( $_POST["tweetfeed_widget_submit"] ) {
		
			$newoptions['tweetfeed_username'] = strip_tags(stripslashes($_POST["tweetfeed_username"]));

			if ($_POST["oAuth_consumerKey"] != "·······") $newoptions['oAuth_consumerKey'] = strip_tags(stripslashes($_POST["oAuth_consumerKey"]));
			if ($_POST["oAuth_consumerSecret"] != "·······") $newoptions['oAuth_consumerSecret'] = strip_tags(stripslashes($_POST["oAuth_consumerSecret"]));
			if ($_POST["oAuth_consumerToken"] != "·······") $newoptions['oAuth_consumerToken'] = strip_tags(stripslashes($_POST["oAuth_consumerToken"]));
			if ($_POST["oAuth_consumerTokenSecret"] != "·······") $newoptions['oAuth_consumerTokenSecret'] = strip_tags(stripslashes($_POST["oAuth_consumerTokenSecret"]));
			
			$newoptions['tweetfeed_backgroundColor'] = strip_tags(stripslashes($_POST["tweetfeed_backgroundColor"]));
			$newoptions['tweetfeed_fontColor'] = strip_tags(stripslashes($_POST["tweetfeed_fontColor"]));
			$newoptions['tweetfeed_linkColor'] = strip_tags(stripslashes($_POST["tweetfeed_linkColor"]));
			$newoptions['tweetfeed_timeColor'] = strip_tags(stripslashes($_POST["tweetfeed_timeColor"]));
			$newoptions['tweetfeed_count'] = strip_tags(stripslashes($_POST["tweetfeed_count"]));
			$newoptions['tweetfeed_rSeed'] = strip_tags(stripslashes($_POST["tweetfeed_rSeed"]));
			$newoptions['tweetfeed_timeLineSwitch'] = strip_tags(stripslashes($_POST["tweetfeed_timeLineSwitch"]));
			$newoptions['tweetfeed_reload'] = strip_tags(stripslashes($_POST["tweetfeed_reload"]));
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('tweetfeed_widget', $options);
		}
		
		$tweetfeed_timeLineSwitch = attribute_escape($options['tweetfeed_timeLineSwitch']);

		$tweetfeed_username = attribute_escape($options['tweetfeed_username']);

		$oAuth_consumerKey = attribute_escape($options['oAuth_consumerKey']);
		$oAuth_consumerSecret = attribute_escape($options['oAuth_consumerSecret']);
		$oAuth_consumerToken = attribute_escape($options['oAuth_consumerToken']);
		$oAuth_consumerTokenSecret = attribute_escape($options['oAuth_consumerTokenSecret']);

		$tweetfeed_backgroundColor = attribute_escape($options['tweetfeed_backgroundColor']);
		$tweetfeed_fontColor = attribute_escape($options['tweetfeed_fontColor']);
		$tweetfeed_linkColor = attribute_escape($options['tweetfeed_linkColor']);
		$tweetfeed_timeColor = attribute_escape($options['tweetfeed_timeColor']);
		$tweetfeed_count = attribute_escape($options['tweetfeed_count']);
		$tweetfeed_rSeed = attribute_escape($options['tweetfeed_rSeed']);
		$tweetfeed_reload = attribute_escape($options['tweetfeed_reload']);
		?>
		    
		
			<p><label for="tweetfeed_timeLineSwitch"><?php _e('Timeline:'); ?> <select id="tweetfeed_timeLineSwitch" name="tweetfeed_timeLineSwitch">
				<option value="userTimeline" <?php if ($tweetfeed_timeLineSwitch == "userTimeline") echo "selected"; ?> >
					users timeline
				</option>
				<option value="friendsTimeline" <?php if ($tweetfeed_timeLineSwitch == "friendsTimeline") echo "selected"; ?> >
					friends timeline (needs oauth)
				</option>
			</select></label><br /><span style="font-size: 0.8em;">Hit save after changing  - to show the needed fields!</span>
			</p>
		
			<p style="<?php if ($tweetfeed_timeLineSwitch != "userTimeline") echo "display: none;"; ?>"><label for="tweetfeed_username"><?php _e('Username:'); ?> <input class="widefat" id="tweetfeed_username" name="tweetfeed_username" type="text" value="<?php echo $tweetfeed_username; ?>" /></label></p>

			<p style="<?php if ($tweetfeed_timeLineSwitch != "friendsTimeline") echo "display: none;"; ?>"><label for="oAuth_consumerKey"><?php _e('Consumer Key:'); ?> <input class="widefat" id="oAuth_consumerKey" name="oAuth_consumerKey" type="password" value="<?php if ($oAuth_consumerKey != "") echo "·······"; ?>" /></label></p>
			<p style="<?php if ($tweetfeed_timeLineSwitch != "friendsTimeline") echo "display: none;"; ?>"><label for="oAuth_consumerSecret"><?php _e('Consumer Secret:'); ?> <input class="widefat" id="oAuth_consumerSecret" name="oAuth_consumerSecret" type="password" value="<?php if ($oAuth_consumerSecret != "") echo "·······"; ?>" /></label></p>
			<p style="<?php if ($tweetfeed_timeLineSwitch != "friendsTimeline") echo "display: none;"; ?>"><label for="oAuth_consumerToken"><?php _e('OAuth Token:'); ?> <input class="widefat" id="oAuth_consumerToken" name="oAuth_consumerToken" type="password" value="<?php if ($oAuth_consumerToken != "") echo "·······"; ?>" /></label></p>
			<p style="<?php if ($tweetfeed_timeLineSwitch != "friendsTimeline") echo "display: none;"; ?>"><label for="oAuth_consumerTokenSecret"><?php _e('OAuth Token Secret:'); ?> <input class="widefat" id="oAuth_consumerTokenSecret" name="oAuth_consumerTokenSecret" type="password" value="<?php if ($oAuth_consumerTokenSecret != "") echo "·······"; ?>" /></label></p>
			
			
			<p><label for="tweetfeed_backgroundColor"><?php _e('Bubble color:'); ?> <input class="widefat" id="tweetfeed_backgroundColor" name="tweetfeed_backgroundColor" type="text" value="<?php echo $tweetfeed_backgroundColor; ?>" /></label></p>
			<p><label for="tweetfeed_fontColor"><?php _e('Font color:'); ?> <input class="widefat" id="tweetfeed_fontColor" name="tweetfeed_fontColor" type="text" value="<?php echo $tweetfeed_fontColor; ?>" /></label></p>
			<p><label for="tweetfeed_linkColor"><?php _e('Link color:'); ?> <input class="widefat" id="tweetfeed_linkColor" name="tweetfeed_linkColor" type="text" value="<?php echo $tweetfeed_linkColor; ?>" /></label></p>
			<p><label for="tweetfeed_timeColor"><?php _e('Timestamp color:'); ?> <input class="widefat" id="tweetfeed_timeColor" name="tweetfeed_timeColor" type="text" value="<?php echo $tweetfeed_timeColor; ?>" /></label></p>

			<p><label for="tweetfeed_count"><?php _e('Tweet count:'); ?> <input class="widefat" id="tweetfeed_count" name="tweetfeed_count" type="text" value="<?php echo $tweetfeed_count; ?>" /></label><br /><small>Default = 3</small></p>
			
			<p><label for="tweetfeed_rSeed"><?php _e('Random seed:'); ?> <input class="widefat" id="tweetfeed_rSeed" name="tweetfeed_rSeed" type="text" value="<?php echo $tweetfeed_rSeed; ?>" /></label><br /><small>If set, this will be the count of tweets that will be 'downloaded' and shuffled. Afterwards the widget will still display only the count which is set in 'Tweet count'.<br />Combined with the Timer below, this can for example randomize your friends timeline from time to time, while the page stays open. · Default is 'off'</small></p>
			
			<p><label for="tweetfeed_reload"><?php _e('Tweet refresh time:'); ?> <input class="widefat" id="tweetfeed_reload" name="tweetfeed_reload" type="text" value="<?php echo $tweetfeed_reload; ?>" /></label><br /><small>In Seconds · Default = 60 · set to 0 to disable refresh</small></p>
			<input type="hidden" id="tweetfeed_widget_submit" name="tweetfeed_widget_submit" value="1" />
		<?php
	}

?>
