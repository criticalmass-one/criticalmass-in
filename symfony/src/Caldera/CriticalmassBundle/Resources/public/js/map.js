// Create an object containing LatLng, population.
var citymap = {};
citymap['wedel'] = {
  center: new google.maps.LatLng(53.57033623530256, 9.719623122674422),
  population: 30000
};
var cityCircle;

function initialize() {
	function setMapOptions(result)
	{
		var mapOptions = {
			zoom: result.zoom,
			center: new google.maps.LatLng(result.mapcenter.latitude, result.mapcenter.longitude),
			mapTypeId: google.maps.MapTypeId.TERRAIN
		}

	  var map = new google.maps.Map(document.getElementById('map-canvas'),
	      mapOptions);

	  for (var city in citymap) {
	    // Construct the circle for each value in citymap. We scale population by 20.
	    var populationOptions = {
	      strokeColor: '#FF0000',
	      strokeOpacity: 0.8,
	      strokeWeight: 2,
	      fillColor: '#FF0000',
	      fillOpacity: 0.35,
	      map: map,
	      center: citymap[city].center,
	      radius: citymap[city].population / 20
	    };
	    cityCircle = new google.maps.Circle(populationOptions);
	  }
	}

	$.ajax({
		type: 'GET',
		url: '/criticalmass/symfony/web/app_dev.php/mapapi/mapcenter',
		data: {
		},
		cache: false,
		success: setMapOptions
	});



}

google.maps.event.addDomListener(window, 'load', initialize);