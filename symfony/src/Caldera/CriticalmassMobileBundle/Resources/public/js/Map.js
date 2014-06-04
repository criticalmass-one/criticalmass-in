Map = function(mapIdentifier, city, parentPage)
{
    this.mapIdentifier = mapIdentifier;
    this.city = city;
    this.parentPage = parentPage;

    this.initMap();
    this.drawCityMarkers();
    this.drawPositions();
    /*
    $.ajax({
        type: 'GET',
        url: UrlFactory.getApiPrefix() + 'completemapdata/' + this.parentPage.getCitySlug(),
        cache: false,
        context: this,
        success: this.setMapOptions
    });*/
/*
    var this2 = this;

    this.timer = setInterval(function()
    {
        this2.getNewMapData();
    }, 5000);*/
};
Map.prototype.positionsArray = [];

Map.prototype.drawPositions = function()
{
    function callback(ajaxResultData)
    {
        for (index in ajaxResultData)
        {
            var user = ajaxResultData[index];

            var userColor = 'rgb(' + user.colorRed + ', ' + user.colorGreen + ', ' + user.colorBlue + ')';

            var circleOptions = {
                color: userColor,
                fillColor: userColor,
                opacity: 80,
                fillOpacity: 50,
                weight: 1
            };

            var circle = L.circle([user.latitude, user.longitude], 25, circleOptions);
            circle.addTo(this.map);

            this.positionsArray[user] = circle;
        }
    }

    $.support.cors = true;
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: UrlFactory.getNodeJSApiPrefix() + '?action=fetch&rideId=1',
        cache: false,
        context: this,
        crossDomain: true,
        success: callback
    });
}


Map.prototype.cityMarkersArray = [];

Map.prototype.drawCityMarkers = function()
{
    var criticalmassIcon = L.icon({
        iconUrl: '/bundles/calderacriticalmasscore/images/marker/criticalmassblue.png',
        iconSize: [25, 41],
        iconAnchor: [13, 41],
        popupAnchor: [0, -36],
        shadowUrl: '/bundles/calderacriticalmasscore/images/marker/defaultshadow.png',
        shadowSize: [41, 41],
        shadowAnchor: [13, 41]
    });

    var cities = CityFactory.getAllCities();

    for (slug in cities)
    {
        var city = cities[slug];

        var marker = L.marker([city.getLatitude(), city.getLongitude()], { riseOnHover: true, icon: criticalmassIcon });
        marker.bindPopup(city.getTitle());
        marker.addTo(this.map);

        this.cityMarkersArray[slug] = marker;
        /*
        this.cityMarkersArray[markerElement.id] = L.marker([markerElement.centerPosition.latitude,
            markerElement.centerPosition.longitude],
            { riseOnHover: true }
        ).addTo(this.map)
            .bindPopup(markerElement.cityTitle);

        this.elementsArray[markerElement.id].on('click', function(e) {
            this.switchCity(markerElement.citySlug);
        }, this);*/
    }
};

Map.prototype.initMap = function()
{
    this.map = L.map('map');

    if (this.hasOverridingMapPosition())
    {
        var mapPositon = this.getOverridingMapPosition();

        this.map.setView([mapPositon.latitude,
            mapPositon.longitude],
            mapPositon.zoomFactor);

        $("select#flip-auto-center").val('off').slider('refresh');
    }
    else
    {
        this.map.setView([53.5496385, 9.9625133], 16);
    }


    L.tileLayer('https://{s}.tiles.mapbox.com/v3/maltehuebner.i1c90m12/{z}/{x}/{y}.png', {
        attribution: '<a href="http://www.mapbox.com/about/maps/" target="_blank">Terms &amp; Feedback</a>'
    }).addTo(this.map);

    //L.tileLayer('http://www.criticalmass.local/images/heatmap/8ef69b5739bc4fb6c06c7ba476bbe004/{z}/{x}/{y}.png').addTo(this.map);
};

Map.prototype.parentPage = null;
Map.prototype.mapIdentifier = null;

Map.prototype.elementsArray = [];

Map.prototype.timer = null;

Map.prototype.getOverridingMapPosition = function()
{
    var address = new String(window.location);
    var addressArray = address.split('/');
    var tail = addressArray.pop();

    if (tail.indexOf('@') > 0)
    {
        var positionString = tail.split('@').pop();
        var positionArray = positionString.split(',');

        var mapPosition = { latitude: positionArray.shift(), longitude: positionArray.shift(), zoomFactor: positionArray.shift() };

        return mapPosition;
    }

    return null;
}

Map.prototype.hasOverridingMapPosition = function()
{
    var address = new String(window.location);

    return address.indexOf('@') > 0;
}

Map.prototype.refreshOverridingMapPosition = function()
{
    var address = new String(window.location);
    var addressArray = address.split('/');
    var tail = addressArray.pop();

    tail = tail.slice(tail.indexOf('@'), tail.indexOf('#'));

    var positionString = this.map.getCenter().lat + "," + this.map.getCenter().lng + "," + this.map.getZoom();
    tail = positionString + tail;

    addressArray.push(tail);

    address = addressArray.join();

    window.location = '#mapPage@' + positionString;
}

Map.prototype.initMapEventListeners = function(ajaxResultData)
{
    var this2 = this;

    this.map.on('dragend', function()
    {
        this2.refreshOverridingMapPosition();
    });

    this.map.on('zoomend', function()
    {
        this2.refreshOverridingMapPosition();
    });

    this.map.on('dragstart', function()
    {
        $("select#flip-auto-center").val('off').slider('refresh');
    });

    $("#flip-gps-sender").on("slidestop", function(event, ui) {
        $.ajax({
            type: 'GET',
            url: UrlFactory.getApiPrefix() + 'gpsstatus',
            data: {
                'status': $("select#flip-gps-sender")[0].selectedIndex
            },
            cache: false
        });
    } );

    this.initMapPageEventListeners();
};

Map.prototype.getNewMapData = function()
{
    $.ajax({
        type: 'GET',
        //url: UrlFactory.getApiPrefix() + 'completemapdata/' + this.parentPage.getCitySlug(),
        url: UrlFactory.getNodeJSApiPrefix() + '?action=fetch&rideId=1',
        cache: false,
        context: this,
        success: this.refreshMap
    });
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
    this.map.removeLayer(this.elementsArray[elementId]);
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
    var this2 = this;

    if (!this.doesElementExist(markerElement.id))
    {
        if (markerElement.type == 'citymarker')
        {
            this.elementsArray[markerElement.id] = L.marker([markerElement.centerPosition.latitude,
                markerElement.centerPosition.longitude],
                { riseOnHover: true }
            ).addTo(this.map)
                .bindPopup(markerElement.cityTitle);

            this.elementsArray[markerElement.id].on('click', function(e) {
                this.switchCity(markerElement.citySlug);
            }, this);
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

            if (markerElement.citySlug == this.parentPage.getCitySlug() && !this.hasOverridingMapPosition())
            {
                this.elementsArray[markerElement.id].openPopup();
            }

            this.elementsArray[markerElement.id].on('click', function(e) {
                this.switchCity(markerElement.citySlug);
            }, this);
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

Map.prototype.switchCity = function(newCitySlug)
{
    this.parentPage.switchCityBySlug(newCitySlug);

    this.getNewMapData();
};