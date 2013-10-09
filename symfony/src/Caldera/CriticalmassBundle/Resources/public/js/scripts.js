/**
 * Ruft die Liste der Staedte mit den Koordinaten der Position des Benutzers
 * auf, so dass die Liste nach der Entfernung der Staedte zu der angegebenen
 * Position sortiert wird. Anschliessend wird der Create-Trigger aufgerufen, um
 * die jQuery-mobile-Darstellung aufzurufen.
 *
 * @param position: Ergebnis der Geolocation-Abfrage
 */
function sidebarLocationSuccess(position)
{
	$('#rightpanel').load('/loadcities/', { 'latitude': position.coords.latitude, 'longitude': position.coords.longitude }, function()
	{
        sessionStorage.setItem('rightsidebarContent', $('#rightpanel').html());
		$('#rightpanel').trigger('create');
	});
}

/**
 * Wenn keine Geolocation-Daten bestimmt werden koennen, wird die Staedteliste
 * ohne Koordinaten aufgerufen, so dass die Resultate lediglich nach dem Alpha-
 * bet sortiert werden. Anschliessend wird der Create-Trigger aufgerufen, um
 * die jQuery-mobile-Darstellung aufzurufen.
 */
function sidebarLocationNoPosition()
{
	$('#rightpanel').load('/loadcities/', function()
	{
        sessionStorage.setItem('rightsidebarContent', $('#rightpanel').html());
		$('#rightpanel').trigger('create');
	});
}

/**
 * Beim Oeffnen einer Seite wird ein Event-Listener installiert, der das Oeff-
 * nen des rechten Panels abfaengt. Bevor das Panel geoeffnet wird, erfolgt ei-
 * ne Geolocation-Abfrage, um die einzublendende Staedte-Liste nach der Ent-
 * fernung der Staedte zum Standort des Benutzers zu sortieren.
 */
$(window).load(function()
{
	// Event-Listener eintragen
	$('#rightpanel').on('panelbeforeopen', function(event, ui)
	{
        if (sessionStorage.getItem('rightsidebarContent'))
        {
            $('#rightpanel').html(sessionStorage.getItem('rightsidebarContent'));
            $('#rightpanel').trigger('create');
        }
        else
		// Ist der Geolocation-Service verfuegbar?
		if (navigator.geolocation)
		{
			// dann die Staedte nach ihrer Entfernung zum Benutzer sortieren
			navigator.geolocation.getCurrentPosition(sidebarLocationSuccess, sidebarLocationNoPosition);
		}
		else
		{
			// ansonsten die nach dem Alphabet sortierte Liste ausgeben
			sidebarLocationNoPosition();
		}
	});
});
