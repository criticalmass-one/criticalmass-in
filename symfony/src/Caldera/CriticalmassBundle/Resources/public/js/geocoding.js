var geocoder;
var map;
var marker;

function initializeGeocoding() {
  geocoder = new google.maps.Geocoder();

  var latlng;

  if ($('#caldera_criticalmassbundle_ridetype_latitude').val() != '0' &&
      $('#caldera_criticalmassbundle_ridetype_longitude').val() != '0')
  {
    latlng = new google.maps.LatLng(
      $('#caldera_criticalmassbundle_ridetype_latitude').val(),
      $('#caldera_criticalmassbundle_ridetype_longitude').val()
    );

    initMap(latlng);
    placeNewMarker(latlng);
  }
  else
  {
    $.ajax({
      type: 'GET',
      url: '/mapapi/getcitylocation/' + citySlugString,
      data: {
      },
      success: function(result)
      {
        latlng = new google.maps.LatLng(result.latitude, result.longitude);
        initMap(latlng);
      }
    });
  }

  $('#caldera_criticalmassbundle_ridetype_city').change(function()
  {
    var cityId = $('#caldera_criticalmassbundle_ridetype_city').val();

    centerMapToLngLat(cityId);
  });

  $('#caldera_criticalmassbundle_ridetype_mapLocation').change(function()
  {
    codeAddress();
  });

  $('#caldera_criticalmassbundle_ridetype_latitude').change(function()
  {
    locateAddress();
  });

  $('#caldera_criticalmassbundle_ridetype_longitude').change(function()
  {
    locateAddress();
  });
}

function centerMapToLngLat(cityId)
{
  alert("FOOO: " + cityId);
  $.ajax({
    type: 'GET',
    url: '/mapapi/getcitylocationbyid/' + cityId,
    data: {
    },
    success: function(result)
    {
      alert("A: " + result.latitude + "B: " + result.longitude);
      var latlng = new google.maps.LatLng(result.latitude, result.longitude);
      map.setCenter(latlng);
    }
  });
}

function initMap(latlng)
{
  var mapOptions = {
    zoom: 12,
    center: latlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  }

  map = new google.maps.Map(document.getElementById('geocoding-map'), mapOptions);
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
      position: location,
      draggable: true
  });

  google.maps.event.addListener(marker, 'drag', markerListener);
}

function markerListener()
{
  var latlng = marker.getPosition();

  $('#caldera_criticalmassbundle_ridetype_latitude').val(latlng.lat());
  $('#caldera_criticalmassbundle_ridetype_longitude').val(latlng.lng());
}

function codeAddress() {
  var address = $('#caldera_criticalmassbundle_ridetype_mapLocation').val();

  geocoder.geocode( { 'address': address}, function(results, status)
  {
    if (status == google.maps.GeocoderStatus.OK)
    {
      placeNewMarker(results[0].geometry.location);

      $('#caldera_criticalmassbundle_ridetype_latitude').val(results[0].geometry.location.lat());
      $('#caldera_criticalmassbundle_ridetype_longitude').val(results[0].geometry.location.lng());
    }
  });
}

function locateAddress() {
  var address = $('#caldera_criticalmassbundle_ridetype_mapLocation').val();

  var location = new google.maps.LatLng(
    $('#caldera_criticalmassbundle_ridetype_latitude').val(),
    $('#caldera_criticalmassbundle_ridetype_longitude').val()
  );

  placeNewMarker(location);
}

google.maps.event.addDomListener(window, 'load', initializeGeocoding);
