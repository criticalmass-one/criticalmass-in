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

        }

        function processPosition2(positionResult)
        {
            alert('jo');
            this2.drawOwnPosition(positionResult);
        }

        navigator.geolocation.watchPosition(processPosition2, processError2, { maximumAge: 15000, timeout: 5000, enableHighAccuracy: true });
    }
};

OwnPosition.prototype.drawOwnPosition = function(positionResult)
{
    var circleOptions = {
        color: 'red',
        fillColor: 'red',
        opacity: 80,
        fillOpacity: 50,
        weight: 1
    };

    this.ownPosition = L.circle([positionResult.coords.latitude, positionResult.coords.longitude], positionResult.coords.accuracy, circleOptions);
    this.ownPosition.addTo(this.map.map);
};