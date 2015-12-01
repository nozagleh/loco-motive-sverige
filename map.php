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
		var Stations = new Array();
			$(document).ready(function(){
				
				try {
                $.ajaxSetup({
                    url: "http://api.trafikinfo.trafikverket.se/v1/data.json",
                    error: function (msg) {
                        if (msg.statusText == "abort") return;
                        alert("Request failed: " + msg.statusText + "\n" + msg.responseText);
                    }
                });
            }
            catch (e) { alert("Ett fel uppstod vid initialisering."); }
            PreloadTrainStations();

            //TODO: 
            // Get station from database

            function PreloadTrainStations() {
            // Request to load all stations
            var xmlRequest = "<REQUEST>" +
                                // Use your valid authenticationkey
                                "<LOGIN authenticationkey='00adf64e36bd4f3c8b12e6a807e1e54e' />" +
                                "<QUERY objecttype='TrainStation'>" +
                                    "<FILTER/>" +
                                    "<INCLUDE>Prognosticated</INCLUDE>" +
                                    "<INCLUDE>AdvertisedLocationName</INCLUDE>" +
                                    "<INCLUDE>LocationSignature</INCLUDE>" +
                                    "<INCLUDE>PlatformLine</INCLUDE>"+
                                "</QUERY>" +
                             "</REQUEST>";
            $.ajax({
                type: "POST",
                contentType: "text/xml",
                dataType: "json",
                data: xmlRequest,
                success: function (response) {
                    if (response == null) return;
                    try {
                        var stationlist = [];
                        $(response.RESPONSE.RESULT[0].TrainStation).each(function (iterator, item)
                        {
                            // Save a key/value list of stations
                            Stations[item.LocationSignature] = item.AdvertisedLocationName;
                            Stations[item.PlatformLine] = item.PlatformLine;
                            // Create an array to fill the search field autocomplete.
                            if (item.Prognosticated == true)
                                stationlist.push({ label: item.AdvertisedLocationName, value: item.LocationSignature });
                        });
                        //fillSearchWidget(stationlist);
                    }
                    catch (ex) { }
                }
            });
        }

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
					
					<h3>Avgående tåg</h3>
        			<table border="1" id="timeTableDeparture">
                      <tr>
                        <th scope="col" style="width:40px;">Tid</th>
						<th scope="col" style="width:40px;">T.Nr</th>
                        <th scope="col" style="width:200px;">Till</th>
                        <th scope="col" style="width:80px;"></th>
                        <th scope="col"  style="width:80px;">Spår</th>
                      </tr>
                    </table>
				</div>
				<script type="text/javascript">
					var geoloc = {lat: <?php echo($lat); ?>, lng: <?php print($lng); ?>};
					
					console.log(geoloc);
					
					var map;
					function closeStationInfo(event){
							$("#station").fadeOut(400);
							$("#map").removeClass('fade');
							this.removeEventListener("click", closeStationInfo);
					}

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

					function Search(stationid) {
						//var sign = $("#station").data("sign");
						var sign = stationid;
						// Clear html table
						$('#timeTableDeparture tr:not(:first)').remove();
						// Request to load announcements for a station by its signature
						var xmlRequest = "<REQUEST version='1.0'>" +
                                "<LOGIN authenticationkey='00adf64e36bd4f3c8b12e6a807e1e54e' />" +
                                "<QUERY objecttype='TrainAnnouncement' " +
                                    "orderby='AdvertisedTimeAtLocation' >" +
                                    "<FILTER>" +
                                    "<AND>" +
                                        "<OR>" +
                                            "<AND>" +
                                                "<GT name='AdvertisedTimeAtLocation' " +
                                                            "value='$dateadd(-02:00:00)' />" +
                                                "<LT name='AdvertisedTimeAtLocation' " +
                                                            "value='$dateadd(14:00:00)' />" +
                                            "</AND>" +
                                            "<GT name='EstimatedTimeAtLocation' value='$now' />" +
                                        "</OR>" +
                                        "<EQ name='LocationSignature' value='" + sign + "' />" +
                                        "<EQ name='ActivityType' value='Avgang' />" +
                                    "</AND>" +
                                    "</FILTER>" +
                                    // Just include wanted fields to reduce response size.
									"<INCLUDE>AdvertisedTrainIdent</INCLUDE>" +
                                    "<INCLUDE>InformationOwner</INCLUDE>" +
                                    "<INCLUDE>AdvertisedTimeAtLocation</INCLUDE>" +
                                    "<INCLUDE>TrackAtLocation</INCLUDE>" +
                                    "<INCLUDE>FromLocation</INCLUDE>" +
                                    "<INCLUDE>ToLocation</INCLUDE>" +
                                "</QUERY>" +
                                "</REQUEST>";
							$.ajax({
								type: "POST",
								contentType: "text/xml",
								dataType: "json",
								data: xmlRequest,
								success: function (response) {
								    if (response == null) return;
								    if (response.RESPONSE.RESULT[0].TrainAnnouncement == null)
								        jQuery("#timeTableDeparture tr:last").
								            after("<tr><td colspan='4'>Inga avgångar hittades</td></tr>");
								        try {
								        	//console.log(response.RESPONSE.RESULT[0].TrainAnnouncement);
								        	renderTrainAnnouncement(response.RESPONSE.RESULT[0].TrainAnnouncement);
								        }
								        catch (ex) { }
								    }
								});
					}

					function renderTrainAnnouncement(announcement) {
			            $(announcement).each(function (iterator, item) {
			                var advertisedtime = new Date(item.AdvertisedTimeAtLocation);
			                var hours = advertisedtime.getHours()
			                var minutes = advertisedtime.getMinutes()
			                if (minutes < 10) minutes = "0" + minutes
			                var toList = new Array();
			                $(item.ToLocation).each(function (iterator, toItem) {
								//console.log(item.ToLocation);
			                    toList.push(Stations[toItem]);
			                });
			                var owner = "";
			                if (item.InformationOwner != null) owner = item.InformationOwner;
			                jQuery("#timeTableDeparture tr:last").
			                    after("<tr><td>" + hours + ":" + minutes + "</td><td>"+ item.AdvertisedTrainIdent +"</td><td>" + toList.join(', ') +
			                    "</td><td>" + owner + "</td><td style='text-align: center'>" + item.TrackAtLocation +
			                    "</td></tr>");
			                    console.log(item.AdvertisedTrainIdent);
			            });
			        }

					function addStations(){
						var station_marker = {
							url: 'img/station-small.png',
							size: new google.maps.Size(32,32),
							origin: new google.maps.Point(0,0)
						};
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
			    						var isOpen = false;
			    						marker.addListener('click',function(e){
			    							var stationName = this.getTitle();
			    							var stationid = this.get('id');
										  	//console.log(stationid);
										  	$("#station").fadeToggle(400);
										  	$("#stationBannerName").text(stationName);
										  	$("#map").toggleClass('fade');
										  	isOpen = true;
										  	Search(stationid);
										  	//document.getElementById('map').addEventListener("click", closeStationInfo);
										});

									/*	$("#map").click(function(event) {
											if (isOpen) {
												$("#station").fadeOut(400);
										  		$("#map").removeClass('fade');
										  		isOpen = false;
											};	
										
										  	});*/

			    						/* Find a way to hide markers at certain zoom level
										map.addListener('zoom_changed', function() {
											console.log(map.getZoom());
										    if (map.getZoom() <= 10) {
										  		marker.setVisible(false);
										  	}else{
										  		marker.setVisible(true);
										  	};
										});
										*/
			    					}
			    				},
			    				error: function(e){
			    					console.log(e);
			    				}
		    				});
					  }
					  console.log("i am here");
					  // Esc key
					  $(document).keydown(function(event) {
					  	console.log("function");
					  	if(event.keyCode == 27){
					  		$("#station").fadeOut(400);
							$("#map").removeClass('fade');
					  	}
					  });
					  console.log("i am here too!");


		    	</script>
		<script async defer
		      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAniM7GOQ9kIfDD9V5-QwQ6owIRnaMV62I&callback=initMap">
		    </script>
			</div>
		</div>
	</body>
</html>