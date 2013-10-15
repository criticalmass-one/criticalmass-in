var map;
var elementsArray = [];
var infoWindowArray = [];

function addLoadedMarkers(result)
{
    for (cityId in result.cities)
    {
        var city = result.cities[cityId];

        L.marker([city.center.latitude, city.center.longitude], { riseOnHover: true }).addTo(map)
            .bindPopup('<h2>' + city.title + '</h2><p>' + city.description + '</p>');
    }
}

window.onload = function()
{
    // create a map in the "map" div, set the view to a given place and zoom
    map = L.map('map').setView([53, 9], 5);

    // add an OpenStreetMap tile layer
    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 18
    }).addTo(map);


    $.ajax({
        type: 'GET',
        url: '/api/listcities',
        cache: false,
        success: addLoadedMarkers
    });
}