/**
 * Initialisiert eine Abfrage nach dem Treffpunkt einer Tour, um ihn auf der
 * eingebetteten Karte einblenden zu koennen.
 */
function initializeInformation() {
	// Position des Treffpunktes abfragen
	$.ajax({
		type: 'GET',
		url: '/mapapi/getridelocation/' + citySlugString,
		data: {
		},
		// Treffpunkt auf der Karte eintragen
		success: function(result) {
			// latlng-Objekt initialisieren
			var latlng = new google.maps.LatLng(result.latitude, result.longitude);

			// Optionen fuer die Kartendarstellung festlegen, zentriert auf den Treffpunkt
			var mapOptions = {
				zoom: 12,
				center: latlng,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}

			// aus den Daten eine neue Karte generieren
			var map = new google.maps.Map(document.getElementById('information-map'), mapOptions);

			// Marker an die Position des Treffpunktes setzen
			var marker = new google.maps.Marker({
				map: map,
				position: latlng
		  });
		}
	});
}

// eingebettete Karte initialisieren
google.maps.event.addDomListener(window, 'load', initializeInformation);
