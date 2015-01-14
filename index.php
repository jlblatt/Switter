<!DOCTYPE html>
<html>

	<head>
		<title>Switter - Twitter Search</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=960,maximum-scale=1.0" />
		<link rel="stylesheet" href="css/style.css" media="screen" />
		<script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
		<script src="js/common.js"></script>
		<script type="text/javascript">
		      WebFontConfig = {
		        google: { families: [ 'Gudea' ] }
		      };

		      (function() {
		        var wf = document.createElement('script');
		        wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
		            '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
		        wf.type = 'text/javascript';
		        wf.async = 'true';
		        var s = document.getElementsByTagName('script')[0];
		        s.parentNode.insertBefore(wf, s);
		      })();

			  var _gaq = _gaq || [];
			  _gaq.push(['_setAccount', 'UA-22974070-1']);
			  _gaq.push(['_trackPageview']);

			  (function() {
			    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			  })();
		</script>
	</head>

	<body>
		<div id="wrapper">
			<form id="switter" name="switter" action="./" method="get">
				<!--<h1>I am working on this page *right now* (11PM EST Oct 15th).  It may be broken!  It will be fixed before I go to sleep.<br /></h1>-->
				<label for="query" id="query-label">Query</label>
				<div id="query-wrapper"><input type="text" id="query" name="query" placeholder="search" maxlength="255" autocomplete="off" <? if($_GET["query"]) echo 'value="' . $_GET["query"] . '"'; ?>/></div>
				<div id="submit-wrapper"><input type="submit" id="submit" class="btn_lg" value="Search" /></div>

				<!--<div class="clear"></div>

				<label for="result_type_mixed">Mixed</label>
				<input type="radio" name="result_type" class="result_type" id="result_type_mixed" value="mixed" <? if($_GET["result_type"] == "mixed" || !$_GET["result_type"]) echo 'checked'; ?> />

				<label for="result_type_recent">Recent</label>
				<input type="radio" name="result_type" class="result_type" id="result_type_recent" value="recent" <? if($_GET["result_type"] == "recent") echo 'checked'; ?> />

				<label for="result_type_popular">Popular</label>
				<input type="radio" name="result_type" class="result_type" id="result_type_popular" value="popular" <? if($_GET["result_type"] == "popular") echo 'checked'; ?> />-->

				<div class="clear"></div>
			</form>

			<div id="loading">
				<img src="images/loading.gif" alt="Loading" title="Loading" /><br />
				<img src="images/loading.gif" alt="Loading" title="Loading" /><br />
			</div>

			<div id="results" <? if($_GET["query"]) echo 'style="display: block;"'; ?>>
			<?
				if($_GET["query"])
				  {
				    $ch = curl_init("http://search.twitter.com/search.json?q=" . urlencode($_GET["query"]));
				    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				    $json = curl_exec($ch);
				    curl_close($ch);
				    
				    $response = json_decode($json);
				    
				    echo "<!--";
				    var_dump($response, true);
				    echo "-->";

				    for($i = 0, $size = sizeof($response->results); $i < $size; $i++)
				      {
					$tweet = $response->results[$i];

					$tweetText = preg_replace("/(" . $_GET["query"] . ")/i", "<strong>$1</strong>", $tweet->text);
					$tweetText = preg_replace("/(http[s]?\:\/\/[^\s\<]+)/", '<a target="_blank" href="$1">$1</a>', $tweetText);
					$tweetText = preg_replace("/\#([\w]+)/", '<a class="newsearch" href="./?query=%23$1">#$1</a>', $tweetText);
					$tweetText = preg_replace("/\@([\w]+)/", '<a class="newsearch" href="./?query=%40$1">@$1</a>', $tweetText);

					echo '<div class="tweet">';
					echo '<div class="profile-image-container"><a target="_blank" href="http://twitter.com/#!/' . $tweet->from_user . '"><img class="profile-image" src="' . $tweet->profile_image_url . '" /></a></div>';
					echo '<div class="timestamp">' . preg_replace("/\+\d+$/", "", $tweet->created_at) . '</div>';
					echo '<div class="user"><a target="_blank" href="http://twitter.com/#!/' . $tweet->from_user . '">' . $tweet->from_user_name . '</a> (@' . $tweet->from_user . ')</div>';
					echo '<div class="text">' . $tweetText . '</div>';
					echo '<div class="permalink"><a target="_blank" href="http://twitter.com/#!/' . $tweet->from_user . '/status/' . $tweet->id . '">Permalink &raquo;</a></div>';
					echo '<div class="clear"></div>';
					echo '</div>';
				      }

				    echo '<div id="search-meta">Search completed in ' . $response->completed_in . ' seconds.</div>';
				  }
			?>
			</div>
		</div>
	</body>

</html>
