/**
 * Speichert eine Instanz des Geocoding-Services ab.
 */
var geocoder;

/**
 * Speichert die Instanz einer eingebetteten Karte ab.
 */
var map;

/**
 * Speichert die Instanz des Markers ab, der das Ergebnis auf der Karte an-
 * zeigt.
 */
var marker;

/**
 * Initialisiert die Geocoding-Funktion und laedt eine Instanz der Google-Geo-
 * coding-API. Wenn bereits Werte in den Eingabefeldern fuer Laengen- und Brei-
 * tengrad stehen, wird die Karte auf diese Position zentriert. Ansonsten wird
 * der Mittelpunkt der Stadt abgefragt und auf der Karte angezeigt.
 */
function initializeGeocoding() {
	// Geocoding-API laden
	geocoder = new google.maps.Geocoder();

	// Speicher fuer eine Koordinate
	var latlng;

	// sind in den Eingabefeldern sinnvolle Werte enthalten?
	if ($('#caldera_criticalmassbundle_ridetype_latitude').val() != '0' &&
			$('#caldera_criticalmassbundle_ridetype_longitude').val() != '0')
  {
		// neue Koordinate erstellen
		latlng = new google.maps.LatLng(
			$('#caldera_criticalmassbundle_ridetype_latitude').val(),
			$('#caldera_criticalmassbundle_ridetype_longitude').val()
		);

		// Karte mit diesen Mittelpunkt erstellen
		initMap(latlng);

		// Marker auf diese Position setzen
		placeNewMarker(latlng);
	}
	else
	{
		// Mittelpunkt der ausgewaehlten Stadt abfragen
		$.ajax({
			type: 'GET',
			url: '/mapapi/getcitylocation/' + citySlugString,
			data: {
			},
			success: function(result)
			{
				// Mittelpunkt und Karte anzeigen
				latlng = new google.maps.LatLng(result.latitude, result.longitude);
				initMap(latlng);
			}
		});
	}

	// Event-Listener fuer das Auswahlfeld der Staedte hinzufuegen
	$('#caldera_criticalmassbundle_ridetype_city').change(function()
	{
		// ID der Stadt auslesen
		var cityId = $('#caldera_criticalmassbundle_ridetype_city').val();

		// Karte auf den Mittelpunkt der gewaehlten Stadt skalieren
		centerMapToLngLat(cityId);
	});

	// Event-Listener fuer die Codierung einer eingebeben Adresse initialisieren
	$('#caldera_criticalmassbundle_ridetype_mapLocation').change(function()
	{
		codeAddress();
	});

	// beim Aendern des Breitengrades Karte neu zentrieren
	$('#caldera_criticalmassbundle_ridetype_latitude').change(function()
	{
		locateAddress();
	});

	// beim Aendern des Laengengrades Karte neu zentrieren
	$('#caldera_criticalmassbundle_ridetype_longitude').change(function()
	{
		locateAddress();
	});
}

/**
 * Zentriert die eingebettete Karte zum Mittelpunkt der im Parameter angegeben-
 * en Stadt.
 *
 * @param cityId: Numerische ID der Stadt
 */
function centerMapToLngLat(cityId)
{
	$.ajax({
		type: 'GET',
		url: '/mapapi/getcitylocationbyid/' + cityId,
		data: {
		},
		success: function(result)
		{
			// aus der Antwort ein latlng-Objekt konstruieren
			var latlng = new google.maps.LatLng(result.latitude, result.longitude);

			// Karte auf den Mittelpunkt zentrieren
			map.setCenter(latlng);
		}
	});
}

/**
 * Initialisiert die eingebettete Karte. Ueber das latlng-Objekt des Parameters
 * wird der Mittelpunkt berechnet.
 *
 * @param latlng: Mittelpunkt der zu konstruierenden Karte
 */
function initMap(latlng)
{
	var mapOptions = {
		zoom: 12,
		center: latlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	}

	// Karte erstellen
	map = new google.maps.Map(document.getElementById('geocoding-map'), mapOptions);
}

/**
 * Plaziert ein neues Marker-Objekt auf der Karte, das den potenziellen Treff-
 * punkt visualisiert. Die Position des Markers wird mit dem Parameter fest-
 * gelegt, ein bereits existierender Marker wird zuvor entfernt.
 *
 * @param location: latlng-Angabe der Position des Markers
 */
function placeNewMarker(location)
{
	// existiert bereits ein Marker?
	if (marker)
	{
		// Marker von der Karte entfernen
		marker.setMap(null);
	}

	// Karte auf die Position des Markers zentrieren
	map.setCenter(location);

	// neuen Marker fuer die uebergebene Koordinate erstellen
	marker = new google.maps.Marker({
		map: map,
		position: location,
		draggable: true
	});

	// Event-Listener fuer das Verschieben des Markers hinzufuegen
	google.maps.event.addListener(marker, 'drag', markerListener);
}

/**
 * Diese Funktion dient als Event-Listener und wird aufgerufen, sobald der Mar-
 * ker per Drag and Drop bewegt wird. Sie uebertraegt anschliessend die neuen
 * Koordinaten des Markers in die Eingabefelder fuer Breiten- und Laengengrad.
 */
function markerListener()
{
	// Position ermitteln
	var latlng = marker.getPosition();

	// Koordinaten in Eingabefelder uebertragen
	$('#caldera_criticalmassbundle_ridetype_latitude').val(latlng.lat());
	$('#caldera_criticalmassbundle_ridetype_longitude').val(latlng.lng());
}

/**
 * Codiert die eingegebene Adresse mit der Geocoding-API, setzt den Marker auf
 * die Ergebnis-Koordinaten und schreibt Laengen- und Breitengrad zusaetzlich
 * in die jeweiligen Eingabefelder.
 */
function codeAddress() {
	// Adresse als Zeichenkette auslesen
	var address = $('#caldera_criticalmassbundle_ridetype_mapLocation').val();

	// Adresse an den Geocoding-Service senden
	geocoder.geocode( { 'address': address}, function(results, status)
	{
		if (status == google.maps.GeocoderStatus.OK)
		{
			// Marker auf die Ergebnis-Koordinate setzen
			placeNewMarker(results[0].geometry.location);

			// Koordinaten ausserdem in die Eingabefelder uebertragen
			$('#caldera_criticalmassbundle_ridetype_latitude').val(results[0].geometry.location.lat());
			$('#caldera_criticalmassbundle_ridetype_longitude').val(results[0].geometry.location.lng());
		}
	});
}

/**
 * Wird bei Aenderungen in den Eingabefeldern von Laengen- und Breitengrad auf-
 * gerufen, erzeugt aus deren Eingaben eine neue Koordinate und setzt den Mar-
 * ker auf diese Koordinate.
 */
function locateAddress() {
	var address = $('#caldera_criticalmassbundle_ridetype_mapLocation').val();

	// Koordinate konstruieren
	var location = new google.maps.LatLng(
		$('#caldera_criticalmassbundle_ridetype_latitude').val(),
		$('#caldera_criticalmassbundle_ridetype_longitude').val()
	);

	// Marker an diese Position setzen
	placeNewMarker(location);
}

// Initialisierung starten
google.maps.event.addDomListener(window, 'load', initializeGeocoding);
