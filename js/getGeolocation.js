/*
*   With this we can get the geolocation from the browser
*   Either we can redirect a user to a page with the map and the geolocation as GET or we can display it here
*/
$(document).ready(function(){
	
	$("#geoBtn").click(function(event) {
		getLocation();
	});
});

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}
function showPosition(position) {
    alert("Latitude: " + position.coords.latitude + 
    "<br>Longitude: " + position.coords.longitude);
}