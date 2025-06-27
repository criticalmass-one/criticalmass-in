import L from 'leaflet';
import polylineEncoded from 'polyline-encoded';
import markerCluster from 'leaflet.markercluster';
import extraMarkers from 'leaflet-extra-markers';

export default class Map {
    mapContainer;
    map;
    polylineList = [];

    rideIcon = L.ExtraMarkers.icon({
        icon: 'fa-bicycle',
        markerColor: 'red',
        shape: 'circle',
        prefix: 'far'
    });
    locationIcon = L.ExtraMarkers.icon({
        icon: 'fa-bicycle',
        markerColor: 'white',
        shape: 'circle',
        prefix: 'far'
    });
    subrideIcon = L.ExtraMarkers.icon({
        icon: 'fa-bicycle',
        markerColor: 'green',
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
        this.setMarkerByProvidedData();
        this.setDraggableMarkerByProvidedData();
        this.addProvidedPolyline();
        this.queryApi();
        this.loadRide();
        this.loadPhotos();
        this.loadTracks();
        this.initEventListeners();
        this.disableInteraction();
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

    setDraggableMarkerByProvidedData() {
        const draggable = this.mapContainer.dataset.mapMarkerDraggable;
        const markerType = this.mapContainer.dataset.mapMarkerType;
        const mapCenterLatitude = this.mapContainer.dataset.mapCenterLatitude;
        const mapCenterLongitude = this.mapContainer.dataset.mapCenterLongitude;
        const markerLatitudeTarget = document.getElementById(this.mapContainer.dataset.mapMarkerLatitudeTarget);
        const markerLongitudeTarget = document.getElementById(this.mapContainer.dataset.mapMarkerLongitudeTarget);

        if (draggable && markerLatitudeTarget && markerLongitudeTarget && markerType) {
            const markerLatLng = L.latLng(markerLatitudeTarget.value || mapCenterLatitude, markerLongitudeTarget.value || mapCenterLongitude);

            const options = {
                draggable: true,
                autoPan: true,
                icon: this.getIconForType(markerType)
            };

            const marker = L.marker(markerLatLng, options);

            marker.addTo(this.map);

            marker.on('moveend', (event) => {
                const latLng = event.target.getLatLng();

                markerLatitudeTarget.value = latLng.lat;
                markerLongitudeTarget.value = latLng.lng;
            });
        }
    }

    setMarkerByProvidedData() {
        const draggable = this.mapContainer.dataset.mapMarkerDraggable;
        const markerType = this.mapContainer.dataset.mapMarkerType;
        const markerLatitude = this.mapContainer.dataset.mapMarkerLatitude;
        const markerLongitude = this.mapContainer.dataset.mapMarkerLongitude;

        if (!draggable && markerLatitude && markerLongitude && markerType) {
            const markerLatLng = L.latLng(markerLatitude, markerLongitude);

            const options = {
                autoPan: true,
                icon: this.getIconForType(markerType)
            };

            const marker = L.marker(markerLatLng, options);

            marker.addTo(this.map);
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

    addPolyline(polylineString, polylineColorString, identifier) {
        if (polylineString && polylineColorString) {
            const polyline = L.Polyline.fromEncoded(polylineString, {color: polylineColorString});

            polyline.addTo(this.map);

            this.polylineList[identifier] = polyline;

            this.map.fitBounds(polyline.getBounds());
        }
    }

    updatePolyline(polylineString, polylineColorString, identifier) {
        if (polylineString && polylineColorString) {
            const polyline = L.Polyline.fromEncoded(polylineString, {color: polylineColorString});
            const latLngList = polyline.getLatLngs();

            this.polylineList[identifier].setLatLngs(latLngList);

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
            const rideUrl = `/api/${encodeURIComponent(citySlug)}/${encodeURIComponent(rideIdentifier)}`;

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
            const photoUrl = `/api/${encodeURIComponent(citySlug)}/${encodeURIComponent(rideIdentifier)}/listPhotos`;

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
            const trackUrl = `/api/${encodeURIComponent(citySlug)}/${encodeURIComponent(rideIdentifier)}/listTracks`;

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

        if ('subride' === type) {
            return this.subrideIcon;
        }

        if ('location' === type) {
            return this.locationIcon;
        }
    }

    initEventListeners() {
        document.addEventListener('geocoding-result', (event) => {
            const result = event.result;
            const latitude = parseFloat(result.lat);
            const longitude = parseFloat(result.lon);
            const mapCenter = L.latLng(latitude, longitude);

            this.map.eachLayer((layer) => {
                if (layer instanceof L.Marker) {
                    layer.setLatLng(mapCenter);
                }
            });

            if (result.boundingbox) {
                const boundingbox = result.boundingbox;
                const northWest = new L.latLng([boundingbox[1], boundingbox[2]]);
                const southEast = new L.latLng([boundingbox[0], boundingbox[3]]);

                const bounds = new L.latLngBounds(northWest, southEast);

                this.map.flyToBounds(bounds);
            } else {
                this.map.setView(mapCenter);
            }
        });

        document.addEventListener('map-polyline-add', (polylineEvent) => {
            this.addPolyline(polylineEvent.polylineString, polylineEvent.colorString, polylineEvent.identifier);
        });

        document.addEventListener('map-polyline-update', (polylineEvent) => {
            this.updatePolyline(polylineEvent.polylineString, polylineEvent.colorString, polylineEvent.identifier);
        });

        document.addEventListener('map-clear', () => {
            this.map.eachLayer((layer) => {
                this.map.removeLayer(layer);
            });
        });
    }

    disableInteraction() {
        if (this.mapContainer.dataset.lockMap) {
            this.mapContainer.querySelector('.leaflet-control-zoom').remove();
            this.mapContainer.style.cursor = 'default';
            this.map.dragging.disable();
            this.map.touchZoom.disable();
            this.map.doubleClickZoom.disable();
            this.map.scrollWheelZoom.disable();
            this.map.boxZoom.disable();
            this.map.keyboard.disable();

            if (this.map.tap) {
                this.map.tap.disable();
            }
        }
    };
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.map').forEach(function (mapContainer) {
        new Map(mapContainer);
    });
});
