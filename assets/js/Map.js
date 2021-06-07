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
    cityIcon = L.ExtraMarkers.icon({
        icon: 'fa-university',
        markerColor: 'blue',
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
        const apiQueryUrl = this.mapContainer.dataset.apiQuery;
        const dataType = this.mapContainer.dataset.apiType;
        const dataIcon = this.getIconForType(dataType);
        const that = this;

        if (apiQueryUrl) {
            fetch(apiQueryUrl).then((response) => {
                return response.json();
            }).then((resultList) => {
                const layer = L.featureGroup();

                for (var i in resultList) {
                    const data = resultList[i];
                    const dataLatLng = L.latLng(data.latitude, data.longitude);
                    const marker = L.marker(dataLatLng, {icon: dataIcon});

                    marker.addTo(layer);
                }

                layer.addTo(that.map);
                that.map.fitBounds(layer.getBounds());
            }).catch(function (err) {
                console.warn(err);
            });
        }
    }

    loadRide() {
        const that = this;
        const citySlug = this.mapContainer.dataset.citySlug;
        const rideIdentifier = this.mapContainer.dataset.rideIdentifier;

        if (citySlug && rideIdentifier) {
            const rideUrl = Routing.generate('caldera_criticalmass_rest_ride_show', {
                citySlug: citySlug,
                rideIdentifier: rideIdentifier
            });

            fetch(rideUrl).then((response) => {
                return response.json();
            }).then((ride) => {
                const rideLatLng = L.latLng(ride.latitude, ride.longitude);

                const marker = L.marker(rideLatLng, {icon: that.rideIcon});

                that.map.setView(rideLatLng, 10);
                marker.addTo(that.map);
            }).catch(function (err) {
                console.warn(err);
            });
        }
    }

    loadPhotos() {
        const that = this;
        const citySlug = this.mapContainer.dataset.citySlug;
        const rideIdentifier = this.mapContainer.dataset.rideIdentifier;

        if (citySlug && rideIdentifier) {
            const photoUrl = Routing.generate('caldera_criticalmass_rest_photo_ridelist', {
                citySlug: citySlug,
                rideIdentifier: rideIdentifier
            });

            fetch(photoUrl).then((response) => {
                return response.json();
            }).then((photoList) => {
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
            }).catch(function (err) {
                console.warn(err);
            });
        }
    }

    loadTracks() {
        const that = this;
        const citySlug = this.mapContainer.dataset.citySlug;
        const rideIdentifier = this.mapContainer.dataset.rideIdentifier;

        if (citySlug && rideIdentifier) {
            const trackUrl = Routing.generate('caldera_criticalmass_rest_track_ridelist', {
                citySlug: citySlug,
                rideIdentifier: rideIdentifier
            });

            fetch(trackUrl).then((response) => {
                return response.json();
            }).then((trackList) => {
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
            }).catch(function (err) {
                console.warn(err);
            });
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

    getIconForType(type) {
        if ('ride' === type) {
            return this.rideIcon;
        }

        if ('city' === type) {
            return this.cityIcon;
        }

        if ('photo' === type) {
            return this.photoIcon;
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.map').forEach(function (mapContainer) {
        new Map(mapContainer);
    });
});
