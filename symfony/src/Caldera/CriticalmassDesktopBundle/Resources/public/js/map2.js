/**
 * Enthält während des Betriebs der Live-Seite die Instanz der eingebetteten Karte.
 */
var map;

/**
 * Array aller in der Karte enthaltenen grafischen Elemente.
 */
var elementsArray = [];

function switchCityContext(citySlug, cityTitle)
{
    $('a#currentCitySlugButton').html(cityTitle);
    citySlugString = citySlug;

    refreshLivePage();
}

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
    $('span#usercounter').html(result.userOnline);
}

/**
 * Aktualisiert die Aufschrift der Durchschnittsgeschwindigkeit auf den Wert
 * aus dem Resultat-Array.
 */
function setAverageSpeed(result)
{
    $('span#averagespeed').html(result.averageSpeed);
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
/*
function resizeImagesToZoomLevel(zoomLevel)
{
    var index;

    for (index in elementsArray)
    {
        if (elementsArray[index].type == "image")
        {
            clearElement(elementsArray[index].id);
        }
    }
}*/

function drawImage(imageElement)
{
    if (!doesElementExist(imageElement.id))
    {
        var imageUrl = imageElement.imagePath;

        var ratio = imageElement.imageWidth / imageElement.imageHeight;

        var showWidth = 10;
        var showHeight = showWidth / ratio;
        var scaleFactor = 0.00005;
        var gpsHeightCorrection = 3.7;

        var imageBounds = [[imageElement.centerPosition.latitude, imageElement.centerPosition.longitude],
                           [imageElement.centerPosition.latitude + showWidth * scaleFactor, imageElement.centerPosition.longitude + showHeight * scaleFactor * gpsHeightCorrection]];

        elementsArray[imageElement.id] = L.imageOverlay(imageUrl, imageBounds).addTo(map);
    }
}

function drawMarker(markerElement)
{
    if (!doesElementExist(markerElement.id))
    {
        if (markerElement.type == 'citymarker')
        {
            elementsArray[markerElement.id] = L.marker(
                [markerElement.centerPosition.latitude, markerElement.centerPosition.longitude],
                { riseOnHover: true }).addTo(map).bindPopup('fooobaaaaarbaaaaaz');

            elementsArray[markerElement.id].on('click', function () {
                switchCityContext(markerElement.citySlug, markerElement.cityTitle);
            });
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
            else
            {
                popupContent += '<span>Der Treffpunkt ist noch nicht bekannt.</span>';
            }

            popupContent += '<time>Datum: ' + markerElement.date + '</time>';

            if (markerElement.hasTime)
            {
                popupContent += '<time>Uhrzeit: ' + markerElement.time + '</time>';
            }

            popupContent += '</section>';

            elementsArray[markerElement.id] = L.marker([markerElement.centerPosition.latitude, markerElement.centerPosition.longitude], { riseOnHover: true }).addTo(map).bindPopup(popupContent);

            if (markerElement.popup)
            {
                elementsArray[markerElement.id].openPopup();
            }
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
            if (index == elements[pos].id)
            {
                found = true;
                break;
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

            if (elements[index].type == "image")
            {
                drawImage(elements[index]);
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

    refreshMapCenter(result);
}

function refreshMapCenter(result)
{
    var autoCenter = $("select#flip-auto-center")[0].selectedIndex;

    if (autoCenter == 1)
    {
        map.panTo(new L.LatLng(result.mapCenter.latitude, result.mapCenter.longitude));
    }
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
    map = L.map('map').setView([result.mapCenter.latitude, result.mapCenter.longitude], result.zoomFactor);

    // add an OpenStreetMap tile layer
    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 18
    }).addTo(map);

/*    map.on("zoomend", function(){
        resizeImagesToZoomLevel(map.getZoom());
    });*/

    refreshLivePage2(result);
}

/**
 * Initialisiert den Ablauf der Live-Seite. Primär werden hier Event-Handler festgelegt.
 */
function initializeLivePage()
{
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

window.onload = function()
{
    startMapInitialization();
    initializeLivePage();
}