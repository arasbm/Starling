<!DOCTYPE html>
<?php
# Spherical Law of Cosines
function distance_slc($lat1, $lon1, $lat2, $lon2) {
  $earth_radius = 3960.00; # in miles
  $delta_lat = $lat2 - $lat1 ;
  $delta_lon = $lon2 - $lon1 ;
  $distance  = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($delta_lon)) ;
  $distance  = acos($distance);
  $distance  = rad2deg($distance);
  $distance  = $distance * 60 * 1.1515;
  $distance  = round($distance, 4);
 
  return $distance;
}
?>
<?php
$ny_lon = -74.0064;
$ny_lat =  40.7142;

$filename = "tweets/labelled_tweets.txt";
$lines = file($filename, FILE_IGNORE_NEW_LINES);

$tweets = array();
$cnt = 0;
$range = 6.25;
$conf_avg = 0.0;
foreach ($lines as &$line) {
	$data = explode(";@;", $line);
	//echo $data[5];
	$lat = $data[1];
	$lon = $data[2];
	$dist = distance_slc($ny_lat, $ny_lon, $lat, $lon);
	#echo $lat."   ".$lon;
	#echo "<br>";
	#echo $dist;
	#echo "<br>";
	$prob = $data[5];
        if ($dis <= $range){
       	$tweet_array = array(
			"prob" => $prob,
    	 		"lat"  => $lat,
    	 		"lon"  => $lon,
 		);
       	$tweets[$cnt] = $tweet_array;
	$conf_avg += $prob;
	$cnt += 1;
        }
	if ($cnt == 100) break;
}
$conf_avg /= $cnt/100.0;
#echo $conf_avg;
#echo "<br>";
#$conf_avg = 90;
$avatar_image = "images/image1";

if ($conf_avg >= 0.0 && $conf_avg <= 25) $avatar_image = "images/image1";
elseif ($conf_avg > 25.0 && $conf_avg <= 50) $avatar_image = "images/image2";
elseif ($conf_avg > 50.0 && $conf_avg <= 75) $avatar_image = "images/image3";
else $avatar_image = "images/image4";

//echo $tweets;
/*
$js_array = "";
$max_ = 99;#count($lines);
$cnt = 0;
foreach ($tweets as &$value){
		if ($cnt == 0)	
				$js_array .= '[["'.implode('","', $value).'"],';
		elseif ($cnt == $max_ - 1){
				$js_array .= '["'.implode('","'	, $value).'"]]';
				break;
		}
		else	
				$js_array .= '["'.implode('","', $value).'"],';
		$cnt += 1;
}
echo $js_array;
*/
?>
<html>
  <head>
    <title>Starling: predicting flu!</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <link href="/Starling/css/home.css" media="screen" rel="stylesheet" type="text/css" />
    <style type="text/css">
      html { height: 100% }
      body { height: 100%; margin: 25px; padding: 0 }
      #map_canvas { height: 100% }
    </style>
  <h1>Starlring</h1>
  <div>
    <button id="city-button" class="citybutton">New York, NY</button>
  </div>
  <div>
    <img border="0" class="avatarimage" src="<?php echo $avatar_image; ?>" alt="avator" width="304" height="228">
  </div>
  <div>
    <p>There is <?php echo ceil($conf_avg)."%"; ?> chance that you will catch the flu!</p>
</body>


<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.0.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  $("#city-button").click(function() {
    window.location.replace("/Starling/heatmap.php");
  });
});
</script>
</html>
