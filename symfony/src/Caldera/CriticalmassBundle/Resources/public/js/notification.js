/**
 * Diese Funktion oeffnet ein Popup-Fenster, in dem der Versand der Benachrich-
 * tigungen noch einmal bestaetigt werden muss, um versehentliche Sendungen zu
 * unterbinden. Als Parameter wird der Typ der Benachrichtigung angegeben, der
 * versendet werden soll.
 *
 * @param type: Typ der Benachrichtigung
 */
function processNotification(type)
{
	// oeffnet das jeweilige Popup-Fenster
	$('#confirmationpopup' + type).popup('open');
}

/**
 * Haengt Event-Listener an die jeweiligen Knoepfe an, die spaeter das Popup-
 * fenster oeffnen.
 */
function initialize()
{
	// Ride-Notifications
	$('#notify_ride').click(function()
	{
		processNotification('ride');
	});

	// Uhrzeit-Notifications
	$('#notify_time').click(function()
	{
		processNotification('time');
	});

	// Location-Notifications
	$('#notify_location').click(function()
	{
		processNotification('location');
	});
}

// Initialisierung einleiten
google.maps.event.addDomListener(window, 'load', initialize);
