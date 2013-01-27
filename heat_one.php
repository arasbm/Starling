
<?php
$filename = "tweets/labelled_tweets.txt";
$lines = file($filename, FILE_IGNORE_NEW_LINES);
$tweets = array();
$cnt = 0;
foreach ($lines as &$line) {
		$data = explode(";@;", $line);
		$tweet_txt = str_replace(array('\'', '"'), '', $data[5]); 
       	$tweet_array = array(
				"tweet" => $tweet_txt,
    	 		"lat"  => $data[2],
    	 		"lon"  => $data[1],
 		);
       	$tweets[$cnt] = $tweet_array;
	   	$cnt += 1; 
    }
$js_array = "";
$max_ = 1000;#count($lines);
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
?>
<html>
  <head>
    <link href="/Starling/css/main.css" media="screen" rel="stylesheet" type="text/css" />
    <link href="/Starling/css/heatmap.css" media="screen" rel="stylesheet" type="text/css" />
    <title>Starling | How serious is the flu where you are?</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
  </head>
  <body onload="initialize()">
    <h1>Starling</h1>
    <p>Find how serious is the flu in your area.
    <div id="map_canvas" style="width:100%; height:80%"></div>
</body>
    <!-- <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDTd_bReBaN0_QCy2OvqCczXEHoD6pMrmQ&sensor=false"></script> -->
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.9.0.js"></script>
    <script type="text/javascript" src="libs/heatmapjs/heatmap.js"></script>
    <script type="text/javascript" src="libs/heatmapjs/heatmap-gmaps.js"></script>

<div id="heatmapArea" />
       <script type="text/javascript">
      function load_tweets(){
	locations = <?php echo $js_array; ?>; 
      }
      function initialize() {
        var myLatlng = new google.maps.LatLng(48.3333, 16.35); 
        var mapOptions = {
          zoom: 3,
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
        //var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
        var map = new google.maps.Map($("#map_canvas")[0], mapOptions);
	//load_tweets();

	//var infowindow = new google.maps.InfoWindow();

        /**
	var marker, i;
	for (i = 0; i < locations.length; i++) {  
		marker = new google.maps.Marker({
		position: new google.maps.LatLng(locations[i][1], locations[i][2]),
		map: map
		});

		google.maps.event.addListener(marker, 'click', (function(marker, i) {
		return function() {
	  		infowindow.setContent(locations[i][0]);
	  		infowindow.open(map, marker);
		}
		})(marker, i));
	}
        */
    
        // Heatmap standard gmaps initialization
        //var myLatlng = new google.maps.LatLng(48.3333, 16.35);
        // we'll use the heatmapArea
        //var map = new google.maps.Map($("#heatmapArea")[0], myOptions);

        // let's create a heatmap-overlay
        // with heatmap config properties
        var heatmap = new HeatmapOverlay(map, {
          "radius":20,
          "visible":true,
          "opacity":80
        });
         
        // here is our dataset
        // important: a datapoint now contains lat, lng and count property!
        var testData={
          max: 46,
          data: [{lat: 33.5363, lng:-117.044, count: 1},{lat: 33.5608, lng:-117.24, count: 1},{lat: 38, lng:-97, count: 1},{lat: 38.9358, lng:-77.1621, count: 1}]
        };
        
        debugger; 
        // now we can set the data
        google.maps.event.addListenerOnce(map, "idle", function(){
          // this is important, because if you set the data set too early, the latlng/pixel projection doesn't work
          heatmap.setDataSet(testData);
        });
      }
    </script>
</html>
