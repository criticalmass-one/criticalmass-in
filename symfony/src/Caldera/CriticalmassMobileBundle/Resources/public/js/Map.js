Map = function(mapIdentifier, city)
{
    this.mapIdentifier = mapIdentifier;
    this.city = city;

    $.ajax({
        type: 'GET',
        url: 'http://www.criticalmass.local/app_dev.php/api/completemapdata/' + this.city.getCitySlug(),
        cache: false,
        context: this,
        success: this.setMapOptions
    });

    this.initEventListeners();
    this.timer = setInterval(this.getNewMapData, 5000);
};

Map.prototype.mapIdentifier = null;
Map.prototype.city = null;

Map.prototype.elementsArray = [];

Map.prototype.timer = null;

Map.prototype.tileLayerAddress = 'https://{s}.tiles.mapbox.com/v3/maltehuebner.i1c90m12/{z}/{x}/{y}.png';

Map.prototype.setMapOptions = function(ajaxResultData)
{
    this.map = L.map('map').setView(
        [ajaxResultData.mapCenter.latitude,
         ajaxResultData.mapCenter.longitude
        ],
        ajaxResultData.zoomFactor);

    L.tileLayer(this.tileLayerAddress, {
        attribution: '<a href="http://www.mapbox.com/about/maps/" target="_blank">Terms &amp; Feedback</a>'
    }).addTo(this.map);


    this.refreshMap(ajaxResultData);
};

Map.prototype.getNewMapData = function()
{
    $.ajax({
        type: 'GET',
        url: 'http://www.criticalmass.local/app_dev.php/api/completemapdata/' + this.city.getCitySlug(),
        cache: false,
        context: this,
        success: this.refreshMap
    });
};

Map.prototype.initEventListeners = function()
{
    this.map.on('dragstart', function()
    {
        $("select#flip-auto-center").val('off').slider('refresh');
    });

    $("#flip-gps-sender").on("slidestop", function(event, ui) {
        $.ajax({
            type: 'GET',
            url: '/app_dev.php/api/gpsstatus',
            data: {
                'status': $("select#flip-gps-sender")[0].selectedIndex
            },
            cache: false
        });
    } );
};

Map.prototype.refreshLabels = function(ajaxResultData)
{
    var d = new Date();

    $('p.lastmodified span#datetime').html(
        (d.getHours() < 10 ? '0' + d.getHours() : d.getHours()) + ':' +
            (d.getMinutes() < 10 ? '0' + d.getMinutes() : d.getMinutes()) + ':' +
            (d.getSeconds() < 10 ? '0' + d.getSeconds() : d.getSeconds()) + ' Uhr');

    $('span#usercounter').html(ajaxResultData.userOnline);

    $('span#averagespeed').html(ajaxResultData.averageSpeed);
};

Map.prototype.refreshMap = function(ajaxResultData)
{
    // grafische Elemente neu anordnen
    this.refreshElements(ajaxResultData.elements);

    this.refreshMapCenter(ajaxResultData);

    this.refreshLabels(ajaxResultData);
};

Map.prototype.refreshMapCenter = function()
{
    var autoCenter = $("select#flip-auto-center")[0].selectedIndex;

    if (autoCenter == 1)
    {
        map.panTo(new L.LatLng(result.mapCenter.latitude, result.mapCenter.longitude));
    }
};

Map.prototype.refreshElements = function(elements)
{
    if (elements)
    {
        // lösche alte Elemenete
        this.clearOldElements(elements);

        // JSON-Antwort durchgehen
        for (index in elements)
        {
            // Typ des Elementes auslesen
            var type = elements[index].type;

            // Kreis
            if (type == "circle")
            {
                this.drawCircle(elements[index]);
            }

            // Pfeil
            if (type == "arrow")
            {
                this.drawArrow(elements[index]);
            }

            if (elements[index].type == "positionmarker" || elements[index].type == "ridemarker" || elements[index].type == "citymarker")
            {
                this.drawMarker(elements[index]);
            }
        }
    }
};

Map.prototype.clearOldElements = function(elements)
{
    var index;

    for (index in this.elementsArray)
    {
        var found = false;
        var pos;

        for (pos in elements)
        {
            if (index == elements[pos].id)
            {
                found = true;
            }
        }

        if (!found)
        {
            this.clearElement(index);
        }
    }
};

Map.prototype.clearAllElements = function()
{
    if (this.elementsArray)
    {
        for (index in this.elementsArray)
        {
            this.clearElement(index);
        }
    }
};

Map.prototype.clearElement = function(elementId)
{
    this.map.removeLayer(elementsArray[elementId]);
    delete this.elementsArray[elementId];
};

Map.prototype.doesElementExist = function(elementId)
{
    var found = false;

    var index;

    for (index in this.elementsArray)
    {
        if (index == elementId)
        {
            found = true;
            break;
        }
    }

    return found;
};

Map.prototype.drawMarker = function(markerElement)
{
    if (!this.doesElementExist(markerElement.id))
    {
        if (markerElement.type == 'citymarker')
        {
            this.elementsArray[markerElement.id] = L.marker([markerElement.centerPosition.latitude,
                                                             markerElement.centerPosition.longitude],
                                                             { riseOnHover: true }
                                                            ).addTo(this.map)
                                                             .bindPopup(markerElement.cityTitle);

            this.elementsArray[markerElement.id].on('click', function(){
                //switchCityContext(markerElement.citySlug);
            })
        }

        if (markerElement.type == 'positionmarker')
        {
            var popupContent = '<section class="position"><h2>' + markerElement.username + '</h2><p>' + markerElement.description + '</p></section>';

            this.elementsArray[markerElement.id] = L.marker([markerElement.centerPosition.latitude,
                                                             markerElement.centerPosition.longitude],
                                                             { riseOnHover: true }
                                                            ).addTo(this.map)
                                                             .bindPopup(popupContent);
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

            var criticalmassIcon = L.icon({
                iconUrl: '/bundles/calderacriticalmasscore/images/marker/criticalmassblue.png',
                iconSize: [25, 41],
                iconAnchor: [13, 41],
                popupAnchor: [0, -36],
                shadowUrl: '/bundles/calderacriticalmasscore/images/marker/defaultshadow.png',
                shadowSize: [41, 41],
                shadowAnchor: [13, 41]
            });

            this.elementsArray[markerElement.id] = L.marker([markerElement.centerPosition.latitude,
                                                             markerElement.centerPosition.longitude],
                                                             { riseOnHover: true,
                                                               icon: criticalmassIcon }
                                                            ).addTo(this.map)
                                                             .bindPopup(popupContent);

            if (markerElement.popup)
            {
                this.elementsArray[markerElement.id].openPopup();
            }
        }
    }
};

Map.prototype.drawCircle = function(circleElement)
{
    if (!this.doesElementExist(circleElement.id))
    {
        var circleOptions = {
            color: circleElement.strokeColor,
            fillColor: circleElement.fillColor,
            opacity: circleElement.strokeOpacity,
            fillOpacity: circleElement.fillOpacity,
            weight: circleElement.strokeWeight
        };

        this.elementsArray[circleElement.id] = L.circle([
                                                         circleElement.centerPosition.latitude,
                                                         circleElement.centerPosition.longitude
                                                        ],
                                                        circleElement.radius, circleOptions).addTo(this.map);
    }
};