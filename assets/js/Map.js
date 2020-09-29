import L from 'leaflet';
import polylineEncoded from 'polyline-encoded';
import markerCluster from 'leaflet.markercluster';
import extraMarkers from 'leaflet-extra-markers';

export default class Map {
    mapContainer;
    map;
    rideIcon = L.ExtraMarkers.icon({
        icon: 'fa-bicycle',
        markerColor: 'red',
        shape: 'circle',
        prefix: 'far'
    });
    photoIcon = L.ExtraMarkers.icon({
        icon: 'fa-camera',
        markerColor: 'yellow',
        shape: 'square',
        prefix: 'far'
    });

    constructor(mapContainer, options) {
        this.mapContainer = mapContainer;

        const defaults = {};

        this.settings = {...defaults, ...options};

        this.createMap();
        this.setViewByProvidedData();
        this.addProvidedPolyline();
        this.queryApi();
        this.loadRide();
        this.loadPhotos();
        this.loadTracks();
    }

    createMap() {
        this.map = new L.map(this.mapContainer.id);
        this.mapContainer.map = this.map;

        const basemap = L.tileLayer('https://tiles.caldera.cc/wikimedia-intl/{z}/{x}/{y}.png', {
            attribution: 'Wikimedia maps beta | Map data &copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
        });
        basemap.addTo(this.map);
    }

    setViewByProvidedData() {
        const mapCenterLatitude = this.mapContainer.dataset.mapCenterLatitude;
        const mapCenterLongitude = this.mapContainer.dataset.mapCenterLongitude;
        const mapZoomLevel = this.mapContainer.dataset.mapZoomlevel;

        if (mapCenterLatitude && mapCenterLongitude && mapZoomLevel) {
            const mapCenter = L.latLng(mapCenterLatitude, mapCenterLongitude);

            this.map.setView(mapCenter, mapZoomLevel);
        }
    }

    addProvidedPolyline() {
        const polylineString = this.mapContainer.dataset.polyline;
        const polylineColorString = this.mapContainer.dataset.polylineColor;

        if (polylineString && polylineColorString) {
            const polyline = L.Polyline.fromEncoded(polylineString, {color: polylineColorString});

            polyline.addTo(this.map);

            this.map.fitBounds(polyline.getBounds());
        }
    }

    queryApi() {
        const rideApiQuery = this.mapContainer.dataset.rideApiQuery;

        const that = this;

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

                            const marker = L.marker(rideLatLng, {icon: that.rideIcon});

                            marker.addTo(layer);
                        }

                        layer.addTo(that.map);
                        that.map.fitBounds(layer.getBounds());
                    }
                }
            }

            apiRequest.open('Get', rideApiQuery);
            apiRequest.send();
        }
    }

    loadRide() {
        const that = this;
        const citySlug = this.mapContainer.dataset.citySlug;
        const rideIdentifier = this.mapContainer.dataset.rideIdentifier;

        if (citySlug && rideIdentifier) {
            const rideRequest = new XMLHttpRequest();

            rideRequest.onreadystatechange = function () {
                if (rideRequest.readyState === 4) {
                    if (rideRequest.status === 200) {
                        const ride = JSON.parse(rideRequest.responseText);

                        const rideLatLng = L.latLng(ride.latitude, ride.longitude);

                        const marker = L.marker(rideLatLng, {icon: that.rideIcon});

                        that.map.setView(rideLatLng, 10);
                        marker.addTo(that.map);
                    }
                }
            }

            const rideUrl = Routing.generate('caldera_criticalmass_rest_ride_show', {
                citySlug: citySlug,
                rideIdentifier: rideIdentifier
            });

            rideRequest.open('Get', rideUrl);
            rideRequest.send();
        }
    }

    loadPhotos() {
        const that = this;
        const citySlug = this.mapContainer.dataset.citySlug;
        const rideIdentifier = this.mapContainer.dataset.rideIdentifier;

        if (citySlug && rideIdentifier) {
            const photoRequest = new XMLHttpRequest();

            photoRequest.onreadystatechange = function () {
                if (photoRequest.readyState === 4) {
                    if (photoRequest.status === 200) {
                        const photoList = JSON.parse(photoRequest.responseText);
                        const photoLayer = L.markerClusterGroup({
                            showCoverageOnHover: false,
                            iconCreateFunction: function (cluster) {
                                return that.photoIcon;
                            }
                        });

                        for (const i in photoList) {
                            const photo = photoList[i];

                            if (photo.latitude && photo.longitude) {
                                const photoLatLng = L.latLng(photo.latitude, photo.longitude);

                                const marker = L.marker(photoLatLng);
                                marker.addTo(photoLayer);
                            }
                        }

                        photoLayer.addTo(that.map);
                    }
                }
            }

            const photoUrl = Routing.generate('caldera_criticalmass_rest_photo_ridelist', {
                citySlug: citySlug,
                rideIdentifier: rideIdentifier
            });

            photoRequest.open('Get', photoUrl);
            photoRequest.send();
        }
    }

    loadTracks() {
        const that = this;
        const citySlug = this.mapContainer.dataset.citySlug;
        const rideIdentifier = this.mapContainer.dataset.rideIdentifier;

        if (citySlug && rideIdentifier) {
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

                        that.map.fitBounds(trackLayer.getBounds());
                        trackLayer.addTo(that.map);
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
    }

    addMarkerByNumber(markerNumber, mapContainer, markerLayer) {
        const markerLatitudePropertyName = 'mapMarker' + markerNumber + 'Latitude';
        const markerLongitudePropertyName = 'mapMarker' + markerNumber + 'Longitude';

        if (markerLatitudePropertyName in mapContainer.dataset && markerLongitudePropertyName in mapContainer.dataset) {
            const latitude = this.mapContainer.dataset[markerLatitudePropertyName];
            const longitude = this.mapContainer.dataset[markerLongitudePropertyName];

            if (markerNumber === '') {
                markerNumber = 0;
            }

            const markerLatLng = L.latLng(latitude, longitude);

            const marker = L.marker(markerLatLng, { icon: that.rideIcon });

            marker.markerNumber = markerNumber;

            marker.addTo(markerLayer);

            return true;
        }

        return false;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.map').forEach(function (mapContainer) {
        new Map(mapContainer);
    });
});
