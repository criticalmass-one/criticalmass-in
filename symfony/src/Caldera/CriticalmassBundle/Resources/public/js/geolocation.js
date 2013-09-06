/**
 * Sendet die aktuelle Position des Clients an den Server. Es werden alle Daten
 * uebertragen, die in der Geolocation-Spezifikation vorgesehen sind, um die 
 * weitere Auswertung kuemmert sich der Server.
 *
 * @param position: Ergebnis der Geolocation-Abfrage
 */function sendPosition(position)
{
	$.ajax({
		type: 'GET',
		url: '/trackposition',
		data: {
			latitude: position.coords.latitude,
			longitude: position.coords.longitude,
			accuracy: position.coords.accuracy,
			altitude: position.coords.altitude,
			altitudeaccuracy: position.coords.altitudeAccurary,
			speed: position.coords.speed,
			heading: position.coords.heading,
			timestamp: position.coords.timestamp
		},
		cache: false,
		success: function(result) {

		}
	});
}

/**
 * Hier wird noch kurz abgefragt, ob der Benutzer ueberhaupt GPS-Daten senden
 * moechte oder diese Funktionalitaet abgeschaltet hat.
 */
function preparePositionSending()
{
	// Status der Geolocation-Uebertragung abfragen
	$.ajax({
		type: 'GET',
		url: '/settings/getgpsstatus',
		data: {
		},
		cache: false,
		success: function(result) {
			// sollen Daten gesendet werden?
			if (result.status == true)
			{
				// Unterstuetzt der Browser ueberhaupt die Geolocation-Dienste?
				if (navigator.geolocation)
				{
					// Position abfragen und an den Server senden lassen
					navigator.geolocation.getCurrentPosition(sendPosition);
				}
				else
				{
					//alert("Geolocation nicht möglich.");
				}
			}
		}
	});
}

/**
 * Diese Funktion stoesst das Senden der GPS-Position des Clients an. Dazu
 * wird zunaechst der Server befragt, ob der Benutzer Daten senden moechte so-
 * wie das Intervall fuer den naechsten Aufruf abgefragt. Anschliessend werden
 * die Geolocation-Daten uebertragen, der aktuelle Intervall-Timer geloescht
 * und ein neuer Timer mit dem Ergebnis-Intervall der Benutzerabfrage gestar-
 * tet. Diese Funktion ruft sich also in regelmaessigen Abstaenden selbst auf.
 */
function refreshInterval()
{
	// Intervall Abfragen
	$.ajax({
		type: 'GET',
		url: '/settings/getgpsinterval',
		data: {
		},
		cache: false,
		success: function(result) {
			// aus dem Ergebnis wird ein neuer Timer erstellt
			var timer = setInterval(function()
			{
				clearInterval(timer);

				if (result.interval > 0)
				{
					refreshInterval();
					preparePositionSending();
				}
			}, result.interval);
		}
	});
}

/**
 * Diese Funktion wird beim Laden der Seite aufgerufen und startet zum ersten
 * Mal das Intervall, an dessen Ende der Client seine Position an den Server
 * sendet.
 */
function initializeGeolocation()
{
	refreshInterval();
}

// Initialisierung des Geolocation-Services starten
google.maps.event.addDomListener(window, 'load', initializeGeolocation);
