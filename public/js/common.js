window.onload = getLocation;

var geo = navigator.geolocation;     /*     Here we will check if the browser supports the Geolocation API; if exists, then we will display the location     */

function getLocation() {
    if( geo ) {
        geo.getCurrentPosition( displayLocation );
    }
    else  { alert( "Oops, Geolocation API is not supported");
    }
}

/*     This function displays the latitude and longitude when the browser has a location.     */

function displayLocation( position ) {
    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;
    var div = document.getElementById( 'location' );
    div.innerHTML = "You are at Latitude: " + latitude + ", Longitude: " + longitude;
}