import L from 'leaflet';
import polylineEncoded from 'polyline-encoded';
import markerCluster from 'leaflet.markercluster';
import extraMarkers from 'leaflet-extra-markers';

export default class Map {
    constructor(element, options) {
        const defaults = {};

        this.settings = {...defaults, ...options};

        this.init();
    }

    init() {
        document.querySelectorAll('.map').forEach(function (mapContainer) {
            const map = new L.map(mapContainer.id);
            mapContainer.map = map;

            const basemap = L.tileLayer('https://tiles.caldera.cc/wikimedia-intl/{z}/{x}/{y}.png', {
                attribution: 'Wikimedia maps beta | Map data &copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
            });
            basemap.addTo(map);

            const mapCenterLatitude = mapContainer.dataset.mapCenterLatitude;
            const mapCenterLongitude = mapContainer.dataset.mapCenterLongitude;
            const mapZoomLevel = mapContainer.dataset.mapZoomlevel;

            if (mapCenterLatitude && mapCenterLongitude && mapZoomLevel) {
                const mapCenter = L.latLng(mapCenterLatitude, mapCenterLongitude);
            }

            var polylineString = mapContainer.dataset.polyline;
            var polylineColorString = mapContainer.dataset.polylineColor;

            if (polylineString && polylineColorString) {
                var polyline = L.Polyline.fromEncoded(polylineString, {color: polylineColorString});

                polyline.addTo(map);

                map.fitBounds(polyline.getBounds());
            }

            const markerLayer = L.featureGroup();

            this.addMarkerByNumber('', mapContainer, markerLayer);

            let markerNumber = 1;
            let result = true;

            do {
                result = this.addMarkerByNumber(markerNumber, mapContainer, markerLayer);

                ++markerNumber;
            }
            while (result);

            if (markerLayer.getLayers().length > 0) {
                map.fitBounds(markerLayer.getBounds());
                markerLayer.addTo(map);
            }

            const rideApiQuery = mapContainer.dataset.rideApiQuery;

            if (rideApiQuery) {
                const apiRequest = new XMLHttpRequest();

                apiRequest.onreadystatechange = function () {
                    if (apiRequest.readyState === 4) {
                        if (apiRequest.status === 200) {
                            const rideList = JSON.parse(apiRequest.responseText);
                            const layer = L.featureGroup();

                            for (var i in rideList) {
                                const ride = rideList[i];
                                const rideLatLng = L.latLng(ride.latitude, ride.longitude);

                                const rideIcon = L.ExtraMarkers.icon({
                                    icon: 'fa-bicycle',
                                    markerColor: 'red',
                                    shape: 'circle',
                                    prefix: 'far'
                                });

                                const marker = L.marker(rideLatLng, {icon: rideIcon});

                                marker.addTo(layer);
                            }

                            layer.addTo(map);
                            map.fitBounds(layer.getBounds());
                        }
                    }
                }

                apiRequest.open('Get', rideApiQuery);
                apiRequest.send();
            }

            var citySlug = mapContainer.dataset.citySlug;
            var rideIdentifier = mapContainer.dataset.rideIdentifier;

            if (citySlug && rideIdentifier) {
                const rideRequest = new XMLHttpRequest();

                rideRequest.onreadystatechange = function () {
                    if (rideRequest.readyState === 4) {
                        if (rideRequest.status === 200) {
                            const ride = JSON.parse(rideRequest.responseText);

                            const rideLatLng = L.latLng(ride.latitude, ride.longitude);
                            map.setView(rideLatLng, 10);

                            const rideIcon = L.ExtraMarkers.icon({
                                icon: 'fa-bicycle',
                                markerColor: 'red',
                                shape: 'circle',
                                prefix: 'far'
                            });

                            const marker = L.marker(rideLatLng, {icon: rideIcon});

                            map.setView(rideLatLng, mapZoomLevel);
                            marker.addTo(map);
                        }
                    }
                }

                const rideUrl = Routing.generate('caldera_criticalmass_rest_ride_show', {
                    citySlug: citySlug,
                    rideIdentifier: rideIdentifier
                });

                rideRequest.open('Get', rideUrl);
                rideRequest.send();

                const photoRequest = new XMLHttpRequest();

                photoRequest.onreadystatechange = function () {
                    if (photoRequest.readyState === 4) {
                        if (photoRequest.status === 200) {
                            const photoList = JSON.parse(photoRequest.responseText);
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

                const photoUrl = Routing.generate('caldera_criticalmass_rest_photo_ridelist', {
                    citySlug: citySlug,
                    rideIdentifier: rideIdentifier
                });

                photoRequest.open('Get', photoUrl);
                photoRequest.send();

                const trackRequest = new XMLHttpRequest();

                trackRequest.onreadystatechange = function () {
                    if (trackRequest.readyState === 4) {
                        if (trackRequest.status === 200) {
                            const trackList = JSON.parse(trackRequest.responseText);

                            const trackLayer = L.featureGroup();

                            for (const i in trackList) {
                                const track = trackList[i];
                                const polylineString = track.polylineString;
                                const polylineColor = 'red';
                                const polyline = L.Polyline.fromEncoded(polylineString, {color: polylineColor});

                                polyline.addTo(trackLayer);
                            }

                            map.fitBounds(trackLayer.getBounds());
                            trackLayer.addTo(map);
                        }
                    }
                }

                const trackUrl = Routing.generate('caldera_criticalmass_rest_track_ridelist', {
                    citySlug: citySlug,
                    rideIdentifier: rideIdentifier
                });

                trackRequest.open('Get', trackUrl);
                trackRequest.send();
            }
        });
    }


    addMarkerByNumber(markerNumber, mapContainer, markerLayer) {
        const markerLatitudePropertyName = 'mapMarker' + markerNumber + 'Latitude';
        const markerLongitudePropertyName = 'mapMarker' + markerNumber + 'Longitude';

        if (markerLatitudePropertyName in mapContainer.dataset && markerLongitudePropertyName in mapContainer.dataset) {
            const latitude = mapContainer.dataset[markerLatitudePropertyName];
            const longitude = mapContainer.dataset[markerLongitudePropertyName];

            if (markerNumber === '') {
                markerNumber = 0;
            }

            const markerLatLng = L.latLng(latitude, longitude);

            const rideIcon = L.ExtraMarkers.icon({
                icon: 'fa-bicycle',
                markerColor: 'red',
                shape: 'circle',
                prefix: 'far'
            });

            const marker = L.marker(rideLatLng, { icon: rideIcon });

            marker.markerNumber = markerNumber;

            marker.addTo(markerLayer);

            return true;
        }

        return false;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const element = document.querySelector('.map');

    new Map(element);
});
