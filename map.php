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
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				
			});
		</script>
	</head>
	<body>
		<div id="container">
			<div id="content">
				<header>
					<h1 id="logo">LOGO</h1>
				</header>
				<div id="map"></div>
				<div id="station">
					<div class="stationBanner">
						<h1 id="stationBannerName">Station Name</h1>
					</div>
					
					<ul>
						<li>A Train</li>
						<li>Another train</li>
						<li>Huh the third train</li>
						<li>Well four is fine</li>
						<li>Haaang on, isn't five a bit much...</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
						<li>NOOOOO</li>
					</ul>
				</div>
				<script type="text/javascript">
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

					  addStations();

					  marker.addListener('click',function(){
					  	console.log("A marker was clicked");
					  });
					}

					function addStations(){
						var station_marker = 'img/train-station.png';
					  	var stations = [];
						$.ajax({
			    				url: 'getStations.php',
			    				type: 'POST',
			    				dataType: 'json',
			    				success: function(data){
			    					//console.log(data);
			    					var dataLen = data.length;
			    					for (var i = 0; i < dataLen; i++) {
			    						var tempArr = [data[i]["ID"],data[i]["Name"],data[i]["Lat"],data[i]["Lng"],i];
			    						stations.push(tempArr);
			    					}
			    					for (var i = 0; i < stations.length; i++) {
			    						var station = stations[i];
			    						var location = new google.maps.LatLng(station[2],station[3]);

			    						var marker = new google.maps.Marker({
			    							id: station[0],
			    							position: location,
			    							map: map,
			    							icon: station_marker,
			    							title: station[1]
			    						});
			    						marker.addListener('click',function(e){
			    							var stationName = this.getTitle();
										  	console.log(stationName);
										  	$("#station").fadeToggle(400);
										  	$("#stationBannerName").text(stationName);
										});
			    					}
			    				},
			    				error: function(e){
			    					console.log(e);
			    				}
		    				});
					  }

		    	</script>
		<script async defer
		      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAniM7GOQ9kIfDD9V5-QwQ6owIRnaMV62I&callback=initMap">
		    </script>
			</div>
		</div>
	</body>
</html>