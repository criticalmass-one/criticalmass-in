/**
 * Enthält während des Betriebs der Live-Seite die Instanz der eingebetteten Karte.
 */
var map;

/**
 * Array aller in der Karte enthaltenen grafischen Elemente.
 */
var elementsArray = [];

/**
 * Setzt den Zeitpunkt der letzten Änderung der Seite auf die aktuelle Uhr-
 * zeit. Sollte es sich bei den Bestandteilen der Uhrzeit um einstellige Werte
 * handeln, wird eine führende Null vorangestellt.
 */
function setLastModifiedLabel()
{
	var d = new Date();

	$('p.lastmodified span#datetime').html(
		(d.getHours() < 10 ? '0' + d.getHours() : d.getHours()) + ':' + 
		(d.getMinutes() < 10 ? '0' + d.getMinutes() : d.getMinutes()) + ':' + 
		(d.getSeconds() < 10 ? '0' + d.getSeconds() : d.getSeconds()) + ' Uhr');
}

/**
 * Aktualisiert die Aufschrift des Zählers momentan angemeldeter Benutzer auf
 * den Wert aus dem Resultat-Array.
 */
function setUsercounter(result)
{
	$('span#usercounter').html(result.usercounter);
}

/**
 * Aktualisiert die Aufschrift der Durchschnittsgeschwindigkeit auf den Wert
 * aus dem Resultat-Array.
 */
function setAverageSpeed(result)
{
	$('span#averagespeed').html(result.averagespeed);
}

/**
 * Zeichnet ein Kreis-Element aus den übergebenen Informationen.
 */
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

/**
 * Entfernt ein grafisches Element aus der Kartenansicht. Dazu sind zwei
 * Schritte notwendig: Zuerst wird das Element aus der Karte entfernt, indem
 * die Karten-Eigenschaft auf null gesetzt wird. Anschließend wird das Element
 * aus dem Array gelöscht.
 *
 * @param elementId: ID des zu entfernenden Elements
 */
function clearElement(elementId)
{
	elementsArray[elementId].setMap(null);
	delete markersArray[elementId];
}

/**
 * Entfernt alle grafischen Elemente aus der eingebetteten Karte.
 */
function clearAllElements()
{
	if (elementsArray)
	{
		for (index in elementsArray)
		{
			clearElement(index);
		}
	}
}

/**
 * Entfernt alle grafischen Elemente aus der Karte, die nicht mehr in der aktu-
 * ellen Liste einzuzeichnender Elemente vorhanden sind.
 *
 * @param elements: Liste jener Elemente, die noch in der Karte vorhanden sein
 * sollen
 */
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

/**
 * Empfängt eine Liste einzuzeichnender Elemente. Je nach Typ des grafischen
 * Elementes wird die weitere Bearbeitung an eine separate Funktion delegiert.
 *
 * @param elements: Liste einzuzeichnender Elemente
 */
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

/**
 * Stößt den Prozess der Aktualisierung der Live-Übersicht an, indem aktu-
 * elle Daten vom Server angefordert und zur Verarbeitung weitergereicht wer-
 * den.
 */
function refreshLivePage()
{
	$.ajax({
		url: '/mapapi/mapdata/' + citySlugString,
		success: refreshLivePage2
	});
}

/**
 * Diese Funktion empfängt die JSON-Antwort des Servers und delegiert die Ak-
 * tualisierung an verschiedene Unterfunktionen.
 *
 * @param result: JSON-Antwort des Servers
 */
function refreshLivePage2(result)
{
	refreshElements(result.elements);
	setLastModifiedLabel();
	setUsercounter(result);
	setAverageSpeed(result);
}

/**
 * Initialisiert die eingebettete Karte. Dazu werden einige Informationen aus
 * der Antwort des Servers übernommen, beispielsweise der Mittelpunkt und die
 * Zoom-Stufe der Kartenansicht.
 *
 * Anschließend werden die grafischen Elemente in die Karte eingebaut.
 *
 * @param result: JSON-Antwort des Servers
 */
function setMapOptions(result)
{
	var mapOptions = {
		zoom: result.zoom,
		center: new google.maps.LatLng(result.mapcenter.latitude, result.mapcenter.longitude),
		mapTypeId: google.maps.MapTypeId.TERRAIN,
		disableDefaultUI: true
	}

	map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

	refreshLivePage2(result);
}

/**
 * Initialisiert den Ablauf der Live-Seite. Primär werden hier Event-Handler festgelegt.
 */
function initializeLivePage()
{
	if ($('#map-canvas').length > 0)
	{
		$.ajax({
			type: 'GET',
			url: '/mapapi/mapdata/' + citySlugString,
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

/**
 * Stößt die Initialisierung der Live-Übersicht an und setzt gleichzeitig 
 * eine Intervallfunktion fest, mit der neue Daten regelmäßig geladen werden.
 */
function startInitialization()
{
	initializeLivePage();

	var timer = setInterval(refreshLivePage, 5000);
}

google.maps.event.addDomListener(window, 'load', startInitialization);
