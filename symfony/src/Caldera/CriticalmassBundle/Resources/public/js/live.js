var markersArray = [];
var map;
var arrow;

function setLastModifiedLabel()
{
	var d = new Date();

	$('p.lastmodified span#datetime').html(
		(d.getHours() < 10 ? '0' + d.getHours() : d.getHours()) + ':' + 
		(d.getMinutes() < 10 ? '0' + d.getMinutes() : d.getMinutes()) + ':' + 
		(d.getSeconds() < 10 ? '0' + d.getSeconds() : d.getSeconds()) + ' Uhr');
}

function setArrow(result)
{
	var position1 = result.mainpositions['position-0'];
	var position2 = result.mainpositions['position-1'];

	var vector = [position2.latitude - position1.latitude, position2.longitude - position1.longitude];
	var arrowLength = 12;

	var coord1 = new google.maps.LatLng(position1.latitude, position1.longitude);
	var coord2 = new google.maps.LatLng(position1.latitude + vector[0] * arrowLength, position1.longitude + vector[1] * arrowLength);

	arrow = new google.maps.Polyline({
		path: [coord1, coord2],
		icons: [{
			icon: { path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW },
			offset: '100%'
		}],
		map: map
	});
}

function addMarker(options)
{
	options.map = map;
	options.center = new google.maps.LatLng(options.latitude, options.longitude);

	circleMarker = new google.maps.Circle(options);
	markersArray[options.id] = circleMarker;
}

function clearAllMarkers()
{
	if (markersArray)
	{
		for (i in markersArray)
		{
			markersArray[i].setMap(null);
		}
	}
}

function clearMarker(id)
{
	markersArray[id].setMap(null);
	delete markersArray[id];
}

function refreshMarkers()
{
	$.ajax({
		url: '/mapapi/mapdata',
		success: refreshMarkers2
	});

}

function placeNewMarkers(result)
{
	for (var pos in result.mainpositions)
	{
		if (!existsMarker(result.mainpositions[pos]))
		{
			addMarker(result.mainpositions[pos]);
		}
	}

	for (var pos in result.additionalpositions)
	{
		if (!existsMarker(result.additionalpositions[pos]))
		{
			addMarker(result.additionalpositions[pos]);
		}
	}
}

function refreshMarkers2(result)
{
	placeNewMarkers(result);
	flushOldMarkers(result);
	setLastModifiedLabel();
	setArrow(result);
}

function flushOldMarkers(result)
{
	var pos;
	var i;

	for (i in markersArray)
	{
		var found = false;

		for (pos in result.mainpositions)
		{
			if (i == result.mainpositions[pos].id)
			{
				found = true;
			}
		}

		for (pos in result.additionalpositions)
		{
			if (i == result.additionalpositions[pos].id)
			{
				found = true;
			}
		}

		if (!found)
		{
			clearMarker(i);
		}
	}
}

function existsMarker(options)
{
	var found = false;

	for (i in markersArray)
	{
		if (i == options.id)
		{
			found = true;
			break;
		}
	}

	return found;
}

function setMapOptions(result)
{
	var mapOptions = {
		zoom: result.zoom,
		center: new google.maps.LatLng(result.mapcenter.latitude, result.mapcenter.longitude),
		mapTypeId: google.maps.MapTypeId.TERRAIN,
		disableDefaultUI: true
	}

	map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

	placeNewMarkers(result);
	setArrow(result);
}

function initializeLivePage()
{
	if ($('#map-canvas').length > 0)
	{
		$.ajax({
			type: 'GET',
			url: '/mapapi/mapdata',
			data: {
			},
			cache: false,
			success: setMapOptions
		});
	}

	$( "#slider-gps-interval" ).on( "slidestop", function( event, ui ) {
		$.ajax({
			type: 'GET',
			url: '/settings/gpsinterval',
			data: {
				'interval': event.target.value
			},
			cache: false
		});
	} );

	$( "#flip-gps-sender" ).on( "slidestop", function( event, ui ) {
		$.ajax({
			type: 'GET',
			url: '/settings/gpsstatus',
			data: {
				'status': $("select#flip-gps-sender")[0].selectedIndex
			},
			cache: false
		});
	} );
}

function startInitialization()
{
	initializeLivePage();

	var timer = setInterval(refreshMarkers, 5000);
}

google.maps.event.addDomListener(window, 'load', startInitialization);
