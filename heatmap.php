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

$filename = "tweets/labelled_tweets.txt";
$lines = file($filename, FILE_IGNORE_NEW_LINES);

$tweets = array();
$cnt = 0;
$range = 6.25;

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
	$prob = ceil(10.0*$data[5]);
        if ($dis <= $range){
       	$tweet_array = array(
     		"lat"  => (float)$lat,
     		"lng"  => (float)$lon,
		"count" => $prob,
 	);
       	$tweets[$cnt] = $tweet_array;
	#$conf_avg += $prob;
	$cnt += 1;
        }
	if ($cnt == 100) break;
}

$js_array = "";
$max_ = 99;#count($lines);
$cnt = 0;

foreach ($tweets as &$value){
	if ($cnt == 0)	
		$js_array .= '['.json_encode($value).',';
	elseif ($cnt == $max_ - 1){
		$js_array .= ''.json_encode($value).']';
		break;
	}
	else	
		$js_array .= ''.json_encode($value).',';
	$cnt += 1;
}
#echo $js_array;
?>

<html>
<head>
<link href="/Starling/css/main.css" media="screen" rel="stylesheet" type="text/css" />
<link href="/Starling/css/heatmap.css" media="screen" rel="stylesheet" type="text/css" />

<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<title>Starling Heatmap Layer</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="shortcut icon" type="image/png" href="http://www.patrick-wied.at/img/favicon.png" />
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>

</head>
<body>
<div id="main">
			<h1>Heatmap Overlay</h1>
			<a href="http://www.patrick-wied.at/static/heatmapjs/" title="heatmap.js">Back to the project page</a><br /><br />
			<div id="heatmapArea">
			
			</div>
			
</div>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.0.js"></script>
<script type="text/javascript" src="libs/heatmapjs/heatmap.js"></script>
<script type="text/javascript" src="libs/heatmapjs/heatmap-gmaps.js"></script>

<script type="text/javascript">

var map;
var heatmap; 

window.onload = function(){

	var myLatlng = new google.maps.LatLng(40.7142, -74.0064);
	// sorry - this demo is a beta
	// there is lots of work todo
	// but I don't have enough time for eg redrawing on dragrelease right now
	var myOptions = {
	  zoom: 2,
	  center: myLatlng,
	  mapTypeId: google.maps.MapTypeId.ROADMAP,
	  disableDefaultUI: false,
	  scrollwheel: true,
	  draggable: true,
	  navigationControl: true,
	  mapTypeControl: false,
	  scaleControl: true,
	  disableDoubleClickZoom: false
	};
	map = new google.maps.Map(document.getElementById("heatmapArea"), myOptions);
	
	heatmap = new HeatmapOverlay(map, {"radius":15, "visible":true, "opacity":60});
	/*
	document.getElementById("gen").onclick = function(){
		var x = 5;
		while(x--){
		
			var lat = Math.random()*180;
			var lng = Math.random()*180;
			var count = Math.floor(Math.random()*180+1);
			
			heatmap.addDataPoint(lat,lng,count);
		
		}
	
	};
        */
	/*
	document.getElementById("tog").onclick = function(){
		heatmap.toggle();
	};
        */
	
	var testData={
    		max: 46,
    	};
    
        //TODO: data
        var testData = {
          max: 10,
          data: <?php echo $js_array; ?>
        };

        /**
        var testData = { 
          max: 50,
          data: [{lat:51.7095283,lng:-9.1194756,count:9},{lat:38.3487638,lng:-98.3494226,count:10},{lat:32.73261958,lng:-117.10200657,count:10},{lat:41.50784259,lng:-90.49844687,count:10},{lat:41.574642,lng:-71.8622123,count:10},{lat:53.60688358,lng:-2.18780333,count:9},{lat:39.94887081,lng:-82.98523033,count:1},{lat:38.81725,lng:-90.9516783,count:1},{lat:42.829458,lng:-78.8012425,count:10},{lat:33.93198369,lng:-116.89457023,count:4},{lat:39.60083392,lng:-104.90009183,count:1},{lat:51.01304922,lng:-2.64807715,count:10},{lat:32.50613349,lng:-92.07534037,count:5},{lat:39.11086567,lng:-84.49480983,count:5},{lat:1.35270884,lng:103.83495171,count:6},{lat:51.5263393,lng:-3.59258817,count:7},{lat:32.3123743,lng:-92.4512393,count:7},{lat:30.19607053,lng:-95.40982671,count:10},{lat:52.57062447,lng:-1.80816922,count:1},{lat:38.39061102,lng:-85.75853094,count:10},{lat:46.96377183,lng:-122.31220245,count:9},{lat:50.83293905,lng:-0.32111555,count:10},{lat:38.0751157,lng:-87.5518611,count:3},{lat:32.8508545,lng:-97.2353489,count:8},{lat:40.7180716,lng:-99.06229016,count:7},{lat:33.88173922,lng:-117.85465305,count:8},{lat:39.39174478,lng:-84.3154361,count:5},{lat:39.94887471,lng:-82.98521239,count:1},{lat:53.45710384,lng:-2.84920832,count:1},{lat:40.2803196,lng:-74.24059658,count:5},{lat:53.46902097,lng:-2.88380468,count:2},{lat:51.60291924,lng:-1.29079498,count:2},{lat:53.60692952,lng:-2.18806032,count:5},{lat:52.26296425,lng:-9.73418236,count:10},{lat:38.7106701,lng:-77.2542601,count:10},{lat:53.65800085,lng:-1.88339243,count:5},{lat:38.9566741,lng:-90.1874145,count:10},{lat:34.5903596,lng:-112.3339308,count:10},{lat:44.80926225,lng:-73.07225285,count:10},{lat:42.73583002,lng:-83.34786147,count:4}]};
        */
        //debugger; 	
	// this is important, because if you set the data set too early, the latlng/pixel projection doesn't work
	google.maps.event.addListenerOnce(map, "idle", function(){
		heatmap.setDataSet(testData);
	});
};

</script>
</body>
</html>
