Ride = function()
{
};

Ride.prototype.id = null;
Ride.prototype.citySlug = null;
Ride.prototype.dateTime = null;
Ride.prototype.location = null;
Ride.prototype.latitude = null;
Ride.prototype.longitude = null;

Ride.prototype.getId = function()
{
    return this.id;
};

Ride.prototype.setId = function(id)
{
    this.id = id;
};

Ride.prototype.getCitySlug = function()
{
    return this.citySlug;
};

Ride.prototype.setCitySlug = function(citySlug)
{
    this.citySlug = citySlug;
};

Ride.prototype.getDateTime = function()
{
    return this.dateTime;
}

Ride.prototype.setDateTime = function(dateTime)
{
    this.dateTime = dateTime;
}

Ride.prototype.getLocation = function()
{
    return this.location;
}

Ride.prototype.setLocation = function(location)
{
    this.location = location;
}

Ride.prototype.getLatitude = function()
{
    return this.latitude;
}

Ride.prototype.setLatitude = function(latitude)
{
    this.latitude = latitude;
}

Ride.prototype.getLongitude = function()
{
    return this.longitude;
}

Ride.prototype.setLongitude = function(longitude)
{
    this.longitude = longitude;
}