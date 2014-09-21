RideFactory = function()
{

};

RideFactory.storage = sessionStorage.rideListData;

RideFactory.hasRides = function()
{
    return (this.storage != null) && (this.storage != '');
};

RideFactory.convertObjectToRide = function(objectData)
{
    var ride = new Ride();

    ride.setId(objectData.id);
    ride.setCitySlug(objectData.slug);
    ride.setDateTime(new Date(objectData.dateTime));
    ride.setLocation(objectData.location);
    ride.setLatitude(objectData.latitude);
    ride.setLongitude(objectData.longitude);
    ride.setHasLocation(objectData.hasLocation);
    ride.setTitle(objectData.title);
    ride.setDescription(objectData.description);
    ride.setUrl(objectData.url);
    ride.setFacebook(objectData.facebook);
    ride.setTwitter(objectData.twitter);
    ride.setWeatherForecast(objectData.weatherForecast);

    return ride;
};

RideFactory.getRideFromStorageBySlug = function(citySlug)
{
    if (!this.storage)
    {
        return null;
    }

    var rideList = JSON.parse(this.storage);

    for (index in rideList.rides)
    {
        if (rideList.rides[index].slug == citySlug)
        {
            return this.convertObjectToRide(rideList.rides[index]);
        }
    }

    return null;
};

RideFactory.storeAllRides = function()
{
    if (!this.storage || this.storage == null)
    {
        $.ajax({
            type: 'GET',
            async: false,
            url: Url.getApiPrefix() + 'ride/getcurrent',
            cache: false,
            context: this,
            success: function(data)
            {
                this.storage = JSON.stringify(data);
                CallbackHell.executeEventListener('rideListRefreshed');
            }
        });
    }
};

RideFactory.refreshAllStoredRides = function()
{
    this.storage = null;
    this.storeAllRides();
};