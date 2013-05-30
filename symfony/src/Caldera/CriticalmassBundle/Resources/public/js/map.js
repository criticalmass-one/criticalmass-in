function setMapOptions(result)
{
	var mapOptions = {
		zoom: result.zoom,
		center: new google.maps.LatLng(result.mapcenter.latitude, result.mapcenter.longitude),
		mapTypeId: google.maps.MapTypeId.TERRAIN
	}

	var map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);

	for (var pos in result.positions)
	{
		var populationOptions = {
			strokeColor: '#FF0000',
			strokeOpacity: 0.8,
			strokeWeight: 2,
			fillColor: '#FF0000',
			fillOpacity: 0.35,
			map: map,
			center: new google.maps.LatLng(result.positions[pos].latitude, result.positions[pos].longitude),
			radius: 1000
		};

		cityCircle = new google.maps.Circle(populationOptions);
	}
}

function initialize()
{
	$.ajax({
		type: 'GET',
		url: '/criticalmass/symfony/web/app_dev.php/mapapi/mapdata',
		data: {
		},
		cache: false,
		success: setMapOptions
	});
}

google.maps.event.addDomListener(window, 'load', initialize);