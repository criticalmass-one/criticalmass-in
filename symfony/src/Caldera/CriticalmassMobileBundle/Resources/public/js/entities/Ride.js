Ride = function()
{
};

Ride.prototype.id = null;
Ride.prototype.citySlug = null;
Ride.prototype.dateTime = null;
Ride.prototype.title = null;
Ride.prototype.description = null;
Ride.prototype.location = null;
Ride.prototype.latitude = null;
Ride.prototype.longitude = null;
Ride.prototype.hasLocation = null;

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
};

Ride.prototype.setDateTime = function(dateTime)
{
    this.dateTime = dateTime;
};

Ride.prototype.getLocation = function()
{
    return this.location;
};

Ride.prototype.setLocation = function(location)
{
    this.location = location;
};

Ride.prototype.getLatitude = function()
{
    return this.latitude;
};

Ride.prototype.setLatitude = function(latitude)
{
    this.latitude = latitude;
};

Ride.prototype.getLongitude = function()
{
    return this.longitude;
};

Ride.prototype.setLongitude = function(longitude)
{
    this.longitude = longitude;
};

Ride.prototype.getHasLocation = function()
{
    return this.hasLocation;
};

Ride.prototype.setHasLocation = function(hasLocation)
{
    this.hasLocation = hasLocation;
};

Ride.prototype.getTitle = function()
{
    return this.title;
};

Ride.prototype.setTitle = function(title)
{
    this.title = title;
};

Ride.prototype.getDescription = function()
{
    return this.description;
};

Ride.prototype.setDescription = function(description)
{
    this.description = description;
};

Ride.prototype.getFormattedDateTime = function()
{
    return this.dateTime.getDate() + '.' + (this.dateTime.getMonth() + 1) + '. ' + this.dateTime.getHours() + '.' + (this.dateTime.getMinutes() < 10 ? '0' + this.dateTime.getMinutes() : this.dateTime.getMinutes()) + ' Uhr';
};

Ride.prototype.getLatLng = function()
{
    return L.latLng(this.latitude, this.longitude);
};

Ride.prototype.isRolling = function()
{
    return true;
};