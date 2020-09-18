import '../scss/criticalmass.scss';
import L from 'leaflet';
import polylineEncoded from 'polyline-encoded';

//window.bootstrap = bootstrap;
require('bootstrap');

document.addEventListener("DOMContentLoaded", function(){
    document.querySelectorAll('.map').forEach(function (mapContainer) {
        const map = new L.map(mapContainer.id);
        const basemap = L.tileLayer('https://tiles.caldera.cc/wikimedia-intl/{z}/{x}/{y}.png', {
            attribution: 'Wikimedia maps beta | Map data &copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
        });
        basemap.addTo(map);

        const mapCenterLatitude = mapContainer.dataset.mapCenterLatitude;
        const mapCenterLongitude = mapContainer.dataset.mapCenterLongitude;
        const mapZoomLevel = mapContainer.dataset.mapZoomlevel;

        if (mapCenterLatitude && mapCenterLongitude && mapZoomLevel) {
            const mapCenter = L.latLng(mapCenterLatitude, mapCenterLongitude);

            const marker = L.marker(mapCenter);
            map.setView(mapCenter, mapZoomLevel);
            marker.addTo(map);
        }

        var polylineString = mapContainer.dataset.polyline;
        var polylineColorString = mapContainer.dataset.polylineColor;

        if (polylineString && polylineColorString) {
            var polyline = L.Polyline.fromEncoded(polylineString, {color: polylineColorString});

            polyline.addTo(map);

            map.fitBounds(polyline.getBounds());
        }

        var citySlug = mapContainer.dataset.citySlug;
        var rideIdentifier = mapContainer.dataset.rideIdentifier;
        
        if (citySlug && rideIdentifier) {
            const rideRequest = new XMLHttpRequest();

            rideRequest.onreadystatechange = function() {
                if (rideRequest.readyState === 4) {
                    if (rideRequest.status === 200) {
                        const ride =  JSON.parse(rideRequest.responseText);

                        const rideLatLng = L.latLng(ride.latitude, ride.longitude);
                        map.setView(rideLatLng, 10);

                        const marker = L.marker(mapCenter);
                        map.setView(mapCenter, mapZoomLevel);
                        marker.addTo(map);
                    }
                }
            }

            const rideUrl = Routing.generate('caldera_criticalmass_rest_ride_show', { citySlug: citySlug, rideIdentifier: rideIdentifier });

            rideRequest.open('Get', rideUrl);
            rideRequest.send();

            const photoRequest = new XMLHttpRequest();

            photoRequest.onreadystatechange = function() {
                if (photoRequest.readyState === 4) {
                    if (photoRequest.status === 200) {
                        const ride =  JSON.parse(photoRequest.responseText);

                        const rideLatLng = L.latLng(ride.latitude, ride.longitude);
                        map.setView(rideLatLng, 10);

                        const marker = L.marker(mapCenter);
                        map.setView(mapCenter, mapZoomLevel);
                        marker.addTo(map);
                    }
                }
            }
    });
});

