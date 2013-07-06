var geocoder;
var map;
var marker;

function initialize() {
  geocoder = new google.maps.Geocoder();
  var latlng = new google.maps.LatLng(-34.397, 150.644);
  var mapOptions = {
    zoom: 12,
    center: latlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  }
  map = new google.maps.Map(document.getElementById('information-map'), mapOptions);

}

function placeNewMarker(location)
{
  if (marker)
  {
    marker.setMap(null);
  }

  map.setCenter(location);

  marker = new google.maps.Marker({
      map: map,
      position: location
  });
}



google.maps.event.addDomListener(window, 'load', initialize);