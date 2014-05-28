PositionSender = function(parentPage)
{
    this.parentPage = parentPage;

    this.parentPage.positionSender = this;

    this.startSender();
};

PositionSender.prototype.parentPage = null;

PositionSender.prototype.interval = null;

PositionSender.prototype.startSender = function()
{
  var this2 = this;

  this.interval = window.setInterval(function()
  {
      if (this2.parentPage.isGpsActivated())
      {
          this2.catchPosition(this2);
      }
  }, 1000);
}

PositionSender.prototype.catchPosition = function(this2)
{
    if (navigator.geolocation)
    {
        function processError2(positionError)
        {
            this2.processError(positionError);
        }

        function processPosition2(positionResult)
        {
            this2.processPosition(positionResult);
        }

        navigator.geolocation.getCurrentPosition(processPosition2,
                                                 processError2);
    }
    else
    {
        var notificationLayer = new NotificationLayer('Schade: Dein Browser unterstützt leider noch keine Positionsbestimmung.');
        this.parentPage.showNotificationLayer(notificationLayer);
    }
}

PositionSender.prototype.processPosition = function(positionResult)
{
    $.ajax({
        type: 'GET',
        url: UrlFactory.getApiPrefix() + 'trackposition',
        context: this,
        data: {
            latitude: positionResult.coords.latitude,
            longitude: positionResult.coords.longitude,
            accuracy: positionResult.coords.accuracy,
            altitude: positionResult.coords.altitude,
            altitudeaccuracy: positionResult.coords.altitudeAccurary,
            speed: positionResult.coords.speed,
            heading: positionResult.coords.heading,
            timestamp: positionResult.timestamp
        },
        cache: false,
        success: function(result) {
            _paq.push(['trackEvent', 'sendPosition', 'success']);
        },
        error: function()
        {
            _paq.push(['trackEvent', 'sendPosition', 'error']);

            var notificationLayer = new NotificationLayer('Deine Position konnte nicht an den Server übertragen werden.');
            this.parentPage.showNotificationLayer(notificationLayer);
        }
    });

    this.setQuality(positionResult.coords.accuracy);
}

PositionSender.prototype.quality = null;

PositionSender.prototype.setQuality = function(quality)
{
    this.quality = quality;

    this.parentPage.refreshGpsGauge(quality);
}

PositionSender.prototype.getQuality = function()
{
    return this.quality;
}

PositionSender.prototype.processError = function(positionError)
{
    switch(positionError.code)
    {
        case positionError.PERMISSION_DENIED:
            var notificationLayer = new NotificationLayer('Wir kommen leider nicht an deine Positionsdaten ran. Bitte erlaube dieser App den Zugriff auf deine Positionsdaten.');
            break;
        case positionError.POSITION_UNAVAILABLE:
            var notificationLayer = new NotificationLayer('Dein Gerät hat leider keine brauchbaren Positionsdaten zurückgeliefert.');
            break;
        case positionError.TIMEOUT:
            var notificationLayer = new NotificationLayer('Dein Gerät konnte die Anfrage an deine Positionsdaten leider nicht bearbeiten.');
            break;
        case positionError.UNKNOWN_ERROR:
            var notificationLayer = new NotificationLayer('Hmm, es ist ein unbekannter Fehler aufgetreten — mehr wissen wir leider auch nicht.');
            break;
    }

    this.setQuality(-1);
    this.parentPage.showNotificationLayer(notificationLayer);
}