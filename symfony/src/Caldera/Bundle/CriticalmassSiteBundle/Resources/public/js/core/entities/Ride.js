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
Ride.prototype.url = null;
Ride.prototype.facebook = null;
Ride.prototype.twitter = null;
Ride.prototype.weatherForecast = null;

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

Ride.prototype.getFormattedDate = function()
{
    var month = '';

    switch (this.dateTime.getMonth() + 1) {
        case 1:
            month = 'Januar'
            break;
        case 2:
            month = 'Februar';
            break;
        case 3:
            month = 'März';
            break;
        case 4:
            month = 'April';
            break;
        case 5:
            month = 'Mai';
            break;
        case 6:
            month = 'Juni';
            break;
        case 7:
            month = 'Juli';
            break;
        case 8:
            month = 'August';
            break;
        case 9:
            month = 'September';
            break;
        case 10:
            month = 'Oktober';
            break;
        case 11:
            month = 'November';
            break;
        case 12:
            month = 'Dezember';
            break;
    }

    return this.dateTime.getDate() + '. ' + month + ' ' + this.dateTime.getFullYear();
};

Ride.prototype.getFormattedTime = function()
{
    return this.dateTime.getHours() + '.' + (this.dateTime.getMinutes() < 10 ? '0' + this.dateTime.getMinutes() : this.dateTime.getMinutes()) + ' Uhr';
};

Ride.prototype.getLatLng = function()
{
    return L.latLng(this.latitude, this.longitude);
};

Ride.prototype.isRolling = function()
{
    var currentDateTime = new Date();
    var timeDiff = currentDateTime.getTime() - this.dateTime.getTime();

    return timeDiff > 900;
};

Ride.prototype.getUrl = function()
{
    return this.url;
};

Ride.prototype.setUrl = function(url)
{
    this.url = url;
};

Ride.prototype.getFacebook = function()
{
    return this.facebook;
};

Ride.prototype.setFacebook = function(facebook)
{
    this.facebook = facebook;
};

Ride.prototype.getTwitter = function()
{
    return this.twitter;
};

Ride.prototype.setTwitter = function(twitter)
{
    this.twitter = twitter;
};

Ride.prototype.getWeatherForecast = function()
{
    return this.weatherForecast;
};

Ride.prototype.setWeatherForecast = function(weatherForecast)
{
    this.weatherForecast = weatherForecast;
};