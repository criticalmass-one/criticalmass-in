PositionSender = function(parentPage)
{
    this.parentPage = parentPage;

    this.parentPage.positionSender = this;

    this.startSender();
};

PositionSender.prototype.parentPage = null;

PositionSender.prototype.pingToken = null;

PositionSender.prototype.startSender = function()
{
    $.ajax({
        type: 'GET',
        url: Url.getApiPrefix() + 'user/getpingtoken',
        context: this,
        dataType: 'json',
        success: function (res) {
            this.pingToken = res.pingToken;

            this.startLoop();
        }
    });
};

PositionSender.prototype.startLoop = function()
{
    if (navigator.geolocation)
    {
        var this2 = this;

        navigator.geolocation.watchPosition(processPosition2, processError2, { maximumAge: 15000, timeout: 5000, enableHighAccuracy: true });

        function processError2(positionError)
        {
            if (this2.parentPage.isGpsActivated() && this2.parentPage.isUserLoggedIn())
            {
                this2.processError(positionError);
            }
        }

        function processPosition2(positionResult)
        {
            if (this2.parentPage.isGpsActivated() && this2.parentPage.isUserLoggedIn())
            {
                this2.processPosition(positionResult);
            }
        }
    }
};

PositionSender.prototype.processPosition = function(positionResult)
{
    var positionData = {
        action: 'trackPosition',
        token: this.pingToken,
        citySlug: this.parentPage.getCitySlug(),
        latitude: positionResult.coords.latitude,
        longitude: positionResult.coords.longitude,
        accuracy: positionResult.coords.accuracy,
        altitude: positionResult.coords.altitude,
        altitudeAccuracy: positionResult.coords.altitudeAccuracy,
        speed: positionResult.coords.speed,
        heading: positionResult.coords.heading,
        timestamp: positionResult.timestamp
    };

    $.ajax({
        type: 'GET',
        url: Url.getNodeJSApiPrefix(),
        context: this,
        data: positionData,
        cache: false,
        success: function(result) {
            _paq.push(['trackEvent', 'sendPosition', 'success']);
        },
        error: function()
        {
            _paq.push(['trackEvent', 'sendPosition', 'failureServerError']);

            var notificationLayer = new NotificationLayer('Deine Position konnte nicht an den Server übertragen werden.');
            this.parentPage.showNotificationLayer(notificationLayer);
        }
    });

    this.setQuality(positionResult.coords.accuracy);
};

PositionSender.prototype.quality = null;

PositionSender.prototype.setQuality = function(quality)
{
    this.quality = quality;

    this.parentPage.refreshGpsGauge(quality);
};

PositionSender.prototype.getQuality = function()
{
    return this.quality;
};

PositionSender.prototype.processError = function(positionError)
{
    switch(positionError.code)
    {
        case positionError.PERMISSION_DENIED:
            var notificationLayer = new NotificationLayer('Wir kommen leider nicht an deine Positionsdaten ran. Bitte erlaube dieser App den Zugriff auf deine Positionsdaten.');

            _paq.push(['trackEvent', 'sendPosition', 'failureDeviceError']);
            break;
        case positionError.POSITION_UNAVAILABLE:
            var notificationLayer = new NotificationLayer('Dein Gerät hat leider keine brauchbaren Positionsdaten zurückgeliefert.');

            _paq.push(['trackEvent', 'sendPosition', 'failurePositionError']);
            break;
        case positionError.TIMEOUT:
            var notificationLayer = new NotificationLayer('Dein Gerät konnte die Anfrage an deine Positionsdaten leider nicht bearbeiten.');

            _paq.push(['trackEvent', 'sendPosition', 'failureTimeoutError']);
            break;
        case positionError.UNKNOWN_ERROR:
            var notificationLayer = new NotificationLayer('Hmm, es ist ein unbekannter Fehler aufgetreten — mehr wissen wir leider auch nicht.');

            _paq.push(['trackEvent', 'sendPosition', 'failureUnknownError']);
            break;
    }

    this.setQuality(-1);
    this.parentPage.showNotificationLayer(notificationLayer);
};