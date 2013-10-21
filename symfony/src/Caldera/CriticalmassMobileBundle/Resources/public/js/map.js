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
        var circleOptions = {
            color: circleElement.strokeColor,
            fillColor: circleElement.fillColor,
            opacity: circleElement.strokeOpacity,
            fillOpacity: circleElement.fillOpacity,
            weight: circleElement.strokeWeight
        };

        elementsArray[circleElement.id] = L.circle([circleElement.centerPosition.latitude, circleElement.centerPosition.longitude], circleElement.radius, circleOptions).addTo(map);
    }
}

function drawArrow(arrowElement)
{/*
    if (!doesElementExist(arrowElement.id))
    {
        var lineCoordinates = [
            new google.maps.LatLng(arrowElement.fromPosition.latitude, arrowElement.fromPosition.longitude),
            new google.maps.LatLng(arrowElement.toPosition.latitude, arrowElement.toPosition.longitude)
        ];

        var lineSymbol = {
            path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW
        };

        elementsArray[arrowElement.id] = new google.maps.Polyline({
            path: lineCoordinates,
            icons: [{
                icon: lineSymbol,
                offset: '100%'
            }],
            map: map
        });
    }*/
}

function drawMarker(markerElement)
{
    if (!doesElementExist(markerElement.id))
    {
        if (markerElement.type == 'citymarker')
        {
            elementsArray[markerElement.id] = L.marker([markerElement.centerPosition.latitude, markerElement.centerPosition.longitude], { riseOnHover: true }).addTo(map).bindPopup('fooobaaaaarbaaaaaz');
        }

        if (markerElement.type == 'positionmarker')
        {
            var popupContent = '<section class="position"><h2>' + markerElement.username + '</h2><p>' + markerElement.description + '</p></section>';

            elementsArray[markerElement.id] = L.marker([markerElement.centerPosition.latitude, markerElement.centerPosition.longitude], { riseOnHover: true }).addTo(map).bindPopup(popupContent);
        }

        if (markerElement.type == 'ridemarker')
        {
            var popupContent = '<section class="ride"><h2>' + markerElement.title + '</h2>';

            if (markerElement.hasLocation)
            {
                popupContent += '<address>' + markerElement.location + '</address>';
            }

            popupContent += '<time>Datum: ' + markerElement.date + '</time>';

            if (markerElement.hasTime)
            {
                popupContent += '<time>Uhrzeit: ' + markerElement.time + '</time>';
            }

            popupContent += '</section>';

            elementsArray[markerElement.id] = L.marker([markerElement.centerPosition.latitude, markerElement.centerPosition.longitude], { riseOnHover: true }).addTo(map).bindPopup(popupContent).openPopup();
        }
    }
}

function doesElementExist(elementId)
{
    var found = false;

    var index;

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
    map.removeLayer(elementsArray[elementId]);
    delete elementsArray[elementId];
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
    var index;

    for (index in elementsArray)
    {
        var found = false;
        var pos;

        for (pos in elements)
        {
            //alert("Vergleiche " + index + " und " + elements[pos].id);
            if (index == elements[pos].id)
            {
                found = true;
            }
        }

        if (!found)
        {
            clearElement(index);
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
        // lösche alte Elemenete
        clearOldElements(elements);

        // JSON-Antwort durchgehen
        for (index in elements)
        {
            // Typ des Elementes auslesen
            var type = elements[index].type;

            // Kreis
            if (type == "circle")
            {
                drawCircle(elements[index]);
            }

            // Pfeil
            if (type == "arrow")
            {
                drawArrow(elements[index]);
            }

            if (elements[index].type == "positionmarker" || elements[index].type == "ridemarker" || elements[index].type == "citymarker")
            {
                drawMarker(elements[index]);
            }
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
    // grafische Elemente neu anordnen
    refreshElements(result.elements);

    // Anzeige der letzten Änderung aktualisieren
    setLastModifiedLabel();

    // Benutzerzähler aktualisieren
    setUsercounter(result);

    // Geschwindigkeitsanzeige aktualisieren
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
    // create a map in the "map" div, set the view to a given place and zoom
    map = L.map('map').setView([result.mapCenter.latitude, result.mapCenter.longitude], result.zoom);

    // add an OpenStreetMap tile layer
    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 18
    }).addTo(map);

    refreshLivePage2(result);
}

/**
 * Initialisiert den Ablauf der Live-Seite. Primär werden hier Event-Handler festgelegt.
 */
function initializeLivePage()
{/*

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
    } );*/
}

/**
 * Stößt die Initialisierung der Live-Übersicht an und setzt gleichzeitig
 * eine Intervallfunktion fest, mit der neue Daten regelmäßig geladen werden.
 */
function startMapInitialization()
{
    $.ajax({
        type: 'GET',
        url: '/mapapi/mapdata/' + citySlugString,
        cache: false,
        success: setMapOptions
    });

    var timer = setInterval(refreshLivePage, 5000);
}

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
function refreshGeolocationInterval()
{
    // Intervall abfragen
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
                    refreshGeolocationInterval();
                    preparePositionSending();
                }
            }, result.interval);
        }
    });
}

window.onload = function()
{
    startMapInitialization();
    refreshGeolocationInterval();
}