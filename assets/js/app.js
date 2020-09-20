import '../scss/criticalmass.scss';
import L from 'leaflet';
import polylineEncoded from 'polyline-encoded';
import markerCluster from 'leaflet.markercluster';
import extraMarkers from 'leaflet-extra-markers';

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

                        const marker = L.marker(rideLatLng);
                        map.setView(rideLatLng, mapZoomLevel);
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
                        const photoList =  JSON.parse(photoRequest.responseText);
                        const photoLayer = L.markerClusterGroup({
                            showCoverageOnHover: false,
                            iconCreateFunction: function (cluster) {
                                return L.ExtraMarkers.icon({
                                    icon: 'fa-camera',
                                    markerColor: 'yellow',
                                    shape: 'square',
                                    prefix: 'far'
                                });
                            }
                        });

                        for (const i in photoList) {
                            const photo = photoList[i];

                            if (photo.latitude && photo.longitude) {
                                const photoLatLng = L.latLng(photo.latitude, photo.longitude);

                                const marker = L.marker(photoLatLng);
                                map.setView(photoLatLng, mapZoomLevel);
                                marker.addTo(photoLayer);
                            }
                        }

                        photoLayer.addTo(map);
                    }
                }
            }

            const photoUrl = Routing.generate('caldera_criticalmass_rest_photo_ridelist', { citySlug: citySlug, rideIdentifier: rideIdentifier });

            photoRequest.open('Get', photoUrl);
            photoRequest.send();

            const trackRequest = new XMLHttpRequest();

            trackRequest.onreadystatechange = function() {
                if (trackRequest.readyState === 4) {
                    if (trackRequest.status === 200) {
                        const trackList =  JSON.parse(trackRequest.responseText);

                        for (const i in trackList) {
                            const track = trackList[i];
                            const polylineString = track.polylineString;
                            const polylineColor = 'red';
                            const polyline = L.Polyline.fromEncoded(polylineString, { color: polylineColor });

                            polyline.addTo(map);
                        }
                    }
                }
            }

            const trackUrl = Routing.generate('caldera_criticalmass_rest_track_list', { citySlug: citySlug, rideIdentifier: rideIdentifier });

            trackRequest.open('Get', trackUrl);
            trackRequest.send();
        }
    });
});

