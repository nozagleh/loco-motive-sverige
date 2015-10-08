<?php
	$lat = $_GET["lat"];
	$lng = $_GET["lng"];
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title></title>
		<link rel="stylesheet" type="text/css" href="styles/stylesheet.css">
		<!--<script type="text/javascript" src="js/getGeolocation.js"></script>-->
	</head>
	<body>
		<div id="map"></div>
		<script type="text/javascript">

			//var latIn = <?php echo($lat); ?>;
			//var lngIn = <?php print($lng); ?>;

			var geoloc = {lat: <?php echo($lat); ?>, lng: <?php print($lng); ?>};
			console.log(geoloc);
			var map;
			function initMap() {
			  map = new google.maps.Map(document.getElementById('map'), {
			    //center: {lat: 0, lng: 0},
			    center: geoloc,
			    zoom: 14
			  });

			  var person_marker = 'img/geo_marker_small.png';
			  var marker = new google.maps.Marker({
			  	position: geoloc,
			  	map: map,
			  	icon: person_marker,
			  	title: 'You are here'
			  });
			}
    	</script>
<script async defer
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAniM7GOQ9kIfDD9V5-QwQ6owIRnaMV62I&callback=initMap">
    </script>
	</body>
</html>