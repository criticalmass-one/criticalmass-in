var map;

var elementsArray = [];

function setLastModifiedLabel()
{
	var d = new Date();

	$('p.lastmodified span#datetime').html(
		(d.getHours() < 10 ? '0' + d.getHours() : d.getHours()) + ':' + 
		(d.getMinutes() < 10 ? '0' + d.getMinutes() : d.getMinutes()) + ':' + 
		(d.getSeconds() < 10 ? '0' + d.getSeconds() : d.getSeconds()) + ' Uhr');
}

function setUsercounter(result)
{
	$('span#usercounter').html(result.usercounter);
}

function setAverageSpeed(result)
{
	$('span#averagespeed').html(result.averagespeed);
}

function drawCircle(circleElement)
{
	if (!doesElementExist(circleElement.id))
	{
		circleElement.map = map;
		circleElement.center = new google.maps.LatLng(circleElement.latitude, circleElement.longitude);

		googleMapsCircleElement = new google.maps.Circle(circleElement);
		elementsArray[circleElement.id] = googleMapsCircleElement;
	}
}

function drawArrow(circleElement)
{

}

function drawMarker(circleElement)
{

}

function doesElementExist(elementId)
{
	var found = false;

	for (index in elementsArray)
	{
		if (index == elementId)
		{
			found = true;
			break;
		}
	}

	return found;
}


function clearElement(id)
{
	elementsArray[id].setMap(null);
	delete markersArray[id];
}

function clearAllElements()
{
	if (elementsArray)
	{
		for (index in elementsArray)
		{
			elementsArray[index].setMap(null);
		}
	}
}

function clearOldElements(elements)
{
	var pos;
	var index;

	for (index in elementsArray)
	{
		var found = false;

		for (pos in elements)
		{
			if (index == elements[pos].id)
			{
				found = true;
			}
		}

		if (!found)
		{
			clearElement(i);
		}
	}
}

function refreshElements(elements)
{
	if (elements)
	{
		for (index in elements)
		{
			if (elements[index].type == "circle")
			{
				drawCircle(elements[index]);
			}
			/*
			if (elements[index].type == "arrow")
			{
				drawArrow(elements[index]);
			}

			if (elements[index].type == "marker")
			{
				drawMarker(elements[index]);
			}*/
		}
	}
}

function refreshLivePage()
{
	$.ajax({
		url: '/mapapi/mapdata',
		success: refreshLivePage2
	});
}

function refreshLivePage2(result)
{
	refreshElements(result.elements);
	setLastModifiedLabel();
	setUsercounter(result);
	setAverageSpeed(result);
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

	refreshElements(result.elements);
	setAverageSpeed(result);
	setUsercounter(result);
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

	var timer = setInterval(refreshLivePage, 5000);
}

google.maps.event.addDomListener(window, 'load', startInitialization);
