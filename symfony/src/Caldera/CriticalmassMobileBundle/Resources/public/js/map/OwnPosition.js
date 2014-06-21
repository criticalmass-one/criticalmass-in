OwnPosition = function(map)
{
    this.map = map;
};

OwnPosition.prototype.map = null;

OwnPosition.prototype.ownPosition = null;

OwnPosition.prototype.showOwnPosition = function()
{
    if (navigator.geolocation && !this.map.parentPage.isUserLoggedIn())
    {
        var this2 = this;

        function processError2(positionError)
        {
            this2.setQuickLinkButtonStatus(false);
            this2.removeOwnPosition();
        }

        function processPosition2(positionResult)
        {
            this2.setQuickLinkButtonStatus(true);
            this2.drawOwnPosition(positionResult);
        }

        navigator.geolocation.watchPosition(processPosition2, processError2, { maximumAge: 15000, timeout: 5000, enableHighAccuracy: true });
    }
    else
    if (this.ownPosition != null)
    {
        this.removeOwnPosition();
    }
};

OwnPosition.prototype.drawOwnPosition = function(positionResult)
{
    if (this.ownPosition == null)
    {
        this.createOwnPosition([positionResult.coords.latitude, positionResult.coords.longitude], positionResult.coords.accuracy);
    }
    else
    {
        this.moveOwnPosition([positionResult.coords.latitude, positionResult.coords.longitude], positionResult.coords.accuracy);
    }
};

OwnPosition.prototype.createOwnPosition = function(latLng, radius)
{
    var circleOptions = {
        color: 'red',
        fillColor: 'red',
        opacity: 1.0,
        fillOpacity: 0.3,
        weight: 1
    };

    this.ownPosition = L.circle(latLng, radius, circleOptions);
    this.ownPosition.addTo(this.map.map);
};

OwnPosition.prototype.moveOwnPosition = function(latLng, radius)
{
    this.ownPosition.setLatLng(latLng);
    this.ownPosition.setRadius(radius);
};

OwnPosition.prototype.removeOwnPosition = function()
{
    this.map.map.removeLayer(this.ownPosition);
    this.ownPosition = null;
};

OwnPosition.prototype.panToOwnPosition = function()
{
    if (this.ownPosition)
    {
        this.map.map.panTo(this.ownPosition.getLatLng());

        _paq.push(['trackEvent', 'panTo', 'ownPosition']);
    }
};

OwnPosition.prototype.setQuickLinkButtonStatus = function(status)
{
    $('#quicklinkOwnPosition').attr('disabled', !status);
};