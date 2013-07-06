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
  map = new google.maps.Map(document.getElementById('geocoding-map'), mapOptions);

  $('#caldera_criticalmassbundle_ridetype_mapLocation').change(function()
  {
    codeAddress();
  });
}

function codeAddress() {
  var address = $('#caldera_criticalmassbundle_ridetype_mapLocation').val();

  geocoder.geocode( { 'address': address}, function(results, status)
  {
    if (status == google.maps.GeocoderStatus.OK)
    {
      map.setCenter(results[0].geometry.location);

      marker = new google.maps.Marker({
          map: map,
          position: results[0].geometry.location
      });
    }
  });
}

google.maps.event.addDomListener(window, 'load', initialize);